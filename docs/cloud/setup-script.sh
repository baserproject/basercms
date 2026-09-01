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

# dockerd はこのあとも常駐させる必要があるため、バックグラウンドで起動する。
# `&` は `service docker start || dockerd ...` の || リスト全体に掛かるので、
# service 側が失敗すると dockerd がこのシェルのジョブとして残り続ける。
service docker start >/dev/null 2>&1 || dockerd >/tmp/dockerd.log 2>&1 &
for i in $(seq 1 30); do docker info >/dev/null 2>&1 && break; sleep 1; done

# 5分制限に収めるため並列で pull する。
# 失敗しても docker compose up の時に遅延 pull されるので致命的ではない。
#
# 【重要】引数なしの `wait` を書いてはいけない。
# 引数なしの `wait` は上の dockerd のジョブも待ってしまい、dockerd は終了しないため
# 永久に返らない。実際にこれでセットアップスクリプトがハングし、リポジトリの
# クローンもこの下の処理も一切行われない状態になった。
# pull のジョブ ID だけを明示的に待つこと。
pull_pids=""
for image in baserproject/basercms:php8.5 \
             mysql:8.0 \
             sj26/mailcatcher:latest \
             phpmyadmin \
             postgres:15.2 \
             dpage/pgadmin4:7.8; do
    docker pull "$image" >/dev/null 2>&1 &
    pull_pids="${pull_pids} $!"
done
wait $pull_pids

#
# gh（GitHub CLI）の導入
#
# セッション内で PR まで作るには gh が要る。GitHub 接続済みの標準環境では
# 最初から入っていることが多いため、無いときだけ入れる。
# 失敗してもセッション起動を止めないよう、握りつぶして先へ進む。
#
if ! command -v gh >/dev/null 2>&1; then
    (
        apt-get update -qq \
            && apt-get install -y -qq --no-install-recommends gh
    ) >/tmp/gh-install.log 2>&1 || true
    command -v gh >/dev/null 2>&1 \
        && echo "gh installed." \
        || echo "WARNING: failed to install gh. See /tmp/gh-install.log."
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

exit 0
