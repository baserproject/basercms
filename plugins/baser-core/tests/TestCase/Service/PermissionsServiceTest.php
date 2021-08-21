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
use BaserCore\Service\PermissionService;

/**
 * BaserCore\Model\Table\PermissionsTable Test Case
 *
 * @property PermissionService $PermissionService
 */
class PermissionServiceTest extends BcTestCase
{

    /**
     * Test subject
     *
     * @var PermissionService
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
        $this->PermissionService = new PermissionService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->PermissionService);
        parent::tearDown();
    }

    /**
     * Test getNew
     *
     * @return void
     */
    public function testGetNew()
    {
        $permission = $this->PermissionService->getNew(1);
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
        $permission = $this->PermissionService->get(1);
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
        $permissions = $this->PermissionService->getIndex($request->getQueryParams());
        $this->assertEquals('システム管理', $permissions->first()->name);
        $this->assertEquals(15, $permissions->count());
        // user_group_idがない場合
        $request = $this->getRequest('/')->withQueryParams(['user_group_id' => 999]);
        $permissions = $this->PermissionService->getIndex($request->getQueryParams());
        $this->assertnull($permissions->first());
    }

    /**
     * Test create
     *
     * @return void
     */
    public function testCreate()
    {
        $data = [
            'name' => 'testCreate',
            'user_group_id' => '3',
            'auth' => true,
            'url' => '/baser/admin/*',
            'status' => true
        ];
        $permission = $this->PermissionService->create($data);
        $newRecord = $this->PermissionService->Permissions->find()->all()->last();
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
        $record = $this->PermissionService->Permissions->get(1);
        $permission = $this->PermissionService->update($record, $data);
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
        $this->PermissionService->delete(1);
        $permissions = $this->PermissionService->Permissions->find('all');
        $this->assertEquals(2, $permissions->first()->id);
        // Adminのgroup_idが最後の1つの場合
        $this->expectException("Exception");
        $this->expectExceptionMessage("最後のシステム管理者は削除できません");
        $this->PermissionService->delete(20);
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testGetMethodList()
    {
        $this->assertEquals(
            $this->PermissionService->getMethodList(),
            ['*' => 'ALL',
            'GET' => 'GET',
            'POST' => 'POST',]
        );
    }

}
