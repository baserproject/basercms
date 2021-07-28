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
use BaserCore\Service\Admin\UserManageService;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
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
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.Controller/UsersController/UsersPagination',
    ];

    public $autoFixtures = false;

    /**
     * UsersController
     * @var UsersController
     */
    public $UsersController;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures('UsersUserGroups', 'UserGroups', 'Dblogs');
        if ($this->getName() == 'testIndex_pagination') {
            $this->loadFixtures('Controller\UsersController\UsersPagination');
        } else {
            $this->loadFixtures('Users', 'LoginStores');
        }
        $request = $this->getRequest();
        $request = $this->loginAdmin($request);
        $this->UsersController = new UsersController($request);
        $this->UsersController->loadModel('BaserCore.Users');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UsersController);
        parent::tearDown();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertEquals($this->UsersController->Authentication->getUnauthenticatedActions(), ['login']);
        $this->assertNotEmpty($this->UsersController->Authentication->getConfig('logoutRedirect'));
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
        // アクション実行（requestの変化を判定するため $this->get() ではなくクラスを直接利用）
        $this->UsersController->beforeFilter(new Event('beforeFilter'));
        $this->UsersController->index(new UserManageService());
        $this->assertEquals(1, $this->UsersController->getRequest()->getQuery('num'));
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

        // イベントテスト
        $this->entryControllerEventToMock('Controller.Users.afterAdd', function(Event $event) {
            $user = $event->getData('user');
            $users = TableRegistry::getTableLocator()->get('Users');
            $user->name = 'etc';
            $users->save($user);
        });
        $data = [
            'name' => 'Test_test_Man2',
            'password_1' => 'Lorem ipsum dolor sit amet',
            'password_2' => 'Lorem ipsum dolor sit amet',
            'real_name_1' => 'Lorem ipsum dolor sit amet',
            'real_name_2' => 'Lorem ipsum dolor sit amet',
            'email' => 'test2@example.com',
            'nickname' => 'Lorem ipsum dolor sit amet',
        ];
        $this->post('/baser/admin/baser-core/users/add', $data);
        $query = $users->find()->where(['name' => 'etc']);
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

        // イベントテスト
        $this->entryControllerEventToMock('Controller.Users.afterEdit', function(Event $event) {
            $user = $event->getData('user');
            $users = TableRegistry::getTableLocator()->get('Users');
            $user->name = 'etc';
            $users->save($user);
        });
        $data = [
            'id' => 1,
            'name' => 'Test_test_Man2',
            'password_1' => 'Lorem ipsum dolor sit amet',
            'password_2' => 'Lorem ipsum dolor sit amet',
            'real_name_1' => 'Lorem ipsum dolor sit amet',
            'real_name_2' => 'Lorem ipsum dolor sit amet',
            'email' => 'test2@example.com',
            'nickname' => 'Lorem ipsum dolor sit amet',
        ];
        $this->post('/baser/admin/baser-core/users/edit/1', $data);
        $users = $this->getTableLocator()->get('Users');
        $query = $users->find()->where(['name' => 'etc']);
        $this->assertEquals(1, $query->count());
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
     * [ADMIN] 管理者ログイン後ログアウト
     */
    public function testLoginAndLogout()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/users/login');
        $this->assertRedirect('/baser/admin');
        $this->post('/baser/admin/baser-core/users/logout');
        $this->assertRedirect('/baser/admin/baser-core/users/login');
    }

    /**
     * [ADMIN] 代理ログイン
     */
    public function testLogin_agent()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        // 代理元 id:1 (admin)
        $request = $this->loginAdmin($this->getRequest());
        // 一旦ログイン
        $this->post('/baser/admin/baser-core/users/login');
        // 代理先 id:2 (operator)
        $this->get('/baser/admin/baser-core/users/login_agent/2');
        $this->assertEquals(1, $_SESSION['AuthAgent']['User']->id);
        $this->assertRedirect('/baser/admin');
    }

    /**
     * 代理ログインをしている場合、元のユーザーに戻る
     */
    public function testBack_agent()
    {
        $request = $this->loginAdmin($this->getRequest());
        $this->session(['AuthAgent.User' => $request->getAttribute('authentication')->getIdentity()->getOriginalData()]);
        $this->get('/baser/admin/baser-core/users/back_agent');
        $this->assertSession(null, 'AuthAgent');
        $this->assertRedirect('/baser/admin');
    }

}
