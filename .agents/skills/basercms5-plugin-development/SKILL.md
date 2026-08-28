---
name: basercms5-plugin-development
description: 'baserCMS 5系（CakePHP 5ベース）のプラグインを新規開発・改修する際の正本パターン集。「プラグインを作成」「bc_plugin で bake」「管理画面を追加」「Table/Entity の書き方」「initialize でアソシエーション宣言」「validationDefault」「fetchTable と TableRegistry の使い分け」「BcAdminForm / control() の書き方」「検索フォーム element」「control の id/name 生成規則と JS 連携」「イベントリスナー（BcModelEventListener 等）」「adminNavigation の登録」「setting.php / config.php」「BcShortCode 登録」「paginate($query)」「redirect は return」「_ids で belongsToMany 保存」「BcKeyValue」「afterSave の再入ガード」「$.bcUtil.adminBaseUrl」「Time::format の ICU パターン」「routes.php の RouteBuilder」等で参照する。環境・ディレクトリ・命名・コアハック禁止などの共通ルールは basercms5-development、テーマ開発は basercms5-theme-development、4系→5系の移行作業は basercms-plugin-4-to-5-upgrade、テスト実行・テスト基盤導入は basercms-unittest を参照。'
license: MIT
---

# baserCMS 5系 プラグイン開発の正本パターン集

baserCMS 5系（CakePHP 5ベース）で**プラグインを新規開発・改修する**ときのコーディングパターンを機能別にまとめたもの。環境・ディレクトリ構成・命名規則・コアハック禁止（`vendor/baserproject`・`vendor/cakephp` は読み取り専用）などの共通ルールは **basercms5-development**、テーマ（templates 中心）の開発は **basercms5-theme-development**、4系からの移行作業は **basercms-plugin-4-to-5-upgrade**、テストの実行・基盤導入は **basercms-unittest**、bc-custom-content（カスタムコンテンツ）を使った開発は **basercms5-custom-content-development** を参照する。

## 1. 新規開発の始め方

- **雛形は bake で生成する**: `bin/cake bake bc_plugin {PluginName}` でプラグイン雛形、`bin/cake bake bc_all {ModelName} --plugin {PluginName}` で Table/Entity/Controller/テンプレート一式を生成できる。既存プラグインへのテスト基盤の後付け手順は **basercms-unittest** を参照。
- **標準ディレクトリ構成**:
  - `src/{PluginName}Plugin.php` — プラグインクラス
  - `src/Controller/`（フロント）・`src/Controller/Admin/`（管理画面）
  - `src/Model/Table/`・`src/Model/Entity/`
  - `src/View/Helper/`・`src/Event/`（イベントリスナー。置くだけで自動 attach される）
  - `config/`（`config.php`・`setting.php`・`routes.php`・`Migrations/`）
  - `templates/`（フロント）・`templates/Admin/`（管理画面。検索フォームは `templates/Admin/element/search/`）
  - `webroot/`（js/css/img。Vue ソースは `webroot/js/src`）
- **プラグインクラス**は `class SamplePlugin extends \BaserCore\BcPlugin` と書く。`install(array $options = []): bool` は `return parent::install($options);` を基本とし、`bootstrap()` は**動的な設定（実行時に決まる値の Configure 追加、ログ設定の登録など）**にのみ使う。
- **setting.php / config.php は「静的な return 配列」にする**（実行型コードを書かない）。動的に決まる部分は `bootstrap()` に分離する。
  ```php
  // config/setting.php
  return [
      'BcApp' => [...],
      'BcShortCode' => ['Sample' => ['Sample.showList']],
  ];
  ```
