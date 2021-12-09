<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase;

use App\Application;
use BaserCore\Plugin;
use BaserCore\Service\SiteConfigServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\Core\Container;
use Cake\Http\MiddlewareQueue;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Http\ServerRequest;
use Cake\Routing\Router;

/**
 * Class PluginTest
 * @package BaserCore\Test\TestCase
 * @property Plugin $Plugin
 */
class PluginTest extends BcTestCase
{
    /**
     * @var Plugin
     */
    public $Plugin;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents'
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->application = new Application(CONFIG);
        $this->Plugin = new Plugin(['name' => 'BaserCore']);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->application);
        unset($this->Plugin);
        parent::tearDown();
    }

    /**
     * test bootstrap
     *
     * @return void
     */
    public function testBootStrap(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test loadPlugin
     *
     * @return void
     */
    public function testLoadPlugin(): void
    {
        $priority = 1;
        $plugin = $this->Plugin->getName();
        $this->assertTrue($this->Plugin->loadPlugin($this->application, $plugin, $priority));
    }

    /**
     * test middleware
     *
     * @return void
     */
    public function testMiddleware(): void
    {
        $middleware = new MiddlewareQueue();
        $middlewareQueue = $this->Plugin->middleware($middleware);
        $this->assertInstanceOf(AuthenticationMiddleware::class, $middlewareQueue->current());
    }

    /**
     * test getAuthenticationService
     * @param string $prefix (Api|Admin|それ以外)
     * @param array $authenticators サービスの認証
     * @param string $identifiers サービスの識別
     * @param array $config サービスの設定
     * @var Authentication\AuthenticationService $service
     * @return void
     * @dataProvider getAuthenticationServiceDataProvider
     */
    public function testGetAuthenticationService($prefix, $authenticators, $identifiers, $config): void
    {
        $request = $this->getRequest()->withParam('prefix', $prefix);
        $service = $this->Plugin->getAuthenticationService($request);
        if($config) {
            foreach ($config as $key => $value) {
                $this->assertEquals($service->getConfig($key), $value);
            }
        }
        foreach($authenticators as $authenticator) {
            $this->assertNotEmpty($service->authenticators()->get($authenticator));
        }

        if ($identifiers) {
            $this->assertNotEmpty($service->identifiers()->get($identifiers));
        }
    }
    public function getAuthenticationServiceDataProvider()
    {
        return [
            // APIの場合
            ['Api', ['Jwt', 'Form'], 'JwtSubject', []],
            // Adminの場合
            ['Admin', ['Session', 'Form'], 'Password', ['unauthenticatedRedirect' => Router::url('/baser/admin/baser-core/users/login', true)]],
            // // それ以外の場合
            ['', ['Form'], '', []]
        ];
    }

    /**
     * test routes
     *
     * @return void
     */
    public function testRoutes(): void
    {
        $routes = Router::createRouteBuilder('/');
        $this->Plugin->routes($routes);

        // トップページ
        $result = Router::parseRequest($this->getRequest('/'));
        $this->assertEquals('index', $result['pass'][0]);
        // 管理画面（index付）
        $this->loginAdmin($this->getRequest());
        $result = Router::parseRequest($this->getRequest('/baser/admin/users/index'));
        $this->assertEquals('Users', $result['controller']);
        // API（.well-known）
        $result = Router::parseRequest($this->getRequest('/baser/api/baser-core/.well-known/jwks.json'));
        $this->assertEquals('json', $result['_ext']);
        // サイト
        Router::reload();
        $builder = Router::createRouteBuilder('/');
        // ルーティング設定をするために一旦　Router::setRequest() を実施
        Router::setRequest(new ServerRequest(['url' => '/en/']));
        $this->Plugin->routes($builder);
        $result = Router::parseRequest(new ServerRequest(['url' => '/en/baser-core/users/']));
        $this->assertEquals('index', $result['action']);
        $result = Router::parseRequest(new ServerRequest(['url' => '/en/baser-core/users/view']));
        $this->assertEquals('view', $result['action']);
    }

    /**
     * test services
     *
     * @return void
     */
    public function testServices(): void
    {
        $container = new Container();
        $this->Plugin->services($container);
        $this->assertTrue($container->has(SiteConfigServiceInterface::class));
    }

    /**
     * test setupDefaultTemplatesPath
     * テストの前に実行されていることが前提
     */
    public function testSetupDefaultTemplatesPath()
    {
        $this->assertEquals([
            ROOT . DS . 'plugins' . DS . 'bc-front' . DS . 'templates' . DS,
            ROOT . DS . 'templates' . DS
        ], Configure::read('App.paths.templates'));
    }

}
