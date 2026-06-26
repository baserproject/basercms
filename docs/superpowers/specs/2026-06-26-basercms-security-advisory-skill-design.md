# basercms-security-advisory スキル 設計書

- 日付: 2026-06-26
- 対象: baserCMS リポジトリ（monorepo）向け新規スキル
- 種別: 手順書型スキル ＋ 補助スクリプト

## 1. 目的

baserCMS のリポジトリセキュリティアドバイザリ（GitHub Security Advisories / GHSA、triage 状態を含む）への対応を、次の一連の工程として一気通貫で扱えるようにする。

1. アドバイザリの一覧取得と分類
2. 指摘の的確性の検証（実コードと突き合わせ）
3. 課題ごとの修正方針の確定
4. アドバイザリ単位でのプライベートフォーク作成・ブランチ作成・修正・PR 作成
5. ローカルでの検証（全 PR 統合ブランチ＋ユニットテスト）

この工程は手作業だと煩雑で、GitHub 固有の落とし穴（フォーク複製の反映待ち、Copilot/GHA がアドバイザリフォークで使えない等）も多い。再現性を高めるため、手順書（SKILL.md）と補助スクリプト（scripts/）で構成する。

## 2. スコープ

### 含む
- triage を含むアドバイザリの一覧・詳細取得
- 実コード（**実行時の現在のブランチ**）に対する指摘検証の進め方
- アドバイザリ単位のフォーク／ブランチ／コミット／push／PR 作成
- 全 PR を統合ブランチへマージし、ローカルで全テスト実行
- 最新 base ブランチへの追従（各 PR への base マージ）と定番競合の解決

### 含まない（既存スキルへ委譲）
- ユニットテストの実行・集計・切り分けの詳細手順 → `basercms-unittest`
- CakePHP / PHP / プラグイン移行起因の不具合修正レシピ → `cakephp-migration` / `php-migration` / `basercms-plugin-migration`

## 3. 前提・制約

- **対象 base ブランチ = 実行時の現在のブランチ**（`git rev-parse --abbrev-ref HEAD` で自動検出）。実行前に対象リリースブランチ（例: 5.3.x）を checkout しておく運用とする。
- **Copilot コードレビューはセキュリティアドバイザリのプライベートフォークでは実質利用できない**。レビューはローカルの `/code-review` を正規手順とする。
- **GitHub Actions はプライベートフォークで動かない**。CI 検証はローカル（Docker / `basercms-unittest`）で行う。GHA でも確認したい場合は、全 PR を取り込んだ統合ブランチを個人フォーク（例: `ryuring`）へ push して GHA を回す。
- フォーク作成 API（`POST /repos/{owner}/{repo}/security-advisories/{ghsa}/forks`）は既存アドバイザリが前提。新規発見の脆弱性は、扱いをユーザーに確認する。
- `gh` CLI が認証済みであること。

## 4. ディレクトリ構成

```
.agents/skills/basercms-security-advisory/
├── SKILL.md            # 手順書本体（frontmatter: name / description / license）
└── scripts/
    ├── list-advisories.sh      # アドバイザリ一覧（state別集計・triage抽出）
    ├── fetch-advisory.sh       # 個別アドバイザリ詳細（JSON/本文）取得
    ├── create-fork-branch.sh   # フォーク作成 → remote追加 → base(現ブランチ)からブランチ作成
    ├── push-with-retry.sh      # フォーク複製反映待ちの push リトライ（終了コード判定）
    ├── open-pr.sh              # base=現ブランチ で PR 作成
    ├── build-integration.sh    # 全PRブランチを統合ブランチへマージ（競合検出で停止）
    └── run-tests.sh            # basercms-unittest 連携でローカル全テスト実行
```

- `.claude/skills/` へのシンボリックリンク、`skills-lock.json` への登録は既存スキルと同方式で行う。

## 5. SKILL.md frontmatter（案）

```yaml
---
name: basercms-security-advisory
description: baserCMS のリポジトリセキュリティアドバイザリ（GHSA・triage含む）対応を、一覧取得→指摘検証→課題別の修正→プライベートフォーク/ブランチ/PR作成→ローカル検証まで一気通貫で扱う手順とスクリプト。「セキュリティアドバイザリを確認」「triageの脆弱性を検証」「アドバイザリごとにフォークとPRを作って」「脆弱性修正をプルリクにまとめて」等のときに使う。Copilot/GHAはアドバイザリforkで使えないためローカル検証（/code-review・basercms-unittest）を正とする点、push反映待ちリトライ、共有ファイルのhunk分割、base追従の定番競合解決を収録。
license: MIT
---
```

## 6. ワークフロー（SKILL.md 章立て）

