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

use BaserCore\Service\UsersService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class UsersServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property UsersService $Users
 */
class UsersServiceTest extends BcTestCase
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
     * @var UsersService|null
     */
    public $Users = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Users = new UsersService();
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
     * Test getNew
     */
    public function testGetNew()
    {
        $this->assertEquals(1, $this->Users->getNew()->user_groups[0]->id);
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $user = $this->Users->get(1);
        $this->assertEquals('baser admin', $user->name);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $request = $this->getRequest('/');

        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals('baser admin', $users->first()->name);

        $request = $this->getRequest('/?user_group_id=2');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals('baser operator', $users->first()->name);

        $request = $this->getRequest('/?num=1');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());

        $request = $this->getRequest('/?name=baser');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(2, $users->all()->count());
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $request = $this->getRequest('/');
        $request = $request->withParsedBody([
            'name' => 'ucmitz',
            'user_groups' => [
                '_ids' => [1]
            ],
            'password_1' => 'aaaaaaaaaaaaaa',
            'password_2' => 'aaaaaaaaaaaaaa'
        ]);
        $request = $request->withData('password', $request->getData('password_1'));
        $this->Users->create($request->getData());
        $request = $this->getRequest('/?name=ucmitz');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        $request = $this->getRequest('/');
        $request = $request->withParsedBody([
            'name' => 'ucmitz',
        ]);
        $user = $this->Users->get(1);
        $this->Users->update($user, $request->getData());
        $request = $this->getRequest('/?name=ucmitz');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $this->Users->delete(2);
        $request = $this->getRequest('/');
        $users = $this->Users->getIndex($request->getQueryParams());
        $this->assertEquals(1, $users->all()->count());
    }

    /**
     * Test Last Admin Delete
     */
    public function testLastAdminDelete()
    {
        $this->expectException("Cake\Core\Exception\Exception");
        $this->Users->delete(1);
    }

}
