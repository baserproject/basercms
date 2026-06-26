#!/usr/bin/env bash
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/common.sh"

GHSA="${1:-}"
[ -n "$GHSA" ] || bcsa_usage "usage: create-fork-branch.sh <GHSA-ID>"
bcsa_require gh git

BASE="$(bcsa_current_branch)"
FORK="$(bcsa_fork_fullname "$GHSA")"
REMOTE="$(bcsa_remote_name "$GHSA")"
BRANCH="security/$GHSA"

echo "base=$BASE fork=$FORK remote=$REMOTE branch=$BRANCH"

# 1) 一時プライベートフォークを作成（既に存在する場合もエラーにしない）
gh api -X POST "/repos/$BCSA_UPSTREAM/security-advisories/$GHSA/forks" >/dev/null 2>&1 || \
  echo "（フォークは既存か、作成APIが応答済み）"

# 2) remote 追加（既存なら URL 更新）
if git remote get-url "$REMOTE" >/dev/null 2>&1; then
  git remote set-url "$REMOTE" "git@github.com:$FORK.git"
else
  git remote add "$REMOTE" "git@github.com:$FORK.git"
fi

# 3) 現ブランチ起点でブランチ作成（既存ならチェックアウト）
if git show-ref --verify --quiet "refs/heads/$BRANCH"; then
  git checkout "$BRANCH"
else
  git checkout -b "$BRANCH"
fi

echo "done: $BRANCH (base $BASE) / remote $REMOTE -> $FORK"
