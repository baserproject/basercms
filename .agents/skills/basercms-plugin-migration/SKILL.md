---
name: basercms-plugin-migration
description: 'baserCMS プラグインを 5.2系 から 5.3系（PHP 8.5 / CakePHP 5.2.x ベース、開発中）へ移行する際の baserCMS 固有の破壊的変更・非推奨・テスト基盤対応のレシピ集。「プラグインを5.3に対応」「baserCMS 5.3 マイグレーション」「PluginCollection::create(): $config null given」「Plugin named X is already loaded（統合テスト）」「BcApp.testAppPluginsToLoad」「plugins.php の null → []」「Class BcCcFile\... not found（サブプラグイン未ロード）」「Cannot set a node''s parent as itself（TreeBehavior + Content フィクスチャ）」「validateUnique(): Argument #2 not passed（vendor の CakePHP 二重ロード）」「MissingTemplateException error500.php でテストの真因がマスクされる」「BcFile コンストラクタ／delete() パス指定の非推奨」等、プラグイン本体・テスト基盤の移行時に参照する。PHP本体起因は php-migration、CakePHP本体起因は cakephp-migration、テスト実行手順は basercms-unittest スキルを参照。新しい baserCMS バージョン対応時は本書にバージョン別追記する。'
license: MIT
---

# baserCMS プラグイン バージョン移行ガイド（5.2 → 5.3）

baserCMS プラグインを 5.2系 から 5.3系へ移行する際に遭遇した baserCMS 固有の問題と修正レシピ集。**バージョン別**に整理して育てる。

> 収録: 5.2 → 5.3。5.3系は **PHP 8.5 / CakePHP 5.2.x** ベース（執筆時点で開発中）。
> 役割分担:
> - **PHP 本体**起因（暗黙nullable・動的プロパティ・非正規キャスト・setAccessible・null オフセット等）→ `php-migration` スキル
> - **CakePHP 本体／関連パッケージ**起因（AbstractMigration→BaseMigration・イベント戻り値・PluginCollection・TreeBehavior・ResultSet・order()/group() 等）→ `cakephp-migration` スキル
> - **ユニットテストの実行・集計・切り分け**手順 → `basercms-unittest` スキル
> - 本書は上記に当てはまらない **baserCMS 固有 + プラグインのテスト基盤** に絞る。

---

## 大原則

- **「致命的エラー」と「非推奨警告」を区別する**。`logs/debug.log` の `debug:` や `logs/error.log` の `error:` で `…は非推奨です` と出るものは警告（動作継続）。Fatal / Exception が本当のエラー。
- **テスト大量失敗は根本原因単位で分類する**。数百件でも原因は十数種。例外メッセージを集計して systemic な原因（vendor 混在・プラグイン未ロード・フィクスチャ不正）から潰す。
- 依存の更新は `composer require` を使い、`composer update` で依存全体を動かさない（`cakephp-migration` 大原則と同じ）。

---

## 5.2 → 5.3 の変更

### 0. 依存バージョンの更新（composer.json）
プラグインの `require-dev`（または `require`）の baser-core を 5.3 系に上げる。
```jsonc
"require-dev": {
    "baserproject/baser-core": "5.3.x"
}
```
- 上げた後は `composer update baserproject/baser-core --with-dependencies`（プラグインの vendor を持つ場合）で 5.3 系 + CakePHP 5.2.x を取り込む。
- PHP 要件は 8.1 互換を維持するなら `"php": ">=8.1"` のまま（8.5 の非推奨対応は `php-migration` の ❌ 印=据え置き方針に従う）。

---

## テスト基盤（standalone プラグイン test harness）

> 自前 `vendor/` と `tests/TestApp/` を持つ「単体実行型」のプラグインを 5.3 で動かすと、ここが最も詰まる。コアプラグイン（親ディレクトリのアプリ経由でテスト）の場合はアプリ側 `tests/bootstrap.php` が同等の処理をしているので、本節はアプリ側を参照する。

