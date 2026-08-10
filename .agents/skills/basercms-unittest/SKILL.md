---
name: basercms-unittest
description: baserCMS（CakePHP5 / PHPUnit）のユニットテストをローカル Docker 環境で実行・調査する手順。「ユニットテストを実行して」「全テストを走らせて」「このテストだけ流して」「テスト失敗を調べて」「プラグイン単体でテストを動かしたい」「プラグインにテスト環境を導入したい」等のときに参照する。コンテナ名・実行コマンド・権限自動承認のためのコマンド整形・失敗の集計と切り分け方に加え、プラグイン単体（スタンドアロン）でユニットテストを実行する仕組みの導入手順（composer.json / phpunit.xml.dist / tests/bootstrap.php / TestApp 一式、phinx 0.16.10 ピンの罠）を収録。
license: MIT
---

# baserCMS ユニットテスト実行ガイド（ローカル）

baserCMS のユニットテストは Docker コンテナ上で実行する。実行・絞り込み・失敗調査の定石をまとめる。

## 実行環境

- 実行は `docker compose`（`<プロジェクトのdocker-compose.yml配置パス>`）。
- **PHPコンテナ名: `basercms`**、baserCMS 配置先: **`/var/www/html`**。
- テスト設定: `phpunit.xml.dist`。`<testsuites>` にプラグインごとの testsuite が定義（BaserCore / BcBlog / BcCustomContent / BcMail / BcThemeFile …）。
- DB はローカル環境依存（過去に `bc-db` ホスト無しの失敗があったが、現在は `<db-host>` コンテナを利用。この種の接続失敗は環境要因でスルー可）。
- **実行前に必ずコンテナの稼働を確認する**。PHP コンテナ・DB コンテナが停止（`Exited`）していると `docker exec` が失敗する。`docker ps` で確認し、落ちていれば `docker start <container> <db-container>`、DB は mysqld が接続を受け付けるまで待つ（詳細は後述「事前確認: コンテナの稼働」）。

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
5. **「ある値が保存後に NULL/不正になる」系（afterSave 連鎖が絡む）の特定法**: エンティティのプロパティをログしても**全ポイントで正しい値**なのに DB が NULL、ということが起きる（dirty でない／別エンティティの save が原因）。**DB を生SQLで段階的に読んで二分**する: 怪しい処理（save / afterSave / cascade メソッド）の**前後**に `$d = $this->getConnection()->execute("SELECT col FROM t WHERE id=?", [$id])->fetch("assoc"); \Cake\Log\Log::write("debug", "STEPn=".var_export($d["col"]??"NULL", true));` を仕込み、`tests/TestApp/logs/debug.log` を `grep` してどのステップ後に化けるかを挟み込みで特定する（`?? 'X'` は null を 'X' に潰すので解釈に注意）。原因の save を見つけたら、その save が書く**値そのもの**をログする（例: 集計が `2026.0` 等の異常値→ `func()->min(datetime)` の数値化など）。デバッグ用ログ・一時テストは特定後に**必ず全削除**する。

## プラグイン単体（スタンドアロン）でのテスト実行環境の導入

フルスイート（上記 `basercms` コンテナ）とは別に、**1プラグインだけを独立してテストできる**仕組み。4→5 移行中のプラグインを TDD で進めるのに有効（移植したテストが Table/Service 5系化の検証になる）。

- **実行コンテナはアプリ側**（例: `<container>`、配置先 `/var/www/html/v5`）。DB ホスト（例 `<db-host>`）は Docker ネットワーク内でしか解決できないため、**phpunit はコンテナ内で実行**する（ホスト直叩きは接続不可）。
- プラグインは自前の `composer.json` / `vendor/` を持ち、baser-core を dev 依存として取り込む。

### tests/bootstrap.php と TestApp はどこから入手するか（重要）