- **adminNavigation（管理メニュー）**は setting.php に登録する。`'plugin'`・`'controller'` は**いずれも CamelCase** で書く（`'controller' => 'SampleArticles'`）。snake_case で書くと**全管理画面が404になる**（メニュー描画時のURL解決に失敗するため対象画面以外も巻き込む）。
- **管理画面URLの `{controller}` セグメントは実際にはプラグイン名をダッシュ区切り（kebab-case）にしたもの**（例: `BlogTagGroup` → `/baser/admin/blog-tag-group/{controller}/...`）。他プラグインのURLパターンから類推せず、`bin/cake routes | grep <PluginName>`（大文字小文字を落として検索）で実際のルートを確認してから使う。
- **BcBlog（`bc-blog`）の管理画面「記事編集」URLは `edit/{blogContentId}/{id}` の2引数が必須**（`Admin\BlogPostsController::beforeFilter()` が `$this->request->getParam('pass.0')` を**ブログコンテンツID**として要求し、無いと `BcException: コンテンツデータが見つかりません。` になる）。記事IDだけの `edit/{id}` ではアクセスできない。他プラグインが特定ブログ記事の編集画面へリンクを張る場合（例: 追加フィールドプラグインの編集導線）は、`edit($blogContentId, $id)` のシグネチャ通り両方を渡す。
- **有効化**は管理画面のプラグイン管理から行うか、`plugins` テーブルへ直接 INSERT（`status=1`）する。DBで有効化されたプラグインは自動で読み込まれるため、composer への登録や `composer dump-autoload` は不要。
- サードパーティ製ライブラリ（例 `phpoffice/phpspreadsheet`）は `composer require` で導入する。雛形アセット（Excel テンプレート等）は `templates/Admin/Excel/...` に置き、`\Cake\Core\Plugin::templatePath('Sample') . 'Admin' . DS . 'Excel' . DS . ...` で参照する（`templatePath()` は末尾スラッシュ付き）。

## 2. ORM / Table / Entity

責務分離: バリデーション・ビジネスロジックは Table（`src/Model/Table`）、行データの振る舞いは Entity（`src/Model/Entity`）に書く。DB変更は Migration (phinx) を使う。

- **アソシエーションは `initialize()` で宣言し、エイリアスは複数形**にする。`className` はプラグイン接頭辞付き・複数形で書く。エイリアスは `contain()`・条件キー・エンティティのプロパティ名すべてに波及する。
  ```php
  public function initialize(array $config): void
  {
      parent::initialize($config);
      $this->setTable('sample_articles');
      $this->addBehavior('BaserCore.BcUpload', [...]);
      $this->belongsTo('Users', ['className' => 'BaserCore.Users', 'foreignKey' => 'user_id']);
      $this->hasMany('SampleComments', ['className' => 'Sample.SampleComments', 'foreignKey' => 'article_id', 'dependent' => true]);
      $this->belongsToMany('SampleTags', [
          'className' => 'Sample.SampleTags',
          'joinTable' => 'sample_articles_sample_tags',
          'foreignKey' => 'article_id',
          'targetForeignKey' => 'tag_id',
      ]);
  }
  ```
  `dependent` は `hasMany`/`hasOne` のみ有効。
- **バリデーションは `validationDefault()`** に書く。カスタムルールはシグネチャ `($value, $context)` のメソッドを `add()` で登録する。
  ```php
  public function validationDefault(\Cake\Validation\Validator $validator): \Cake\Validation\Validator
  {
      $validator->notEmptyString('title', 'タイトルを入力してください');
      $validator->add('title', 'custom', ['rule' => [$this, 'customMethod'], 'message' => '...']);
      return $validator;
  }
  ```
- **検索はクエリビルダ**で書く: `$this->find()->where([...])->contain([...])->orderBy([...])->first()/all()`。list は `find('list', keyField: 'id', valueField: 'name')`。
  - **配列値に IN は自動付与されない**。複数値は必ず `'field IN' => (array)$values` と明示する（書かないと `Cannot convert value Array` 例外）。**空配列の `IN ()` は例外**になるので、空になりうる場合は先頭でガードして早期 return する。
  - **null 一致は `IS` 演算子**: `['field IS' => null]`／否定は `['field IS NOT' => null]`。
  - 関連テーブルの列で絞り込むときは `contain()` ではなく `->innerJoinWith('Assocs')` か `matching()` を使う（`contain` は WHERE に使えない）。条件キーは複数形エイリアス（`SampleComments.user_id`）。常時 join すると関連なし行が除外される点に注意。
