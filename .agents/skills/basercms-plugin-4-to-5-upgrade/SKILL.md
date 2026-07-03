---
name: basercms-plugin-4-to-5-upgrade
description: 'baserCMS 4 (CakePHP 2ベース) のプラグイン内部コードを baserCMS 5 (CakePHP 5ベース) へ移行する際の、Controller/Table/Entity/View/Helper/フォーム/Vue・JS の具体的な書き換えパターン集。**テーマ（Controller/Tableを持たずtemplates/layout・element・Blog・Pages・Mail＋少数Helperで構成）の移行もこのスキルの対象**（末尾「テーマ（Theme）固有の移行パターン」TH-系: `$this->BcBaser->siteConfig[...]`廃止・`fullUrl()`廃止・4系専用Componentの直接インスタンス化・`blogPosts()`等の廃止ショートカットメソッド・他プラグインHelper依存の切り分け 等）。「プラグインを4から5へ移行」「admin_ メソッドを Controller/Admin へ」「public $belongsTo/$hasMany を initialize() へ」「ClassRegistry/TableRegistry」「find(all/first/list, 配列) をクエリビルダへ」「$this->Model（null）を fetchTable へ」「getControlSource の単数→複数形」「$this->Form を BcAdminForm・control() へ」「検索フォーム searches/→search/」「FormHelper::create 文字列モデル→null」「FormHelper::year()/month()/domId() 廃止」「Time::format の ICU パターン」「Number::format/Text::truncate の null 不可」「配列条件の IN 自動付与なし・null は IS」「Vue/JS の admin URL を $.bcUtil.adminBaseUrl へ＋webpack 再ビルド」「$this->data / $View->request がヘルパ誤ロードを誘発」「MissingTableClassException / MissingHelperException / No context provider found」等、プラグインの画面・モデル・テンプレート・フロント表示のエラーを1つずつ潰す作業で参照する。サイト全体の4→5移行手順（インストール/DB移行/テーマ・プラグイン変換手順/Git運用）は basercms4-to-5-upgrade、5.2→5.3 のプラグイン移行は basercms-plugin-5x-update、CakePHP本体起因は cakephp-migration、PHP本体起因は php-migration、テスト実行は basercms-unittest スキルを参照。'
license: MIT
---

# baserCMS プラグイン内部コードの 4 → 5 移行パターン集

`BcAddonMigrator` でプラグインの雛形を5系へ変換した**後**に必要となる、手作業のコード書き換えパターンを症状別にまとめたもの。サイト全体の移行手順（baserCMS5 のインストール、`BcDbMigrator` でのデータ移行、テーマ変換、プラグインの変換手順＝ZIP化→`bc_addon_migrator`→配置、Git/リポジトリ運用）は **basercms4-to-5-upgrade** スキルを参照。本スキルはそこから呼ばれ、プラグインの Controller / Table / Entity / View / Helper / フォーム / Vue・JS を1画面ずつ通して動かすための具体策を提供する。

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
1画面の修正で見つけた不具合のうち、同じ原因がプラグイン全体に散在するものは、その場で**横断的に**一括対応する（1箇所だけ直して次画面で同じエラーに当たる、を繰り返さない）。手順: ①直したら同じパターンを `grep -rn` で全件洗い出す（例: `$this->Form->input(`、`'multiple' => 'checkbox'`、単数 `get('Cpm.Cpm...')`、`searches/`、`Time->format($x)` 第2引数なし 等）→ ②機械的に一意な変換は `perl -pi` で一括適用 → ③変更ファイルを全て `php -l` で検証 → ④非自明な箇所だけ個別対応。横断一括できる代表例は **C-0** にカタログ化（見つけ次第追記）。新しい横断パターンを見つけたら C-0 に追加してから一括実行する。

## 具体的なコード変換ルール

### 1. ファイル・フォルダ操作

*   **禁止**: `File`, `Folder` クラス (CakePHP 2), `Cake\Filesystem\Folder`, `Cake\Filesystem\File` クラス（CakePHP 5）
*   **推奨**:
    *   baserCMS固有の処理: `BaserCore\Utility\BcFile`, `BaserCore\Utility\BcFolder`
    *   一般的なファイル操作: PHP標準の `SplFileInfo`, `FilesystemIterator` など

### 2. リクエスト処理 (`Controller`, `View`)

*   **禁止**: `$this->request->data`, `$this->request->query`, `$this->params` への直接アクセス
*   **必須**:
    *   `$this->getRequest()->getData('key')`
    *   `$this->getRequest()->getQuery('key')`
    *   `$this->getRequest()->getParam('key')`
*   **フラッシュメッセージ**:
    *   × `$this->setMessage('message')`
    *   × `$this->setMessage('message', false)`
    *   ○ `$this->BcMessage->setSuccess('message')`
    *   × `$this->setMessage('message', true)`
    *   ○ `$this->BcMessage->setError('message')`
*   **認証コンポーネントの削除**:
    *   `$components` から `'BcAuth'`, `'Cookie'`, `'BcAuthConfigure'` を削除する（`BcAdminAppController` 継承により不要、またはフロントでは不要なため）
    *   上記削除により `$components` が空になった場合は定義ごと削除する
*   **不要なプロパティの削除**:
    *   `$name`, `$uses`, `$subMenuElements`, `$crumbs` は不要なため削除する
*   **配列記法の変更**:
    *   `array()` は `[]` （短縮構文）に変更する
*   **レイアウトの指定変更**:
    *   `$this->layout = null` / `false` → `$this->viewBuilder()->disableAutoLayout()`
    *   `$this->autoLayout = false` → `$this->viewBuilder()->disableAutoLayout()`
*   **ルーティングの指定**:
    *   文字列連結によるURL組み立て（`Router::fullBaseUrl() . '/admin/...'`）は避け、配列形式の `Router::url()` を使用する。
    *   ○ `\Cake\Routing\Router::url(['prefix' => 'Admin', 'plugin' => 'Cpm', 'controller' => 'CpmSales', 'action' => 'edit', $id], true)`
*   **レスポンスステータスの変更**:
    *   `$this->response->statusCode(400)` → `$this->setResponse($this->getResponse()->withStatus(400))`
*   **リクエストデータの更新**:
    *   `$this->getRequest()->getData('key') = 'val'` → `$this->setRequest($this->getRequest()->withData('key', 'val'))`
*   **ajaxErrorの変更**:
    *   `$this->ajaxError(500, 'message')` → `throw new \Cake\Http\Exception\InternalErrorException('message')`
    *   その他のコードは適切な例外クラス（`BadRequestException`, `NotFoundException` など）を使用する
*   **モデルのロード**:
    *   `ClassRegistry::init()` は廃止。`TableRegistry::getTableLocator()->get('PluginName.ModelName')` または `fetchTable('PluginName.ModelName')`を使用する。
    *   **重要**: テーブル読み込み時の名称は**複数形** (`PluginName.ModelNames`) を使用してください。単数形 (`PluginName.ModelName`) では正しくロードされない場合があります。
    *   `$this->ModelName` 形式のモデル呼び出しは不可。`fetchTable()` を使用する。
*   **名前空間・クラス読み込み**:
    *   `App::uses()` は全て削除する
    *   `App::import()` は全て削除し、必要に応じて `use` 文に置き換える
*   **Appクラスの代替**:
    *   `App::path('View', 'PluginName')` → `\Cake\Core\Plugin::templatePath('PluginName')`
*   **PHP 8 互換性 (isset)**:
    *   メソッドの戻り値に対する `isset()` は使用不可。
    *   × `isset($this->getRequest()->getData('key'))`
    *   ○ `$this->getRequest()->getData('key') !== null`
*   **PHP 8.2 互換性 (動的プロパティ)**:
    *   動的プロパティを使用するクラスには `#[\AllowDynamicProperties]` 属性を付与する。
    *   例: 継承元のコントローラーに存在しないモデルを `$this->Model = $this->fetchTable(...)` でロードする場合など。
*   **キャッシュクリア**:
    *   `clearAllCache()` は `BcUtil::clearAllCache()` に変更。
    *   `convertSize()` (テーブルクラス等のメソッド) は `BcUtil::convertSize()` に変更。引数は推奨通りそのまま渡す。
    *   `getEnablePlugins()` は `BcUtil::getEnablePlugins()` に変更。
    *   `findExpanded()` は廃止。`initialze()` で `$this->addBehavior('BaserCore.BcKeyValue');` をロードした上で `getKeyValue()` を使用する。
    *   `$useTable` プロパティは廃止。`initialize()` で `$this->setTable('table_name');` を使用する。
*   **定数**:
*   **データベース接続**:
    *   `ConnectionManager::getDataSource('default')` → `ConnectionManager::get('default')`
