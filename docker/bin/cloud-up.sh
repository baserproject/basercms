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

# クラウドで起動する compose サービス（GUI 管理ツールと PostgreSQL は起動しない。理由は手順3）
# setup-script.sh が pull するイメージの一覧と一致させること。
CLOUD_SERVICES="bc-db bc-php bc-smtp"

# クラウドセッション以外では何もしない
if [ -z "${CLAUDE_CODE_REMOTE_SESSION_ID:-}" ] && [ "${CLOUD_FORCE:-0}" != "1" ]; then
    exit 0
fi

mkdir -p "$TMP_DIR"

# 多重起動の防止（既に別プロセスが動いていれば何もしない）
#
# ロックは mkdir の原子性で取るが、VM が実行中に強制停止されると trap が走らず
# ロックディレクトリが残る。それを放置すると以降 cloud-up.sh が永久に即終了し、
# 起動処理が二度と走らなくなる。保持プロセスの生存を確認し、死んでいれば奪う。
STALE_LOCK_TAKEN=0

current_boot_id() {
    cat /proc/sys/kernel/random/boot_id 2>/dev/null
}

write_lock_owner() {
    echo "$$" >"${LOCK_FILE}/pid" 2>/dev/null
    current_boot_id >"${LOCK_FILE}/boot_id" 2>/dev/null
}

# ロックを保持しているプロセスが今も生きているか
lock_holder_is_alive() {
    lock_boot_id="$(cat "${LOCK_FILE}/boot_id" 2>/dev/null)"
    now_boot_id="$(current_boot_id)"
    # 別ブートで作られたロックは無条件に stale（PID の使い回しで誤判定しないため）
    if [ -n "$now_boot_id" ] && [ "$lock_boot_id" != "$now_boot_id" ]; then
        return 1
    fi
    lock_pid="$(cat "${LOCK_FILE}/pid" 2>/dev/null)"
    [ -n "$lock_pid" ] || return 1
    kill -0 "$lock_pid" 2>/dev/null
}

acquire_lock() {
    if mkdir "$LOCK_FILE" 2>/dev/null; then
        write_lock_owner
        return 0
    fi
    if lock_holder_is_alive; then
        return 1
    fi
    rm -rf "$LOCK_FILE"
    mkdir "$LOCK_FILE" 2>/dev/null || return 1
    write_lock_owner
    STALE_LOCK_TAKEN=1
    return 0
}

acquire_lock || exit 0
trap 'rm -rf "$LOCK_FILE" 2>/dev/null' EXIT

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
[ "$STALE_LOCK_TAKEN" -eq 1 ] && log "Removed a stale lock left by a previous run."

#
# 1. Docker デーモンの起動
#
# VM 再起動をまたぐと /run/docker/containerd/containerd.pid が残る。
# 再起動後は同じ PID が別プロセスに再利用されていることがあり、その場合 dockerd は
#   failed to start containerd: ... process with PID N is still running
# で起動を拒否する（実際に踏んだ）。
# dockerd も containerd も動いていないときに限り、残骸を掃除する。
cleanup_stale_docker_pids() {
    pgrep -x dockerd >/dev/null 2>&1 && return 0
    pgrep -x containerd >/dev/null 2>&1 && return 0
    rm -f /run/docker/containerd/containerd.pid \
          /var/run/docker/containerd/containerd.pid \
          /run/docker.pid \
          /var/run/docker.pid 2>/dev/null
    return 0
}

wait_for_dockerd() {
    for _ in $(seq 1 60); do
        docker info >/dev/null 2>&1 && return 0
        sleep 1
    done
    return 1
}

if ! docker info >/dev/null 2>&1; then
    log "Starting dockerd."
    cleanup_stale_docker_pids
    # `&` は `||` リスト全体に掛かり、bash が fork するサブシェルは呼び出し元の
    # stdio を継承したまま dockerd を wait し続ける。呼び出し元が出力を読んで
    # いると EOF が来なくなるため、波括弧で囲んで stdio を切り離す。
    # 詳細は docs/cloud/setup-script.sh の同箇所のコメントを参照。
    { service docker start >/dev/null 2>&1 || dockerd >>/tmp/dockerd.log 2>&1; } </dev/null >/dev/null 2>&1 &
    wait_for_dockerd
fi

# 1回目が残骸で弾かれることがあるため、掃除してもう一度だけ試す
if ! docker info >/dev/null 2>&1; then
    log "dockerd did not start. Cleaning up stale state and retrying."
    cleanup_stale_docker_pids
    dockerd >>/tmp/dockerd.log 2>&1 &
    wait_for_dockerd
fi

docker info >/dev/null 2>&1 || fail "dockerd is not available. See /tmp/dockerd.log."
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
#    クラウドでは bc-pg / bc-pga（PostgreSQL・pgAdmin）と bc-pma（phpMyAdmin）を
#    起動しない。
#    - PostgreSQL はユニットテストで使われていない（実参照は config/app.php のコメントのみ）
#    - pgAdmin はその管理画面なので、あわせて出番がない
#    - pgAdmin は ./pgadmin をバインドするが、クラウドでは compose が root:root 0755 で
#      作るため uid 5050 のコンテナが書き込めず、起動直後に Exited(1) で落ちる
#    - phpMyAdmin はクラウドVMのポートに外から到達できないため、人が画面を開けない。
#      DB の中身は次のほうが速く確実で、GUI を経由する意味がない:
#        docker exec bc-db mysql -uroot -proot basercms -e "SELECT ..."
#    起動するサービスを明示することで、docker-compose.yml.default はローカル・CI と
#    共通のまま変えずに済む。
log "docker compose up -d (${CLOUD_SERVICES})"
(cd "$DOCKER_DIR" && docker compose up -d $CLOUD_SERVICES) || fail "docker compose up failed."

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

#
# 7. ユニットテストの準備
#    composer run-script test は内部で bin/cake setup test を実行するが、
#    ファイル単位で流すときは vendor/bin/phpunit を直接呼ぶのが普通で、その場合
#    未実行だと弾かれる。READY と言われたのにまだ準備が終わっていない状態を
#    残さないため、ここで済ませておく（数秒）。
#    失敗しても起動全体を落とす必要はないので WARNING に留める。
#
log "Running bin/cake setup test."
docker exec bc-php sh -c 'cd /var/www/html && bin/cake setup test' >/dev/null 2>&1 \
    && log "setup test finished." \
    || log "WARNING: setup test failed. Run it manually before vendor/bin/phpunit."

#
# 8. tmp/logs の所有者を www-data に揃える
#    docker exec は root で走るため、root で bin/cake や composer を叩くと
#    tmp/cache 配下に root 所有のキャッシュファイルが残る。Web からのアクセスは
#    www-data で動くため、そのファイルを上書きできず
#    「_bc_env_ cache was unable to write 'enable_plugins'」で 500 になる。
#    起動のたびに揃え直しておく。
#
log "Fixing ownership of tmp and logs."
docker exec bc-php sh -c 'chown -R www-data:www-data /var/www/html/tmp/cache /var/www/html/logs' \
    || log "WARNING: Failed to fix ownership. The site may return 500."

touch "$DONE_FILE"
log "===== cloud-up done ====="
exit 0
