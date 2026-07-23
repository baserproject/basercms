---
name: basercms5-theme-development
description: 'baserCMS 5系（CakePHP 5ベース）のテーマを新規開発・改修する際の正本パターン集。「テーマを作成」「テーマプラグインの構造」「layout/element の書き方」「固定ページテンプレート（$page->contents）」「ウィジェット element の配置（element/widget）」「メールフォームテンプレート（createMailForm / mailFormControl 等 BcBaser の mailForm 系）」「ブログテンプレート（posts/single・getEyeCatch・posted）」「BcBaser ヘルパの委譲機構（methods()）」「css()/js() の $inline」「getUrl でのURL生成」「currentContent/currentSite」「BcUtil::loginUser」「BcSiteConfig::get」「テーマの webroot アセット配信」等で参照する。共通ルールは basercms5-development、プラグイン内部コード（Controller/Table）は basercms5-plugin-development、4系テーマの移行は basercms-theme-4-to-5-upgrade を参照。'
license: MIT
---

# baserCMS 5系 テーマ開発パターン集

本スキルの対象は baserCMS 5系（CakePHP 5ベース）における**テーマの新規開発・改修**。5系のテーマは**プラグイン形式**であり、`plugins/<ThemeName>/` に配置してテンプレート（`templates/`）・アセット（`webroot/`）・少数のフロント表示用 Helper（`src/View/Helper/`）で構成する。環境・ディレクトリ・命名・コアハック禁止などの共通ルールは **basercms5-development**、テーマに Controller/Table などプラグイン相当の内部コードを持たせる場合は **basercms5-plugin-development**、4系テーマからの移行作業は **basercms-theme-4-to-5-upgrade** を参照する。

## 1. テーマプラグインの構造

テーマは以下のディレクトリ構成で作る（コアの参考実装: `vendor/baserproject/bc-front/templates/`、サンプル: `plugins/BcThemeSample/`）。

```
plugins/<ThemeName>/
├── config.php                  … テーマ情報（return 配列。'type' => 'Theme' 必須）
├── src/<ThemeName>Plugin.php   … プラグインクラス（bake 生成の雛形でよい）
├── src/View/Helper/            … テーマ独自のフロント表示用 Helper（任意）
├── templates/
│   ├── layout/                 … レイアウト（default.php 等）
│   ├── element/                … 共通エレメント（header.php / footer.php 等）
│   ├── element/widget/         … ウィジェット用 element（§5）
│   ├── Pages/                  … 固定ページテンプレート（default.php 等）
│   ├── Blog/<content>/         … ブログテンプレート（posts.php / index.php / single.php / archives.php）
│   └── Mail/<content>/         … メールフォームテンプレート（index.php / confirm.php / submit.php）
└── webroot/                    … css / js / img（テーマアセット）
```

- `config.php` は `return ['type' => 'Theme', 'title' => ..., 'description' => ..., 'author' => ..., 'url' => ...];` の形式で書く。
- レイアウト `templates/layout/default.php` では本文を `$this->fetch('content')` で出力し、`<head>` 内は `$this->BcBaser->charset()` / `title()` / `metaDescription()` / `css()` / `js()` / `scripts()`、`</body>` 直前で `$this->BcBaser->func()` 等の BcBaser API を使って組み立てる。
- 固定ページテンプレート `templates/Pages/default.php` は、管理画面で編集された本文を `echo $page->contents` で出力する（`$page` は Page エンティティ）。
- コンテンツ別テンプレは `templates/Blog/<コンテンツのテンプレート名>/posts.php` のように「機能プラグイン名/テンプレート名/アクション名」で配置し、管理画面のコンテンツ設定で選択されたテンプレート名のディレクトリが使われる。テーマに無いテンプレートはコア（`bc-front/templates/plugin/BcBlog/` `plugin/BcMail/` 等）へフォールバックする。
- `webroot/` 配下のアセットは `/テーマ名snake形式/css/style.css` のような URL（プラグイン webroot 配信）で配信される（§4）。
- テーマの適用は管理画面（テーマ管理）から行う。開発環境で直接切り替える場合は `sites` テーブルの `theme` カラムを UPDATE してキャッシュクリア（`bin/cake cache clear_all`）する。

