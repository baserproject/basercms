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
        'plugin.BaserCore.Dblogs',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $request = $this->getRequest();
        $this->loginAdmin($request);
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
    public function testInitialize()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // $this->assertNotEmpty($this->PermissionsController->BcAuth);
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
     * 並び替えを更新する [AJAX]
     */
    public function testAdmin_ajax_update_sort()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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


}
