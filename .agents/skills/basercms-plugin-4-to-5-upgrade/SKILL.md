---
name: basercms-plugin-4-to-5-upgrade
description: 'baserCMS 4 (CakePHP 2ベース) のプラグイン内部コードを baserCMS 5 (CakePHP 5ベース) へ移行する際の、Controller/Table/Entity/View/Helper/フォーム/Vue・JS の具体的な書き換えパターン集。「プラグインを4から5へ移行」「admin_ メソッドを Controller/Admin へ」「public $belongsTo/$hasMany を initialize() へ」「ClassRegistry/TableRegistry」「find(all/first/list, 配列) をクエリビルダへ」「$this->Model（null）を fetchTable へ」「getControlSource の単数→複数形」「$this->Form を BcAdminForm・control() へ」「検索フォーム searches/→search/」「FormHelper::create 文字列モデル→null」「FormHelper::year()/month()/domId() 廃止」「Time::format の ICU パターン」「Number::format/Text::truncate の null 不可」「配列条件の IN 自動付与なし・null は IS」「Vue/JS の admin URL を $.bcUtil.adminBaseUrl へ＋webpack 再ビルド」「$this->data / $View->request がヘルパ誤ロードを誘発」「MissingTableClassException / MissingHelperException / No context provider found」「App::importでVendor同梱した外部ライブラリ(leafo/scssphp等)が5系に移植されておらず機能が死ぬ」「PHP8で削除されたeach()や正規表現の不正エスケープ等、古い同梱ライブラリのPHP8非互換」等、プラグインの画面・モデル・テンプレート・フロント表示のエラーを1つずつ潰す作業で参照する。本スキルは4系イディオムの検出と変換に絞り、5系での正しい書き方の正本は basercms5-plugin-development を参照する。サイト全体の4→5移行手順（インストール/DB移行/テーマ・プラグイン変換手順/Git運用）は basercms4-to-5-upgrade、テーマ（templates 中心）の移行は basercms-theme-4-to-5-upgrade、5.2→5.3 のプラグイン移行は basercms-plugin-5x-update、CakePHP本体起因は cakephp-migration、PHP本体起因は php-migration、テスト実行は basercms-unittest、ブログ等へのカスタムフィールド後付け系プラグインを bc-custom-content へ移行する場合は basercms5-custom-content-development スキルを参照。'
license: MIT
---

# baserCMS プラグイン内部コードの 4 → 5 移行パターン集

`BcAddonMigrator` でプラグインの雛形を5系へ変換した**後**に必要となる、手作業のコード書き換えパターンを症状別にまとめたもの。サイト全体の移行手順（baserCMS5 のインストール、`BcDbMigrator` でのデータ移行、テーマ変換、プラグインの変換手順＝ZIP化→`bc_addon_migrator`→配置、Git/リポジトリ運用）は **basercms4-to-5-upgrade** スキルを参照。本スキルはそこから呼ばれ、プラグインの Controller / Table / Entity / View / Helper / フォーム / Vue・JS を1画面ずつ通して動かすための具体策を提供する。

本スキルが扱うのは**4系イディオムの検出と変換**である。**5系の正しい書き方の正本は basercms5-plugin-development**（テーマは basercms5-theme-development）であり、変換先の仕様（ORM・コントローラ・フォーム・イベント・Vue連携・日付/数値・ルーティング）に迷ったらそちらを参照する。以下の各節は「4系イディオムの検出（症状・grep パターン）→ 変換の要点 → 正本スキルの該当節」の形で読む。

**ブログ記事等にカスタムフィールドを後付けする4系プラグインを5系標準の bc-custom-content へ移行する場合は、本スキルではなく basercms5-custom-content-development を参照する**（bc-custom-content は既存 blog_posts への後付けフィールド追加には使えず、独立したコンテンツ種別として作り直す設計になるため、通常のプラグイン内部コード変換とは別の専用パターン集が必要）。

> **推奨: 移行に着手する前に一度 `basercms5-claude-workflow-setup`（環境セットアップ）を参照し、進め方の環境（設計=superpowers brainstorming／権限整理=permissions-audit／その上での Auto mode／spec・plan の Markdown プレビュー）を整える。提案ベースで、整っていればスキップ。下記「移行の進め方」はその環境の上で回す。**

## 移行の進め方（最重要・最初にやる順序）
プラグインの 4→5 移行は、**画面を1枚ずつ場当たりで直す前に、まず横断で全体を片付ける**のが速くて安全。実証済みの推奨順序:

> **★★[必須ゲート] あるプラグインの5系化に着手したら、コードを1行でも触る前に、まずステップ1の「ファイル状態台帳」を作る。台帳が無いうちは静的監査(2)も構文変換(3)も始めない。** これは飛ばしやすい（監査や Table 変換にすぐ着手したくなる）が、台帳が無いと「どのファイルが未着手/見送りか」を俯瞰できず、進捗の抜け・二重作業・deferred の取りこぼしが起きる。台帳ファイルを作成 → プレビュー（`markdown-to-html`）→ それから 2 以降に進む。

1. **全ファイルの状態台帳化**: src/templates/js/migration を1行1ファイルで `未着手/移行中/移行済/見送り/対象外` 管理（種別×状態サマリ付き、生きたドキュメント）。どこが残っているか俯瞰できる。作成後は状態が変わるたびに更新する（例: `docs/migration/<plugin>-file-ledger.md`）。
2. **★横断コードチェック（静的監査）を最初に**: 全5系コードをアクション/メソッド単位で4系正本と突合し、`移行済 / deferred / 4系残骸 / 未実装` ＋バグ(深刻度)＋ブラウザ確認ポイントを監査ドキュメント化する。**テストを書く前**にやることで、残骸/即Fatal/設計判断が要る箇所を地図化でき手戻りが激減する。並列サブエージェントで種別/ドメイン別に分担すると速い（`basercms-unittest` の横断監査メモ参照）。
3. **★横断「構文だけ5系化」を次に**: 監査で出た4系残骸を5系構文へ一括変換（`$this->Model->`→fetchTable、`find('first',配列)`→builder、`getDataSource`→getConnection、Event の `bindModel`/`$event->data` 等。下記 C-0/C-A/§8 のカタログを適用）。**完了条件は「php -l 全クリーン＋4系API残骸grepゼロ(deferredのTODO除く)＋既存フルスイート回帰ゼロ」。この段では新規テストを書かない**。Fatal を一掃して「5系構文として成立」の土台を作る。外部依存(Slack/メール/CSV/Excel/集計)は中身を移さず `// TODO baserCMS5移行:` か `NotImplementedException` で deferred 明示。
4. **テスト＆ブラウザで意味検証**: 構文変換だけでは保証できない **entity↔配列・日付marshal・afterSave連鎖・FormProtection・view変数の形・JS連携(C-F2)** を、描画する統合テスト＋ブラウザ確認で詰めて `移行済` に上げる。`php -l` は構文しか見ず描画/JSの死は捕まらないのでこの段が必須。

> なぜ 2→3 を先にやるか: 構文残骸は「画面は開くが delete/ajax/CSV を押すと500」のように**呼ぶまで顕在化しない**。先に全部洗って構文を5系化しておけば、以後のテスト＆ブラウザは「動くはずの土台の上で意味を確認する」作業に集中でき、Fatal とロジック誤りが混ざらない。

> **★★[運用原則] 横断作業中に「対応イベント/対応APIが5系に無い」「再設計が要る」等で無理に実装しない判断をしたら、その場でコードに `// TODO baserCMS5移行: <理由>` を残すだけでなく、必ず1の「ファイル状態台帳」にも該当行を追記・更新する**（状態を「見送り(deferred)」にし、理由と代替案の要点を一言添える）。コード内TODOだけだと後で台帳を見ても分からず、逆に台帳だけだとコードを読む人に伝わらない——**両方に書いて初めて「無理な実装をせず記録する」運用が機能する**。判断に迷ったら実装を止めてこの記録に切り替えるのが正しい（無理に動かして壊すより安全）。実例: PopularBlogPost プラグインで、CakePHP5に無い `Model.afterFind`（一覧へのランキング注入）と廃止された `bindModel`（設定の動的結合）を deferred にした際、コードのTODOコメントと `docs/migration/popular-blog-post-file-ledger.md` の両方に理由（対応イベント無し・代替手段）を記録した。

## 横断対応の原則（同一原因の散在は一括で）
1画面の修正で見つけた不具合のうち、同じ原因がプラグイン全体に散在するものは、その場で**横断的に**一括対応する（1箇所だけ直して次画面で同じエラーに当たる、を繰り返さない）。手順: ①直したら同じパターンを `grep -rn` で全件洗い出す（例: `$this->Form->input(`、`'multiple' => 'checkbox'`、単数 `get('Sample.Sample...')`、`searches/`、`Time->format($x)` 第2引数なし 等）→ ②機械的に一意な変換は `perl -pi` で一括適用 → ③変更ファイルを全て `php -l` で検証 → ④非自明な箇所だけ個別対応。横断一括できる代表例は **C-0** にカタログ化（見つけ次第追記）。新しい横断パターンを見つけたら C-0 に追加してから一括実行する。

## 具体的なコード変換ルール

### 1. ファイル・フォルダ操作
- 検出: 4系 `File`/`Folder` クラス、および `Cake\Filesystem\File`/`Folder`（いずれも5系で使用不可）。
- 変換: baserCMS 固有の処理は `BaserCore\Utility\BcFile`/`BcFolder`、一般的なファイル操作は PHP 標準（`SplFileInfo`/`FilesystemIterator` 等）へ。共通ルールは basercms5-development を参照。