- **保存はエンティティ経由**: `$entity = $table->newEntity($data);`（新規）または `$entity = $table->patchEntity($table->get($id), $data);`（更新）→ `$table->save($entity);`。
- **belongsToMany の保存は `_ids`** で書く: `patchEntity($e, $data + ['sample_tags' => ['_ids' => [$id, ...]]], ['associated' => ['SampleTags']])`。表示側は `contain(['SampleTags'])`。
- **name/value の KVS テーブル**は `initialize()` に `$this->addBehavior('BaserCore.BcKeyValue')` を足すと `saveKeyValue([$key => $value])` が使える。読み出しは `find()->where(['name' => $key])->first()->value`。
- **集計は `func()->sum()` ＋ `->first()->alias ?? 0`** で書く:
  ```php
  $total = $this->find()
      ->where(['user_id' => $userId])
      ->select(['total' => $this->find()->func()->sum('amount')])
      ->first()->total ?? 0;
  ```
  - **datetime カラムの MIN/MAX に集計関数を使わない**。`func()->min()/max()` は結果が数値型にキャストされて壊れるため、`->orderBy(['col' => 'ASC'])->first()` で最古/最新行を取り、そのエンティティの日時プロパティを使う。
- **afterSave で他テーブルを save する（相互に save し合う）場合は再入ガードフラグを置く**。`save()` に `callbacks` オプションは**存在しない**（渡しても黙って無視され afterSave は常に発火する）ため、抑止はフラグで行う。
  ```php
  private bool $inAfterSave = false;
  public function afterSave(EventInterface $e, EntityInterface $entity, \ArrayObject $o): void
  {
      if ($this->inAfterSave) return;
      $this->inAfterSave = true;
      try { /* 他テーブルの save 等 */ } finally { $this->inAfterSave = false; }
  }
  ```
- **削除は `get($id)` → `delete($entity)`** で書く（関連の `dependent => true` が cascade する）。`deleteAll()` は ORM の cascade を通さず子が孤児として残るので、子を連れて消す削除には使わない。
- **`fetchTable()` は Controller / Command 専用**。Table・Lib・Helper・イベントリスナー内では `\Cake\ORM\TableRegistry::getTableLocator()->get('Sample.SampleArticles')` を使う（`fetchTable` を呼ぶと `BadMethodCallException`）。エイリアスは常に複数形。
- **`getControlSource()`（フォーム選択肢の供給元）**は Table に定義し、テンプレートからは `$this->BcAdminForm->getControlSource('Sample.SampleArticles.user_id')` のように **`Plugin.複数形モデル.field`** で呼ぶ（単数形だと `MissingTableClassException`）。
- **`disableHydration()` を使うと結果はフラット連想配列**になる。select には明示エイリアスを付けて（`->select(['id' => 'SampleArticles.id', 'tag_name' => 'SampleTags.name'])`）`$row['id']` でフラット参照する。
- 生SQLが必要なときは `$this->getConnection()->execute($sql)->fetchAll('assoc')`（戻りはフラット連想配列）。トランザクションは `$this->getConnection()->begin()/commit()/rollback()`。
- プラグイン共通のメソッドは共通基底 Table（`\BaserCore\Model\Table\AppTable` を継承した `SampleAppTable` 等）に置き、各 Table はそれを継承する。

## 3. コントローラ

管理画面コントローラは `src/Controller/Admin/` に置き、プラグインの Admin 基底（`SampleAdminAppController` など、`BcAdminAppController` を継承）を経由して継承する。

