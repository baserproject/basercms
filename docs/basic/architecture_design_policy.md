# アーキテクチャー設計方針

## ビジネスロジックの実装対象

ビジネスロジックの実装は、テーブルクラスではなく、
サービスクラスに実装し、コントローラーにおいては、テーブルクラスを直接利用することはせず、
そのサービスクラスを利用します。

　
## サービスクラスの実装

サービスクラスは、テーブルクラスと対になるクラスを用意してもよいですが、
基本的には役割ごとに用意することが望ましいです。

- ユーザーを管理する（一覧表示、登録、編集、削除）
- プラグインをインストールしアンインストールする

最初は１つのクラスで実装を行い、肥大化に伴い役割を分けファイルを分割するとよいでしょう。

サービスクラスを作成する際は、まずインターフェイスを定義します。

```php
// baser-core/src/Service/Admin/UserManageServiceInterface.php
interface UserManageServiceInterface
{
    public function get($id): EntityInterface;
}
```
作成したインターフェイスを実装するようにサービスクラスを定義します。
```php
// baser-core/src/Service/Admin/UserManageService.php
class UserManageService implements UserManageServiceInterface
{
    public function get($id)
    {
        // 実装
    }
}
```

管理画面用として利用するサービスクラスの名前空間は次のようにしてください。

```php
namespace BaserCore\Service\Admin;
```

API用として利用するサービスクラスの名前空間は次のようにしてください。

```php
namespace BaserCore\Service\Api;
```

なお、サービスクラスは状態を持たないように実装します。

　
## 利用するサービスの定義

利用するサービスはサービスプロバイダで定義します。

メンバー変数 `provides` に利用するサービスのインターフェイスを定義し、`services` メソッドにて、
DIコンテナを利用して、利用するサービスのインターフェイスごとに、実装するサービスクラスを追加します。
```php
// baser-core/src/ServiceProvider/BcServiceProvider.php
class BcServiceProvider extends ServiceProvider
{
    protected $provides = [
        UsersServiceInterface::class,
        UserManageServiceInterface::class
    ];
    
    public function services($container): void
    {
        $container->add(UsersServiceInterface::class, UsersService::class);
        $container->add(UserManageServiceInterface::class, UserManageService::class);
    }

}
```

　
## コントーローラーの実装

コントローラーでは、引数に利用したいサービスのインターフェイスを定義すると、
サービスプロバイダで定義したサービスクラスを利用できます。

```php
// baser-core/src/Controller/Admin/UsersController.php
class UsersController extends BcAdminAppController
{
    public function index(UserManageServiceInterface $userManage)
    {
        // 処理を記載
    }
}
```

コントローラーではビジネスロジックをできるだけ実装せず、サービスクラスに実装して、それを利用します。

また、コントローラー内ではテーブルクラスを直接利用せつ、サービスクラスに利用する処理を実装し、そちらを利用するようにします。

　
## コントローラーからビューへの変数の引き渡し

サービスクラスから提供されるデータ以外の、ビューの表示用の変数などは、できるだけコントローラーから引き渡さず、
ヘルパーで取得するようにします。

ヘルパに実装することでテスタブルなコードを目指します。

　
## ヘルパーの実装

ヘルパーについて、ビジネスロジックに関するものは、内部的にサービスクラスを利用し、サービスクラスのラッパーとなるように実装します。  
なお、CakePHP4では、ヘルパでのDIコンテナの利用ができませんので、代替措置として `BcContainerTrait ` を利用します。

```php
// baser-core/src/View/Helper/BcAdminUserHelper.php
class BcAdminUserHelper extends Helper
{
    use \BaserCore\Utility\BcContainerTrait;
    
    public $userManageService;
    
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->userManageService = $this->getService(UserManageServiceInterface::class)
    }
    
    public function get($id)
    {
        $this->userManageService->get($id);
    }
}
```

管理画面用のサービスクラスを利用するヘルパーのクラス名は次のようにしてください。

```php
BcAdmin{EntityName}Helper
```

なお、ラッパーとして実装したメソッドについては、サービスクラス側でテストを書き、ヘルパー側ではテストを書いたものとし、ヘルパーではテストは書きません。

　
## APIの実装

APIは、コントローラーからサービスクラスを呼び出し実装します。

`BcApiController` を継承することにより簡易的な認証がかかります。  
※ 認証処理については、将来的にセキュリティ性の高いJWTなどを利用する予定です。

```php
// baser-core/src/Controller/Api/UsersController.php
class UsersController extends BcApiController
{
    public function index(UsersServiceInterface $users)
    {
        // 実装
    }
}
```
### 参考
https://book.cakephp.org/4/ja/development/dependency-injection.html
