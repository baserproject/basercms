# basercms-security-advisory スキル Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** baserCMS のセキュリティアドバイザリ対応（一覧→検証→課題別フォーク/ブランチ/PR→ローカル検証）を再現可能にする手順書型スキル `basercms-security-advisory`（SKILL.md ＋ 補助スクリプト）を作成する。

**Architecture:** `.agents/skills/basercms-security-advisory/` に SKILL.md と `scripts/*.sh` を作成し、`.claude/skills/` へシンボリックリンクして即利用可能にする。スクリプトは共通関数 `common.sh` を source し、base ブランチは「現在のブランチ」を自動検出する。実テスト実行は既存スキル `basercms-unittest` に委譲する。

**Tech Stack:** Bash（`set -euo pipefail`）, `gh` CLI, `git`, `python3`(JSON整形), Docker（テスト実行は basercms-unittest 経由）, Markdown（SKILL.md）。

## Global Constraints

- 作業ブランチ: `skill/basercms-security-advisory`（既存。5.3.x からの派生）。
- スキル配置: `.agents/skills/basercms-security-advisory/`、`.claude/skills/basercms-security-advisory` は `../../.agents/skills/basercms-security-advisory` への相対シンボリックリンク。
- `skills-lock.json` は**変更しない**（外部 source 管理用。本スキルはローカル暫定。baser-skills への正規化は後追い）。
- すべてのスクリプトは先頭で `#!/usr/bin/env bash` と `set -euo pipefail`。実行権限を付与（`chmod +x`）。
- パイプ/リダイレクトはスクリプト内部に閉じ込める（AGENTS.md の権限自動承認方針）。`gh` 出力を python へ渡す際は一時ファイル経由にし、ヒアドキュメントと stdin パイプを競合させない。
- 対象 upstream は環境変数 `BCSA_UPSTREAM`（既定 `baserproject/basercms`）。base ブランチは常に現在のブランチ。
- フォーク full_name = `<owner>/basercms-<小文字化したGHSA-ID>`、remote 名 = `sec-<小文字化しGHSA-接頭辞を除いたID>`。
- shellcheck は環境に無い前提。構文検査は `bash -n` を用いる。
- コミットメッセージ末尾に `Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>` を付ける。

---

### Task 1: スキル雛形（ディレクトリ・SKILL.md frontmatter・シンボリックリンク）

**Files:**
- Create: `.agents/skills/basercms-security-advisory/SKILL.md`
- Create (symlink): `.claude/skills/basercms-security-advisory`

**Interfaces:**
- Produces: スキルディレクトリと、Claude が読み込む symlink。SKILL.md 本文は Task 9 で追記する（ここでは frontmatter ＋ 見出し骨子のみ）。

- [ ] **Step 1: ディレクトリと SKILL.md（frontmatter＋骨子）を作成**

`.agents/skills/basercms-security-advisory/SKILL.md`:
```markdown
---
name: basercms-security-advisory
description: baserCMS のリポジトリセキュリティアドバイザリ（GHSA・triage含む）対応を、一覧取得→指摘検証→課題別の修正→プライベートフォーク/ブランチ/PR作成→ローカル検証まで一気通貫で扱う手順とスクリプト。「セキュリティアドバイザリを確認」「triageの脆弱性を検証」「アドバイザリごとにフォークとPRを作って」「脆弱性修正をプルリクにまとめて」等のときに使う。Copilot/GHAはアドバイザリforkで使えないためローカル検証（/code-review・basercms-unittest）を正とする点、push反映待ちリトライ、共有ファイルのhunk分割、base追従の定番競合解決を収録。
license: MIT
---

# baserCMS セキュリティアドバイザリ対応ガイド

（本文は後続タスクで追記）

## 0. 前提
## 1. 一覧と分類
## 2. 指摘の検証
## 3. 修正方針の確定
## 4. 課題別フォーク/ブランチ/PR
## 5. ローカル検証
## 6. 最新baseへの追従
## 7. 落とし穴レシピ
## 8. 補助スクリプト一覧
## 9. 既存スキル連携
```

