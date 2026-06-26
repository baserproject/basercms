#!/usr/bin/env bash
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/common.sh"

GHSA="${1:-}"
[ -n "$GHSA" ] || bcsa_usage "usage: open-pr.sh <GHSA-ID> [--title T] [--body-file F]"
shift || true
bcsa_require gh git

TITLE=""; BODY_FILE=""
while [ $# -gt 0 ]; do
  case "$1" in
    --title) TITLE="${2:-}"; shift 2;;
    --body-file) BODY_FILE="${2:-}"; shift 2;;
    *) bcsa_usage "unknown arg: $1";;
  esac
done

BASE="$(bcsa_current_branch)"
FORK="$(bcsa_fork_fullname "$GHSA")"
HEAD="security/$GHSA"
[ -n "$TITLE" ] || TITLE="fix: $GHSA のセキュリティ修正"

args=(pr create --repo "$FORK" --base "$BASE" --head "$HEAD" --title "$TITLE")
if [ -n "$BODY_FILE" ]; then args+=(--body-file "$BODY_FILE"); else args+=(--body "対象アドバイザリ: $GHSA"); fi
gh "${args[@]}"