*   **グローバルクラスの指定**:
    *   名前空間内での標準クラスや名前空間なしで参照できないコアクラス（`Router`, `Configure`, `Hash`, `TableRegistry` 等）利用時は、必ず `\` を付けるか `use` 文を追加する。
    *   × `catch (Exception $e)` → ○ `catch (\Exception $e)`
    *   × `Router::url(...)` → ○ `\Cake\Routing\Router::url(...)` または `use Cake\Routing\Router;`
*   **配列アクセスの禁止**:
    *   × `$this->getRequest()->query['key']`
    *   × `$this->getRequest()->data['key']`
    *   ○ `$this->getRequest()->getQuery('key')`
    *   ○ `$this->getRequest()->getData('key')`
*   **URLの取得**:
    *   `$this->request->url` は廃止。`$this->getRequest()->getPath()` を使用する。
    *   **重要**: `getPath()` は、4系の `$request->url` と異なり、**先頭にスラッシュ** (`/`) が付与されます。条件分岐などで文字列比較を行っている箇所は、スラッシュの有無を考慮して修正してください。
*   **Base URLの取得**:
    *   `$this->request->base` は廃止。`$this->getRequest()->getAttribute('base')` を使用する。
*   **URL配列生成ルールの変更**:
    *   `['plugin' => null, 'admin' => false, 'controller' => 'search_indices', 'action' => 'search']` のような配列形式のURL指定において、以下の変更が必要です。
    *   **Plugin**: `null` の場合は `'BaserCore'` を指定。`null` でない場合はキャメルケース (例: `blog` -> `Blog`)。
        *   **注意**: コアプラグインの場合は、先頭に `Bc` を付与する (例: `blog` -> `BcBlog`, `mail` -> `BcMail`)。
    *   **Prefix**: `'admin' => true` は `'prefix' => 'Admin'` に変更。`'admin' => false` は削除（キーを含めない）。
    *   **Controller**: キャメルケースにする (例: `blog_posts` -> `BlogPosts`)。
        *   特例: `search_indices` は `SearchIndexes` に変更。
    *   **例**:
        *   OLD: `['plugin' => null, 'admin' => false, 'controller' => 'search_indices', 'action' => 'search']`
        *   NEW: `['plugin' => 'BaserCore', 'controller' => 'SearchIndexes', 'action' => 'search']`
*   **Viewパスの取得**:
    *   `$this->viewPath` は廃止。`$this->getTemplatePath()` を使用する。
    *   **注意**: 処理ロジックで使用している場合は、その意図を確認し、適切なメソッドに置き換えてください。
*   **SSL判定**:
    *   × `$this->request->is('ssl')`
    *   ○ `$this->getRequest()->is('https')`
*   **検索インデックス設定**:
    *   × `$this->search = 'id'`
    *   ○ `$this->setSearch('id')`
*   **ヘルプ設定**:
    *   × `$this->help = 'id'`
    *   ○ `$this->setHelp('id')`
*   **_checkSubmitTokenの削除**:
    *   `$this->_checkSubmitToken()` は廃止されたため削除する（CakePHP標準のCsrfComponent等で代替されるため、コードレベルでの呼び出しは不要）。
*   **イミュータブルなクエリ操作**:
    *   × `$this->getRequest()->getQuery('key') = 'value'`
    *   ○ `$query = $this->getRequest()->getQueryParams(); $query['key'] = 'value'; $this->setRequest($this->getRequest()->withQueryParams($query));`
*   **名前付きパラメーター (Named Parameters) の廃止**:
    *   CakePHP 4/5 では名前付きパラメーター（例: `/controller/action/name:value`）は廃止されました。クエリパラメーター（例: `/controller/action?name=value`）を使用してください。
    *   × `$this->passedArgs['key']`
    *   ○ `$this->getRequest()->getQuery('key')`
    *   URL生成時も、名前付き引数ではなく `?` (クエリ文字列) として渡す形式に変更する。
*   **パスワード互換性**:
    *   4系から5系への移行時、既存のパスワードハッシュを有効にするため、`app/Config/install.php` の `Security.salt` の値を `.env` の `SECURITY_SALT` に設定する。
*   **テーマ適用の修正**:
    *   4系の `site_configs` テーブルの `theme` の値を、5系の `sites` テーブルの `theme` カラムに適用する。
    *   自動化が困難な場合は、手動作業として実施する。

### 3. 日付・時刻

*   **変更**: CakePHP 4 の `FrozenTime` は、CakePHP 5 では `Cake\I18n\DateTime` が推奨されます（または `FrozenTime` も互換性のため残っている場合がありますが、最新の型定義に従うこと）。
*   **フォーマット**: `i18nFormat()` などのメソッドシグネチャを確認すること。

#### ★[最重要] datePicker の日付が保存されない（datetime カラムが NULL になる）
症状: 見積の作成日/受注日/請求予定日、請求の billing_date/recorded、入金 due_date、売上 sale_date などが**保存しても消える（NULL）**。原因は2つの組合せ:
1. **`BcFormHelper::datePicker` はスラッシュ形式(`yy/mm/dd` 例 `2026/06/28`)で送る**（ヘルパが値を `str_replace('-','/')` して表示し、jQuery UI もスラッシュ）。
2. **CakePHP5 の `DateTimeType::marshal` は「日付のみ(`Y-m-d`)」も NULL にし、`Y-m-d H:i:s` を要求する**。スラッシュ形式はもちろん、ハイフンの日付のみでも NULL になる（実測: `2026/06/28`→NULL、`2026-06-28`→NULL、`2026-06-28 00:00:00`→OK）。
   - **逆に純 `date` カラム(`DateType`)は「時刻付き」を NULL にする**（`2026-06-28`→OK、`2026-06-28 00:00:00`→NULL）。＝カラム型で要求が真逆。**移行先カラムが date か datetime かを必ず確認**（migration の `SHOW CREATE TABLE`）。
- **直し方（保存側で正規化。表示はスラッシュのままでよい）**: フォーム→DB 変換（`convertRecordFormToDb` 等、patchEntity の前）で、datetime カラム向けは `'/'→'-'`＋日付のみなら ` 00:00:00` 補完＋空は null、date カラム向けは `'/'→'-'`＋空は null（時刻は付けない）に正規化する。
  - **DRY 化推奨**: プラグイン共通の Util に集約して各 Table から呼ぶ（実例: `Cpm\Lib\CpmUtil::normalizeDateTime($v)` / `normalizeRecordDateTimes($record, 'Model', ['date_a','date_b'])`）。1画面で気づいたら**同種の datePicker 保存を全フォームへ横展開**（見積/請求/入金/売上/プロジェクト…）。手書きの ` . ' 00:00:00'` 連結も同 Util に寄せる。
  - 検証は「スラッシュ日付を POST → `assertRedirect`（save 成功）→ 再取得して `->format('Y-m-d')` が一致」の統合テストで固定（`basercms-unittest`）。**既存テストが日付を assert していないと silently NULL を見逃す**ので、日付の値まで assert すること。
- **編集画面の日付表示（GET）もスラッシュで統一**: 4系互換の `convert*ToFormData`（編集表示用にエンティティを `toArray()` して整形）が日付を `format('Y-m-d')`（ハイフン）で出すと、add 画面（既定値が `date('Y/m/d')` 等スラッシュ）と不一致になる。**表示用も `format('Y/m/d')` に揃える**（保存側の正規化が両形式を受けるので安全）。これも共通 Util に集約（実例 `CpmUtil::formatDateObjectsForForm($cols)`）して見積/請求/入金/経費/工数/商品… 全 `convert*ToFormData` から呼ぶ。
- **★[最重要・別の罠] `func()->min()/max()/sum()` を datetime カラムに使うと結果が数値型にキャストされ壊れる**: `find()->select(['min_date' => func()->min('billing_date')])` の集計結果は **`2026.0` のような float** になる（集計関数の戻り型が数値既定のため）。これを `datetime` カラム（例 見積 `billing_start_date`）へ代入すると**マーシャリングで NULL** になる（しかも代入元の他経路では再現せず原因究明が難航する）。**datetime の最小/最大が欲しいときは集計関数を使わず `->orderBy(['col' => 'ASC'/'DESC'])->first()` で「最古/最新行」を取り、そのエンティティの日時プロパティ（正しい DateTime 型）を使う**。`func()->sum/count` は数値カラムなら問題ない（数値が欲しいので正しい）。調査時は「DB を生SQLで段階的に読み」どの save 後に NULL 化するかを二分する（`basercms-unittest` の error.log/DB読みデバッグ）。

### 4. データベース・モデル (`Table`, `Entity`)

*   **構成変更**: `Model` (CakePHP 2) は `Table` と `Entity` (CakePHP 4/5) に分離されました。
    *   ビジネスロジックやバリデーション → `Table` クラス (`src/Model/Table`)
    *   個々のデータ振る舞い → `Entity` クラス (`src/Model/Entity`)
*   **検索 (`find`)**:
    *   × `find('all', ['conditions' => ...])` (配列形式)
    *   ○ `$this->find()->where([...])->all()` (クエリビルダー形式)
*   **保存 (`save`)**:
    *   × `$this->save($data)` (配列を渡す)
    *   ○ `$this->save($entity)` (エンティティを作成/パッチしてから渡す)
        ```php
        $entity = $this->newEmptyEntity();
        $entity = $this->patchEntity($entity, $data);
        $entity = $this->patchEntity($entity, $data);
        $this->save($entity);
        ```
*   **モデル取得 (ClassRegistry)**:
    *   × `ClassRegistry::isKeySet($key)` や `ClassRegistry::getObject($key)`
    *   ○ `\Cake\ORM\TableRegistry::getTableLocator()->get($key)` (これだけでインスタンス化も兼ねるため、存在確認は不要)

### 5. ビュー・ヘルパー (`View`)

*   **ヘルパー呼び出し**:
    *   `$this->BcBaser` などは継続して利用可能ですが、メソッドの引数や戻り値が変更されている可能性があります。
    *   PHP関数の直接呼び出し（例: `<?php echo $this->BcBaser->siteConfig['name']; ?>`）よりも、ヘルパーメソッド経由での取得を優先してください。
*   **要素 (Element)**:
    *   パス指定が変更されています。プラグイン内のElementは `PluginName.element_name` 形式で指定します。
*   **ヘルパー読み込み確認**:
    *   × `$this->Helpers->loaded('name')`
    *   ○ `$this->helpers()->has('name')`
*   **例外クラス (BcException)**:
    *   `BcException` を利用する場合は、必ず `use BaserCore\Error\BcException;` を記述するか、完全修飾名 `\BaserCore\Error\BcException` を使用してください。
*   **プラグイン読み込み確認**:
    *   `CakePlugin::loaded('name')` は廃止。
    *   `\Cake\Core\Plugin::isLoaded('name')` を使用してください。

### 6. プラグイン・名前空間

*   **名前空間**: 全てのクラスに適切な名前空間 (`namespace`) を付与してください。
    *   例: `BcBlog\Controller`, `BaserCore\Model\Table`
*   **プラグイン読み込み**: baserCMSのコアプラグインは `config/plugins.php` で制御されます。また、外部のプラグインは、baserCMSのDBのテーブルで管理されており、テーブル上で有効化されている場合は、自動的に読み込まれるため、composer に含める必要はなく、`composer dump-autoload` の実行も不要です。

### 7. テスト

*   **Fixture**: `FixtureManager` (配列定義) は廃止されました。`FixtureFactory` を使用した定義に書き換えてください。

## 8. イベント (`Event`) — リスナー(`src/Event/*EventListener.php`)の4→5（実証済み）

イベントデータ/サブジェクトの基本:
*   `$event->data['key']` → `$event->getData('key')`、`$event->subject` → `$event->getSubject()`。書き込みは `$event->setData('key', $v)`。
*   **`$event->getSubject()` は Table（Model）を返す**。保存系イベントで保存されたエンティティは `$event->getData('entity')` で取る（4系の `$model->data` ではない）。

リスナー本体の移行（プラグインの `BcPlugin` が `src/Event/` を**自動 attach** するので、リスナーが壊れていると対象イベント発火で即 Fatal＝横断で最優先に直す）:
*   **イベント定義**: 4系 `public $events = ['User.beforeFind', ...]` はそのまま使えることが多いが、ハンドラ名は `User.beforeFind` → メソッド `userBeforeFind(EventInterface $event)`（CamelCase）。型ヒントは `\Cake\Event\Event` ではなく **`EventInterface`**。
*   **★他プラグイン修飾のイベント名は4系のままだと「無発火（エラーも出ない）」**（実証済み・BlogPostTenImage/PopularBlogPost で発見）。5系の命名へ改名する:
    *   **Model系**: 4系 `Blog.BlogPost.afterSave` → 5系 **`BcBlog.BlogPosts.afterSave`**（プラグイン名=5系プラグイン名・モデル名=Table名の複数形）。ハンドラ名も `bcBlogBlogPostsAfterSave` に合わせる。
    *   **View系**: 5系は `BcViewEventDispatcher` が標準 `View.afterRender` 等を `View.<Plugin>.<ViewName>.afterRender` として再ディスパッチする方式。4系 `Blog.Blog.afterRender` → 5系 **`BcBlog.Blog.afterRender`**（View の `getPlugin()`/`getName()` に一致させる。vendor の `BcViewEventDispatcher.php` で確認できる）。
    *   **存在確認の方法**: 発火側（`vendor/baserproject/bc-blog` 等）を `grep -rn "dispatchLayerEvent\|createEvent"` して**実在するイベントに合わせる**。4系にあって5系に無いもの（例: **`Model.afterFind` はトリガー自体が5系に不在**）は発明せず deferred（TODO＋台帳の両方に記録）。
*   **`bindModel()` / `unbindModel()` は5系廃止**: beforeFind での動的アソシエーション付与は5系ORM非対応。`contain([...])` を呼び出し側クエリに足すか、対象 Table の `initialize()` に `hasOne/belongsTo` を宣言する方式へ。暫定は `return;`(noop)＋TODO で Fatal だけ回避。
*   **4系 ORM 残骸**: `$User->find('first', 配列)` → `find()->where()->first()`、`$model->save(配列)` → `newEntity()+save($entity)`、`$model->data` → `$event->getData('entity')`。
*   **request は immutable**: `$view->request->data['x'] = ...` や `$controller->request->action` への代入は5系で無効。値をビューに渡すなら `$controller->set('x', $v)`、アクション名取得は `$view->getRequest()->getParam('action')`。
*   **ヘルパー系**: `BcForm::input()` → `BcAdminForm::control()`、`admin_add`/`admin_edit` 等の**4系アクション名**は5系では `add`/`edit`（プレフィックスは prefix で扱う）。
*   **redirect 配列の `'admin' => true`（4系）→ `'prefix' => 'Admin'`（5系）**（コントローラ/リスナー共通）。

## Table / ORM レイヤーの移行パターン（BcAddonMigrator が変換しないため手作業必須）

> `BcAddonMigrator` は Model→Table のファイル移動・名前変更はするが、**クラス内の4系ORM記法はほぼ変換しない**。大規模プラグイン（例: Cpm）では1テーブル数百行がまるごと4系のまま残る。下記を機械的に潰す。**まずテーブル群のアソシエーション宣言を直す**のが全ての前提（コントローラ/ヘルパー/テンプレートが依存するため）。

### T-A. アソシエーション宣言（最重要・最頻出）: `public $belongsTo/$hasMany/...` → `initialize()`
4系のクラスプロパティ宣言は5系では**完全に無視される**（エラーも出ず、ただ関連が存在しない＝`Undefined property / association` で落ちる）。`initialize()` 内のメソッド呼び出しに変換する。
- **エイリアスは複数形**にする（`CpmEstimate` → `CpmEstimates`）。これが `contain()`・結果配列キー・`$this->Xxx->` 連鎖アクセスすべてに波及する（T-D 参照）。
- `className` はプラグイン接頭辞付き・複数形（`'Cpm.CpmEstimate'` → `'Cpm.CpmEstimates'`、コアUser は `'User'` → `'BaserCore.Users'`）。
- `dependent` は5系では `hasMany`/`hasOne` のみ有効（`belongsTo` の `dependent` は不要なので落とす）。
```php
// 4系（プロパティ宣言）
public $belongsTo = ['MainUser' => ['className'=>'User','foreignKey'=>'main_user_id']];
public $hasMany   = ['CpmEstimate' => ['className'=>'Cpm.CpmEstimate','foreignKey'=>'project_id','dependent'=>true]];
public $hasAndBelongsToMany = ['CpmProjectTag' => ['className'=>'Cpm.CpmProjectTag','joinTable'=>'cpm_project_tags_cpm_projects','foreignKey'=>'project_id','associationForeignKey'=>'project_tag_id']];
// 5系（initialize 内）
public function initialize(array $config): void {
    parent::initialize($config);
    $this->setTable('cpm_projects');
    $this->belongsTo('MainUser', ['className'=>'BaserCore.Users','foreignKey'=>'main_user_id']);
    $this->hasMany('CpmEstimates', ['className'=>'Cpm.CpmEstimates','foreignKey'=>'project_id','dependent'=>true]);
    $this->belongsToMany('CpmProjectTags', ['className'=>'Cpm.CpmProjectTags','joinTable'=>'cpm_project_tags_cpm_projects','foreignKey'=>'project_id','targetForeignKey'=>'project_tag_id']);
}
```
- HABTM → `belongsToMany`。`associationForeignKey` → `targetForeignKey`。

### T-B. `public $actsAs` → `$this->addBehavior()`（initialize内）
`public $actsAs = ['BcUpload' => [...]]` → `$this->addBehavior('BaserCore.BcUpload', [...])`。`BcCache` は5系に無い場合が多いので削除。

### T-C. `public $validate`（配列バリデーション）→ `validationDefault(Validator $validator)`
4系の `public $validate = [...]` は無効。`public function validationDefault(\Cake\Validation\Validator $validator): \Cake\Validation\Validator` に移す。`notBlank` → `$validator->notEmptyString('field', 'msg')` 等。カスタムルール（`notBlankCompleted` 等）は `$validator->add('field','ruleName',['rule'=>[$table,'method'],'message'=>...])` で登録し、ルールメソッドのシグネチャを `($value, $context)` に変更。

### T-D. `public $name` と単数形エイリアスの連鎖アクセス
- `public $name = 'CpmProject';` は削除。
- `$this->CpmEstimate->...`（関連テーブルへの直接アクセス）は、5系では**関連経由ではなく `fetchTable('Cpm.CpmEstimates')` で取得**するのが安全（`$this->CpmEstimates` 動的プロパティは存在しない）。深い連鎖 `$this->CpmUser->CpmCommit->...` は各テーブルを個別に `fetchTable()` する。
- 結果配列アクセス `$result['CpmProject']['budget']` → エンティティ `$result->budget`、関連は `$result->cpm_estimates`（snake_case 複数形プロパティ）。

### T-E. コールバック
- `afterFind()` は**5系で廃止**。集計・加工は finder メソッド（`findWithBalance` 等）か、呼び出し側で明示実行に移す。
- `beforeSave/afterSave/beforeFind` はシグネチャ変更: `afterSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)`。`$this->data['Model']['x']` → `$entity->x`。

### T-F. 検索・保存・生SQL・データソース
- `find('all'/'first'/'list', ['conditions'=>,'recursive'=>,'fields'=>,'order'=>,'joins'=>])` → クエリビルダ（`find()->where()->contain()->order()->first()/all()`、list は `find('list', keyField:, valueField:)`）。
- `$this->create($data); $this->save();` → `$e = $this->newEntity($data); $this->save($e);`。`$this->id` → `$e->id`。
- `$this->save($array, ['callbacks'=>false,'validate'=>false])` → `$this->save($entity, ['checkRules'=>false])` ＋ パッチ時に `validate:false`。
- `$this->query($sql)`（生SQL・戻り値は4系ネスト配列）→ `$this->getConnection()->execute($sql)->fetchAll('assoc')`。**[重要] 生SQL内のテーブル名にハードコードされた旧プレフィックス（`mysite_` 等）は v5 では存在しない**ので除去する（例: `mysite_cpm_practices` → `cpm_practices`）。可能ならクエリビルダに置き換える。
- `$this->getDataSource(); $db->begin()/commit()/rollback()` → `$this->getConnection()->begin()/commit()/rollback()`。
- `reduceAssociations()` / `unbindModel()` / `bindModel()` は廃止 → `find()->contain([...])` で必要な関連だけ指定する方式に変更。
- `createArrayForJoin()` 等の baserCMS 2 系独自メソッドも廃止 → `contain`/`matching`/`innerJoinWith` で書き換え。
- **配列値は `IN` を自動付与しない（CakePHP5の重要な落とし穴）**: 4系は `['field' => [1,2]]` を自動で `IN` にしたが、**CakePHP5 は `field = :c0` を生成し配列を単一値にバインド→ `InvalidArgumentException: Cannot convert value Array ... to int`**。複数値（複数チェックボックス等）の条件は **明示的に `'field IN' => (array)$values`** と書く。`createIndexConditions` 等の条件生成メソッドで配列になりうるキーは要修正。
- **`null` 一致は `IS` 演算子**: `['field' => null]` ではなく `['field IS' => null]`（CakePHP5。`!= null` は `'field IS NOT' => null`）。
- **関連テーブルの列で絞り込むなら `innerJoinWith`/`matching`**: `contain(['Assoc'])` だけでは WHERE に `Assoc.col` を書けない（別クエリでhydrateするだけ）。条件に `Assoc.col` がある時のみ `->innerJoinWith('Assoc')` を足す（常時 join すると関連無しレコードが除外される点に注意）。条件キーは関連の**複数形エイリアス**（`CpmEstimate.create_user_id` → `CpmEstimates.create_user_id`）。
- **未移行の基底クラス**: Table が `extends CpmAppModel`（4系基底）のままだと `Class "Cpm\Model\Table\CpmAppModel" not found`。移行済み基底（例 `CpmAppModelsTable`＝`\BaserCore\Model\Table\AppTable` 継承＋共有メソッド保持）に直す。`grep -rn "extends CpmAppModel\b"` で一括検出。
- **共有メソッドを使う Table は共通基底を継承する**: プラグイン共通基底（例 `CpmAppModelsTable`）に定義した共有メソッド（例 `getConditionPeriodByYearMonth()`）を、`\BaserCore\Model\Table\AppTable` を**直接**継承している Table から呼ぶと `BadMethodCallException: Unknown method ...`（ORM の `__call` 経由）。その Table の `extends` を共通基底に揃える（例 `class CpmBillingsTable extends CpmAppModelsTable`）。共通基底自体が `AppTable` を継承していれば baser 機能は維持される。
- **ヘルパ内の単数テーブル参照**: View Helper が `TableRegistry::...->get('Cpm.CpmBilling')`（単数）や `find('list', ['conditions'=>...])`（4系）を持つことが多い → 複数形 `get('Cpm.CpmBillings')` ＋ `find('list')->where([...])`。全ヘルパを `grep -rnE "get\('(Cpm|Cards)\.[A-Za-z]+[^s']'\)"` で洗う。
- **KVS（name/value）保存は `BcKeyValue` ビヘイビア＋`saveKeyValue()`**: 4系で `name`/`value` 行に JSON 等を出し入れしていたテーブル（例 CpmInputCompletions）は、Table の `initialize()` に `$this->addBehavior('BaserCore.BcKeyValue')` を足すと `saveKeyValue([$key => $value])`（コア BcKeyValueBehavior、内部で `find()->where(['name'=>$key])` upsert）が使える。読み出しの 4系マジックフィンダー `findByName($key)`（戻り値を `$row['Model']['value']` で扱う）は、**`find()->where(['name'=>$key])->first()` → `$row->value`** に直す（5系の `findByName` は Query を返すので配列前提コードは壊れる）。
- **ユーザーのグループ絞り込みは `users.user_group_id`（4系列）廃止 → 多対多 `users_user_groups` を join**: baser5 の `users` テーブルに `user_group_id` 列は無い（user↔group は多対多）。`'User.user_group_id' => [3,5,...]` のような条件は、`->innerJoin(['UsersUserGroup'=>'users_user_groups'], ['UsersUserGroup.user_id = Users.id'])->where(['UsersUserGroup.user_group_id IN'=>[...]])->groupBy(['Users.id'])`（1ユーザーが複数グループ所属で重複するため groupBy）に置き換える。`cpm_user_settings`（use_cpm 等）は別途 leftJoin。

### T-H. Table / Lib 内では `fetchTable()` は使えない（コントローラ専用）
`$this->fetchTable('Plugin.Models')` は **Controller のメソッド**。Table クラスや Lib・サービス内で使うと `BadMethodCallException: Unknown method 'fetchTable'`。Table/Lib 内では `\Cake\ORM\TableRegistry::getTableLocator()->get('Plugin.Models')` を使う。

### T-I. `getControlSource()`（フォーム選択肢生成）の4系→5系
baserCMS 2/4 の Model::getControlSource は Table に残るが中身が4系（`$this->Assoc->find('all', recursive)`、`$row['Model']['field']`、`unbindModel`、`createArrayForJoin`、`$loginUser['X']['y']`）。
- 関連は `TableRegistry::getTableLocator()->get('Plugin.Models')` で取得し、`find()->orderBy([...])->all()`、`find('list', keyField:, valueField:)`。
- ネスト配列アクセスはエンティティへ（`$row->field`、`$loginUser->id`）。別テーブルの値（例 use_cpm）は `find()->select()->all()->combine('key','val')->toArray()` でマップ化して参照。
- `unbindModel`/`createArrayForJoin` による絞り込みは `contain`/`matching`/`innerJoinWith` で書き換え（複雑なグループ絞り込みは一旦TODOで全件返しにして表示を優先するのも可）。
- **テンプレート/ヘルパからの呼び出しは `Plugin.ModelPlural.field`（複数形）にする**: `$this->BcAdminForm->getControlSource('Cpm.CpmProject.user_id')`（単数）は、`BcFormHelper::getControlSource` が内部で `TableRegistry::getTableLocator()->get('Cpm.CpmProject')` を引くため **`MissingTableClassException: Table class for alias 'Cpm.CpmProject' could not be found`**。`'Cpm.CpmProjects.user_id'`（複数形）に直す。フォームテンプレ（form.php / _form_*.php / 検索 element）に4系の単数モデル名が散在しがちなので一括 grep（`getControlSource('Plugin.Model[^s].`）。

### T-J. 集計（SUM/集約）メソッドと afterSave の5系化（実証済みパターン）
4系の集計 Model メソッド（`getAggregateX`/`aggregate*`/`afterSave` 等）を移植する際の定型。**ユニットテストで4系と同値を固定しながら**進めるのが安全（→ `basercms-unittest` のプラグイン単体テスト）。

- **SUM/集約は `func()->sum` ＋ `->first()->alias ?? 0`**: 4系 `find('first', ['fields'=>['SUM(amount) AS total'], 'conditions'=>...])` → 
  ```php
  $total = $this->Assocs->find()
      ->where(['Assocs.user_id' => $userId, 'Assocs.date >=' => $begin, 'Assocs.date <=' => $end])
      ->select(['total' => $this->Assocs->find()->func()->sum('amount')])
      ->first()->total ?? 0;
  ```
  4系が int キャストしていたら `(int)`、していなければ `?? 0` のまま（decimal は数値文字列で返る）。関連条件があるときだけ `->innerJoinWith('Assocs')`（複数形エイリアス）＋ `'Assocs.type_cd IN'=>[...]`。
- **空配列の `IN` はエラー**: `'x IN' => $ids` で `$ids` が空配列だと CakePHP5 が `IN ()` を生成して例外。集計対象IDが空になりうるメソッド（ユニット所属ユーザーが0人等）は **先頭で空ガード**し、クエリを回さず 0（や全0の配列）を返す。
- **`MIN()/MAX()` は `func()->min` の entity マッピングが不安定** → `->orderBy(['x'=>'ASC'])->select(['x'])->first()->x`（最古/最新を1件取る）で代替すると確実。
- **集計オーケストレーションの戻り値は4系の配列形状を維持**: コントローラ/ビューが `Hash::extract($rows, '{n}.Model')` で消費するメソッド（`aggregateAll` 等）は、5系でも `[ ['Model'=>[...カラム＋集計値...]], ... ]` を返す。find はエンティティを返すので、各行を `array_merge_recursive(['Model'=>$entity->toArray()], ['Model'=>[集計値]])` で組み立てる。`aggregate($data)` 本体が `$data['Model']['id']` 参照＋`array_merge_recursive` なら **配列入力のままで5系互換**（中で4系APIを使っていなければ無改修で再利用できる）。
- **`virtualFields`（旧 afterFind 計算値）は明示計算メソッドに集約**: 4系 virtualFields（`commitedTotal`/`balance`/`costTotal` 等）は5系に無い。計算を1メソッド（例 `calcBalance($entity)`：内部で集計し `$entity->costTotal = ...` と動的セットして返す）に集約し、**find 後に各行へ適用してから合算**する。複数メソッド（assignee 合計・cost 合計）から再利用できる。
- **afterSave の月按分・一括再保存**: `afterSave(EventInterface,EntityInterface,ArrayObject): void`。再入防止に `private bool $batchFlg`（冒頭 `if ($this->batchFlg) return;`）。`$entity->date`（`DateTimeInterface` なら `format('Y-m-d')`）から月範囲を作り、`func()->sum` で合計→按分率算出→対象行を再取得し `calc_amount` を再計算→ `$this->batchFlg = true; $this->saveMany($rows); $this->batchFlg = false;`。4系の `$this->data` 参照・`saveAll`・`recursive` を置換。
- **置換保存(saveMultiple)＝既存削除→新規。`deleteAll` は cascade しない**: 4系 `$Model->delete($id, true)` は `dependent=>true` の子も従属削除したが、**5系 `deleteAll(['id'=>...])` は ORM の cascade を通さず子が孤児として残る**。子を連れて消す必要がある削除は `$e = $T->get($id); $T->delete($e);`（または対象を1件ずつ `delete($entity)`）に置き換える（関連側 hasMany に `dependent=>true` が要る）。置換保存テストでは「子も消えたこと」を assert すると回帰を防げる。
- **コピーの芋づる複製**: 親 `copy()` は本体を `newEntity/save` で複製し、子は子 Table の `copy($id, $newParentId)` を呼んで再帰複製。子 copy の戻り値は5系では **`EntityInterface|false`**（配列でない）。親側は `->id`＋真偽判定で使う（`$res['Child']['id']` の配列アクセスをしない）。トランザクションは `getConnection()->begin()/commit()/rollback()`。
- **コールバック本体を後続Planへ遅延するときは「黙って空スタブ」にしない**: 4系シグネチャのままだと save が `BadMethodCallException` になるため afterSave 等は5系シグネチャへ替えるが、本体未実装で空 return にすると**保存時の副作用（集計反映・通知）が黙って消える**。本体に `// TODO baserCMS5移行(後続): ...` を残し、かつ**「現状スタブ(無処理)であること」を固定する tripwire テスト**を1本置く（将来本体を実装するとそのテストが落ちて気づける）。
- **★[最重要] 5系の `save()` に `callbacks` オプションは無い（4系の `['callbacks'=>false]` は黙って無視）**: CakePHP5 `Table::save($entity, $options)` の既定オプションは `atomic/checkRules/_primary` のみ。**`['callbacks'=>false]` を渡しても afterSave/beforeSave は常に発火する**（4系の callbacks 抑止は5系に存在しない）。これに気づかず4系の `callbacks=>false` をそのまま移植すると、**相互再帰する afterSave（例: 見積↔請求↔プロジェクトが互いを save し合う）が無限ループする**。
  - **正しい再入防止は「ガードフラグ」**（`afterSave 月按分` の `private bool $batchFlg` と同じ手法）。相互再帰の各 afterSave を持つ Table に再入ガードを置く:
    ```php
    private bool $inAfterSave = false;
    public function afterSave(EventInterface $e, EntityInterface $entity, ArrayObject $o): void {
        if ($this->inAfterSave) return;        // 再入を遮断
        $this->inAfterSave = true;
        try { /* 他テーブルを save（相手 afterSave も走るが、相手側ガード or 値収束で停止） */ }
        finally { $this->inAfterSave = false; }
    }
    ```
    循環の各エッジは必ずどこかの Table の afterSave を2回目に通るため、各 afterSave に再入ガードを置けば全サイクルが切れる。
  - イベント自体を一時停止したい場合は `$table->getEventManager()->off('Model.afterSave', ...)` もあるが、ガードフラグの方が局所的で安全。
  - 移植時 `grep -rn "'callbacks'" src/` で4系由来の無効オプションを洗い出し、ガードフラグへ置き換える（値収束で自然終了する単純ケース以外は必須）。
- **4系の複数テーブル生SELECT（`SELECT A.*, B.*`）は5系で flat 化に注意**: 4系はテーブルエイリアスで `$row['A'][..]/$row['B'][..]` のネストで返ったが、`getConnection()->execute($sql)->fetchAll('assoc')` は **flat な連想配列**を返す。同名カラム（`id`/`name` 等）が後勝ちで衝突するため、`['A'=>$row, 'B'=>$row]` のように同じ flat 配列を両キーに入れると**値が混ざる**。ORM（`get($id)->contain(['B'])` → `['A'=>$e->toArray(),'B'=>$e->b->toArray()]`）で取り直すか、SELECT で列別名（`A.id AS A__id`）を付けて再構築する。

### T-K. ユーザー↔ユニット(グループ)は 5系 BTM。テストで本番に無い列を捏造しない
- baser5 の `users` に `user_group_id` 列は**無い**（user↔group は `users_user_groups` の多対多）。4系の `belongsTo('Unit', foreignKey:'user_group_id')` や `hasMany('Users', foreignKey:'user_group_id')` は壊れる。**`belongsToMany` ＋ `joinTable:'users_user_groups'`** に再設計する（向き: users 起点なら foreignKey=user_id/target=user_group_id、user_groups 起点なら逆）。`innerJoinWith` で join し、1ユーザー複数グループ所属の重複を避けるため `->groupBy(['<起点>.id'])`。（条件絞り込みのみの簡易ケースは T-F 行末の生 join 方式でも可）
- **[最重要の戒め] テストを通すために本番に存在しないカラム（例 `users.user_group_id`）を migration で足してはいけない**。それは本番で壊れる非互換を隠蔽するだけ。正しい5系スキーマ（BTM）で解くこと。テスト用スキーマは本番の `SHOW CREATE TABLE` に忠実に作る。

### NumberHelper::currency() は null 不可
`Cake\View\Helper\NumberHelper::currency()` は第1引数が `string|float`。null を渡すと `TypeError`。テンプレートで `$this->Number->currency($e->budget ?? 0, ...)` のように null 合体する。`NumberHelper::format()` も同様（null は `TypeError`）。

### NumberHelper の独自フォーマット（`Number::addFormat('yen', ...)`）は廃止 → `format()` へ
4系は `config/bootstrap.php` 等で `CakeNumber::addFormat('yen', ['places'=>0])` を登録し `$this->Number->currency($v, 'yen')` で「通貨記号なし・3桁区切り」を出していた。**CakePHP5 に `Number::addFormat` は無く**、`currency()` の第2引数は ICU の ISO通貨コード（`JPY` 等）前提。未知コード `'yen'` を渡すと **「YEN 550,000」のようにコードが接頭辞として出力**される（ISO `JPY` でも「￥550,000」で「円」サフィックスにはならない）。
- **正攻法は `currency()` ではなく `Number::format()`**: `$this->Number->currency($v, 'yen', ['places'=>0])` → `$this->Number->format($v, ['places'=>0])`。`format()` は記号なし・3桁区切りで `places`(最小小数桁)/`precision`(最大小数桁) を解釈。`thounsands`(4系の誤綴り)・`negative` 等の旧オプションは `_setAttributes` が `places/precision/pattern/useIntlCode` しか見ないため**無害に無視**される（負数は `-` で表示）。多数あるなら一括置換: 正規表現 `->currency\((.*?),\s*'yen'(\s*,\s*\[[^\]]*\])?\s*\)` → `->format($1$2)`（値内の `()` を含むため `[^)]` ではなく `.*?` で受ける）。
- 4系の `'yen'` フォーマットは「記号なし数値」だったので、テンプレ側が別途 `円` を付けている箇所（`currency(...)?>円` 等）はそのまま「550,000円」になる。互換ヘルパ（NumberHelper継承＋`Number`差し替え）でも実現できるが、専用クラスが増えるだけで内部は同じ `format()`。標準API（`format()`置換）が素直。

### T-運用. 大規模プラグインはテーブル層を「宣言だけ先に一括」変換すると安全
テーブルが数十枚ある大規模プラグイン（例: Cpm は約30テーブル・7000行超）は、(1) **まず T-A〜T-D（アソシエーション/ビヘイビア/バリデーション/`$name`削除）の“宣言”だけを全テーブル一括で `initialize()`/`validationDefault()` 化**してモデル層をロード可能にし、(2) メソッド本体の `find()/query()/連鎖アクセス` 等は各行に `// TODO baserCMS5移行:` マーカーを付けて残し、後続の**画面通し工程**で実際に呼ばれた箇所だけ確実に直す、の2段構えが安全・効率的。宣言とメソッド本体を同時に直すと業務ロジックを壊しやすい。宣言変換は機械的なので、ファイル単位で並列実行（1エージェント=1テーブル、`php -l` で自己検証）すると速い。