### 2. リクエスト処理 (`Controller`, `View`)
4系イディオム → 変換の要点（5系のリクエスト操作・redirect・例外・Component の正しい書き方は basercms5-plugin-development §3「コントローラ」を参照）:
- `$this->request->data/query/params`・配列アクセス（`query['key']`/`data['key']`）→ `getData('key')`/`getQuery('key')`/`getParam('key')`/`getQueryParams()`。
- `$this->setMessage('msg')`/`setMessage('msg', true)` → `$this->BcMessage->setSuccess('msg')`/`setError('msg')`。
- `$components` の `'BcAuth'`/`'Cookie'`/`'BcAuthConfigure'` は削除（空になったら定義ごと削除）。
- `$name`/`$uses`/`$subMenuElements`/`$crumbs` プロパティは削除。`array()` → `[]`。
- `$this->layout = null/false`・`$this->autoLayout = false` → `$this->viewBuilder()->disableAutoLayout()`。
- 文字列連結のURL組み立て（`Router::fullBaseUrl() . '/admin/...'`）→ 配列形式の `\Cake\Routing\Router::url([...], true)`。
- `$this->response->statusCode(400)` → `$this->setResponse($this->getResponse()->withStatus(400))`。
- getter への代入（`getData('key') = 'val'`／`getQuery('key') = 'val'`）→ request は immutable。`withData()`/`withQueryParams()` で新 request を作り `setRequest()`（§3）。
- `$this->ajaxError(500, 'msg')` → `throw new \Cake\Http\Exception\InternalErrorException('msg')` 等、適切な例外へ。
- `ClassRegistry::init()`・`$this->ModelName` 形式のモデル呼び出し → `fetchTable('PluginName.ModelNames')`（**複数形**必須。単数形は正しくロードされない）。Table/Lib 内は T-H。
- `App::uses()` は全削除、`App::import()` は `use` 文へ。`App::path('View', 'P')` → `\Cake\Core\Plugin::templatePath('P')`。
- メソッド戻り値への `isset()` は PHP 8 で不可 → `!== null` 比較へ。動的プロパティを使うクラスは `#[\AllowDynamicProperties]`（PHP 8.2）。
- `clearAllCache()` → `BcUtil::clearAllCache()`、`convertSize()` → `BcUtil::convertSize()`、`getEnablePlugins()` → `BcUtil::getEnablePlugins()`。`findExpanded()` 廃止 → `BcKeyValue` ビヘイビア＋`getKeyValue()`。`$useTable` 廃止 → `initialize()` の `setTable()`。
- `ConnectionManager::getDataSource('default')` → `ConnectionManager::get('default')`。
- 名前空間内のコアクラス参照は `\` 付与か `use` 文（`catch (Exception $e)` → `catch (\Exception $e)`）。
- `$this->request->url`（先頭スラッシュなし）→ `getPath()`（**先頭スラッシュあり**。文字列比較箇所はスラッシュ差を考慮）。`$this->request->base` → `getAttribute('base')`。`$this->viewPath` → `getTemplatePath()`。
- URL配列生成: `'plugin' => null` → `'BaserCore'`（コアプラグインは `Bc` 接頭辞: `blog`→`BcBlog`、`mail`→`BcMail`）、`'admin' => true` → `'prefix' => 'Admin'`（`false` はキーごと削除）、controller は CamelCase（特例: `search_indices` → `SearchIndexes`）。
- `is('ssl')` → `is('https')`。`$this->search = 'id'` → `setSearch('id')`。`$this->help = 'id'` → `setHelp('id')`。
- `$this->_checkSubmitToken()` は廃止 → 削除（CSRF 側で代替）。
- 名前付きパラメーター（`/action/name:value`・`$this->passedArgs['key']`）廃止 → クエリパラメーター（`getQuery('key')`。URL生成もクエリ文字列へ）。
- パスワード互換: 4系 `app/Config/install.php` の `Security.salt` を `.env` の `SECURITY_SALT` に設定。テーマ適用: 4系 `site_configs` の `theme` を5系 `sites.theme` へ（自動化困難なら手動）。

### 3. 日付・時刻
- `FrozenTime` → `Cake\I18n\DateTime` が推奨。ICU パターンと PHP `date()` 形式の使い分け・null 安全の正本は basercms5-plugin-development §7「日付・数値・文字列」を参照。

#### ★[最重要] datePicker の日付が保存されない（datetime カラムが NULL になる）
- 検出: 日付系カラムが**保存しても消える（NULL）**。原因は2つの組合せ: ①4系 `BcFormHelper::datePicker` は**スラッシュ形式**（例 `2026/06/28`）で送る、②5系 `DateTimeType::marshal` は `Y-m-d H:i:s` を要求し、スラッシュ形式もハイフンの「日付のみ」も NULL にする。逆に純 `date` カラム（`DateType`）は「時刻付き」を NULL にする——**カラム型で要求が真逆**なので、移行先が date か datetime かを migration の `SHOW CREATE TABLE` で必ず確認する。
- 変換の要点: 保存側（patchEntity の前）で正規化する。datetime 向けは `'/'→'-'`＋日付のみなら ` 00:00:00` 補完＋空は null、date 向けは `'/'→'-'`＋空は null（時刻は付けない）。プラグイン共通の Util に集約し、**1画面で気づいたら同種の datePicker 保存を全フォームへ横展開**する。編集画面の表示（GET）も add 画面の既定値と揃えてスラッシュ（`$v->format('Y/m/d')`＝PHP 形式）で統一する（保存側の正規化が両形式を受けるので安全）。
- 検証: 「スラッシュ日付を POST → `assertRedirect` → 再取得して日付値まで assert」の統合テストで固定する（**日付の値まで assert しないと silently NULL を見逃す**。basercms-unittest 参照）。
- **★別の罠**: `func()->min()/max()` を datetime カラムに使うと結果が数値型（`2026.0` のような float）になり、datetime カラムへの代入がマーシャリングで NULL になる。集計関数を使わず `orderBy()->first()` で最古/最新行を取る——正しい書き方は basercms5-plugin-development §2「ORM / Table / Entity」を参照。

### 4. データベース・モデル (`Table`, `Entity`)
- 4系 `Model` は `Table`（バリデーション・ロジック）と `Entity`（行の振る舞い）に分離する。
- `find('all', ['conditions' => ...])`（配列形式）→ クエリビルダ、`$this->save($data)`（配列）→ `newEntity()`/`patchEntity()` → `save($entity)`。
- `ClassRegistry::isKeySet()`/`getObject()` → `TableRegistry::getTableLocator()->get()`（インスタンス化も兼ねるので存在確認は不要）。
- クエリビルダ・保存・関連の5系の正しい書き方は basercms5-plugin-development §2「ORM / Table / Entity」を参照。

### 5. ビュー・ヘルパー (`View`)
- `$this->Helpers->loaded('name')` → `$this->helpers()->has('name')`。`CakePlugin::loaded('name')` → `\Cake\Core\Plugin::isLoaded('name')`。
- プラグイン内 element は `PluginName.element_name` 形式で指定。`BcException` は `use BaserCore\Error\BcException;` か完全修飾名で。
- `$this->BcBaser->siteConfig['name']` のようなプロパティ直接参照はヘルパメソッド/静的メソッド経由へ（テーマ側の同種パターンは basercms-theme-4-to-5-upgrade TH-1）。
- **`$helpers` 配列に紛れた不正なエントリ（メソッド名等の4系残骸。例: `'afterRender'`）は削除する**——5系ではヘルパ解決時に MissingHelperException になる（実例あり）。
- **ヘルパの public プロパティ直読み（`$this-><Helper>->settings[...]` 等）に依存するテンプレは、4系では「先に getter が呼ばれた副作用」で埋まっていたことがある**——5系化ではヘルパのコンストラクタで初期化して直読みでも空にならないようにする。リクエスト依存の設定（サイト別設定等）は bootstrap ではサイト判定できないため、ヘルパ側で `currentSite` 属性から読み分ける。

### 6. プラグイン・名前空間
- 全クラスに適切な `namespace` を付与する（例: `BcBlog\Controller`）。
- 外部プラグインは DB（plugins テーブル）で有効化されていれば自動読込される（composer 登録・`composer dump-autoload` 不要）。プラグインクラス・setting.php/config.php の5系作法は basercms5-plugin-development §1「新規開発の始め方」を参照。

### 7. テスト
- `FixtureManager`（配列定義）は廃止 → `FixtureFactory` へ書き換える。実行手順・基盤導入は basercms-unittest を参照。

## 8. イベント (`Event`) — リスナー(`src/Event/*EventListener.php`)の4→5（実証済み）
検出と変換の要点（5系イベントの基底クラス・命名規則・ハンドラ作法の正本は basercms5-plugin-development §5「イベント」を参照）:
- `$event->data['key']` → `getData('key')`、`$event->subject` → `getSubject()`（**Table が返る**。保存されたエンティティは `getData('entity')` で取る——4系の `$model->data` ではない）。書き込みは `setData('key', $v)`。
- ハンドラ名はイベント名の CamelCase（`User.beforeFind` → `userBeforeFind(EventInterface $event)`）。型ヒントは `\Cake\Event\Event` ではなく **`EventInterface`**。
- **★他プラグイン修飾のイベント名は4系のままだと「無発火（エラーも出ない）」**（実証済み）。Model系 `Blog.BlogPost.afterSave` → `BcBlog.BlogPosts.afterSave`（5系プラグイン名＋Table複数形）、View系 `Blog.Blog.afterRender` → `BcBlog.Blog.afterRender`、メール送信は `Mail.Mail.beforeSendMail` → **`BcMail.Mail.beforeSendEmail`**（イベント名自体も改名。from 等の送信オプションは `$event->setData('sendEmailOptions', [...])` で渡す——発火側 MailController で確認）。実在確認は発火側を `grep -rn "dispatchLayerEvent\|createEvent" vendor/baserproject/...`。**`Model.afterFind` はトリガー自体が5系に不在**——発明せず deferred（TODO＋台帳の両方に記録）。
- `bindModel()`/`unbindModel()` は5系廃止 → `contain([...])` を呼び出し側に足すか `initialize()` に宣言する方式へ。暫定は `return;`(noop)＋TODO で Fatal だけ回避。
- 4系 ORM 残骸（`find('first', 配列)`・`save(配列)`・`$model->data`）→ 5系化（§4／T-F）。
- request は immutable: `$view->request->data['x'] = ...` 等への代入は無効 → ビューへ渡すなら `$controller->set('x', $v)`、アクション名は `$view->getRequest()->getParam('action')`。
- `BcForm::input()` → `BcAdminForm::control()`。4系アクション名 `admin_add`/`admin_edit` → `add`/`edit`（プレフィックスは prefix で扱う）。redirect 配列の `'admin' => true` → `'prefix' => 'Admin'`（コントローラ/リスナー共通）。
- リスナーはプラグインの BcPlugin が **自動 attach** するため、壊れているとイベント発火で即 Fatal ＝横断で最優先に直す。

## Table / ORM レイヤーの移行パターン（BcAddonMigrator が変換しないため手作業必須）

> `BcAddonMigrator` は Model→Table のファイル移動・名前変更はするが、**クラス内の4系ORM記法はほぼ変換しない**。大規模プラグイン（例: Sample）では1テーブル数百行がまるごと4系のまま残る。下記を機械的に潰す。**まずテーブル群のアソシエーション宣言を直す**のが全ての前提（コントローラ/ヘルパー/テンプレートが依存するため）。

### T-A. アソシエーション宣言（最重要・最頻出）: `public $belongsTo/$hasMany/...` → `initialize()`
- 検出: 4系のクラスプロパティ宣言（`public $belongsTo`/`$hasMany`/`$hasAndBelongsToMany`）。5系では**完全に無視される**（エラーも出ず、ただ関連が存在しない＝`Undefined property / association` で落ちる）。
- 変換の要点: `initialize()` 内のメソッド呼び出しへ。**エイリアスは複数形**にする（`contain()`・結果配列キー・連鎖アクセスすべてに波及＝T-D 参照）。`className` はプラグイン接頭辞付き・複数形（コア User は `'BaserCore.Users'`）。HABTM → `belongsToMany`（`associationForeignKey` → `targetForeignKey`）。`dependent` は5系では `hasMany`/`hasOne` のみ有効（`belongsTo` の `dependent` は落とす）。
- 5系の宣言の正しい書き方（コード例含む）は basercms5-plugin-development §2「ORM / Table / Entity」を参照。

### T-B. `public $actsAs` → `$this->addBehavior()`（initialize内）
- 検出: `public $actsAs = ['BcUpload' => [...]]` 等。変換: `initialize()` で `$this->addBehavior('BaserCore.BcUpload', [...])`。`BcCache` は5系に無い場合が多いので削除。（basercms5-plugin-development §2 参照）

### T-C. `public $validate`（配列バリデーション）→ `validationDefault(Validator $validator)`
- 検出: 4系の `public $validate = [...]`（5系では無効）。変換: `validationDefault()` へ。`notBlank` → `notEmptyString('field', 'msg')`、カスタムルールは `add()` で登録しシグネチャを `($value, $context)` に変更。正しい書き方は basercms5-plugin-development §2 を参照。

### T-D. `public $name` と単数形エイリアスの連鎖アクセス
- `public $name = '...';` は削除。
- 関連テーブルへの直接アクセス（`$this-><Assoc>->...`）は、5系では関連経由ではなく `fetchTable('<Plugin>.<Assocs>')` で個別取得する（動的プロパティは存在しない）。深い連鎖も各テーブルを個別に取得。
- 結果配列アクセス `$result['<Model>']['field']` → エンティティ `$result->field`、関連は snake_case プロパティ（belongsTo 単数・hasMany 複数）。エンティティの参照作法は basercms5-plugin-development §2 を参照。

### T-E. コールバック
- `afterFind()` は**5系で廃止**。集計・加工は finder メソッドか呼び出し側の明示実行へ（C-B も参照）。
- `beforeSave/afterSave/beforeFind` はシグネチャ変更: `afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)`。`$this->data['<Model>']['x']` → `$entity->x`。（basercms5-plugin-development §2 参照）

### T-F. 検索・保存・生SQL・データソース
検出と変換の要点（クエリビルダ・IN/IS・innerJoinWith・BcKeyValue の正しい書き方は basercms5-plugin-development §2「ORM / Table / Entity」を参照）:
- `find('all'/'first'/'list', ['conditions'=>,'recursive'=>,'fields'=>,'order'=>,'joins'=>])` → クエリビルダ（list は `find('list', keyField:, valueField:)`）。
- `$this->create($data); $this->save();` → `newEntity($data)`＋`save($entity)`。`$this->id` → `$e->id`。
- `$this->save($array, ['callbacks'=>false,'validate'=>false])` → `save($entity, ['checkRules'=>false])`＋パッチ時に `validate:false`。**`callbacks` オプションは5系に存在しない**（T-J 参照）。
- `$this->query($sql)`（4系ネスト配列）→ `getConnection()->execute($sql)->fetchAll('assoc')`（戻りは**フラット連想配列**）。**生SQL内にハードコードされた旧テーブルプレフィックスは除去**（T-G）。可能ならクエリビルダへ。
- `getDataSource()`＋`$db->begin()/commit()/rollback()` → `getConnection()->begin()/commit()/rollback()`。
- `reduceAssociations()`/`unbindModel()`/`bindModel()`、baserCMS 2系独自メソッド（`createArrayForJoin()` 等）は廃止 → `contain`/`matching`/`innerJoinWith` で書き換え。
- **配列値に IN は自動付与されない**: 4系は `['field' => [1,2]]` を自動 IN 化したが、5系は `InvalidArgumentException: Cannot convert value Array ... to int` になる → `'field IN' => (array)$values` と明示。空配列の IN は例外なので空ガード。**null 一致は `IS` 演算子**。**関連テーブルの列で絞るなら `innerJoinWith`/`matching`＋複数形エイリアス**（`contain` は WHERE に使えない）。
- **未移行の基底クラス**: Table が4系基底（`extends <Plugin>AppModel`）のままだと `Class "...AppModel" not found`。移行済み共通基底（`AppTable` 継承）へ。`grep -rn "extends <Plugin>AppModel\b"` で一括検出。共通基底の共有メソッドを使う Table が `AppTable` を**直接**継承していると `BadMethodCallException: Unknown method ...`（ORM の `__call` 経由）→ `extends` を共通基底に揃える。
- **ヘルパ内の単数テーブル参照・4系 find**: `grep -rnE "get\('<Plugin>\.[A-Za-z]+[^s']'\)"` で全ヘルパを洗い、複数形＋クエリビルダへ。
- **KVS（name/value）テーブル**: 4系マジックフィンダー `findByName($key)`（戻りを `$row['<Model>']['value']` で扱う）は、5系では Query が返るため配列前提コードが壊れる → `find()->where(['name'=>$key])->first()->value`。保存は `BcKeyValue` ビヘイビア＋`saveKeyValue()`（§2）。
- 4系の `users.user_group_id` 列は5系に存在しない → T-K 参照。

### T-H. Table / Lib 内では `fetchTable()` は使えない（コントローラ専用）
- 検出: Table・Lib・サービス内の `$this->fetchTable(...)` → `BadMethodCallException: Unknown method 'fetchTable'`。変換: `\Cake\ORM\TableRegistry::getTableLocator()->get('<Plugin>.<Models>')`。（basercms5-plugin-development §2 参照）

### T-I. `getControlSource()`（フォーム選択肢生成）の4系→5系
- 検出: ①Table に残る4系の中身（`$this->Assoc->find('all', recursive)`・`$row['<Model>']['field']`・`unbindModel`・`createArrayForJoin`・`$loginUser['X']['y']`）。②テンプレ/ヘルパからの**単数モデル名**呼び出し → `MissingTableClassException: Table class for alias '<Plugin>.<Model>' could not be found`。フォームテンプレ・検索 element に散在しがちなので `grep "getControlSource('<Plugin>.<Model>[^s]."` で一括検出。
- 変換の要点: 中身は TableRegistry＋クエリビルダ・エンティティ参照へ、絞り込みは `contain`/`matching`/`innerJoinWith` へ（複雑なものは一旦 TODO で全件返しにして表示優先も可）。呼び出しは **`Plugin.複数形モデル.field`** に直す。5系の正しい定義・呼び出し方は basercms5-plugin-development §2「ORM / Table / Entity」を参照。

### T-J. 集計（SUM/集約）メソッドと afterSave の5系化（実証済みパターン）
4系の集計 Model メソッド・afterSave を移植する際の検出と要点。**ユニットテストで4系と同値を固定しながら**進める（basercms-unittest）。5系の正しい書き方（`func()->sum`・datetime の MIN/MAX 回避・再入ガード・削除の cascade）は basercms5-plugin-development §2「ORM / Table / Entity」を参照。
- 4系 `find('first', ['fields'=>['SUM(x) AS total'], 'conditions'=>...])` → `func()->sum`＋`->first()->alias ?? 0`（§2）。4系が int キャストしていたら踏襲（decimal は数値文字列で返る）。
- **空配列の `IN` は例外**（`IN ()` を生成して throw。4系は黙って0件）→ 集計対象IDが空になりうるメソッドは**先頭で空ガード**して 0 を返す。
- `MIN()/MAX()`（特に datetime）は集計関数を使わず `orderBy()->first()` で代替（§2）。
- **集計オーケストレーションの戻り値は4系の配列形状を維持**: 消費側が `Hash::extract($rows, '{n}.<Model>')` のままなら、各行を `['<Model>' => エンティティのtoArray()＋集計値]` で組み立てて返す。配列入力のみで完結する下位メソッドは無改修で再利用できる。
- **`virtualFields`（旧 afterFind 計算値）は5系に無い** → 計算を明示メソッドに集約し、find 後に各行へ適用してから合算する。
- afterSave の一括再保存は5系シグネチャ＋**再入ガードフラグ**へ（§2）。4系の `$this->data` 参照・`saveAll`・`recursive` を置換する。
- **4系 `$Model->delete($id, true)` の従属削除は、5系の `deleteAll()` では再現しない**（ORM の cascade を通さず子が孤児として残る）→ `get($id)` → `delete($entity)` に置き換える（§2）。置換保存のテストでは「子も消えたこと」を assert すると回帰を防げる。
- **コピーの芋づる複製**: 子 copy の戻り値は5系では `EntityInterface|false`（配列でない）。親側は `->id`＋真偽判定で使う。トランザクションは `getConnection()->begin()/commit()/rollback()`。
- **コールバック本体を後続へ遅延するときは「黙って空スタブ」にしない**: 5系シグネチャに替えつつ本体未実装なら、TODO コメント＋「現状スタブ(無処理)であること」を固定する tripwire テストを1本置く（将来の実装時に落ちて気づける）。
- **★[最重要] 5系の `save()` に `callbacks` オプションは無い（4系の `['callbacks'=>false]` は黙って無視され afterSave が常に発火）**: 相互に save し合う afterSave が**無限ループ**する。`grep -rn "'callbacks'" src/` で4系由来の無効オプションを洗い出し、再入ガードフラグへ置き換える（ガードの書き方は §2）。
- **4系の複数テーブル生SELECT（`SELECT A.*, B.*`）は5系 `fetchAll('assoc')` でフラット化**され、同名カラム（`id`/`name` 等）が後勝ちで衝突する（同じ flat 配列を両キーに入れると値が混ざる）→ ORM で取り直すか、SELECT に列別名（`A.id AS A__id`）を付けて再構築する。

### T-K. ユーザー↔ユニット(グループ)は 5系 BTM。テストで本番に無い列を捏造しない
- 検出: baser5 の `users` に `user_group_id` 列は**無い**（user↔group は `users_user_groups` の多対多）。4系の `belongsTo`/`hasMany`（`foreignKey:'user_group_id'`）は壊れる。
- 変換: `belongsToMany`＋`joinTable:'users_user_groups'` に再設計し、絞り込みは `innerJoinWith`＋`->groupBy(['<起点>.id'])`（1ユーザー複数グループ所属の重複回避）。宣言の作法は basercms5-plugin-development §2。
- **[最重要の戒め] テストを通すために本番に存在しないカラムを migration で足してはいけない**（非互換の隠蔽になる）。テスト用スキーマは本番の `SHOW CREATE TABLE` に忠実に作る。

### NumberHelper::currency() は null 不可
- 検出: nullable な値を `$this->Number->currency()`/`format()` に渡す4系テンプレ → 5系では TypeError。変換: `?? 0` でガード。詳細は basercms5-plugin-development §7「日付・数値・文字列」を参照。

### NumberHelper の独自フォーマット（`Number::addFormat('yen', ...)`）は廃止 → `format()` へ
- 検出: 4系 bootstrap の `CakeNumber::addFormat(...)` 登録＋テンプレの `currency($v, '<独自名>')`。5系に `addFormat` は無く、未知コードは接頭辞としてそのまま出力される（「YEN 550,000」のような表示になる）。
- 変換: `currency($v, '<独自名>', $opts)` → `format($v, $opts)`（一括置換の正規表現は C-0 表を参照）。4系の旧オプション（誤綴り `thounsands`・`negative` 等）は無害に無視される。テンプレ側が別途通貨単位を付けている箇所はそのまま成立する。5系の数値フォーマットの正本は basercms5-plugin-development §7。

### T-運用. 大規模プラグインはテーブル層を「宣言だけ先に一括」変換すると安全
テーブルが数十枚ある大規模プラグイン（例: Sample は約30テーブル・7000行超）は、(1) **まず T-A〜T-D（アソシエーション/ビヘイビア/バリデーション/`$name`削除）の“宣言”だけを全テーブル一括で `initialize()`/`validationDefault()` 化**してモデル層をロード可能にし、(2) メソッド本体の `find()/query()/連鎖アクセス` 等は各行に `// TODO baserCMS5移行:` マーカーを付けて残し、後続の**画面通し工程**で実際に呼ばれた箇所だけ確実に直す、の2段構えが安全・効率的。宣言とメソッド本体を同時に直すと業務ロジックを壊しやすい。宣言変換は機械的なので、ファイル単位で並列実行（1エージェント=1テーブル、`php -l` で自己検証）すると速い。

### T-G. テーブル名プレフィックスの前提変更
4系は `mysite_` 等のテーブルプレフィックスを使っていることがある（`SHOW TABLES` で確認）。**5系（標準インストール）はプレフィックス無し**。生SQL・`joinTable`・`setTable()` でプレフィックスをハードコードしている箇所をすべて無印に直す。データ移行で `mysite_<plugin>_x` → `<plugin>_x` のように作成する場合も同様の方針（プレフィックス除去）に従う。

---

## Controller / 画面通し（管理画面）レイヤーの移行パターン

> テーブル層の基盤（T-A〜T-G）を固めた後、画面（コントローラ＋テンプレート＋関連element＋Lib＋依存プラグイン）を1枚ずつ通して潰す工程。1画面が広範囲に波及する（実例: Sample プロジェクト管理 index = コントローラ + テーブルの calcBalance + SampleUtil(Lib) + index_row/index_list テンプレート + 別プラグイン(SampleDep) 依存）。エラーをブラウザで1つずつ追って潰すのが確実。
>
> **画面結合フェーズの定石（テーブル層完了後・ドメイン単位で実証済み）**: ブラウザ手動より先に **ログイン付きコントローラ統合テスト**（`basercms-unittest` 参照）を各コントローラに1本立て、(1) GET で index/edit/add が描画200に到達することで移行漏れを自動検知 →(2) テンプレの `Form->create('Model')`文字列 / 素の `$this->Form->` / ネスト配列アクセス `$row['Model']['x']` を潰す →(3) コントローラの `passedArgs`/`recursive`/`reduceAssociations`/`find('first'|'all'|'list',配列)`/`field()`/`delete($id,true)`/単数形`$this->Model` を5系化 →(4) POST フロー（add/edit/delete・ajax確定）で DB 変化を assert。delete は全画面共通で `get($id)`→`delete($entity)`＋存在しないIDは `try/catch RecordNotFoundException` で4系の graceful 分岐（「無効な処理です。」→index）を再現。設定ビュー変数の欠落は AppController::beforeRender で横断解消（下表 `set(Configure::read(...))` の行）。**注意: 描画テストの 200 OK は `Undefined variable` 等の warning を握り潰す**ので、テスト後に `tests/TestApp/logs/error.log` を grep して pristine を確認すること。

### C-0. 機械的に一括変換できるパターン（プラグイン全体へ先行一括適用すると効率的）
画面を1枚ずつ通す前に、**構文を壊さない・意味が一意に定まる**変換はプラグイン全体へ `perl -pi` で先に当てておくと往復が減る（各変換後に必ず `php -l` で全変更ファイルを検証）。Sample プラグインでの実績（テンプレ＋src）:
| 対象 | 変換 | 備考 |
|---|---|---|
| `->Form->input(` | → `->Form->control(` | CakePHP5 で `input()` 廃止。ヘルパは据え置き（`$this->Form`のまま）。値バインドのため BcAdminForm に寄せるかは画面ごと判断 |
| `->element('admin/...')` | → `->element('...')` | 5系は `templates/Admin/element/` 配下なので `admin/` 接頭辞不要。`js()/css()` の `Sample.admin/...` アセットは触らない |
| `getControlSource('Sample.SampleProject.` 等の**単数**モデル | → `Sample.SampleProjects.`（複数） | `Sample.SamplePractice.`→`SamplePractices.`、`Sample.SampleProduct.`→`SampleProducts.`。単数は MissingTableClass。**`control('SampleProject.field')` のフォームdataキーは単数のまま変えない**（getControlSource の第1引数だけ） |
| `currency($v, 'yen', $opts)` | → `format($v, $opts)` | `Number::addFormat` 廃止。正規表現は値内の `()` を含むため `->currency\((.*?),\s*'yen'(\s*,\s*\[[^\]]*\])?\s*\)`→`->format($1$2)` |
| `getRequest()->action` | → `getRequest()->getParam('action')` | マジックプロパティ廃止 |
| `getRequest()->query`（配列用途） | → `getRequest()->getQueryParams()` | 単一キーは `getQuery('k')`。**getterへの代入** `getQuery('x') = ...` は別途 `withQueryParams()` 化（Fatal） |
| 検索フォーム `searches/`（複数） | → `search/`（単数）へ**移動** | setSearch が読むのは `search/`。**移動だけでなく中身も横断変換**: `$this->Form->`→`$this->BcAdminForm->`（`SampleForm`/`BcAdminForm` は対象外）、`create('Model', [...])`→`create(null, [...])`。重複（移行済み）ファイルは破棄 |
| `Time->format($x)`（第2引数なし／`'Y-m-d'`） | → `Time->format($x, 'yyyy-MM-dd')` | 第2引数省略は**日時**表示（`2026-01-01 00:00:00`）。日付のみは ICU `'yyyy-MM-dd'`（PHP date形式ではない）。`Time->format\(([^,)]+)\)`→`Time->format($1, 'yyyy-MM-dd')` |
| `control(['type'=>'select','multiple'=>'checkbox',...])` | → checkbox ループ（C-G参照） | baser5 で崩れる（空select/`< class="">`）。`grep -rn "'multiple' => 'checkbox'"` で全件洗い、options をループして `control('field[]', type=checkbox)` に。値（OPTS）が箇所毎に異なるので一括 perl ではなく個別 Edit 推奨 |
| 単数テーブル `get('Sample.Sample<単数>')`（src/ヘルパ含む） | → 複数形 `get('Sample.Sample<単数>s')` | Helper/Table/Controller 全 src。`grep -rnE "get\('(Sample\|SampleDep)\.[A-Za-z]+[^s']'\)"` |
| `$this->Form->create('Model', ...)`（文字列モデル） | → `$this->BcAdminForm->create(null, [..., 'valueSources'=>['data','context']])` | フォーム/編集テンプレ全般。文字列モデルは `No context provider found for value of type string`（CakeException）。`create\('[A-Za-z]+',`→`create(null,`。`$this->Form->`→`$this->BcAdminForm->` も併せて |
| `$this->action`（テンプレ）／`'admin_xxx'` | → `$this->getRequest()->getParam('action')`／`'xxx'` | View の `$this->action` 廃止。5系は prefix=Admin で **action名に `admin_` は付かない**（`admin_add`→`add`、`admin_index`→`index`）。getControlSource の mode 判定や create の action分岐に影響 |
| `$this->BcAuth->user()` | → `\BaserCore\Utility\BcUtil::loginUser()` | 戻りは `UserInterface\|false`（**未ログイン時は `null` ではなく `false`**）。**`?->` は `false` に効かない**ため `BcUtil::loginUser()?->id` は未ログイン時に `Attempt to read property "id" on bool` 警告＋null になる（テスト実行時に顕在化）。堅牢形は `(\BaserCore\Utility\BcUtil::loginUser() ?: null)?->id`（`false ?: null`→null→`null?->id`）か明示分岐 `$u = BcUtil::loginUser(); $u ? $u->id : null;`。`$user['id']` 等の配列アクセスはエンティティ参照へ |
| `getData()(...)`（二重括弧） | → `getData(...)` | 4系→5系の機械変換ミスで `$this->getRequest()->getData()('Model.x')` のように `()` が二重になっている箇所がテンプレに残る。`sed 's/getData()(/getData(/g'` で一括 |
| `ConnectionManager::get('default')` + `$db->begin/commit/rollback` | → `$table->getConnection()->begin/commit/rollback` | トランザクションは対象テーブルの接続から |
| `$this->Form->year('Model.field', ...)` / `->month(...)` | → `control('Model.field.year', ['type'=>'select','options'=>$years,'label'=>false])` 等 | **`FormHelper::year()/month()` は CakePHP5 で廃止**。`$years`/`$months` をテンプレ冒頭で自前生成。`getData('Model.field')` は `['year'=>,'month'=>]` で受かる |
| `$this->postConditions($data)`（コントローラ） | → 検索条件を明示的に組み立て | **`Controller::postConditions()` は5系廃止**。`Call to undefined method`。`if(!empty($d['Model']['x'])) $conditions['Models.x']=...` を手書き |
| `$this->redirect(...)` に `return` が無い（コントローラ） | → `return $this->redirect(...)` | **4系の redirect は exit したが5系は Response を返すだけで後続コードが実行され続ける**。ガード節（引数チェック→リダイレクト）が効かず、直後の処理で TypeError（null 引数）等になる。`perl -pi -e 's/^(\s+)\$this->redirect\(/$1return \$this->redirect(/'` で一括。★例外: `initialize()`/`beforeFilter()` 等 **`: void` 宣言メソッド内は return すると TypeError** になるため、一括後に void メソッド内に混入していないか確認する |
| `$this->set(Configure::read('Sample'))` の欠落 | → **プラグイン Admin AppController の `beforeRender()` で1回 set ＋ 各コントローラがそれを継承** | テンプレが参照する設定ビュー変数（`$billingStatuses`/`$taxRateList`/`$saleTypes`/`$estimateTypes` 等）の `Undefined variable`。4系は **プラグイン AppController::beforeRender が全画面に自動 set** していた。★5系の落とし穴: `bin/cake bake` 由来や手移植の Admin コントローラは **`BaserCore\Controller\Admin\BcAdminAppController` を直継承**しがちで、プラグインの `SampleAppController`（=4系の set を持つべき層）を経由しない→設定ビュー変数が全画面で欠落。**正しい直し方は per-action set ではなく、`src/Controller/Admin/SampleAppController.php` に `public function beforeRender(EventInterface $event): void { parent::beforeRender($event); $this->set(\Cake\Core\Configure::read('Sample')); }` を置き、各 Admin コントローラを `extends SampleAppController` にする**（per-action の重複 set は削除）。横断的に一発で解消できる。`Configure::read('Sample')` の値自体は `BcPlugin::bootstrap()`/`config/setting.php` で load 済みか確認（未 load なら C-D で復元） |
| `$this->Xxx = new XxxHelper(new View());`（4系のヘルパー直接生成） | → `$this->Xxx = $this->loadHelper('Plugin.Xxx');` | **CakePHP2 のヘルパー直接 `new` は5系で無効**（`Class "XxxHelper" not found` — クラス自体はプラグイン名前空間下に実在するが、`new` では名前空間解決できず、Viewの通常のヘルパーロード規約（`loadHelper()`）を通す必要がある）。実例: `MailHelper`（`BcMail\View\Helper\MailHelper`）をテンプレート内で `new MailHelper(new View())` していた箇所が `/lp_maintenance/` 等で 500 になっていた。`grep -rn "new [A-Z][A-Za-z]*Helper(" テンプレ全体` で横断検出できる |

注意: `'div'`/`'between'`/`'after'` 等 input 専用の旧オプションは control では HTML属性に漏れることがあるが Fatal にはならない（画面通し時に個別清掃）。`searches/`→`search/`（C-G）はディレクトリ移動＋各フォームの他4系記法も伴うので一括ではなく画面ごとに行う。

### C-A. コントローラの4系イディオム
検出と変換の要点（paginate・fetchTable・redirect・Component・delete 定石の5系の正しい書き方は basercms5-plugin-development §3「コントローラ」を参照）:
- `$this->ModelName`（4系の自動モデルプロパティ）→ `fetchTable('<Plugin>.<Models>')`（複数形）。連鎖アクセスは各テーブルを個別に fetchTable。
- `$this->siteConfigs['admin_list_num']` → `\BaserCore\Utility\BcSiteConfig::get('admin_list_num')`。
- `$this->passedArgs`（名前付き引数）廃止 → 並べ替えは Paginator のクエリ文字列で自動処理、その他は `getQuery()`。`$this->RequestHandler->isAjax()`/`$this->params['url']['ajax']` → `is('ajax')`。
- **ServerRequest のマジックプロパティは廃止**: `->action` → `getParam('action')`、`->query` → `getQueryParams()`（単一キーは `getQuery('key')`）、`->data` → `getData()`。テンプレも同様。一括置換可。
- **リクエストは不変（immutable）**: getter への代入残骸（`getQuery('x') = ...`／`->query['k']=...`）は `Can't use method return value in write context` の **Fatal（php -l で検出可）** → `withQueryParams()`/`withData()` で新 request を `setRequest()`（§3）。
- 4系 `$this->paginate = [配列]; $this->paginate('Model')` → クエリビルダを組んで `$this->paginate($query)`（§3）。`recursive`/`unbindModel` → `contain([...])`、`joins` → `leftJoinWith`/`innerJoinWith`/`matching`、`group`/`having` → `groupBy()`/`having()`、条件キーは**複数形エイリアス**。
- `getDataSource()`→`buildStatement([...])` 等の4系クエリ生成は廃止 → クエリビルダ、合計などは「条件に合致するIDを取得→`func()->sum()`」で再実装（§2）。
- **[重要] protected ヘルパーの移動漏れ**: admin_index が呼ぶ protected メソッド（検索条件生成等）は `BcAddonMigrator` が Admin コントローラに移動しないことがある（フロント側に残る or どこにも無い）。4系の元実装を Admin コントローラに移植し、複数形エイリアス・クエリビルダ・旧プレフィックス除去で5系化する。
- **コンポーネントは自動ロードされない**: 4系 `public $components = [...]` は5系で消える → 使用するコントローラの `initialize()` で `loadComponent()`。無いと該当プロパティが null/未定義で Fatal。
- **delete アクション**: 4系の id 直渡し `$this->Model->delete($id)` は廃止 → 5系の定石（`allowMethod` → `get($id)` の try/catch → `delete($entity)`＋`unlockedActions` 登録）は §3 を参照。
- **横断の「残骸＝呼ぶと即 Fatal」パターン**: 大規模プラグインは `index/add/edit` だけ移行され、`delete`/`ajax_*`/CSV/Excel/メール/通知系が4系のまま放置されがち（**画面は開くが操作で500**）。①静的チェックで全残骸を列挙 → ②「構文だけ5系化」を一括（`php -l`＋既存テスト回帰ゼロが完了条件。新規テストは書かない）→ ③テスト＆ブラウザで意味検証の順（「移行の進め方」参照）。外部依存は `// TODO baserCMS5移行:` か `NotImplementedException` で deferred を明示（放置すると呼んだとき黙って Fatal する）。

### C-A2. 廃止コンポーネント（PaginatorComponent / RequestHandlerComponent 等）
- 検出: `$this->loadComponent("Paginator")` → `MissingComponentException: PaginatorComponent could not be found`（CakePHP 5 で廃止）。
- 変換: `initialize()` から削除し `$this->paginate($query)` を直接使う（§3）。`RequestHandlerComponent` も廃止（`is('ajax')` 等で代替）。`initialize()` の `loadComponent()` を点検する。

### C-A3. カスタム Component の initialize シグネチャ
- 検出: 4系 `public function initialize(Controller $controller)` → 5系と非互換で **Fatal**（`Declaration ... must be compatible`）。
- 変換: `initialize(array $config): void`＋コントローラ参照は `$this->getController()`（型ヒント `Controller` の未 import による誤解決にも注意）。5系 Component の作法は basercms5-plugin-development §3 を参照。

### C-B. afterFind 由来の計算値はコントローラで明示セット
旧 `afterFind` の行ごとの計算は5系で自動実行されない → Table に計算メソッドを用意し、paginate 後にループして各エンティティへセットする（basercms5-plugin-development §3「コントローラ」参照）。paginate の `fields` で関連カラムを平坦化していた場合も、ループ内で関連プロパティから展開する。

### C-C. テンプレート（element 含む）の半変換状態に注意
`BcAddonMigrator` 後のテンプレートは**エンティティアクセスと4系配列アクセスが混在**することが多い。検出と変換の要点（フォーム・表示の5系の正しい書き方は basercms5-plugin-development §4「管理画面フォーム・一覧・検索」・§7「日付・数値・文字列」を参照）:
- `Hash::get($data, 'Model.field')`（第3引数のデフォルト値付き含む）→ `$data->field`。関連アクセス `$data['Assoc']` → snake_case プロパティ（belongsTo は単数形・hasMany は複数形）。null 安全に `?->`/`?? ''` を併用。
- グローバル Lib クラスの静的呼び出しは名前空間付きへ（`\<Plugin>\Lib\<Util>::method()`）。
- **Lib 静的メソッドは「4系配列」と「エンティティ」両方で呼ばれる**ことがある（一覧テンプレはエンティティ、編集フォームは4系ネスト配列）。片方前提に直すと他方で `Attempt to read property "x" on array` になる → 冒頭に両対応の正規化を置く: `$p = isset($x['<Model>']) ? $x['<Model>'] : $x;` 以後 `$p['field'] ?? null` で参照（エンティティは ArrayAccess なので両対応になる）。日付を `strtotime()` 等へ渡すときは `(string)` キャスト。
- **`$this->Form->input(...)` は CakePHP5 で廃止** → `control(...)`。BcAdminForm で開いたフォーム内の element は**同じインスタンスの `BcAdminForm->control()`** に統一する（`$this->Form` は別インスタンスでコンテキストを共有しない）。
- **element パスの `admin/` 接頭辞は不要**: `エレメントテンプレート「admin/...」が見つかりませんでした` → `element('foo/bar')` に直す（CSS/JS のアセットパスは別物なので触らない）。
- **空配列の `IN ()` は例外**: `Impossible to generate condition with empty list of values for field (...)`（4系は黙って0件）→ 空ガード（§2）。
- リンク配列のコントローラ/プラグイン名は **CamelCase**（未マッチは `MissingRouteException`）。
- **View での `$View->request`／`$this->data` プロパティ参照は「ヘルパ自動ロード」を誘発**: 5系 View に該当プロパティは無く、`<Plugin>.requestHelper could not be found`／`<Plugin>.dataHelper could not be found`（MissingHelperException）になる。**全フォームで発火するイベントリスナ内に残っていると無関係な画面まで巻き込む**ので最優先で5系化: `$view->getRequest()->getParam(...)`／`$this->getRequest()->getData('Model.x')`。`grep -rn '\$this->data\b'` で一掃。ガードの controller 比較は CamelCase・action は `admin_` なし。
- **コントローラ生SQL `fetchAll('assoc')` はフラット連想配列**＝4系ネスト形（`$row[0]['total']`/`$row['<Model>']['x']`）前提のテンプレで**値が空・合計0**になる → フラット参照に直す。合計0は `($v/$total)*100` の `DivisionByZeroError` を誘発するのでガード。SQL の別名は平易名で出す。
- **`Time->format()` は ICU パターン＋第2引数省略は日時**: `'Y-m-d'`/`'Y/m/d'` と書くと月が分(0)になる（`2026-0-31` 様の表示）。**一覧表示（TimeHelper=ICU）と編集フォームの整形（エンティティの `format()`=PHP date 形式）は別物**——症状がどちらの経路かで直し方が違う。正しい使い分け・null 安全は §7 を参照。
- **`Text::truncate()`/`Number::format()` は第1引数 null 不可（TypeError）** → `(string)` キャスト／`?? 0`（§7）。金額・備考系テンプレに多発するので grep で一括。
- コントローラが4系ネスト配列で `set()` したビュー変数は、テンプレ側も配列アクセスに揃える（エンティティ前提のままだと `Attempt to read property "x" on array`）。
- `css()`/`js()` の第2引数は basercms-theme-4-to-5-upgrade の F-14（`['inline'=>false]` → `false`）。

### C-D. setting.php の欠落（設定キー＝Division by zero／adminNavigation＝メニュー/UI変化）
`BcAddonMigrator` での変換や、その後の「配列の整理」作業で、`config/setting.php` の内容が**間引かれる**ことがある。2系統に注意し、必ず4系 `Config/setting.php` と照合して復元する:
- **設定キーの欠落**: コードが参照するキー（例 `Sample.operatingDays`/`productTypes`/`practiceTypes`/`monthlyUnitPricePartner`/`mitsumoriKessai`）が無いと `Division by zero`・`array_merge(): Argument #2 must be of type array, null given`・未定義参照になる。**画面ごとに1つずつ潰さず、横断的に洗い出す**: 参照キー `grep -rohE "Configure::read\('Sample\.[a-zA-Z0-9_]+" templates/ src/ | sed "s/.*Sample\.//" | sort -u` と 定義キー `grep -oE "'[a-zA-Z0-9_]+'\s*=>" config/setting.php | …` を `comm -23` で突合し、未定義キーを4系 `Config/setting.php` から一括復元する。
- **adminNavigation（管理メニュー）の欠落・改変**: メニュー項目が落ちる/直リンク化されると**4系と管理画面のメニュー構成（UI）が変わる**。例: 4系は「分析」メニュー（`sample_menus/analysis`、`currentRegex` で `sample_aggregate`/`sample_units` 等を内包）から集計へ遷移する仕様なのに、変換後 setting.php では「集計」が直リンク化され「分析」や他メニュー（経理/工数管理/マスター等）が消えている、等。**UI を勝手に変えない**方針なら、adminNavigation も4系と突き合わせて元の構成に戻す（メニューのURLは4系の小文字表記 `'controller' => 'sample_projects'` のままで baserCMS5 admin が解決する）。未移行画面へのリンクは押すとエラーになるが、画面移行を進めるにつれ解消する。
- **★4系 setting.php が「実行型」（ロード時に DB や他プラグインのクラスへアクセスして値を組み立てる）の場合、5系の `return 配列` へ単純変換できない**——検出: setting.php 内の `ClassRegistry`/モデル呼び出し/他プラグインの Util クラス参照。変換: **静的な設定は setting.php の return 配列、動的な組み立ては `<Plugin>Plugin::bootstrap()`（try/catch ガード付き）へ分離**する（正本: basercms5-plugin-development §1「新規開発の始め方」）。

### C-F. Vue/JS から叩く ajax は戻り値を Response にし、URL を5系管理パスへ
検出と変換の要点（ajax の Response・adminBaseUrl・webpack 再ビルドの5系の正しい書き方は basercms5-plugin-development §6「Vue / jQuery 連携」を参照）:
- **ajax アクションの戻り値**: 4系 `$this->autoRender=false; return json_encode(...)` → 5系は文字列 return では何も出力されない → JSON の Response を返す（§6）。
- **JS/バンドル内のハードコード管理URL（4系 `/admin/...`）は 403/404 になる**（サブディレクトリ・prefix 変更で壊れる）。配置固定パスのハードコードも禁止 → `$.bcUtil.adminBaseUrl` に連結する（§6）。別ベースの前置（`$.baseUrl + '/admin/...'`）は前置ごと置換して二重ベースを除去。Vue2 のテンプレート式からはグローバル `$` を参照できないので computed 経由（§6）。
- **自プラグイン以外の `/admin/<plugin>/` も対象**: `grep -rn "['\"]/admin/" webroot/js/src webroot/js/admin` で**全ハードコードURLを横断的に洗い**、1画面で気づいたら全 Vue/JS をまとめて変換 → **必ず webpack で再ビルド**（§6。bundle への反映を grep で確認、ブラウザは強制リロード）。
- **バンドルが stale** だと `[Vue warn] Property "xxx" is not defined`（ソースには有るのに bundle に無い）等が出る → `.bundle.js` を手パッチせず、ソース修正 → 再ビルド（§6）。
- **Vue prop の型不一致**: `Invalid prop: Expected String, got Number` → `[String, Number]` の複数型許容へ（ソース修正 → 再ビルド）。
- **`Expected Object/Array, got String with value "R"/"u"...` が1文字ずつ大量に出る＝ajax が壊れている兆候**: サーバ側アクションの4系残骸（`$this->ModelName`＝null・`query()`）で Fatal になり、JSON でなくエラーHTML/文字列が返って `v-for` が文字単位で反復している。原因は Vue ではなくサーバ側の未移行 → エンドポイントを5系化し、Vue が参照するフィールドのフラット配列で返す（§6）。
- Table メソッド内の4系流の手動ヘルパー生成は名前空間付きへ（`new \BaserCore\View\Helper\BcTextHelper(new \Cake\View\View())`）。
- CSV 出力等「画面表示に使わない」メソッドの4系残骸は呼ばれる段で個別移行する（放置すると実行時に落ちる）。

### C-F2. ★バンドルJSが参照する DOM id / name が5系の生成規則と食い違う（描画は通るのに JS 連携が静かに死ぬ）
4系前提の JS/バンドルは**4系の DOM 命名**で DOM を掴むため、セレクタが何もマッチせず**イベント・自動入力・計算が無言で動かなくなる**（PHP エラーも JS エラーも出ないことが多く、画面は正常に見える）。
- **2つの食い違い（両方確認する）**: ① id — 4系 CamelCase（`#ModelField`）⇔ 5系 `control()` は**小文字ハイフン**（モデル別名＋フィールド）。② name — 4系 `data[Model][field]` ⇔ 5系 `Model[field]`（`data[...]` プレフィックス無し）。5系の id/name 生成規則の正本は basercms5-plugin-development §4「管理画面フォーム・一覧・検索」。例外: 自前で明示 id を振るヘルパ製フィールドは CamelCase のまま——同一フォームで両者が混在し気づきにくい。
- **調査法（画面移行時に必ず1回やる）**: JS が掴む id/name を `grep -roE "#[A-Z][A-Za-z]+|name=['\"]?data\[[A-Za-z]+\]" webroot/js/src` で列挙し、ログイン付き統合テストで実レンダリング HTML の id/name と突合する。**描画200・PHPテスト緑ではこの不整合は検出できない**（JS 実行はしないため）——手動ブラウザ確認か「期待 id/name が HTML に実在する」assert（§4 の番人パターン）で締める。
- **直し方（原則は JS を5系生成 DOM に寄せる）**: 推奨は JS 側を5系の id/name に合わせて webpack 再ビルド。代替はテンプレ control に明示 `id` で4系 CamelCase を復元（再ビルド不要だが、label の `for` 不一致・CSS/他JS の旧 id 参照・同一画面での id 重複に注意。**name は POST キーなので変えない**）。明示 id の作法は §4。
- **★Vue が動的生成するフォーム行（明細等）の POST が `Unexpected field '...' in POST data`（BadRequestException）で落ちる**: 5系の FormProtection が、描画時に DOM に無いフィールドを弾く（4系は SecurityComponent 未使用で素通りしていた挙動）。**`Allowed memory size exhausted` として現れることがある**——巨大 POST の例外トレース描画が重いだけで、真因はスタックの `Unexpected field` の方。直し方は2点セット（Vue の `:name` を5系形式 `'Model[' + i + '][field]'` へ＋対象アクションを `unlockedActions` に登録。§6 参照）。4系の `data[...]` 付き name は弾かれなくても `getData()` で拾えず**保存されない**。
- **検証**: 「明細を含む POST で `assertRedirect`＋明細が実際に保存される」統合テストで固定（basercms-unittest）。CSRF は別ミドルウェアなので unlockedActions では無効化されない。

### C-G. 検索フォーム（setSearch）と生SQLのカスタムページネーション
検出と変換の要点（検索フォーム・control()・GET 送信・valueSources・カスタムページネーションの5系の正しい書き方は basercms5-plugin-development §4「管理画面フォーム・一覧・検索」を参照）:
- **検索フォームの配置**: 4系/変換後の **`searches/`（複数）** のままだと `エレメントテンプレート search/xxx が見つかりませんでした`（5系 `setSearch()` は `search/`（単数）を読む）。**全画面で再発する＝横断対応の典型**: ①`searches/*.php` 全件に `$this->Form->`→`$this->BcAdminForm->`・`create('Model',…)`→`create(null,…)` を一括適用 → ②`search/`（単数）へ全移動（`git mv`）→ ③移行済みファイルは `searches/` 側を破棄 → ④空の `searches/` を削除 → ⑤全件 `php -l`。
- **文字列モデルの `create('Model', ...)`** は `No context provider found for value of type string` → コンテキストレス `create(null, [...])` へ。GET 検索・入力値の再表示（`valueSources` に `query`）・`control()` 必須の規約は §4。
- 4系の `control(['multiple'=>'checkbox'])`/`multiCheckbox()` は**崩れる**（空 select や `< class="">`）→ `grep -rn "'multiple' => 'checkbox'"` で全件洗い、options ループの個別 control（§4）へ。受け側の条件は `'field IN' => (array)$values`（T-F）。
- 自作フォームヘルパが `BcForm` を使っていると管理画面で崩れる → `BcAdminForm` へ（§4）。
- **`FormHelper::domId()` は CakePHP5 で廃止**（`Call to undefined method`）→ `\Cake\Utility\Inflector::camelize(str_replace('.', '_', $fieldName))` で再現し、JS が参照する id と合わせるため control に `'id'` を明示する。
- **4系 JS の CamelCase id セレクタは5系の小文字ハイフン id と不一致**でハンドラが無反応になる（検索ボタンが無く change 自動 submit のみの画面では「絞り込みを変えても何も起きない」）→ フォーム委譲で id 依存をなくすか明示 id（§4）。
- 一覧の一括処理/一括選択（`ListTool.batch`/`checkall`）・`submit()`/`button()` も `BcAdminForm` に統一（フィールド名は既存 JS が参照するので変えない）。`control()` の label に HTML を渡すときは配列形式＋`escape: false`（§4）。
- **`$this->FormTable` は `MissingHelperException`**（未ロードヘルパ名として現在プラグインに探しにいく）→ 5系の登録名は `BcFormTable`。`dispatchAfterForm()` は `BcAdminForm`（§4）。
- **HABTM 保存（4系 `saveAll`）→ `_ids`**（§2）。GET 表示は `contain()`＋選択 ID 抽出＋checkbox ループ。
- **生SQLのカスタムページネーション**: 4系の `paginate()`/`paginateCount()` オーバーライドは5系で呼ばれない（廃止）→ コントローラで総件数＋当ページ行を自前取得し `PaginatedResultSet` をビュー変数に set する（§4。sort はホワイトリストで ORDER BY を組み立て）。**`SELECT *` 結合の UNION をサブクエリで包むと `SQLSTATE[42S21] Duplicate column name`** — 総件数は UNION SQL を実行して数え、`ORDER BY ... LIMIT` は**末尾へ直接付与**する。テンプレは4系ネスト形 → `fetchAll('assoc')` のフラット形へ。

### C-I. 編集/登録フォーム画面（edit/add）の共通移行パターン
4系の edit/add（`read(null,$id)`/`find('first',...)` → `set()` → `save()`、テンプレは4系ネスト配列前提）の移行定型:
- **GET（表示）**: エンティティを取得 → **4系互換ネスト配列へ整形する `convert*ToFormData()`** を用意し `withParsedBody()` でセット（belongsTo=単数 snake・hasMany=複数 snake を4系キーへマップ）。日付/日時オブジェクトはそのまま渡すと入力欄に時分秒付きで出るため、convert 時に文字列化する（表示形式の統一は §3「日付・時刻」の datePicker 節）。
- **POST（保存）**: 4系の整形メソッド（`convert*FormToDb` 等）はそのまま流用可 → `get($id)` or `newEmptyEntity()` → `patchEntity()` → `save()` → `return $this->redirect([...])`。
- **テンプレ**: `create('Model')` → `create(null, ['valueSources'=>['data','context']])`、`Form->value('Model.x')` → `getData('Model.x')`、`pass[1]` → `getParam('pass')[1] ?? null`、`$this->Form->` → `$this->BcAdminForm->`（C-0 参照。フォームの5系の正しい書き方は basercms5-plugin-development §4）。
- 未移行の深い副作用（afterSave の関連集計・通知コンポーネント・4系専用メソッド）は表示・基本保存を優先して TODO マーカーで保留（使う段で個別移行）。
- **【落とし穴】カスタムバリデーションルールは「保存しないと走らない」**: ルール本体が4系 ORM のままだと GET 表示は通るのに **POST 保存時に Fatal**。GET だけ確認して「OK」としない。ルール本体を5系化する（§2）。`$context['data']['id']` 等は防御的に取得。
- **既存の移行済みメソッドを再利用する**: 4系の複雑なクエリを再移行せず、既に5系化済みの同等メソッドに置き換えると安全・速い。
- **編集画面に同居する「関連レコード一覧」**: 本体だけ `withParsedBody()` にすると関連一覧が**エラーなく空**で描画されない（気づきにくい）→ コントローラで関連を別途取得して `set()` し、テンプレをエンティティ参照＋null ガードに直す。
- `BcBaser->link()` に画像/HTML を渡すときは `['escape' => false]`（無いと生テキスト表示）。4系の管理画面アセット画像（`admin/btn_*.png` 等）は5系に存在せず404 → bca-btn-icon の作法へ置換（いずれも §4）。

### C-J. `getControlSource` 等で `disableHydration` を使うとフラット配列で返る
`disableHydration()` の結果行は**フラット連想配列**（`$row['Table']['field']` ではない）→ select に明示エイリアスを付けてフラット参照する（basercms5-plugin-development §2 参照）。関連列で WHERE 絞り込みしつつ関連無しも含めたいときは `leftJoinWith()`（`contain` は WHERE 不可）。

### C-H. 一覧 element をフォームに埋め込むと PaginatorHelper が落ちる
- 検出: 一覧用 element（`Paginator->sort()`/`element('pagination')`/`list_num`）をページネーションしない画面（編集フォーム等）に流用すると `You must set a pagination instance using setPaginated() first`（4系は黙って描画したが、5系はビュー変数に `PaginatedInterface` が無いと throw）。
- 変換: ページャ系の描画を「専用一覧コントローラのときだけ」にガードする（basercms5-plugin-development §4 参照）。埋め込み専用 element も同様にガード。
- **【最重要の落とし穴】`getParam('controller')`/`params['controller']` は CamelCase**: 4系変換残りの snake_case 比較（`=== '<snake_case名>'`）は**常に false** になり、一覧 element がフォーム埋め込み側の分岐に落ちて、**画面はエラーなく開くのにデータが出ない**（合計だけ出てリストが空、等）→ CamelCase 比較へ。`grep -rn "=== '<plugin接頭辞>_" templates` で全件洗う。

### C-E. 依存プラグインも芋づる式に必要
`contain()` する関連の所属プラグイン（例 `<PluginA>`→`<PluginB>`）が無効だと `Table class for alias '<PluginB>.<PluginB>Xxx' could not be found`。**依存プラグインを有効化（status=1）し、テーブルを4系から作成**する。依存プラグイン側のイベントリスナー等が未移行だと warning が出るが、対象画面の表示自体は止まらないことが多い（依存プラグイン本体の移行時に対応）。

## フロント表示エラーの実例パターン（症状 → 原因 → 修正）

> テーマ／プラグインのフロント表示を復旧させる際に頻出する実例。`BcAddonMigrator` で自動変換した後も残る典型的な不具合。**エラーメッセージで検索して該当パターンに当てる**こと。1つ直すと次のエラーが現れる「玉ねぎ剝き」になるため、`curl -sk -o /dev/null -w "%{http_code}" <URL>` でステータスを見ながら1件ずつ潰す。

> テンプレート・テーマ描画系の F-パターン（F-1〜F-5, F-9, F-11〜F-17, F-19, F-20）は **basercms-theme-4-to-5-upgrade** スキルへ移動した。

### F-6. プラグインの `config/routes.php` が CLI を巻き込んで全コマンドを壊す
`$request = \Cake\Routing\Router::getRequest();` は **CLI 実行時に null** を返すため、直後の `$request->getPath()` で `Error: Call to a member function getPath() on null` となり、`bin/cake` が一切動かなくなる（ルート読込は CLI でも走るため）。また `Router::connect()` は **CakePHP 5 で廃止**。
- **修正**: 先頭で `if (!$request) return;` のnullガードを入れ、ルート定義は RouteBuilder の `$routes->connect(...)` に変更する（plugin の routes.php には `$routes` がスコープに渡る）。コントローラー名は CamelCase（`onemind_files` → `OnemindFiles`）。

### F-7. `MissingRouteException: /files/... could not be found`（アップロードファイル未移行）
`BcDbMigrator`／データ復元はコア系の `files/` サブディレクトリ（blog・contents・editor・mail・theme_configs・uploads 等）しか作らないことがあり、**プラグイン独自のアップロードディレクトリ（例: `onemind_configs`）が欠落**する。物理ファイルが無いと `/files/...` がルートにフォールバックして例外になる。
- **修正**: 4系の `files/`（または `app/webroot/files/`）全体を `v5/webroot/files/` に**マージコピー**する。`rsync -a files/ v5/webroot/files/`。
- **★cp のネスト罠**: `v5/webroot/files` は復元時点で一部生成済みのため、`cp -a files v5/webroot/files` だと **`files/files/` にネスト**する。`cp` を使うなら `cp -a files/. v5/webroot/files/`（末尾 `/.`）。
- 実体ファイルが無い間は、アップロード系ヘルパがフォールバックで**ルート相対 `/files/...`（サブディレクトリ無し）**の URL を出すことがあり、移行後に正しい `/v5/files/...` に戻る（URL 生成側のバグと誤診しない）。

### F-8. `Call to undefined method`（`BcAddonMigrator` のメソッド移植漏れ）
`BcAddonMigrator` は Helper 等のクラスを変換するが、**一部メソッドが移植されずに欠落**することがある（例: `isTopMainVisualUseBanner()` `inPlugin()` `react()`）。テンプレートから呼ばれて `Call to undefined method` になる。
- **修正**: 4系と5系のクラスのメソッド一覧を `grep -nE "function "` で**差分比較**し、欠落メソッドを5系記法に直して手動移植する。
    *   `getEnablePlugins()`（グローバル関数）→ `\BaserCore\Utility\BcUtil::getEnablePlugins()`。戻り値は **Plugin エンティティの配列**なので、`Hash::extract($plugins, '{n}.name')` で名称を取り出す（4系の `'{n}.Plugin.name'` ではない）。

### F-10. ショートコードが実行されず `[Plugin.method ...]` の生テキストのまま残る
ショートコードは各プラグインの `config/setting.php` の `'BcShortCode' => [...]` で登録されるが、**プラグインが無効（plugins.status=0）だと setting.php が読み込まれず未登録**になる。BcShortCode は未登録のショートコードを**エラーを出さずそのまま生テキストで出力**するため、画面に `[<Plugin>.methodName]` 等が露出する。
- **判別**: 露出しているショートコードの接頭辞（`[Plugin.xxx]` の Plugin 部分）のプラグインの status を確認。
- **修正**: 当該プラグインを移行・有効化する（F-5参照）。未移行のうちは生テキストのまま残るのは想定どおり。

### F-22. 4系プラグインが `Vendor/` にバンドルした外部ライブラリ（`App::import('Vendor', ...)`）が5系に移植されておらず、機能が丸ごと死ぬ（PHP8非互換の追加修正も必要）
CakePHP2時代は composer が一般的でなく、外部ライブラリ（例: `leafo/scssphp`）をプラグイン内 `Vendor/<lib>/` にソースごと同梱し、`App::import('Vendor', '<Plugin>.<lib>/<bootstrap>')` で読み込むパターンが多かった。`BcAddonMigrator` はプラグインの Controller/Model/View 等は変換するが、**この種の同梱外部ライブラリ（`Vendor/` 配下）は変換・移植の対象外**でそのまま置き去りになる。該当機能を呼ぶと `class_exists()` が false のまま失敗（例外・警告がログに出るだけで機能停止）し、気づかれにくい。
- **判別**: 4系ソース側で `grep -rn "App::import('Vendor'" app/Plugin/<Plugin>/` し、対応する `Vendor/<lib>/` ディレクトリの有無を確認。5系側で該当機能を実行してエラーログ（`class '...' not found` 等）が出ないか確認する。
- **修正方針は2通り**:
  1. **（推奨）composer で現行の後継パッケージを導入**（例: `leafo/scssphp` → `scssphp/scssphp`。同じ作者がメンテ移管したフォークで、クラス名前空間が変わる（`Leafo\ScssPhp` → `ScssPhp\ScssPhp`）ことが多いので、呼び出し側で両対応の `class_exists()` 分岐を書く）。
  2. **（バンドル版をそのまま移植する場合）** 4系の `Vendor/<lib>/` ソース一式を、5系プラグインの `vendor_bundled/<lib>/`（**`vendor/` という名前は避ける** — baserCMS5系標準`.gitignore`が `/plugins/*/vendor/` をプラグイン配下の依存物として無視するため、追跡させたいなら別名にする）へそのままコピーし、旧来の手動 `include_once` ブートストラップ（例 `scss.inc.php`）を利用側コードから `require_once` する。**この方法を選ぶ場合、旧ライブラリ自体が古いPHPバージョン（5.x〜7.x）を前提にしていることが多く、PHP8で動かすには追加のパッチが必要**になる。実際に踏んだ非互換の型（両方とも実行して初めて発覚し、`php -l` の構文チェックだけでは検出できない実行時エラー）:
     - **PHP8で削除された `each()` 関数の呼び出し**（`Call to undefined function ...\each()`）。`list($k,) = each($arr)` は `array_key_first($arr)`（キーだけ必要な場合）等、用途に応じた代替に書き換える。
     - **正規表現の文字クラス内に無効なエスケープシーケンス**（`preg_match(): Compilation failed: escape sequence is invalid in character class`）。全角¥記号や `\A`（本来は「文字列先頭」を意図した誤字・OCR崩れ等）が `[...]` の中に混入していると、PHP7以前のPCREは黙って許容するが、PHP8のPCRE2は構文エラーで例外を投げる。`preg_match`/`preg_replace` は失敗すると `null` を返すため、後続処理が壊れた出力（NULL連結等）を生成する。文字クラスでなく `(?:^|\s)`（マッチ判定）や `(?<=\s)`（固定長lookbehind）等、意図に合わせて書き直す。
  - どちらの方法でも、**実際にその機能を動かすテスト用コマンド（`Cake\Command\Command` を継承した一時的な `bin/cake` コマンド等）を書いて実行結果を目視確認する**こと。`php -l` の構文チェックだけでは、この種の実行時のみ顕在化する非互換（`each()`・不正な正規表現・削除された関数等）を検出できない。確認後、一時コマンドは削除する。

### F-21. URL文字列からの `str_replace` ドメイン抽出がサブディレクトリ設置で壊れ「全リクエスト404」になる
4系コード（EventListener の startup ガード等）が `str_replace('/', '', str_replace('https://', '', $url))` のような文字列加工でドメインを取り出して `HTTP_HOST` と比較していると、サブディレクトリ設置（SITE_URL=`https://host/subdir/`）では「hostsubdir」になり**恒常的に不一致＝プラグイン有効化した瞬間に全ページ404**（例外を投げるガードの場合）になる（実測）。
- 検出: `grep -rn "str_replace.*https\|HTTP_HOST" src/`。
- 修正: `parse_url($url, PHP_URL_HOST)` でホストのみ比較する（4系の意図＝ドメイン一致判定に忠実）。

