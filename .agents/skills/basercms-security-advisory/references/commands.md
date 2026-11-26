# 補助コマンド集

SKILL.md の各手順で実行するコマンド。`<...>` を置き換えて、対象リポジトリ（basercms）のルートで実行する。
**base ブランチは実行時の現在のブランチ**。

## 共通の命名

| 変数 | 値 | 例（GHSA-AbCd-1234-xyzw） |
|---|---|---|
| `UPSTREAM` | 対象リポジトリ（既定 `baserproject/basercms`） | `baserproject/basercms` |
| `LOW` | GHSA-ID を小文字化 | `ghsa-abcd-1234-xyzw` |
| `FORK` | `<UPSTREAM の owner>/basercms-<LOW>` | `baserproject/basercms-ghsa-abcd-1234-xyzw` |
| `REMOTE` | `sec-<LOW から先頭 ghsa- を除いたもの>` | `sec-abcd-1234-xyzw` |
| `BRANCH` | `security/<GHSA-ID>` | `security/GHSA-AbCd-1234-xyzw` |

```bash
UPSTREAM="${BCSA_UPSTREAM:-baserproject/basercms}"
GHSA="<GHSA-ID>"
LOW="$(printf '%s' "$GHSA" | tr '[:upper:]' '[:lower:]')"
FORK="${UPSTREAM%%/*}/basercms-$LOW"
REMOTE="sec-${LOW#ghsa-}"
BRANCH="security/$GHSA"
BASE="$(git rev-parse --abbrev-ref HEAD)"
```

## list-advisories — 一覧と集計

```bash
UPSTREAM="${BCSA_UPSTREAM:-baserproject/basercms}"
gh api -X GET "/repos/$UPSTREAM/security-advisories" -f per_page=100 --paginate \
  --jq '.[] | "[\(.ghsa_id)] \(.state) \(.severity)  \(.summary)"' | sort -k2,3
```

state で絞るときは `--jq` を `'.[] | select(.state=="triage") | ...'` にする。件数は `| awk '{print $2}' | sort | uniq -c`。

## fetch-advisory — 個別詳細

```bash
mkdir -p /tmp/bc-advisories
gh api "/repos/$UPSTREAM/security-advisories/$GHSA" > "/tmp/bc-advisories/$GHSA.json"
gh api "/repos/$UPSTREAM/security-advisories/$GHSA" --jq '
  "GHSA: \(.ghsa_id) | state: \(.state) | severity: \(.severity)",
  "SUMMARY: \(.summary)",
  (.vulnerabilities[]? | "  affected: \(.package.name) | range: \(.vulnerable_version_range) | patched: \(.patched_versions)"),
  "---- DESCRIPTION:", (.description // "" | .[0:6000])'
```

## create-fork-branch — フォーク → remote → ブランチ

```bash
# 1) 一時プライベートフォーク（既存でもエラーにしない）
gh api -X POST "/repos/$UPSTREAM/security-advisories/$GHSA/forks" >/dev/null 2>&1 || echo "（フォークは既存か作成済み）"
# 2) remote 追加（既存なら URL 更新）
git remote get-url "$REMOTE" >/dev/null 2>&1 \
  && git remote set-url "$REMOTE" "git@github.com:$FORK.git" \
  || git remote add "$REMOTE" "git@github.com:$FORK.git"
# 3) 現ブランチ起点でブランチ作成（既存ならチェックアウト）
git show-ref --verify --quiet "refs/heads/$BRANCH" && git checkout "$BRANCH" || git checkout -b "$BRANCH"
```

## push-with-retry — 反映待ちリトライ付き push

新規フォーク直後は `remote rejected (failure)` が返る。**文字列ではなく終了コードで判定**する。

```bash
n=0; MAX=20
until git push -u "$REMOTE" "$BRANCH"; do
  n=$((n + 1)); [ "$n" -ge "$MAX" ] && { echo "push 失敗（$n 回）" >&2; break; }
  echo "retry $n: フォーク反映待ち..." >&2
  sleep 3
done
```

## open-pr — base=現ブランチで PR 作成

```bash
gh pr create --repo "$FORK" --base "$BASE" --head "$BRANCH" \
  --title "fix: $GHSA のセキュリティ修正" --body "対象アドバイザリ: $GHSA"   # 本文はファイルなら --body-file <F>
```

## build-integration — 全 security/GHSA-* を統合ブランチへマージ

既存の統合ブランチは作り直す（`git branch -D` を含むので、残したい変更がないか先に確認する）。競合したら止めて解決後にやり直す。

```bash
INTEG="${INTEG:-security/triage-all-merged}"
git show-ref --verify --quiet "refs/heads/$INTEG" && git branch -D "$INTEG"
git checkout -b "$INTEG"
for b in $(git for-each-ref --format='%(refname:short)' 'refs/heads/security/GHSA-*' | sort); do
  if git merge --no-ff --no-edit "$b" >/dev/null 2>&1; then echo "✅ merged $b"
  else echo "❌ CONFLICT: $b" >&2; git merge --abort; break; fi
done
```

## run-tests — ローカル全テスト

詳細な実行・集計手順は `basercms-unittest` を参照。コンテナ名は既定 `basercms`。

```bash
CONTAINER="${BCSA_CONTAINER:-basercms}"
# 全件
docker exec "$CONTAINER" sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage > /tmp/phpunit_full.log 2>&1; echo "EXIT=$?"; tail -8 /tmp/phpunit_full.log'
# 絞り込み
docker exec "$CONTAINER" sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage --filter <X> 2>&1 | tail -20'
```