### T-G. テーブル名プレフィックスの前提変更
4系は `mysite_` 等のテーブルプレフィックスを使っていることがある（`SHOW TABLES` で確認）。**5系（標準インストール）はプレフィックス無し**。生SQL・`joinTable`・`setTable()` でプレフィックスをハードコードしている箇所をすべて無印に直す。データ移行で `mysite_cpm_x` → `cpm_x` のように作成する場合は [[catchup-portal-v4-to-v5-migration]] の方針に従う。

---

## Controller / 画面通し（管理画面）レイヤーの移行パターン

> テーブル層の基盤（T-A〜T-G）を固めた後、画面（コントローラ＋テンプレート＋関連element＋Lib＋依存プラグイン）を1枚ずつ通して潰す工程。1画面が広範囲に波及する（実例: Cpm プロジェクト管理 index = コントローラ + テーブルの calcBalance + CpmUtil(Lib) + index_row/index_list テンプレート + Cards 依存プラグイン）。エラーをブラウザで1つずつ追って潰すのが確実。
>
> **画面結合フェーズの定石（テーブル層完了後・ドメイン単位で実証済み）**: ブラウザ手動より先に **ログイン付きコントローラ統合テスト**（`basercms-unittest` 参照）を各コントローラに1本立て、(1) GET で index/edit/add が描画200に到達することで移行漏れを自動検知 →(2) テンプレの `Form->create('Model')`文字列 / 素の `$this->Form->` / ネスト配列アクセス `$row['Model']['x']` を潰す →(3) コントローラの `passedArgs`/`recursive`/`reduceAssociations`/`find('first'|'all'|'list',配列)`/`field()`/`delete($id,true)`/単数形`$this->Model` を5系化 →(4) POST フロー（add/edit/delete・ajax確定）で DB 変化を assert。delete は全画面共通で `get($id)`→`delete($entity)`＋存在しないIDは `try/catch RecordNotFoundException` で4系の graceful 分岐（「無効な処理です。」→index）を再現。設定ビュー変数の欠落は AppController::beforeRender で横断解消（下表 `set(Configure::read(...))` の行）。**注意: 描画テストの 200 OK は `Undefined variable` 等の warning を握り潰す**ので、テスト後に `tests/TestApp/logs/error.log` を grep して pristine を確認すること。

