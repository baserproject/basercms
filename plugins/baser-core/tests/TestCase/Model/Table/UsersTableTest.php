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

use BaserCore\Model\Table\UsersTable;
use BaserCore\TestSuite\BcTestCase;
use Cake\Validation\Validator;

/**
 * BaserCore\Model\Table\UsersTable Test Case
 */
class UsersTableTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var UsersTable
     */
    public $Users;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users'
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('Users') ? [] : ['className' => 'BaserCore\Model\Table\UsersTable'];
        $this->Users = $this->getTableLocator()->get('Users', $config);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);
        parent::tearDown();
    }

    /**
     * Test initialize method
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertEquals('users', $this->Users->getTable());
        $this->assertEquals('name', $this->Users->getDisplayField());
        $this->assertEquals('id', $this->Users->getPrimaryKey());
        $this->assertIsBool($this->Users->hasBehavior('Timestamp'));
        $this->assertEquals('UserGroups', $this->Users->getAssociation('UserGroups')->getName());
    }

    /**
     * Test testBeforeMarshal
     */
    public function testBeforeMarshal() {
        $user = $this->Users->newEntity([
            'password_1' => 'testtest'
        ]);
        $this->assertNotEmpty($user->password);
    }

    /**
     * Test testAfterMarshal
     */
    public function testAfterMarshal() {
        $user = $this->Users->newEntity([
            'password' => '',
            'password_1' => ''
        ]);
        $this->assertEquals($user->getError('password_1'), [0 => '']);
        $this->assertEquals($user->getError('password_2'), [0 => '']);
    }

    /**
     * Test validationDefault method
     *
     * @return void
     */
    public function testValidationDefault()
    {
        $validator = $this->Users->validationDefault(new Validator());
        $fields = [];
        foreach($validator->getIterator() as $key => $value) {
            $fields[] = $key;
        }
        $this->assertEquals(['id', 'name', 'real_name_1', 'real_name_2', 'nickname', 'user_groups', 'email', 'password'], $fields);
    }

    /**
     * Test buildRules method
     *
     * @return void
     */
    public function testBuildRules()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

}
