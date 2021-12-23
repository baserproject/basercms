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
        $record = $this->PermissionService->Permissions->find()->first();
        $id = $record->id;
        $beforeName = $record->name;
        $data = [
            'id' => $id,
            'name' => 'testUpdate',
            'user_group_id' => '2',
            'url' => '/baser/admin/*'
        ];

        $this->PermissionService->update($record, $data);
        $record = $this->PermissionService->Permissions->get($id);

        $this->assertEquals($id, $record->id);
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

        $this->PermissionService->publish($permission->id);

        $permission = $permissions->get($permission->id);
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

        $this->PermissionService->unpublish($permission->id);

        $permission = $permissions->get($permission->id);
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
     * Test getAuthList
     *
     * @return void
     */
    public function testGetAuthList()
    {
        $this->assertEquals(
            $this->PermissionService->getAuthList(),
            [
                0 => '拒否',
                1 => '許可',
            ]
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
        $this->assertFalse($data['auth']);
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
     * @param string $url
     * @param array $userGroupId
     * @param bool $expected 期待値
     * @dataProvider checkDataProvider
     */
    public function testCheck($url, $userGroup, $expected)
    {
        $result = $this->PermissionService->addCheck("/fuga", false);
        $result = $this->PermissionService->addCheck("/piyo", true);
        $result = $this->PermissionService->check($url, $userGroup);
        $this->assertEquals($expected, $result);
    }

    public function checkDataProvider()
    {
        return [
            ['hoge', [1], true],
            ['hoge', [2], false],
            ['/fuga', [1], true],
            ['/fuga', [2], false],
            ['/piyo', [2], true],
            ['/baser/admin/baser-core/users/logout', [2], true],
            ['/baser/admin/pages/2000', [2], true],
            ['/baser/admin/bc-blog/blog_post/edit/100', [2, 3], true],
            ['/baser/admin/bc-blog/blog_post/edit/100', [2], false],
            ['/baser/admin/bc-blog/blog_post/add', [2, 3], false],
            ['/baser/admin/baser-core/contents/delete', [2, 3], true],
        ];
    }


    /**
     * 権限チェック対象を追加する
     * @param string $url
     * @param bool $expected
     * @return void
     * @dataProvider addCheckDataProvider
     */
    public function testAddCheck($url, $auth, $expected)
    {
        $this->PermissionService->addCheck($url, $auth);
        $result = $this->PermissionService->check($url, [2]);
        $this->assertEquals($expected, $result);

    }
    public function addCheckDataProvider()
    {
        return [
            ["/baser/admin/test1/*", false, false],
            ["/baser/admin/test2/*", true, true],
        ];
    }

    public function testChangeSort()
    {
        $permissions = $this->getTableLocator()->get('Permissions');
        $permissionList = $permissions
            ->find()
            ->order(['sort' => 'ASC'])
            ->limit(3)
            ->all();
        $beforeOrderId = [];
        foreach($permissionList as $permission) {
            $beforeOrderId[] = $permission->id;
        }

        $conditions = ['user_group_id' => 2];
        $this->PermissionService->changeSort($beforeOrderId[0], 2, $conditions);

        $permissionList = $permissions
            ->find()
            ->order(['sort' => 'ASC'])
            ->limit(3)
            ->all();
        $afterOrderId = [];
        foreach($permissionList as $permission) {
            $afterOrderId[] = $permission->id;
        }
        $this->assertEquals($beforeOrderId[0], $afterOrderId[2]);
        $this->assertEquals($beforeOrderId[1], $afterOrderId[0]);
        $this->assertEquals($beforeOrderId[2], $afterOrderId[1]);

        $this->PermissionService->changeSort($beforeOrderId[0], -2, $conditions);
        $permissionList = $permissions
            ->find()
            ->order(['sort' => 'ASC'])
            ->limit(3)
            ->all();
        $afterOrderId2 = [];
        foreach($permissionList as $permission) {
            $afterOrderId2[] = $permission->id;
        }
        $this->assertEquals($beforeOrderId, $afterOrderId2);
    }

}