1. **一覧と分類** — `gh api .../security-advisories --paginate` を state（triage/published/closed）別に集計し triage を抽出（`list-advisories.sh`）。
2. **指摘の検証** — 各詳細を取得（`fetch-advisory.sh`）し、現在のブランチの実コードと突き合わせて「的確 / 不正確 / 非該当」を判定。範囲が広い場合は読み取り専用の並列サブエージェントで分担する指針を記載。
3. **修正方針の確定** — 同一 sink の重複、共有ファイルの hunk 分割、非該当の却下を整理。判断が要る点（重複の扱い等）はユーザーに確認。
4. **課題別フォーク/ブランチ/PR** — アドバイザリ単位で `create-fork-branch.sh` → 該当 hunk のみ適用（複数アドバイザリが同一ファイルを触る場合は stash ではなく hunk 単位で手適用）→ どの脆弱性をどう直したか明確なコミット → `push-with-retry.sh` → `open-pr.sh`。
5. **ローカル検証** — `build-integration.sh` で全 PR を統合ブランチへマージ → `run-tests.sh`（`basercms-unittest`）で全テスト → 必要なら個人フォークへ push して GHA 確認。**Copilot/GHA がアドバイザリ fork で使えない**ことを明記。
6. **最新 base への追従** — origin/base が進んだら各 PR へ base をマージし、定番競合を解決。

## 7. 補助スクリプト仕様

すべて base = 現在のブランチを自動検出。`set -euo pipefail`。パイプ/リダイレクトは単一コマンド内に収め、権限の自動承認に配慮する。

| スクリプト | 引数 | 役割 / 出力 |
|---|---|---|
| `list-advisories.sh` | `[--state triage]` | advisories を集計。state別件数＋（triageの）ghsa_id/severity/summary 一覧を表示 |
| `fetch-advisory.sh` | `<GHSA-ID>` | 詳細 JSON を `/tmp/bc-advisories/<id>.json` に保存し、summary/severity/対象バージョン/本文を整形表示 |
| `create-fork-branch.sh` | `<GHSA-ID>` | ① `POST .../security-advisories/<id>/forks` でフォーク作成 ② remote `sec-<short>` 追加 ③ `git checkout -b security/<GHSA-ID>`（現ブランチ起点） |
| `push-with-retry.sh` | `<remote> <branch> [max_retries]` | `until git push` を終了コードで判定しリトライ（新規フォーク複製の反映待ち）。`->` 等の文字列での成功誤検知をしない |
| `open-pr.sh` | `<GHSA-ID> [--title T] [--body-file F]` | `gh pr create --repo <fork> --base <現ブランチ> --head security/<GHSA-ID>` |
| `build-integration.sh` | `[統合ブランチ名]` | 現ブランチから統合ブランチを作成し `security/GHSA-*` を順次 `--no-ff --no-edit` マージ。競合は検出して停止 |
| `run-tests.sh` | `[--filter X]` | `basercms-unittest` の Docker 実行を呼び出し、ログを集計（致命/警告を切り分け） |

## 8. 収録する落とし穴レシピ（実体験ベース）

- **Copilot レビュー不可・GHA 不動**: アドバイザリ fork では使えない。ローカル `/code-review` ＋ `basercms-unittest` が正。GHA 確認は個人 fork へ push。
- **push 反映待ち**: 新規 fork 直後は `remote rejected (failure)` が出る。終了コード判定でリトライ（`->` 等の grep 成功誤検知に注意）。
- **共有ファイルの hunk 分割**: 1 ファイルに複数アドバイザリの修正が混在する場合は `git checkout … -- file` で全取りせず、該当 hunk だけ手で適用（例: `PluginsService` の basename と php 実行パス検証）。
- **同一 sink の重複アドバイザリ**: 同一修正を複数 fork へ展開、または片方を重複としてクローズ。判断はユーザーに確認。
- **非該当の見極め**: フレームワークのデフォルト保護（ORM バインド / slug エンコード / `h()` 出力）で再現しないものは却下し、具体的な PoC を要求。報告時点のブランチ状態まで遡って確認する手も。
- **base 追従の定番競合**: `order() → orderBy()` 改名、パス検証の `realpath() === false` バイパス修正 × `$fullPath` 検証 の併合など。
- **フルスイートのフレイキー**: `CreateReleaseCommandTest`（テスト内で実 composer を実行）は単体では緑。環境要因は切り分ける。
- **権限境界の認可**: `permission.php` の Api/Admin と Admin の `auth` 整合を取る際は、管理画面 SPA（ビルド済み JS まで）の依存を確認してから変更する。

## 9. 既存スキル連携

- テスト実行: `basercms-unittest` に委譲（重複させない）。
- 移行起因の競合・非推奨: `cakephp-migration` / `php-migration` / `basercms-plugin-migration` を参照。

## 10. 成功基準

- 本スキル＋スクリプトのみで、triage 確認 → 課題別 PR 作成 → 統合ブランチでのローカル全テスト緑、までを再現できる。
- baserCMS の任意の活発なブランチ（実行時の現在のブランチ）を base として動作する。
- アドバイザリ fork で Copilot/GHA に頼らず、ローカルで検証が完結する。

## 11. 非目標（YAGNI）

- 修正内容そのものの自動生成（脆弱性の修正は都度の判断が必要なため、スクリプト化しない）。
- baserCMS 以外のプロジェクトへの汎用化（org/repo/コンテナ前提は baserCMS 固有のままとする）。