### T-1. CakePHP の二重ロード（vendor 混在）で `validateUnique(): Argument #2 ($options) not passed`
`composer test` が **ルートの phpunit**（`../../vendor/bin/phpunit`）を起動する一方、プラグインの `tests/bootstrap.php` が `ROOT/vendor/autoload.php`（=プラグイン自前 vendor）を読み込むと、**2つの異なる CakePHP バージョンが同時にロード**される。Composer のオートローダは `register(prepend=true)` で後勝ちになるため、起動時に読まれたクラス（例 `Cake\ORM\Table` 5.0系）と実行時に初めて読まれるクラス（例 `Cake\Validation\Validator` 5.2系）が混在し、`validateUnique()` 等でシグネチャ不一致の `ArgumentCountError` が出る。
- **判別**: スタックトレースに `…/vendor/cakephp/…`（ルート）と `…/plugins/<plugin>/vendor/cakephp/…`（自前）の **両方**が現れる。
- **修正（standalone）**: テストは**プラグイン自前の `vendor/bin/phpunit`** で実行する。bootstrap が読む `ROOT/vendor` と phpunit bin の autoload が同一 vendor になり、CakePHP が単一バージョンに揃う。
  ```jsonc
  // plugins/<plugin>/composer.json
  "scripts": { "test": ["Composer\\Config::disableProcessTimeout", "vendor/bin/phpunit --colors=always"] }
  ```
  実行（Docker 経由・basercms-unittest 参照）: `docker compose exec <container> sh -c "cd /var/www/html/plugins/<plugin> && vendor/bin/phpunit"`

### T-2. `plugins.php` の `null` → `[]`（CakePHP 5.2 の PluginCollection::create が array 必須）
`tests/TestApp/config/plugins.php` の値が `null` だと、`BaseApplication::bootstrap()` → `PluginCollection::addFromConfig()` → `create($name, null)` で `TypeError: …create(): Argument #2 ($config) must be of type array, null given` になる（5.1 までは許容）。
```php
// Before → After
return ['BaserCore' => null, ...];  →  return ['BaserCore' => []];
```
- 関連: `cakephp-migration` 1-4 にも同記述あり。

### T-3. プラグインの読み込みは `BcApp.testAppPluginsToLoad` を使う（plugins.php は BaserCore のみ）
baser-core 5.2+ の `BcTestCase::loadTestAppPlugins()` は `Configure::read('BcApp.testAppPluginsToLoad')` を読み、**未 bootstrap が無くなるまでスナップショット単位で**（ガード付きで）各プラグインを bootstrap する。
- **plugins.php には `BaserCore` のみ**を記載し、コンテンツ系プラグイン（BcBlog / BcCustomContent / BcSearchIndex 等）と**対象プラグイン自身**は `testAppPluginsToLoad` に列挙する。本体アプリ（`config/plugins.php` は BaserCore + 基盤のみ、`tests/bootstrap.php` で `Plugin::loaded()` から testAppPluginsToLoad を生成）と同方式。
  ```php
  // plugins/<plugin>/tests/bootstrap.php（Migrator の後など）
  Configure::write('BcApp.testAppPluginsToLoad', [
      'BcMcp',            // 対象プラグイン自身
      'BcSearchIndex', 'BcBlog', 'BcCustomContent', // 依存コンテンツプラグイン
  ]);
  ```

### T-4. 統合テストで `Plugin named 'X' is already loaded`（二重ロード例外）
**T-3 と必ずセットで守る**。プラグインを **plugins.php と testAppPluginsToLoad の両方**に書くと、統合テストがリクエスト毎に生成する per-request app が `plugins.php` を再び `addFromConfig` し、既に読み込み済みのプラグインを再 add して `CakeException: Plugin named 'X' is already loaded`（CakePHP 5.2、`cakephp-migration` 2-7）になる。500 → さらに後述 T-6 でマスクされ「原因不明の 500」に見える。
- **修正**: `BaserCore` は plugins.php、その他は testAppPluginsToLoad に**分離**して重複させない。

### T-5. 同梱サブプラグインのクラスが `not found`（例 `Class "BcCcFile\Utility\BcCcFileUtil" not found`）
`BcCcFile` は **bc-custom-content に同梱されたサブプラグイン**（`vendor/baserproject/bc-custom-content/plugins/BcCcFile/`）で、`BcCustomContentPlugin::bootstrap()` → `loadPlugin()` が `App.paths.plugins` を追加し `BcUtil::includePluginClass()` で**動的に PSR-4 登録**する。composer の autoload には現れないため、親プラグインが bootstrap されないとクラス解決できない。
- **修正**: 親プラグイン（`BcCustomContent`）を **T-3 の `testAppPluginsToLoad` に入れて bootstrap させる**。サブプラグインの namespace を composer.json に手書き登録する必要はない。