- [ ] **Step 2: symlink を作成**

Run:
```bash
cd /Users/ryuring/Projects/basercms
ln -s ../../.agents/skills/basercms-security-advisory .claude/skills/basercms-security-advisory
```

- [ ] **Step 3: 検証（frontmatter と symlink 解決）**

Run:
```bash
cd /Users/ryuring/Projects/basercms
head -4 .claude/skills/basercms-security-advisory/SKILL.md
readlink .claude/skills/basercms-security-advisory
test -f .claude/skills/basercms-security-advisory/SKILL.md && echo "OK: symlink resolves"
```
Expected: frontmatter（`name: basercms-security-advisory`）が表示され、`OK: symlink resolves` が出る。

- [ ] **Step 4: Commit**

```bash
cd /Users/ryuring/Projects/basercms
git add .agents/skills/basercms-security-advisory/SKILL.md .claude/skills/basercms-security-advisory
git commit -m "feat(skill): basercms-security-advisory の雛形を追加

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 2: 共通関数 `common.sh`

**Files:**
- Create: `.agents/skills/basercms-security-advisory/scripts/common.sh`

**Interfaces:**
- Produces（他スクリプトが source して使用）:
  - `bcsa_current_branch()` → 現在のブランチ名を stdout
  - `bcsa_lower <s>` → 小文字化
  - `bcsa_fork_fullname <GHSA-ID>` → `<owner>/basercms-<lower id>`
  - `bcsa_remote_name <GHSA-ID>` → `sec-<lower id 'ghsa-'除去>`
  - `bcsa_require <cmd...>` → 不足コマンドがあれば exit 1
  - `bcsa_usage <msg>` → メッセージを stderr 出力し exit 2
  - 変数 `BCSA_UPSTREAM`（既定 `baserproject/basercms`）

- [ ] **Step 1: common.sh を作成**

`.agents/skills/basercms-security-advisory/scripts/common.sh`:
```bash
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
```

- [ ] **Step 2: 構文チェックと関数の動作確認**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
bash -n common.sh && echo "syntax OK"
bash -c '. ./common.sh; bcsa_fork_fullname GHSA-pqpg-933x-f4c5; bcsa_remote_name GHSA-pqpg-933x-f4c5'
```
Expected:
```
syntax OK
baserproject/basercms-ghsa-pqpg-933x-f4c5
sec-pqpg-933x-f4c5
```

- [ ] **Step 3: Commit**

```bash
cd /Users/ryuring/Projects/basercms
chmod +x .agents/skills/basercms-security-advisory/scripts/common.sh
git add .agents/skills/basercms-security-advisory/scripts/common.sh
git commit -m "feat(skill): common.sh（共通関数）を追加

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 3: `list-advisories.sh`

**Files:**
- Create: `.agents/skills/basercms-security-advisory/scripts/list-advisories.sh`

**Interfaces:**
- Consumes: `common.sh`（`BCSA_UPSTREAM`, `bcsa_require`）
- Produces: アドバイザリの state別件数と一覧を stdout。`--state <state>` で絞り込み。

- [ ] **Step 1: スクリプトを作成**

`.agents/skills/basercms-security-advisory/scripts/list-advisories.sh`:
```bash
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
```

- [ ] **Step 2: 構文チェック**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
bash -n list-advisories.sh && echo "syntax OK"
```
Expected: `syntax OK`

- [ ] **Step 3: 動作確認（実 API・読み取り専用）**

Run:
```bash
cd /Users/ryuring/Projects/basercms
.agents/skills/basercms-security-advisory/scripts/list-advisories.sh --state triage | head -5
```
Expected: `total: N | by state: {...}` に続き、triage のアドバイザリ行が表示される（権限が無い/ネットワーク不可の場合はスキップしてよい＝環境要因）。

- [ ] **Step 4: Commit**

