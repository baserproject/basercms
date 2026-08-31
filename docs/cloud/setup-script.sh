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

service docker start || dockerd >/tmp/dockerd.log 2>&1 &
for i in $(seq 1 30); do docker info >/dev/null 2>&1 && break; sleep 1; done

# 5分制限に収めるため並列で pull する。
# 失敗しても docker compose up の時に遅延 pull されるので致命的ではない。
docker pull baserproject/basercms:php8.5 || true &
docker pull mysql:8.0 || true &
docker pull sj26/mailcatcher:latest || true &
docker pull phpmyadmin || true &
docker pull postgres:15.2 || true &
docker pull dpage/pgadmin4:7.8 || true &
wait

exit 0
