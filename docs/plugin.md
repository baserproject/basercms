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

