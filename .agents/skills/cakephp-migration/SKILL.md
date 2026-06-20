---
name: cakephp-migration
description: baserCMS の CakePHP バージョンアップ（5.0 → 5.1 → 5.2 ～）対応の非推奨・破壊的変更パターン集と修正レシピ。「Association alias already set」「A validation rule with the name already exists」「_Token not found」「headers already sent」「Plugin named X is already loaded」「ResultSet/PaginatedResultSet のエラー」「order()/group() 非推奨」「イベントリスナーの戻り値非推奨」「find('all', $options) 非推奨」「Table::get の配列 options 非推奨／名前付き引数」「配列条件の null 値で例外」「_cake_core_ キャッシュ設定」「pluginBootstrap で後続プラグインの setting.php が読まれず Configure が null（live イテレーション）」「_checkFilePath で .. テンプレートが MissingPluginException/500」「TreeBehavior でルートに parent_id=null を入れて lft/rght 破損」「Entity::set 配列一括の非推奨」「AbstractMigration/AbstractSeed 非推奨 → BaseMigration/BaseSeed（cakephp/migrations 4.5+）」「config/routes.php の fallbacks() でテストのみ 404」等、CakePHP本体・関連パッケージ起因の問題の調査・修正時に参照する。新しい CakePHP バージョン対応時は本書にバージョン別追記する。PHP本体起因の問題は php-migration スキルを参照。
license: MIT
---

# CakePHP バージョン移行ガイド（baserCMS）

CakePHP のアップグレードで遭遇した破壊的変更・非推奨と修正レシピ集。**バージョン別**に整理して育てる。PHP 本体起因（暗黙nullable・動的プロパティ等）は別スキル `php-migration` を参照。

> 収録: 5.0 → 5.1、5.1 → 5.2。バージョンの帰属は移行作業時の実観測ベース（厳密な導入版と差異がありうる）。新バージョン対応時は新しい「## X.Y → X.Z の変更」節を追記する。

---

## 大原則（全バージョン共通）

- **アップグレードは `composer require` を使う。`composer update` は使わない**（依存全体が動くのを避ける）。
  例: `composer require cakephp/cakephp:"5.2.*" --update-with-dependencies`
- **「致命的エラー」と「非推奨警告」を区別する**。デバッグモードでは非推奨警告が画面下部に表示され「エラー」に見えるが、`logs/debug.log` の `debug:` は警告（動作継続）。`logs/error.log` や Fatal/Exception が本当のエラー。
- **テスト大量失敗時は根本原因単位で分類する**。数百件の失敗でも原因は十数種。フルログから例外を集計して systemic な原因を先に潰す（集計コマンド・切り分け手順は `basercms-unittest` スキル参照）。
- **regression と既存/環境failを切り分ける**。`git diff HEAD -- <file>` で自分の変更対象か確認。未変更ファイルかつ fixture/プラグインロード依存（`Plugin::isLoaded('X')` が false／DIコンテナ未登録／英語↔日本語メッセージ不一致）なら移行起因ではなく環境要因。**vendor が既に新版に入れ替わっているため単純な `git stash` ではクリーンな baseline は作れない**。

> ユニットテストの実行方法・失敗の集計と切り分け手順は `basercms-unittest` スキルを参照。

---

## 5.0 → 5.1 の変更

5.1 では E_USER_DEPRECATED 系の非推奨が増える。`Error.errorLevel` で抑制可能だが、本対応として潰す。

### 1-1. `Query::order()` / `group()` の非推奨
`order()`→`orderBy()`、`group()`→`groupBy()`（`SelectQuery::group()` も）。
```php
// Before
$query->order(['Contents.id'])->group('site_id');
// After
$query->orderBy(['Contents.id'])->groupBy('site_id');
```

### 1-2. `find('all', $options)`（options 配列）の非推奨（Since 5.0.0）
options 配列渡しが非推奨。名前付き引数 or スプレッドにする。
```php
// Before
$this->Table->find('all', ['limit' => $n, 'conditions' => $c]);
// After（いずれか）
$query = $this->Table->find()->where($c); if ($n) $query->limit($n);
$this->Table->find('all', ...$options);   // $options のキーが finder の名前付き引数に一致する場合
$this->Table->find('all', conditions: $c, order: $o);
```
- 既に `...$options` / `conditions:` 等になっているものは対象外（誤検出注意）。
- 警告: `Calling 'findAll' finder with options array is deprecated. Use named arguments instead.`