### C-0. 機械的に一括変換できるパターン（プラグイン全体へ先行一括適用すると効率的）
画面を1枚ずつ通す前に、**構文を壊さない・意味が一意に定まる**変換はプラグイン全体へ `perl -pi` で先に当てておくと往復が減る（各変換後に必ず `php -l` で全変更ファイルを検証）。Cpm プラグインでの実績（テンプレ＋src）:
| 対象 | 変換 | 備考 |
|---|---|---|
| `->Form->input(` | → `->Form->control(` | CakePHP5 で `input()` 廃止。ヘルパは据え置き（`$this->Form`のまま）。値バインドのため BcAdminForm に寄せるかは画面ごと判断 |
| `->element('admin/...')` | → `->element('...')` | 5系は `templates/Admin/element/` 配下なので `admin/` 接頭辞不要。`js()/css()` の `Cpm.admin/...` アセットは触らない |
| `getControlSource('Cpm.CpmProject.` 等の**単数**モデル | → `Cpm.CpmProjects.`（複数） | `Cpm.CpmPractice.`→`CpmPractices.`、`Cpm.CpmProduct.`→`CpmProducts.`。単数は MissingTableClass。**`control('CpmProject.field')` のフォームdataキーは単数のまま変えない**（getControlSource の第1引数だけ） |
| `currency($v, 'yen', $opts)` | → `format($v, $opts)` | `Number::addFormat` 廃止。正規表現は値内の `()` を含むため `->currency\((.*?),\s*'yen'(\s*,\s*\[[^\]]*\])?\s*\)`→`->format($1$2)` |
| `getRequest()->action` | → `getRequest()->getParam('action')` | マジックプロパティ廃止 |
| `getRequest()->query`（配列用途） | → `getRequest()->getQueryParams()` | 単一キーは `getQuery('k')`。**getterへの代入** `getQuery('x') = ...` は別途 `withQueryParams()` 化（Fatal） |
| 検索フォーム `searches/`（複数） | → `search/`（単数）へ**移動** | setSearch が読むのは `search/`。**移動だけでなく中身も横断変換**: `$this->Form->`→`$this->BcAdminForm->`（`CpmForm`/`BcAdminForm` は対象外）、`create('Model', [...])`→`create(null, [...])`。重複（移行済み）ファイルは破棄 |
| `Time->format($x)`（第2引数なし／`'Y-m-d'`） | → `Time->format($x, 'yyyy-MM-dd')` | 第2引数省略は**日時**表示（`2026-01-01 00:00:00`）。日付のみは ICU `'yyyy-MM-dd'`（PHP date形式ではない）。`Time->format\(([^,)]+)\)`→`Time->format($1, 'yyyy-MM-dd')` |
| `control(['type'=>'select','multiple'=>'checkbox',...])` | → checkbox ループ（C-G参照） | baser5 で崩れる（空select/`< class="">`）。`grep -rn "'multiple' => 'checkbox'"` で全件洗い、options をループして `control('field[]', type=checkbox)` に。値（OPTS）が箇所毎に異なるので一括 perl ではなく個別 Edit 推奨 |
| 単数テーブル `get('Cpm.Cpm<単数>')`（src/ヘルパ含む） | → 複数形 `get('Cpm.Cpm<単数>s')` | Helper/Table/Controller 全 src。`grep -rnE "get\('(Cpm\|Cards)\.[A-Za-z]+[^s']'\)"` |
| `$this->Form->create('Model', ...)`（文字列モデル） | → `$this->BcAdminForm->create(null, [..., 'valueSources'=>['data','context']])` | フォーム/編集テンプレ全般。文字列モデルは `No context provider found for value of type string`（CakeException）。`create\('[A-Za-z]+',`→`create(null,`。`$this->Form->`→`$this->BcAdminForm->` も併せて |
| `$this->action`（テンプレ）／`'admin_xxx'` | → `$this->getRequest()->getParam('action')`／`'xxx'` | View の `$this->action` 廃止。5系は prefix=Admin で **action名に `admin_` は付かない**（`admin_add`→`add`、`admin_index`→`index`）。getControlSource の mode 判定や create の action分岐に影響 |
| `$this->BcAuth->user()` | → `\BaserCore\Utility\BcUtil::loginUser()` | 戻りは `UserInterface\|false`（**未ログイン時は `null` ではなく `false`**）。**`?->` は `false` に効かない**ため `BcUtil::loginUser()?->id` は未ログイン時に `Attempt to read property "id" on bool` 警告＋null になる（テスト実行時に顕在化）。堅牢形は `(\BaserCore\Utility\BcUtil::loginUser() ?: null)?->id`（`false ?: null`→null→`null?->id`）か明示分岐 `$u = BcUtil::loginUser(); $u ? $u->id : null;`。`$user['id']` 等の配列アクセスはエンティティ参照へ |
| `getData()(...)`（二重括弧） | → `getData(...)` | 4系→5系の機械変換ミスで `$this->getRequest()->getData()('Model.x')` のように `()` が二重になっている箇所がテンプレに残る。`sed 's/getData()(/getData(/g'` で一括 |
| `ConnectionManager::get('default')` + `$db->begin/commit/rollback` | → `$table->getConnection()->begin/commit/rollback` | トランザクションは対象テーブルの接続から |
| `$this->Form->year('Model.field', ...)` / `->month(...)` | → `control('Model.field.year', ['type'=>'select','options'=>$years,'label'=>false])` 等 | **`FormHelper::year()/month()` は CakePHP5 で廃止**。`$years`/`$months` をテンプレ冒頭で自前生成。`getData('Model.field')` は `['year'=>,'month'=>]` で受かる |
| `$this->postConditions($data)`（コントローラ） | → 検索条件を明示的に組み立て | **`Controller::postConditions()` は5系廃止**。`Call to undefined method`。`if(!empty($d['Model']['x'])) $conditions['Models.x']=...` を手書き |
| `$this->set(Configure::read('Cpm'))` の欠落 | → **プラグイン Admin AppController の `beforeRender()` で1回 set ＋ 各コントローラがそれを継承** | テンプレが参照する設定ビュー変数（`$billingStatuses`/`$taxRateList`/`$saleTypes`/`$estimateTypes` 等）の `Undefined variable`。4系は **プラグイン AppController::beforeRender が全画面に自動 set** していた。★5系の落とし穴: `bin/cake bake` 由来や手移植の Admin コントローラは **`BaserCore\Controller\Admin\BcAdminAppController` を直継承**しがちで、プラグインの `CpmAppController`（=4系の set を持つべき層）を経由しない→設定ビュー変数が全画面で欠落。**正しい直し方は per-action set ではなく、`src/Controller/Admin/CpmAppController.php` に `public function beforeRender(EventInterface $event): void { parent::beforeRender($event); $this->set(\Cake\Core\Configure::read('Cpm')); }` を置き、各 Admin コントローラを `extends CpmAppController` にする**（per-action の重複 set は削除）。横断的に一発で解消できる。`Configure::read('Cpm')` の値自体は `BcPlugin::bootstrap()`/`config/setting.php` で load 済みか確認（未 load なら C-D で復元） |

注意: `'div'`/`'between'`/`'after'` 等 input 専用の旧オプションは control では HTML属性に漏れることがあるが Fatal にはならない（画面通し時に個別清掃）。`searches/`→`search/`（C-G）はディレクトリ移動＋各フォームの他4系記法も伴うので一括ではなく画面ごとに行う。

### C-A. コントローラの4系イディオム
- `$this->ModelName`（4系の自動モデルプロパティ）→ `$this->fetchTable('Plugin.ModelNames')`（複数形）。`$this->CpmProject->CpmEstimate->` のような連鎖は各テーブルを個別に `fetchTable()`。
- `$this->siteConfigs['admin_list_num']` → `\BaserCore\Utility\BcSiteConfig::get('admin_list_num')`。
- `$this->passedArgs`（名前付き引数）は廃止。並べ替えは CakePHP の Paginator がクエリ文字列 `?sort=&direction=&page=` で自動処理。`view_type` 等は `getRequest()->getQuery()`。
- `$this->RequestHandler->isAjax()` / `$this->params['url']['ajax']` → `$this->getRequest()->is('ajax')`。
- **ServerRequest のマジックプロパティは廃止**: `$this->getRequest()->action`（4系）→ `getParam('action')`。`$this->getRequest()->query`（配列全体）→ `getQueryParams()`、単一キーは `getQuery('key')`。`->data`→`getData()`。テンプレでも同様（`getControlSource('...', ['mode'=>$this->getRequest()->getParam('action')])`）。一括置換可（`getRequest()->action\b`→`getRequest()->getParam('action')`、`getRequest()->query\b`→`getRequest()->getQueryParams()`）。
- **リクエストは不変（immutable）**: 前セッションの自動変換で `$this->getRequest()->getQuery('begin') = $x;`（getterへ代入）や `->query['k']=$x;` が残ると `Can't use method return value in write context` の **Fatal（php -l で検出可）**。クエリへ値を足すなら `$this->setRequest($this->getRequest()->withQueryParams($this->getRequest()->getQueryParams() + ['begin'=>$x,'end'=>$y]))`、POSTデータは `withData('Model.field', $v)`。
- 4系 `$this->paginate = ['fields'=>,'conditions'=>,'recursive'=>,'joins'=>,'group'=>,'having'=>,'order'=>,'limit'=>]; $this->paginate('Model')` → クエリビルダを組んで `$this->paginate($query)`。
  - `recursive`/`unbindModel` → `contain([...])` で必要な関連のみ指定。
  - `joins`（手書き結合）→ `leftJoinWith('Assoc')` / `innerJoinWith('Assoc', fn($q)=>...)` / `matching()`。`group`/`having` はそのまま `->groupBy()` / `->having()`。
  - 条件キーは**複数形エイリアス**（`CpmProject.x`→`CpmProjects.x`、`CpmEstimate.x`→`CpmEstimates.x`）。
- `$Db = $this->Model->getDataSource(); $Db->buildStatement([...])` 等の4系クエリ生成は廃止 → クエリビルダ、または合計などは「条件に合致するIDを取得→`func()->sum()`」で再実装。
- **[重要] protected ヘルパーの移動漏れ**: `createAdminIndexConditions()` 等、admin_index が呼ぶ protected メソッドは `BcAddonMigrator` が Admin コントローラに移動しないことがある（フロント側に残る or どこにも無い）。4系の元実装を Admin コントローラに移植し、条件キーを複数形エイリアス・`find('list')`→クエリビルダ・生SQLの旧プレフィックス除去で5系化する。
- **コンポーネントは自動ロードされない**: 4系 `public $components = ['Plugin.Excel']` 等は5系で消える。`$this->Excel->...` を使うアクションは `initialize()` に `$this->loadComponent('Plugin.Excel')`。無いと `$this->Excel` が null/未定義で Fatal（cf. CpmBillings の Excel ダウンロード）。
- **delete アクションの定石**: `$this->getRequest()->allowMethod(['post','delete'])` → `$entity = $table->get($id)`（`try/catch RecordNotFoundException`→「無効な処理です」→index）→ `$table->delete($entity)`。4系 `$this->Model->delete($id)` の id 直渡しは廃止（エンティティを渡す）。delete を `FormProtection` の `unlockedActions` に登録（bca-submit-token POST は `_Token` 無し）。
- **横断の「残骸＝呼ぶと即 Fatal」パターンと段階戦略**: 大規模プラグインは `index/add/edit` だけ移行され、**`delete`/`ajax_*`/CSV/Excel/メール/Slack が4系のまま放置されがち**（`$this->Model->`動的null・`find('first',配列)`・`getDataSource`）。これらは**呼ぶまで動かないので「画面は開くが操作で500」**になる。横断で潰すなら ①静的コードチェックで全残骸を列挙 → ②**「構文だけ5系化」を一括**（`php -l`＋既存テスト回帰ゼロを完了条件にし、この段では新規テストを書かない）→ ③テスト＆ブラウザで意味検証、の順が速い。**外部依存(Slack/メール/CSV/Excel/集計)は中身を移さず `// TODO baserCMS5移行:` か `throw new \Cake\Http\Exception\NotImplementedException(...)` で deferred を明示**（4系本体を放置すると呼んだとき黙って Fatal するので、ガードして「未実装」を顕在化させる）。

### C-A2. 廃止コンポーネント（PaginatorComponent / RequestHandlerComponent 等）
`$this->loadComponent("Paginator")` は `MissingComponentException: PaginatorComponent could not be found`。**CakePHP 5 で PaginatorComponent は廃止** → `initialize()` から削除し、ページネーションは `$this->paginate($query)` を直接使う。同様に `RequestHandlerComponent` も廃止（`$this->getRequest()->is('ajax')` 等で代替）。`initialize()` の `loadComponent()` を点検する。

### C-A3. カスタム Component の initialize シグネチャ
4系 `public function initialize(Controller $controller)` は5系の `Cake\Controller\Component::initialize(array $config): void` と非互換で **Fatal**（`Declaration ... must be compatible`）。`public function initialize(array $config): void { parent::initialize($config); ... }` に変更し、コントローラ参照は `$this->getController()` で取得する（型ヒントの `Controller` も未importだと `Component\Controller` に誤解決される）。

### C-B. afterFind 由来の計算値はコントローラで明示セット
旧 `afterFind` で行っていた行ごとの計算（収支・利益率等）は5系で自動実行されない。テーブルに計算メソッド（例 `calcBalance(EntityInterface $e)`）を用意し、コントローラで `paginate` 後にループして `$e` にプロパティをセットする。paginate の `fields` で関連カラムを平坦化していた場合（例 `CpmProduct.no` を行に持たせる）も、ループ内で `$e->no = $e->cpm_product->no` のように展開する。

