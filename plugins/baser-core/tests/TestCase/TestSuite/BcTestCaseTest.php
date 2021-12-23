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

namespace BaserCore\Test\TestCase\TestSuite;

use Cake\Http\Session;
use Cake\Core\Configure;
use Cake\Routing\Router;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\AnalyseController;

/**
 * BaserCore\TestSuite\BcTestCase
 *
 */
class BcTestCaseTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.LoginStores',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
    ];

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Request を取得するテスト
     *
     * @return void
     */
    public function testGetRequest(): void
    {
        // デフォルトURL $url = '/'
        $urlList = ['' => '/*', '/about' => '/*', '/baser/admin/users/login' => '/baser/admin/{controller}/{action}/*'];
        foreach($urlList as $url => $route) {
            $request = $this->getRequest($url);
            $this->assertEquals($route, $request->getParam('_matchedRoute'));
        }
        // テストAttributeとsetRequest
        $request = $this->getRequest();
        $this->assertObjectHasAttribute('params', $request);
        $this->assertSame($request, Router::getRequest());
        // configを設定する場合
        $session = new Session();
        $session->write('test', 'testGetRequest');
        $request = $this->getRequest('/', [], 'GET', ['session' => $session]);
        $this->assertEquals('testGetRequest', $request->getSession()->read('test'));

    }

    /**
     * サンプル用のユーザーを取得するのテスト
     *
     * @return void
     */
    public function testGetUser(): void
    {
        // デフォルト引数が1かテスト
        $this->assertEquals($this->getUser()->id, "1");
        // サンプル用のデータを取得できてるかテスト
        $this->assertEquals($this->getUser(1)->email, "testuser1@example.com");
        $this->assertEquals($this->getUser(2)->email, "testuser2@example.com");
    }

    /**
     * 管理画面にログインするのテスト
     *
     * @return void
     */
    public function testLoginAdmin(): void
    {
        // デフォルト引数が1かテスト
        $this->assertEquals($this->loginAdmin($this->getRequest())->getAttribute('authentication')->getIdentity()->getOriginalData()->id, "1");
        // session書かれているかテスト
        $this->assertSession($this->loginAdmin($this->getRequest())->getAttribute('authentication')->getIdentity()->getOriginalData(), Configure::read('BcPrefixAuth.Admin.sessionKey'));
        $this->assertSession($this->loginAdmin($this->getRequest(), 2)->getAttribute('authentication')->getIdentity()->getOriginalData(), Configure::read('BcPrefixAuth.Admin.sessionKey'));
    }

    /**
     * test apiLoginAdmin
     */
    public function testApiLoginAdmin(): void
    {
        $this->assertNotEmpty($this->apiLoginAdmin(1));
        $this->assertEmpty($this->apiLoginAdmin(100));
    }

    /**
     * test プライベートメソッド実行
     *
     * @return void
     */
    public function testExecPrivateMethod(): void
    {
        $sampleClass = new AnalyseController();
        $samplePrivateMethod = 'pathToClass';
        $result = $this->execPrivateMethod($sampleClass, $samplePrivateMethod, [ROOT . DS . "plugins"]);
        $this->assertEquals("", $result);
    }

}