### 1-3. ページネーション結果が `PaginatedResultSet` に
`paginate()` の戻り値が `PaginatedInterface`（`PaginatedResultSet`）になり、`isEmpty()` 等の ResultSet メソッドを**直接持たない**。先に `->items()` を呼ぶ。
```php
// Before（テンプレート等）
if (!$sites->isEmpty()) { ... }
// After
if (!$sites->items()->isEmpty()) { ... }
```

### 1-4. 翻訳キャッシュ設定のリネーム
`_cake_core_` → `_cake_translations_`。`config/app.php`・`config/bootstrap.php`・`BcUtil` 等の参照を更新。
- `config/plugins.php` で `'BaserCore' => null` のような値は `'BaserCore' => []` にする。

---

## 5.1 → 5.2 の変更

5.2 は「これまでサイレントに上書き/許容していた操作を例外化」する変更が多い。

### 2-1. イベントリスナーの戻り値返却が非推奨（5.3 でエラー化）
リスナーから値を `return` せず、`$event->setResult()` / `$event->stopPropagation()` を使う。戻り型は `: void` にできる。
```php
// Before
public function beforeSave(EventInterface $event) { return false; }
// After
public function beforeSave(EventInterface $event): void { $event->setResult(false); }
```
- **Table/Marshaller コールバックの戻り値は元々無視される**ので単に `return` を削除すればよい（`beforeMarshal` は `ArrayObject $data` を参照で書き換える。返り値は使われない）。
- コントローラ `beforeFilter` の定番修正:
  ```php
  // Before
  $response = parent::beforeFilter($event);
  if ($response) return $response;
  ...
  return $this->redirect(...);
  // After
  parent::beforeFilter($event);
  if ($event->getResult()) return;
  ...
  $event->setResult($this->redirect(...));
  return;
  ```
- クロージャ内の `return`（SEO等）は対象外（誤検出注意）。

### 2-2. `AssociationCollection::add()` が重複エイリアスで例外化
5.1までサイレント上書き → 5.2は `Association alias 'X' is already set.` を throw。
`initialize()` で定義済みの関連を、別メソッド/Service/Controller で `hasMany`/`belongsTo` 等で**再設定**すると発生（finder 差し替え等）。
```php
// 再設定の前に既存を削除してガードする
if ($table->associations()->has('CustomLinks')) {
    $table->associations()->remove('CustomLinks');
}
$table->hasMany('CustomLinks')->setFinder('all')->...;
```
- inline `hasMany('X')` は、ガード済みの専用メソッド（例: `setHasManyLinksByAll()`）に集約すると安全。
- 検出: `grep -rnE "\->(hasMany|belongsTo|belongsToMany|hasOne)\(" --include="*.php"` で **Service/Controller 内**の関連設定を洗う（Table の initialize 以外は要注意）。

### 2-3. 重複バリデーションルール名で例外化
同一フィールドに同名ルールを2回 `add` すると `A validation rule with the name 'X' already exists`。5.1は上書き。
- 同名ブロックが重複していれば**統合**（後勝ちの意図を保つ）。例: `alias` に対する複数の検証定義をまとめる、`minLength` を一度だけ追加する等。
- コールバックの名前付き引数 `$context` の衝突に注意（独自メソッドの第2引数を `$context` にしない）。
- 注意: テストが「同じ validator に同メソッドを2回呼んで上書きを確認」している場合は**テスト側の事情**で、本番フロー（1フィールド1回）では起きない＝アプリ修正対象外のことがある。

### 2-4. `ResultSet` の型・clone 不可
- `Query::all()` の戻り値が `Cake\ORM\ResultSet`（旧 `ResultSetDecorator`）。型宣言は `ResultSetInterface` か `iterable` に緩める。
- **ResultSet は clone 不可・イテレータ共有**。`clone $resultSet` や、ライブな ResultSet への二重イテレートで要素が消える。`->toArray()` で**早期に materialize** してから使う（フォームの項目が消える等の不具合の原因）。
- ヘルパ/サービスの引数型は `ResultSetInterface`/`iterable` にし、`is_array($x) ? $x : $x->toArray()` で吸収。

### 2-5. 配列条件の `null` 値が例外化
`where(['field' => null])` は `Expression 'field' has invalid 'null' value. ... operator (IS, IS NOT) is missing.` を throw。null安全な `IS` 演算子を使う。
```php
// Before
$conditions = ['Contents.entity_id' => $entityId];   // $entityId が null だと例外
// After
$conditions = ['Contents.entity_id IS' => $entityId]; // 非nullなら =、nullなら IS NULL
```

