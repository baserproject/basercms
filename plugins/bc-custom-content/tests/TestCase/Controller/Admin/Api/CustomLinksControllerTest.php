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
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Scenario\CustomContentsScenario;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class CustomLinksControllerTest
 */
class CustomLinksControllerTest extends BcTestCase
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
     * test index
     */
    public function test_index()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //テストデータを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_links.json?custom_table_id=1&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->customLinks);

        //custom_table_idを指定しない場合、
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_links.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに custom_table_id を指定してください。', $result->message);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');
    }

    /**
     * test add
     */
    public function test_add()
    {
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        CustomFieldFactory::make([
            'id' => 1,
            'name' => 'recruit_category',
        ])->persist();
        $customTable->create([
            'type' => 'recruit',
            'name' => 'recruit',
            'title' => '求人情報',
            'display_field' => '求人情報'
        ]);
        //Postデータを用意
        $data = [
            'custom_table_id' => 1,
            'custom_field_id' => 1,
            'lft' => 1,
            'rght' => 2,
            'name' => 'recruit_category_add',
            'title' => '求人分類',
            'type' => 'text'
        ];
        //APIを呼ぶ
        $this->post('/baser/api/admin/bc-custom-content/custom_links/add.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->customLink);
        $this->assertEquals('カスタムリンク「求人分類」を追加しました。', $result->message);
        //custom_entryテーブルにフィルドが生成されたか確認
        $this->assertTrue($dataBaseService->columnExists('custom_entry_1_recruit', 'recruit_category_add'));

        //タイトルがない場合、
        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_links/add.json?token=' . $this->accessToken, ['title' => '']);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('タイトルを入力してください。', $result->errors->title->_empty);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');

    }

    /**
     * test view
     */
    public function test_view()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_category',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_links/view/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->customLink);

        //存在しないIDを指定した場合、
        $this->get('/baser/api/admin/bc-custom-content/custom_links/view/11111.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_category');
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //テストデータを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_category',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //カスタムエントリテーブルでrecruit_categoryフィルドを生成
        $dataBaseService->addColumn('custom_entry_1_recruit_category', 'recruit_category', 'integer');
        $data = [
            'title' => '求人分類_edit',
            'name' => 'recruit_category_edit',
            'type' => 'BcCcRelated_edit',
            'status' => 1,
            'default_value' => '新卒採用_edit'
        ];
        //APIを呼ぶ
        $this->post('/baser/api/admin/bc-custom-content/custom_links/edit/1.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->customLink);
        $this->assertEquals('カスタムリンク「求人分類_edit」を更新しました。', $result->message);
        //custom_entry_1_recruit_categoryテーブルにrecruit_category_editが変更されたか確認すること
        $this->assertTrue($dataBaseService->columnExists('custom_entry_1_recruit_category', 'recruit_category_edit'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_category');

        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_links/edit/11.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        $data = [
            'title' => ''
        ];
        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_links/edit/1.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('タイトルを入力してください。', $result->errors->title->_empty);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //データを生成
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $this->loadFixtureScenario(CustomContentsScenario::class);
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //テストデータを生成
        //カスタムテーブルとカスタムエントリテーブルを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_category',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //カスタムエントリテーブルでrecruit_categoryフィルドを生成
        $dataBaseService->addColumn('custom_entry_1_recruit_category', 'recruit_category', 'integer');
        //APIを呼ぶ
        $this->post('/baser/api/admin/bc-custom-content/custom_links/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->customLink);
        $this->assertEquals('カスタムリンク「求人分類」を削除しました。', $result->message);
        //custom_entry_1_recruit_categoryテーブルにrecruit_categoryが存在しないか確認すること
        $this->assertFalse($dataBaseService->columnExists('custom_entry_1_recruit_category', 'recruit_category'));
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_category');

        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/admin/bc-custom-content/custom_links/delete/11.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test list
     */
    public function test_list()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/admin/bc-custom-content/custom_links/list/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('求人分類', $result->customLinks->{1});
        $this->assertEquals('この仕事の特徴', $result->customLinks->{2});
    }

    /**
     * test get_parent_list
     */
    public function test_get_parent_list()
    {
        //データを生成
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);
        $customTable->create([
            'id' => 1,
            'name' => 'recruit_category',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        //APIを呼ぶ
        $this->post('/baser/api/admin/bc-custom-content/custom_links/get_parent_list/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->parentList);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_category');
    }
}