### C-C. テンプレート（element 含む）の半変換状態に注意
`BcAddonMigrator` 後のテンプレートは**エンティティアクセスと4系配列アクセスが混在**することが多い。
- `\Cake\Utility\Hash::get($data, 'Model.field')` → `$data->field`。`Hash::get($data,'Model.field','Y-m-d')`（第3引数は4系のデフォルト値）も `$data->field` に。
- 関連アクセス `$data['Assoc']` → `$data->assoc_property`（**snake_case**）。belongsTo はプロパティが**単数形** snake_case（`CpmProduct`→`$data->cpm_product`、`MainUser`→`$data->main_user`、`CardsCompany`→`$data->cards_company`）。hasMany は複数形（`$data->cpm_costs`）。null安全に `?->` や `?? ''` を併用。
- グローバル Lib クラスの静的呼び出し `CpmUtil::method()` → 名前空間付き `\Cpm\Lib\CpmUtil::method()`。
- **Lib 静的メソッドは「4系配列」と「エンティティ」両方で呼ばれる**: 同じ `CpmUtil::isXxx($project)` が、一覧テンプレートからは**エンティティ**（`$data`）で、編集コントローラ/フォームからは**4系ネスト配列**（`$this->getRequest()->getData()` = `['CpmProject'=>[...]]`）で呼ばれることがある。`$project->order_status_cd`（エンティティ前提）に直すと配列呼び出しで `Attempt to read property "x" on array` の warning＋null。両対応の正規化を冒頭に置く: `$p = isset($project['CpmProject']) ? $project['CpmProject'] : $project;` その後 `$p['order_status_cd'] ?? null` で参照（エンティティは ArrayAccess なので `isset($entity['CpmProject'])` は false→自身を使う、4系配列は内側を取り出す、どちらも `$p['field']` で読める）。`strtotime()` 等へ渡す日付は `(string)` キャスト。
- **`$this->Form->input(...)` は CakePHP5 で廃止** → `control(...)`。BcAdminForm で開いたフォーム内の element（例 tag_picker）は、**同じインスタンスの `$this->BcAdminForm->control(...)`** に統一する（`$this->Form` は別インスタンスでフォームコンテキスト/値バインドを共有しないため）。`input()` 互換は baser にも無い。
- **element パスの `admin/` 接頭辞は5系で不要**: 4系 `View/Elements/admin/foo/bar.php` を `element('admin/foo/bar')` で呼んでいたものは、`BcAddonMigrator` 後 `templates/Admin/element/foo/bar.php`（`admin/` サブフォルダ無し）に移るため、`エレメントテンプレート「admin/foo/bar」が見つかりませんでした` になる。**`element('foo/bar')` に直す**（`admin/` を外す）。CSS/JS の `Cpm.admin/...` アセットパスはそのままでよい（別物）。
- **空配列の `IN ()` は CakePHP5 で例外**: `Impossible to generate condition with empty list of values for field (...)`（`Cake\Database\Exception\DatabaseException`）。4系は空 IN を黙って0件にしたが5系は throw。`where(['X IN' => $ids])` の前で `if (empty($ids)) return [];`（または条件を付けない）でガードする。例: タグ未設定プロジェクトで `getProjectTags($tagIds=[])` が `WHERE CpmProjectTags.id IN ()` になり落ちる。
- リンク配列のコントローラ名は **CamelCase**（`'controller' => 'cpm_practices'` → `'CpmPractices'`）、別プラグインは `'plugin' => 'Cards'` のように CamelCase。未マッチだと `MissingRouteException`。
- **View での `$View->request` / `$this->request` プロパティアクセスは「ヘルパ自動ロード」を誘発する**: CakePHP5 の View には `request` プロパティが無く、`$View->request->params[...]`（4系）は **未知プロパティ→ヘルパ `request` ロード**扱いで `<Plugin>.requestHelper could not be found`（MissingHelperException）になる（`$this->data` と同じ罠＝C-C 参照）。`$View->getRequest()->getParam('controller')` / `getRequest()->getData(...)` に直す。**特にイベントリスナ（`Form.afterForm` 等、全フォームで発火）に残っていると無関係な画面まで巻き込んで落とす**ので、`dispatchAfterForm()` 経由で別プラグインのリスナが落ちたら、そのリスナ（例 `OnemindHelperEventListener::formAfterForm`）の `$View->request` を最優先で5系化する。ガードの controller 比較も CamelCase（`'Users'`）・action は `admin_` なし（`edit`/`add`）。
- **コントローラ生SQL `fetchAll('assoc')` は「フラット連想配列」＝テンプレの4系ネスト形と不一致**: 4系で `$this->Model->query($sql)` が返したネスト形 `$row[0]['total']` / `$row['Model']['x']` を前提にしたテンプレに、5系で `getConnection()->execute($sql)->fetchAll('assoc')`（フラット `$row['total']`）を渡すと、`$row[0]` が null になり**値が空・合計0**になる。テンプレ側を `$practice['total']`/`$practice['date']` のフラット参照に直す。**合計が0だと `($v/$total)*100` で `DivisionByZeroError`** も誘発するので `$total ? (...) : 0` でガード（症状: 「Division by zero」エラー画面）。SQLの別名は `Xxx__yyy` ではなく平易名（`total`/`date`）で出す。
- **`$this->data`（4系 View のリクエストデータ）は `MissingHelperException` を誘発**: CakePHP5 の View には `data` プロパティが無く、`$this->data->x` は未知プロパティ→**ヘルパ `data` の自動ロード**扱いになり `Cpm.dataHelper could not be found`。`$this->getRequest()->getData('Model.x')` に置換（一覧 element 等では該当キー無し→null で falsy になり従来の分岐が保たれる）。`grep -rn '\$this->data\b'` で一掃。
- **`$this->Time->format()` の日付書式は ICU パターン（PHP date() ではない）＋第2引数省略は日時**: CakePHP5 の `TimeHelper::format`/`i18nFormat` は ICU 形式。`Y`=週年・`m`=分・`M`=月・`y`=年なので **`'Y-m-d'` も `'Y/m/d'` も月が分(0)になり `2026-0-31` / `2026/0/31` のlike表示**になる（スラッシュ版でも同じ罠）。ハイフン表示は **`'yyyy-MM-dd'`**、スラッシュ表示は **`'yyyy/MM/dd'`**（時刻込みは `'yyyy-MM-dd HH:mm'`）。また **`format($d)` と第2引数を省略すると日時（`2026-01-01 00:00:00`）になる**ので、日付のみ表示にしたい一覧等では必ず付ける（横断対応: `grep -rnE "Time->format\([^,)]*, '[YmdHis/.: -]*'\)"` で PHP形式を洗い ICU へ一括）。`$this->BcTime->format` も同様。
  - **★[重要] 一覧表示と編集フォームで「日付の整形手段」が違う＝format 文字列も違う**: ①**一覧テンプレ**で値を表示するのは `$this->Time->format($e->x, ...)`（**ICU**）→ `'yyyy/MM/dd'`。②**編集フォームの入力欄**に出すために convert で日付オブジェクトを文字列化するのは **PHP の `$v->format('Y/m/d')`**（`DateTimeInterface::format`＝PHP date形式で月は正しい `06`）。この2つは別物で、同じ `'Y/m/d'` でも ICU 経路では月=0、PHP 経路では正しい。「一覧の日付が `2026/0/30`」なら ICU(TimeHelper) を PHP形式の文字列で呼んでいる＝`'yyyy/MM/dd'` に直す。「編集欄の日付」なら `$v->format('Y/m/d')`（PHP）でよい（共通 Util `CpmUtil::formatDateObjectsForForm` がこれ）。
- **日付の null 安全**: 一覧で null になりうる日付列は `$this->Time->format($e->x, 'yyyy-MM-dd')`（Time helper は null で空文字）か、エンティティの Chronos を `$e->x?->format('Y-m-d')`（こちらは **PHP** date() 形式）で出す。
- **`Text::truncate()` / `BcText::truncate()` は CakePHP5 で第1引数 string 必須（null 不可）**: nullable なカラム（備考・notes 等）を渡すと `Cake\Utility\Text::truncate(): Argument #1 ($text) must be of type string, null given`（TypeError）。`truncate((string)$e->notes, 46)` のように **`(string)` キャスト**する。同様に他の文字列必須ヘルパ（`h()` は null 許容だが、`truncate`/`stripTags` 等）に nullable を渡す箇所は横断的にキャストを当てる。
- **`NumberHelper::format()` / `Number::format()` も第1引数 string|int|float 必須（null 不可）**: `$this->Number->format($this->getRequest()->getData('Model.amount'), [...])` で値が null（未入力カラム）だと `NumberHelper::format(): Argument #1 ($number) must be of type string|int|float, null given`（TypeError）。`format($x ?? 0, [...])` でガード。フォームの金額表示（budget/tax/taxed_budget/billing_amount 等）に多発するので `grep -n "Number->format(" 該当テンプレ` で一括。
- **コントローラで `$this->set('project', ['CpmProject'=>$e->toArray(), ...])` のように4系ネスト配列で渡したビュー変数は、テンプレ側も配列アクセス**: 受け取りテンプレが `$project->order_notes` / `$project->id`（エンティティ前提）のままだと `Attempt to read property "x" on array`。`$project['CpmProject']['order_notes'] ?? ''` / `['id'] ?? null` に直す。エンティティで渡すか配列で渡すかをコントローラとテンプレで揃える（4系互換ネストで渡すなら配列アクセス）。
- `css()`/`js()` の第2引数は F-14（`['inline'=>false]` → `false`）。

### C-D. setting.php の欠落（設定キー＝Division by zero／adminNavigation＝メニュー/UI変化）
`BcAddonMigrator` での変換や、その後の「配列の整理」作業で、`config/setting.php` の内容が**間引かれる**ことがある。2系統に注意し、必ず4系 `Config/setting.php` と照合して復元する:
- **設定キーの欠落**: コードが参照するキー（例 `Cpm.operatingDays`/`productTypes`/`practiceTypes`/`monthlyUnitPricePartner`/`mitsumoriKessai`）が無いと `Division by zero`・`array_merge(): Argument #2 must be of type array, null given`・未定義参照になる。**画面ごとに1つずつ潰さず、横断的に洗い出す**: 参照キー `grep -rohE "Configure::read\('Cpm\.[a-zA-Z0-9_]+" templates/ src/ | sed "s/.*Cpm\.//" | sort -u` と 定義キー `grep -oE "'[a-zA-Z0-9_]+'\s*=>" config/setting.php | …` を `comm -23` で突合し、未定義キーを4系 `Config/setting.php` から一括復元する。
- **adminNavigation（管理メニュー）の欠落・改変**: メニュー項目が落ちる/直リンク化されると**4系と管理画面のメニュー構成（UI）が変わる**。例: 4系は「分析」メニュー（`cpm_menus/analysis`、`currentRegex` で `cpm_aggregate`/`cpm_units` 等を内包）から集計へ遷移する仕様なのに、変換後 setting.php では「集計」が直リンク化され「分析」や他メニュー（経理/工数管理/マスター等）が消えている、等。**UI を勝手に変えない**方針なら、adminNavigation も4系と突き合わせて元の構成に戻す（メニューのURLは4系の小文字表記 `'controller' => 'cpm_projects'` のままで baserCMS5 admin が解決する）。未移行画面へのリンクは押すとエラーになるが、画面移行を進めるにつれ解消する。

### C-F. Vue/JS から叩く ajax は戻り値を Response にし、URL を5系管理パスへ
- **ajax アクションの戻り値**: 4系は `$this->autoRender=false; return json_encode(...)` だが、5系は**文字列returnは出力されない**。`return $this->getResponse()->withType('application/json')->withStringBody(json_encode($rows ?: []))` にする。`Hash::extract($r,'{n}.Model')` は、生SQLのエイリアスを `Model__field`→平易名に直して `fetchAll('assoc')` すれば不要（フラット配列をそのまま json_encode）。
- **JS バンドル内のハードコード管理URL → `$.bcUtil.adminBaseUrl` を使う（最重要・横断）**: 4系の `/admin/...` が `.bundle.js`（および `webroot/js/src` の Vue/JS ソース）に**ハードコード**されていると、5系では 403/404（リクエストがアプリ配下 `/<subdir>/baser/admin/...` ではなくドキュメントルート直下 `/admin/...`＝4系想定へ飛ぶ。本プロジェクトは `/v5/baser/admin/...`）。
  - **`/v5/baser/...` のハードコードは禁止**（ドキュメントルート直下へ移設した瞬間にまた壊れる）。baser が用意するグローバル **`$.bcUtil.adminBaseUrl`**（`baseUrl + '/' + baserCorePrefix + '/' + adminPrefix + '/'` を実行時生成、**末尾スラッシュ付き** 例 `/v5/baser/admin/`）を使う。後続はスラッシュ無しで連結: `$.bcUtil.adminBaseUrl + 'cpm/cpm_billings/edit/' + id`。
  - **スクリプト/メソッド/プレーンJS**: `$.bcUtil.adminBaseUrl + 'cpm/...'` を直接使える（`$` グローバルが使える）。`$.baseUrl + '/admin/...'` のように別ベースを前置している箇所は、前置ごと `$.bcUtil.adminBaseUrl + '...'` に置換する（二重ベースを除去）。
  - **Vue2 テンプレート式（`:href` 等）からはグローバル `$` を参照できない**（テンプレートは `with(this)` 評価でホワイトリスト外の global は不可）。各コンポーネントに **computed `adminBaseUrl(){ return $.bcUtil.adminBaseUrl; }`** を足し、テンプレートは `:href="adminBaseUrl + 'cpm/...'"`（テンプレートリテラルなら `` `${adminBaseUrl}cpm/...` ``）。
  - **Cpm 以外のプラグインURL（例 Cards）も対象**: `/admin/cpm/` だけでなく `/admin/cards/...` 等もハードコードされている。`grep -rn "['\"]/admin/" webroot/js/src webroot/js/admin` で**全 `/admin/<plugin>/` を横断的に洗う**（1画面で気づいたら全 Vue/JS をまとめて変換し再ビルド）。
  - 修正後は **必ず webpack で再ビルド**（下記）。bundle に `grep -c "bcUtil.adminBaseUrl"` / `grep -c "/v5/baser"` で反映を確認。ブラウザはバンドルを強くキャッシュするので Cmd+Shift+R で検証。
- 集計系の生SQLは F系の `query()`→`getConnection()->execute()->fetchAll('assoc')`、`Xxx__yyy` エイリアス→平易名、ゼロ除算ガードを併せて適用（T-F 参照）。
- **Vue バンドルは「ソースを直して再ビルド」が正攻法**: コンパイル済み `.bundle.js` がソース（`webroot/js/src`）より**古い(stale)**と、`[Vue warn] Property "xxx" is not defined`（ソースには有るのにbundleに無い）等が出る。`.bundle.js` を手パッチで追うのは破綻するので、**ソースを修正→再ビルド**する。ビルドは `package.json` に script が無くても `webpack.config.js` があれば `cd <plugin> && npm install && npx webpack`（本プロジェクトは node_modules 導入済み・mode production）。再ビルド後に bundle へ修正（URL・prop 等）が反映されたか grep 確認。
- **Vue prop の型不一致**: 4系データが数値を渡すのに prop が `projectType: String` だと `Invalid prop: Expected String, got Number`。`projectType: [String, Number]` のように複数型許容にする（ソース修正→再ビルド）。
- **`Invalid prop: Expected Object/Array, got String with value "R"/"u"...`（1文字ずつ）= ajax が壊れている兆候**: ajax アクションが `$this->ModelName`（null）や `query()` で **fatal** を起こすと、レスポンスがJSONでなくエラーHTML/文字列になり、Vue が配列の代わりに**文字列**を受け取って `v-for` が**文字単位で反復**する（プロパティ警告が1文字ずつ大量に出る）。原因は Vue ではなく**サーバ側 ajax アクション/モデルメソッドの未移行**。エンドポイントを `fetchTable()` 化し、返すデータ構造（Vue が参照するフィールドのフラット配列）を整える。例: `byClient()` は `find()->contain([...])` の各エンティティを `toArray()` して Vue の `billing.id/name/amount/...` に合わせたフラット配列で返す（`Hash::extract('{n}.Model')` は不要）。
- **Table メソッド内での手動ヘルパー生成**: 集計SQLで都道府県名等を引くため `new BcTextHelper(new View())` のような4系記法がある場合、5系は名前空間付きで `new \BaserCore\View\Helper\BcTextHelper(new \Cake\View\View())`。
- **CSV出力等の未スコープ生SQL**: `download_csv` が呼ぶ `salesByMonthGroupByProject` など「画面表示に使わない」メソッドは後回しにしやすい。`$this->ModelName->`（null）や `query()` が残ると実行時に落ちるので、その機能を使う段で個別移行する。

### C-F2. ★バンドルJSが参照する DOM id / name が5系の生成規則と食い違う（描画は通るのに JS 連携が静かに死ぬ）
4系前提の `.bundle.js`（および `webroot/js/src` の JS/Vue）は **4系の DOM 命名**でDOMを掴むが、5系の `BcAdminForm->control()` は**別の id/name を生成する**ため、セレクタが何もマッチせず**イベント・自動入力・計算が無言で動かなくなる**（PHP エラーも JS エラーも出ないことが多く、画面は正常に見える）。実例: 見積フォームで「クライアント選択→提出先へ反映」「プロジェクト選択→件名へ反映」「見積タイプ切替で備考/請求方法切替」等が全滅していた。

