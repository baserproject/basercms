#!/usr/bin/env bash
#
# cloud-status.sh
# クラウドセッションにおける baserCMS 起動状態の確認。
#
# cloud-up.sh はバックグラウンドで動くため、作業前にこのスクリプトで完了を待つ。
# 進行中の場合は完了するまでブロックする（既定 900 秒）。
#
# 使い方:
#   docker/bin/cloud-status.sh          # 完了まで待って状態を表示
#   TIMEOUT=60 docker/bin/cloud-status.sh
#

set -u

ROOT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/../.." && pwd)"
DOCKER_DIR="${ROOT_DIR}/docker"
TMP_DIR="${ROOT_DIR}/tmp"
LOG_FILE="${TMP_DIR}/cloud-up.log"
LOCK_FILE="${TMP_DIR}/cloud-up.lock"
DONE_FILE="${TMP_DIR}/cloud-up.done"
FAILED_FILE="${TMP_DIR}/cloud-up.failed"
TIMEOUT="${TIMEOUT:-900}"

show_detail() {
    echo
    echo "--- docker compose ps ---"
    if [ -e "${DOCKER_DIR}/docker-compose.yml" ]; then
        (cd "$DOCKER_DIR" && docker compose ps) 2>&1
    else
        echo "docker/docker-compose.yml がまだありません。"
    fi
    echo
    echo "--- tail -30 tmp/cloud-up.log ---"
    if [ -e "$LOG_FILE" ]; then
        tail -30 "$LOG_FILE"
    else
        echo "tmp/cloud-up.log はまだありません。"
    fi
}

if [ -z "${CLAUDE_CODE_REMOTE_SESSION_ID:-}" ] && [ "${CLOUD_FORCE:-0}" != "1" ]; then
    echo "LOCAL: クラウドセッションではありません。ローカルの実行環境は .github/instructions/local.instructions.md を参照してください。"
    exit 0
fi

#
# cloud-up.sh が一度も走っていない場合はここから起動する。
# SessionStart フックは $CLAUDE_PROJECT_DIR の解決が環境によって変わるため、
# 起動していないことがある。フックは「先回りして始めておく」ための最適化に過ぎず、
# 確実な入口はこのスクリプトとする。
#
if [ ! -e "$DONE_FILE" ] && [ ! -e "$FAILED_FILE" ] \
   && [ ! -e "$LOG_FILE" ] && [ ! -e "$LOCK_FILE" ]; then
    echo "NOT STARTED: cloud-up.sh がまだ実行されていません。ここから起動します。"
    nohup bash "${ROOT_DIR}/docker/bin/cloud-up.sh" >/dev/null 2>&1 &
    sleep 3
fi

WAITED=0
while [ ! -e "$DONE_FILE" ] && [ ! -e "$FAILED_FILE" ]; do
    if [ "$WAITED" -ge "$TIMEOUT" ]; then
        echo "TIMEOUT: ${TIMEOUT} 秒待ちましたが起動が完了しませんでした。"
        show_detail
        exit 1
    fi
    if [ "$WAITED" -eq 0 ]; then
        echo "WAITING: cloud-up.sh の完了を待っています（最大 ${TIMEOUT} 秒）..."
    fi
    sleep 5
    WAITED=$((WAITED + 5))
done

if [ -e "$FAILED_FILE" ]; then
    echo "FAILED: baserCMS の起動に失敗しました。tmp/cloud-up.log を確認してください。"
    show_detail
    exit 1
fi

echo "READY: baserCMS は起動済みです。"
echo "  PHP コンテナ: bc-php / DB コンテナ: bc-db / 配置先: /var/www/html"
echo "  ユニットテスト: docker exec bc-php sh -c 'cd /var/www/html && composer run-script test'"
show_detail
exit 0