`composer.json` / `phpunit.xml.dist` は本書の記述から手で起こせるが、**`tests/bootstrap.php` と `tests/TestApp/` 一式（app.php / app_local.php / bootstrap.php / paths.php / plugins.php / routes.php / src/Application.php / src/Controller/AppController.php）は中身が定型で量が多く、ゼロから書き起こすものではない**。これらは **baserCMS 公式の `baserproject/bc-bake` プラグインのプラグイン雛形生成コマンドに含まれている**。

- リポジトリ: https://github.com/baserproject/BcBake
- 手順（**人手の準備が必要**）: `composer require --dev baserproject/bc-bake` で導入 → **管理画面にログインして有効化**（または `plugins` テーブルに `name='BcBake', status=1` を直接投入。bc-bake は独自テーブル・install migration を持たない開発ツールなので直書きでも実用上足りる）→ 雛形生成コマンドを実行:
  ```
  bin/cake bake bc_plugin {PluginName}
  ```
  これは CakePHP 標準の `bake plugin` が作るファイルに加え、baserCMS 用のテスト基盤（`tests/bootstrap.php` ＋ `TestApp` 一式）等を生成する。
  （MVC一式は `bin/cake bake bc_all {table_name} -p {PluginName} --prefix Admin`。）
- bc-bake 経由は **composer導入＋有効化という人手の前提**が要る（エージェント単独では完結しにくいので、この準備はユーザーに依頼する）。有効化は管理画面ログイン、または `plugins` テーブルへ直接 `INSERT (name,title,version,status,db_init,priority,created,modified) VALUES ('BcBake','BcBake','1.2.1',1,1,100,NOW(),NOW())`。有効化すると `bin/cake bake --help` に `bc_plugin` / `bc_all` が現れる。

#### ★既存プラグインへ後付けする手順（検証済み）

`bake bc_plugin {Name}` は **「登録済み（ロード済み）プラグイン名」で存在チェック**するため、既存プラグイン名では `Plugin: {Name} already exists, no action taken` で弾かれる（**パス選択で vendor 側を選んでも同じ**＝チェックはディレクトリでなく解決済みプラグインパス基準）。そこで:

1. 既存プラグイン本体を一時リネームして退避（例 `plugins/Sample` → `plugins/Sample2`）。これで "Sample" 名が未登録扱いになる。**plugins テーブルで status=0 に落とす必要はない**（ディレクトリ退避だけで未登録扱いになり、active プラグインが一時的に dir 欠落でも bake の bootstrap は許容される＝検証済み）。
2. `bin/cake bake bc_plugin Sample`（パスは `1`＝`plugins/`、確認は `y`）。**正しい "Sample" 名**で雛形が生成される。対話は `(echo 1; yes y) | ...` で流す。`| head` 等で**パイプを途中で閉じると SIGPIPE で生成が中断**するので、出力はファイルへリダイレクトして完走させる。
   - ハングしたら（対話待ちで停止）`pkill -f 'cake bake'` で止め、生成途中の plugin ディレクトリを `rm -rf` してからリトライする。
3. 生成された `plugins/Sample/tests/bootstrap.php` と `plugins/Sample/tests/TestApp/` を、退避した本体 `plugins/Sample2/tests/` へ**移動**する（bootstrap.php と TestApp のみ。後述）。
4. `TestApp/config/install.php` は **汎用値（host=127.0.0.1 / database=basercms,test_basercms）で生成される**ので、プロジェクトのDB（例 host=<db-host> / database=<project>_v5・test_<project>_v5 / user=<dbuser>）へ**必ず編集**する。
5. 生成側 `plugins/Sample` を削除し、本体を戻す（`plugins/Sample2` → `plugins/Sample`）。
6. `vendor/bin/phpunit` で緑を確認。

