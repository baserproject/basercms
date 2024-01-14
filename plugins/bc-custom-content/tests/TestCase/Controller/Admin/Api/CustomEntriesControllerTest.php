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

namespace BcCustomContent\Test\TestCase\Controller\Admin\Api;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomEntriesScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class CustomContentsControllerTest
 */
class CustomEntriesControllerTest extends BcTestCase
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
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test view
     */
    public function test_view()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_entries/view/1.json?custom_table_id=1&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('Webエンジニア・Webプログラマー', $result->entry->title);
        $this->assertEquals('求人情報', $result->entry->custom_table->title);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');

        //custom_table_idを指定しない場合。
        $this->get('/baser/api/admin/bc-custom-content/custom_entries/view/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに custom_table_id を指定してください。', $result->message);

        //存在しないcustom_table_idを指定しない場合。
        $this->get('/baser/api/admin/bc-custom-content/custom_entries/view/10.json?custom_table_id=11&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        //存在しないIDを指定しない場合。
        $this->get('/baser/api/admin/bc-custom-content/custom_entries/view/100.json?custom_table_id=1&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(500);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(
            "データベース処理中にエラーが発生しました。SQLSTATE[42S02]: Base table or view not found: 1146 Table 'test_basercms.custom_entry_1_recruit_categories' doesn't exist",
            $result->message
        );
    }

    /**
     * test add
     */
    public function test_add()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //追加データを準備
        $data = [
            'custom_table_id' => 1,
            'name' => 'プログラマー',
            'title' => 'プログラマー',
            'creator_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'level' => 0,
            'status' => 1
        ];
        //APIを呼ぶ
        $this->post('/baser/api/admin/bc-custom-content/custom_entries/add.json?custom_table_id=1&token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->entry);
        $this->assertEquals('フィールド「プログラマー」を追加しました。', $result->message);

        //タイトルがない場合、
        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_entries/add.json?custom_table_id=1&token=' . $this->accessToken, []);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('This field is required', $result->errors->title->_required);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');

        //custom_table_idを指定しない場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_entries/add.json?custom_table_id=0&token=' . $this->accessToken, []);
        //ステータスを確認
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに custom_table_id を指定してください。', $result->message);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        $data = [
            'title' => 'title updated',
        ];
        //APIを呼ぶ
        $this->post('/baser/api/admin/bc-custom-content/custom_entries/edit/1.json?custom_table_id=1&token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->entry);
        $this->assertEquals('フィールド「title updated」を更新しました。', $result->message);

        //タイトルを指定しない場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_entries/edit/1.json?custom_table_id=1&token=' . $this->accessToken, ['title' => '']);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('タイトルは必須項目です。', $result->errors->title->_empty);

        //存在しないIDを指定した場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_entries/edit/11.json?custom_table_id=11&token=' . $this->accessToken, ['title' => '']);
        $this->assertResponseCode(404);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        //custom_table_idを指定しない場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_entries/edit/11.json?token=' . $this->accessToken, ['title' => '']);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに custom_table_id を指定してください。', $result->message);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);

        //APIを呼ぶ
        $this->post('/baser/api/admin/bc-custom-content/custom_entries/delete/1.json?custom_table_id=1&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->entry);
        $this->assertEquals('フィールド「Webエンジニア・Webプログラマー」を削除しました。', $result->message);

        //custom_table_idを指定しない場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_entries/delete/11.json?token=' . $this->accessToken, ['title' => '']);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに custom_table_id を指定してください。', $result->message);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

    public function test_index()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_entries.json?custom_table_id=1&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(3, $result->entries);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');

        //パラメーターに custom_table_id を指定しない場合、
        $this->get('/baser/api/admin/bc-custom-content/custom_entries.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに custom_table_id を指定してください。', $result->message);

        //存在しないcustom_table_id を指定した場合、
        $this->get('/baser/api/admin/bc-custom-content/custom_entries.json?custom_table_id=11&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(500);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(
            'データベース処理中にエラーが発生しました。Record not found in table `custom_tables`.',
            $result->message
        );
    }

    /**
     * test list
     */
    public function test_list()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_categories',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //フィクチャーからデーターを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomEntriesScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_entries/list.json?custom_table_id=1&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->entries);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }
}
