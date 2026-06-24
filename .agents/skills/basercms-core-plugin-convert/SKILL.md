---
name: basercms-core-plugin-convert
description: baserCMS の「通常プラグイン（サードパーティ／単体配布）」を monorepo の「コアプラグイン」に昇格させる手順。「コアプラグインに変更」「コアプラグイン化」「通常プラグインをコアに昇格」「monorepo に取り込む」等のときに参照する。プラグイン名の規約変更（bc- プレフィックス付与・CamelCase→ハイフン区切り）、.git/シンボリックリンク/standalone テスト基盤の除去、ルート phpunit.xml.dist・tests/bootstrap.php・composer.json・.gitignore への登録、baser-core setting.php の corePlugins/defaultInstallCorePlugins、phpdoc.dist.xml、split_monorepo.yml への追加、monorepo-builder merge による依存集約、全体テストでの実行確認、monorepo split 用 read-only リポジトリの確認までを収録。プラグインのバージョン非推奨対応（5.2→5.3）は basercms-plugin-migration スキル、テスト実行手順は basercms-unittest スキルを参照。
license: MIT
---

# baserCMS 通常プラグイン → コアプラグイン 昇格手順

サードパーティ／単体配布の baserCMS プラグインを、baserCMS 本体（monorepo）の**コアプラグイン**として取り込む手順。
参照: `plugins/bc-blog` 等の既存コアプラグインが「正」の構成。

> 前提と役割分担
> - バージョン移行（5.2→5.3 / PHP8.5）でのコード非推奨対応は `basercms-plugin-migration` スキル。
> - ユニットテストの実行・集計・切り分けは `basercms-unittest` スキル。
> - 本書は「通常プラグイン構成 → コアプラグイン構成」への**構造変換**に絞る。

---

## 0. 大原則 / 完成形

- コアプラグインは `plugins/<bc-name>/` に置かれ、**自前の `.git` / `vendor/` / `bin/` / `composer.lock` / `phpunit.xml.dist` / `tests/bootstrap.php` / `tests/TestApp/` を持たない**。テストは**アプリ全体のテスト基盤**で実行する。
- 依存・オートロード・テスト登録・CI 登録は**ルート側**に集約する。
- 完成形の例（`plugins/bc-blog`）: `README.md / composer.json / config/ / config.php / src/ / tests/ / webroot/`（＋必要なら `webpack.config.js`）。

---

## 1. プラグイン名をコアプラグイン規約へ変更

> 既に規約準拠の名前なら本節はスキップ。命名規約の詳細リネーム作業自体は別途 namespace 一括置換が必要（本スキルは「コア化に伴う命名規約」のみ規定）。

- **フォルダ名**: ケバブケースにし、**`bc-` プレフィックスを付与**する。
  - CamelCase は**ハイフン区切り**へ。例: `CuMcp` → `bc-mcp`、`MyAwesomePlugin` → `bc-my-awesome-plugin`。
- **namespace / プラグインクラス**: パスカルケースで `Bc` プレフィックス。例: `BcMcp`、メインクラス `BcMcpPlugin`。
- **composer パッケージ名**: `baserproject/<bc-name>`。例: `baserproject/bc-mcp`。
- フォルダ名（ケバブ）と namespace（パスカル）の対応: `bc-mcp` ⇔ `BcMcp`。
- 命名に伴う `namespace` / `use` / 文字列リテラル（`'plugin' => 'Xxx'`・URL スラッグ `/xxx`・コマンド名・設定キー）の一括置換、`composer.json` の `name`/autoload/`extra.cakephp.plugin-name` 変更も行う（誤爆防止のため先頭トークンのみ置換し、`Mcp/BcBlog` のようなサブ階層名は温存）。

---

## 2. プラグイン側の standalone 成果物を削除

