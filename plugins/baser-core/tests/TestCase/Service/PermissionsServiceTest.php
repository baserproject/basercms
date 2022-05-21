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
        $permission = $this->PermissionsService->create($data);
        $newRecord = $this->PermissionsService->Permissions->find()->all()->last();
        $this->assertEquals($newRecord->name, $permission->name);
    }

    /**
     * Test update
     *
     * @return void
     */
    public function testUpdate()
    {
        $record = $this->PermissionsService->Permissions->find()->first();
        $id = $record->id;
        $beforeName = $record->name;
        $data = [
            'id' => $id,
            'name' => 'testUpdate',
            'user_group_id' => '2',
            'url' => '/baser/admin/*'
        ];

        $this->PermissionsService->update($record, $data);
        $record = $this->PermissionsService->Permissions->get($id);

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
        $copyPermission = $this->PermissionsService->copy($permission->id);

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
        $this->PermissionsService->delete($beforeId);

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

        $this->PermissionsService->publish($permission->id);

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

        $this->PermissionsService->unpublish($permission->id);

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
            $this->PermissionsService->getMethodList(),
            ['*' => '全て',
            'GET' => '表示のみ',
            'POST' => '表示と編集',]
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
            $this->PermissionsService->getAuthList(),
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
        $reflection = new \ReflectionClass($this->PermissionsService);
        $method = $reflection->getMethod('autoFillRecord');
        $method->setAccessible(true);

        $data = $method->invokeArgs($this->PermissionsService, [[]]);
        $this->assertGreaterThan(0, $data['no']);
        $this->assertGreaterThan(0, $data['sort']);
        $this->assertFalse($data['auth']);
        $this->assertEquals('*', $data['method']);
        $this->assertTrue($data['status']);

        $data = $method->invokeArgs($this->PermissionsService, [[
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
        $result = $this->PermissionsService->addCheck("/fuga", false);
        $result = $this->PermissionsService->addCheck("/piyo", true);
        $result = $this->PermissionsService->check($url, $userGroup);
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
        $this->PermissionsService->addCheck($url, $auth);
        $result = $this->PermissionsService->check($url, [2]);
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
        $this->PermissionsService->changeSort($beforeOrderId[0], 2, $conditions);

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

        $this->PermissionsService->changeSort($beforeOrderId[0], -2, $conditions);
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

    /**
     * test getList
     */
    public function test_getLis()
    {
        $this->assertEquals([], $this->PermissionsService->getList());
    }

}