**移すのは `tests/bootstrap.php` と `tests/TestApp/` だけ**にする。`bake bc_plugin` は `composer.json` / `phpunit.xml.dist` も生成するが、**`composer.json` は `name` がプレースホルダ・baser-core 版が違う・phinx ピンが無い**等で再生成されるため、本体側の自前 `composer.json`（phinx 0.16.10 ピン入り）を**上書きしないこと**。`phpunit.xml.dist` も既に自前があれば流用元の testsuite 名残骸（例 `CuMcp`）に注意。

### 必要なファイル一式（プラグイン直下）

1. `composer.json` … `require-dev` に以下。**`robmorgan/phinx` は必ず `0.16.10` にピン**（後述の罠）。
   ```json
   "require-dev": {
       "phpunit/phpunit": "10.5.31",
       "josegonzalez/dotenv": "^4.0",
       "baserproject/baser-core": "5.2.1",
       "robmorgan/phinx": "0.16.10",
       "vierge-noire/cakephp-fixture-factories": "^3.0",
       "vierge-noire/cakephp-test-suite-light": "^3.0"
   },
   "autoload-dev": {
       "psr-4": {
           "<Plugin>\\Test\\": "tests/",
           "App\\": "tests/TestApp/src/",
           "BaserCore\\Test\\": "vendor/baserproject/baser-core/tests/"
       }
   },
   "minimum-stability": "dev",
   "prefer-stable": true
   ```
2. `phpunit.xml.dist` … `bootstrap="tests/bootstrap.php"`、`<testsuite>` に `<directory>tests/TestCase/</directory>`、`<extensions>` に `Cake\TestSuite\Fixture\Extension\PHPUnitExtension`。
3. `tests/bootstrap.php` … TestApp の `config/paths.php` 読込 → `vendor/autoload.php` → コア bootstrap → `Configure::load('app')` → **`Configure::load('install')` で test 用 Datasources を設定** → `(new Migrations\TestSuite\Migrator())->runMany([['plugin'=>'BaserCore'],['plugin'=>'<Plugin>']])` でスキーマ構築 → `Configure::write('BcApp.testAppPluginsToLoad', ['<Plugin>'])`。
4. `tests/TestApp/`（最小アプリ骨格）… `config/install.php`（`Datasources.default` と **`Datasources.test`（test 用DB、例 `test_<project>_v5`）**）、`app.php`/`app_local.php`/`bootstrap.php`/`paths.php`/`plugins.php`/`routes.php`、`src/Application.php`、`src/Controller/AppController.php`。
5. `config/Migrations/`（プラグイン側のスキーマ。`*_Initial.php` ＋ `schema-dump-default.lock`）。BaserCore 分は vendor から自動で流れる。
6. **test 用 DB（例 `test_<project>_v5`）を事前作成**しておく。

### ★罠: phinx 0.16.11 で migrations が壊れる（最重要）

`cakephp/migrations 4.6.6` 環境で `robmorgan/phinx` が **0.16.11** に上がると、bootstrap のマイグレーション実行時に次の Fatal で**テスト本体に到達せず全滅**する:
```
Class Migrations\Db\Adapter\PhinxAdapter contains 2 abstract methods and must
therefore be declared abstract or implement the remaining methods
(Phinx\Db\Adapter\AdapterInterface::preExecuteActions, ...::postExecuteActions)
```
phinx 0.16.11 が `AdapterInterface` にメソッドを追加したのに migrations 4.6.6 の `PhinxAdapter` が未実装なため。**対処は phinx を 0.16.10 に固定**（動作実績のあるアプリ本体と同じ版に合わせる）:
```
docker exec -w /var/www/html/v5/plugins/<Plugin> <container> sh -c 'COMPOSER_MEMORY_LIMIT=-1 composer update robmorgan/phinx --with-all-dependencies --no-interaction'
```
composer.json の `require-dev` にも `"robmorgan/phinx": "0.16.10"` を明記して再発を防ぐ。

### 事前確認①: 対象コンテナの特定（思い込み厳禁）

