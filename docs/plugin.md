# baserCMSのプラグイン開発

baserCMSの管理画面を適用するには次を行います。

## Plugin クラスの継承先を変更

`/src/Plugin.php` について、 `BaserCore\BcPlugin` を継承します。

## Controller を Admin フォルダに配置

管理画面を実装する Controller は、`/src/Controller/Admin/`  内に配置します。

## Controller では BcAdminAppController を継承

管理画面を実装する Controller では `BaserCore\Controller\Admin\BcAdminAppController` を継承します。

## Template を Admin フォルダに配置

管理画面を実装する Template は、`/src/template/Admin/{コントローラー名}/` 内に配置します。

## プラグインを有効化

管理画面にログインし、インストールを実行します。

## 表示を確認

`https://host/baser/plugin-name/controller_name/action_name` でアクセスできます。

## テーブルを利用する場合

マイグレーションファイルを設置することで、インストール時に自動的に読み込まれます。

マイグレーションファイルの作成はコマンドで実行します。下記コマンドを実行すると、`/PluginName/config/Migrations/` 内に自動生成します。
```
bin/cake bake migration_snapshot Initial --plugin MyPlugin
```

※ 作り直す場合は、出来上がったファイルを削除する必要があります。

## インストール処理

インストール時に何らかの処理を行いたい場合は、Plugin クラスに記述します。

```php
class Plugin extends \BaserCore\BcPlugin
{
    public function install($options = []) : bool
    {
        // ここに必要なインストール処理を記述
        return parent::install($options);
    }
}
```

