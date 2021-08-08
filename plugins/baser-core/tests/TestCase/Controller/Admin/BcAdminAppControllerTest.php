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

namespace BaserCore\Test\TestCase\Controller\Admin;

use ReflectionClass;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use \Cake\Http\Exception\NotFoundException;
use BaserCore\Controller\Admin\BcAdminAppController;

/**
 * BaserCore\Controller\BcAdminAppController Test Case
 */
class BcAdminAppControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminApp = new BcAdminAppController($this->loginAdmin($this->getRequest()));
        $this->RequestHandler = $this->BcAdminApp->components()->load('RequestHandler');
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
        unset($this->BcAdminApp, $this->RequestHandler);
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcAdminApp->BcMessage);
        $this->assertNotEmpty($this->BcAdminApp->Authentication);
        $this->assertNotEmpty($this->BcAdminApp->Paginator);
        // ログインしているユーザが削除された場合ログアウトされる
    }

    /**
     * Test setViewConditions method
     *
     * @return void
     */
    public function testSetViewConditions()
    {
        // Sessionテスト
        $this->testSaveViewConditions();
        $this->testLoadViewConditions();

        // Requestテスト
        $request = $this->BcAdminApp->getRequest();
        $BcAdminApp = new BcAdminAppController($this->loginAdmin($request));

            // namedオプションの場合
        $named =  [
            'named' => [
                'num' => 30,
            ]
        ];
        $this->execPrivateMethod($BcAdminApp, 'saveViewConditions', ['testModel', ['default' => $named]]);
        $this->execPrivateMethod($BcAdminApp, 'loadViewConditions', ['testModel', ['default' => $named]]);
        $this->assertEquals($named['named']['num'], $BcAdminApp->getRequest()->getParam('pass.num'));

            // クエリパラメータの場合

                // 設定のパラメータ
        $savedQuery = [
            'query' => [
                'list_type' => 1,
        ]];
                // 実際のクエリパラメータ
        $query = [
            'list_type' => 2,
        ];
        $BcAdminApp->setRequest($request->withQueryParams($query));
        $this->execPrivateMethod($BcAdminApp, 'saveViewConditions', ['testModel', ['default' => $savedQuery]]);
        $this->execPrivateMethod($BcAdminApp, 'loadViewConditions', ['testModel', ['default' => $savedQuery]]);
        $this->assertEquals($query, $BcAdminApp->getRequest()->getQueryParams());
    }

    /**
     * Test loadViewConditions method
     *
     * @return void
     */
    public function testLoadViewConditions()
    {
        $query = ['test' => 'test'];
        $named = ['test'];
        $request = $this->BcAdminApp->getRequest();
        $session = $request->getSession();
        $session->write('BcApp.viewConditions.PagesDisplay.named', $named);
        $session->write('BcApp.viewConditions.PagesDisplay.query', $query);
        $BcAdminApp = new BcAdminAppController($this->loginAdmin($request));
        $this->execPrivateMethod($BcAdminApp, 'loadViewConditions');
        $this->assertEquals($named[0], $BcAdminApp->getRequest()->getParam('pass')[0]);
        $this->assertEquals($query, $BcAdminApp->getRequest()->getParam('pass')['?']);
    }

    /**
     * Test saveViewConditions method
     *
     * @return void
     */
    public function testSaveViewConditions()
    {
        // namedオプション
        $named =  [
            'named' => [
                'num' => 30,
                'site_id' => 0,
                'list_type' => 1,
                'sort' => 'id',
                'direction' => 'asc'
            ]
        ];
        $request = $this->BcAdminApp->getRequest();
        $BcAdminApp = new BcAdminAppController($this->loginAdmin($request));
        $BcAdminApp->setRequest($request->withParam('named', $named));
        $this->execPrivateMethod($BcAdminApp, 'saveViewConditions', ['testModel', ['default' => $named]]);
        $this->assertSession($named, 'BcApp.viewConditions.PagesDisplay.named');

        // 実際のクエリパラメータ
        $query = [
            'list_type' => 1,
        ];
        // 追加設定されたクエリパラメータ
        $savedQuery = [
            'query' => [
                'queryStatus' => 'overriden',
            ]
        ];
        $BcAdminApp->setRequest($request->withQueryParams($query));
        $this->execPrivateMethod($BcAdminApp, 'saveViewConditions', ['testModel', ['default' => $savedQuery]]);
        $this->assertSession(array_merge($savedQuery['query'], $query), 'BcApp.viewConditions.PagesDisplay.query');
    }

    /**
     * Test beforeRender method
     *
     * @return void
     */
    public function testBeforeRender()
    {
        $event = new Event('Controller.beforeRender', $this->BcAdminApp);
        // 拡張子指定なしの場合
        $this->BcAdminApp->beforeRender($event);
        $this->assertEquals('BaserCore.BcAdminApp', $this->BcAdminApp->viewBuilder()->getClassName());
        $this->assertEquals("BcAdminThird", $this->BcAdminApp->viewBuilder()->getTheme());
        // classNameをリセット
        $this->BcAdminApp->viewBuilder()->setClassName('');
        // 拡張子jsonの場合classNameがsetされないか確認
        $this->BcAdminApp->setRequest($this->BcAdminApp->getRequest()->withParam('_ext', 'json'));
        $this->RequestHandler->startup($event);
        $this->BcAdminApp->beforeRender($event);
        $this->assertEmpty($this->BcAdminApp->viewBuilder()->getClassName());
    }

    /**
     * Test setTitle method
     *
     * @return void
     */
    public function testSetTitle()
    {
        $template = 'test';
        $this->execPrivateMethod($this->BcAdminApp, 'setTitle', [$template]);

        $viewBuilder = new ReflectionClass($this->BcAdminApp->viewBuilder());
        $vars = $viewBuilder->getProperty('_vars');
        $vars->setAccessible(true);
        $actual = $vars->getValue($this->BcAdminApp->viewBuilder())['title'];
        $this->assertEquals($template, $actual);
    }

    /**
     * Test setSearch method
     *
     * @return void
     */
    public function testSetSearch()
    {
        $template = 'test';
        $this->execPrivateMethod($this->BcAdminApp, 'setSearch', [$template]);

        $viewBuilder = new ReflectionClass($this->BcAdminApp->viewBuilder());
        $vars = $viewBuilder->getProperty('_vars');
        $vars->setAccessible(true);
        $actual = $vars->getValue($this->BcAdminApp->viewBuilder())['search'];
        $this->assertEquals($template, $actual);
    }

    /**
     * Test setHelp method
     *
     * @return void
     */
    public function testSetHelp()
    {
        $template = 'test';
        $this->execPrivateMethod($this->BcAdminApp, 'setHelp', [$template]);

        $viewBuilder = new ReflectionClass($this->BcAdminApp->viewBuilder());
        $vars = $viewBuilder->getProperty('_vars');
        $vars->setAccessible(true);
        $actual = $vars->getValue($this->BcAdminApp->viewBuilder())['help'];
        $this->assertEquals($template, $actual);
    }

    /**
     * Test _checkReferer method
     * @dataProvider checkRefererDataProvider
     * @return void
     */
    public function testCheckReferer($referer, $expected)
    {
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
    }

    public function checkRefererDataProvider()
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
}