- **2つの食い違い（両方確認する）**:
  1. **id**: 4系 `CpmEstimateSubmissionTarget`（CamelCase）⇔ 5系 `control('CpmEstimate.submission_target')` は **`cpmestimate-submission-target`（モデル別名+フィールドを小文字ハイフン）**。`#CpmEstimateXxx` セレクタが全滅。
  2. **name**: 4系 `name="data[CpmEstimate][field]"` ⇔ 5系 `name="CpmEstimate[field]"`（`data[...]` プレフィックス無し）。`input[name='data[Model][field]']` セレクタが全滅。
  - 例外: `CpmForm->projectPicker` 等**自前で明示 id を振るヘルパ**のフィールドは CamelCase id のまま（変更不要）。＝同一フォームで「ヘルパ製=CamelCase」「素 control=kebab」が混在し気づきにくい。

- **調査法（画面移行時に必ず1回やる）**:
  - JS が掴む id/name を洗う: `grep -roE "#Cpm[A-Za-z]+|\\\$\\(\"#[A-Za-z]+\"|name=['\"]?data\\[[A-Za-z]+\\]" webroot/js/src`（`#CamelCaseId` と `data[Model][...]` を列挙）。
  - 実レンダリングの id/name と突合: 管理画面をログイン付き統合テストで GET し body をダンプ（`basercms-unittest` のダンプ法）、`grep -oE 'id="[^"]+"|name="[^"]+"'` で**JS が期待する id/name が実在するか**を確認。**素 control は kebab・`Model[field]` で出る**ので、JS の `#CamelCase`/`data[...]` は基本マッチしない＝要対応。
  - 描画200・PHPテスト緑でも**この不整合は検出できない**（JS実行はしないため）。手動ブラウザ確認 or 「期待 id がHTMLに存在する」アサート（下記）で締める。

- **直し方（2案。原則は JS を5系生成 DOM に寄せる）**:
  - **推奨: JS 側を5系の生成 id/name に合わせて再ビルド**。`data[Model][field]`→`Model[field]`、`#CamelCase`→実 kebab id（または name+value セレクタ `input[name="Model[field]"]:checked` 等）。label の `for`/aria が崩れず、id 重複も起きない。要 webpack 再ビルド（C-F）。
  - **代替: テンプレ control に明示 `id` を付けて4系 CamelCase を復元**（`control('Model.field', [..., 'id' => 'ModelField'])`）。**バンドル再ビルド不要**で id 駆動の処理を最短復旧できるが、下記リスクに注意。name は変えない（POSTキーが壊れる）。ラジオ/チェックボックスは name セレクタで掴むことが多く id 付与不要なことも。
  - **★control の id を変更/明示する際のリスク**:
    - **label の `for` 不一致**: 5系 `label('Model.field')` は**フィールド名から kebab id を `for` に自動生成**する。control の id だけ変えると label クリックでフォーカスが当たらない → label にも `['for' => 同じid]` を合わせる（1対1ラベルの画面では必須）。
    - **error 表示の aria 連携 / CSS / 他JS / E2E** が `#旧id` を参照していると外れる（`grep -rn "#旧id" webroot/css webroot/js` で確認）。
    - **id 重複**: 同一画面に同種フォーム（add＋検索フォーム等）があると明示 id がぶつかる。
    - POST・バリデーション・`getData`・エラー検出は **name ベースなので id 変更の影響なし**（id は送信に使われない）。

- **★Vue/JS が動的生成するフォーム行（明細・工数等）の保存が `Unexpected field '...' in POST data`（BadRequestException）で落ちる**: 5系の管理画面は `FormProtection`（フォーム改竄防止）が効いており、**フォーム描画時にDOMへ存在しないフィールドは送信トークンに含められない**。Vue が後から足す明細行（`v-for` 生成の input）はトークンに無いため、送信すると `FormProtection` が弾く。4系(CakePHP2)は該当コントローラが `SecurityComponent` 未使用で素通りしていた挙動。さらに**メモリ枯渇エラー(`Allowed memory size exhausted` in `exception_stack_trace.php`)として現れることがある**＝巨大POSTを含む例外トレースの描画が重いだけで、真因はスタックに出る `Unexpected field` の方（トレースを読む）。
  - **直し方は2点セット**:
    1. **POST 名を5系形式に**: Vue の `:name="'data[Model][' + i + '][field]'"`（4系）→ `:name="'Model[' + i + '][field]'"`。5系コントローラは `getData('Model')`／`saveMultiple($this->getRequest()->getData(), $id)`（中で `$records['Model']`）で受けるため、`data[...]` プレフィックスが付くと `getData('data.Model...')` になり**拾えない**（弾かれなくても保存されない）。要 webpack 再ビルド。
    2. **動的アクションを `unlockedActions` に登録**: コントローラの `beforeFilter` で `$this->FormProtection->setConfig('unlockedActions', ['add','edit', ...POSTする全アクション])`（baser-core の `ContentsController`/`PluginsController` と同手法）。フォーム全体（`send_order` 等そのフォームから serialize して投げる ajax アクションも）対象に含める。存在しないアクション名を入れても無害だが正確に。
  - **検証**: 「明細を含む POST で `assertRedirect`（=BadRequest にならない）＋明細が実際に保存される（`find()->where(['estimate_id'=>$id])->count()===N`）」統合テストで固定する（`basercms-unittest` の POST フローテスト）。CSRF は別ミドルウェア（`CsrfProtection`）なので unlockedActions では無効化されない（CSRF は維持される）。

### C-G. 検索フォーム（setSearch）と生SQLのカスタムページネーション
- **検索フォームの配置ディレクトリ（横断対応）**: 5系の `setSearch('xxx_index')` は **`templates/Admin/element/search/xxx_index.php`（単数 `search/`）** を読む（コア `bc-admin-third/.../element/search.php` が `'search/' . $name` を解決）。4系/変換後は **`searches/`（複数）** に置かれていて `エレメントテンプレート search/xxx が見つかりませんでした` になる（**全画面で再発する＝横断対応すべき典型**）。**1画面で遭遇したら全検索フォームをまとめて処理する**: ①`searches/*.php` 全件に `$this->Form->`→`$this->BcAdminForm->`・`create('Model',…)`→`create(null,…)` を一括適用 → ②`search/`（単数）へ全移動（`git mv`） → ③既に移行済みのファイルは `searches/` 側を破棄 → ④空の `searches/` を削除 → ⑤全件 `php -l`。
- **検索フォーム本体の4系記法**: `$this->Form->create('Model', [...])`（文字列モデル）→ `$this->BcAdminForm->create(null, ['type'=>'get', 'url'=>['plugin'=>'...','controller'=>'CamelCase','action'=>'...']])`（管理画面は **BcAdminForm**、コンテキストレスは `null`）。`$this->Form->input(...)`→`$this->BcAdminForm->control(...)`。選択肢は `$this->BcAdminForm->getControlSource('Plugin.Model.field')`。
- **【規約】管理画面のフォーム入力は必ず `control()` を使う**: 素のウィジェット（`text()`/`select()`/`hidden()`/`multiCheckbox()`）を直接呼ぶと **bca-* の管理画面スタイルが当たらず崩れる**（例: select が素のドロップダウンに、`multiCheckbox()` は `< class="">` の壊れたラベルを出す）。コアの検索elementと同じく `<span class="bca-search__input-item">` 内に `label()` ＋ `control()` を置く。
- **複数チェックボックスは「ループして各 `control('field[]', type=checkbox)`」（横断対応）**: baser5 に「複数チェックボックスの単一control」は無く、`control(['type'=>'select','multiple'=>'checkbox'])` も `multiCheckbox()` も**崩れる**（空selectや `< class="">`）。コア UserGroups 編集と同様に options をループし `control('Model.field[]', ['type'=>'checkbox','label'=>$label,'value'=>$v,'checked'=>in_array((string)$v,$selected,true),'hiddenField'=>false,'id'=>...])`（`$selected = array_map('strval',(array)$this->getRequest()->getData('Model.field'))`）。条件側は `'Model.field IN' => (array)$values`（T-F 参照）。**`grep -rn "'multiple' => 'checkbox'"` で全件洗って横断対応する**（OPTSが箇所毎に違うので perl 一括ではなく個別 Edit）。**新ヘルパは作らない方針**＝テンプレ内のインライン foreach で実装する。
- **カスタムフォームヘルパは admin では `BcAdminForm` を使う**: 自作 Helper（例 CpmFormHelper の projectPicker）が `$this->BcForm->control(...)` を使うと**フロント用テンプレート**で描画され select 等が崩れる。`protected array $helpers = ['BcAdminForm', 'BcHtml'];` にして `$this->BcAdminForm->control(...)` を使う。
- **`FormHelper::domId()` は CakePHP5 で廃止**: 自作ヘルパが `$this->domId('Model.field')`（4系で 'ModelField' のCamelCase ID生成）を使っていると `Call to undefined method`。`\Cake\Utility\Inflector::camelize(str_replace('.', '_', $fieldName))` で再現する。JSが参照するIDと合わせるため、生成した control に `'id' => $thatId` を明示する。
- **【最重要】control の自動生成 id は「小文字ハイフン」＝旧 baser の CamelCase id と不一致**: CakePHP5 の `control('CpmCommit.period.year')` が吐く id は `_domId()`（`mb_strtolower(Text::slug($name,'-'))`）で **`cpmcommit-period-year`**。4系テンプレの JS は `$("#CpmCommitPeriodYear")` のような **CamelCase id** 前提なので**セレクタが一致せずハンドラが無反応**になる（検索ボタンが無く change 自動 submit のみの画面では「絞り込みを変えても何も起きない」になる）。対策（いずれか）: ①**フォーム委譲で id 依存をなくす**（推奨）`$("#FormId").on("change", "select, input[type=radio]", function(){ $("#FormId").submit(); })`（フォーム id は baser `create()` の `'id'` 指定がそのまま付く）、②各 control に `'id'=>'CamelCase'` を明示して旧 JS に合わせる。
- **検索は GET 送信が基本**: 条件がURLクエリに残りページャ遷移でも保持される。コントローラは条件を **`$this->getRequest()->getQueryParams()`** から読む（`getData()`=POSTには来ない）。`setViewConditions($model)` は GET クエリをセッションに保存し次回以降のリクエストに再注入する（`loadViewConditions`）。
- **入力値の再表示**: `create(null, ...)`（コンテキストレス）だと送信値がフォームに残らない。`create(null, ['type'=>'get', 'valueSources'=>['query','context'], ...])` のように **`valueSources` に `query` を追加**すると、`control('Model.field')` が GET クエリから値を復元して入力欄に再表示する。
- **一覧の一括処理（batch）/一括選択（checkall）も `BcAdminForm`**: 4系の `$this->Form->control('ListTool.batch', ...)`／`$this->Form->button('適用', ...)`／`control('ListTool.checkall', ...)` は `$this->Form`（フロント用）だと bca スタイルが当たらず**素のドロップダウン/ボタンに崩れる**。`$this->BcAdminForm->` に統一（コア一覧 element と同じ）。フィールド名（`ListTool.batch` 等）は既存JS（`jquery.baser_ajax_batch`）が参照するので変えない。
- **control の label に HTML を渡すときは配列形式 `['text'=>'<...>', 'escape'=>false]`**: `control('x', ['type'=>'checkbox', 'label'=>'<span class="bca-visually-hidden">…</span>'])` のように label を**文字列**で渡すと HTML が**エスケープされ生テキスト表示**される（例: 一覧の一括選択チェックボックスの隠しラベル）。`'label' => ['text' => '<span…>…</span>', 'escape' => false]` にする。
- **`$this->FormTable` は CakePHP5 で `MissingHelperException`（`Cpm.FormTableHelper could not be found`）**: 4系テンプレの `$this->FormTable->dispatchBefore()`/`dispatchAfter()` は、未ロードのヘルパ名 'FormTable' として現在プラグインに探しにいく。baser5 コアの登録名は **`BcFormTable`**（`$this->BcFormTable->dispatchBefore()`）。`dispatchAfterForm()` は `BcAdminForm`（`$this->BcAdminForm->dispatchAfterForm()`）。
- **belongsToMany（旧HABTM）の保存は `_ids`**: 4系 `saveAll(['Model'=>[...], 'Assoc'=>['Assoc'=>[id,...]]])` は5系で `patchEntity($e, $data + ['assoc_prop'=>['_ids'=>[$id,...]]], ['associated'=>['Assoc']])` → `save()`。アソシエーション alias が `User`（単数）なら patch のキーは小文字スネーク `user`、`['associated'=>['User']]`。GET 表示は `contain(['User'])` して `array_map(fn($u)=>$u['id'], $e->user)` で選択IDを取り出し、メンバーは複数チェックボックスのループ（C-G の checkbox ループ参照）で `checked`。
- **`SELECT *` 結合のUNIONは派生テーブル化できない**: 生SQLのページネーションで `SELECT COUNT(*) FROM (<unionSql>) t` のように**サブクエリ化すると重複カラム名（id等）で `SQLSTATE[42S21] Duplicate column name`** になる。総件数は `<unionSql>` を実行して `count(fetchAll('assoc'))`、データは `<unionSql> . ' ORDER BY ... LIMIT n OFFSET m'` のように**末尾へ直接付与**する（サブクエリで包まない）。
- **生SQLのカスタムページネーション（UNION等）**: 4系は対象モデルで `paginate()`/`paginateCount()` を `func_get_arg` でオーバーライドしていたが、**CakePHP5 では呼ばれない（廃止）**。コントローラで手動ページネーションする:
  1. 検索SQL（`buildSearchQuery`）を生成し、`SELECT COUNT(*) FROM (<sql>) t` で総件数、`SELECT * FROM (<sql>) t ORDER BY ... LIMIT n OFFSET m` で当ページ行を `getConnection()->execute()->fetchAll('assoc')`。
  2. `new \Cake\Datasource\Paging\PaginatedResultSet(new \ArrayIterator($rows), $params)` を作り `$this->set('datas', $datas)`。PaginatorHelperはビューのvarsからPaginatedInterfaceを**自動検出**する。
  3. `$params` のキーは NumericPaginator 準拠（`alias,count,totalCount,perPage,limit,page,currentPage,pageCount,start,end,hasPrevPage,hasNextPage,sort,sortDefault,direction,directionDefault,completeSort,scope`）。sort はホワイトリストで `ORDER BY` を組み立て（SQLインジェクション防止）。
  - テンプレート側は4系の `query()` ネスト形 `$data[0]['col']` → `fetchAll('assoc')` のフラット形 `$data['col']` に直す。