```bash
cd /Users/ryuring/Projects/basercms
chmod +x .agents/skills/basercms-security-advisory/scripts/list-advisories.sh
git add .agents/skills/basercms-security-advisory/scripts/list-advisories.sh
git commit -m "feat(skill): list-advisories.sh を追加

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 4: `fetch-advisory.sh`

**Files:**
- Create: `.agents/skills/basercms-security-advisory/scripts/fetch-advisory.sh`

**Interfaces:**
- Consumes: `common.sh`
- Produces: 詳細 JSON を `/tmp/bc-advisories/<GHSA-ID>.json` に保存し、要約を stdout。引数 `<GHSA-ID>` 必須。

- [ ] **Step 1: スクリプトを作成**

`.agents/skills/basercms-security-advisory/scripts/fetch-advisory.sh`:
```bash
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
```

- [ ] **Step 2: 構文チェックと引数バリデーション**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
bash -n fetch-advisory.sh && echo "syntax OK"
( ./fetch-advisory.sh; echo "exit=$?" ) 2>&1 | tail -2
```
Expected: `syntax OK` の後、`usage: fetch-advisory.sh <GHSA-ID>` と `exit=2`。

- [ ] **Step 3: Commit**

```bash
cd /Users/ryuring/Projects/basercms
chmod +x .agents/skills/basercms-security-advisory/scripts/fetch-advisory.sh
git add .agents/skills/basercms-security-advisory/scripts/fetch-advisory.sh
git commit -m "feat(skill): fetch-advisory.sh を追加

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 5: `create-fork-branch.sh`

**Files:**
- Create: `.agents/skills/basercms-security-advisory/scripts/create-fork-branch.sh`

**Interfaces:**
- Consumes: `common.sh`（`bcsa_fork_fullname`, `bcsa_remote_name`, `bcsa_current_branch`）
- Produces: 一時プライベートフォーク作成、remote 追加、`security/<GHSA-ID>` ブランチを現ブランチ起点で作成。引数 `<GHSA-ID>` 必須。

- [ ] **Step 1: スクリプトを作成**

`.agents/skills/basercms-security-advisory/scripts/create-fork-branch.sh`:
```bash
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
```

- [ ] **Step 2: 構文チェックと引数バリデーション**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
bash -n create-fork-branch.sh && echo "syntax OK"
( ./create-fork-branch.sh; echo "exit=$?" ) 2>&1 | tail -2
```
Expected: `syntax OK` の後、`usage: create-fork-branch.sh <GHSA-ID>` と `exit=2`。

- [ ] **Step 3: Commit**

```bash
cd /Users/ryuring/Projects/basercms
chmod +x .agents/skills/basercms-security-advisory/scripts/create-fork-branch.sh
git add .agents/skills/basercms-security-advisory/scripts/create-fork-branch.sh
git commit -m "feat(skill): create-fork-branch.sh を追加

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 6: `push-with-retry.sh`

**Files:**
- Create: `.agents/skills/basercms-security-advisory/scripts/push-with-retry.sh`

**Interfaces:**
- Consumes: `common.sh`
- Produces: `git push -u <remote> <branch>` を**終了コードで判定**してリトライ（新規フォーク複製の反映待ち）。引数 `<remote> <branch> [max_retries=20]`。

- [ ] **Step 1: スクリプトを作成**

`.agents/skills/basercms-security-advisory/scripts/push-with-retry.sh`:
```bash
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
```

- [ ] **Step 2: 構文チェックと引数バリデーション**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
bash -n push-with-retry.sh && echo "syntax OK"
( ./push-with-retry.sh sec-x; echo "exit=$?" ) 2>&1 | tail -2
```
Expected: `syntax OK` の後、`usage: push-with-retry.sh <remote> <branch> [max_retries]` と `exit=2`。

- [ ] **Step 3: Commit**

