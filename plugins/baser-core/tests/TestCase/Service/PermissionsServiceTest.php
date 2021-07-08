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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Service\PermissionsService;

/**
 * BaserCore\Model\Table\PermissionsTable Test Case
 *
 * @property PermissionsService $PermissionsService
 */
class PermissionsServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var PermissionsService
     */
    public $Permissions;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Permissions',
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
        $this->PermissionsService = new PermissionsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PermissionsService);
        parent::tearDown();
    }

    /**
     * Test getNew
     *
     * @return void
     */
    public function testGetNew()
    {
        $permission = $this->PermissionsService->getNew(1);
        $this->assertEquals(1, $permission->user_group_id);
        $this->assertFalse($permission->hasErrors());
    }
    /**
     * Test get
     *
     * @return void
     */
    public function testGet()
    {
        $permission = $this->PermissionsService->get(1);
        $this->assertEquals('システム管理', $permission->name);
        $this->assertEquals(2, $permission->user_group->id);
    }
    /**
     * Test getIndex
     *
     * @return void
     */
    public function testGetIndex()
    {
        // user_group_idがある場合
        $request = $this->getRequest('/')->withQueryParams(['user_group_id' => 2]);
        $permissions = $this->PermissionsService->getIndex($request->getQueryParams());
        $this->assertEquals('システム管理', $permissions->first()->name);
        $this->assertEquals(15, $permissions->count());
        // user_group_idがない場合
        $request = $this->getRequest('/')->withQueryParams(['user_group_id' => 999]);
        $permissions = $this->PermissionsService->getIndex($request->getQueryParams());
        $this->assertnull($permissions->first());
    }
    /**
     * Test set
     *
     * @return void
     */
    public function testSet()
    {
        // 正常な場合
        $data = [
            'name' => 'testSet',
            'user_group_id' => '3',
            'url' => '/baser/admin/*'
        ];
        $permission = $this->PermissionsService->set($data);
        $this->assertEquals("testSet", $permission->name);
        $this->assertEquals(21, $permission->no);
        // 異常な場合
        $data = [
            'name' => '',
            'user_group_id' => '',
            'url' => ''
        ];
        $permission = $this->PermissionsService->set($data);
        $this->assertTrue($permission->hasErrors());
    }

    /**
     * Test create
     *
     * @return void
     */
    public function testCreate()
    {
        $data = $this->PermissionsService->set([
            'name' => 'testCreate',
            'user_group_id' => '3',
            'url' => '/baser/admin/*'
        ]);

        $permission = $this->PermissionsService->create($data);
        $newRecord = $this->PermissionsService->Permissions->find('all')->all()->last();
        $this->assertEquals($newRecord->name, $permission->name);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $data = [
            'name' => 'testUpdate',
            'user_group_id' => '2',
            'url' => '/baser/admin/*'
        ];
        $record = $this->PermissionsService->Permissions->get(1);
        $permission = $this->PermissionsService->update($record, $data);
        $this->assertEquals('testUpdate', $permission->name);
        $this->assertEquals(21, $permission->no);
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testDelete()
    {
        // group_idが1出ない場合
        $this->PermissionsService->delete(1);
        $permissions = $this->PermissionsService->Permissions->find('all');
        $this->assertEquals(2, $permissions->first()->id);
        // Adminのgroup_idが最後の1つの場合
        $this->expectException("Exception");
        $this->expectExceptionMessage("最後のシステム管理者は削除できません");
        $this->PermissionsService->delete(20);
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testGetMethodList()
    {
        $this->assertEquals(
            $this->PermissionsService->getMethodList(),
            ['*' => 'ALL',
            'GET' => 'GET',
            'POST' => 'POST',]
        );
    }

}
