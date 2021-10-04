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

namespace BaserCore\Test\TestCase\Controller\Admin;

use Cake\Event\Event;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\Controller\Admin\PermissionsController;


/**
 * BaserCore\Controller\Admin\PermissionsController Test Case
 */
class PermissionsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Permissions',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Dblogs',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $request = $this->getRequest('/baser/admin/baser-core/users/');
        $request = $this->loginAdmin($request);
        $this->PermissionsController = new PermissionsController($request);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->PermissionsController);
        parent::tearDown();
    }

    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $event = new Event('Controller.beforeFilter', $this->PermissionsController);
        
        $this->PermissionsController->beforeFilter($event);
        $this->assertNotEmpty($this->PermissionsController->Permissions);
        $this->assertNotEmpty($this->PermissionsController->viewBuilder()->getHelpers('BcTime'));
        
        $unLockActions = $this->PermissionsController->Security->getConfig("unlockedActions");
        $this->assertEquals($unLockActions, [
            0 => 'update_sort',
            1 => 'batch',
        ]);
    }

    /**
     * アクセス制限設定の一覧を表示する
     */
    public function testIndex()
    {
        $this->get('/baser/admin/baser-core/permissions/index');
        $this->assertResponseError();
        $this->get('/baser/admin/baser-core/permissions/index/1');
        $this->assertResponseOk();
    }

    /**
     * Test add method
     *
     * @return void
     */
    public function testAdd()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'name' => 'テストルール名',
            'url' => '/baser/admin/baser-core/users/index/?test',
            'method' => '*',
            'status' => 1,
        ];
        $this->post('/baser/admin/baser-core/permissions/add/2', $data);
        $this->assertResponseSuccess();

        $permissions = $this->getTableLocator()->get('Permissions');
        $permission = $permissions->find()->order(['id' => 'DESC'])->first();
        $this->assertEquals('テストルール名', $permission->name);
    }

    /**
     * [ADMIN] 登録処理
     */
    public function testAdmin_ajax_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 編集処理
     */
    public function testEdit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $permissions = $this->getTableLocator()->get('Permissions');
        $permission = $permissions->find()->order(['id' => 'ASC'])->last();
        $permissionBeforeName = $permission->name;
        $permissionUgi = $permission->user_group_id;
        $permission->name .= '変更';
        $this->post('/baser/admin/baser-core/permissions/edit/2/' . $permission->id, $permission->toArray());
        $this->assertResponseSuccess();

        $permission = $permissions->find()->order(['id' => 'ASC'])->last();

        $this->assertNotEquals($permission->name, $permissionBeforeName);
        $this->assertEquals($permission->name, $permissionBeforeName . '変更');
    }

    /**
     * [ADMIN] 削除処理
     */
    public function testDelete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $permissions = $this->getTableLocator()->get('Permissions');
        $permission = $permissions->find()->order(['id' => 'ASC'])->last();
        $permissionId = $permission->id;
        $this->post('/baser/admin/baser-core/permissions/delete/' . $permission->id);
        $this->assertResponseSuccess();

        $permission = $permissions->find()->where(['id' => $permissionId])->last();
        $this->assertNull($permission);
    }

    /**
     * [ADMIN] データコピー（AJAX）
     */
    public function testCopy()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $permissions = $this->getTableLocator()->get('Permissions');
        $permission = $permissions->find()->order(['id' => 'ASC'])->last();
        $permissionId = $permission->id;
        $this->post('/baser/admin/baser-core/permissions/copy/' . $permission->id);
        $this->assertResponseSuccess();

        $permission = $permissions->find()->order(['id' => 'ASC'])->last();
        $this->assertGreaterThan($permissionId, $permission->id);

    }

    /**
     * [ADMIN] 無効状態にする（AJAX）
     */
    public function testUnpublish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $permissions = $this->getTableLocator()->get('Permissions');
        $permission = $permissions->find()->order(['id' => 'ASC'])->last();
        $permissionId = $permission->id;
        $permissionUgi = $permission->user_group_id;
        $permission->status = true;
        $permissions->save($permission);
        $this->post('/baser/admin/baser-core/permissions/unpublish/' . $permission->id);
        $this->assertRedirect('/baser/admin/baser-core/permissions/index/' . $permissionUgi);

        $permission = $permissions->find()->where(['id' => $permissionId])->last();
        $this->assertFalse($permission->status);
    }

    /**
     * [ADMIN] 有効状態にする（AJAX）
     */
    public function testPublish()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $permissions = $this->getTableLocator()->get('Permissions');
        $permission = $permissions->find()->order(['id' => 'ASC'])->last();
        $permissionId = $permission->id;
        $permissionUgi = $permission->user_group_id;
        $permission->status = false;
        $permissions->save($permission);
        $this->post('/baser/admin/baser-core/permissions/publish/' . $permission->id);
        $this->assertRedirect('/baser/admin/baser-core/permissions/index/' . $permissionUgi);

        $permission = $permissions->find()->where(['id' => $permissionId])->last();
        $this->assertTrue($permission->status);
    }
    
    /**
     * 一括処理
     *
     */
    public function testBatch()
    {
        $this->enableCsrfToken();
        $permissions = $this->getTableLocator()->get('Permissions');
        
        // 空データ送信
        $this->post('/baser/admin/baser-core/permissions/batch', []);
        $this->assertResponseEmpty();
        
        // unpublish
        $data = [
            'ListTool' => [
                'batch' => 'unpublish',
                'batch_targets' => [1],
            ]
        ];
        $this->post('/baser/admin/baser-core/permissions/batch', $data);
        $this->assertResponseNotEmpty();
        
        $permission = $permissions->find()->where(['id' => 1])->last();
        $this->assertFalse($permission->status);
       
        // publish
        $data = [
            'ListTool' => [
                'batch' => 'publish',
                'batch_targets' => [1],
            ]
        ];
        $this->post('/baser/admin/baser-core/permissions/batch', $data);
        $this->assertResponseNotEmpty();
        
        $permission = $permissions->find()->where(['id' => 1])->last();
        $this->assertTrue($permission->status);
        
        // delete
        $data = [
            'ListTool' => [
                'batch' => 'delete',
                'batch_targets' => [1],
            ]
        ];
        $this->post('/baser/admin/baser-core/permissions/batch', $data);
        $this->assertResponseNotEmpty();
        
        $permission = $permissions->find()->where(['id' => 1])->last();
        $this->assertNull($permission);
    }
    
    /**
     * 表示順変更
     */
    public function testUpdate_sort()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->post('/baser/admin/baser-core/permissions/update_sort/2');
        $this->assertResponseFailure();
        
        
        $data = [
            'Sort' => [
                'id' => 1,
                'offset' => 2
            ]
        ];
        $permissions = $this->getTableLocator()->get('Permissions');
        $permissionList = $permissions
            ->find()
            ->order(['sort' => 'ASC'])
            ->select('id')
            ->limit(3)
            ->all();
        $beforeOrderId = [];
        foreach($permissionList as $permission) {
            $beforeOrderId[] = $permission->id;
        }
        $this->post('/baser/admin/baser-core/permissions/update_sort/2', $data);
        $permissionList = $permissions
            ->find()
            ->order(['sort' => 'ASC'])
            ->select('id')
            ->limit(3)
            ->all();
        
        $afterOrderId = [];
        foreach($permissionList as $permission) {
            $afterOrderId[] = $permission->id;
        }
        $this->assertNotEquals($beforeOrderId, $afterOrderId);
    }
    


}
