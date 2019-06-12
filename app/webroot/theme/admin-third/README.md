# admin-third 開発ドキュメント

## 概要
admin-thirdは、新しい管理システムのテーマです。  
baserCMS 4.2 で、パッケージに同梱され、テーマの切り替えが可能となり、 baserCMS 5 でデフォルトの管理システムテーマとなる予定です。

## 開発への参加
[開発タスク](https://docs.google.com/spreadsheets/d/1LqDuPntPkR-2XHKR1B42vrXQhJz02UHzaURvaPCbgj8/edit#gid=0) はGoogleスプレッドシートで管理していますので、開発環境を整えてから参加ください。

## 環境構築

### レポジトリのクローン
basercamp レポジトリより、clone します。
```$xslt
git clone https://github.com/baserproject/basercamp
```

### 開発ブランチ
開発ブランチは、 `dev-4.2-admin-design` を利用します。

### Docker環境の構築

[Dockerを利用した開発方法（Mac）](https://github.com/baserproject/basercamp/blob/dev-4.2-admin-design/docker/README.md) を参考に、Docker 環境を構築します。

### タスクランナーの準備
npm を利用して、タスクランナーの実行環境を構築します。  
node は、8系の利用が必須となりますので、nodebrew で、node のバージョンを切り替えれるようにしておきます。

```$xslt
# nodebrew インストール
brew install nodebrew
nodebrew setup

# 利用可能バージョン確認
nodebrew ls-remote

# 8系の最新版をインストールし、利用設定を行う
nodebrew install-binary v8.15.1
nodebrew use v8.15.1

# npm で、必要なパッケージをインストールする
cd app/webroot/theme/admin-third/__assets/
npm install

# タスクランナー実行
npm run dev
```

## 参考ドキュメント

- [Dockerを利用した開発方法（Mac）](https://github.com/baserproject/basercamp/blob/dev-4.2-admin-design/docker/README.md)
- [スタイルガイド](http://localhost/guide.html)
- [開発タスク](https://docs.google.com/spreadsheets/d/1LqDuPntPkR-2XHKR1B42vrXQhJz02UHzaURvaPCbgj8/edit#gid=0)
  
  
  
  
  
  