monorepo では不要・有害（vendor 二重化や nested git）なものを削除する。
```
plugins/<bc-name>/
  .git                  ← nested リポジトリを削除（親 monorepo に取り込む）
  vendor/               ← 自前 vendor を削除（ルート vendor を使う）
  bin/                  ← 自前 cake バイナリを削除（ROOT/bin/cake を使う）
  composer.lock         ← 削除
  phpunit.xml.dist      ← 削除（ルート phpunit.xml.dist に testsuite を登録する）
  tests/bootstrap.php   ← 削除（ルート tests/bootstrap.php を使う）
  tests/TestApp/        ← 削除（ルート tests/TestApp を使う）
  .github/              ← 単体 CI は不要（split 先リポジトリでも別管理）。既存コアに無ければ削除
  .gitignore            ← 既存コアに無ければ削除
  VERSION.txt           ← 削除（バージョンは本体 monorepo 管理になる）
  CHANGELOG.md          ← 削除（変更履歴も本体管理）
  .phpunit.cache / .phpunit.result.cache / .DS_Store / .idea ← 削除
```
- 完成形は既存コア（`plugins/bc-blog`）に合わせる: `README.md / composer.json / config/ / config.php / src/ / tests/ / webroot/`（＋必要なら `templates/`・`webpack.config.js`）。
- ⚠️ `.git` 削除は不可逆。nested git の独自履歴が必要なら事前に退避。

## 3. ルート `.gitignore` の調整（2 種類・向きが逆なので注意）

### 3-1. プラグイン本体を「追跡対象」にする（無視の除外 `!`）
ルート `.gitignore` は `/plugins/*` で**全プラグインを無視**し、コアプラグインだけ `!` で**ホワイトリスト**している。コア化したプラグインを `!/plugins/<bc-name>` で追跡対象に加える（**これを忘れると `git add` されず取り込まれない**）。
```gitignore
# plugins
/plugins/*
!/plugins/baser-core
...
!/plugins/bc-seo
!/plugins/bc-mcp          ← 追加（無視の除外＝追跡対象化）
/plugins/*/vendor
/plugins/*/composer.lock
```
- 確認: `git check-ignore -v plugins/<bc-name>/composer.json`（何も返らなければ追跡対象）、`git status --short plugins/<bc-name>`。

### 3-2. webroot シンボリックリンクは「無視」する
baserCMS は `webroot/<plugin_underscored>` → `plugins/<bc-name>/webroot` のシンボリックリンクを生成する（例 `webroot/bc_mcp`）。実体は `plugins/<bc-name>/webroot` にあるため、**リンク側はルート `.gitignore` で無視**する（他コアと同じ並び）。
```gitignore
/webroot/bc_blog
/webroot/bc_custom_content
/webroot/bc_mcp          ← 追加（アンダースコア表記）
```
> 向きに注意: **`plugins/<bc-name>` は追跡（`!` で除外）／`webroot/<underscored>` は無視**。

---

## 4. テストを「全体実行」に載せ替える

### 4-1. ルート `phpunit.xml.dist` に testsuite を追加（⚠️ 配置順が重要）
（プラグイン側 phpunit.xml.dist は 2. で削除済み。**追加するのはルート側**。）
```xml
<testsuite name="BcMcp">
    <directory>plugins/bc-mcp/tests/TestCase</directory>
</testsuite>
```
- **⚠️ DB テーブルを持つが `defaultInstallCorePlugins` に入れないプラグインは、`BcInstaller` の testsuite より「前」に置く**。`BcInstaller` の `InstallationsControllerTest::testStep3`（`mode=createDb`）は**実際のインストール処理**で全テーブルを drop し **defaultInstall のプラグインのみ**再作成する。後ろに置くと、自プラグインのテーブルが作り直されないまま自テストが走り全アクション 500 になる（既存の非 default コア=BcContentLink/BcCustomContent 等もすべて BcInstaller より前に並んでいる）。
  - 例: `... BcSeo` の直後・`BcInstaller` の直前に `BcMcp` を置く。
  - defaultInstall に入れる方針なら BcInstaller の後でも可（createDb が作り直すため）。

