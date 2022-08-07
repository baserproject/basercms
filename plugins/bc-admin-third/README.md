# BcAdminThird

baserCMSの管理画面テーマのプロジェクト

## CSSの制作

- [スタイルガイド](https://localhost/bc_admin_third/guide.html)

　
## Javascriptの開発

Javascript の開発は、Gulp でファイルを監視し Webpack を利用して開発します。

### 事前準備
事前に Node.js をインストールしておき、npm コマンドが利用できるようにしておきます。

### 開発環境の構築
```shell script
npm install
```

### ファイル監視
bc-admin-third ディレクトリの直下で、gulp を実行し、ファイル監視を開始します。
```shell script
cd plugins/bc-admin-third
gulp
```
監視対象は、`/src/js/` 配下の javascript ファイルとなります。  
監視対象のファイルを更新すると、`/webroot/js/` 配下のディレクトリ構造を維持した同階層展に、`bundle` というサフィックス付のファイルにコンパイルします。
```
/src/js/admin/users/index.js
↓
/webroot/js/admin/users/index.bundle.js
```

### ライブラリの導入方針
外部ライブラリはできるだけ、npm でインストールして利用します。
npm でインストールできないものは、`/webroot/js/vendor/` フォルダに配置して import や require で読み込みます。  
外部ライブラリは、自動で `webroot/js/admin/vendor.bundle.js` に出力します。


### 共通処理
全ての画面で読み込む共通処理などは、`/webroot/js/src/admin/common.js` を利用します。  
共通処理は、自動で `webroot/js/admin/common.bundle.js` に出力します。

　
## jQueryの利用について

`bootstrap` の読み込みにおいて jQueryが必要となるため、`package.json` に定義しているが、こちらを利用する場合に、`vendor` い配置した jQuery プラグインがうまく動作しない。
  
どうやら、プラグインで読み込む `$` と、endpoint で参照する `$` が別のインスタンスを指している様子。
解決方法が分からないため、vendor に配置した jquery を、テンプレートから直接読み込んでいる。

### Javascriptファイルの移行について

admin/vendorsをvendor/直下に移行

admin/libsにおいてajaxでhtmlを取得する処理は廃止し、json化していく予定
- baser_ajax_batch_config.js
- baser_ajax_data_list_config.js
- baser_ajax_data_list.js


