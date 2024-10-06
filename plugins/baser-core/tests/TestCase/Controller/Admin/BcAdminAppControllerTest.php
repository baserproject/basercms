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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PermissionsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use \Cake\Http\Exception\NotFoundException;
use BaserCore\Controller\Admin\BcAdminAppController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Controller\BcAdminAppController Test Case
 */
class BcAdminAppControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * BcAdminApp
     * @var BcAdminAppController $BcAdminApp
     */
    public $BcAdminApp;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(PermissionsScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $request = $this->loginAdmin($this->getRequest());
        Router::setRequest($request);
        $this->BcAdminApp = new BcAdminAppController($request);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        Router::reload();
        unset($this->BcAdminApp);
    }
//
//    /**
//     * Test initialize method
//     *
//     * @return void
//     */
//    public function testInitialize()
//    {
//        $this->assertNotEmpty($this->BcAdminApp->BcMessage);
//        $this->assertNotEmpty($this->BcAdminApp->Authentication);
//        $this->assertFalse($this->BcAdminApp->FormProtection->getConfig('validate'));
//        $components = $this->BcAdminApp->components();
//        $components->unload('FormProtection');
//        $_ENV['IS_CONSOLE'] = false;
//        $this->BcAdminApp->initialize();
//    }
//
//    /**
//     * test beforeFilter
//     */
//    public function testBeforeFilter()
//    {
//        $this->loginAdmin($this->BcAdminApp->getRequest());
//        $this->BcAdminApp->beforeFilter(new Event('beforeFilter'));
//        $this->assertFalse(isset($_SESSION['Flash']['flash'][0]['message']));
//        $this->loginAdmin($this->BcAdminApp->getRequest(), 2);
//        $this->BcAdminApp->beforeFilter(new Event('beforeFilter'));
//        $this->assertEquals('指定されたページへのアクセスは許可されていません。', $_SESSION['Flash']['flash'][0]['message']);
//    }
//
//    /**
//     * Test beforeRender method
//     *
//     * @return void
//     */
//    public function testBeforeRender()
//    {
//        $event = new Event('Controller.beforeRender', $this->BcAdminApp);
//        // 拡張子指定なしの場合
//        $this->BcAdminApp->beforeRender($event);
//        $this->assertEquals('BaserCore.BcAdminApp', $this->BcAdminApp->viewBuilder()->getClassName());
//        $this->assertEquals("BcAdminThird", $this->BcAdminApp->viewBuilder()->getTheme());
//        // classNameをリセット
//        $this->BcAdminApp->viewBuilder()->setClassName('');
//        // 拡張子jsonの場合classNameがsetされないか確認
//        $this->BcAdminApp->setRequest($this->BcAdminApp->getRequest()->withParam('_ext', 'json'));
//        $this->RequestHandler->startup($event);
//        $this->BcAdminApp->beforeRender($event);
//        $this->assertEmpty($this->BcAdminApp->viewBuilder()->getClassName());
//    }
//
//    /**
//     * Test setSearch method
//     *
//     * @return void
//     */
//    public function testSetSearch()
//    {
//        $template = 'test';
//        $this->execPrivateMethod($this->BcAdminApp, 'setSearch', [$template]);
//
//        $viewBuilder = new ReflectionClass($this->BcAdminApp->viewBuilder());
//        $vars = $viewBuilder->getProperty('_vars');
//        $vars->setAccessible(true);
//        $actual = $vars->getValue($this->BcAdminApp->viewBuilder())['search'];
//        $this->assertEquals($template, $actual);
//    }
//
//    /**
//     * Test setHelp method
//     *
//     * @return void
//     */
//    public function testSetHelp()
//    {
//        $template = 'test';
//        $this->execPrivateMethod($this->BcAdminApp, 'setHelp', [$template]);
//
//        $viewBuilder = new ReflectionClass($this->BcAdminApp->viewBuilder());
//        $vars = $viewBuilder->getProperty('_vars');
//        $vars->setAccessible(true);
//        $actual = $vars->getValue($this->BcAdminApp->viewBuilder())['help'];
//        $this->assertEquals($template, $actual);
//    }

    /**
     * Test _checkReferer method
     * @dataProvider checkRefererDataProvider
     * @return void
     */
    public function testCheckReferer($referer, $expected)
    {
        $tmpHost = Configure::read('BcEnv.host');
        Configure::write('BcEnv.host', $referer? parse_url($referer)['host'] : null);
        $_SERVER['HTTP_REFERER'] = $referer;

        if ($expected === 'error') {
            Configure::write('BcEnv.host', parse_url('http://www.example2.com/')['host']);
            try {
                $this->execPrivateMethod($this->BcAdminApp, '_checkReferer');
            } catch (NotFoundException $e) {
                $this->assertStringContainsString("Not Found", $e->getMessage());
            }
        } else {
            $result = $this->execPrivateMethod($this->BcAdminApp, '_checkReferer');
            $this->assertEquals($result, $expected);
        }
        Configure::write('BcEnv.host', $tmpHost);
        unset($_SERVER['HTTP_REFERER']);
    }

    public static function checkRefererDataProvider()
    {
        return [
            // refererがnullの場合　
            [null, false],
            // refererがある場合
            ["http://www.example.com/", true],
            // refererが同サイトドメインでない場合
            ["http://www.example.com/", 'error'],
        ];
    }