### 4-2. マイグレーション・プラグインリストへ追加（⚠️ 複数箇所ある）
プラグインが DB マイグレーション（`config/Migrations`）を持つ場合、**テスト用 DB にテーブルを作る／再構築するプラグインリストすべて**に追加する。1 箇所でも漏れると、全体実行時にそのプラグインのテーブルだけ欠落し `Could not describe columns on <table>`（→ コントローラ初期化失敗で当該プラグインの全アクション 500）になる。**単独 testsuite では通り、全体実行でのみ落ちる**のが特徴（切り分けは `basercms-plugin-migration` のテスト基盤節も参照）。

1. **ルート `tests/bootstrap.php` の `Migrator::runMany`**（初期スキーマ構築）
   ```php
   (new Migrator())->runMany([
       ['plugin' => 'BaserCore'],
       ...
       ['plugin' => 'BcMcp'],   // 追加
   ]);
   ```
2. **「全テーブル drop → 再マイグレーション」をするテストの $plugins 配列（複数あり）**。これらは `deleteTables()`/`deleteAllTables()` で**全テーブルを drop** した後、ハードコードされたリストで再マイグレーションする。ここに無いとテーブルが復活せず、テスト順で先頭側に位置するため**以降の全テストで欠落したまま**になる（実際に bc-mcp 追加時に `oauth2_clients` がこれで消え、OAuth2 全テストが 500 になった）。**判明している箇所（両方必須）**:
   - `plugins/baser-core/tests/TestCase/Service/BcDatabaseServiceTest.php` → `test_deleteTablesForMigrations()` の `$plugins`
   - `plugins/bc-installer/tests/TestCase/Service/Admin/InstallationsAdminServiceTest.php` → `test_deleteAllTables()` の `$plugins`
   ```php
   $plugins = [ 'BaserCore', ..., 'BcMail', 'BcMcp', /* 追加 */ 'BcSearchIndex', ... ];
   ```
- **抜け漏れ確認（重要）**: この種の再構築リストは複数ファイルに散在する。**必ず grep で全部洗う**:
   ```bash
   grep -rn "'BcWidgetArea'," plugins --include="*.php"   # 末尾要素で「コア列挙リスト」を検出
   ```
   ヒットした各リストに新プラグインが入っているか確認する（1 つでも漏れると全体実行のみで落ちる）。
- **確実な検出法**: `BcTestCase::setUp` 冒頭に「対象テーブルの存在を各テストで記録」する一時計測を仕込み、**全 suite を実行**して `OK→MISSING` 転移点を洗い出す（転移直前の OK テストが drop 元）。全 dropper を一度に特定できる。確認後に計測は除去する。
- 切り分け診断（どのテストでテーブルが消えるか）: `BcTestCase::setUp` 冒頭に一時計測（`ConnectionManager::get('test')->getSchemaCollection()->listTables()` に対象テーブルが在るか各テストで記録）を仕込み、最初に MISSING になる直前の OK テストが drop 元。確認後に必ず除去する。

