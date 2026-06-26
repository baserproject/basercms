#!/usr/bin/env bash
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/common.sh"

REMOTE="${1:-}"; BRANCH="${2:-}"; MAX="${3:-20}"
{ [ -n "$REMOTE" ] && [ -n "$BRANCH" ]; } || \
  bcsa_usage "usage: push-with-retry.sh <remote> <branch> [max_retries]"
bcsa_require git

n=0
# 新規フォークは複製反映前に 'remote rejected (failure)' を返すため、
# 文字列ではなく push の終了コードで成否を判定する。
while ! git push -u "$REMOTE" "$BRANCH"; do
  n=$((n + 1))
  if [ "$n" -ge "$MAX" ]; then
    echo "push 失敗（$n 回試行）: $REMOTE $BRANCH" >&2
    exit 1
  fi
  echo "retry $n: フォーク反映待ち..." >&2
  git ls-remote --heads "$REMOTE" >/dev/null 2>&1 || true
done
echo "pushed: $REMOTE $BRANCH"
