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

namespace BaserCore\Test\TestCase\Controller;

use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Utility\BcContainer;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Response;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\AppController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use ReflectionClass;

/**
 * BaserCore\Controller\AppController Test Case
 * @property AppController $AppController
 */
class AppControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->AppController = new AppController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test construct
     *
     * @return void
     */
    public function testConstruct(): void
    {
        $this->assertNotEmpty($this->getRequest()->getSession());
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->AppController->BcMessage);
        $this->assertNotEmpty($this->AppController->FormProtection);
        $this->assertTrue($this->AppController->FormProtection->getConfig('validate'));
        $this->assertEquals(['x', 'y', 'MAX_FILE_SIZE'], $this->AppController->FormProtection->getConfig('unlockedFields'));
        $callback = $this->AppController->FormProtection->getConfig('validationFailureCallback');
        $this->expectException("Cake\Http\Exception\BadRequestException");
        $this->expectExceptionMessage("不正なリクエストと判断されました\nもしくは、システムが受信できるデータ上限より大きなデータが送信された可能性があります。\n不正なリクエストです。");
        $callback(new BadRequestException('不正なリクエストです。'));
    }

    /**
     * test beforeFilter
     */
    public function testBeforeFilter()
    {
        $expectCache = 'no-store, no-cache, must-revalidate, post-check=0, pre-check=0';

        // Ajaxの場合はノーキャッシュヘッダーを付ける
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $this->AppController->setResponse(new Response());
        $this->AppController->beforeFilter(new Event('beforeFilter'));
        unset($_SERVER['HTTP_X_REQUESTED_WITH']);
        $this->assertEquals($expectCache, $this->AppController->getResponse()->getHeader('Cache-Control')[0]);

        // ログインしている場合はノーキャッシュヘッダーを付ける
        $this->loginAdmin($this->getRequest());
        $this->AppController->setResponse(new Response());
        $this->AppController->beforeFilter(new Event('beforeFilter'));
        $this->assertEquals($expectCache, $this->AppController->getResponse()->getHeader('Cache-Control')[0]);

        // requestview が false の場合は、ログインしていてもノーキャッシュヘッダーを付けない
        $this->AppController->setRequest($this->getRequest('/?requestview=false'));
        $this->AppController->setResponse(new Response());
        $this->AppController->beforeFilter(new Event('beforeFilter'));
        $this->assertEmpty($this->AppController->getResponse()->getHeader('Cache-Control'));

        // インストーラー、アップデーターでない場合はここでテーマをセットしない
        $this->assertEquals('', $this->AppController->viewBuilder()->getTheme());

        // TODO ucmitz インストーラー実装後に対応する（ルーティングが解決できない）
//        $this->AppController->setRequest($this->getRequest('/baser/installations/index'));
//        $this->assertEquals('BcAdminThird', $this->AppController->viewBuilder()->getTheme());
        // TODO ucmitz アップデーター実装後に対応する（ルーティングが解決できない）
//        $this->AppController->setRequest($this->getRequest('/baser/updaters/index'));
//        $this->assertEquals('BcAdminThird', $this->AppController->viewBuilder()->getTheme());
    }

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

    /**
     * test beforeRender
     */
    public function test_beforeRender()
    {
        $this->AppController->beforeRender(new Event('beforeRender'));
        $this->assertEquals('BcAdminThird', $this->AppController->viewBuilder()->getVars()['currentAdminTheme']);
    }

    /**
     * test checkPermission
     */
    public function testCheckPermission()
    {
        //準備
        $permissionsService = $this->getService(PermissionsServiceInterface::class);
        $permissionsService->addCheck("/fuga", false);
        $permissionsService->addCheck("/piyo", true);

        Configure::write('BcApp.adminGroupId', 2);
        $this->loginAdmin($this->getRequest('/'));

        //result = false test
        $this->AppController->setRequest($this->getRequest('/fuga'));
        $this->assertFalse($this->execPrivateMethod($this->AppController, 'checkPermission', []));

        //result = true test
        $this->AppController->setRequest($this->getRequest('/piyo'));
        $this->assertTrue($this->execPrivateMethod($this->AppController, 'checkPermission', []));
    }

    /**
     * Test setupFrontView
     */
    public function test_setupFrontView()
    {
        $this->AppController->setupFrontView();
        $this->assertEquals('BaserCore.BcFrontApp', $this->AppController->viewBuilder()->getClassName());
        $this->assertEquals('BcFront', $this->AppController->viewBuilder()->getTheme());
        $request = $this->AppController->getRequest();
        $site = $request->getAttribute('currentSite');
        $site['theme'] = 'test';
        $request = $request->withParam('Site', $site);
        $this->AppController->setRequest($request);
        $this->AppController->setupFrontView();
        $this->assertEquals('test', $this->AppController->viewBuilder()->getTheme());
    }

    /**
     * Test setTitle method
     *
     * @return void
     */
    public function testSetTitle()
    {
        $template = 'test';
        $this->AppController->setTitle($template);
        $viewBuilder = new ReflectionClass($this->AppController->viewBuilder());
        $vars = $viewBuilder->getProperty('_vars');
        $vars->setAccessible(true);
        $actual = $vars->getValue($this->AppController->viewBuilder())['title'];
        $this->assertEquals($template, $actual);
    }

    /**
     * test redirectIfIsRequireMaintenance
     */
    public function testRedirectIfIsRequireMaintenance()
    {
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        $siteConfig = BcContainer::get()->get(SiteConfigsServiceInterface::class);
        $siteConfig->setValue('maintenance', true);
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        Configure::write('debug', false);
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertRedirect('/maintenance');
        $this->AppController->setRequest($this->getRequest('/', [], 'GET', ['ajax' => true]));
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        $this->AppController->setRequest($this->getRequest('https://localhost/baser/admin'));
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        $this->loginAdmin($this->getRequest());
        $this->_response = $this->AppController->redirectIfIsRequireMaintenance();
        $this->assertNull($this->_response);
        Configure::write('debug', true);
    }

    /**
     * test __cleanupQueryParams
     */
    public function test__cleanupQueryParams()
    {
        $this->AppController->setRequest($this->getRequest('/index?a=1&amp;b=2'));
        $this->execPrivateMethod($this->AppController, '__cleanupQueryParams');
        $result = $this->AppController->getRequest()->getQueryParams();
        $this->assertEquals(['a' => '1', 'b' => '2'], $result);
    }

    /**
     * test notFound
     */
    public function test_notFound()
    {
        $this->expectException("Cake\Http\Exception\NotFoundException");
        $this->expectExceptionMessage("見つかりませんでした。");
        $this->AppController->notFound();
    }

    /**
     * test saveViewConditions
     */
    public function test_saveViewConditions()
    {
        // クエリパラメーターが保存されるテスト
        $this->AppController->setRequest($this->getRequest()->withQueryParams(['limit' => 10]));
        $options = ['group' => 'index', 'post' => false, 'get' => true];
        $this->execPrivateMethod($this->AppController, 'saveViewConditions', [['Content'], $options]);
        $session = $this->AppController->getRequest()->getSession();
        $query = $session->read('BcApp.viewConditions.PagesView.index.query');
        $this->assertEquals(['limit' => 10], $query);

        // POSTデータが保存されるテスト
        $this->AppController->setRequest($this->getRequest()->withData('title', 'default'));
        $options = ['group' => 'index', 'post' => true, 'get' => false];
        $this->execPrivateMethod($this->AppController, 'saveViewConditions', [['Content'], $options]);
        $session = $this->AppController->getRequest()->getSession();
        $query = $session->read('BcApp.viewConditions.PagesView.index.data.Content');
        $this->assertEquals(['title' => 'default'], $query);
    }

    /**
     * test loadViewConditions
     */
    public function test_loadViewConditions()
    {
        // セッションデータからクエリパラメーターを設定する
        $options = [
            'group' => 'index',
            'default' => ['Content' => ['q' => 'keyword'], 'query' => ['limit' => 10]],
            'post' => false,
            'get' => true
        ];
        $request = $this->getRequest();
        $request->getSession()->write('BcApp.viewConditions.PagesView.index.query', ['id' => 1]);
        $this->AppController->setRequest($request);
        $this->execPrivateMethod($this->AppController, 'loadViewConditions', [['Content'], $options]);
        $this->assertEquals(['limit' => 10, 'id' => 1], $this->AppController->getRequest()->getQueryParams());
        $this->assertEquals(['q' => 'keyword'], $this->AppController->getRequest()->getParsedBody());

        // セッションデータからPOSTデータを設定する
        $options = ['group' => 'index', 'default' => ['Content' => ['q' => 'keyword']], 'post' => true, 'get' => false];
        $request = $this->getRequest();
        $request->getSession()->write('BcApp.viewConditions.PagesView.index.data.Content', ['title' => 'default']);
        $this->AppController->setRequest($request);
        $this->execPrivateMethod($this->AppController, 'loadViewConditions', [['Content'], $options]);
        $this->assertEmpty($this->AppController->getRequest()->getQueryParams());
        $this->assertEquals(['q' => 'keyword', 'title' => 'default'], $this->AppController->getRequest()->getParsedBody());
    }

    /**
     * test setViewConditions
     */
    public function test_setViewConditions()
    {
        $targetModel = ['Content'];
        $options = [
            'group' => 'index',
            'default' => [
                'query' => ['limit' => 10],
                'Content' => ['title' => 'default']
            ],
            'get' => true,
            'post' => true,
        ];
        $request = $this->getRequest()->withQueryParams(['limit' => 10])->withData('title', 'default');
        $this->AppController->setRequest($request);
        $this->execPrivateMethod($this->AppController, 'setViewConditions', [$targetModel, $options]);

        $this->assertEquals(['limit' => 10], $this->AppController->getRequest()->getQueryParams());
        $this->assertEquals(['title' => 'default'], $this->AppController->getRequest()->getParsedBody());

        $session = $this->AppController->getRequest()->getSession();
        $query = $session->read('BcApp.viewConditions.PagesView.index.query');
        $this->assertEquals(['limit' => 10], $query);
        $data = $session->read('BcApp.viewConditions.PagesView.index.data.Content');
        $this->assertEquals(['title' => 'default'], $data);
    }


    /**
     * Test saveDblog
     *
     * @return void
     * @dataProvider saveDblogDataProvider
     */
    public function testSaveDblog(string $message, int $userId = null): void
    {
        $request =$this->getRequest('/baser/admin/baser-core/users/');
        if (isset($userId)) $this->loginAdmin($request, $userId);

        $this->execPrivateMethod($this->AppController, 'saveDblog', [$message]);

        $where = [
            'message' => $message,
            'controller' => 'Users',
            'action' => 'index'
        ];
        if (isset($userId)) {
            $where['user_id'] = $userId;
        } else {
            $where['user_id IS'] = null;
        }

        $dblogs = $this->getTableLocator()->get('Dblogs');
        $query = $dblogs->find()->where($where);
        $this->assertSame(1, $query->count());
    }

    public static function saveDblogDataProvider(): array
    {
        return [
            ['dblogs testSaveDblog message guest', null],
            ['dblogs testSaveDblog message login', 1]
        ];
    }

    /**
     * Test ajaxError
     * @param $errorNo
     * @param $message
     * @param $expectedOutput
     * @dataProvider ajaxErrorDataProvider
     */
    public function testAjaxError($errorNo, $message, $expectedOutput)
    {
        ob_start();
        $this->AppController->ajaxError($errorNo, $message);
        $output = ob_get_clean();

        $response = $this->AppController->getResponse();

        $this->assertEquals($errorNo, $response->getStatusCode());
        $this->assertEquals($expectedOutput, $output);
    }

    public static function ajaxErrorDataProvider()
    {
        return [
            [400, 'This is an error message.', "This is an error message."],
            [404, ['Error 1', 'Error 2'], "Error 1<br>Error 2"],
            [500, [['Error 1a', 'Error 1b'], 'Error 2'], "Error 1a<br />Error 1b<br>Error 2"],
            [500, '', '']
        ];
    }

    /**
     * Test requirePermission
     */
    public function testRequirePermission()
    {
        $request = $this->getRequest('/baser/admin/baser-core/users/');
        $this->assertTrue($this->AppController->requirePermission($request));

        $request = $request->withParam('prefix', 'Mypage');
        $this->assertTrue($this->AppController->requirePermission($request));

        Configure::write('BcPrefixAuth.Mypage.requirePermission', true);
        $this->assertTrue($this->AppController->requirePermission($request));

        Configure::write('BcPrefixAuth.Mypage.requirePermission', false);
        $this->assertFalse($this->AppController->requirePermission($request));
    }

}
