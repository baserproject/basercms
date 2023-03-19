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
use BaserCore\Service\PermissionGroupsServiceInterface;
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Test\Factory\PermissionGroupFactory;
use Cake\Datasource\Exception\RecordNotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PermissionGroupsServiceTest
 *
 * @property PermissionGroupsService $PermissionGroups
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
}