```bash
cd /Users/ryuring/Projects/basercms
chmod +x .agents/skills/basercms-security-advisory/scripts/push-with-retry.sh
git add .agents/skills/basercms-security-advisory/scripts/push-with-retry.sh
git commit -m "feat(skill): push-with-retry.sh を追加

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 7: `open-pr.sh`

**Files:**
- Create: `.agents/skills/basercms-security-advisory/scripts/open-pr.sh`

**Interfaces:**
- Consumes: `common.sh`（`bcsa_fork_fullname`, `bcsa_current_branch`）
- Produces: フォーク内に base=現ブランチ・head=`security/<GHSA-ID>` の PR を作成。引数 `<GHSA-ID> [--title T] [--body-file F]`。

- [ ] **Step 1: スクリプトを作成**

`.agents/skills/basercms-security-advisory/scripts/open-pr.sh`:
```bash
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
```

- [ ] **Step 2: 構文チェックと引数バリデーション**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
bash -n open-pr.sh && echo "syntax OK"
( ./open-pr.sh; echo "exit=$?" ) 2>&1 | tail -2
```
Expected: `syntax OK` の後、`usage: open-pr.sh <GHSA-ID> [--title T] [--body-file F]` と `exit=2`。

- [ ] **Step 3: Commit**

```bash
cd /Users/ryuring/Projects/basercms
chmod +x .agents/skills/basercms-security-advisory/scripts/open-pr.sh
git add .agents/skills/basercms-security-advisory/scripts/open-pr.sh
git commit -m "feat(skill): open-pr.sh を追加

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 8: `build-integration.sh`

**Files:**
- Create: `.agents/skills/basercms-security-advisory/scripts/build-integration.sh`

**Interfaces:**
- Consumes: `common.sh`（`bcsa_current_branch`）
- Produces: 現ブランチから統合ブランチを作成し、ローカルの `security/GHSA-*` ブランチを順次 `--no-ff --no-edit` マージ。競合時は中断して該当ブランチ名を表示。引数 `[統合ブランチ名=security/triage-all-merged]`。

- [ ] **Step 1: スクリプトを作成**

`.agents/skills/basercms-security-advisory/scripts/build-integration.sh`:
```bash
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
```

- [ ] **Step 2: 構文チェック**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
bash -n build-integration.sh && echo "syntax OK"
```
Expected: `syntax OK`

- [ ] **Step 3: Commit**

```bash
cd /Users/ryuring/Projects/basercms
chmod +x .agents/skills/basercms-security-advisory/scripts/build-integration.sh
git add .agents/skills/basercms-security-advisory/scripts/build-integration.sh
git commit -m "feat(skill): build-integration.sh を追加

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 9: `run-tests.sh`

**Files:**
- Create: `.agents/skills/basercms-security-advisory/scripts/run-tests.sh`

**Interfaces:**
- Consumes: `common.sh`、Docker コンテナ（`BCSA_CONTAINER` 既定 `basercms`、パス `/var/www/html`）
- Produces: コンテナ内で `vendor/bin/phpunit --no-coverage` を実行しログ末尾を表示。`--filter <X>` で単一テスト。テスト手順詳細は `basercms-unittest` スキルに委譲。

- [ ] **Step 1: スクリプトを作成**

`.agents/skills/basercms-security-advisory/scripts/run-tests.sh`:
```bash
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
```

- [ ] **Step 2: 構文チェック**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
bash -n run-tests.sh && echo "syntax OK"
```
Expected: `syntax OK`

- [ ] **Step 3: Commit**