### 2-6. エンティティの文字列キャスト非推奨
テンプレ等で `(string)$entity` 相当（HTML 属性へ直挿し等）が非推奨。`json_encode($entity)` を使う。
```php
$this->BcBaser->js('...', false, ['data-current' => json_encode($content)]);
```

### 2-7. `PluginCollection::add()` が既ロードで例外化
`Plugin named 'X' is already loaded`。`addPlugin` の前にガードする。
```php
if (!$application->getPlugins()->has($name)) {
    $application->addPlugin($name);
}
```
- テーマ（`coreFrontTheme` 等）と `$themes` 配列で同名が二重追加されるケースに注意（`addTheme()`）。
- Migrations / Authentication / CakephpFixtureFactories 等の追加箇所も同様にガードする。

### 2-8. FormProtection の `_Token` not found（postLink + 独自JS）
baserCMS は postLink のフォームを独自JS（`plugins/bc-admin-third/src/js/admin/common/lib/jquery.bcToken.js`）で再構成して送信する。
CakePHP 5.2 で postLink の onclick が **`document.{form}.submit()` → `document.{form}.requestSubmit()`**（CSP対応）に変わり、抽出用正規表現が一致せず `_Token` が送られなくなる。
```js
// jquery.bcToken.js: 両対応にする
var regex = /document\.(post_.+?)\.(?:request)?[Ss]ubmit\(\)/;
```
- 修正後は管理テーマの JS を再ビルド: `cd plugins/bc-admin-third && npm run build`（bcToken は common/startup バンドルに含まれる。webpack が `src/**/*.js` を自動エントリー化）。
- 個別アクションを `unlockedActions` で除外するより、この正規表現修正の方が**全 postLink を一括で**直せる（CSRF保護も維持）。プラグイン無効化・テーマ切替/コピー/削除・キャッシュクリア等が一斉に復旧する。

### 2-9. `PluginCollection` の live イテレーション中の変更で後続プラグインの bootstrap がスキップ
5.2 の `BaseApplication::pluginBootstrap()` は `PluginCollection::with('bootstrap')` の **live なジェネレータ**を走査する。あるプラグインの `bootstrap()` がイテレーション中に `addPlugin()`/`remove()`（テーマ追加・DebugKit 削除等）でコレクションを変更すると、**直後に並ぶプラグインの `bootstrap()` がスキップ**され、`setting.php` 等が読み込まれず `Configure::read('BcXxx....')` が null になる。
- 本番では問題のプラグイン（baserCMS では BaserCorePlugin）がコンテンツプラグインを「後から」追加するため直後に要素が無く顕在化しないが、**テストで事前ロード（`appPluginsToLoad` 相当）すると顕在化**する。
- **修正**: live イテレーションをやめ、未 bootstrap が無くなるまで**スナップショット単位で bootstrap** する（テスト基盤 `BcTestCase::setUp` 等）。
  ```php
  $booted = [];
  do {
      $pending = [];
      foreach ($app->getPlugins() as $plugin) {
          if ($plugin->isEnabled('bootstrap') && !in_array($plugin->getName(), $booted, true)) $pending[] = $plugin;
      }
      foreach ($pending as $plugin) { $booted[] = $plugin->getName(); $plugin->bootstrap($app); }
  } while ($pending);
  ```

### 2-10. `View::_checkFilePath()` の第2引数がプラグイン名に（`..` を含むテンプレートで MissingPluginException）
5.2 で `View::_checkFilePath(string $file, ?string $plugin)` が第2引数 `$plugin` を `_paths($plugin)` に渡すようになった。`_getTemplateFileName()`/`_getLayoutFileName()` をオーバーライドして**ファイルシステムパスを第2引数に渡している**と、パスをプラグイン名と誤解して `..` を含むテンプレート（`render('../element/...')` 等）が 500 になる。
- **修正**: 第2引数には**パスではなくプラグイン名**を渡す。`_checkFilePath($path . $name, $path)` → `_checkFilePath($path . $name, $plugin)`。
- `..` を含まない通常テンプレートは早期 return のため影響なし（顕在化するのは `..` 経由のみ）。

