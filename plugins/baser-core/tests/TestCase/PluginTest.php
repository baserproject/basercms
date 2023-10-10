<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase;

use App\Application;
use BaserCore\Plugin;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BaserCore\Middleware\BcRequestFilterMiddleware;
use Cake\Core\Configure;
use Cake\Core\Container;
use Cake\Event\EventManager;
use Cake\Http\Middleware\CsrfProtectionMiddleware;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Router;
use Cake\Filesystem\File;

/**
 * Class PluginTest
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
        $event = EventManager::instance();

        $this->assertTrue(is_array($event->listeners('Controller.initialize')));

        $this->assertTrue(is_array($event->listeners('Model.beforeFind')));

        $this->assertTrue(is_array($event->listeners('View.beforeRenderFile')));

        $this->assertTrue(is_array($event->listeners('Application.beforeFind')));

        $this->assertTrue(is_array($event->listeners('BaserCore.Contents.afterMove')));

        $this->assertTrue(is_array($event->listeners('BaserCore.Contents.Pages')));

        $this->assertNotNull($this->application->getPlugins()->get('Authentication'));
        $this->assertNotNull($this->application->getPlugins()->get('Migrations'));

        $pathsPluginsExpected = [
            '/var/www/html/plugins/',
            '/var/www/html/vendor/baserproject/',
        ];

        $this->assertEquals($pathsPluginsExpected, Configure::read('App.paths.plugins'));

        $this->assertNotNull(Configure::read('BcApp.coreAdminTheme'));
        $this->assertNotNull(Configure::read('BcApp.coreFrontTheme'));

        $plugins = BcUtil::getEnablePlugins();
        foreach ($plugins as $plugin) {
            $this->assertNotNull($this->application->getPlugins()->get($plugin['name']));
        }

        $this->assertNotNull(\Cake\Core\Plugin::getCollection()->get('DebugKit'));
        $this->assertEquals('/var/www/html/plugins/' . Configure::read('BcApp.coreFrontTheme') . '/templates/', Configure::read('App.paths.templates')[0]);

        copy('config/.env','config/.env.bak');

        $file = new File('config/.env');
        $file->write('export APP_NAME="baserCMS"
export DEBUG="true"
export APP_ENCODING="UTF-8"
export APP_DEFAULT_LOCALE="en_US"
export APP_DEFAULT_TIMEZONE="Asia/Tokyo"

export INSTALL_MODE="false"
export SITE_URL="https://localhost/"
export SSL_URL="https://localhost/"
export ADMIN_SSL="true"
export ADMIN_PREFIX="admin"
export BASER_CORE_PREFIX="baser"
export SQL_LOG="false"
');
        $file->close();

        $fileSetting = new File('config/setting.php');
        $fileSetting->write('<?php
return [];
');

        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->Plugin->bootstrap($this->application);
        $this->assertEquals('/var/www/html/plugins/' . Configure::read('BcApp.coreAdminTheme') . '/templates/', Configure::read('App.paths.templates')[0]);

        $this->assertNotNull(\Cake\Core\Plugin::getCollection()->get('DebugKit'));

        $this->assertTrue(Configure::isConfigured('baser'));

        $fileSetting->delete();
        copy('config/.env.bak','config/.env');
        $fileEnvBak = new File('config/.env.bak');
        $fileEnvBak->delete();
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
        $middleware->add(CsrfProtectionMiddleware::class);
        $middlewareQueue = $this->Plugin->middleware($middleware);
        $this->assertInstanceOf(BcRequestFilterMiddleware::class, $middlewareQueue->current());
        $this->assertEquals(6, $middlewareQueue->count());
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
                if($key === 'unauthenticatedRedirect') {
                    $value = Router::url($value, true);
                }
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
            // Api/Admin の場合
            ['Api/Admin', ['Jwt', 'Form'], 'JwtSubject', []],
            // Adminの場合
            ['Admin', ['Session', 'Form'], 'Password', ['unauthenticatedRedirect' => '/baser/admin/baser-core/users/login']],
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
        $result = Router::parseRequest($this->getRequest('/baser/admin'));
        $this->assertEquals('Dashboard', $result['controller']);
        // API（.well-known）
        $result = Router::parseRequest($this->getRequest('/baser/api/baser-core/.well-known/jwks.json'));
        $this->assertEquals('json', $result['_ext']);
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
        $this->assertTrue($container->has(SiteConfigsServiceInterface::class));
    }

    /**
     * test setupDefaultTemplatesPath
     * テストの前に実行されていることが前提
     */
    public function testSetupDefaultTemplatesPath()
    {
        $this->assertEquals([
            ROOT . DS . 'plugins' . DS . 'bc-front' . DS . 'templates' . DS,
            ROOT . DS . 'vendor' . DS . 'baserproject' . DS . 'bc-front' . DS . 'templates' . DS,
            ROOT . DS . 'plugins' . DS . 'bc-admin-third' . DS . 'templates' . DS,
            ROOT . DS . 'vendor' . DS . 'baserproject' . DS . 'bc-admin-third' . DS . 'templates' . DS,
            ROOT . DS . 'templates' . DS,
        ], Configure::read('App.paths.templates'));
    }

}
