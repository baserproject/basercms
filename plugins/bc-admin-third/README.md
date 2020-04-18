# BcAdminThird

## Javascriptの開発
Gulp と Webpack を利用して開発します。

bc-admin-third 直下で、gulp コマンドを実行します。

webroot/js/src/ 配下の javascript ファイルを変更すると、webroot/js/ 配下に展開されます。

```
webroot/js/src/admin/users/index.js 
↓
webroot/js/admin/users/index.bundle.js
```

外部ライブラリはできるだけ、npm でインストールして利用します。

npm でインストールできないものは、webroot/js/vendor フォルダに配置して import や require で読み込みます。

外部ライブラリは、webroot/js/admin/vendor.bundle.js に出力されます。

共通処理は、webroot/js/src/admin/common.js を利用し、webroot/js/admin/common.bundle.js に出力されます。


