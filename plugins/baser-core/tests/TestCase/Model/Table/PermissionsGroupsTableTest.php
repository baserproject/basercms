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

namespace BaserCore\Test\TestCase\Model\Table;

use BaserCore\Model\Table\PermissionGroupsTable;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class PermissionGroupsTableTest
 * @property PermissionGroupsTable $PermissionGroupsTable
 */
class PermissionsGroupsTableTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PermissionGroupsTable = $this->getTableLocator()->get('BaserCore.PermissionGroups');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PermissionGroupsTable);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertEquals('permission_groups', $this->PermissionGroupsTable->getTable());
        $this->assertEquals('name', $this->PermissionGroupsTable->getDisplayField());
        $this->assertEquals('id', $this->PermissionGroupsTable->getPrimaryKey());
        $this->assertIsBool($this->PermissionGroupsTable->hasBehavior('Timestamp'));
        $this->assertEquals('Permissions', $this->PermissionGroupsTable->getAssociation('Permissions')->getName());
    }

    /**
     * Test validationDefault
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $validator = $this->PermissionGroupsTable->getValidator('default');
        $errors = $validator->validate([
            'name' => '',
        ]);

        $this->assertEquals('ルールグループ名を入力してください。', current($errors['name']));

        $validator = $this->PermissionGroupsTable->getValidator('default');
        $errors = $validator->validate([
            'name' => str_repeat('a', 61),
        ]);

        $this->assertEquals('ルールグループ名は60文字以内で入力してください。', current($errors['name']));
    }

}
