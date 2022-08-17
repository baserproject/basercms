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

namespace BaserCore\Test\TestCase\Controller\Api;

use BaserCore\Controller\Api\UsersController;
use BaserCore\TestSuite\BcTestCase;
use Cake\Core\Configure;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Controller\Api\UsersController Test Case
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
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs'
    ];

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test initialize
     * @return void
     */
    public function testInitialize(){
        $request = $this->getRequest('/baser/api/baser-core/users/');
        $request = $this->loginAdmin($request);
        $usersController = new UsersController($request);

        $this->assertEquals($usersController->Authentication->unauthenticatedActions, ['login']);
    }

    public function testLoginAndRefreshToken()
    {
        $this->get('/baser/api/baser-core/users/login.json');
        $this->assertResponseCode(401);
        $this->post('/baser/api/baser-core/users/login.json');
        $this->assertResponseCode(401);
        $this->post('/baser/api/baser-core/users/login.json', ['email' => 'testuser1@example.com', 'password' => 'password']);
        $this->assertResponseOk();
        $this->assertFlashMessage('ようこそ、ニックネーム1さん。');
        $body = json_decode($this->_getBodyAsString());
        $this->assertEquals('/baser/admin', $body->redirect);
        $this->get('/baser/api/baser-core/users/refresh_token.json?token=' . $body->refresh_token);
        $this->assertResponseContains('access_token');
        $this->post('https://localhost/baser/api/baser-core/users/login.json', [
            'email' => 'testuser1@example.com',
            'password' => 'password',
            'saved' => 1
        ]);
        $loginStores = $this->getTableLocator()->get('BaserCore.LoginStores');
        $this->assertEquals(1, $loginStores->find()->where(['user_id' => 1])->count());
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/baser-core/users/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('baser admin', $result->users[0]->name);
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
            'user_groups' => [
                '_ids' => [1]
            ],
        ];
        $this->post('/baser/api/baser-core/users/add.json?token=' . $this->accessToken, $data);
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
        $this->post('/baser/api/baser-core/users/edit/1.json?token=' . $this->accessToken, $data);
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
        $this->post('/baser/api/baser-core/users/delete/2.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/baser-core/users/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('baser admin', $result->user->name);
    }

    /**
     * test Login
     * @return void
     */
    public function testLogin()
    {
        $this->get('/baser/api/baser-core/users/login.json');
        $this->assertResponseCode(401);

        $this->post('/baser/api/baser-core/users/login.json');
        $this->assertResponseCode(401);

        $this->post('/baser/api/baser-core/users/login.json', ['email' => 'testuser1@example.com', 'password' => 'password']);
        $this->assertResponseOk();
        $this->assertFlashMessage('ようこそ、ニックネーム1さん。');

        $body = json_decode($this->_getBodyAsString());
        $this->assertEquals('/baser/admin', $body->redirect);
    }
}
