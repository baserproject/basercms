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

namespace BaserCore\Test\TestCase\Controller\Api\Admin;

use BaserCore\Service\UserGroupsService;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use BaserCore\Test\Scenario\LoginStoresScenario;
use BaserCore\Test\Scenario\PermissionsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UserGroupsPaginationsScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UsersScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Controller\Api\UserGroupsController Test Case
 */
class UserGroupsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

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
        $this->loadFixtureScenario(SitesScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $this->loadFixtureScenario(UsersUserGroupsScenario::class);
        $this->loadFixtureScenario(UsersScenario::class);
        $this->loadFixtureScenario(LoginStoresScenario::class);
        $this->loadFixtureScenario(PermissionsScenario::class);
        $this->loadFixtureScenario(UserGroupsPaginationsScenario::class);
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
        $this->get('/baser/api/admin/baser-core/user_groups/index.json?token=' . $this->accessToken);
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
            'auth_prefix' => ['Admin']
        ];
        $this->post('/baser/api/admin/baser-core/user_groups/add.json?token=' . $this->accessToken, $data);
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
            'name' => 'Test_test_Man',
            'auth_prefix' => ['Admin']
        ];
        $this->post('/baser/api/admin/baser-core/user_groups/edit/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseSuccess();
    }

    /**
     * Test delete method
     *
     * @return void
     */
    public function testDelete()
    {
        // ユーザーが存在しないユーザーグループを作成
        $userGroupsTable = $this->getTableLocator()->get('UserGroups');
        $userGroup = $userGroupsTable->newEntity([
            'name' => 'test_api_empty_group',
            'title' => 'テスト用API空グループ',
            'auth_prefix' => 'Admin',
            'use_move_contents' => false
        ]);
        $userGroupsTable->save($userGroup);
        $newId = $userGroup->id;

        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/api/admin/baser-core/user_groups/delete/' . $newId . '.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        
        // 削除されたことを確認
        $deletedGroup = $userGroupsTable->find()->where(['id' => $newId])->first();
        $this->assertNull($deletedGroup);
    }

    /**
     * Test delete with users
     * ユーザーが所属しているユーザーグループは削除できないことを確認
     */
    public function testDeleteWithUsers()
    {
        // ユーザーを作成
        $user = UserFactory::make([
            'id' => 100,
            'name' => 'test_user',
            'password' => 'password',
            'real_name_1' => 'test',
            'real_name_2' => 'user',
            'email' => 'testuser@example.com',
            'nickname' => 'テストユーザー',
            'status' => true
        ])->persist();

        // ユーザーグループとユーザーの関連付けを作成
        UsersUserGroupFactory::make([
            'user_id' => 100,
            'user_group_id' => 2
        ])->persist();

        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/api/admin/baser-core/user_groups/delete/2.json?token=' . $this->accessToken);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ユーザーが所属しているユーザーグループは削除できません。', $result->message);
    }

    /**
     * Test View
     */
    public function testView()
    {
        $this->get('/baser/api/admin/baser-core/user_groups/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('admins', $result->userGroup->name);
    }

    /**
     * Test List
     */
    public function testList()
    {
        $this->get('/baser/api/admin/baser-core/user_groups/list/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());

        $userGroupsService = new UserGroupsService();
        $userGroups = $userGroupsService->getList();

        foreach ($result->userGroups as $key => $v) {
            $this->assertEquals($userGroups[$key], $v);
        }
    }

    /**
     * test copy
     * @return void
     */
    public function testCopy()
    {
        $this->get('/baser/api/admin/baser-core/user_groups/copy/1.json?token=' . $this->accessToken);
        $this->assertResponseCode(405);

        $this->post('/baser/api/admin/baser-core/user_groups/copy/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ユーザーグループ「admins」をコピーしました。', $result->message);
        $this->assertEquals('admins_copy', $result->userGroup->name);

        $this->post('/baser/api/admin/baser-core/user_groups/copy/test.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);
    }
}
