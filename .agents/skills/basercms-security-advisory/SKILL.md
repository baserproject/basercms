---
name: basercms-security-advisory
description: baserCMS のリポジトリセキュリティアドバイザリ（GHSA・triage含む）対応を、一覧取得→指摘検証→課題別の修正→プライベートフォーク/ブランチ/PR作成→ローカル検証まで一気通貫で扱う手順とスクリプト。「セキュリティアドバイザリを確認」「triageの脆弱性を検証」「アドバイザリごとにフォークとPRを作って」「脆弱性修正をプルリクにまとめて」等のときに使う。Copilot/GHAはアドバイザリforkで使えないためローカル検証（/code-review・basercms-unittest）を正とする点、push反映待ちリトライ、共有ファイルのhunk分割、base追従の定番競合解決を収録。
license: MIT
---

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
3. 必要なら統合ブランチを個人フォーク（`<個人フォーク名>`）へ push して GHA を回す（**アドバイザリ fork では GHA は動かない**）

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