**コンテナ名は決め打ちしない。** 環境には複数の baserCMS 系コンテナが同居することがある（例: `basercms`＝コア monorepo の開発環境 PHP 8.5、`<container>`＝本プロジェクト PHP 8.1）。名前が似ていて取り違えやすい。実行前に**対象アプリが入っているコンテナを実際に確認**する:
```
docker ps -a --format '{{.Names}}\t{{.Status}}'                      # 候補を列挙
docker exec <候補> sh -c 'ls -d /var/www/html/v5 >/dev/null 2>&1 && echo HIT; php -v | head -1'
```
目的のパス（例 `/var/www/html/v5`）が存在し、想定の PHP バージョンが出るコンテナが正解。DB コンテナも同様に、接続先ホスト名（`install.php` の `host`、例 `<db-host>`）と一致する名前のものを使う（`bc-db` 等の別 DB と混同しない）。

### 事前確認②: コンテナの稼働（最初に必ず）

特定したら、**アプリコンテナと DB コンテナが両方 Up か確認し、落ちていれば起動する**。停止状態（`Exited`）のまま `docker exec` すると `Connection refused`／コンテナ無し で無駄に詰まる。
```
docker ps -a --format '{{.Names}}\t{{.Status}}' | grep -E '<container>|<db-container>'
docker start <container> <db-container>
```
DB コンテナは起動直後は mysqld がまだ接続を受け付けないことがあるので、**疎通するまで待つ**:
```
for i in $(seq 1 30); do docker exec <db-container> sh -c 'mysqladmin ping -h127.0.0.1 -u<user> -p<pass> 2>/dev/null | grep -q alive' && break; sleep 2; done
```
（接続情報はプラグインの `tests/TestApp/config/install.php` の `Datasources.test`、本番は `v5/config/install.php` を参照。<container> では DB ホスト `<db-host>` / DB `test_<project>_v5` / user `<dbuser>`。）

### 実行コマンド

```
docker exec -w /var/www/html/v5/plugins/<Plugin> <container> sh -c 'vendor/bin/phpunit --testdox 2>&1 | tail -60'
```
bootstrap を通過してテスト一覧が出れば、migrations → DB 構築まで成功している。

### 旧4系テスト資産の整理（移行時）

- **集約クラスは削除**: `CakeTestSuite` を継承し `public static function suite()` で束ねるだけの `*AllTest` / `*AllModelTest` は CakePHP5/PHPUnit10 で機能せず（`CakeTestSuite` 不在）、`<directory>` 探索に拾われてロードエラーになる。価値ゼロなので削除。
- **Fixture を5系へ**: `extends CakeTestFixture` ＋ `public $import` / `public $records`（4系）→ `Cake\TestSuite\Fixture\TestFixture` 系へ。
- **テストを5系へ**: `extends BaserTestCase` / `ClassRegistry::init('Plugin.Model')` / `public $fixtures = ['plugin.sample.User']`（4系記法）→ BaserCore の TestCase 基底＋`fetchTable()` ＋ 5系 fixtures へ。
- 注意: メソッド名が揃っていても **Table 本体が4系のまま**（`$this->Assoc->find('first',...)`・`unbindModel`・`virtualFields`・`Behaviors->detach('BcCache')` 等の残骸／`// TODO baserCMS5移行:` マーカー）のことがある。その場合テストは赤になるのが正しく、SUT の5系化と対で進める。

## テストデータ: Factory とスキーマ（移行TDD用）

移行中の Table/集計/保存メソッドを TDD で5系化する際の、データ準備の定石。

