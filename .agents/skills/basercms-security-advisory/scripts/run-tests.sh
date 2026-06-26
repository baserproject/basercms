#!/usr/bin/env bash
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/common.sh"
bcsa_require docker

CONTAINER="${BCSA_CONTAINER:-basercms}"
FILTER=""
if [ "${1:-}" = "--filter" ]; then FILTER="${2:-}"; fi

# 詳細な実行・集計手順は basercms-unittest スキルを参照。
if [ -n "$FILTER" ]; then
  docker exec "$CONTAINER" sh -c "cd /var/www/html && vendor/bin/phpunit --no-coverage --filter ${FILTER} 2>&1 | tail -20"
else
  docker exec "$CONTAINER" sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage > /tmp/phpunit_full.log 2>&1; echo "EXIT=$?"; tail -8 /tmp/phpunit_full.log'
fi
