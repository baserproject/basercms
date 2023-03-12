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

namespace BcCustomContent\Test\TestCase\Controller\Api;

use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Service\CustomTablesServiceInterface;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class CustomTablesControllerTest
 */
class CustomTablesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BcCustomContent.Factory/CustomTables',
    ];

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
        $this->setFixtureTruncate();
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

        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);
        $customTable->create([
            'type' => 'recruit',
            'name' => 'recruit',
            'title' => '求人',
            'display_field' => '求人'
        ]);
        //APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_tables/index.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->customTables);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
        $dataBaseService->dropTable('custom_entry_2_recruit');
    }

    /**
     * test view
     */
    public function test_view()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);
        //APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_tables/view/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->customTable);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');

        //存在しないcustomTableIDを指定場合、
        $this->get('/baser/api/bc-custom-content/custom_tables/view/11.json?token=' . $this->accessToken);
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
        //テストデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ];
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_tables/add.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('テーブル「お問い合わせタイトル」を追加しました。', $result->message);
        $this->assertEquals('contact', $result->customTable->name);

        //自動テーブルが生成できるか確認すること
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $this->assertTrue($dataBaseService->tableExists('custom_entry_1_contact'));

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');

        //エラーを発生した時のテスト
        //テストデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'お問い合わせタイトル',
        ];
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_tables/add.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals(
            '識別名は半角英数字とアンダースコアのみで入力してください。',
            $result->errors->name->regex);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $data = [
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ];
        $customTable->create($data);

        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_tables/edit/1.json?token=' . $this->accessToken, ['name' => 'contact_edit']);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('テーブル「お問い合わせタイトル」を更新しました。', $result->message);
        $this->assertEquals('contact_edit', $result->customTable->name);

        //テーブル名も変更されたの確認
        $this->assertTrue($dataBaseService->tableExists('custom_entry_1_contact_edit'));
        //変更した前テーブル名が存在しないの確認
        $this->assertFalse($dataBaseService->tableExists('custom_entry_1_contact'));

        //エラーする時をテスト
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_tables/edit/1.json?token=' . $this->accessToken, ['name' => 'あああああ']);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals(
            '識別名は半角英数字とアンダースコアのみで入力してください。',
            $result->errors->name->regex);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact_edit');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);

        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_tables/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('テーブル「お問い合わせタイトル」を削除しました。', $result->message);
        $this->assertEquals('contact', $result->customTable->name);
        //テーブル名が存在しないの確認
        $this->assertFalse($dataBaseService->tableExists('custom_entry_1_contact'));

        //エラーを発生した時をテスト
        $this->post('/baser/api/bc-custom-content/custom_tables/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test batch
     */
    public function test_list()
    {
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //テストデータを生成
        $customTable->create([
            'type' => 'contact',
            'name' => 'contact',
            'title' => 'お問い合わせタイトル',
            'display_field' => 'お問い合わせ'
        ]);
        $customTable->create([
            'type' => 'recruit',
            'name' => 'recruit',
            'title' => '求人',
            'display_field' => '求人'
        ]);
        //APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_tables/list.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('お問い合わせタイトル', $result->customTables->{1});
        $this->assertEquals('求人', $result->customTables->{2});
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_contact');
        $dataBaseService->dropTable('custom_entry_2_recruit');
    }
}
