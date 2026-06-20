---
name: basercms-unittest
description: baserCMS（CakePHP5 / PHPUnit）のユニットテストをローカル Docker 環境で実行・調査する手順。「ユニットテストを実行して」「全テストを走らせて」「このテストだけ流して」「テスト失敗を調べて」等のときに参照する。コンテナ名・実行コマンド・権限自動承認のためのコマンド整形・失敗の集計と切り分け方を収録。
---

# baserCMS ユニットテスト実行ガイド（ローカル）

baserCMS のユニットテストは Docker コンテナ上で実行する。実行・絞り込み・失敗調査の定石をまとめる。

## 実行環境

- 実行は `docker compose`（`/Users/ryuring/Projects/catchup-docker/docker-compose.yml`）。
- **PHPコンテナ名: `basercms`**、baserCMS 配置先: **`/var/www/html`**。
- テスト設定: `phpunit.xml.dist`。`<testsuites>` にプラグインごとの testsuite が定義（BaserCore / BcBlog / BcCustomContent / BcMail / BcThemeFile …）。
- DB はローカル環境依存（過去に `bc-db` ホスト無しの失敗があったが、現在は `cu-db` コンテナを利用。この種の接続失敗は環境要因でスルー可）。

## コマンド整形（重要：権限の自動承認）

- 複合コマンドの**外側**にパイプ `|` やリダイレクト `>` を置くと権限の自動承認が効かず確認プロンプトになる。
- **`docker exec basercms sh -c '...'` の単一引用符内**にリダイレクト・`tail` 等をすべて収めると、単一の `docker exec` コマンド扱いになり自動承認される。
- 安全な読み取り専用コマンド（`grep`/`find`/`ls`/`cat`/`sed -n`/`head`/`tail`）は単体で使う。

## 実行コマンド

### 全テスト（フルスイート）
出力が大きいのでコンテナ内のファイルに保存し、末尾だけ表示する。完走まで約10分強かかるため、必要に応じてバックグラウンド実行する。
```
docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage > /tmp/phpunit_full.log 2>&1; tail -45 /tmp/phpunit_full.log'
```

### 単一ファイル / 単一メソッド
```
docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage plugins/baser-core/tests/TestCase/Model/Table/PagesTableTest.php 2>&1 | tail -20'
docker exec basercms sh -c 'cd /var/www/html && vendor/bin/phpunit --no-coverage --filter testBeforeSave plugins/baser-core/tests/TestCase/Model/Table/PagesTableTest.php 2>&1 | tail -20'
```

### 構文チェック（lint）
修正後は必ず実施。暗黙nullable等の非推奨警告も併せて出る。
```
docker exec basercms sh -c 'cd /var/www/html && php -l plugins/baser-core/src/Controller/Admin/ThemesController.php'
```

## 失敗の調査手順

1. **致命的か警告か**を判別。`logs/debug.log` の `debug:` は非推奨警告（動作継続）。`logs/error.log` や Fatal/Exception が本当のエラー。デバッグモードでは警告も画面表示され「エラー」に見えるので注意。
2. **失敗が多いときは根本原因単位で集計**。フルログから例外メッセージを正規化して集計し、systemic な原因（少数の原因が大量の失敗を生む）を先に特定する。
   ```
   docker exec basercms sh -c 'cd /var/www/html && grep -hoE "[A-Za-z\\\\]+Exception: .{0,80}|[A-Za-z\\\\]+Error: .{0,80}" /tmp/phpunit_full.log | sed -E "s/[0-9]+/N/g" | sort | uniq -c | sort -rn | head -30'
   ```
   テストクラス単位の集計:
   ```
   docker exec basercms sh -c 'cd /var/www/html && grep -hoE "^[0-9]+\) [A-Za-z0-9_\\\\]+Test::" /tmp/phpunit_full.log | sort | uniq -c | sort -rn | head -40'
   ```
3. **アプリ src 起因か、テスト/環境/fixture/i18n 起因かを切り分ける**。
   - `git diff HEAD -- <file>` … 当該ファイルが自分の変更対象か。
   - 未変更ファイル かつ プラグインロード依存（`Plugin::isLoaded('X')` が false で behavior 未アタッチ＝`Unknown method` 等）／DIコンテナ未登録（`Alias ... is not being managed by the container`）／外部プラグインクラス未ロード／**英語↔日本語メッセージ不一致**（ロケール/翻訳）なら、移行起因ではなく環境・テスト要因の可能性が高い。
   - **クリーンな baseline は作りにくい**点に注意：フレームワークを上げた後は vendor が入れ替わっているため、単純な `git stash` では移行前の状態を再現できない。
4. **修正 → lint → `--filter` で単体確認 → 全テスト再実行**で件数の改善と新規回帰を確認する。

## メモ

- 全体テストでメソッド名を表示したい場合は環境変数 `SHOW_TEST_METHOD=true`（`BcTestCase::setUp` が対応）。
- ローカル固有の事情は `.github/instructions/local.instructions.md`（`.gitignore` 対象で存在しない場合あり）も参照。

関連: 移行起因の不具合の修正レシピは `cakephp-migration` / `php-migration` スキルへ。
