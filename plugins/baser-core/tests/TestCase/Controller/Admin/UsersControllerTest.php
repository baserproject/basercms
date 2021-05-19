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

use BaserCore\Controller\Admin\UsersController;
use BaserCore\Service\UserManageService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Controller\Admin\UsersController Test Case
 */
class UsersControllerTest extends BcTestCase
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
        'plugin.BaserCore.LoginStores',
        'plugin.BaserCore.Controller/UsersController/UsersPagination',
    ];

    public $autoFixtures = false;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();

        $this->loadFixtures('UsersUserGroups', 'UserGroups');
        if ($this->getName() == 'testIndex_pagination') {
            $this->loadFixtures('Controller\UsersController\UsersPagination');
        } else {
            $this->loadFixtures('Users', 'LoginStores');
        }

        $config = $this->getTableLocator()->exists('Users')? [] : ['className' => 'BaserCore\Model\Table\UsersTable'];
        $Users = $this->getTableLocator()->get('Users', $config);
        $this->session(['AuthAdmin' => $Users->get(1)]);
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/admin/baser-core/users/');
        $this->assertResponseOk();

        // イベントテスト
        $this->entryControllerEventToMock('Controller.Users.searchIndex', function(Event $event) {
            $request = $event->getData('request');
            return $request->withQueryParams(['num' => 1]);
        });
        // アクション実行（requestの変化を判定するため $this->get() ではなくクラスを初期化）
        $controller = new UsersController($this->getRequest('/baser/admin/baser-core/users/'));
        $controller->beforeFilter(new Event('beforeFilter'));
        $controller->index(new UserManageService());
        $this->assertEquals(1, $controller->getRequest()->getQuery('num'));
    }

    /**
     * Test index pagination
     *
     * @return void
     */
    public function testIndex_pagination()
    {
        $this->get('/baser/admin/baser-core/users/index?num=1&page=2');
        $this->assertResponseOk();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'name' => 'Test_test_Man',
            'password_1' => 'Lorem ipsum dolor sit amet',
            'password_2' => 'Lorem ipsum dolor sit amet',
            'real_name_1' => 'Lorem ipsum dolor sit amet',
            'real_name_2' => 'Lorem ipsum dolor sit amet',
            'email' => 'test@example.com',
            'nickname' => 'Lorem ipsum dolor sit amet',
        ];
        $this->post('/baser/admin/baser-core/users/add', $data);
        $this->assertResponseSuccess();
        $users = $this->getTableLocator()->get('Users');
        $query = $users->find()->where(['name' => $data['name']]);
        $this->assertEquals(1, $query->count());
    }

    /**
     * Test edit method
     *
     * @return void
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'name' => 'Test_test_Man'
        ];
        $this->post('/baser/admin/baser-core/users/edit/1', $data);
        $this->assertResponseSuccess();
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/users/delete/1');
        $this->assertResponseSuccess();
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'users',
            'action' => 'index'
        ]);
    }

    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ログイン処理を行う
     * ・リダイレクトは行わない
     * ・requestActionから呼び出す
     */
    public function testLogin_exec()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 管理者ログイン画面
     */
    public function testLogin()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 代理ログイン
     */
    public function testLogin_agent()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 代理ログインをしている場合、元のユーザーに戻る
     */
    public function testBack_agent()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 認証クッキーをセットする
     */
    public function testSetAuthCookie()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 管理者ログアウト
     */
    public function testLogout()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] ユーザー情報削除　(ajax)
     */
    public function testAjax_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ログインパスワードをリセットする
     * 新しいパスワードを生成し、指定したメールアドレス宛に送信する
     */
    public function testReset_password()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