## 2. View・ビュー変数の作法

layout / element / テンプレート内の **`$this` は View 自身**である。Helper 内の `$this->getView()` と混同しない（View で `$this->getView()` を呼ぶと `Call to undefined method ...View::getView()`）。

コントローラが供給するビュー変数に依存せず、テンプレート側で必要な値を**自前で導出**するのが基本。コアはフロントの全ビューへ `$user` 等のグローバル変数を供給しない。

```php
// ログインユーザー（UserInterface|false — false があり得るので必ず判定する）
$user = \BaserCore\Utility\BcUtil::loginUser();
$authPrefixes = $user ? $user->getAuthPrefixes() : [];

// 現在のプレフィックス（Front / Admin など）
$currentPrefix = \BaserCore\Utility\BcUtil::getRequestPrefix($this->getRequest());

// セッション
$session = $this->getRequest()->getSession();
```

現在のコンテンツ・サイト情報は**リクエスト属性**から取る。どちらもエンティティで、コンテキストによっては null があり得るため `?->` で参照する。

```php
$currentContent = $this->getRequest()->getAttribute('currentContent'); // Content エンティティ|null
$currentSite = $this->getRequest()->getAttribute('currentSite');       // Site エンティティ|null
$entityId = $currentContent?->entity_id;  // 機能側レコードID（ブログコンテンツID 等）
$siteId = $currentSite?->id;
```

現在のURLパスは `$this->getRequest()->getPath()` と書く（先頭スラッシュ付きで返る）。

## 3. ヘルパ

### BcBaser の委譲機構（PluginBaserHelper）

`$this->BcBaser->blogPosts('news', 5)` のような機能系ショートカットは、BcBaserHelper 自体のメソッドではなく、各プラグインの `Bc<Plugin>BaserHelper::methods()` が返す**マッピングテーブル**経由で `__call` に委譲される（例: bc-blog の `BcBlogBaserHelper::methods()` に `'blogPosts' => ['Blog', 'posts']`）。

- **実在確認は2段階で行う**: ①`grep -rln "function <名前>" vendor/baserproject/` で関数定義を探す → ②見つからなければ `grep -rn "'<名前>'" vendor/baserproject/*/src/View/Helper/Bc*BaserHelper.php` で `methods()` マッピングを探す。マッピング委譲のため**関数定義が無いのが正常**であり、①だけで「存在しない」と判定しない。
- 委譲先メソッドは**提供プラグインが有効なときだけ**動く。無効時は BadMethodCall になる。

### よく使う静的ユーティリティ

```php
\BaserCore\Utility\BcSiteConfig::get('formal_name');  // サイト基本設定（address / name 等のキー）
\BaserCore\Utility\BcUtil::fullUrl($url);              // フルURL生成
```

### ヘルパの動的ロード

ヘルパは `new` でインスタンス化せず（DIコンテナ未注入で `getService()` が失敗する）、View からロードする。`loadHelper()` はインスタンスを返す。

```php
$blog = $this->getView()->loadHelper('BcBlog.Blog');  // Helper 内から
$blog = $this->loadHelper('BcBlog.Blog');             // テンプレート内（$this が View）から
```

### 参照名の規約と他プラグイン依存のガード

- コア機能の参照名は **`Bc` 接頭辞**で書く: ヘルパ `BcBlog.Blog`、テーブル `TableRegistry::getTableLocator()->get('BcBlog.BlogPosts')`、element `$this->BcBaser->element('BcBlog.xxx')`。コアテーブルは**複数形＋プラグイン接頭辞**（`BaserCore.Contents` / `BaserCore.Sites`）。
- テーマから**他プラグインのヘルパ**を呼ぶ場合は、プラグイン無効時にテーマごと落ちないよう `Plugin::isLoaded()` でガードし、必要なら loadHelper する。

