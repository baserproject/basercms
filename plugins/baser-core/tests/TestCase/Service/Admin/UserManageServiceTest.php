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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Model\Table\LoginStoresTable;
use BaserCore\Service\Admin\UserManageService;
use Cake\Http\Response;

/**
 * Class UserManageServiceTest
 * @package BaserCore\Test\TestCase\Service
 */
class UserManageServiceTest extends \BaserCore\TestSuite\BcTestCase
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
        'plugin.BaserCore.LoginStores'
    ];

    /**
     * @var UserManageService|null
     */
    public $UserManage = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UserManage = new UserManageService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserManage);
        parent::tearDown();
    }

    /**
     * Test isSelfUpdate
     * @param int $loginId
     * @param int $postId
     * @param bool $expected
     * @dataProvider isSelfUpdateDataProvider
     */
    public function testIsSelfUpdate($loginId, $postId, $expected)
    {
        $request = $this->getRequest();
        if ($loginId) {
            $this->loginAdmin($request, $loginId);
        }
        $result = $this->UserManage->isSelfUpdate($postId);
        $this->assertEquals($expected, $result);
    }

    public function isSelfUpdateDataProvider()
    {
        return [
            [null, null, false], // 新規登録
            [null, 1, false],    // 更新
            [1, 1, true],        // 自身を更新
            [1, 2, false]        // 他人を更新
        ];
    }

    /**
     * Test isEditable
     * @param int $loginId
     * @param int $postId
     * @param bool $expected
     * @dataProvider isEditableDataProvider
     */
    public function testIsEditable($loginId, $postId, $expected)
    {
        $request = $this->getRequest();
        if ($loginId) {
            $this->loginAdmin($request, $loginId);
        }
        $result = $this->UserManage->isEditable($postId);
        $this->assertEquals($expected, $result);
    }

    public function isEditableDataProvider()
    {
        return [
            [null, null, false],  // 未ログイン新規
            [1, null, false], //ログイン新規
            [null, 1, false],   // 未ログイン更新
            [1, 1, true],   // ログイン更新
            [2, 1, false]   // 管理者以外ログイン更新
        ];
    }

    /**
     * Test isDeletable
     * @param int $loginId
     * @param int $postId
     * @param bool $expected
     * @dataProvider isDeletableDataProvider
     */
    public function testIsDeletable($loginId, $postId, $expected)
    {
        $request = $this->getRequest();
        if ($loginId) {
            $this->loginAdmin($request, $loginId);
        }
        $result = $this->UserManage->isDeletable($postId);
        $this->assertEquals($expected, $result);
    }

    public function isDeletableDataProvider()
    {
        return [
            [null, null, false],  // 未ログインデータ不完全
            [null, 1, false],   // 未ログイン削除
            [1, 2, true],   // 管理者ログイン削除
            [1, 1, false],   // 管理者ログイン自分を削除
            [2, 1, false]   // 非管理者ログイン削除
        ];
    }

    /**
     * Test getUserGroupList
     */
    public function testGetUserGroupList()
    {
        $this->assertIsArray($this->UserManage->getUserGroupList());
    }

    /**
     * Test willChangeSelfGroup
     * @param $loginId
     * @param $userGroupId
     * @param $expected
     * @dataProvider willChangeSelfGroupDataProvider
     */
    public function testWillChangeSelfGroup($loginId, $userGroupId, $expected)
    {
        $request = $this->getRequest();
        if ($loginId) {
            $this->loginAdmin($request, $loginId);
        }
        $postData = [
            'user_groups' => ['_ids' => $userGroupId]
        ];
        $this->assertEquals($expected, $this->UserManage->willChangeSelfGroup($postData));
    }

    public function willChangeSelfGroupDataProvider()
    {
        return [
            [null, [0 => 1], false],
            [1, [0 => 1], false],
            [1, [0 => 1, 1 => 2], true],
        ];
    }

    /**
     * test Login
     */
    public function testLoginAndLogout()
    {
        $request = $this->getRequest('/baser/admin/users/index');
        $authentication = $this->BaserCore->getAuthenticationService($request);
        $request = $request->withAttribute('authentication', $authentication);
        $response = new Response();
        $request = $this->UserManage->login($request, $response, 1)['request'];
        $this->assertEquals(1, $request->getAttribute('identity')->id);
        $this->assertEquals(1, $request->getSession()->read('AuthAdmin')->id);
        $this->UserManage->logout($request, $response, 1);
        $this->assertNull($request->getSession()->read('AuthAdmin'));
    }

    /**
     * test getAuthSessionKey
     */
    public function testGetAuthSessionKey()
    {
        $this->assertEquals('AuthAdmin', $this->UserManage->getAuthSessionKey('Admin'));
        $this->assertFalse($this->UserManage->getAuthSessionKey('baser'));
    }

    /**
     * test reLogin
     */
    public function testReLogin()
    {
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/users/index'));
        $this->UserManage->update($request->getAttribute('identity')->getOriginalData(), ['name' => 'test']);
        $request = $this->UserManage->reLogin($request, new Response())['request'];
        $this->assertEquals('test', $request->getAttribute('identity')->name);
    }

    /**
     * test setCookieAutoLoginKey
     */
    public function testSetCookieAutoLoginKey()
    {
        $response = $this->UserManage->setCookieAutoLoginKey(new Response(), 1);
        $cookie = $response->getCookie(LoginStoresTable::KEY_NAME);
        $this->assertNotEmpty($cookie['value']);
    }

    /**
     * test checkAutoLogin
     */
    public function testCheckAutoLogin()
    {
        $response = $this->UserManage->setCookieAutoLoginKey(new Response(), 1);
        $request = $this->getRequest('/baser/admin/users/');
        $beforeCookie = $response->getCookie(LoginStoresTable::KEY_NAME);
        $request = $request->withCookieParams([LoginStoresTable::KEY_NAME => $beforeCookie['value']]);
        $response = $this->UserManage->checkAutoLogin($request, $response);
        $afterCookie = $response->getCookie(LoginStoresTable::KEY_NAME);
        $this->assertNotEmpty($afterCookie['value']);
        $this->assertNotEquals($beforeCookie['value'], $afterCookie['value']);
    }

    /**
     * test loginToAgent
     */
    public function testLoginToAgentAndReturnLoginUserFromAgent()
    {
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/users/'));
        $response = new Response();
        $this->UserManage->loginToAgent($request, $response, 2);
        $this->assertSession(1, 'AuthAgent.User.id');
        $this->assertSession(2, 'AuthAdmin.id');
        $this->UserManage->returnLoginUserFromAgent($request, $response);
        $this->assertSession(null, 'AuthAgent.User.id');
        $this->assertSession(1, 'AuthAdmin.id');
    }

}
