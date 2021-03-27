# 開発への貢献方法

「ucmitz」は、baserCMSをCakePHP4に対応するプロジェクトの開発コードネームです。コアデベロッパーの gondoh が命名しました。  

開発に貢献頂ける場合は、`dev` ブランチを利用してください。

　

## 開発方針とロードマップの確認

開発に貢献する前に、開発方針とロードマップを必ず確認しましょう。ucmitz は、メジャーバージョンを３回刻んだ後、baserCMS５としてリリースされます。

- [開発方針](https://docs.google.com/document/d/1QAmScc65CwMyn8QuwWKE9q_8HnSKcW9oefI9RrHoUYY/edit)
- [ロードマップ](https://docs.google.com/spreadsheets/d/1TZ71-O_9KiQM9xAB_a_jnSFVrH2dsyKowMLkyGLcI9g/edit#gid=2131306554)

　
## システム要件の確認

ucmitz は、PHP7.2以降でのみ動作します。詳細についてはシステム要件を確認します。

- [システム要件](https://github.com/baserproject/ucmitz/blob/dev/docs/preparation/basic/system.md)

　

## 開発の準備

次のドキュメントを参考に、ucmitz がローカル環境で動作するように準備します。Docker on Vagrant の環境を提供しています。

- [開発の準備](https://github.com/baserproject/ucmitz/blob/dev/docs/preparation/pre-development.md) 

　
　
## パッケージ構成の確認

ucmitz は、主に `BaserApp`、`BaserCore`、`BcAdminThird` の３つコアパッケージを中心に、ブログやメールフォームなどコアプラグインで構成されます。
詳細については、パッケージ構成を参照してください。

- [パッケージ構成](https://github.com/baserproject/ucmitz/blob/dev/docs/basic/package.md)

　
　
## 開発にとりかかる

実際の開発については、ucmitz開発ガイドを参考にします。

- [ucmitz 開発ガイド](https://github.com/baserproject/ucmitz/blob/dev/docs/development/index.md) 


　

## 新しい機能の定義

新しく機能を定義したい場合、 機能要件一覧に存在するか確認し、なければ Issue を作成して話し合い、承認されれば機能要件一覧に追加します。

- [機能要件一覧](https://docs.google.com/spreadsheets/d/1YT5PuZQdDNU0wrZdqYbh74KuLSw1SIt4_EKwPWOfDKA/edit#gid=0) 

　

## ドキュメントの追加

開発中に発生した開発に必要なドキュメントを追加する場合は、`/docs/` ディレクトリに、マークダウン形式でファイルを作成します。

　

## プロジェクトで ucmitz を利用する

人柱となり自身のプロジェクトで ucmitz を利用したい場合は、composer でインストールできます。
詳細については次のドキュメントを参照してください。

- [外部のCakePHPアプリケーションで ucmitz を利用する](https://github.com/baserproject/ucmitz/blob/dev/docs/etc/use_my_project.md)

　
　
## 新しいプラグインを開発する

ucmitz の新しいプラグインを開発するには次のドキュメントが参考になります。

- [ucmitzのプラグインの開発](https://github.com/baserproject/ucmitz/blob/dev/docs/plugin/plugin-development.md)

　
　
## その他のドキュメント

- [Cloud9 上で ucmitz を動作させる](https://github.com/baserproject/ucmitz/blob/dev/docs/etc/cloud9.md)

　