### 2-11. TreeBehavior: ルートに `parent_id=null` をセットして保存するとツリー破損
5.2 の TreeBehavior は、ルート（`parent_id=0`）のエンティティに **`parent_id=null` をセットして保存すると「ルートへの移動」とみなして `lft`/`rght` を再計算**する。結果ツリーが壊れ、配下の URL 生成等が失敗する。
- **修正**: ルート更新時は `parent_id` を**データに含めない**（変更しない）。
  ```php
  $data = ['id' => $id, 'name' => $name, ...];
  if (!is_null($rootContentId)) $data['parent_id'] = $rootContentId; // ルート(null)では parent_id を触らない
  ```

### 2-12. `Table::get()` の第2引数も名前付き引数に（配列 options は非推奨）
`find()` と同様、5.2 で `Table::get($id, mixed ...$args)` となり options 配列は非推奨。
```php
// Before → After
$table->get($id, ['contain' => ['Users']]);   →  $table->get($id, contain: ['Users']);
// 動的に options を組む場合はスプレッドで名前付き引数展開（PHP 8.1+）
$query->find($finder, array_merge(['prefix' => $prefix], $options));
  → $query->find($finder, ...array_merge(['prefix' => $prefix], $options));
```
- スプレッド展開は**文字列キーの連想配列**前提（finder/get の名前付き引数名に一致）。

### 2-13. `Entity::set([...])`（配列一括セット）の非推奨
エンティティへの配列一括セットは `patchEntity()` を使う。
```php
// Before（テスト等）
$entity->set(['field' => $v, ...]);
// After
$table->patchEntity($entity, ['field' => $v, ...]);
```

---

## エコシステム: cakephp/migrations 4.5+（CakePHP 5.2 期に同梱更新）

### M-1. `AbstractMigration` / `AbstractSeed` の非推奨 → `BaseMigration` / `BaseSeed`
migrations 4.5 以降、`Migrations\AbstractMigration` / `Migrations\AbstractSeed` が非推奨化。マイグレーションを走らせるテストで `Migrations\AbstractMigration is deprecated` が大量に出る（xdebug のスタックダンプも相まって大量エラーに見える）。
- **修正**: 基底クラスを `BaseMigration` / `BaseSeed` に変更。`use Migrations\Table;` → `use Migrations\Db\Table;`（`table()` の戻り型 `Migrations\Db\Table` に合わせる）。
- `BaseMigration::__construct(int $version)`（**引数は version の int 1つ**。旧 `AbstractMigration` の多引数から変更）。`$this->input` は無い。
- baserCMS のように `table()` をオーバーライドしてプレフィックスを付与している場合、`$this->input` が使えないため**実行中アダプターの接続設定から取得**する：
  ```php
  public function table(string $tableName, array $options = []): \Migrations\Db\Table {
      $prefix = $this->getAdapter()->getConnection()->config()['prefix'] ?? '';
      return parent::table($prefix . $tableName, $options);
  }
  ```
- インラインのテスト用マイグレーション（テスト内で `$file->write('<?php class X extends AbstractMigration ...')`）も忘れず `BaseMigration` に変更する。

### M-2. Migration テストの新アダプタ API（`Phinx\Db\Adapter\AdapterFactory` 廃止）
旧 `Phinx\Db\Adapter\AdapterFactory` + Symfony Console の `setInput()` は新基底と非互換。新 `Migrations\Db\Adapter\AdapterFactory` を使い、アダプタ種別は**接続のドライバ名から導出**する（migrations 本体 `Environment::getAdapter()` と同じ）。MySQL 決め打ちを避けると PostgreSQL/SQLite でもテストが通る。
```php
$connection = ConnectionManager::get('test');
$driverClass = get_class($connection->getDriver());
$adapterType = strtolower(substr($driverClass, (int)strrpos($driverClass, '\\') + 1)); // mysql / postgres / sqlite
$options = ['adapter' => $adapterType, 'connection' => $connection] + (array)$connection->config();
$adapter = \Migrations\Db\Adapter\AdapterFactory::instance()->getAdapter($adapterType, $options);
```

---

## バージョン非依存（CakePHP 5.x 全般 / baserCMS 固有）

### 3-1. コントローラは Response を返す（出力直書き禁止）
`header()` + `echo readfile()` 等の直接出力は `headers already sent` の原因。Response を返す。
```php
$content = file_get_contents($distPath);
// 後始末（unlink / emptyFolder）を先に実施
return $this->getResponse()
    ->withType('zip')
    ->withHeader('Cache-Control', 'no-store')
    ->withHeader('Content-Disposition', 'attachment; filename="' . basename($distPath) . '"')
    ->withStringBody($content);
```
- KCAPTCHA 等の出力は `ob_start()`/`ob_get_clean()` で文字列化し `withStringBody()` で返す。
- 戻り型 `: void` を付けている場合は `: \Cake\Http\Response` に変える。
- ダウンロード系（テーマDL・バックアップDL・ログDL）に多い。

