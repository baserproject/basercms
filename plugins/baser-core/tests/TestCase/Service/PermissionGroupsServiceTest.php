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
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Test\Factory\PermissionGroupFactory;
use BaserCore\Test\Factory\PermissionFactory;
use Cake\Datasource\Exception\RecordNotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PermissionGroupsServiceTest
 *
 * @property PermissionGroupsService $PermissionGroups
 * @property PermissionsService $Permissions
 */
class PermissionGroupsServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Factory/Permissions',
        'plugin.BaserCore.Factory/PermissionGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
    ];

    /**
     * Set Up
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->PermissionGroups = $this->getService(PermissionGroupsServiceInterface::class);
        $this->Permissions = $this->getService(PermissionsServiceInterface::class);
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
     * test build
     */
    public function testBuild()
    {
        $this->markTestIncomplete('このテストは未実装です。');
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
}
