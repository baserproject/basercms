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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\PermissionGroupsService;
use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionGroupsServiceInterface;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Service\UserGroupsService;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Test\Factory\PermissionGroupFactory;
use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use Cake\Utility\Hash;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PermissionGroupsServiceTest
 *
 * @property PermissionGroupsService $PermissionGroups
 * @property PermissionsService $Permissions
 * @property UserGroupsService $UserGroupsService
 */
class PermissionGroupsServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PermissionGroups = $this->getService(PermissionGroupsServiceInterface::class);
        $this->Permissions = $this->getService(PermissionsServiceInterface::class);
        $this->UserGroupsService = $this->getService(UserGroupsServiceInterface::class);
    }

    /**
     * Tear Down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->PermissionGroups);
    }

    /**
     * test __construct
     */
    public function test__construct(): void
    {
        $this->assertTrue(isset($this->PermissionGroups->PermissionGroups));
        $this->assertTrue(isset($this->PermissionGroups->UserGroups));
    }

    /**
     * test build
     */
    public function testBuild()
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $plugin = 'BaserCore';
        $this->PermissionGroups->build(0, $plugin);
        $data = $this->PermissionGroups->getIndex(0, ['permission_amount' => true])
            ->where(['plugin' => $plugin])
            ->where(['Permissions.user_group_id' => 0])
            ->all()->toArray();
        $this->assertCount(0, $data);

        $this->PermissionGroups->build(1, $plugin);
        $data = $this->PermissionGroups->getIndex(1, [])->where(['plugin' => $plugin])->all()->toArray();
        Configure::load($plugin . '.permission', 'baser');
        $settings = Configure::read('permission');
        Configure::delete('permission');
        $this->assertCount(count($settings), $data);

        $plugin = 'BcBlog';
        $this->PermissionGroups->build(1, $plugin);
        $data = $this->PermissionGroups->getIndex(1, [])->where(['plugin' => $plugin])->all()->toArray();
        Configure::load($plugin . '.permission', 'baser');
        $settings = Configure::read('permission');
        Configure::delete('permission');
        $this->assertCount(count($settings), $data);

        $result = $this->PermissionGroups->build(1, 'Nghiem');
        $this->assertFalse($result);
    }

    /**
     * test BuildByUserGroup
     */
    public function testBuildByUserGroup()
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $plugins = array_merge([0 => 'BaserCore'], Hash::extract(BcUtil::getEnablePlugins(true), '{n}.name'));
        $count = 0;
        foreach ($plugins as $plugin) {
            Configure::load($plugin . '.permission', 'baser');
            $settings = Configure::read('permission');
            $count += count($settings);
            Configure::delete('permission');
        }
        $this->PermissionGroups->buildByUserGroup(1);
        $Pg = $this->PermissionGroups->getIndex(1, [])->all()->toArray();
        $this->assertCount($count, $Pg);
    }


    /**
     * Test getList
     *
     * @return void
     */
    public function testGetList(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $result = $this->PermissionGroups->getList();
        $this->assertCount(3, $result);
        PermissionGroupFactory::make([
            'name' => 'group 1',
            'type' => 'Supper',
            'plugin' => 'BaserCore',
            'status' => 1
        ])->persist();
        PermissionGroupFactory::make([
            'name' => 'group 2',
            'type' => 'Supper',
            'plugin' => 'BaserCore',
            'status' => 1
        ])->persist();
        $result = $this->PermissionGroups->getList();
        $this->assertCount(5, $result);
        $this->assertContains('group 1', $result);
        $option = ['type' => 'Supper'];
        $result = $this->PermissionGroups->getList($option);
        $this->assertCount(2, $result);
        $this->assertContains('group 2', $result);
    }

    /**
     * Test buildDefaultEtcRuleGroup
     *
     * @return void
     */
    public function testBuildDefaultEtcRuleGroup(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $type = 'Nghiem';
        $name = 'Nghiem';
        $this->PermissionGroups->buildDefaultEtcRuleGroup($type, $name);
        $pg = $this->PermissionGroups->getIndex(1, [])
            ->where(['type' => $type, 'name like' => $name . '%'])
            ->all();
        $this->assertCount(1, $pg);
    }

    /**
     * Test buildAllowAllMethodByPlugin
     *
     * @return void
     */
