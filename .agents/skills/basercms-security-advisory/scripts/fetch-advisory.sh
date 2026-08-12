#!/usr/bin/env bash
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/common.sh"

GHSA="${1:-}"
[ -n "$GHSA" ] || bcsa_usage "usage: fetch-advisory.sh <GHSA-ID>"
bcsa_require gh python3

OUTDIR="/tmp/bc-advisories"
mkdir -p "$OUTDIR"
OUT="$OUTDIR/$GHSA.json"
gh api "/repos/$BCSA_UPSTREAM/security-advisories/$GHSA" > "$OUT"

python3 - "$OUT" <<'PY'
import sys, json
d = json.load(open(sys.argv[1]))
print('GHSA:', d.get('ghsa_id'), '| state:', d.get('state'), '| severity:', d.get('severity'))
print('SUMMARY:', d.get('summary'))
for v in (d.get('vulnerabilities') or []):
    pkg = (v.get('package') or {}).get('name')
    print('  affected:', pkg, '| range:', v.get('vulnerable_version_range'), '| patched:', v.get('patched_versions'))
print('-' * 40, 'DESCRIPTION:')
print((d.get('description') or '')[:6000])
PY
echo "saved: $OUT"
