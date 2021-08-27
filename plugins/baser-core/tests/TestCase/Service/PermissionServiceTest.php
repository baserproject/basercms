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
use phpDocumentor\Reflection\PseudoTypes\True_;

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
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
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
        $beforeId = $record->id;
        $beforeName = $record->name;
        $this->PermissionService->update($record, $data);
        $record = $this->PermissionService->Permissions->get(1);

        $this->assertEquals($beforeId, $record->id);
        $this->assertNotEquals($beforeName, $record->name);
    }

    /**
     * Test copy
     *
     * @return void
     */
    public function testCopy()
    {
        $permissions = $this->getTableLocator()->get('Permissions');

        $permission = $permissions->find()->order(['id' => 'DESC'])->first();
        $copyPermission = $this->PermissionService->copy($permission->id);

        $this->assertGreaterThan($permission->no, $copyPermission->no);
        $this->assertGreaterThan($permission->sort, $copyPermission->sort);
        $this->assertEquals($permission->name, $copyPermission->name);
        $this->assertEquals($permission->url, $copyPermission->url);
        $this->assertEquals($permission->auth, $copyPermission->auth);
        $this->assertEquals($permission->method, $copyPermission->method);
        $this->assertEquals($permission->status, $copyPermission->status);
    }

    /**
     * Test delete
     *
     * @return void
     */
    public function testDelete()
    {
        $permissions = $this->getTableLocator()->get('Permissions');

        $permission = $permissions->find()->order(['id' => 'ASC'])->first();
        $beforeId = $permission->id;
        $this->PermissionService->delete($beforeId);

        $permission = $permissions->find()->order(['id' => 'ASC'])->first();
        $this->assertNotEquals($beforeId, $permission->id);
    }

    /**
     * Test publish
     *
     * @return void
     */
    public function testPublish()
    {
        $permissions = $this->getTableLocator()->get('Permissions');

        $permission = $permissions->find()->order(['id' => 'ASC'])->first();
        $permission->status = false;
        $permissions->save($permission);

        $permission = $this->PermissionService->publish($permission->id);
        $this->assertTrue($permission->status);
    }

    /**
     * Test unpublish
     *
     * @return void
     */
    public function testUnpublish()
    {
        $permissions = $this->getTableLocator()->get('Permissions');

        $permission = $permissions->find()->order(['id' => 'ASC'])->first();
        $permission->status = true;
        $permissions->save($permission);

        $permission = $this->PermissionService->unpublish($permission->id);
        $this->assertFalse($permission->status);
    }

    /**
     * Test getMethodList
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

    /**
     * Test autoFillRecord
     *
     * @return void
     */
    public function testAutoFillRecord()
    {
        $reflection = new \ReflectionClass($this->PermissionService);
        $method = $reflection->getMethod('autoFillRecord');
        $method->setAccessible(true);

        $data = $method->invokeArgs($this->PermissionService, [[]]);
        $this->assertGreaterThan(0, $data['no']);
        $this->assertGreaterThan(0, $data['sort']);
        $this->assertTrue($data['auth']);
        $this->assertEquals('*', $data['method']);
        $this->assertTrue($data['status']);

        $data = $method->invokeArgs($this->PermissionService, [[
            'auth' => false,
            'status' => false,
            'method' => 'GET',
        ]]);
        $this->assertFalse($data['auth']);
        $this->assertFalse($data['status']);
        $this->assertEquals('GET', $data['method']);
    }

    /**
     * 権限チェックを行う
     *
     * @param array $url
     * @param string $userGroupId
     * @param array $expected 期待値
     * @param string $message テストが失敗した時に表示されるメッセージ
     * @dataProvider checkDataProvider
     */
    public function testCheck($url, $userGroupId, $expected, $message = null)
    {
        $result = $this->PermissionService->check($url, $userGroupId);
        $this->assertEquals($expected, $result, $message);
    }

    public function checkDataProvider()
    {
        return [
            ['hoge', 1, true, 'システム管理者は権限をもっています'],
            ['hoge', 2, true, 'サイト運営者は権限をもっています'],
            ['/baser/admin/*', 1, true, 'サイト運営者は権限をもっています'],
            ['/baser/admin/*', 2, false, 'サイト運営者は権限をもっていません'],
            ['/baser/admin/', 2, true, 'サイト運営者は権限をもっています'],
            ['/baser/admin/dashboard', 2, false, 'サイト運営者は権限をもっていません'],
            ['/baser/admin/dashboard/', 2, true, 'サイト運営者は権限をもっています'],
            ['/baser/admin/dashboard', 3, true, 'サイト運営者は権限をもっていません'],
            ['/baser/admin/dashboard/', 3, true, 'サイト運営者は権限をもっていません'],
        ];
    }

    /**
     * 権限チェックの準備をする
     * @param int $userGroupId
     * @param array $expected
     * @return void
     * @dataProvider setCheckDataProvider
     */
    public function testSetCheck($userGroupId, $expected)
    {
        $this->PermissionService->setCheck($userGroupId);
        $result = $this->PermissionService->Permissions->getCurrentPermissions();
        $this->assertEquals($expected, count($result));
    }
    public function setCheckDataProvider()
    {
        return [
            [2, 15],
            [100, 0]
        ];
    }

    /**
     * 権限チェック対象を追加する
     * @return void
     */
    public function testAddCheck(): void
    {
        $url = "/baser/admin/test/*";
        $auth = true;
        $this->loginAdmin($this->getRequest());
        $this->PermissionService->addCheck($url, $auth);
        $permissions = $this->PermissionService->Permissions->getCurrentPermissions();
        $result = array_pop($permissions);
        $this->assertEquals($url, $result->url);
        $this->assertEquals($auth, $result->auth);
    }

}