### 4-3. プラグイン `composer.json` をコアプラグイン形式へ簡素化
`require-dev`（baser-core 等）・`scripts`・`config.allow-plugins`・`minimum-stability` は削除。`vendor-dir` を足す。**外部ランタイム依存がある場合は `require` に残す**（後で monorepo-builder merge がルートへ集約する）。
```jsonc
{
    "name": "baserproject/bc-mcp",
    "description": "BcMcp plugin for baserCMS",
    "homepage": "https://basercms.net",
    "type": "cakephp-plugin",
    "license": "MIT",
    "vendor-dir": "../../vendor",
    "require": {                       // 外部依存が無ければ require ごと省略可（bc-blog 等）
        "php": "^8.1",
        "league/oauth2-server": "^8.5"
        // ...プラグイン固有の外部依存
    },
    "autoload":     { "psr-4": { "BcMcp\\": "src/" } },
    "autoload-dev": { "psr-4": { "BcMcp\\Test\\": "tests/" } }
}
```
- テストが他コアの Test 名前空間（`BcBlog\Test` 等）や `App\`（TestApp）を使う場合、**ルート `composer.json` の autoload-dev** が供給するため、プラグイン側 autoload-dev は自分の `Test` のみでよい。

---

## 5. システム（baserCMS / monorepo）への認識

### 5-1. `plugins/baser-core/config/setting.php` の `corePlugins` に追加
```php
'corePlugins' => [ ..., 'BcMail', 'BcMcp', 'BcSeo', ... ],
```

### 5-2. `defaultInstallCorePlugins` への追加可否を確認
baserCMS **新規インストール時に自動有効化**するなら追加（BcBlog/BcMail 等と同様）。任意機能・オプトインなら**追加しない**（corePlugins のみ＝管理画面から手動有効化）。
→ プロジェクト方針をユーザーに確認する。

### 5-3. ルート `composer.json` への集約（autoload / autoload-dev / replace / require）
**原則 `vendor/bin/monorepo-builder merge` で集約**する（`monorepo-builder.php` の `packageDirectories([... '/plugins'])` が `plugins/` 配下を自動走査し、各 `composer.json` をルートへマージ＋`replace` を管理）。
```bash
docker compose exec <container> sh -c "cd /var/www/html && vendor/bin/monorepo-builder merge"
```
- ⚠️ merge は**全パッケージのバージョン整合をガード**する。monorepo 外のローカル専用プラグイン（例 EgaRyu / BcUpdateSupporter 等）に版差があると `Found conflicting package versions` で**ブロック**される。その場合は当該プラグインの版を揃えるか、ブロック時は **merge と同等の編集を手動**で行う（下記）。
- merge 相当の手動編集（ルート `composer.json`）:
  - `autoload.psr-4` に `"BcMcp\\": "plugins/bc-mcp/src/"`
  - `autoload-dev.psr-4` に `"BcMcp\\Test\\": "plugins/bc-mcp/tests/"`
  - `require` にプラグインの外部依存（`league/oauth2-server` 等、`ext-*` 含む）
  - `replace` に `"baserproject/bc-mcp": "<monorepo版>"`（他コアに合わせる）
- 反映後、依存取得とオートロード再生成:
  ```bash
  docker compose exec <container> sh -c "cd /var/www/html && composer update <added-packages> --no-interaction"
  ```

### 5-4. `phpdoc.dist.xml` に `src` パスを追加
```xml
<path>plugins/bc-mail/src</path>
<path>plugins/bc-mcp/src</path>   <!-- 追加 -->
<path>plugins/bc-search-index/src</path>
```

### 5-5. `.github/workflows/split_monorepo.yml` に split 対象を追加
```yaml
- local_path: 'bc-mcp'
  split_repository: 'bc-mcp'