### Factory（CakephpFixtureFactories）
baser-core 同梱の `vierge-noire/cakephp-fixture-factories` を使う。1テーブル1 Factory:
```php
namespace Sample\Test\Factory;
use CakephpFixtureFactories\Factory\BaseFactory as CakephpBaseFactory;
use Faker\Generator;
class SampleCommitFactory extends CakephpBaseFactory
{
    protected function getRootTableRegistryName(): string { return 'Sample.SampleCommits'; }
    protected function setDefaultTemplate(): void {
        $this->setDefaultData(fn(Generator $faker) => [
            'date' => '2021-11-30 00:00:00', 'project_id' => 1, 'user_id' => 1,
            'commit_type' => 1, 'commit' => 0, 'notes' => '',
            'created' => '2021-12-01 00:00:00', 'modified' => '2021-12-01 00:00:00',
        ]);
    }
}
```
- **NOT NULL 列はすべてデフォルトに入れる**（`SHOW COLUMNS ... WHERE Null='NO'` で確認）。
- 使い方: `SampleCommitFactory::make(['user_id'=>1,'commit'=>100])->persist()` は保存して **entity（id付き）を返す**。テストで id を後続データの外部キーに使える。
- **datetime 列は `'Y-m-d H:i:s'` 形式で渡す**。`'Y-m-d'` だけだと marshal で NULL になり、日付範囲条件にかからない。
- **save 系コールバックは persist で発火する**（例 SamplePractices の afterSave）。これを利用して afterSave の結果（calc_amount 等）を persist 後に assert できる。
- BTM/HABTM のテストデータは join テーブルへ直接リンク: 例 `users_user_groups` は `TableRegistry::getTableLocator()->get('UsersUserGroups')->setTable('users_user_groups')` で `newEntity(['user_id'=>, 'user_group_id'=>])->saveOrFail()`。グループ本体は `get('BaserCore.UserGroups')->save(newEntity(['name'=>'unit_1','title'=>..,'auth_prefix'=>'Admin']))`。

### テスト用スキーマ（migration が未完成な場合）
プラグインの `config/Migrations` が一部テーブルしか作らないことがある（移行途中）。テストDBに必要なテーブルが無いと集計テストが組めない。
- **本番の `SHOW CREATE TABLE` を忠実に再現する migration を足す**（`BaseMigration::up()` 内で `$this->execute(<<<'SQL' CREATE TABLE ... SQL)`）。文字化けする日本語 `COMMENT` 句は除去してよいが、**列名・型・charset は本番に合わせる**。`down()` で `DROP TABLE IF EXISTS`。
- **[戒め] テストを通すためだけに本番に無い列・テーブルを足さない**。それは本番の非互換を隠蔽する。正しい5系スキーマで解く（例: `users.user_group_id` は足さず `users_user_groups` BTM で解く → `basercms-plugin-4-to-5-upgrade` T-K）。

### 移行 TDD のループ
1. 4系の期待値（または最小データでの期待集計値）を assert するテストを書く → 実行して **RED**（4系メソッドが `Undefined property`/`$this->data` 等で落ちる＝想定どおりの赤）。
2. SUT を5系化（`basercms-plugin-4-to-5-upgrade` T-F/T-J 参照）。
3. 再実行して **GREEN**。全スイートで回帰が無いことも確認。

忠実移行しても期待値が合わない場合は、ごまかして通さず原因を記録して判断を仰ぐ（4系ロジックの読み違い or 4系自体のバグの可能性）。

## コントローラ統合テスト（管理画面・実証済み）

スタンドアロン環境でも baser-core 流の **HTTP 統合テスト**が動く。GET でテンプレートまで描画されるので、**手動ブラウザ確認の代替（自動の画面確認）**になり、移行で崩れたテンプレート/フォームを検知できる。

- 様式（baser-core `tests/TestCase/Controller/Admin/*ControllerTest.php` を手本）:
  ```php
  class SampleEstimatesControllerTest extends \BaserCore\TestSuite\BcTestCase {
      use \CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
      public function setUp(): void {
          parent::setUp();
          $this->loadFixtureScenario(\BaserCore\Test\Scenario\InitAppScenario::class); // 管理者/サイト/グループ
          $this->loginAdmin($this->getRequest('/baser/admin/sample/sample_estimates/index'));
      }
  }
  ```
  - GET: `$this->get('/baser/admin/sample/sample_estimates/edit/'.$id); $this->assertResponseSuccess();`
  - POST: `$this->enableCsrfToken(); $this->enableSecurityToken(); $this->post('/baser/admin/sample/sample_estimates/edit/'.$id, $data); $this->assertRedirect([...]);`
  - データは Factory で用意（`SampleEstimateFactory::make([...])->persist()`）。
