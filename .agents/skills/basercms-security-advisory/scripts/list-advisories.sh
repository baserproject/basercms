#!/usr/bin/env bash
set -euo pipefail
DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
. "$DIR/common.sh"
bcsa_require gh python3

STATE=""
if [ "${1:-}" = "--state" ]; then STATE="${2:-}"; fi

TMP="$(mktemp)"
trap 'rm -f "$TMP"' EXIT
gh api -X GET "/repos/$BCSA_UPSTREAM/security-advisories" -f per_page=100 --paginate > "$TMP"

python3 - "$TMP" "$STATE" <<'PY'
import sys, json
from collections import Counter
data = json.load(open(sys.argv[1]))
want = sys.argv[2] if len(sys.argv) > 2 else ""
c = Counter(a.get('state') for a in data)
print('total:', len(data), '| by state:', dict(c))
rows = [a for a in data if (not want or a.get('state') == want)]
rows.sort(key=lambda a: (a.get('severity') or '', a.get('ghsa_id') or ''))
for a in rows:
    print(f"[{a.get('ghsa_id')}] {a.get('state')} {a.get('severity')}  {a.get('summary')}")
PY