```

---

## 6. 全体テストでの実行確認

```bash
docker compose exec <container> sh -c "cd /var/www/html && vendor/bin/phpunit --testsuite BcMcp"
```
- standalone 専用 bootstrap が用意していた前提（外部プロセス・環境変数・鍵等）が**全体 bootstrap には無い**ため、移行直後は失敗が出やすい。代表例:
  - **外部プロセス依存**: プロキシ統合テスト等が**実サーバープロセス**を要する場合、standalone では bootstrap が起動していた。全体側では**該当テストの先頭で起動**する（起動済みなら再利用）。起動コマンドは `ROOT/bin/cake <command>`（アプリの cake と、コア登録済みのコマンド）を使う。`setUp` 全体ではなく**サーバーが要る個別テストにだけ**ガードを入れる（他テストに起動待ちを波及させない）。
    **⚠️ 起動判定は「プロセス存在（pidファイル）」だけでは不十分**。プロセスは起きてもポートの bind が間に合わず、プロキシ接続先（例 `127.0.0.1:{port}`）が接続拒否＝500 になる（ローカルは通り CI でのみ落ちる典型）。**実際に接続できるまでポーリングで待つ**こと。
    **到達できない場合は `markTestSkipped` で隠さず `assertTrue` 等で明示的に失敗させる**（スキップはサーバー起動不具合を握りつぶす）。本方針は `.github/instructions/basercms.instructions.md`「開発・テスト・ビルド」にも記載。
    ```php
    private function requireMcpServer(): void {
        $m = new McpServerManger();
        $cfg = $m->getServerConfig();
        if (!$m->isServerRunning()) { $m->startMcpServer($cfg); }
        $host = $cfg['host'] ?? '127.0.0.1'; $port = (int)($cfg['port'] ?? 3000);
        $deadline = microtime(true) + 15.0; $reachable = false;
        while (microtime(true) < $deadline) {
            $c = @fsockopen($host, $port, $e, $s, 1);
            if ($c) { fclose($c); $reachable = true; break; }
            usleep(300000);
        }
        $this->assertTrue($reachable, "MCP サーバー（{$host}:{$port}）へ接続できませんでした");
    }
    // 実サーバーが要るテストの先頭で $this->requireMcpServer();
    ```
  - **マイグレーション未実行**: 4-2 の追加漏れ → テーブル不在で失敗。
  - **プラグイン未 bootstrap / サブプラグイン未ロード** 等は `basercms-plugin-migration` スキル（T-3〜T-5）参照。
- 仕上げに全体テストの回帰が無いことも確認する。

---

## 7. monorepo split 用 read-only リポジトリの確認

split_monorepo は各プラグインを `baserproject/<split_repository>` の**read-only リポジトリ**へ push する。**対象リポジトリが存在しないと split が失敗**するため、事前に作成済みか確認する。
```bash
gh repo view baserproject/bc-mcp --json name,url,visibility
```
- 未作成の場合は作成する（外部・不可逆な操作。実行前にユーザーへ確認する）。
- **`gh` での作成可否を先に確認**する: 認証アカウントが対象 org（`baserproject`）の admin で、トークンに `repo` スコープがあれば作成できる。
  ```bash
  gh auth status                                   # ログイン／スコープ確認（repo が必要）
  gh api user/memberships/orgs/baserproject        # role が "admin" か確認
  ```
- 作成は**既存 split リポジトリ（例 `baserproject/bc-blog`）の設定に合わせる**: public ／ 説明 `[READ-ONLY] <PascalName>` ／ homepage `https://basercms.net`。
  ```bash
  gh repo create baserproject/bc-mcp --public -d "[READ-ONLY] BcMcp" -h https://basercms.net
  ```
- 権限が無い場合は baserCMS 管理者に作成を依頼する（無断で別 owner に作らない）。

---

## チェックリスト

1. [ ] プラグイン名がコア規約（`bc-` プレフィックス／ケバブ／`Bc` namespace／`baserproject/bc-*`）
2. [ ] `.git` / `vendor` / `bin` / `composer.lock` / `phpunit.xml.dist` / `tests/bootstrap.php` / `TestApp` / `VERSION.txt` / `CHANGELOG.md` 削除
3. [ ] ルート `.gitignore`: `!/plugins/<bc-name>` で追跡対象化 ＋ `/webroot/<underscored>` を無視
4. [ ] ルート `phpunit.xml.dist` に testsuite 追加
5. [ ] マイグレーション・リスト全箇所に追加（マイグレーションがある場合）: ルート `tests/bootstrap.php` の `runMany` ＋ `BcDatabaseServiceTest::test_deleteTablesForMigrations` の `$plugins`
6. [ ] プラグイン `composer.json` をコア形式に簡素化（外部 require は残す）
7. [ ] `corePlugins` に追加 ／ `defaultInstallCorePlugins` は方針確認
8. [ ] ルート `composer.json` に集約（merge もしくは手動）＋ `composer update`
9. [ ] `phpdoc.dist.xml` / `split_monorepo.yml` に追加
10. [ ] 全体テストで testsuite OK（外部プロセス依存は setUp で起動）
11. [ ] split 用 read-only リポジトリの存在確認

> メモ: `.claude/skills/` に追加・更新したスキルは、現行セッションには即時反映されない（起動時に discover）。**Claude Code を再起動**すると `/basercms-core-plugin-convert` として呼べる。
