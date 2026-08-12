#!/usr/bin/env bash
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/common.sh"
bcsa_require git

INTEG="${1:-security/triage-all-merged}"
BASE="$(bcsa_current_branch)"
echo "base=$BASE integration=$INTEG"

# 既存の統合ブランチは作り直す
if git show-ref --verify --quiet "refs/heads/$INTEG"; then
  git branch -D "$INTEG"
fi
git checkout -b "$INTEG"

# security/GHSA-* ブランチを列挙してマージ
mapfile -t BRANCHES < <(git for-each-ref --format='%(refname:short)' 'refs/heads/security/GHSA-*' | sort)
if [ "${#BRANCHES[@]}" -eq 0 ]; then
  echo "マージ対象の security/GHSA-* ブランチがありません" >&2
  exit 1
fi

for b in "${BRANCHES[@]}"; do
  if git merge --no-ff --no-edit "$b" >/dev/null 2>&1; then
    echo "✅ merged $b"
  else
    echo "❌ CONFLICT: $b — 解決後に再実行してください" >&2
    git merge --abort
    exit 1
  fi
done
echo "done: $INTEG（$BASE + ${#BRANCHES[@]} 件）"