- **テーブル取得は `$this->fetchTable('Sample.SampleArticles')`**（複数形エイリアス）。
- **ページネーションはクエリビルダを組んで `$this->paginate($query)`** に渡す（PaginatorComponent は存在しない。`loadComponent('Paginator')` は `MissingComponentException`）。必要な関連は `contain()`、手書き結合は `leftJoinWith()`/`innerJoinWith()`/`matching()`、`->groupBy()`/`->having()` も使える。並べ替え・ページはクエリ文字列 `?sort=&direction=&page=` で自動処理される。
- **サイト設定値は `\BaserCore\Utility\BcSiteConfig::get('admin_list_num')`** で取得する。ログインユーザーは `\BaserCore\Utility\BcUtil::loginUser()`（未ログイン時は **`false`** を返すので `?->` は効かない。`(BcUtil::loginUser() ?: null)?->id` か明示分岐で書く）。
- **フラッシュメッセージは `$this->BcMessage->setSuccess('...')` / `setError('...')`**。
- **`redirect()` は必ず `return $this->redirect([...]);` と書く**。redirect は Response を返すだけで**後続コードが実行され続ける**ため、ガード節（引数チェック→リダイレクト）では return しないと直後の処理まで走って TypeError 等になる。**例外**: `initialize()`/`beforeFilter()` など `: void` 宣言のメソッド内では return を付けない（付けると TypeError）。
- **request は immutable**。getter への代入は書けない。値を足すときは `withQueryParams()`/`withData()` で新しい request を作って `setRequest()` する。
  ```php
  $this->setRequest($this->getRequest()->withQueryParams(
      $this->getRequest()->getQueryParams() + ['begin' => $begin]
  ));
  ```
  読み取りは `getData('key')`/`getQuery('key')`/`getParam('key')`/`getQueryParams()`。ajax 判定は `$this->getRequest()->is('ajax')`。`getParam('controller')` は **CamelCase**（`'SampleArticles'`）で返る点に注意（分岐で snake_case と比較すると常に false）。
- **カスタム Component** は `public function initialize(array $config): void { parent::initialize($config); }` のシグネチャで書き、コントローラ参照は `$this->getController()` で取得する。使用するコントローラの `initialize()` で `$this->loadComponent('Sample.Excel')` のように明示ロードする（自動ロードはされない）。
- **delete アクションの定石**: `$this->getRequest()->allowMethod(['post', 'delete'])` → `$entity = $table->get($id)`（`RecordNotFoundException` を try/catch して「無効な処理です」→ index へ）→ `$table->delete($entity)`。一覧の bca-submit-token 経由の POST を受けるアクションは FormProtection の `unlockedActions` に登録する（§6）。
- **テンプレートが参照する計算値・設定値はコントローラで明示的に `set()` する**。旧来のコールバックによる自動注入は無いので、行ごとの計算値は Table の計算メソッド（`calcBalance($entity)` 等）を paginate 後にループ適用する。プラグイン設定（`Configure::read('Sample')`）を多数のテンプレートが参照する場合は、**プラグインの Admin 基底コントローラの `beforeRender()` で一括 set** する。
  ```php
  public function beforeRender(\Cake\Event\EventInterface $event): void
  {
      parent::beforeRender($event);
      $this->set(\Cake\Core\Configure::read('Sample'));
  }
  ```
  各 Admin コントローラは `BcAdminAppController` 直継承ではなくこの基底を継承する（直継承だと設定ビュー変数が全画面で欠落する）。
- エラー応答は例外で書く: `throw new \Cake\Http\Exception\BadRequestException(...)` / `NotFoundException` / `InternalErrorException`。レスポンス操作は `$this->setResponse($this->getResponse()->withStatus(400))`。
- 検索インデックス・ヘルプは `$this->setSearch('sample_articles_index')` / `$this->setHelp('...')` で設定する。

## 4. 管理画面フォーム・一覧・検索

- **管理画面のフォームは `$this->BcAdminForm` を使い、入力は必ず `control()` で出す**（`input()` は無い）。フロント用 `$this->BcForm`/`$this->Form` や素のウィジェット（`text()`/`select()`/`multiCheckbox()`）を直接呼ぶと bca-* の管理スタイルが当たらず崩れる。一覧の一括処理（`ListTool.batch`）・一括選択（`ListTool.checkall`）・`submit()`/`button()` も `BcAdminForm` で出す。
- **フォーム開始**: エンティティがあるなら `create($entity)`、コンテキストレスなら `create(null, ['valueSources' => ['data', 'context']])` と書く（GET検索フォームは `['query', 'context']`）。文字列モデル名を渡すと `No context provider found for value of type string`。
- **複数チェックボックスは options をループして個別 control** で書く（`['multiple' => 'checkbox']` や `multiCheckbox()` は崩れる）:
  ```php
  <?php $selected = array_map('strval', (array)$this->getRequest()->getData('SampleArticle.tags')) ?>
  <?php foreach($options as $v => $label): ?>
    <?php echo $this->BcAdminForm->control('SampleArticle.tags[]', [
        'type' => 'checkbox', 'label' => $label, 'value' => $v,
        'checked' => in_array((string)$v, $selected, true), 'hiddenField' => false]) ?>
  <?php endforeach ?>
  ```
  受け側の条件は `'field IN' => (array)$values`（§2）。