### C-I. 編集/登録フォーム画面（edit/add）の共通移行パターン
4系の edit/add は `$this->Model->read(null,$id)` や `$this->Model->find('first',...)` で取得→`set()`→`save()` し、テンプレは `['Model'=>[...], 'Assoc'=>[...]]` の4系ネスト配列を前提とする。5系では次の定型で移行する（CpmBillings/CpmPayments/CpmProjects/CpmPractices で実績）:
- **GET（表示）**: `$e = $table->find()->where(['Models.id'=>$id])->contain([...])->first(); if(!$e) $this->notFound();` → **エンティティを4系互換ネスト配列へ整形**する `convertXxxToFormData($e)` を用意し `$this->setRequest($this->getRequest()->withParsedBody($formData))`。整形は `belongsTo`(単数 snake→`CpmProduct`等)、`hasMany`(複数 snake→`CpmSale`等)を4系キーへマップ。
- **日付/日時カラムの入力欄対策（重要）**: エンティティの `Date`/`DateTime` を withParsedBody でそのまま渡すと、テキスト入力欄に `2026-02-28 00:00:00` と**時分秒付き**で出る。convert 時に本体カラムを走査して日付オブジェクトを文字列化する: `foreach($cols as $k=>$v){ if(is_object($v)&&method_exists($v,'format')) $cols[$k]=$v->format('Y-m-d'); }`。
- **POST（保存）**: `$validData = $table->convertRecordFormToDb($this->getRequest()->getData());`（4系の整形メソッドはそのまま流用可）→ `$entity = !empty($validData['Model']['id']) ? $table->get($id) : $table->newEmptyEntity(); $entity = $table->patchEntity($entity, $validData['Model']); $table->save($entity);`。成功後 `$this->redirect([...]); return;`。
- **テンプレ**: `$this->Form->create('Model')`→`$this->BcAdminForm->create(null, ['valueSources'=>['data','context']])`（C-0参照）、`$this->Form->value('Model.x')`→`$this->getRequest()->getData('Model.x')`、`$this->getRequest()->pass[1]`→`$this->getRequest()->getParam('pass')[1] ?? null`、`$this->Form->`→`$this->BcAdminForm->`。
- **未移行の深い副作用は TODO で保留**: `afterSave()` の関連集計更新、Slack通知（コンポーネント未移行）、`isLastData()` 等の4系メソッドは、表示・基本保存を優先し TODO マーカーで残す（その機能を使う段で個別移行）。
- **【保存時の落とし穴】カスタムバリデーションルールは「保存しないと走らない」ので移行漏れに気づきにくい**: `validationDefault()` で `$validator->add('field','rule',['rule'=>[$this,'customMethod']])` 登録されたメソッドが 4系ORM（`$this->field(...)`/`$this->CpmProject->find('first',...)`）のままだと、GET表示は通るのに **POST保存時に fatal**。GET だけ確認して「OK」としない。ルール本体を5系化する（`$this->find()->select([...])->where([...])->first()`、関連は `TableRegistry::getTableLocator()->get('Plugin.Models')`、旧 afterFind 由来の計算値が要るなら `calcBalance()` 等の明示計算メソッドを呼ぶ＝C-B）。`$context['data']['id']` 等は名前付きで防御的に取得。
- **既存の移行済みメソッドを再利用する**: 4系の複雑なクエリ（例 import のCPM利用ユーザー一覧 = MainUser+CpmUserSetting結合+getUserName）を再移行せず、既に5系化済みの同等メソッド（例 `CpmProjects::getControlSource('user_id', ['mode'=>'add'])`）に置き換えると安全・速い。
- **編集画面に同居する「関連レコード一覧」は明示 contain で再取得**: 4系は `find('first', ['recursive'=>2])` でプロダクト＋紐づくプロジェクト等を一括取得し、テンプレが `getData('CpmProject')`（関連配列）で一覧描画していた。5系移行で本体だけ `withParsedBody(['CpmProduct'=>$cols])` にすると**関連一覧が空で描画されない**（エラーは出ない＝気づきにくい）。コントローラで関連を別途取得して set し、テンプレを `$projects`（エンティティ）参照に直す: `$this->set('projects', $this->fetchTable('Cpm.CpmProjects')->find()->where(['CpmProjects.product_id'=>$id])->contain(['MainUser','SubUser'])->all()->toArray())`。テンプレの担当者列は関連参照（`$p->main_user->real_name_1 ?? ''`）、ステータス配列は `$arr[$p->cd] ?? ''` でガード。
- **`BcBaser->link()` に画像/HTML を渡すときは `['escape' => false]`**: `link($this->BcBaser->getImg(...), $url)` のように title がHTMLだと、デフォルトでエスケープされ `<img ...>` が**生テキスト表示**される。`['escape' => false]` を付ける。
- **4系の管理画面アセット画像（`admin/btn_add.png` 等）は baser5 に存在せず 404**: `getImg('admin/btn_add.png')` は `/<subdir>/img/admin/btn_add.png` を指すが無い（alt文字やリンク切れ表示）。**baser5 のアイコンボタンに置換**する: `$this->BcBaser->link('', $url, ['title'=>'新規追加','class'=>'bca-btn-icon','data-bca-btn-type'=>'add','data-bca-btn-size'=>'lg'])`（行の編集/削除アイコンと同じ作法）。

### C-J. `getControlSource` 等で `disableHydration` を使うとフラット配列で返る
Table 内のリスト生成で `find()->select([...])->disableHydration()` を使う場合、結果行は**フラット連想配列**（`$row['Table']['field']` ではない）。`select(['CpmProjects.id'])` のキーは曖昧になりがちなので、**明示エイリアス**を付ける: `->select(['id'=>'CpmProjects.id', 'name'=>'CpmProjects.name', 'product_name'=>'CpmProducts.name'])` → `$row['id']`/`$row['name']`/`$row['product_name']` でフラット参照。関連列で WHERE 絞り込みしつつ関連無しも含めたいときは `leftJoinWith('CpmProducts')`（`contain` は WHERE 不可）。

### C-H. 一覧 element をフォームに埋め込むと PaginatorHelper が落ちる
一覧用 element（`Paginator->sort()` や `element('pagination')`/`list_num` を含む）を、**ページネーションしない画面（編集フォーム等）に流用**すると `You must set a pagination instance using setPaginated() first`（`Cake\Core\Exception\CakeException`、`PaginatorHelper->paginated()` 由来）。4系の PaginatorHelper は未ページネーションでも黙って描画したが、**CakePHP5 はビューのvarsに `PaginatedInterface` が1つも無いと throw**。
- これらの一覧 element は「専用一覧画面（=ページネーションあり）」と「フォーム埋め込み（=related配列を全件表示・ページャ不要）」の双方で使い回されている。**`element('pagination')`/`element('list_num')`/`Paginator->sort()` を、専用一覧コントローラのときだけ描画するようガード**する: `<?php if ((($this->getRequest()->getAttribute('params')['controller'] ?? '') === 'CpmCosts')) $this->BcBaser->element('pagination') ?>`。pagination 行も本体テーブル分岐と同じ条件に揃える。埋め込み専用 element（例 `index_list_fixed`）はそもそもページャ不要なので同様にガード（実質非表示）。
- **【最重要の落とし穴】`getParam('controller')` / `params['controller']` は CamelCase**: CakePHP5 では controller パラメータは**クラス名形の CamelCase（`'CpmCosts'`/`'CpmEstimates'`）**で返る。4系からの変換で **`=== 'cpm_costs'`（アンダースコア）** のまま残っていると比較が**常に false** になり、一覧 element が「フォーム埋め込み（=リクエストデータ参照）」側の分岐に落ちて、**画面はエラーなく開くのにデータが出ない**（合計だけ出てリストが空、等の不可解な症状）。一覧の分岐・行描画・pagination ガードはすべて `=== 'CpmCosts'` に直す。`grep -rn "=== 'cpm_" templates` で全件洗う。

### C-E. 依存プラグインも芋づる式に必要
`contain()` する関連の所属プラグイン（例 Cpm→Cards）が無効だと `Table class for alias 'Cards.CardsCompanies' could not be found`。**依存プラグインを有効化（status=1）し、テーブルを4系から作成**する（[[catchup-portal-v4-to-v5-migration]] 参照）。依存プラグイン側のイベントリスナー等が未移行だと warning が出るが、対象画面の表示自体は止まらないことが多い（依存プラグイン本体の移行時に対応）。

## フロント表示エラーの実例パターン（症状 → 原因 → 修正）

> テーマ／プラグインのフロント表示を復旧させる際に頻出する実例。`BcAddonMigrator` で自動変換した後も残る典型的な不具合。**エラーメッセージで検索して該当パターンに当てる**こと。1つ直すと次のエラーが現れる「玉ねぎ剝き」になるため、`curl -sk -o /dev/null -w "%{http_code}" <URL>` でステータスを見ながら1件ずつ潰す。

### F-1. `MissingTableClassException: Table class for alias 'Content' could not be found.`
4系流の `TableRegistry::getTableLocator()->get('Content')`（単数形・プラグイン接頭辞なし）が原因。
- **修正**: コアテーブルは**複数形＋プラグイン接頭辞**にする。`get('Content')` → `get('BaserCore.Contents')`、`get('Site')` → `get('BaserCore.Sites')`。あわせて `find('first', ['conditions'=>..., 'recursive'=>-1])` はクエリビルダー `find()->where($conditions)->first()` に、エンティティアクセスは `$content['Content']['id']` → `$content->id` に変更する。`where()` の条件キーも `'site_id'` → `'Contents.site_id'` のように **テーブルエイリアス付き**にする。

### F-2. 現在のコンテンツ／サイト情報の取得（リクエスト属性の変更）
4系の `$this->request->params['Content']['site_id']` / `params['Site']['device']` は廃止。
- **修正**: `$this->getView()->getRequest()->getAttribute('currentContent')`（コンテンツのエンティティ）/ `getAttribute('currentSite')`（サイトのエンティティ）を使い、`->id` `->site_id` `->device` 等のプロパティで参照する。

### F-3. コアプラグインの参照名に `Bc` 接頭辞（Helper・テーブル・エレメント共通）
URL配列だけでなく、**ヘルパー読み込み・テーブルエイリアス・エレメントパス**でもコアプラグインは `Bc` 接頭辞が必要。`Blog` → `BcBlog`、`Mail` → `BcMail` 等。
- 例: `loadHelper('Blog.Blog')` → `loadHelper('BcBlog.Blog')`、`get('Blog.BlogContents')` → `get('BcBlog.BlogContents')`、`getElement('Blog...')` → `getElement('BcBlog...')`。

### F-4. ヘルパーの手動インスタンス化（`new XxxHelper(new BcAppView())`）
4系流の `$this->Blog = new BlogHelper(new BcAppView());` は5系では動かない（`BcAppView` 廃止・DIコンテナ未注入で `getService()` が失敗する）。
- **修正**: `$this->Xxx = $this->getView()->loadHelper('Plugin.Helper');` でロードして使う。`loadHelper()` はインスタンスを返す。

### F-5. `League\Container\Exception\NotFoundException: Alias (...ServiceInterface) is not being managed` ／ `MissingHelperException`
フロントが依存するプラグイン（例: BcBlog）が **plugins テーブルで `status=0`（無効）** だと、そのプラグインの ServiceProvider がコンテナに登録されず、Helper コンストラクタの `getService(XxxServiceInterface::class)` で落ちる。
- **判別**: `SELECT name, status FROM plugins;` で対象プラグインの status を確認。
- **修正**: v4 で使っていたプラグインを**有効化**する（正攻法は管理画面のプラグイン管理。データ移行済みでテーブルが揃っているなら `UPDATE plugins SET status=1 WHERE name='BcBlog';` でも可）。有効化後は `bin/cake cache clear_all`。
- 補足: コンテナに無いサービスへ依存しないコードに書き換える手もある（例: テンプレートパス決定はサービスを介さず `TableRegistry` でブログコンテンツを引いて `'BcBlog...' . DS . 'Blog' . DS . $contentsTemplate . DS . $template` を組み立てる）。

### F-6. プラグインの `config/routes.php` が CLI を巻き込んで全コマンドを壊す
`$request = \Cake\Routing\Router::getRequest();` は **CLI 実行時に null** を返すため、直後の `$request->getPath()` で `Error: Call to a member function getPath() on null` となり、`bin/cake` が一切動かなくなる（ルート読込は CLI でも走るため）。また `Router::connect()` は **CakePHP 5 で廃止**。
- **修正**: 先頭で `if (!$request) return;` のnullガードを入れ、ルート定義は RouteBuilder の `$routes->connect(...)` に変更する（plugin の routes.php には `$routes` がスコープに渡る）。コントローラー名は CamelCase（`onemind_files` → `OnemindFiles`）。

### F-7. `MissingRouteException: /files/... could not be found`（アップロードファイル未移行）
`BcDbMigrator`／データ復元はコア系の `files/` サブディレクトリ（blog・contents・editor・mail・theme_configs・uploads 等）しか作らないことがあり、**プラグイン独自のアップロードディレクトリ（例: `onemind_configs`）が欠落**する。物理ファイルが無いと `/files/...` がルートにフォールバックして例外になる。
- **修正**: 4系の `files/`（または `app/webroot/files/`）全体を `v5/webroot/files/` に**マージコピー**する。`rsync -a files/ v5/webroot/files/`。

### F-8. `Call to undefined method`（`BcAddonMigrator` のメソッド移植漏れ）
`BcAddonMigrator` は Helper 等のクラスを変換するが、**一部メソッドが移植されずに欠落**することがある（例: `isTopMainVisualUseBanner()` `inPlugin()` `react()`）。テンプレートから呼ばれて `Call to undefined method` になる。
- **修正**: 4系と5系のクラスのメソッド一覧を `grep -nE "function "` で**差分比較**し、欠落メソッドを5系記法に直して手動移植する。
    *   `getEnablePlugins()`（グローバル関数）→ `\BaserCore\Utility\BcUtil::getEnablePlugins()`。戻り値は **Plugin エンティティの配列**なので、`Hash::extract($plugins, '{n}.name')` で名称を取り出す（4系の `'{n}.Plugin.name'` ではない）。

### F-10. ショートコードが実行されず `[Plugin.method ...]` の生テキストのまま残る
ショートコードは各プラグインの `config/setting.php` の `'BcShortCode' => [...]` で登録されるが、**プラグインが無効（plugins.status=0）だと setting.php が読み込まれず未登録**になる。BcShortCode は未登録のショートコードを**エラーを出さずそのまま生テキストで出力**するため、画面に `[CatchupPortal.showSearchBox]` `[Cpm.unitChart]` 等が露出する。
- **判別**: 露出しているショートコードの接頭辞（`[Plugin.xxx]` の Plugin 部分）のプラグインの status を確認。
- **修正**: 当該プラグインを移行・有効化する（F-5参照）。未移行のうちは生テキストのまま残るのは想定どおり。

### F-11. フロントのヘッダー／メニューが「ログイン時のみ」描画される
ポータル系テーマでは、ヘッダーのナビ・ユーザーメニュー・アプリランチャー等が `if(!empty($user))` で囲まれ、**未ログインだとロゴ＋サイト名しか出ない**ことがある。これは仕様。さらにヘッダーは `CuAppLauncher` `CuCustomUser` `CuTimeCard` 等のプラグインに依存するため、それらが無効だと該当ブロックが空になる。
- **確認のコツ**: 「ヘッダーが出ない」をコードのバグと早合点しない。まず**フロントにログイン**し、依存プラグインの有効化状況（F-5）を確認する。

### F-12. アイキャッチ等の画像404はDBと物理ファイルの不一致のことがある
`/files/blog/.../xxxx_eye_catch.jpg` の `MissingRouteException` が大量に出ても、**該当画像が移行元（4系）のローカルにも存在しない**場合がある（本番サーバーにのみ実体があり、ローカルDBダンプだけ持ってきた等）。移行コードの不具合ではない。
- **判別**: 移行元の `files/`（または `app/webroot/files/`）に実体があるか確認。古い年月の画像が表示でき新しい年月だけ404なら、ローカルに実ファイルが無いだけ。

### F-13. フロントのテーマで `$user` 等のビュー変数が空になりヘッダー等が欠ける
4系では core がフロントの全ビューに `$user`（ログインユーザー）等を供給していたが、**5系はこれをフロントに供給しない**（`AppService::getViewVarsForAll()` の `loginUser` / `currentUserAuthPrefixes` 等は**管理画面レイヤー専用**）。そのためテーマのエレメント（例: `templates/element/header.php`）が `$user` を参照していると、ログイン中でも未ログイン扱いになり `if(!empty($user))` 配下（ナビ・ユーザーメニュー等）が丸ごと描画されない。
- **修正**: エレメント／レイアウトの先頭で必要な変数を自前で導出する。
  ```php
  $user = \BaserCore\Utility\BcUtil::loginUser();                       // UserInterface|false
  $currentPrefix = \BaserCore\Utility\BcUtil::getRequestPrefix($this->getRequest());
  $currentUserAuthPrefixes = $user ? $user->getAuthPrefixes() : [];     // User エンティティの getAuthPrefixes()
  ```