```php
if (\Cake\Core\Plugin::isLoaded('<Plugin>')) {
    $helper = $this->helpers()->has('Sample')
        ? $this->Sample
        : $this->loadHelper('<Plugin>.Sample');
    $helper->show(['num' => 3]);
}
```

## 4. アセットとURL

### css() / js() のシグネチャ

`BcBaser->css($path, $inline = true, $options = [])`（`js()` も同様）。**第2引数は `$inline`**（`false` でバッファに溜めて `scripts()` で出力）で、`media` 等の属性は**第3引数**に書く。

```php
$this->BcBaser->css('style');                                  // <link ...>
$this->BcBaser->css(['print'], true, ['media' => 'print']);    // 属性は第3引数
$this->BcBaser->js('theme', true, ['defer' => true]);
```

### URL はハードコードしない

リンク・画像などの URL はルート相対（`/about/` 等）をハードコードせず `BcBaser->getUrl('/path')` を通して書く。サブディレクトリ設置時に base が自動付与される。

```php
<a href="<?php echo $this->BcBaser->getUrl('/about/') ?>">会社概要</a>
<?php $this->BcBaser->link('会社概要', '/about/') ?>
```

Helper 内で URL 文字列を組み立てる場合は `\Cake\Routing\Router::url($url)` を通すか、リクエストの webroot を前置する: `$this->getView()->getRequest()->getAttribute('webroot') . ltrim($url, '/')`。

### アップロード画像の表示（BcUploadHelper::uploadImage）

`uploadImage('<field>', $entity, $options)` と書く——第1引数は素のフィールド名、**第2引数は EntityInterface 必須**（文字列不可）。BcUploadHelper はデフォルトで View の plugin/name からテーブルを解決するため、テーマや他プラグインのテンプレから使うときは **`'table' => '<Plugin>.<Tables>'`（BcUpload behavior 持ち）を options で明示**する。画像サイズは `'imgsize'`、無い時の代替は `'noimage'`。file 型を含むメールフォーム系テンプレは §6 の `setTableToUpload()` を参照。

### テーマアセットの配信形式

テーマの `webroot/` 配下は**テーマ名の snake_case** をプレフィックスとした URL で配信される。

```php
$this->BcBaser->css('style');  // <ThemeName> の webroot/css/style.css を解決
// URL を直接組む場合
$this->BcBaser->getUrl('/sample_theme/css/style.css');  // plugins/SampleTheme/webroot/css/style.css
```

## 5. ウィジェット

ウィジェットエリアに表示する element は `templates/element/widget/`（**単数形 `widget`**。`widgets` ではない）に置く。BcWidgetArea はこのパスを探索する（コアの配置例: `bc-front/templates/plugin/BcBlog/element/widget/blog_recent_entries.php`）。

- プラグインが提供するウィジェット（例: `BcBlog.widget/blog_recent_entries`）を**テーマでオーバーライド**する場合も、テーマ側の同パス `templates/element/widget/<名前>.php` に置く。
- 管理画面向けのウィジェット設定用 element は `templates/Admin/element/widget/` に置く。

## 6. メールフォームテンプレート

メールフォームテンプレートは `templates/Mail/<テンプレート名>/index.php`（入力）・`confirm.php`（確認）・`submit.php`（完了）で構成し、フォーム描画は **BcBaser の mailForm 系 API** で書く（`BcMailBaserHelper::methods()` が Mailform ヘルパへ橋渡しする）。コアの正本は `vendor/baserproject/bc-front/templates/plugin/BcMail/` にあり、迷ったらこれに合わせる。

フォーム生成は、コントローラがセットするエンティティ `$mailMessage` を第1引数に取り、`valueSources` を指定する。

```php
$this->BcBaser->createMailForm($mailMessage, array_merge($options, ['valueSources' => ['context']]));
```

主要 API（フィールド名は素の名前で書く。`MailMessage.` 接頭辞の文脈は内部処理される）:

| 用途 | 書き方 |
|---|---|
| hidden フィールド | `$this->BcBaser->mailFormHidden('mode', ['id' => 'MailMessageMode'])` |
| フィールドのアンロック | `$this->BcBaser->unlockMailFormField('mode')` |
| 送信ボタン | `$this->BcBaser->mailFormSubmit($caption, $options)` |
| エラー表示 | `$this->BcBaser->mailFormError('field_name', $message)` |
| 画像認証 | `$this->BcBaser->mailFormAuthCaptcha('auth_captcha', ['helper' => $this->BcBaser])` |
| フォーム終了 | `$this->BcBaser->endMailForm()` |
| フリーズ（confirm.php） | `$this->BcBaser->freezeMailForm()` |
| フィールド描画 | `$this->BcBaser->mailFormControl($fieldName, $options)` |
| ラベル | `$this->BcBaser->mailFormLabel($fieldName, $text)` |
| 入力値の取得 | `$this->BcBaser->getMailFormSourceValue($fieldName)` |
| グループ最終フィールド判定 | `$this->BcBaser->isMailFormGroupLastField($mailFields, $field)` |

- `mailFormHidden('mode')` は素のフィールド名だと id が `mode` になる。JS が `#MailMessageMode` を参照する場合は `['id' => 'MailMessageMode']` を明示する。
- **file 型フィールドを描画するテンプレート（index.php / confirm.php）の先頭には必ず次の1行を書く**。無いと `BcUploadHelper を利用するには … table … を指定してください`（BcException）で落ちる。

```php
$this->BcBaser->setTableToUpload('BcMail.MailMessages');
```

- フィールド一覧を描画する `element('mail_input')` はコア版（`bc-front/.../BcMail/element/mail_input.php`）にフォールバックするため、独自デザインが不要ならテーマに置かなくてよい。

## 7. ブログテンプレート

ブログテンプレートは `templates/Blog/<テンプレート名>/` に `index.php`（一覧）・`archives.php`（カテゴリ/タグ/年月アーカイブ）・`single.php`（詳細）・`posts.php`（`BcBaser->blogPosts()` から呼ばれるパーツ用一覧）を置く。

- `$posts` / `$post` は**エンティティ**（またはその ResultSet）。関連もプロパティアクセスで書く: `$post->title`、`$post->content`、`$post->detail`、`$post->blog_category->title`、`$post->blog_content_id`。
- 投稿日時のカラム名は **`posted`**。ソート指定は `'sort' => 'posted'` / `orderBy(['BlogPosts.posted' => 'DESC'])` と書く。
- アイキャッチは `$this->Blog->getEyeCatch($post, ['width' => 300, 'link' => true])` または BcBaser 委譲の `$this->BcBaser->blogPostEyeCatch($post, [...])` で描画する。存在判定は `$post->eye_catch` で行う。
- 記事リンク・日付・カテゴリ・タイトルも BcBaser 委譲 API で書ける: `getBlogPostLinkUrl($post)` / `blogPostDate($post, 'Y.m.d')` / `blogPostCategory($post)` / `blogPostTitle($post, false)` / `blogPostContent($post, true, false, 46)`。
- カテゴリ一覧・タグ一覧は Blog ヘルパ／element で描画する（コアの `element/blog_category_list.php` / `blog_tag_list.php` を参考にする）。
- Table を直接引いて記事を取得する場合は `TableRegistry::getTableLocator()->get('BcBlog.BlogPosts')` からクエリビルダで書き、公開条件は `getConditionAllowPublish()` を使う。

```php
$blogPostsTable = \Cake\ORM\TableRegistry::getTableLocator()->get('BcBlog.BlogPosts');
$posts = $blogPostsTable->find()
    ->where($blogPostsTable->getConditionAllowPublish())
    ->where(['BlogPosts.blog_content_id' => $blogContentId])
    ->orderBy(['BlogPosts.posted' => 'DESC'])
    ->limit($num)
    ->all();
```

同種の取得ロジックが複数テンプレートに散在する場合は、テーマの `src/View/Helper/<ThemeName>Helper.php` に集約する。