```bash
cd /Users/ryuring/Projects/basercms
chmod +x .agents/skills/basercms-security-advisory/scripts/run-tests.sh
git add .agents/skills/basercms-security-advisory/scripts/run-tests.sh
git commit -m "feat(skill): run-tests.sh を追加

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 10: SKILL.md 本文（ワークフロー・落とし穴レシピ・連携）

**Files:**
- Modify: `.agents/skills/basercms-security-advisory/SKILL.md`

**Interfaces:**
- Consumes: Task 2–9 で作成した全スクリプトのパス・引数（本文から参照）。

- [ ] **Step 1: SKILL.md 本文を追記（骨子セクションを内容で置換）**

`.agents/skills/basercms-security-advisory/SKILL.md`（frontmatter は維持し、`# baserCMS セキュリティアドバイザリ対応ガイド` 以降を以下で置換）:
```markdown
# baserCMS セキュリティアドバイザリ対応ガイド

baserCMS のリポジトリセキュリティアドバイザリ（GHSA・triage 含む）を、一覧→検証→課題別修正→フォーク/ブランチ/PR→ローカル検証まで一気通貫で扱う。スクリプトは `scripts/` 配下。**base ブランチは実行時の現在のブランチ**（事前に対象リリースブランチを checkout しておく）。

## 0. 前提
- `gh` CLI 認証済み。対象 upstream は `BCSA_UPSTREAM`（既定 `baserproject/basercms`）。
- **Copilot レビューと GitHub Actions はアドバイザリのプライベートフォークでは使えない**。レビューはローカル `/code-review`、テストはローカル Docker（`basercms-unittest`）が正。
- フォーク full_name=`<owner>/basercms-<小文字GHSA>`、remote=`sec-<小文字GHSA(先頭ghsa-除去)>`、ブランチ=`security/<GHSA-ID>`。

## 1. 一覧と分類
`scripts/list-advisories.sh [--state triage]` で state別件数と一覧を取得し、triage を抽出する。

## 2. 指摘の検証
`scripts/fetch-advisory.sh <GHSA-ID>` で詳細を取得し、**現在のブランチの実コード**と突き合わせて「的確 / 不正確 / 非該当」を判定する。対象が多い場合は読み取り専用の並列サブエージェントで分担する。フレームワークのデフォルト保護（ORM バインド / slug エンコード / `h()` 出力）で再現しないものは非該当として却下し、具体的な PoC を要求する。

## 3. 修正方針の確定
同一 sink の重複アドバイザリ、共有ファイルの hunk 分割、非該当の却下を整理する。重複の扱い（複数 fork へ同一修正 / 片方を重複クローズ）など判断が要る点はユーザーに確認する。

## 4. 課題別フォーク/ブランチ/PR
アドバイザリ単位で:
1. `scripts/create-fork-branch.sh <GHSA-ID>` — フォーク作成 → remote 追加 → 現ブランチ起点でブランチ作成
2. 該当 hunk のみ適用（複数アドバイザリが同一ファイルを触る場合は `git checkout … -- file` で全取りせず hunk 単位で手適用）
3. どの脆弱性をどう直したか明確なメッセージでコミット
4. `scripts/push-with-retry.sh <remote> security/<GHSA-ID>` — フォーク反映待ちのリトライ付き push
5. `scripts/open-pr.sh <GHSA-ID> [--title T] [--body-file F]` — base=現ブランチで PR 作成

## 5. ローカル検証
1. `scripts/build-integration.sh [統合ブランチ名]` — 現ブランチ＋全 `security/GHSA-*` をマージ（競合は停止）
2. `scripts/run-tests.sh` — ローカル全テスト（詳細は `basercms-unittest`）
3. 必要なら統合ブランチを個人フォーク（例 `ryuring`）へ push して GHA を回す（**アドバイザリ fork では GHA は動かない**）

## 6. 最新 base への追従
origin/base が進んだら各 PR ブランチへ base をマージし、push し直す。定番競合:
- `order() → orderBy()`（CakePHP 5.2 改名）
- パス検証 `realpath() === false` バイパス修正 × `$fullPath` 検証 の併合

## 7. 落とし穴レシピ
- **Copilot/GHA 不可**: アドバイザリ fork では使えない。ローカル検証が正。
- **push 反映待ち**: 新規 fork 直後は `remote rejected (failure)`。終了コードでリトライ（`->` 等の文字列で成功誤検知しない）。
- **共有ファイルの hunk 分割**: 例 `PluginsService` の basename と php 実行パス検証は別アドバイザリ。hunk 単位で分けて適用。
- **同一 sink の重複**: 同一修正を複数 fork へ、または片方を重複クローズ。
- **非該当の見極め**: framework デフォルト保護で再現しないものは却下。報告時点のブランチ状態まで遡って確認。
- **フルスイートのフレイキー**: `CreateReleaseCommandTest`（実 composer 実行）は単体では緑。環境要因を切り分ける。
- **認可境界**: `permission.php` の Api/Admin と Admin の `auth` 整合は、管理画面 SPA（ビルド済み JS まで）の依存を確認してから変更。

## 8. 補助スクリプト一覧
| スクリプト | 引数 | 役割 |
|---|---|---|
| list-advisories.sh | `[--state S]` | アドバイザリ一覧・集計 |
| fetch-advisory.sh | `<GHSA-ID>` | 個別詳細取得（/tmp/bc-advisories へ保存） |
| create-fork-branch.sh | `<GHSA-ID>` | フォーク作成→remote→現ブランチ起点ブランチ |
| push-with-retry.sh | `<remote> <branch> [max]` | 反映待ちリトライ push |
| open-pr.sh | `<GHSA-ID> [--title T] [--body-file F]` | base=現ブランチで PR 作成 |
| build-integration.sh | `[統合ブランチ名]` | 全 PR を統合ブランチへマージ |
| run-tests.sh | `[--filter X]` | ローカル全テスト（basercms-unittest 連携） |

## 9. 既存スキル連携
- テスト実行: `basercms-unittest`
- 移行起因の競合・非推奨: `cakephp-migration` / `php-migration` / `basercms-plugin-migration`
```

