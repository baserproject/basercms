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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\LoginStoresScenario;
use BaserCore\Test\Scenario\PermissionsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UserGroupsPaginationsScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UsersScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Controller\Admin\UserGroupsController;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Controller\UserGroupsController Test Case
 */
class UserGroupsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    // TODO loadFixtures を利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要
//    public $autoFixtures = false;

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
        // TODO loadFixtures を利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要
//        $this->loadFixtures('UsersUserGroups', 'Users', 'Sites', 'SiteConfigs', 'LoginStores', 'Permissions');
//        if ($this->getName() == 'testIndex_pagination') {
//            $this->loadFixtures('Controller\UserGroupsController\UserGroupsPagination');
//        } else {
//            $this->loadFixtures('UserGroups');
//        }
        $this->UserGroupsController = new UserGroupsController($this->loginAdmin($this->getRequest()));
    }

    public function tearDown(): void
    {
        unset($this->UserGroupsController);
        parent::tearDown();
    }


    /**
     * Test index method
     *
     * @return void
     */
    public function testIndex()
    {
        $this->get('/baser/admin/baser-core/user_groups/');
        $this->assertResponseOk();
    }

    /**
     * Test index pagination
     *
     * @return void
     */
    public function testIndex_pagination()
    {
        $this->markTestIncomplete('loadFixtures を利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        $this->get('/baser/admin/baser-core/user_groups/?limit=1&page=21');
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
        $this->get('/baser/admin/baser-core/user_groups/add');
        $this->assertResponseOk();

        $this->post('/baser/admin/baser-core/user_groups/add', [
            'name' => 'addtestgroup',
            'title' => 'テストグループ',
            'use_move_contents' => '1',
        ]);
        $this->assertFlashMessage('新規ユーザーグループ「addtestgroup」を追加しました。');

        $userGroups = $this->getTableLocator()->get('UserGroups');
        $userGroup = $userGroups
            ->find()
            ->where([
                'name' => 'addtestgroup',
            ])
            ->first();
        $this->assertEquals($userGroup['name'], 'addtestgroup');
        $this->assertEquals($userGroup['title'], 'テストグループ');
        $this->assertEquals($userGroup['use_move_contents'], 1);
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
            'id' => '1',
            'name' => 'test',
            'title' => 'test',
            'use_move_contents' => '1'
        ];
        $this->loginAdmin($this->getRequest('/'));
        $this->post('/baser/admin/baser-core/user_groups/edit/1', $data);
        $this->assertRedirect('/baser/admin/baser-core/user_groups/index');
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
        $this->post('/baser/admin/baser-core/user_groups/delete/1');
        $userGroups = $this->getTableLocator()->get('UserGroups');
        $this->assertEquals($userGroups->find()->first()->id, '2');
        $this->assertRedirect('/baser/admin/baser-core/user_groups/index');
    }

    /**
     * Test copy method
     *
     * @return void
     */
    public function testCopy()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/user_groups/copy/1');
        $this->assertResponseSuccess();
        $userGroups = $this->getTableLocator()->get('UserGroups');
        $originalUserGroup = $userGroups->get(1);
        $query = $userGroups->find()->where(['name' => $originalUserGroup->name . '_copy']);
        $this->assertEquals(1, $query->count());
    }

}
