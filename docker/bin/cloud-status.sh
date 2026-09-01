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
    # -a を付けないと停止したコンテナが出ず、落ちたことに気づけない。
    echo "--- docker compose ps -a ---"
    if [ -e "${DOCKER_DIR}/docker-compose.yml" ]; then
        (cd "$DOCKER_DIR" && docker compose ps -a) 2>&1
    else
        echo "docker/docker-compose.yml がまだありません。"
    fi
    echo
    # 直近の起動分だけを出す。ログはセッションを跨いで追記されるため、
    # 固定行数で切ると前回分と混ざって切り分けの邪魔になる。
    echo "--- tmp/cloud-up.log（直近の起動分） ---"
    if [ -e "$LOG_FILE" ]; then
        awk '/===== cloud-up start =====/ { buf = "" } { buf = buf $0 "\n" } END { printf "%s", buf }' "$LOG_FILE" \
            | tail -40
    else
        echo "tmp/cloud-up.log はまだありません。"
    fi
}

# 起動したはずなのに落ちているコンテナを拾う。
# READY の判定は bc-php / bc-db だけを見るため、それ以外が死んでいても READY になる。
# 判定は変えずに、気づけるように warning だけ出す。
show_exited_containers() {
    [ -e "${DOCKER_DIR}/docker-compose.yml" ] || return 0
    docker info >/dev/null 2>&1 || return 0

    exited="$( (cd "$DOCKER_DIR" && docker compose ps -a --status exited --format '{{.Name}} ({{.Status}})') 2>/dev/null )"
    [ -n "$exited" ] || return 0

    echo
    echo "  WARNING: 起動していないコンテナがあります。"
    echo "$exited" | sed 's/^/    /'
    echo "    baserCMS 本体（bc-php / bc-db）は動いているため READY ですが、"
    echo "    上記が必要な作業をする場合は docker logs <コンテナ名> で原因を確認してください。"
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
    remote_url=""
    # push できるか / PR を作れるかは別の条件で決まるため、フラグを分けて持つ。
    # ひとまとめにすると「gh が無いだけ」でも「push できません」と誤報する。
    push_ng=0
    pr_ng=0

    if [ -n "$remote_name" ]; then
        remote_url="$(git -C "$ROOT_DIR" remote get-url "$remote_name" 2>/dev/null)"
        echo "  remote: ${remote_url}"
    else
        echo "  remote: なし"
        push_ng=1
    fi

    if [ -n "$branch_name" ]; then
        echo "  branch: ${branch_name}"
    else
        echo "  branch: なし（detached HEAD）"
        push_ng=1
    fi

    # gh auth status はトークンが無効でも終了コード 0 を返すため判定に使えない。
    # 実際に PR を作れるかは REST API へ到達できるかで決まるので、そちらを見る。
    # 認証は agent proxy が注入するため、GH_TOKEN が無効表示でも到達できる。
    if command -v gh >/dev/null 2>&1; then
        gh_repo="$(echo "$remote_url" | sed -e 's#^.*github\.com[:/]##' -e 's#\.git$##')"
        if [ -n "$gh_repo" ] && timeout 20 gh api "repos/${gh_repo}" --jq .full_name >/dev/null 2>&1; then
            echo "  gh:     GitHub API へ到達可（PR 作成可）"
        else
            echo "  gh:     GitHub API へ到達不可"
            pr_ng=1
        fi
    else
        echo "  gh:     未インストール"
        pr_ng=1
    fi

    if [ "$push_ng" -eq 1 ]; then
        echo
        echo "  WARNING: 現状のままでは push できません（PR 作成も不可）。"
        echo "    環境の作り直しは不要で、このセッションのまま復旧できます。"
        if [ -z "$remote_name" ]; then
            echo "    - remote を追加する:"
            echo "        git remote add origin git@github.com:baserproject/basercms.git"
            echo "    - push が 'access denied by the git proxy' になったら、"
            echo "      add_repo ツールでリポジトリをセッションに追加する"
        fi
        if [ -z "$branch_name" ]; then
            echo "    - detached HEAD のため、push する前にブランチを切る:"
            echo "        git checkout -b <ブランチ名>"
        fi
        echo "    手順は docs/cloud/README.md の「PR まで作れる環境にする」を参照。"
    elif [ "$pr_ng" -eq 1 ]; then
        echo
        echo "  NOTE: push は可能ですが、PR 作成はこのままではできません。"
        if ! command -v gh >/dev/null 2>&1; then
            echo "    gh を入れれば解消します:"
            echo "      apt-get update -qq && apt-get install -y -qq --no-install-recommends gh"
        else
            echo "    gh は入っていますが GitHub API へ到達できません。"
            echo "    add_repo ツールでリポジトリをセッションに追加すると通ることがあります。"
        fi
        echo "    PR は gh pr create ではなく gh api の REST で作ります（GraphQL は 403）。"
    fi
}

print_ready() {
    echo "READY: baserCMS は起動済みです。"
    echo "  PHP コンテナ: bc-php / DB コンテナ: bc-db / 配置先: /var/www/html"
    echo "  ユニットテスト（全件）: docker exec bc-php sh -c 'cd /var/www/html && composer run-script test'"
    echo "  ユニットテスト（ファイル単位）: docker exec bc-php sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage <パス>'"
    echo "  （bin/cake setup test は cloud-up.sh が実行済みのため、phpunit を直接叩けます）"
}

# 出力は40行を超えるため、判定行が先頭にしか無いと `| tail` で流れて拾えなくなる。
# head でも tail でも判定できるよう、末尾にも1行のサマリを出す。
print_result() {
    echo
    echo "RESULT: $1"
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
    show_exited_containers
    show_git_status
    print_result "READY"
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
    print_result "FAILED"
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
        print_result "FAILED"
        exit 1
    fi
    if [ -e "$DONE_FILE" ] && is_alive; then
        break
    fi
    if [ "$WAITED" -ge "$TIMEOUT" ]; then
        echo "TIMEOUT: ${TIMEOUT} 秒待ちましたが起動が完了しませんでした。"
        show_detail
        print_result "TIMEOUT"
        exit 1
    fi
    sleep 5
    WAITED=$((WAITED + 5))
done

print_ready
show_detail
show_exited_containers
show_git_status
print_result "READY"
exit 0