- [ ] **Step 2: 検証（スクリプト参照の整合）**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory
for s in list-advisories fetch-advisory create-fork-branch push-with-retry open-pr build-integration run-tests common; do
  test -f "scripts/$s.sh" && echo "OK scripts/$s.sh" || echo "MISSING scripts/$s.sh"
done
grep -c "scripts/" SKILL.md
```
Expected: 8 個すべて `OK`、`grep -c` は 1 以上。

- [ ] **Step 3: Commit**

```bash
cd /Users/ryuring/Projects/basercms
git add .agents/skills/basercms-security-advisory/SKILL.md
git commit -m "docs(skill): basercms-security-advisory の本文（手順・レシピ）を追記

Co-Authored-By: Claude Opus 4.8 <noreply@anthropic.com>"
```

---

### Task 11: スキル発火と全体スモークの最終確認

**Files:**
- 変更なし（確認のみ）

- [ ] **Step 1: スキルが Claude から発見可能か（symlink 経由で SKILL.md が読めるか）**

Run:
```bash
cd /Users/ryuring/Projects/basercms
sed -n '1,3p' .claude/skills/basercms-security-advisory/SKILL.md
ls -l .claude/skills/basercms-security-advisory
```
Expected: frontmatter の `name: basercms-security-advisory` が表示され、symlink が `../../.agents/skills/basercms-security-advisory` を指す。

- [ ] **Step 2: 全スクリプトの構文一括チェック**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
for f in *.sh; do bash -n "$f" && echo "OK $f"; done
```
Expected: 全 `.sh` が `OK`。

- [ ] **Step 3: 実行権限の確認**

Run:
```bash
cd /Users/ryuring/Projects/basercms/.agents/skills/basercms-security-advisory/scripts
ls -l *.sh | awk '{print $1, $NF}'
```
Expected: 各スクリプトに実行ビット（`-rwxr-xr-x` 等）。無ければ `chmod +x *.sh` してコミット。

---

## 自己レビュー結果

- **Spec カバレッジ**: spec の §4 構成（SKILL.md＋7スクリプト）→ Task 1〜10、§5 frontmatter → Task 1、§6 ワークフロー → Task 10、§7 スクリプト仕様 → Task 2〜9、§8 落とし穴レシピ → Task 10、§9 連携 → Task 10、§10 成功基準 → Task 11 のスモーク、で網羅。`skills-lock.json` は spec §3/Global Constraints の通り非変更（option 2）。
- **プレースホルダ**: なし（全コード・全コマンド・期待値を記載）。
- **型/名称整合**: `common.sh` の関数名（`bcsa_current_branch`/`bcsa_fork_fullname`/`bcsa_remote_name`/`bcsa_require`/`bcsa_usage`）は Task 5〜9 の利用箇所と一致。フォーク命名・remote 命名・ブランチ命名は全タスクで統一。
