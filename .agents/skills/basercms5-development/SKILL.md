---
name: basercms5-development
description: 'baserCMS 5系（CakePHP 5.0.5系）＋ jQuery プロジェクトの開発ルール集。「baserCMS 5 で開発」「5系のプラグインを修正」「CakePHP 5系のコードを書く」「htdocs/plugins 配下の Controller／Model／View」「テーマの templates/element・templates/layout」「BcBaserHelper／BcFormHelper／BcHtmlHelper の利用」「Table／Entity の責務分離」「バリデーションは Table クラス」「Migration (phinx) でDB変更」「CakeLog でログ出力」「htdocs/vendor/baserproject・cakephp はコアハック禁止」等、baserCMS 5系プロジェクトのコーディング方針・ディレクトリ構成・命名規則・ログ／エラーハンドリング・読み取り専用ファイルの扱い・サーバ制約を確認する際に参照する。baserCMS 4系の開発は basercms4-development、4→5 へのアップグレードは basercms4-to-5-upgrade スキルを参照。'
license: MIT
---

# baserCMS5系（CakePHP 5.0.5 系）＋ jQuery プロジェクトの開発ルール

このプロジェクトでは、以下の技術スタックおよび設計方針に従って開発されています。

> **推奨: 5系の開発に着手する前に一度 `basercms5-claude-workflow-setup`（環境セットアップ）を参照し、進め方の環境（設計=superpowers brainstorming／権限整理=permissions-audit／その上での Auto mode／spec・plan の Markdown プレビュー）を整える。提案ベースで、整っていればスキップ。**

---

## 使用技術

- **baserCMS 5系** を使用（内部的には **CakePHP 5.0.5** ベース）
- バックエンド：PHP 8.1.5 〜 8.2 / CakePHP 5.0.5
- データベース：MySQL 5.7 〜 8.0
- フロントエンド：jQuery 1.x〜3.x（プロジェクト内で複数バージョンが混在している可能性あり）
- HTTPサーバ：Apache（mod_rewrite 有効）

---

## ディレクトリ構成のルール

- MVC構成は CakePHP 5.0.5 に準拠しつつ、**baserCMSでは主にプラグイン単位で構築されている**
    - `htdocs/plugins/{Plugin名}/Controller/`, `Model/`, `View/` にそれぞれ配置される
- 共通パーツ（ヘッダー、フッターなど）は、**テーマ内の `Elements/` ディレクトリに配置**
    - 主に：`htdocs/{テーマ名}/templates/element/`
- レイアウトもテーマ内に配置される
    - 主に：`htdocs/{テーマ名}/templates/layout/`
    - `$this->layout` により切り替えが可能
- 管理画面系は `/admin/` プレフィックスのURLルーティングで制御
    - 使用ファイル：`htdocs/config/.env`
    - 定義内容：`export ADMIN_PREFIX="admin"`

---

## コーディング方針（baserCMS5固有）

共通ルールのコーディング方針も参照してください。

- クラスベースよりも関数ベース・手続き的なコードが多く採用されている
- JavaScript は Viewファイル内の `<script>` タグ内または `.js` ファイルに記述
- Ajax通信は jQuery（`$.ajax()`）を用いて行う
- ES6構文（Promise, fetch, import/export）は使用しない
- フロント実装に React や Vue などの SPA 技術は導入していない
    - 一部、部分的に利用している箇所がある

---

## 命名規則（baserCMS5固有）

共通ルールの命名規則も参照してください。

※ baserCMSやCakePHPの命名慣習（Controller, Helper, Modelなど）はそのまま準拠する

---

## ログ・エラーハンドリング方針（baserCMS5固有）

共通ルールのログ・エラーハンドリング方針も参照してください。

**目的：** `/log` エイリアスや、例外処理実装時の提案精度向上

### baserCMS5固有の実装方法

- PHPでは `CakeLog::error()` または `error_log()` を利用
- JavaScriptでは `console.error()` を暫定対応とし、本実装時はサーバ送信も検討

### 実装例（baserCMS用で設定ファイルに記述）：

```
/**
 * 専用ログ
 */
if (!defined('PROJECT_PLUGIN_ACTION')) {
	define('PROJECT_PLUGIN_ACTION', 'project_plugin_action');
	CakeLog::config('project_plugin_action', [
		'engine' => 'FileLog',
		'types' => ['project_plugin_action'],
		'file' => 'project_plugin_action',
		'size' => '5MB',
		'rotate' => 5,
	]);
}

// ログを取りたい箇所に記述する
$this->log('Start', PROJECT_PLUGIN_ACTION);
$this->log(print_r($array, true), PROJECT_PLUGIN_ACTION);
```

