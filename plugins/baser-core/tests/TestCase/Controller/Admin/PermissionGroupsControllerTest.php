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

namespace BaserCore\Test\TestCase\Controller\Admin;

use BaserCore\Test\Factory\PermissionGroupFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PermissionGroupsControllerTest
 *
 */
class PermissionGroupsControllerTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test index
     */
    public function test_index()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        //データを生成
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        //対象URLをコル
        $this->get('/baser/admin/baser-core/permission_groups/index/1');
        //ステータスを確認
        $this->assertResponseOk();
        //戻り値を確認
        $vars = $this->_controller->viewBuilder()->getVars();
        //userGroupIdが存在するのを確認
        $this->assertEquals(1, $vars['userGroupId']);
    }

    /**
     * test add
     */
    public function test_add()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $data = [
            'name' => 'システム基本設定　テスト',
            'type' => 'Admin',
            'plugin' => 'BaserCore',
            'status' => 1
        ];
        //APIをコール
        $this->post('/baser/admin/baser-core/permission_groups/add/1?prefix=Admin', $data);
        //フラッシュメッセージを確認
        $this->assertFlashMessage('ルールグループ「システム基本設定　テスト」を登録しました。');
        //ステータスを確認
        $this->assertResponseCode(302);
        //リダイレクトを確認
        // 正確には https://localhost/baser/admin/baser-core/permission_groups/edit/1/1 だが、
        // 全体テストでは、TransactionStrategy を利用する場合、オートインクリメントがリセットされず、末尾が4 となってしまうため
        // 末尾の判定を無視
        $this->assertRedirectContains('https://localhost/baser/admin/baser-core/permission_groups/edit/1/');
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $data = [
            'name' => 'システム基本設定　Update',
            'permissions' => [[
                'id' => "1",
                'name' => "一覧",
                'user_group_id' => "2",
                'method' => "GET",
                'auth' => "1",
                'status' => "1"
            ]]
        ];
        $this->post('/baser/admin/baser-core/permission_groups/edit/1/1', $data);
        //メッセージを確認
        $this->assertFlashMessage('ルールグループ「システム基本設定　Update」を更新しました。');
        //リダイレクトを確認
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'PermissionGroups',
            'action' => 'edit',
            '1',
            '1'
        ]);
        //アクセスグループが編集できるか確認すること
        $permission = PermissionGroupFactory::get(1);
        $this->assertEquals($permission->name, 'システム基本設定　Update');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        //対象URLをコール
        $this->post('/baser/admin/baser-core/permission_groups/delete/1/1');
        //フラッシュメッセージを確認
        $this->assertFlashMessage('アクセスグループ「コンテンツフォルダ管理」を削除しました。');
        //ステータスを確認
        $this->assertResponseCode(302);
        //リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'PermissionGroups',
            'action' => 'index',
            '1'
        ]);
        //削除したアクセスグループが存在するか確認すること
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        PermissionGroupFactory::get(1);
    }

    public function test_rebuild_by_user_group()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        //実行成功場合
        $this->post('/baser/admin/baser-core/permission_groups/rebuild_by_user_group/1');
        //メッセージを確認
        $this->assertFlashMessage('アクセスルールの再構築に成功しました。');
        //リダイレクトを確認
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'PermissionGroups',
            'action' => 'index',
            '1'
        ]);
    }
}
