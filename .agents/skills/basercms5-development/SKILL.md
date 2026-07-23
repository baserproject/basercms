---
name: basercms5-development
description: 'baserCMS 5系（CakePHP 5.0.5系）＋ jQuery プロジェクトの共通ルール（環境・ディレクトリ構成・命名規則・ログ／エラーハンドリング・コアハック禁止・サーバ制約）を定めた基底スキル。「baserCMS 5 で開発」「5系プロジェクトの構成・規約を確認」「htdocs/plugins 配下の配置」「CakeLog でログ出力」「htdocs/vendor/baserproject・cakephp はコアハック禁止」等、5系プロジェクト共通の方針・制約を確認する際に参照する。プラグイン開発・改修の実装パターン（Table/Entity・コントローラ・管理画面フォーム・イベント・Vue/JS 等）は basercms5-plugin-development、テーマ開発・改修（layout/element・ウィジェット・メールフォームテンプレ・アセット）は basercms5-theme-development が正本。baserCMS 4系の開発は basercms4-development、4→5 へのアップグレードは basercms4-to-5-upgrade スキルを参照。'
license: MIT
---

# baserCMS5系（CakePHP 5.0.5 系）＋ jQuery プロジェクトの開発ルール

このプロジェクトでは、以下の技術スタックおよび設計方針に従って開発されています。

5系スキルは3層構造になっており、本スキルは**共通ベース（環境・ディレクトリ構成・命名・ログ・コアハック禁止・サーバ制約）**を扱う。実装パターンは分野別スキルが正本: **プラグイン開発・改修 → `basercms5-plugin-development`**、**テーマ開発・改修 → `basercms5-theme-development`**、**4→5 移行作業 → `basercms4-to-5-upgrade`**（配下の分野別移行スキル含む）。

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

## 実装パターンの正本は分野別スキルへ

具体的な実装パターン（要点・落とし穴を含む）は、分野別の正本スキルに集約している。

- **プラグイン開発**（Table/Entity・コントローラ・管理画面フォーム・イベント・Vue/JS 等）→ **`basercms5-plugin-development`**
- **テーマ開発**（layout/element・ウィジェット・メールフォームテンプレ・アセット）→ **`basercms5-theme-development`**

4→5 移行作業そのものは `basercms4-to-5-upgrade` / `basercms-plugin-4-to-5-upgrade` / `basercms-theme-4-to-5-upgrade` を参照。

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
