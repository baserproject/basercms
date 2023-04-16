# BcFront plugin for baserCMS

## Installation

You can install this plugin into your baserCMS application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:

```
composer require baserproject/BcFront
```
　
## Contributing
- [BcFront の開発](https://baserproject.github.io/5/ucmitz/development/frontend/bc-front)

　
## 概要

baserCMSの基本機能を全て実装しているテーマです。
このテーマをベースにデザインを変更しやすいよう、極力シンプルなデザインとしています。

　
## 各ファイルの説明

このテーマは、baserCMSの全ての機能に対応している為、非常に多くのテンプレートを含んでいますが、
実装しない機能に関わるテンプレートは不要となります。
必要なものだけを調整します。

各ファイルの役割について以下に示します。

- /config/ --- 初期データ等
- /src/css/ --- sass 用ファイル
- /src/View/Helper/ --- テーマ内で利用する表示用関数
- /webroot/css/ --- スタイルシート
- /webroot/img/ --- 画像ファイル
- /webroot/js/ --- Javascriptファイル
- /webroot/files/ --- ブログのアイキャッチ等、管理画面でアップロードしたファイル
- /templates/layout/ --- 大枠となるテンプレート
- /templates/element/ --- 各テンプレートより呼び出される部品となるテンプレート
- /templates/email/ --- メールのテンプレート
- /templates/Error/ --- Not Found 等エラー用のテンプレート
- /templates/Pages/ --- 固定ページのテンプレート
- /templates/Maintenance/ --- メンテナンス中時のテンプレート
- /templates/plugin/BcBlog/Blog/ --- ブログのテンプレート
- /templates/plugin/BcMail/Mail/ --- メールフォームのテンプレート
- /templates/plugin/BcSearchIndex/SearchIndexes/ --- サイト内検索のテンプレート
- /config.php --- テーマの説明を記載
- /README.md --- このファイル
- /screenshot.png --- テーマのスクリーンショット

また、各テンプレートのヘッダ部分には、簡単な説明と、
どこから呼び出されるかが記載されていますので参考にしてください。

　
## スタイルシートについて

独自のスタイルシートは４つ存在します。各ファイルの役割を以下に示します。

- config.css --- テーマ設定でメインカラーやサブカラーを設定する為に利用
- editor.css --- 管理画面のWYSIWYGエディタ上で参照する為に利用
- bge_style.css --- 「BurgerEditor」プラグインを利用する場合に管理画面の表示を最適化するために利用
- style.css --- フロントで参照する為に利用

scss ファイルを編集する場合は、 `/` フォルダ内で、sass 環境を作る必要があります。

```
npm install
npm run dev -w plugins/bc-front
```
