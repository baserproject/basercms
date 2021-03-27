# パッケージ構成

BaserApp を親パッケージとして、BaserCore、BcAdminThird など必要とするパッケージを CakePHP のプラグインとして開発し、子パッケージとして管理しています。  

　
## コアの構成

### 親パッケージ

Gitでクローンした本体。親パッケージとして ucmitz のアプリケーションフレームを提供します。

- [ucmitz](https://github.com/baserproject/ucmitz) 

　

### コアパッケージ

baserCMSのコア（BaserCore）は、CakePHPのプラグインとしての開発を前提とし、`/plugins/` 配下に配置する仕様としています。 
子パッケージとして主にURLに紐づくルーティングと、ビジネスロジックを提供します。
- [BaserCore](https://github.com/baserproject/ucmitz/tree/dev/plugins/baser-core) 

　

### コアテーマ
CakePHPのプラグインとしての開発を前提とし、`/plugins/` 配下に配置する仕様する仕様としています。
子パッケージとして、baserCMSの画面表示をテーマとして提供します。

- [BcAdminThird](https://github.com/baserproject/ucmitz/tree/dev/plugins/bc-admin-third) 

　

### コアプラグイン
ブログ、メールフォーム、アップローダーは、CakePHPのプラグインとしての開発を前提とし、`/plugins/` 配下に配置する仕様としています。

- [BcBlog](https://github.com/baserproject/ucmitz/tree/dev/plugins/bc-blog)
- [BcMail](https://github.com/baserproject/ucmitz/tree/dev/plugins/bc-mail)
- [BcUploader](https://github.com/baserproject/ucmitz/tree/dev/plugins/bc-uploader)


その他のプラグインも同様に、CakePHPのプラグインとしての開発を前提とし、 `/plugins/` 配下内に配置します。  
なお、コアパッケージ、コアテーマ以外のプラグインのロードは、 composer でのインストールは不要で、管理画面でプラグインをインストールする事で利用可能となります。

　

### プラグインフォルダの命名

コアパッケージ、コアテーマ、コアプラグインについては、ハイフン区切り（dasherize）とし、その他のプラグインについては、アッパーキャメルケースとします。

```
例）
コア：baser-core / bc-admin-third / bc-blog
その他：BcSample
```

　

## モノレポによるパッケージ管理

複数のパッケージを統合的に管理するためにPHP用のモノレポ 「monorepo-builder」 を利用しています。
リリース時に次のレポジトリに分割してコミットされます。

- [ucmitz ソースコード / baserproject/ucmitz](https://github.com/baserproject/ucmitz/tree/master)
- [BaserCore ソースコード / baserproject/baser-core](https://github.com/baserproject/baser-core/tree/master)
- [BcAdminThird ソースコード / baserproject/bc-admin-third](https://github.com/baserproject/bc-admin-third/tree/master)

　

### 統合的なパッケージ管理

子パッケージの `composer.json` 記述したパッケージは、`monorepo-builder` により、親パッケージの `composer.json` にまとめあげることができ、`vendor` ディレクトリも親の `vendor` で統合的に管理することができます。

そのため、子パッケージの `composer.lock` と `vendor` ディレクトリは利用しません。（.gitignore で除外済です）

　
### 子パッケージの composer の構成を変更する

子パッケージの `composer.json` を変更した場合は、次のコマンドを実行して親パッケージにまとめあげる必要があります。

```shell script
vendor/bin/monorepo-builder merge
```

　
### パッケージのリリース
モノレポを使うことで、GitHub 上のパッケージごとのレポジトリへ、一括でリリースすることができます。
ただし、子パッケージは、読み取り専用の扱いとします。

子パッケージのリリースを行う場合は、次のコマンドを実行します。

```shell script
vendor/bin/monorepo-builder split
```

　
### 暫定的な公開

`split` コマンドは、`master` ブランチをベースに子パッケージを分割する仕様となっているため、開発中は GitHub に反映することができませんが、暫定的に GitHub に公開するために次の二つの方法で対応することができます。

1. 一時的に、開発ブランチから master ブランチを作り出して `split` を実行する
2. `monorepo-builder` のソースコードを一時的に改修して `split` を実行する

2 に関しては、一時的に次のファイルの編集必要です。

```
/vendor/symplify/monorepo-builder/packages/Split/src/Process/ProcessFactory.php
53行目
--branch=master
　　　↓
--branch=dev
```

ucmitz を開発中は、2を利用して、開発中のコードを定期的に GitHub に公開します。

　

### 参考文献

[MonorepoBuilderでPHPのモノレポを作るチュートリアル](https://qiita.com/suin/items/421a55bdb009b2ada2d1)