- **`control()` の `label` に HTML を渡すときは配列形式** `['text' => '<span...>', 'escape' => false]` と書く（文字列だとエスケープされ生表示）。
- **検索フォームは `templates/Admin/element/search/{name}.php`（単数 `search/`）** に置く。コントローラの `setSearch('sample_articles_index')` がこのパスを解決する。検索は GET 送信が基本（条件がURLに残りページャ遷移でも保持）で、コントローラは `getQueryParams()` から条件を読む。入力値の再表示には `valueSources` に `query` を含める。
- **`control()` の DOM 生成規則を把握して JS と整合させる**（描画は通るのに JS 連携が無言で死ぬ最頻出ポイント）:
  - **id は小文字ハイフン**: `control('SampleArticle.publish_date')` → `id="samplearticle-publish-date"`（`Text::slug` による）。
  - **name は `Model[field]`** 形式: `name="SampleArticle[publish_date]"`。
  - バンドルJSが CamelCase id（`#SampleArticlePublishDate`）や別形式の name を掴む設計なら、**JS 側を実生成の id/name に合わせる**か、`control(..., ['id' => 'SampleArticlePublishDate'])` で明示 id を振る（その場合 label の `for` も合わせる。name は POST キーなので変えない）。フォーム全体への委譲 `$("#FormId").on('change', 'select, input', ...)` にすると id 依存自体をなくせる。
  - **統合テストで「JS が期待する id/name が HTML に実在する」ことを assert する番人パターン**を置くと、この不整合の回帰を検出できる（描画200のテストでは検出できない。basercms-unittest 参照）。
- **年月セレクトは `control('Model.field.year', ['type' => 'select', 'options' => $years, 'label' => false])`** のように自前 options で書く（`year()`/`month()` というメソッドは無い）。`getData('Model.field')` は `['year' =>, 'month' =>]` で受かる。
- **一覧 element とフォームの同居に注意**: `Paginator->sort()` や `element('pagination')` を含む一覧 element を、ページネーションしない画面（編集フォームへの埋め込み等）で描画すると `You must set a pagination instance using setPaginated() first` で落ちる。ページャ系の描画は「専用一覧コントローラのときだけ」に `getParam('controller') === 'SampleArticles'`（CamelCase）でガードする。
- **生SQL等のカスタムページネーション**は、総件数＋当ページ行を自前で取得し `new \Cake\Datasource\Paging\PaginatedResultSet(new \ArrayIterator($rows), $params)` をビュー変数に set する（PaginatorHelper が `PaginatedInterface` を自動検出する）。sort カラムはホワイトリストで ORDER BY を組み立てる。
- フォーム拡張フックは `$this->BcFormTable->dispatchBefore()/dispatchAfter()`、`$this->BcAdminForm->dispatchAfterForm()` を使う。
- `BcBaser->link()` に画像/HTML を渡すときは `['escape' => false]`。アイコンボタンは `['class' => 'bca-btn-icon', 'data-bca-btn-type' => 'add', 'data-bca-btn-size' => 'lg']` の作法で書く。

## 5. イベント

リスナーは `src/Event/` に置く。**プラグインの BcPlugin が自動 attach する**ため、リスナーが壊れているとイベント発火で即 Fatal になる点に注意して書く。

