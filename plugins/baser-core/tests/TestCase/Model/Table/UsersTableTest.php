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
 *
 * @property UsersTable $Users
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
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
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
        $config = $this->getTableLocator()->exists('Users')? [] : ['className' => 'BaserCore\Model\Table\UsersTable'];
        $this->Users = $this->getTableLocator()->get('Users', $config);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Users);
        parent::tearDown();
    }

    /**
     * Test initialize
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
     * Test beforeMarshal
     */
    public function testBeforeMarshal()
    {
        $user = $this->Users->newEntity(
            ['password_1' => 'testtest'],
            ['validate' => false]
        );
        $this->assertNotEmpty($user->password);
    }

    /**
     * Test afterMarshal
     */
    public function testAfterMarshal()
    {
        $user = $this->Users->newEntity([
            'password' => ''
        ]);
        $this->assertEquals($user->getError('password_1'), []);
        $this->assertEquals($user->getError('password_2'), []);
    }

    /**
     * Test validationDefault
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
     * Test validationNew
     *
     * @return void
     */
    public function testValidationNew()
    {
        $user = $this->Users->newEntity([
            'password' => '',
            'password_1' => '',
            'password_2' => ''
        ], ['validate' => 'new']);
        $this->assertEquals($user->getError('password')['_empty'], __d('baser', 'パスワードを入力してください。'));
    }

    /**
     * Test getNew
     */
    public function testGetNew()
    {
        $this->assertEquals(1, $this->Users->getNew()->user_groups[0]->id);
    }

    /**
     * Test getControlSource
     */
    public function testGetControlSource()
    {
        $list = $this->Users->getControlSource('user_group_id')->toList();
        $this->assertEquals('システム管理', $list[0]);
    }

    /**
     * Test createWhere
     *
     * @dataProvider createWhereDataProvider
     */
    public function testCreateWhere($userGroupId, $expected)
    {
        $request = $this->getRequest('/?user_group_id=' . $userGroupId);
        $query = $this->Users->createWhere($this->Users->find(), $request);
        $this->assertEquals($expected, $query->count());
    }

    public function createWhereDataProvider()
    {
        return [
            [1, 1],
            [2, 1]
        ];
    }

    /**
     * Test getLoginFormatData
     */
    public function testGetLoginFormatData()
    {
        $user = $this->Users->getLoginFormatData(1)->toArray();
        $this->assertEquals(1, $user['user_groups'][0]['id']);
    }

}
