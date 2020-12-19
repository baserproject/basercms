<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;
use BaserCore\Model\Table\UserGroupsTable;
use BaserCore\TestSuite\BcTestCase;
use Cake\Validation\Validator;

/**
 * Class UserGroupsTableTest
 * @package BaserCore\Test\TestCase\Model\Table
 * @property UserGroupsTable $UserGroups
 */
class UserGroupsTableTest extends BcTestCase {

    /**
     * Test subject
     *
     * @var UserGroupsTable
     */
    public $UserGroups;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.UserGroups',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('UserGroups') ? [] : ['className' => 'BaserCore\Model\Table\UserGroupsTable'];
        $this->UserGroups = $this->getTableLocator()->get('UserGroups', $config);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UserGroups);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertEquals('user_groups', $this->UserGroups->getTable());
        $this->assertEquals('name', $this->UserGroups->getDisplayField());
        $this->assertEquals('id', $this->UserGroups->getPrimaryKey());
        $this->assertIsBool($this->UserGroups->hasBehavior('Timestamp'));
        $this->assertEquals('Users', $this->UserGroups->getAssociation('Users')->getName());
    }

    /**
     * Test validationDefault
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $validator = $this->UserGroups->validationDefault(new Validator());
        $fields = [];
        foreach($validator->getIterator() as $key => $value) {
            $fields[] = $key;
        }
        $this->assertEquals(['id', 'name', 'title', 'auth_prefix', 'use_admin_globalmenu', 'default_favorites', 'use_move_contents'], $fields);
    }

    /**
     * Test copy
     *
     * @return void
     */
    public function testCopy()
    {
        $this->UserGroups->copy(1);
        $originalUserGroup = $this->UserGroups->get(1);
        $query = $this->UserGroups->find()->where(['name' => $originalUserGroup->name.'_copy']);
        $this->assertEquals(1, $query->count());
    }

}