- **自動 attach の実体・ファイル名の厳密な規則**: `BaserCore\Utility\BcEvent::registerPluginEvent()`（`BaserCorePlugin` から有効化済み全プラグインに対して呼ばれる）が、`src/Event/{Plugin名}{Controller|Model|View|Helper|Mailer}EventListener.php` という**ファイル名と完全一致するクラス**だけを自動生成・`EventManager::instance()->on()` で登録する。例えば `<Plugin>` プラグインでヘッダーを差し替えるビューイベントリスナーなら、ファイル名は必ず `src/Event/<Plugin>ViewEventListener.php`、クラス名も `<Plugin>ViewEventListener` にする（名前が1文字でもズレると静かに登録されず、イベントが一切発火しない）。
- **基底クラス**: モデル系は `BcModelEventListener`、コントローラ系は `BcControllerEventListener`、ビュー系は `BcViewEventListener`、ヘルパ系は `BcHelperEventListener` を継承する。
- **イベント名の命名規則**: `public $events = [...]` に登録する。
  - 自プラグインのモデルイベント: `'SampleArticles.afterSave'`（**Table 名の複数形**）。
  - 他プラグインを修飾する場合: `'BcBlog.BlogPosts.afterSave'`（プラグイン名＋Table複数形）。名前が実在イベントと一致しないと**エラーも出ずに無発火**になるため、発火側を `grep -rn "dispatchLayerEvent\|createEvent" vendor/baserproject/...` して実在するイベント名に合わせる。
  - View 系は `BcViewEventDispatcher` が標準イベントを `{Plugin}.{ViewName}.afterRender` として再ディスパッチする方式。`'BcBlog.Blog.afterRender'` のように View の `getPlugin()`/`getName()` に一致させる。
- **ハンドラ名はイベント名の CamelCase**: `'BcBlog.BlogPosts.afterSave'` → `public function bcBlogBlogPostsAfterSave(EventInterface $event)`。型ヒントは `\Cake\Event\EventInterface`。
- **イベントデータ**: 読み取りは `$event->getData('key')`、書き込みは `$event->setData('key', $v)`、サブジェクト（Table/Controller/View）は `$event->getSubject()`。保存系イベントのエンティティは `$event->getData('entity')` で取る。
- **コンストラクタで Table を即時取得しない**。リスナーは bootstrap 段階で生成されるため、コンストラクタ内の `TableRegistry::...->get()` は `MissingTableClassException` になる。ハンドラ内で必要になったときに**遅延取得**する。
- **`Model.afterFind` というイベントは存在しない**。find 結果の加工は `formatResults()` かカスタム finder に書く。
- View 系ハンドラでビュー変数を読むときは `$view->get('varName')` を使う（`$view->viewVars` プロパティは無い）。`$view->request` プロパティも無い（未知プロパティ参照は**ヘルパの自動ロード**扱いとなり `xxxHelper could not be found` で全画面を巻き込む）ので `$view->getRequest()->getParam(...)` で書く。
- ハンドラからビューへ値を渡すときは `$controller->set('x', $v)`（request への代入は immutable のため無効）。

## 6. Vue / jQuery 連携

- **管理画面の ajax / リンク URL をハードコードしない**。配置（サブディレクトリ・prefix 変更）で壊れるため、baser が用意するグローバル **`$.bcUtil.adminBaseUrl`**（`baseUrl + '/' + baserCorePrefix + '/' + adminPrefix + '/'`、末尾スラッシュ付き）に連結して書く: `$.bcUtil.adminBaseUrl + 'sample/sample_articles/edit/' + id`。
  - **Vue2 のテンプレート式からはグローバル `$` を参照できない**ので、computed `adminBaseUrl() { return $.bcUtil.adminBaseUrl; }` を公開し `:href="adminBaseUrl + 'sample/...'"` と書く。
- **Vue バンドルはソース（`webroot/js/src`）を直して webpack で再ビルドする**（コンパイル済み `.bundle.js`/`.bundle.css` を手で編集しない）。`webpack.config.js` があれば `cd plugins/Sample && npm install && npx webpack`。出力は `webroot/js/[name].bundle.js`、CSS は `../css/[name].bundle.css`。再ビルド後に bundle へ反映されたか grep し、ブラウザは Cmd+Shift+R で確認する。
- **ajax アクションは Response を返す**: `return $this->getResponse()->withType('application/json')->withStringBody(json_encode($rows ?: []));`。文字列を return しても出力されない。Vue が参照するフィールドはフラット配列に整形して返す。
- **Vue が動的生成するフォーム行（明細等）は FormProtection に弾かれる**（`Unexpected field '...' in POST data`）。描画時に DOM に無いフィールドは送信トークンに含まれないため、動的フィールドを POST するアクションは `beforeFilter()` で `$this->FormProtection->setConfig('unlockedActions', ['add', 'edit', ...])` に登録する（CSRF は別ミドルウェアなので維持される）。Vue 側の `:name` は `'Model[' + i + '][field]'` 形式で書く（コントローラの `getData('Model')` で受かる形）。
- prop に複数型が来るなら `type: [String, Number]` のように許容する。ajax レスポンスが JSON でなく文字列になると `v-for` が文字単位で反復する（`Expected Array, got String` が1文字ずつ大量に出たらサーバ側エラーを疑う）。