- **GET 描画（200）に必要な前提**:
  - その画面が使うコンポーネント依存パッケージをスタンドアロン vendor にも入れる（例: Excel 出力に `phpoffice/phpspreadsheet`。無いと component が class_not_found で 500）。
  - 画面が参照する**全テーブル**がテストDBにあること（見積 edit は `sample_dep_companies` 等の別プラグインのテーブルも引く → 本番DDLで migration 補完）。不足は 500 の `Base table or view not found` で分かる。
  - 500 の真因は `tests/TestApp/logs/error.log` を `tail`/`grep` で見る（IntegrationTest はステータス500に丸めるため）。
- **GET 統合テストが捕まえる典型の移行漏れ**: テンプレートの4系 `FormHelper::create('Model')`（文字列モデル）→ `No context provider found ...`。5系は `$this->BcAdminForm->create(null, ['valueSources'=>['data','context']])`。`$this->Form->...` → `$this->BcAdminForm->...`、`$this->Form->value('x')` → `$this->getRequest()->getData('x')` 等（`basercms-plugin-4-to-5-upgrade` C-I/F系）。**302スモーク（未認証リダイレクト）だけでは描画エラーは分からない**ので、ログイン付き GET 統合テストで描画まで通すこと。
- **★[最重要] `assertResponseSuccess()`（200）は `Undefined variable` 等の PHP warning を握り潰す**: テンプレが未 set のビュー変数（例: `$saleTypes`/`$billingStatuses` ＝ AppController::beforeRender の `set(Configure::read(...))` 欠落）を参照していても、warning は出るが描画は 200 で通り**テストは緑のまま**。＝描画テストが緑でも画面が「空のセレクト/壊れた列」で出荷されうる。**対策: 描画テストの後に `tests/TestApp/logs/error.log` を `grep "Undefined variable"`（または Warning/Deprecated）して pristine を確認する**。これを CI 的に締めると AppController 継承漏れ（`basercms-plugin-4-to-5-upgrade` C-0 表の set 行）等を確実に捕まえられる。
- **ajax アクションの POST フロー統合テスト**: 請求確定（`ajax_fixed_multiple`）のような ajax は、(1) `enableCsrfToken()/enableSecurityToken()` ＋必要に応じ ajax ヘッダ、(2) POSTキーは**4系正本のコントローラを読んで確定**（推測しない）、(3) **確定が他テーブルへ連動すること**（例: 請求確定 → 請求 `billing_status_cd=3` だけでなく `SampleSales` が1件立つ）まで assert すると、コントローラ→Table→afterSave の結合が画面経由で実証できる。応答が4系の戻り値直返し→5系 JSON 化で形式が変わる場合は record（JSクライアント側挙動はブラウザ確認で）。
- **★バンドルJSが掴む DOM id の存在を assert して「JS連携の静かな死」を捕まえる**: 4系バンドルJS は CamelCase id（`#SampleEstimateSubmissionTarget`）や `data[Model][field]` name でDOMを掴むが、5系 `control()` は kebab id（`sampleestimate-submission-target`）＋`Model[field]` name を生成するため、セレクタが全滅してもPHPエラーも warning も出ず**描画200のまま JS だけ無言で死ぬ**（自動入力・計算・ラジオ切替が動かない。`basercms-plugin-4-to-5-upgrade` C-F2）。これは PHP テストの素の描画チェックでは捕まらないが、**「JS が参照する id がレンダリングHTMLに存在するか」を assert すれば回帰として捕まえられる**:
  ```php
  // JS が掴む id を grep で洗い出して列挙 → 描画HTMLに含まれることを assert
  $this->get('/baser/admin/sample/sample_estimates/add');
  $this->assertResponseSuccess();
  foreach (['SampleEstimateSubmissionTarget','SampleEstimateName','SampleEstimateContractMonths' /* form.js が #id で掴む全id */] as $id) {
      $this->assertResponseContains('id="' . $id . '"', "JS連携id {$id} がDOMに無い＝セレクタ不一致");
  }
  ```
  id を明示付与で寄せた場合も、JS を5系 id に直した場合も、この assert が「次の移行・テンプレ再生成で再び id がズレた」ことを検知する番人になる。

