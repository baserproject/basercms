#!/bin/bash
#
# Claude Code on the web（クラウドセッション）用 Setup script
#
# このファイルは「コピー元の正本」です。実際には claude.ai/code の
# 環境ダイアログの「Setup script」欄に、以下の内容を貼り付けて使います。
# （Setup script はリポジトリからではなく環境設定から実行されます）
#
# 制約:
#   - 5分以内に終わること
#   - 必ず exit 0 すること（非ゼロで終わるとセッションが起動しない）
#
# ここで pull したイメージは環境キャッシュ（ファイルシステムのスナップショット）に
# 載るため、2回目以降のセッション起動が大幅に短縮されます。
#

# 各段階の経過秒を出す。Setup script が遅いときに、どこが支配的かを
# 環境のセットアップログから切り分けられるようにするため。
SETUP_STARTED_AT=$(date +%s)
step() { echo "[+$(( $(date +%s) - SETUP_STARTED_AT ))s] $*"; }

# dockerd はこのあとも常駐させる必要があるため、バックグラウンドで起動する。
# `&` は `service docker start || dockerd ...` の || リスト全体に掛かるので、
# service 側が失敗すると dockerd がこのシェルのジョブとして残り続ける。
step "starting dockerd"
service docker start >/dev/null 2>&1 || dockerd >/tmp/dockerd.log 2>&1 &
for i in $(seq 1 30); do docker info >/dev/null 2>&1 && break; sleep 1; done
step "dockerd ready"

# 5分制限に収めるため並列で pull する。
# 失敗しても docker compose up の時に遅延 pull されるので致命的ではない。
#
# 【重要】引数なしの `wait` を書いてはいけない。
# 引数なしの `wait` は上の dockerd のジョブも待ってしまい、dockerd は終了しないため
# 永久に返らない。実際にこれでセットアップスクリプトがハングし、リポジトリの
# クローンもこの下の処理も一切行われない状態になった。
# pull のジョブ ID だけを明示的に待つこと。
# pull するのはクラウドで実際に起動するイメージだけにする。
# 起動するサービスは cloud-up.sh の CLOUD_SERVICES と一致させること。
# phpMyAdmin / PostgreSQL / pgAdmin は起動しないため pull もしない（計 2.1GB 削減）。
step "pulling images"
pull_pids=""
for image in baserproject/basercms:php8.5 \
             mysql:8.0 \
             sj26/mailcatcher:latest; do
    docker pull "$image" >/dev/null 2>&1 &
    pull_pids="${pull_pids} $!"
done
wait $pull_pids
step "images pulled"

#
# gh（GitHub CLI）の導入
#
# セッション内で PR まで作るには gh が要る。GitHub 接続済みの標準環境では
# 最初から入っていることが多いため、無いときだけ入れる。
# 失敗してもセッション起動を止めないよう、握りつぶして先へ進む。
#
# 【重要】`apt-get update && apt-get install` と繋いではいけない。
# このイメージには deadsnakes / ondrej-php の PPA が入っており、
# ppa.launchpadcontent.net がネットワークの許可リストに無いため 403 で失敗する。
# 環境によってはこれが警告ではなくエラー扱いになり、apt-get update が非ゼロ終了して
# gh のインストールまで巻き添えで落ちる（実際にこれで gh が入っていなかった）。
# gh は Ubuntu 本体（noble-updates/universe）にあり、そちらのリストが取れていれば
# 入るので、update の終了コードで install をゲートしない。
#
# なお `-o Dir::Etc::sourceparts=/dev/null` で PPA を避けるのは誤り。
# Ubuntu noble ではメインアーカイブも /etc/apt/sources.list.d/ubuntu.sources に
# あり（/etc/apt/sources.list はコメントのみ）、本体ごと無効化されて
# 「Unable to locate package gh」になる。
if ! command -v gh >/dev/null 2>&1; then
    step "installing gh"
    (
        apt-get update -qq || true
        apt-get install -y -qq --no-install-recommends gh || true
    ) >/tmp/gh-install.log 2>&1
    command -v gh >/dev/null 2>&1 \
        && echo "gh installed." \
        || echo "WARNING: failed to install gh. See /tmp/gh-install.log."
    step "gh done"
fi

#
# SessionStart フックの設置
#
# リポジトリ内の .claude/settings.json は読み込まれない。クローン先が
# /workspace/repo のようにプロジェクトルート（/workspace）の配下になるため、
# プロジェクト設定として認識されるのは /workspace/.claude/settings.json だけ。
# よってフックはここで設置する。
#
# フックはクローン先のディレクトリ名に依存せず cloud-up.sh を探索し、
# 見つからなければ何もしない。発火の有無は /tmp/cloud-up-hook.log で確認できる。
# 既にファイルがある場合は尊重して上書きしない（冪等）。
#
if [ ! -e /workspace/.claude/settings.json ]; then
    mkdir -p /workspace/.claude
    cat > /workspace/.claude/settings.json <<'CLAUDE_SETTINGS_JSON'
{
  "hooks": {
    "SessionStart": [
      {
        "matcher": "startup|resume",
        "hooks": [
          {
            "type": "command",
            "command": "nohup bash -c 'for d in \"$CLAUDE_PROJECT_DIR\" \"$PWD\" /workspace/*; do [ -n \"$d\" ] && [ -f \"$d/docker/bin/cloud-up.sh\" ] && { echo \"[$(date +%FT%T)] SessionStart hook fired. root=$d\"; exec bash \"$d/docker/bin/cloud-up.sh\"; }; done' >>/tmp/cloud-up-hook.log 2>&1 &"
          }
        ]
      }
    ]
  }
}
CLAUDE_SETTINGS_JSON
fi

step "setup script finished"
exit 0
