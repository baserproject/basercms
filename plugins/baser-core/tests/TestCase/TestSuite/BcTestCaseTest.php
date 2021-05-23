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

use Cake\TestSuite\TestCase;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\Core\Configure;
use Cake\Routing\Router;

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
        $urlList = ['' => '/', '/test' => '/{controller}', '/test/test' => '/{controller}/{action}/*'];
        foreach($urlList as $url => $route) {
            $request = $this->getRequest($url);
            $this->assertEquals($route, $request->getParam('_matchedRoute'));
        }
        // テストAttributeとsetRequest
        $request = $this->getRequest();
        $this->assertObjectHasAttribute('params', $request);
        $this->assertSame($request, Router::getRequest());
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
        $this->assertEquals($this->loginAdmin()->id, "1");
        // session書かれているかテスト
        $this->assertSession($this->loginAdmin(1), Configure::read('BcPrefixAuth.Admin.sessionKey'));
        $this->assertSession($this->loginAdmin(2), Configure::read('BcPrefixAuth.Admin.sessionKey'));
    }

}