### 3-2. クラスのオーバーライドと自動検出ツール（baserCMS固有）
baserCMS は一部 CakePHP クラスを `plugins/baser-core/src/Routing/`（`namespace Cake\Routing`）でオーバーライドしている（`Asset`・`RouteCollection` 等）。
`AnalyseController` がソースを走査し `ReflectionClass` でロードする際、パス由来のクラス名（`\BaserCore\Routing\Asset`）でロードすると、別名前空間の既ロードクラス（`Cake\Routing\Asset`）を再宣言して **`Cannot redeclare class`** で Fatal になり、全テストが途中停止する。
→ `AnalyseController::CONVERT_CLASS_NAME` に `'\BaserCore\Routing\Asset' => '\Cake\Routing\Asset'` のように補正を追加（**オーバーライドを増やしたら必ず登録**）。

### 3-3. グローバル `env()` 依存の検出器は統合テストで再現不可 → `$request->getEnv()`
リバースプロキシ判定等で 'https' detector を上書きする際に**グローバル `env()`** を見ていると、統合テスト（`$this->configRequest()` で `environment` を差し込む）では再現できず分岐が効かない。`$request->getEnv()` を使うと本番同等かつテストでも再現可能。
```php
// Before: $request->addDetector('https', fn() => env('HTTPS') === 'on' || ...);
// After:  $request->addDetector('https', fn($r) => $r->getEnv('HTTPS') === 'on' || ...);
```

### 3-4. 全テスト実行（長時間）で時刻比較がズレる → 実 `time()` を使わない
`time()`（実時刻）と DB の `modified` 等（テストでは Chronos 固定の bootstrap 時刻）を比較する許容時間チェックは、全テストが数十分かかると範囲外になり**長時間実行時だけ**失敗する。`\Cake\I18n\DateTime::now()->getTimestamp()` を使うと Chronos の固定/移動に追随し、本番では実時刻と同一。

### 3-5. `config/routes.php` の `fallbacks()` がコンテンツルーティングを隠す（テストで 404）
baserCMS はコンテンツルーティング（`BcContentsRoute` = `/*`）で URL を解決し、`BaserCorePlugin::disableRootRoutes()` が `config/routes.php` の `fallbacks()`（`/{controller}/{action}/*`）を無効化している。だが `disableRootRoutes()` は `BcUtil::isConsole()` で**コンソール（ユニットテスト）ではスキップ**されるため、テストでのみ fallbacks が残り `/{controller}/{action}/*` が `BcContentsRoute` より先にマッチしてブログのアーカイブ等が 404 になる。
- **安全策（現行）**: `config/routes.php` の `$builder->fallbacks();` を**無効化**（コメントアウト）して本番挙動に合わせる。baserCMS はコンテンツルーティング主体で汎用 controller fallback を使わないため、これが最も堅牢。
- **⚠️ アンチパターン**: 「テストでも `disableRootRoutes()` を走らせる（`isTest()` で許可）」案は**避ける**。`disableRootRoutes()` はリフレクションでルートコレクションを破壊的に空にするが、テスト環境では**ルートコレクションがリクエストを跨いで蓄積**されるため、2回目以降のビルドで API ルート等まで巻き込んで wipe し、**全 API 統合テストが 404** になる（実測で確認済み）。fallbacks を消すだけの surgical な除去でも、wipe の副作用と相殺せず破綻しやすい。

---

## 調査の進め方（チェックリスト）

1. **致命的か警告か**を `logs/debug.log` / `logs/error.log` で判別。
2. 失敗が多いときは**例外メッセージを集計**して systemic な原因を特定。
3. 各原因について**アプリ src 起因**か**テスト/環境/fixture/i18n 起因**かを切り分け（`git diff HEAD` / `Plugin::isLoaded` / DIコンテナ登録 / 英語↔日本語メッセージ）。
4. アプリ src 起因を該当バージョン節のレシピで修正 → `php -l` で lint → 当該テストを `--filter` で単体確認。
5. 仕上げに全テストを再実行して件数の改善と新規回帰の有無を確認。

関連: PHP本体起因（暗黙nullable・動的プロパティ・fgetcsv・Reflection）は `php-migration` スキルへ。