//    public function testBuildAllowAllMethodByPlugin(): void
//    {
//        $this->loadFixtureScenario(PermissionGroupsScenario::class);
//        $this->loadFixtureScenario(InitAppScenario::class);
//        $userGroupId = 1;
//        $plugin = 'BaserCore';
//        $type = 'Nghiem';
//        $typeName = 'Nghiem';
//        $this->PermissionGroups->buildAllowAllMethodByPlugin($userGroupId, $plugin, $type, $typeName);
//        $pg = $this->PermissionGroups->getIndex(1, [])
//            ->where(['type' => $type, 'name like' => '%' . $typeName . '%'])
//            ->all()->toArray();
//        $this->assertCount(1, $pg);
//        $permissionsService = $this->getService(PermissionsServiceInterface::class);
//        $ps = $permissionsService->getIndex(['permission_group_id' => $pg[0]->id])->all();
//        $this->assertCount(1, $ps);
//    }

    /**
     * Test get
     *
     * @return void
     */
    public function testGet(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $data1 = $this->PermissionGroups->get(1);
        $this->assertNotEmpty($data1);
        $data2 = $this->PermissionGroups->get(1, 1);
        $this->assertNotEmpty($data2);
        $this->expectException(RecordNotFoundException::class);
        $this->PermissionGroups->get(-1);
    }

    /**
     * Test buildAll
     *
     * @return void
     */
    public function testBuildAll(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->PermissionGroups->buildAll();
        $permissionGroups = $this->PermissionGroups->getList();
        $this->assertCount(28, $permissionGroups);
    }


    /**
     * Test deleteByUserGroup
     *
     * @return void
     */
    public function testDeleteByUserGroup(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        PermissionFactory::make(
            [
                'no' => 1,
                'sort' => 1,
                'permission_group_id' => 1,
                'name' => 'nghiem',
                'url' => 'abc',
                'user_group_id' => 99
            ]
        )->persist();
        PermissionFactory::make(
            [
                'no' => 2,
                'sort' => 2,
                'permission_group_id' => 1,
                'name' => 'nghiem 2',
                'url' => 'abc',
                'user_group_id' => 99
            ]
        )->persist();
        $data1 = $this->PermissionGroups->get(1, 99);
        $this->assertCount(2, $data1->permissions);
        $this->PermissionGroups->deleteByUserGroup(99);
        $data2 = $this->PermissionGroups->get(1, 99);
        $this->assertCount(0, $data2->permissions);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $rs = $this->PermissionGroups->getNew('admin');
        $this->assertEquals('admin', $rs->type);
    }

    /**
     * Test getControlSource
     *
     * @return void
     */
    public function testGetControlSource(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $ug = UserGroupFactory::make([
            'name' => 'Nghiem',
            'title' => 'Nghiem title',
            'auth_prefix' => 'Api/Admin,Nghiem'
        ])->persist();
        $field = 'user_group_id';
        $result = $this->PermissionGroups->getControlSource($field);
        $this->assertCount(4, $result);

        $field = 'auth_prefix';
        $prefixes = BcUtil::getAuthPrefixList();
        $result = $this->PermissionGroups->getControlSource($field);
        $this->assertEquals($prefixes, $result);

        $result = $this->PermissionGroups->getControlSource($field, ['user_group_id' => $ug->id]);
        $this->assertCount(1, $result);

    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $data1 = $this->PermissionGroups->get(1);
        $this->PermissionGroups->update($data1, [
            'name' => 'name update test',
            'type' => 'super',
            'plugin' => 'update'
        ]);
        $data2 = $this->PermissionGroups->get(1);
        $this->assertEquals('name update test', $data2->name);
        $this->assertEquals('super', $data2->type);
        $this->assertEquals('update', $data2->plugin);
    }

    /**
     * Test getIndex
     *
     * @return void
     */
    public function testGetIndex(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        PermissionGroupFactory::make([
            'name' => 'group 1',
            'type' => 'Supper',
            'plugin' => 'BaserCore',
            'status' => 1
        ])->persist();
        PermissionGroupFactory::make([
            'name' => 'group 2',
            'type' => 'Supper',
            'plugin' => 'BaserCore',
            'status' => 1
        ])->persist();

        $param = [
            'list_type' => null,
            'permission_amount' => false
        ];
        $data1 = $this->PermissionGroups->getIndex(1, $param);
        $this->assertCount(5, $data1->all());

        $param = [
            'list_type' => 'Admin',
            'permission_amount' => false
        ];
        $data1 = $this->PermissionGroups->getIndex(1, $param);
        $this->assertCount(3, $data1->all());

        $param = [
            'list_type' => 'kami_sama',
            'permission_amount' => false
        ];
        $data1 = $this->PermissionGroups->getIndex(1, $param);
        $this->assertCount(0, $data1->all());

        $param = [
            'list_type' => 'Admin',
            'permission_amount' => true
        ];
        $data1 = $this->PermissionGroups->getIndex(1, $param);
        $this->assertCount(3, $data1->all());
        $data1 = $this->PermissionGroups->getIndex(1, $param);
        $this->assertEquals(0, $data1->where(['PermissionGroups.id' => 1])->first()->amount);
        $data1 = $this->PermissionGroups->getIndex(1, $param);
        $this->assertEquals(1, $data1->where(['PermissionGroups.id' => 2])->first()->amount);
        $data1 = $this->PermissionGroups->getIndex(1, $param);
        $this->assertEquals(1, $data1->where(['PermissionGroups.id' => 3])->first()->amount);
    }

    /**
     * Test rebuildByUserGroup
     *
     * @return void
     */
    public function testRebuildByUserGroup(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $pgs = $this->PermissionGroups->getIndex(1, [])->all();
        $this->assertTrue(count($pgs) > 0);
        foreach ($pgs as $pg){
            $this->PermissionGroups->delete($pg->id);
        }
        $pgs = $this->PermissionGroups->getIndex(1, [])->all();
        $this->assertCount(0, $pgs);

        $this->PermissionGroups->rebuildByUserGroup(1);
        $pgs = $this->PermissionGroups->getIndex(1, [])->all();
        $this->assertTrue(count($pgs) > 0);
    }

    /**
     * Test deleteByPlugin
     *
     * @return void
     */
    public function testDeleteByPlugin(): void
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $result = $this->PermissionGroups->getList();
        $this->assertCount(3, $result);
        PermissionGroupFactory::make([
            'name' => 'group 1',
            'type' => 'Supper',
            'plugin' => 'Nghiem',
            'status' => 1
        ])->persist();
        PermissionGroupFactory::make([
            'name' => 'group 2',
            'type' => 'Supper',
            'plugin' => 'Nghiem',
            'status' => 1
        ])->persist();
        $result = $this->PermissionGroups->getList();
        $this->assertCount(5, $result);
        $this->PermissionGroups->deleteByPlugin('Nghiem');
        $result = $this->PermissionGroups->getList();
        $this->assertCount(3, $result);
    }

    /**
     * Test buildByPlugin
     *
     * @return void
     */
    public function testBuildByPlugin(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $plugin = 'BcBlog';
        $this->PermissionGroups->buildByPlugin($plugin);
        $data = $this->PermissionGroups->getIndex(0, [])->where(['plugin' => $plugin])->all()->toArray();
        Configure::load($plugin . '.permission', 'baser');
        $settings = Configure::read('permission');
        Configure::delete('permission');
        $this->assertCount(count($settings), $data);

        $plugin = 'BaserCore';
        $this->PermissionGroups->buildByPlugin($plugin);
        $data = $this->PermissionGroups->getIndex(0, [])->where(['plugin' => $plugin])->all()->toArray();
        Configure::load($plugin . '.permission', 'baser');
        $settings = Configure::read('permission');
        Configure::delete('permission');
        $this->assertCount(count($settings), $data);
    }

    /**
     * Test delete
     */
    public function test_delete(): void
    {
        PermissionGroupFactory::make(['id' => 1])->persist();

        $data = $this->PermissionGroups->get(1);
        $result = $this->PermissionGroups->delete($data->id);
        $this->assertTrue($result);

        $this->expectException(RecordNotFoundException::class);
        $this->PermissionGroups->get(1);
    }

    /**
     * Test create
     */
    public function testCreate(): void
    {
        // Test create
        $postData = ['name' => 'name test', 'type' => 'super', 'plugin' => 'BcSample'];

        $data = $this->PermissionGroups->create($postData);

        $this->assertEquals('name test', $data->name);
        $this->assertEquals('super', $data->type);
        $this->assertEquals('BcSample', $data->plugin);

        // Test create with exception
        $invalidPostData = ['id' => 'test'];
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (id.integer: "The provided value must be an integer")');
        $this->PermissionGroups->create($invalidPostData);
    }

    /**
     * Test getAvailableMinUserGroupId
     * with valid user group
     */
    public function test_getAvailableMinUserGroupId_with_valid_user_group()
    {
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $result = $this->PermissionGroups->getAvailableMinUserGroupId();

        $this->assertEquals(2, $result);
    }

    /**
     * Test getAvailableMinUserGroupId
     * without user group
     */
    public function test_getAvailableMinUserGroupId_without_user_group()
    {
        $result = $this->PermissionGroups->getAvailableMinUserGroupId();
        $this->assertFalse($result);
    }

}
