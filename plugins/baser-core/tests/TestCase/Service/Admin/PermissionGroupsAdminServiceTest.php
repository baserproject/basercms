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

use BaserCore\Service\Admin\PermissionGroupsAdminService;
use BaserCore\Service\Admin\PermissionGroupsAdminServiceInterface;
use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Service\UserGroupsService;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Test\Factory\PermissionGroupFactory;
use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Utility\Hash;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PermissionGroupsAdminServiceTest
 *
 * @property PermissionGroupsAdminService $PermissionGroupsAdmin
 * @property PermissionsService $Permissions
 * @property UserGroupsService $UserGroupsService
 */
class PermissionGroupsAdminServiceTest extends BcTestCase
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
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/UsersUserGroups',
    ];

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PermissionGroupsAdmin = $this->getService(PermissionGroupsAdminServiceInterface::class);
//        $this->Permissions = $this->getService(PermissionsServiceInterface::class);
//        $this->UserGroupsService = $this->getService(UserGroupsServiceInterface::class);
    }

    /**
     * Tear Down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->PermissionGroupsAdmin);
    }

    /**
     * Test getViewVarsForIndex
     *
     * @return void
     */
    public function testGetViewVarsForIndex(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $request = $this->getRequest('/baser/admin/baser-core/permission_groups/index?list_type=Admin&permission_amount=true');
        $this->loginAdmin($request);

        $vars = $this->PermissionGroupsAdmin->getViewVarsForIndex(1, $request);
        $entities = $vars['entities']->all();
        $this->assertCount(3, $entities);
        $this->assertEquals(1, $vars['userGroupId']);

        $request = $this->getRequest('/baser/admin/baser-core/permission_groups/index?list_type=Admin&permission_amount=false');
        $vars = $this->PermissionGroupsAdmin->getViewVarsForIndex(0, $request);
        $this->assertEquals(0, $vars['userGroupId']);

        $request = $this->getRequest('/baser/admin/baser-core/permission_groups/index?list_type=Nghiem&permission_amount=false');
        $vars = $this->PermissionGroupsAdmin->getViewVarsForIndex(1, $request);
        $entities = $vars['entities']->all();
        $this->assertCount(0, $entities);
    }
}
