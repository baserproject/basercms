#　プログラムの構成

## コアの構成

### コアパッケージ
baserCMSのコア（BaserCore）は、CakePHPのプラグインとしての開発を前提とし、plugins 配下内に配置する仕様とした。 `/plugins/baser-core/`

### コアテーマ
CakePHPのプラグインとしての開発を前提とし、plugins 配下内に配置する仕様とした。 `/plugins/bc-admin-third/`
詳細については、[BcAdminThirdの開発](https://github.com/baserproject/ucmitz/blob/dev/plugins/bc-admin-third/README.md) を参考にする。

なお、テーマの定義は、現在、`BaserCore\Controller\BcAdminAppController::beforeRender()` にて行っている。

### プラグイン
その他のプラグインも、plugins 配下内に配置する。
コアパッケージ、コアテーマ以外のロードは、管理画面でプラグインをインストールする事で利用可能となる。

## プラグインフォルダの命名

コアパッケージ、コアテーマ、コアプラグインについては、ハイフン区切り（dasherize）とし、その他のプラグインについては、アッパーキャメルケースとする。
```
例）
コア：baser-core / bc-admin-third / bc-blog
その他：BcSample
```