- **重要（$this の違い）**: **エレメント／レイアウト内では `$this` は View 自身**なので `$this->getRequest()` を使う。`$this->getView()` は **Helper 内**のメソッドで、View では `Call to undefined method ...View::getView()` になる。
- あわせて 4系の `$this->Session->...`（SessionHelper は CakePHP 5 で廃止）は `$this->getRequest()->getSession()->...` に変更する。
- 補足: フロント（prefix=`Front`）の `BcUtil::loginUser()` はフロントセッション→`getLoggedInUsers()` の順で解決するため、管理画面にログインしていればフロントでもユーザーが取得できる。

### F-14. `BcBaser->css()` / `js()` の第2引数が `$inline` に変わり `media`/属性指定が無視される
4系の `css($path, $options = [])` は第2引数が options（`['media' => 'print']` 等）だったが、**5系は `css($path, $inline = true, $options = [])`**（`js()` も同様）。4系流に `$this->BcBaser->css(['print'], ['media' => 'print'])` と書くと、第2引数の配列が `$inline` と解釈され、**`media="print"` が出力されない**。結果、印刷専用CSS（`.no-print { display:none }` 等）が**画面にも適用され、ヘッダー等が消える**という分かりにくい症状になる。
- **症状**: 「ヘッダー/要素が表示されない」が、HTMLには出力されている（DevTools で `display:none` が print.css 由来）。`<link ... print.css>` に `media="print"` が無い。
- **修正**: 第2引数に `$inline`（通常 `true`）を入れ、属性は第3引数へ。
  ```php
  // Before → After
  $this->BcBaser->css(['print'], ['media' => 'print']);
  $this->BcBaser->css(['print'], true, ['media' => 'print']);
  ```
- レイアウト／エレメント内の **全 `css()`/`js()` 呼び出し**を確認し、第2引数に配列で options を渡している箇所を直す。

### F-15. `Attempt to read property "X" on array`（`request->params['Content'][...]` の変換ミス）
4系の `$this->request->params['Content']['entity_id']` / `['name']`（現在のコンテンツ情報）を、`BcAddonMigrator` が `$this->getRequest()->getAttribute('params')->entity_id` のように**配列を誤ってオブジェクト矢印アクセスに変換**することがある。`getAttribute('params')` は **routing パラメータの配列**なので `->entity_id` は `Attempt to read property "..." on array` 警告になる。
- **修正**: 現在のコンテンツ情報は `getAttribute('currentContent')`（Content エンティティ）から取る。
  ```php
  // Before（4系）: $this->request->params['Content']['entity_id'] / ['name']
  $currentContent = $this->getRequest()->getAttribute('currentContent');
  $currentContent->entity_id;   // ブログコンテンツID 等
  $currentContent->name;        // コンテンツ名（URLセグメント）
  ```
- なお `getAttribute('params')` 自体は配列のままなので、`['pass'][1]` 等の**配列アクセスは正しい**。`isset($this->getRequest()->getAttribute('params')['pass'][1])` のような**メソッド呼び出し結果への isset は不可**（`Cannot use isset() on the result of an expression`）。一旦 `$params = $this->getRequest()->getAttribute('params');` に受けてから `$params['pass'][1] ?? ''` とする。

### F-16. ビューでの `$this->getRequest()->url` は廃止
4系の `$this->request->url`（先頭スラッシュ**なし**）は5系で廃止。`$this->getRequest()->getPath()`（先頭スラッシュ**あり**）を使う。4系で `'/' . $request->url` としていた箇所は、`getPath()` が既にスラッシュ付きなので**先頭の `'/' .` を除去**する（F-2 と同根。コントローラだけでなくテーマのエレメントにも残りがち）。

### F-17. メールフォーム（BcMail）テーマテンプレートの移行
メールフォームのコンテンツ（type=`MailContent`, plugin=`BcMail`）を表示すると、まず BcMail 無効で `MissingControllerException`（コントローラ空・F-5）になり、有効化後もテーマの mail テンプレートが4系の `$this->Mailform->...` API のままで複数段階のエラーになる。コア（`vendor/baserproject/bc-front/templates/plugin/BcMail/`）の5系版を正解として合わせる。
- **(a) フォーム生成**: `No context provider found for value of type 'string'`。`$this->Mailform->create('MailMessage', $opts)` → `$this->BcBaser->createMailForm($mailMessage, array_merge($opts, ['valueSources' => ['context']]))`。第1引数は**エンティティ `$mailMessage`**（コントローラが view にセット）。
- **(b) フォーム系ヘルパーは `$this->BcBaser->...MailForm...` 族へ**（`BcMailBaserHelper::methods()` が `Mailform` に橋渡しし、`MailMessage.` 接頭辞の文脈を内部処理する）。フィールド名から **`MailMessage.` 接頭辞を除去**する。
  | 4系 | 5系 |
  |---|---|
  | `$this->Mailform->hidden('MailMessage.mode')` | `$this->BcBaser->mailFormHidden('mode', ['id' => 'MailMessageMode'])` |
  | `$this->Mailform->unlockField('MailMessage.mode')` | `$this->BcBaser->unlockMailFormField('mode')` |
  | `$this->Mailform->submit($cap, $opt)` | `$this->BcBaser->mailFormSubmit($cap, $opt)` |
  | `$this->Mailform->error('MailMessage.x', $msg)` | `$this->BcBaser->mailFormError('x', $msg)` |
  | `$this->Mailform->authCaptcha('MailMessage.auth_captcha')` | `$this->BcBaser->mailFormAuthCaptcha('auth_captcha', ['helper' => $this->BcBaser])` |
  | `$this->Mailform->end()` | `$this->BcBaser->endMailForm()` |
  | `$this->Mailform->freeze()`（confirm.php） | `$this->BcBaser->freezeMailForm()` |
  - `mailFormHidden('mode')` は素のフィールド名だと id が `mode` になるため、JSが `#MailMessageMode` を参照している場合は `['id' => 'MailMessageMode']` を明示する。
- **(c) ファイルフィールドの BcUpload テーブル指定（重要・忘れやすい）**: file 型フィールドがあると `BcUploadHelper を利用するには … table … を指定してください`（`BcException`）になる。コアの `Mail/default/index.php` と `confirm.php` は先頭で **`$this->BcBaser->setTableToUpload('BcMail.MailMessages');`** を呼んでおり、テーマ override 版にこの1行が無いと落ちる。フォーム／確認画面を描画する各テンプレートに追加する（`setTableToUpload` は `BcUpload::setTable` のエイリアス）。
- mail フィールドを描画する `element('mail_input')` はコア版（`bc-front/.../BcMail/element/mail_input.php`）にフォールバックするので、テーマに mail_input override が無ければ触らなくてよい。

### F-18. 横長テーブル（多数列）で「ページ全体が横スクロール」する＝flex の `min-width:0` 欠落
管理テーマ bc-admin-third のレイアウトは `.bca-container { display:flex }`＋`.bca-main { flex-basis:100% }` だが **`.bca-main` に `min-width:0` が無い**。flex アイテムの既定 `min-width:auto` のため、内容（多数列のワイドなテーブル等）より縮まず `.bca-main` が内容幅まで伸び、**ページ全体に横スクロールが出る**（中央寄せの保存ボタンが右へずれる、等）。子要素に `overflow:auto` を付けても、親 flex アイテムが伸びるため効かない。
- **症状の出方**: 4系では出なかった／データが入って初めて顕在化（移行直後はデータ未取得で列が無く気づかない）。月別カラムが現在日付基準で増える画面（工数シミュレーション等）で再現しやすい。
- **修正（コアテーマ非改変）**: その画面のCSS（Vueバンドルの非scoped `<style>` 等）に **`.bca-main { min-width: 0; }`** を足し、テーブルを**スクロールラッパー**で囲って `overflow:auto !important; max-width:100% !important; width:100%` を与える。これで `.bca-main` が画面幅に収まり、テーブルはラッパー内で横スクロール（`position:sticky` の列固定も機能する）。CSSを直したら **webpack 再ビルド**（バンドルCSSは `MiniCssExtractPlugin` で `../css/[name].bundle.css` に出力）。

### F-9. ブログ記事のソート列 `posts_date` は5系に存在しない
`SQLSTATE[42S22]: Unknown column 'BlogPosts.posts_date'`。5系の `blog_posts` の投稿日時カラムは **`posted`**。
- **修正**: ソート指定を `'sort' => 'posted'` にする。カラム名は移行先DBの `SHOW COLUMNS FROM blog_posts;` で確認するのが確実。

## テーマ（Theme）固有の移行パターン（T-/C-系との違い）

> **テーマは Controller/Table を持たない**（プラグインと構造が根本的に異なる）ため、上記 T-（Table/ORM）・C-（Controller/画面）カタログは基本的に適用外。テーマの主体は `templates/layout` `templates/element` `templates/Blog` `templates/Pages` `templates/Mail` と、少数のフロント表示ロジック用 `src/View/Helper/*.php`。**F-系（フロント表示エラー）が主対象**だが、テーマ特有の"消えたショートカット系ヘルパーメソッド"の当たり方がプラグインと異なるため、以下に固有パターンとしてまとめる（`basercms4-to-5-upgrade` の「テーマの変換」章から呼ばれる想定）。

### TH-1. `$this->BcBaser->siteConfig['key']` / ビュー変数 `$siteConfig['key']` は5系に存在しない
4系は `BcBaserHelper` に `siteConfig` プロパティがあり、また `AppController` が全ビューへ `$siteConfig` をグローバル変数としてセットしていたが、**5系の `BcBaserHelper` に `siteConfig` プロパティは存在せず**、`$siteConfig` もビューへ自動セットされない。テンプレート内で `$siteConfig['formal_name']`／`$this->BcBaser->siteConfig['address']` のような配列アクセスをそのまま残していても構文エラーにはならず、**未定義変数／未定義プロパティの警告つきで空文字が出力される**だけなので気づきにくい（サイト名・住所等が黙って空になる）。
- **修正**: `\BaserCore\Utility\BcSiteConfig::get('address')` のように**静的メソッド**へ置換する。取得できるキーは `formal_name`／`address`／`name`／`google_maps_api_key` 等、4系 `site_configs` テーブルの `name` 列と同じ。
- **横断対応**: 1箇所で見つけたら `grep -rn "siteConfig\[" templates/ src/` でテーマ全体を洗い、layout・element・Helper 全てに機械的に適用する（本パターンは `default.php` の1箇所から発見し、同テーマ内の `google_maps.php` 3箇所へ横展開した実例あり）。

### TH-2. グローバル関数 `fullUrl()` は5系に存在しない
4系はグローバル関数 `fullUrl($url)` が使えたが、5系には存在しない（`Call to undefined function fullUrl()` でFatal）。
- **修正**: `\BaserCore\Utility\BcUtil::fullUrl($url)`（静的メソッド）に置換する。

### TH-3. 4系専用 Component をテーマ Helper が直接インスタンス化しているコードは即Fatal — 先に baser-core に同等機能が移植されていないか確認する
4系のテーマ独自 Helper が `new XxxComponent(new ComponentCollection())` のように CakePHP2 の Component を直接生成しているコードが、`BcAddonMigrator` の機械変換後もそのまま残ることがある。`Component` 基底クラスも `ComponentCollection` も5系に存在しないため**呼び出された瞬間にFatal**（F-4「ヘルパーの手動インスタンス化」と同根だが、対象が Component である点が異なる）。
- **対処の順序**: いきなり自前で再実装しない。**まず `grep -rli "<機能キーワード>" vendor/baserproject/` で baser-core／該当コアプラグインに同等のユーティリティが既に移植されていないか確認する**。baserCMS5 は主要な4系専用ロジック（Google Geocoding 連携など）を `BaserCore\Utility\*` や `BaserCore\View\Helper\*` として先に移植済みのことが多い。
- **実例**: テーマの `BcGooglemapsHelper::getLocation()` が `new BcGmapsComponent(new ComponentCollection())`（4系 `lib/Baser/Controller/Component/BcGmapsComponent.php`。中身は Google Geocoding API への XML リクエスト）を呼んでいた。調査の結果、baser-core に **`BaserCore\Utility\BcGmaps::getLocation($address)`**（`Cake\Http\Client` でのHTTPリクエスト化＋`Cache` によるキャッシュ付き、戻り値 `['latitude'=>..,'longitude'=>..]` は4系と同一形）が既に存在した。テーマ側は独自の `load()`/`_getScript()` インターフェースは残しつつ、`getLocation()` の中身だけをこのコアユーティリティへの委譲に差し替えるだけで解決した（自前でのHTTPクライアント実装・XMLパース実装は不要だった）。
- **見落としがちな呼び出し元**: Component 呼び出しは Helper 内に隠れているため、要素テンプレート側からは「単なるメソッド呼び出し」にしか見えない。要素テンプレートで実際にそのメソッドへ到達する条件（デフォルト引数のときだけ呼ばれる、等）を先に読み解いてから Helper 側の実装を追う（本例では「緯度経度が明示指定されていないときだけ `loadLocation()`→`getLocation()` に到達する」という条件分岐を確認してから着手した）。

### TH-4. `BcBaserHelper` の一部4系専用ショートカットメソッドは5系で完全廃止・代替APIも無い（layoutに潜みやすい）
4系の `BcBaserHelper` にあった `blogPosts($blogName, $num)` のようなフロント用ショートカットメソッドは、5系の `baser-core` は元より `bc-blog` 等どのプラグインにも移植されていない（`grep -rln "function blogPosts" vendor/baserproject/` で0件＝TH-3の「コアに移植済み」パターンとは違い、**本当に無い**）。呼び出すと「メソッドが存在しない」系のFatalになる。
- **見つけ方**: テーマの `templates/layout/*.php` は目立たない1行呼び出し（`$this->BcBaser->blogPosts('works', 8);` 等）でブログ記事一覧を差し込んでいることが多く、element監査だけでは見落としやすい。**layout ファイルも element と同様に全文を読み、`$this->BcBaser->` に続くメソッド名を一つずつ `grep -rln "function <名前>"` で baser-core・関連プラグインに実在するか裏取りする**。
- **修正方針**: 代替APIが無いため、`BlogPostsTable` のクエリビルダで手動再実装するしかない（`getConditionAllowPublish()` ＋ `innerJoin` で対象ブログの `Contents.url` を絞り込み、`orderBy(['BlogPosts.posted'=>'DESC'])->limit($num)` が基本形）。同種の呼び出し（例: 「works」用と「news」用など複数箇所）が散在する場合は、都度インラインで書かず、テーマの `ThemeHelper` 等に共通メソッドとして1つに集約するとよい。
- **重複実装との突合を忘れない**: 移行済みの他要素（例: 「works」ブログ一覧を独自に querybuilder で実装した `element/top_works.php`）と機能が重複していないか確認する。重複していた場合、どちらが正のロジックかはコードだけでは判断できないことが多いため、**ユーザーに確認してから**一本化する（横断で無理に実装せず確認を挟む、という運用原則をここでも適用する）。

### TH-5. layout/element から他プラグインの Helper を直接呼ぶコードは「テーマのバグ」と「依存プラグイン未導入」を区別する
テーマの layout やエレメントが `$this->Banner->showBanner(...)` のように**別プラグインが提供する Helper** を直接呼んでいることがある。移行作業を `フェーズ3.5（棚卸し）` → `フェーズ4-(b)（テーマ/プラグイン機械変換）` → `フェーズ4-(a)（マーケット配布の5系プラグイン導入）` のように段階分けしている場合、その依存プラグインがまだ導入されていない段階でテーマ側だけ見ると `Undefined property: $this->Banner`（Helper未ロード）に見えるが、**これはテーマのコードの不具合ではなく、依存プラグインのフェーズがまだ来ていないだけ**のことがある。
- **対処**: 台帳では「未着手／移行済」ではなく **「見送り（依存プラグイン未導入・フェーズ4-(a)待ち）」**のように区別して記録し、テーマ単体の完了条件に含めない。該当プラグインの導入フェーズが終わった後で改めて動作確認する。
- 呼び出し元のメソッド名・期待するオプション（`['num' => 0]` 等）だけは、後で突き合わせられるよう台帳にメモしておく（導入される5系版プラグインでシグネチャが変わっている可能性があるため）。