### F-18. 横長テーブル（多数列）で「ページ全体が横スクロール」する＝flex の `min-width:0` 欠落
管理テーマ bc-admin-third のレイアウトは `.bca-container { display:flex }`＋`.bca-main { flex-basis:100% }` だが **`.bca-main` に `min-width:0` が無い**。flex アイテムの既定 `min-width:auto` のため、内容（多数列のワイドなテーブル等）より縮まず `.bca-main` が内容幅まで伸び、**ページ全体に横スクロールが出る**（中央寄せの保存ボタンが右へずれる、等）。子要素に `overflow:auto` を付けても、親 flex アイテムが伸びるため効かない。
- **症状の出方**: 4系では出なかった／データが入って初めて顕在化（移行直後はデータ未取得で列が無く気づかない）。月別カラムが現在日付基準で増える画面（工数シミュレーション等）で再現しやすい。
- **修正（コアテーマ非改変）**: その画面のCSS（Vueバンドルの非scoped `<style>` 等）に **`.bca-main { min-width: 0; }`** を足し、テーブルを**スクロールラッパー**で囲って `overflow:auto !important; max-width:100% !important; width:100%` を与える。これで `.bca-main` が画面幅に収まり、テーブルはラッパー内で横スクロール（`position:sticky` の列固定も機能する）。CSSを直したら **webpack 再ビルド**（バンドルCSSは `MiniCssExtractPlugin` で `../css/[name].bundle.css` に出力）。

## テーマ（Theme）の移行パターン（別スキル）
テーマ（templates/layout・element・Blog/Pages/Mail テンプレート）固有の移行パターン（TH-系）と、フロント表示エラーのうちテンプレート・テーマ描画系のパターン（F-1〜F-5, F-9, F-11〜F-17, F-19, F-20）は **basercms-theme-4-to-5-upgrade** スキルに分割した。テーマの作業ではそちらを参照すること。
