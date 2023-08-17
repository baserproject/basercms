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

namespace BaserCore\Test\TestCase\Controller\Api\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PermissionGroupsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PermissionGroupsControllerTest
 */
class PermissionGroupsControllerTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
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
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $this->get('/baser/api/admin/baser-core/permission_groups/index.json?user_group_id=1&token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(3, $result->permissionGroups);
    }

    /**
     * test index
     */
    public function test_list()
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $this->get('/baser/api/admin/baser-core/permission_groups/list.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->permissionGroups);
    }

    /**
     * test view
     */
    public function test_view()
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        $this->get('/baser/api/admin/baser-core/permission_groups/view/1.json?token=' . $this->accessToken);
        $this->assertResponseSuccess();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->permissionGroup->name);

        //存在しないIDを指定した場合、
        $this->get('/baser/api/admin/baser-core/permission_groups/view/11.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test add
     */
    public function test_add()
    {
        //Postデータを用意
        $data = [
            'name' => 'システム基本設定　テスト',
            'type' => 'Admin',
            'plugin' => 'BaserCore',
            'status' => 1
        ];
        //APIをコール
        $this->post('/baser/api/admin/baser-core/permission_groups/add.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->permissionGroup);
        $this->assertEquals('ルールグループ「システム基本設定　テスト」を登録しました。', $result->message);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        //Postデータを用意
        $data = [
            'name' => 'システム基本設定　Update',
        ];
        //APIをコール
        $this->post('/baser/api/admin/baser-core/permission_groups/edit/1.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->permissionGroup);
        $this->assertEquals('ルールグループ「システム基本設定　Update」を更新しました。', $result->message);

        //存在しないIDを指定した場合。
        //APIをコール
        $this->post('/baser/api/admin/baser-core/permission_groups/edit/11.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        //APIをコール
        $this->post('/baser/api/admin/baser-core/permission_groups/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->permissionGroup);
        $this->assertEquals('ルールグループ「コンテンツフォルダ管理」を削除しました。', $result->message);

        //存在しないIDを指定した場合。
        //APIをコール
        $this->post('/baser/api/admin/baser-core/permission_groups/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    public function test_rebuild_by_user_group()
    {
        $this->loadFixtureScenario(PermissionGroupsScenario::class);
        //APIをコール
        $this->post('/baser/api/admin/baser-core/permission_groups/rebuild_by_user_group/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('アクセスルールの再構築に成功しました。', $result->message);
    }
}