### T-6. テストの真因が `MissingTemplateException: Error/error500.php` でマスクされる
standalone の test harness には `templates/Error/error500.php` が無いため、統合テストで発生した 500 が「エラーページのレンダリング失敗」に化けて**本当の例外が見えない**（debug の有無に関わらず baserCMS はエラーテンプレートを描画しようとする）。
- **調査法（一時措置）**: `plugins/<plugin>/templates/Error/error500.php` を一時的に置き、テンプレートに渡る `$error`（Throwable）を捕捉してファイルへ書き出す → テスト実行 → 真因を確認 → **テンプレートは削除**する。
  ```php
  <?php // TEMP: 調査用。確認後に削除
  $e = $error ?? null;
  if ($e) { file_put_contents('/tmp/real_error.txt', get_class($e).': '.$e->getMessage()."\n".$e->getTraceAsString()); }
  echo 'ERR500';
  ```
- エラーレンダラーが探すのは **プラグインの `templates/Error/`**（`tests/TestApp/templates/` ではない）点に注意。

---

## baserCMS 固有の非推奨

### B-1. `BcFile` のコンストラクタ／`delete()` のパス指定
`logs/error.log` に下記が出る（**6.0.0 で必須化**＝今は警告）。
```
BcFile では、コンストラクタでパスの指定をしないのは非推奨です。パスの指定行うようにしてください。この要件はバージョン 6.0.0 で必須となります。
BcFile::delete() では、第一引数にパスを指定するのは非推奨です。パスの指定はコンストラクタで行ってください。この要件はバージョン 6.0.0 で必須となります。
```
- **修正**: パスはコンストラクタで渡し、`delete()` は引数なしにする。
  ```php
  // Before → After
  $file = new BcFile();           $file = new BcFile($path);
  $file->delete($path);           $file->delete();
  ```
- 自前プラグインのコードで `BcFile` を使っている箇所のみ対象（vendor 内の発生は baser-core 側の対応待ちで、プラグイン移行としては無視してよい）。

---

## TreeBehavior + Content フィクスチャ

### C-1. `Cannot set a node's parent as itself.`（Content 保存テスト）
CakePHP 5.2 の TreeBehavior は「親に自分自身」を保存時に例外化する（`cakephp-migration` 2-11 と同系の厳格化）。baserCMS のテストシナリオで顕在化しやすい典型:
- `BlogContentScenario` 等は `$parentId = $args[2] ?? 1` のように **`?? 1`** でデフォルトを与えるため、テストが `null` を渡しても **parent_id=1** になる。`id=1` のコンテンツだと **parent_id == id（自己親）** の不正データになる。
- **読み取りテストでは無害**だが、Content を保存するテスト（`editXxx` で name/title/site_id 等を更新し Content 関連が save される）でのみ例外化する。
- **修正（テスト側）**: シナリオ読込後に Content を正当なルートへ補正してツリーを再構築する。
  ```php
  $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
  $contentsTable->updateAll(['parent_id' => null], ['id' => 1]); // behavior を介さず is-a-root に
  $contentsTable->recover();                                     // lft/rght 再構築
  ```
- プラグイン本体コードが `parent_id` をルートに対して送っていないか（`cakephp-migration` 2-11）も併せて確認する。送っていなければアプリ修正は不要で、上記フィクスチャ補正で足りる。

---

## 日付・時刻依存テスト

### D-1. アップロードパス等の年月ハードコード
ファイルアップロード先は実行時の年月（`date('Y/m')`）でディレクトリが切られるため、`'2025/09/...'` のように固定値でアサートしていると**月替わりで必ず失敗**する。
```php
// Before → After
$this->assertEquals('2025/09/00000001_image_field.png', $result['image_field'] ?? '');
$this->assertEquals(date('Y/m') . '/00000001_image_field.png', $result['image_field'] ?? '');
```
- 時刻比較全般の注意は `cakephp-migration` 3-4（実 `time()` を使わない）も参照。

---

## 調査の進め方（チェックリスト）

1. テスト実行・全失敗の集計は `basercms-unittest` スキル。まず **vendor 混在（T-1）** を疑い、トレースに 2 つの cakephp パスが無いか確認。
2. `PluginCollection::create(): … null given` → T-2、`Plugin named 'X' is already loaded` → T-3/T-4。
3. `Class "…\…" not found`（サブプラグイン）→ T-5。
4. 統合テストの原因不明 500 → T-6 で真因を可視化してから分類。
5. フレームワーク／PHP 起因は `cakephp-migration` / `php-migration` の該当節へ。baserCMS 固有非推奨は B-1、TreeBehavior は C-1、日付依存は D-1。
6. 修正のたびに `php -l` → `--filter` 単体確認 → 仕上げに全テスト再実行で件数改善と新規回帰を確認。

> メモ: 新しくスキルファイルを `.claude/skills/` に追加・更新した直後は、現在の Claude Code セッションには反映されない（セッション起動時に discover される）。**Claude Code を再起動**すると `/basercms-plugin-migration` として呼べるようになる。
