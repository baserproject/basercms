# ucmitzのプラグイン開発

ucmitz の管理画面を適用するには次の作業を行います。

　
## Plugin クラスの変更

`/src/Plugin.php` について、 `BcPlugin` を継承します。

```php
class Plugin extends BaserCore\BcPlugin
{
}
```
　
## Controller の配置と変更

管理画面を実装する Controller は、`Admin` ディレクトリ内に配置します。

また、 `BcAdminAppController` を継承します。
```php
/src/Controller/Admin/YourController.php
class YourController extends BaserCore\Controller\Admin\BcAdminAppController
{
}
```
　
## Template の配置

管理画面を実装する Template は、`Admin` ディレクトリ内に配置します。

```php
/src/template/Admin/YourController/action_name.php
```
　
## プラグインを有効化

管理画面にログインし、インストールを実行します。  
composerでの有効化や `dumpautoload` は不要です。インストールが完了すると自動的に読み込まれるようになります。

　
## 表示を確認

`https://host/baser/plugin-name/controller_name/action_name` でアクセスできます。

　
## テーブルを利用する場合

マイグレーションファイルを設置することで、インストール時に自動的に読み込まれます。

データベースのテーブルからマイグレーションファイルを作成する場合はコマンドで実行します。下記コマンドを実行すると、`/PluginName/config/Migrations/` 内に自動生成します。
```
bin/cake bake migration_snapshot Initial --plugin MyPlugin
```

※ 作り直す場合は、出来上がったファイルを削除する必要があります。  
※ マイグレーションファイルを作る際、composer でインストールされた状態である必要があります。

　
## インストール処理

インストール時に、マイグレーションファイルの読み込み以外に何らかの処理を行いたい場合は、Plugin クラスに記述します。

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

