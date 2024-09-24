<?php

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\PermissionsAdminServiceInterface;
use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class PermissionsAdminServiceTest extends BcTestCase
{
    /**
     * Trait
     */
    use BcContainerTrait;
    use ScenarioAwareTrait;


    public function setUp(): void
    {
        parent::setUp();
        $this->PermissionAdmin = $this->getService(PermissionsAdminServiceInterface::class);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->PermissionAdmin);
    }

    /**
     * Test getViewVarsForIndex
     */
    public function testGetViewVarsForIndex(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $request = $this->getRequest('/baser/admin/baser-core/permissions/index');
        $this->loginAdmin($request);

        $result = $this->PermissionAdmin->getViewVarsForIndex($request, 1);

        $this->assertArrayHasKey('currentUserGroup', $result);
        $currentUserGroup = $result['currentUserGroup'];
        $this->assertEquals('システム管理', $currentUserGroup->title);
        $this->assertEquals(1, $currentUserGroup->id);

        $this->assertArrayHasKey('permissionGroups', $result);
        $this->assertCount(3, $result['permissionGroups']);

        $this->assertArrayHasKey('permissions', $result);
        $this->assertCount(3, $result['permissions']);
        $this->assertArrayHasKey('sortmode', $result);
    }

    /**
     * Test getViewVarsForAdd
     */
    public function testGetViewVarsForAdd(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(PermissionGroupsScenario::class);

        $entity = PermissionFactory::make([
            'name' => 'test',
            'user_group_id' => 2,
            'permission_group_id' => 1,
        ])->getEntity();
        $result = $this->PermissionAdmin->getViewVarsForAdd(1, $entity);

        $this->assertArrayHasKey('permissionGroups', $result);
        $this->assertCount(3, $result['permissionGroups']);

        $this->assertArrayHasKey('permission', $result);
        $this->assertEquals('test', $result['permission']->name);

        $this->assertEquals(1, $result['userGroupId']);
        $this->assertEquals('システム管理', $result['userGroupTitle']);
    }

    /**
     * Test getViewVarsForEdit
     */
    public function testGetViewVarsForEdit(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(PermissionGroupsScenario::class);

        $entity = PermissionFactory::make([
            'name' => 'test edit',
            'user_group_id' => 2,
            'permission_group_id' => 1,
        ])->getEntity();
        $result = $this->PermissionAdmin->getViewVarsForEdit(1, $entity);

        $this->assertArrayHasKey('permissionGroups', $result);
        $this->assertCount(3, $result['permissionGroups']);

        $this->assertArrayHasKey('permission', $result);
        $this->assertEquals('test edit', $result['permission']->name);

        $this->assertEquals(1, $result['userGroupId']);
        $this->assertEquals('システム管理', $result['userGroupTitle']);
    }
}