---

## 使用ライブラリとヘルパー

- `BcBaserHelper`, `BcFormHelper`, `BcHtmlHelper` など baserCMS 固有ヘルパーを活用
- HTML の出力は `$this->BcBaser->css()`, `$this->BcBaser->img()` などを使用し、手書きは控えめにする

---

## 実装の要点・落とし穴（CakePHP5 / baserCMS5）

新規実装・改修の双方で踏みやすい要点。4→5 移行作業そのものは `basercms4-to-5-upgrade` / `basercms-plugin-4-to-5-upgrade` を参照。

### 管理画面のフォーム
- **管理画面のフォームは `$this->BcAdminForm` を使い、入力は必ず `control()` で出す**。フロント用 `$this->BcForm`／`$this->Form` や素のウィジェット（`text()`/`select()`/`checkbox()`）を直接呼ぶと **bca-* の管理スタイルが当たらず崩れる**。一覧の一括処理（`ListTool.batch`）・一括選択（`ListTool.checkall`）・`submit()`/`button()` も `BcAdminForm`。
- **コンテキストレスフォームは `create(null, ...)`**。`create('Model')`（文字列）は `No context provider found for value of type string`。送信値の再表示が要るなら `create(null, ['valueSources' => ['data','context']])`（GET検索は `['query','context']`）。
- **複数チェックボックスは options をループして `control('Model.field[]', ['type'=>'checkbox','value'=>$v,'checked'=>in_array((string)$v,$selected,true),'hiddenField'=>false])`**。`control(['multiple'=>'checkbox'])` や `multiCheckbox()` は崩れる。
- **`control()` の `label` に HTML を入れるときは配列形式 `['text'=>'<span…>','escape'=>false]`**（文字列だとエスケープされ生表示）。
- **`control()` が自動生成する id は小文字ハイフン**（`Text::slug`、例 `cpmcommit-period-year`）。JS から要素を掴むなら **フォーム委譲**（`$("#FormId").on('change','select,input',…)`）にするか、`control(..., ['id'=>'…'])` で明示する。`FormHelper::year()/month()/domId()` は廃止（`year`/`month` は `select`＋自前 options、`domId` は `Inflector::camelize(str_replace('.','_',$field))`）。
- **プラグイン提供のフォーム拡張フック**は `$this->BcFormTable->dispatchBefore()/dispatchAfter()`、`$this->BcAdminForm->dispatchAfterForm()`（旧 `$this->FormTable` / `$this->Form->dispatchAfterForm` の名前は不可）。

### 日付・数値・文字列ヘルパー
- **`Time::format($d, 'yyyy-MM-dd')` は ICU パターン**（PHP の `date()` 形式ではない。`Y`=週年・`m`=分・`M`=月）。第2引数を省略すると**日時**になるので、日付のみは必ず指定する。
- **`Number::format()` / `Text::truncate()` は第1引数 null 不可**（`string|int|float` / `string`）。nullable を渡す箇所は `format($x ?? 0, …)` / `truncate((string)$x, …)`。

### ORM・コントローラ
- **テーブル取得**: コントローラは `$this->fetchTable('Plugin.Models')`、**Table / Lib / Helper / イベント内は `\Cake\ORM\TableRegistry::getTableLocator()->get('Plugin.Models')`**（`fetchTable` はコントローラ専用）。エイリアスは**複数形**（`Cpm.CpmProjects`）。`getControlSource('Plugin.Model.field')` も複数形でないと `MissingTableClassException`。
- **配列条件に IN は自動付与されない**: `where(['field'=>[1,2]])` は `= ?` になり `Cannot convert value Array`。複数値は `'field IN' => (array)$v`。**null 一致は `['field IS' => null]`**（`!=` は `IS NOT`）。空配列の `IN ()` は例外になるので空ならガード。
- **belongsToMany（旧HABTM）の保存は `_ids`**: `patchEntity($e, $data + ['assoc'=>['_ids'=>[$id,…]]], ['associated'=>['Assoc']])`。表示は `contain(['Assoc'])`。
- **name/value の KVS テーブル**は Table の `initialize()` に `$this->addBehavior('BaserCore.BcKeyValue')` を足すと `saveKeyValue([$key=>$value])` が使える。読み出しは `find()->where(['name'=>$key])->first()->value`。
- **`getParam('controller')` は CamelCase**（`'CpmCosts'`）。テンプレ/分岐で `=== 'cpm_costs'`（スネーク）にすると常に false。リンク配列の `'controller'` も CamelCase（別プラグインは `'plugin'=>'Cards'`）。
- **テンプレートが参照する設定値**は `$this->set(\Cake\Core\Configure::read('Plugin'))` でまとめてビュー変数に供給する（個別 set 忘れで `Undefined variable`／`array_merge(... null)`）。