//
//    /**
//     * test setAdminTheme
//     */
//    public function testSetAdminTheme()
//    {
//        $this->execPrivateMethod($this->BcAdminApp, 'setAdminTheme');
//        $this->assertEquals('BcAdminThird', $this->BcAdminApp->viewBuilder()->getTheme());
//        /* @var \BaserCore\Service\SiteConfigsServiceInterface $siteConfigService */
//        $siteConfigService = $this->getService(SiteConfigsServiceInterface::class);
//        $siteConfigService->setValue('admin_theme', 'test');
//        $this->execPrivateMethod($this->BcAdminApp, 'setAdminTheme');
//        $this->assertEquals('test', $this->BcAdminApp->viewBuilder()->getTheme());
//    }

    /**
     * test redirectIfIsNotSameSite
     */
    public function testRedirectIfIsNotSameSite()
    {
        $this->BcAdminApp->setRequest($this->getRequest('https://localhost/index'));
        $this->_response = $this->BcAdminApp->redirectIfIsNotSameSite();
        $this->assertNull($this->_response);
        $this->BcAdminApp->setRequest($this->getRequest('http://main.com/index'));
        $this->_response = $this->BcAdminApp->redirectIfIsNotSameSite();
        $this->assertRedirect('https://localhost/index');
        $this->BcAdminApp->setRequest($this->getRequest('https://localhost/baser/admin'));
        $this->_response = $this->BcAdminApp->redirectIfIsNotSameSite();
        $this->assertNull($this->_response);
        // ユニットテストにおいては、現在のサイトのURLの取得を https固定にしているため
        // http://localhost/ と https://localhost/ は同一サイトとして扱われリダイレクトしない
        $this->BcAdminApp->setRequest($this->getRequest('http://localhost/'));
        $this->_response = $this->BcAdminApp->redirectIfIsNotSameSite();
        $this->assertNull($this->_response);
    }

    /**
     * test checkPasswordModified
     */
    public function testCheckPasswordModified()
    {
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);

        $siteConfigsService->setValue('password_reset_days', 1);
        $this->get('/baser/admin');
        $this->assertResponseCode(200);

        // パスワード再設定画面にリダイレクト
        $users = TableRegistry::getTableLocator()->get('BaserCore.Users');
        $user = $users->get(1);
        $user->password_modified = new \DateTime('-2 days');
        $users->save($user);
        $this->get('/baser/admin');
        $this->assertResponseCode(302);

        $siteConfigsService->setValue('password_reset_days', 0);
        $this->get('/baser/admin');
        $this->assertResponseCode(200);
    }
}
