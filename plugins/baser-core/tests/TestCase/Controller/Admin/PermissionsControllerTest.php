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

use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\IntegrationTestTrait;


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
    
    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $request = $this->getRequest();
        $request = $this->loginAdmin($request);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * beforeFilter
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * アクセス制限設定の一覧を表示する
     */
    public function testAdmin_index()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
    public function testAdmin_edit()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 削除処理　(ajax)
     */
    public function testAdmin_ajax_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 削除処理
     */
    public function testAdmin_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
    public function testAdmin_ajax_copy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 無効状態にする（AJAX）
     */
    public function testAdmin_ajax_unpublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [ADMIN] 有効状態にする（AJAX）
     */
    public function testAdmin_ajax_publish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }


}
