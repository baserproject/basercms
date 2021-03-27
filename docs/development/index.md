# ucmitz 開発ガイド

## 開発の手順

実際の開発については、開発の手順に従って開発します。

- [開発の手順](https://github.com/baserproject/ucmitz/blob/dev/docs/development/procedure.md) 

　
## 移行上のルール

baserCMS4のコードを移行していることが ucmitzの開発になるのですが、様々なルールがありますので必ず確認してください。

- [移行上のルール](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_rule.md)

　
## 開発上の注意点

baserCMS4で利用しているCakePHP2系からCakePHP4系に移行するにあたり、様々な変更点や注意点があります。開発上の注意点を参考にします。

- [ルーティングにおける注意点](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_point/routing.md)
- [コントローラーにおける注意点](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_point/controller.md)
- [モデルにおける注意点](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_point/model.md)
- [ビューにおける注意点](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_point/view.md)
- [ヘルパーにおける注意点](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_point/helper.md)
- [リクエスト関連における注意点](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_point/request.md)
- [セッション関連における注意点](https://github.com/baserproject/ucmitz/blob/dev/docs/development/migration_point/session.md)

　
## 全体的な変更点

開発における全体的な変更点については次を確認してください。

- [全体的な変更点](https://github.com/baserproject/ucmitz/blob/dev/docs/development/changed.md)

　
## 既知の問題点

現時点で解決できていない既知の問題点については次を確認してください。

- [既知の問題点](https://github.com/baserproject/ucmitz/blob/dev/docs/development/problem.md)

　　
## ユニットテスト

ユニットメソッドの作成方法と実行方法については次を確認してください。

- [ユニットテスト](https://github.com/baserproject/ucmitz/blob/dev/docs/development/test/unittest.md)

　
## テーマの開発

ucmitzの管理画面のテーマの開発を行うには次を確認してください。

- [BcAdminThirdの開発](https://github.com/baserproject/ucmitz/blob/dev/plugins/bc-admin-third/README.md)

なお、テーマの定義は、現在、次のメソッドにて行っています。
```php
BaserCore\Controller\Admin\BcAdminAppController::beforeRender()
```

　
