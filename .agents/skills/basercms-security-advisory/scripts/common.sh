#!/usr/bin/env bash
# basercms-security-advisory 共通関数。各スクリプトから source する。
set -euo pipefail

BCSA_UPSTREAM="${BCSA_UPSTREAM:-baserproject/basercms}"

bcsa_current_branch() {
  git rev-parse --abbrev-ref HEAD
}

bcsa_lower() {
  printf '%s' "$1" | tr '[:upper:]' '[:lower:]'
}

# フォーク full_name: <owner>/basercms-<lower ghsa id>
bcsa_fork_fullname() {
  local ghsa low owner
  ghsa="$1"; low="$(bcsa_lower "$ghsa")"; owner="${BCSA_UPSTREAM%%/*}"
  printf '%s/basercms-%s' "$owner" "$low"
}

# remote 名: sec-<lower ghsa id (先頭 'ghsa-' を除去)>
bcsa_remote_name() {
  local low; low="$(bcsa_lower "$1")"
  printf 'sec-%s' "${low#ghsa-}"
}

bcsa_require() {
  local c
  for c in "$@"; do
    command -v "$c" >/dev/null 2>&1 || { echo "必要なコマンドが見つかりません: $c" >&2; exit 1; }
  done
}

bcsa_usage() { echo "$1" >&2; exit 2; }
