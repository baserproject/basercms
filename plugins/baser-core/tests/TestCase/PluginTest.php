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
use BaserCore\TestSuite\BcTestCase;
use Cake\Http\MiddlewareQueue;
use Authentication\Middleware\AuthenticationMiddleware;
use Cake\Routing\Router;
use Cake\Utility\Security;

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
        'plugin.BaserCore.Plugins',
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
        $this->Plugin = new Plugin(['name' => 'BcBlog']);
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test services
     *
     * @return void
     */
    public function testServices(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