## 循環 afterSave の統合シナリオテスト（相互結合ドメイン）

複数 Table の afterSave が相互発火する結合（例: 見積↔請求↔プロジェクト↔入金↔売上）は、Table 単体テストでは「サイクル全体が破綻なく収束するか」を保証できない。**業務の一連の流れを1本で貫通させる統合シナリオテスト**を1ドメインにつき1〜2本足す。

- 様式: 単体テストと同じ `BcTestCase` + Factory。複数 Table を `getTableLocator()->get()` で取得し、業務シナリオの順にメソッドを呼ぶ。例（請求ライフサイクル）:
  1. Factory でプロジェクト＋見積（受注前）を用意
  2. 請求生成（`createNewMultiple`）→ 請求件数・見積 `billing_amount`・入金自動生成を assert
  3. 請求確定（`fixedMultiple`）→ 請求 `billing_status_cd=3`・入金額更新・売上計上を assert
  4. 見積への集計反映（`sale_amount`/`billing_status_cd`）を assert
- **再入ガードの実証**: 「ハングせず最後まで到達できること自体」が無限ループ無しの証明になる（`basercms-plugin-4-to-5-upgrade` の `inAfterSave` ガード）。併せて**件数が爆発していない**ことも assert（例: 単一払いで請求1件）。単一払い（`contract_months=1`/`payment_span=1`）と複数分割（年契約・月払=12分割）の両ケースを用意すると分割ロジックも同時に守れる。
- **[最重要] 統合テストでも「4系に忠実」を貫く（4系がバグでも再現する）**: 統合の期待値は「業務的に正しそうな値」ではなく「4系が実際に出す値」。例: 4系 `SampleSale::afterSave` は見積 `sale_amount` を**その請求1件分の計上額**で上書きするため、複数請求では**最後に処理した請求の計上額だけが残る**（last-write-wins、合計にはならない）。5系もこの挙動を再現するのが正なので、テストは合計ではなく `100000`（最後の1件分）を assert し、**なぜそうなるかを4系の行番号付きコメントで残す**。期待が業務的に変でも勝手に「直さない」（直すのは別タスク）。→ 「移行 TDD のループ」の原則の具体例。

## 並列でテストを回すときの注意（テストDBの競合）

複数の作業を並列に進めるとき、**`vendor/bin/phpunit`（フルスイート）を同時に複数走らせない**こと。共有のテストDB `test_<project>_v5` を使うため、fixture の truncate / insert が競合して不安定になる（落ちたり相互汚染したり）。並列で書き換えを進める場合は、各並列ジョブは `php -l`（構文チェック）までに留め、**フルスイートは最後に1回だけ**まとめて実行する。
（移行全体をどの順序で進めるか＝横断コードチェック→構文5系化→テスト＆ブラウザ、という方法論は `basercms-plugin-4-to-5-upgrade`「移行の進め方」を参照。本スキルはテストの実行・調査のみを扱う。）

## メモ

- 全体テストでメソッド名を表示したい場合は環境変数 `SHOW_TEST_METHOD=true`（`BcTestCase::setUp` が対応）。
- ローカル固有の事情は `.github/instructions/local.instructions.md`（`.gitignore` 対象で存在しない場合あり）も参照。

関連: 移行起因の不具合の修正レシピは `cakephp-migration` / `php-migration` スキルへ。
