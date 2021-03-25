# 開発への貢献方法

baserCMS５の開発コードネームは、`ucmitz` です。開発については、`ucmitz` レポジトリの `dev` ブランチを利用します。

　

## 開発方針とロードマップの確認

開発にたずさわる前に 開発方針とロードマップを必ず確認します。

- [開発方針](https://docs.google.com/document/d/1QAmScc65CwMyn8QuwWKE9q_8HnSKcW9oefI9RrHoUYY/edit)
- [ロードマップ](https://docs.google.com/spreadsheets/d/1TZ71-O_9KiQM9xAB_a_jnSFVrH2dsyKowMLkyGLcI9g/edit#gid=2131306554)

　
## 開発環境の準備

[開発環境の準備](https://github.com/baserproject/ucmitz/blob/dev/docs/pre-development.md) を参考に、ucmitz がローカル環境で動作するように準備します。

　
## パッケージ構成の確認

- [BaserApp](https://github.com/baserproject/ucmitz) ：Gitでクローンした本体。親パッケージとしてbaserCMSのアプリケーションフレームを提供
- [BaserCore](https://github.com/baserproject/ucmitz/tree/dev/plugins/baser-core) ：baserCMSの本体、子パッケージとして主にURLに紐づくルーティングと、ビジネスロジックを提供　`/plugins/baser-core`
- [BcAdminThird](https://github.com/baserproject/ucmitz/tree/dev/plugins/bc-admin-third) ：子パッケージとして、baserCMSの画面表示をテーマとして提供　`/plugins/bc-admin-third`

　
## 開発の手順

[開発の手順](https://github.com/baserproject/ucmitz/blob/dev/docs/procedure-development) に従って開発します。

　
 
## 全体の構成や baserCMS４からの変更点について

[baserCMS4 から ucmitz への移行](https://github.com/baserproject/ucmitz/blob/dev/migration-docs/README.md) を参考にします。

　

## 新しい仕様の定義について

仕様の定義者は、新しく仕様を定義する場合、 [機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) に存在するか確認し、なければ追加します。

　

## 開発中に発生した開発に必要なドキュメントの追加について

`/docs/` または、 `/migration-docs/` に、マークダウン形式でファイルを作成します。

　

## その他の開発に必要なドキュメント
- [システム要件](https://github.com/baserproject/ucmitz/blob/dev/docs/system.md)
- [コーディング方針](https://github.com/baserproject/ucmitz/blob/dev/docs/coding-policy.md)
- [データベースの定義](https://github.com/baserproject/ucmitz/blob/dev/docs/database.md)
- [プラグインの呼び出し](https://github.com/baserproject/ucmitz/blob/dev/docs/call-plugin.md)
- [BcAdminThirdの開発](https://github.com/baserproject/ucmitz/blob/dev/plugins/bc-admin-third/README.md)
- [外部のCakePHPアプリケーションで baserCMSを利用する](https://github.com/baserproject/ucmitz/blob/dev/docs/application.md)
- [baserCMSのプラグイン開発](https://github.com/baserproject/ucmitz/blob/dev/docs/plugin.md)
- [ユニットテスト](https://github.com/baserproject/ucmitz/blob/dev/docs/unittest.md)
- [モノレポによるパッケージ管理](https://github.com/baserproject/ucmitz/blob/dev/docs/monorepo.md)
- [Cloud9 上で Docker を動作させる](https://github.com/baserproject/ucmitz/blob/dev/docs/cloud9.md)
- [将来的に追加したい機能](https://docs.google.com/document/d/1AwJQ0h0xQ5utFB1tVzLh1b1UhZp-lxQbM2fDjxtDc9I/edit#)
