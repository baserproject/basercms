# baserCMSのプラグイン開発

baserCMSの管理画面を適用するには次を実行します。

## Plugin クラスの継承先を変更

`/src/Plugin.php` について、 `BaserCore\BcAdminPlugin` を継承します。

## Controller を Admin フォルダに配置

Controller は、`/src/Controller/Admin/`  内に配置します。

## Controller では BcAdminAppController を継承

Controller では `BaserCore\Controller\Admin\BcAdminAppController` を継承します。

## Template を Admin フォルダに配置

Template は、`/src/Template/Admin/{コントローラー名}/` 内に配置します。

## プラグインを有効化

`Application::bootstrap()` 内に、`$this->addPlugin('PluginName')` を追記します。

## 表示を確認

`https://host/baser/plugin-name/controller_name/action_name` でアクセスできます。

