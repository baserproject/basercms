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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\UserGroupsService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UserGroupsServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property UserGroupsService $UserGroups
 */
class UserGroupsServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.LoginStores'
    ];

    /**
     * @var UserGroupsService|null
     */
    public $UserGroups = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UserGroups = new UserGroupsService();
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
     * Test get
     */
    public function testGet()
    {
        $userGroups = $this->UserGroups->get(1);
        $this->assertEquals('admins', $userGroups->name);
    }

    /**
     * Test all
     */
    public function testGetIndex()
    {
        $userGroups = $this->UserGroups->getIndex();
        $this->assertEquals(2, $userGroups->count());
    }

    /**
     * Test create
     * @dataProvider createDataProvider
     */
    public function testCreate($authPrefix, $expected)
    {
        $data = [
            'name' => 'ucmitzGroup',
            'title' => 'ucmitzグループ',
            'use_move_contents' => '1',
            'auth_prefix' => $authPrefix
        ];
        $a = $this->UserGroups->create($data);
        $group = $this->UserGroups->getIndex();
        $this->assertEquals($group->last()->name, $data['name']);
        $this->assertEquals($group->last()->auth_prefix, $expected);
    }
    public function createDataProvider()
    {
        return [
            // auth_prefixがすでにある場合
            ['test', 'test'],
            // auth_prefixがない場合
            [null, 'Admin'],
        ];
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        $data = ['name' => 'ucmitzGroup'];
        $userGroup = $this->UserGroups->get(1);
        $this->UserGroups->update($userGroup, $data);
        $group = $this->UserGroups->getIndex();
        $this->assertEquals($group->first()->name, $data['name']);
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $this->UserGroups->delete(2);
        $group = $this->UserGroups->UserGroups->find('all');
        $this->assertEquals(1, $group->count());
    }
}
