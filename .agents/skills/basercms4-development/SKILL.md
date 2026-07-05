---
name: basercms4-development
description: 'baserCMS 4系（CakePHP 2.10ベース）＋ jQuery プロジェクトの開発ルール集。「baserCMS 4 で開発」「4系のプラグインを修正」「CakePHP 2系のコードを書く」「app/Plugin 配下の Controller／Model／View」「テーマの Elements／Layouts」「BcBaserHelper／BcFormHelper／BcHtmlHelper の利用」「CakeLog でログ出力」「lib/Baser・lib/Cake はコアハック禁止」等、baserCMS 4系プロジェクトのコーディング方針・ディレクトリ構成・命名規則・ログ／エラーハンドリング・読み取り専用ファイルの扱い・サーバ制約を確認する際に参照する。baserCMS 5系の開発は basercms5-development、4→5 へのアップグレードは basercms4-to-5-upgrade スキルを参照。'
license: MIT
---

# baserCMS4系（CakePHP2系）＋ jQuery プロジェクトの開発ルール

このプロジェクトでは、以下の技術スタックおよび設計方針に従って開発されています。

---

## 使用技術

- **baserCMS 4系** を使用（内部的には **CakePHP 2.10** ベース）
- バックエンド：PHP 7.4 〜 8.2 / CakePHP 2系
- データベース：MySQL 5.7 〜 8.0
- フロントエンド：jQuery 1.x〜3.x（プロジェクト内で複数バージョンが混在している可能性あり）
- HTTPサーバ：Apache（mod_rewrite 有効）

---

## ディレクトリ構成のルール

- MVC構成は CakePHP 2系に準拠しつつ、**baserCMSでは主にプラグイン単位で構築されている**
    - `app/Plugin/{Plugin名}/Controller/`, `Model/`, `View/` にそれぞれ配置される
- 共通パーツ（ヘッダー、フッターなど）は、**テーマ内の `Elements/` ディレクトリに配置**
    - 主に：`theme/{テーマ名}/Elements/`
    - 一部環境では：`app/webroot/theme/{テーマ名}/Elements/`
- レイアウトもテーマ内に配置される
    - 主に：`theme/{テーマ名}/Layouts/`
    - 一部環境では：`app/webroot/theme/{テーマ名}/Layouts/`
    - `$this->layout` により切り替えが可能
- 管理画面系は `/admin/` プレフィックスのURLルーティングで制御
    - 使用ファイル：`app/Config/core.php`
    - 定義内容：`Configure::write('Routing.prefixes', array('admin'));`

---

## コーディング方針（baserCMS4固有）

共通ルールのコーディング方針も参照してください。

- クラスベースよりも関数ベース・手続き的なコードが多く採用されている
- JavaScript は Viewファイル内の `<script>` タグ内または `.js` ファイルに記述
- Ajax通信は jQuery（`$.ajax()`）を用いて行う
- ES6構文（Promise, fetch, import/export）は使用しない
- フロント実装に React や Vue などの SPA 技術は導入していない
    - 一部、部分的に利用している箇所がある

---

## 命名規則（baserCMS4固有）

共通ルールの命名規則も参照してください。

※ baserCMSやCakePHPの命名慣習（Controller, Helper, Modelなど）はそのまま準拠する

---

## ログ・エラーハンドリング方針（baserCMS4固有）

共通ルールのログ・エラーハンドリング方針も参照してください。

**目的：** `/log` エイリアスや、例外処理実装時の提案精度向上

### baserCMS4固有の実装方法

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

## ファイル・ディレクトリに関する扱いルール

**目的：** AIがコードを解析・提案する際に、以下の3つの分類に基づいて適切に扱うことで、不要な提案や誤った変更を防ぎます。

- **読み取り専用**：フレームワークのコアファイルなど、参照はするが変更は禁止
- **設定調査用**：設定値を確認するために参照するが、変更は禁止

以下の分類に基づいて、ファイル・ディレクトリの扱いを明確に区別してください。

### 読み取り専用（AI/人間が参照するが改変しないこと）

- `lib/Baser/`（baserCMS のコア）
- `lib/Cake/`（CakePHP 本体）

### 設定調査用（内容の把握目的。値の取得などには使うが変更禁止）

- `app/Config/database.php`（接続設定の確認）
- `app/Config/core.php`（ルーティング・デバッグ設定などの調査）
- `app/Console/cake`（CLI の仕様確認）
- `app/webroot/index.php`, `.htaccess`（起動ファイル・Rewrite設定）

---

## サーバ構成・制約（baserCMS4固有）

共通ルールのサーバ構成・制約も参照してください。

- baserCMS はドキュメントルート配下に配置（`index.php` がルート）

---

## 制約・注意点

- baserCMS の標準構成に準拠したカスタマイズが基本（コアハック禁止）
- Laravel や CakePHP 4系など、他フレームワークへの置き換え提案は不要
- モダン JS フレームワーク（Vue, React など）に関する提案も不要
- 検討や提案を行う場合は、既存構成との互換性と影響を最優先に考慮すること
- PHP 7.2の制約を考慮した実装を心がける
- jQuery 1.x〜3.xの互換性を保つ
- MySQL 5.7〜8.0の両方で動作するSQL文を使用