### View の罠
- **View で `$this->data`（4系）や `$View->request`（プロパティ）にアクセスしない**。CakePHP5 の View には無く、**未知プロパティ＝ヘルパの自動ロード**扱いになり `<Plugin>.requestHelper could not be found` / `dataHelper could not be found`（MissingHelperException）。`$this->getRequest()->getData(...)` / `getRequest()->getParam(...)` を使う。特に**全フォーム・全一覧で発火するイベントリスナ**に残っていると無関係な画面まで巻き込んで落とすので最優先で直す。

### 一部 Vue / jQuery を使う画面
- **管理画面の ajax / リンク URL をハードコードしない**。`/admin/...` も `/<subdir>/baser/admin/...` も配置変更で壊れる。baser が用意するグローバル **`$.bcUtil.adminBaseUrl`**（`baseUrl + '/' + baserCorePrefix + '/' + adminPrefix + '/'`、末尾スラッシュ付き）に連結する。Vue2 テンプレート式からはグローバル `$` を参照できないので、テンプレート用に computed `adminBaseUrl(){ return $.bcUtil.adminBaseUrl; }` を公開する。
- **Vue バンドルはソース（`webroot/js/src`）を直して webpack 再ビルド**（コンパイル済み `.bundle.js`/`.bundle.css` を手で追わない）。出力は `webroot/js/[name].bundle.js`、CSS は `MiniCssExtractPlugin` で `../css/[name].bundle.css`。
- **広い表でページ全体が横スクロールする**ときは、管理テーマ `.bca-container`(flex) の子 `.bca-main` に `min-width:0` が無いのが原因。当該画面の CSS に `.bca-main{min-width:0}` を足し、表は `overflow:auto` のラッパーで囲う（`position:sticky` の列固定もこれで効く）。

### 外部パッケージ
- プラグインが使う **サードパーティ製ライブラリ（例 `phpoffice/phpspreadsheet`）は `composer require` で導入**する（標準インストールの vendor には無い）。Excel 等の雛形アセットは `templates/Admin/Excel/<controller>/<file>.xlsx` に置き、`Plugin::templatePath('Plugin') . 'Admin' . DS . 'Excel' . DS . …`（`templatePath()` は末尾スラッシュ付き）で参照する。

---

## ファイル・ディレクトリに関する扱いルール

**目的：** AIがコードを解析・提案する際に、以下の3つの分類に基づいて適切に扱うことで、不要な提案や誤った変更を防ぎます。

- **読み取り専用**：フレームワークのコアファイルなど、参照はするが変更は禁止
- **設定調査用**：設定値を確認するために参照するが、変更は禁止

以下の分類に基づいて、ファイル・ディレクトリの扱いを明確に区別してください。

### 読み取り専用（AI/人間が参照するが改変しないこと）

- `htdocs/vendor/baserproject/`（baserCMS のコア）
- `htdocs/vendor/cakephp/`（CakePHP 本体）

### 設定調査用（内容の把握目的。値の取得などには使うが変更禁止）

- `htdocs/config/install.php`（接続設定の確認）
- `htdocs/config/.env.php`（ルーティング・デバッグ設定などの調査）
- `htdocs/bin/cake`（CLI の仕様確認）
- `htdocs/webroot/index.php`, `.htaccess`（起動ファイル・Rewrite設定）

---

## サーバ構成・制約（baserCMS5固有）

共通ルールのサーバ構成・制約も参照してください。

- baserCMS はドキュメントルート配下に配置（`index.php` がルート）

---

## 制約・注意点

- baserCMS の標準構成に準拠したカスタマイズが基本（コアハック禁止）
- Laravel や CakePHP 5系など、他フレームワークへの置き換え提案は不要
- モダン JS フレームワーク（Vue, React など）に関する提案も不要
- 検討や提案を行う場合は、既存構成との互換性と影響を最優先に考慮すること
- PHP 8.1.5の制約を考慮した実装を心がける
- jQuery 1.x〜3.xの互換性を保つ
- MySQL 5.7〜8.0の両方で動作するSQL文を使用
- CakePHP4系準拠。Table / Entity の責務分離を意識。
- バリデーションは Table クラスに定義。
- データベース変更は Migration (phinx) を利用。
