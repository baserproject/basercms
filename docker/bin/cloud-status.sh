#!/usr/bin/env bash
#
# cloud-status.sh
# クラウドセッションにおける baserCMS 起動状態の確認。
#
# cloud-up.sh はバックグラウンドで動くため、作業前にこのスクリプトで完了を待つ。
# 進行中の場合は完了するまでブロックする（既定 900 秒）。
#
# 完了マーク（tmp/cloud-up.done）はディスクに残るが、dockerd とコンテナは VM の
# 再起動で消える。マークだけを信用すると起動していないのに「起動済み」と誤報し、
# 直後の docker exec が原因不明のエラーになる。そのため必ず実体（dockerd と必須
# コンテナ）の生存を確認し、実体が無ければ stale とみなして起動し直す。
#
# 未完了を検出した場合は cloud-up.sh を起動する。多重起動は cloud-up.sh 側の
# ロックで防がれるため、SessionStart フックが発火していてもいなくても安全に動く。
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
DONE_FILE="${TMP_DIR}/cloud-up.done"
FAILED_FILE="${TMP_DIR}/cloud-up.failed"
UP_SCRIPT="${ROOT_DIR}/docker/bin/cloud-up.sh"
TIMEOUT="${TIMEOUT:-900}"

# baserCMS を利用するのに最低限必要なコンテナ（docker-compose.yml.default の container_name）
REQUIRED_CONTAINERS="bc-php bc-db"

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

# dockerd と必須コンテナが実際に動いているか。完了マークの裏取りに使う。
is_alive() {
    docker info >/dev/null 2>&1 || return 1
    local name
    for name in $REQUIRED_CONTAINERS; do
        [ "$(docker inspect -f '{{.State.Running}}' "$name" 2>/dev/null)" = "true" ] || return 1
    done
    return 0
}

# cloud-up.sh をバックグラウンドで起動する。
# 既に別プロセスが動いていれば cloud-up.sh 側のロックで即終了するため、呼び出しは安全。
start_cloud_up() {
    if [ ! -e "$UP_SCRIPT" ]; then
        echo "ERROR: ${UP_SCRIPT} が見つかりません。"
        return 1
    fi
    nohup bash "$UP_SCRIPT" >/dev/null 2>&1 &
    return 0
}

# push / PR 作成が可能かを診断する。
# クラウドセッションは VM が揮発するため、push できない環境で実装を進めると
# 成果物を失う。作業開始時点（このスクリプトの実行時）に分かるようにしておく。
show_git_status() {
    echo
    echo "--- git / PR ---"
    remote_name="$(git -C "$ROOT_DIR" remote 2>/dev/null | head -1)"
    branch_name="$(git -C "$ROOT_DIR" branch --show-current 2>/dev/null)"
    git_warn=0

    if [ -n "$remote_name" ]; then
        echo "  remote: $(git -C "$ROOT_DIR" remote get-url "$remote_name" 2>/dev/null)"
    else
        echo "  remote: なし"
        git_warn=1
    fi

    if [ -n "$branch_name" ]; then
        echo "  branch: ${branch_name}"
    else
        echo "  branch: なし（detached HEAD）"
        git_warn=1
    fi

    if command -v gh >/dev/null 2>&1; then
        if timeout 10 gh auth status >/dev/null 2>&1; then
            echo "  gh:     認証済み"
        else
            echo "  gh:     未認証"
            git_warn=1
        fi
    else
        echo "  gh:     未インストール"
        git_warn=1
    fi

    if [ "$git_warn" -eq 1 ]; then
        echo
        echo "  WARNING: この環境からは push / PR 作成ができません。"
        echo "    環境が GitHub リポジトリに接続されていない可能性があります。"
        echo "    対処は docs/cloud/README.md の「PR まで作れる環境にする」を参照。"
        echo "    開発とテストは可能ですが、VM は揮発するため成果物は"
        echo "    パッチ（git format-patch）として書き出して手元へ渡すこと。"
    fi
}

print_ready() {
    echo "READY: baserCMS は起動済みです。"
    echo "  PHP コンテナ: bc-php / DB コンテナ: bc-db / 配置先: /var/www/html"
    echo "  ユニットテスト: docker exec bc-php sh -c 'cd /var/www/html && composer run-script test'"
}

if [ -z "${CLAUDE_CODE_REMOTE_SESSION_ID:-}" ] && [ "${CLOUD_FORCE:-0}" != "1" ]; then
    echo "LOCAL: クラウドセッションではありません。ローカルの実行環境は .github/instructions/local.instructions.md を参照してください。"
    exit 0
fi

#
# ここから先が起動判定の本体。
#
# SessionStart フックは $CLAUDE_PROJECT_DIR の解決が環境によって変わり、起動して
# いないことがある。フックは「先回りして始めておく」ための最適化に過ぎず、
# 確実な入口はこのスクリプトとする。未起動・停止のどちらでもここから復旧する。
#
# 1. 完了マークがあり、かつ実体も動いていれば起動済み
#
if [ -e "$DONE_FILE" ] && is_alive; then
    print_ready
    show_detail
    show_git_status
    exit 0
fi

#
# 2. 完了マークだけが残っている（VM 再起動などで実体が消えた）場合は stale として破棄する
#
if [ -e "$DONE_FILE" ]; then
    echo "STALE: 完了マークは残っていますが baserCMS は動いていません（VM 再起動の可能性）。"
    rm -f "$DONE_FILE"
fi

#
# 3. 失敗マークが残っている場合は自動で再試行せず内容を報告する
#
if [ -e "$FAILED_FILE" ]; then
    echo "FAILED: baserCMS の起動に失敗しています。tmp/cloud-up.log を確認してください。"
    echo "  再試行する場合: rm -f tmp/cloud-up.failed && docker/bin/cloud-status.sh"
    show_detail
    exit 1
fi

#
# 4. 未完了。cloud-up.sh を起動して完了を待つ
#
echo "STARTING: cloud-up.sh を起動します（最大 ${TIMEOUT} 秒待機）..."
start_cloud_up || exit 1

WAITED=0
while :; do
    if [ -e "$FAILED_FILE" ]; then
        echo "FAILED: baserCMS の起動に失敗しました。tmp/cloud-up.log を確認してください。"
        show_detail
        exit 1
    fi
    if [ -e "$DONE_FILE" ] && is_alive; then
        break
    fi
    if [ "$WAITED" -ge "$TIMEOUT" ]; then
        echo "TIMEOUT: ${TIMEOUT} 秒待ちましたが起動が完了しませんでした。"
        show_detail
        exit 1
    fi
    sleep 5
    WAITED=$((WAITED + 5))
done

print_ready
show_detail
show_git_status
exit 0
