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

use BaserCore\Service\UserGroupsService;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Controller\Api\UserGroupsController Test Case
 */
class UserGroupsControllerTest extends BcTestCase
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
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/api/baser-core/user_groups/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('admins', $result->userGroups[0]->name);
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
            'name' => 'ucmitzGroup',
            'title' => 'ucmitzグループ',
            'use_move_contents' => '1',
        ];
        $this->post('/baser/api/baser-core/user_groups/add.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
        $UserGroups = $this->getTableLocator()->get('UserGroups');
        $query = $UserGroups->find()->where(['name' => $data['name']]);
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
        $this->post('/baser/api/baser-core/user_groups/edit/1.json?token=' . $this->accessToken, $data);
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
        $this->post('/baser/admin/baser-core/UserGroups/delete/1.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/baser-core/user_groups/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('admins', $result->userGroups->name);
    }

    /**
     * Test List
     */
    public function testList()
    {
        $this->get('/baser/api/baser-core/user_groups/list/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());

        $userGroupsService = new UserGroupsService();
        $userGroups = $userGroupsService->getList();

        foreach ($result->userGroups as $key => $v){
            $this->assertEquals($userGroups[$key], $v);
        }
    }
}
