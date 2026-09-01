#!/usr/bin/env bash
#
# cloud-up.sh
# Claude Code on the web（クラウドセッション）で baserCMS を起動しきるスクリプト。
#
# - .claude/settings.json の SessionStart フックからバックグラウンドで起動される
# - ローカル環境では何もせず即終了する（CLOUD_FORCE=1 で強制実行）
# - 何度実行しても安全（冪等）
# - 途中で失敗してもセッション自体は止めないため、必ず exit 0 で終了する
#
# 進捗ログ: tmp/cloud-up.log
# 完了マーク: tmp/cloud-up.done / 失敗マーク: tmp/cloud-up.failed
#

set -u

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DOCKER_DIR="${ROOT_DIR}/docker"
TMP_DIR="${ROOT_DIR}/tmp"
LOG_FILE="${TMP_DIR}/cloud-up.log"
LOCK_FILE="${TMP_DIR}/cloud-up.lock"
DONE_FILE="${TMP_DIR}/cloud-up.done"
FAILED_FILE="${TMP_DIR}/cloud-up.failed"

# クラウドセッション以外では何もしない
if [ -z "${CLAUDE_CODE_REMOTE_SESSION_ID:-}" ] && [ "${CLOUD_FORCE:-0}" != "1" ]; then
    exit 0
fi

mkdir -p "$TMP_DIR"

# 多重起動の防止（既に別プロセスが動いていれば何もしない）
if ! mkdir "$LOCK_FILE" 2>/dev/null; then
    exit 0
fi
trap 'rmdir "$LOCK_FILE" 2>/dev/null' EXIT

exec >>"$LOG_FILE" 2>&1

log() {
    echo "[$(date +"%Y/%m/%d %H:%M:%S")] $*"
}

fail() {
    log "FAILED: $*"
    touch "$FAILED_FILE"
    exit 0
}

rm -f "$DONE_FILE" "$FAILED_FILE"
log "===== cloud-up start ====="

#
# 1. Docker デーモンの起動
#
if ! docker info >/dev/null 2>&1; then
    log "Starting dockerd."
    service docker start >/dev/null 2>&1 || dockerd >/tmp/dockerd.log 2>&1 &
    for _ in $(seq 1 60); do
        docker info >/dev/null 2>&1 && break
        sleep 1
    done
fi
docker info >/dev/null 2>&1 || fail "dockerd is not available."
log "dockerd is ready."

#
# 2. compose ファイルの用意
#    docker-compose.yml は .gitignore 対象のためクラウドVMには存在しない。
#    .github/workflows/test.yml と同じ手順で default から生成する。
#
if [ ! -e "${DOCKER_DIR}/docker-compose.yml" ]; then
    log "Create docker-compose.yml from docker-compose.yml.default."
    cp "${DOCKER_DIR}/docker-compose.yml.default" "${DOCKER_DIR}/docker-compose.yml" || fail "Failed to copy docker-compose.yml.default."
    # クラウドにはデバッガの接続先が無いため Xdebug は無効にする
    sed -i -e 's/XDEBUG_MODE: "debug"/XDEBUG_MODE: "off"/g' "${DOCKER_DIR}/docker-compose.yml"
fi

if [ ! -e "${DOCKER_DIR}/.env" ]; then
    log "Create docker/.env from docker/.env.example."
    cp "${DOCKER_DIR}/.env.example" "${DOCKER_DIR}/.env" || fail "Failed to copy docker/.env.example."
fi

#
# 3. コンテナの起動
#
log "docker compose up -d"
(cd "$DOCKER_DIR" && docker compose up -d) || fail "docker compose up failed."

#
# 4. MySQL の待機
#
log "Waiting for MySQL."
CONNECTED=0
for _ in $(seq 1 90); do
    if docker exec bc-db sh -c 'mysqladmin ping -h127.0.0.1 -uroot -proot 2>/dev/null' | grep -q 'mysqld is alive'; then
        CONNECTED=1
        break
    fi
    sleep 2
done
[ "$CONNECTED" -eq 1 ] || fail "MySQL did not become ready."
log "MySQL is ready."

#
# 5. コンテナへの CA 証明書の導入
#    クラウドセッションの通信は agent proxy を経由するため、その CA を信頼しないと
#    コンテナ内からの HTTPS が全て
#    「SSL certificate problem: self-signed certificate in certificate chain」で失敗し、
#    composer の dist ダウンロードが軒並みエラーになる。
#    コンテナは毎セッション作り直されるため、毎回適用する。
#
CA_BUNDLE="/root/.ccr/ca-bundle.crt"
if [ -e "$CA_BUNDLE" ]; then
    log "Installing agent proxy CA into bc-php."
    docker cp "$CA_BUNDLE" bc-php:/usr/local/share/ca-certificates/ccr-proxy.crt \
        && docker exec bc-php sh -c 'update-ca-certificates' >/dev/null 2>&1 \
        && log "CA is installed." \
        || log "WARNING: Failed to install CA. HTTPS from the container may fail."
else
    log "No agent proxy CA found at ${CA_BUNDLE}. Skip."
fi

#
# 6. baserCMS のインストール
#    config/install.php が生成済みならインストール済みとみなす。
#    composer の既定のプロセスタイムアウト（300秒）ではダウンロードが打ち切られるため
#    --timeout で延長する。
#
if [ -e "${ROOT_DIR}/config/install.php" ]; then
    log "baserCMS is already installed. Skip app-install."
else
    log "Running composer run-script app-install. (this takes a few minutes)"
    docker exec bc-php sh -c 'cd /var/www/html && composer run-script --timeout=3000 app-install' || fail "app-install failed."
    log "app-install finished."
fi

touch "$DONE_FILE"
log "===== cloud-up done ====="
exit 0