## 7. 日付・数値・文字列

- **`$this->Time->format($d, 'yyyy-MM-dd')` は ICU パターン**で書く（PHP の `date()` 形式ではない。`Y`=週年・`m`=分・`M`=月なので `'Y-m-d'` と書くと月が0になる）。スラッシュ区切りは `'yyyy/MM/dd'`、時刻込みは `'yyyy-MM-dd HH:mm'`。**第2引数を省略すると日時表示になる**ため、日付のみは必ず指定する。`BcTime->format` も同様。
  - 一方、**エンティティの日時プロパティの `$v->format('Y/m/d')` は PHP の date() 形式**（`DateTimeInterface::format`）。TimeHelper 経由（ICU）と使い分ける。
  - null になりうる日付は `$this->Time->format($e->x, 'yyyy-MM-dd')`（helper は null で空文字）か `$e->x?->format('Y-m-d')` で書く。
- **`Number::format()` / `NumberHelper::format()` / `currency()` は第1引数 null 不可**（TypeError）。nullable な値は `format($x ?? 0, [...])` / `currency($e->budget ?? 0, ...)` とガードする。
- **`Text::truncate()` / `BcText::truncate()` も第1引数 string 必須**。nullable カラムは `truncate((string)$e->notes, 46)` と `(string)` キャストする。
- **独自の数値フォーマット登録（addFormat 相当）は無い**。記号なし3桁区切りは `$this->Number->format($v, ['places' => 0])` で書く（`currency()` の第2引数は ICU の ISO 通貨コード前提で、未知コードは接頭辞としてそのまま出力される）。

## 8. ルーティング・ショートコード・管理画面CSS

- **`config/routes.php` は RouteBuilder のクロージャ**で書く:
  ```php
  <?php
  use Cake\Routing\RouteBuilder;
  return function (RouteBuilder $routes) {
      $routes->connect('/sample/*', ['plugin' => 'Sample', 'controller' => 'SampleArticles', 'action' => 'view']);
  };
  ```
  コントローラ名は CamelCase。`Router::getRequest()` を routes.php で使う場合は **CLI 実行時に null** になるため `if (!$request) return;` のガードを必ず入れる（無いと `bin/cake` が全コマンド停止する）。
- **ショートコードは setting.php の `'BcShortCode'` キーに登録**する（§1のコード例参照）。登録元プラグインが無効だと未登録扱いとなり、コンテンツ中の `[Sample.showList]` が**エラーなく生テキストのまま露出**する（プラグイン有効化が前提）。
- **横長テーブル（多数列）の管理画面**では、管理テーマの `.bca-container`(flex) 子要素 `.bca-main` に `min-width:0` が無いためページ全体が横スクロールする。その画面の CSS に `.bca-main { min-width: 0; }` を足し、テーブルは `overflow:auto` のスクロールラッパーで囲う（`position:sticky` の列固定もこれで効く）。CSSがバンドル内なら webpack 再ビルド（§6）。

## 9. テスト

テストの実行手順・プラグイン単体（スタンドアロン）テスト基盤の導入・Fixture Factory の書き方は **basercms-unittest** スキルを正本として参照する。開発時の要点だけ挙げると: 管理画面はログイン付きコントローラ統合テストで GET 描画200＋POST フロー（DB 変化の assert）を固定する、描画200は warning を握り潰すのでテスト後に `error.log` の pristine を確認する、JS 連携がある画面は「JS が期待する DOM id/name が HTML に実在する」assert（§4の番人パターン）を置く、日付保存は値まで assert する（silently NULL を見逃さない）。
