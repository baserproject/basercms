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
use BcCustomContent\Test\Factory\CustomFieldFactory;
use BcCustomContent\Test\Scenario\CustomFieldsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class CustomFieldsControllerTest
 */
class CustomFieldsControllerTest extends BcTestCase
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
        'plugin.BcCustomContent.Factory/CustomFields',
        'plugin.BcCustomContent.Factory/CustomLinks',
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
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_fields/index.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->customFields);
    }

    /**
     * test view
     */
    public function test_view()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_fields/view/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->customField);
        $this->assertEquals('求人分類', $result->customField->title);

        //存在しないIDを指定した場合、
        $this->get('/baser/api/bc-custom-content/custom_fields/view/11.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません', $result->message);
    }

    /**
     * test add
     */
    public function test_add()
    {
        $data = [
            'title' => '求人分類',
            'name' => 'recruit_category',
            'type' => 'BcCcRelated',
            'status' => 1,
            'default_value' => '新卒採用',
        ];
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_fields/add.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('フィールド「求人分類」を追加しました。', $result->message);
        $this->assertEquals('求人分類', $result->customField->title);

        //エラーを発生したの場合、
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_fields/add.json?token=' . $this->accessToken, ['title' => null]);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('項目見出しを入力してください。', $result->errors->title->_empty);
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        $data = CustomFieldFactory::get(1);
        $data['title'] = 'test edit title';
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_fields/edit/1.json?token=' . $this->accessToken, $data->toArray());
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('フィールド「test edit title」を更新しました。', $result->message);
        $this->assertEquals('test edit title', $result->customField->title);

        //タイトルを指定しない場合、
        $data['title'] = null;
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_fields/edit/1.json?token=' . $this->accessToken, $data->toArray());
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('項目見出しを入力してください。', $result->errors->title->_empty);

        //存在しないIDを指定したの場合、
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_fields/edit/11.json?token=' . $this->accessToken, $data->toArray());
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
        //サービスをコル
        $dataBaseService = $this->getService(BcDatabaseServiceInterface::class);
        $customTable = $this->getService(CustomTablesServiceInterface::class);

        //データを生成
        $customTable->create([
            'id' => 1,
            'name' => 'recruit',
            'title' => '求人情報',
            'type' => '1',
            'display_field' => 'title',
            'has_child' => 0
        ]);
        $dataBaseService->addColumn('custom_entry_1_recruit', 'recruit_category', 'text');
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_fields/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('フィールド「求人分類」を削除しました。', $result->message);
        $this->assertEquals('求人分類', $result->customField->title);
        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit');

        //存在しないIDを指定したの場合、
        //APIを呼ぶ
        $this->post('/baser/api/bc-custom-content/custom_fields/delete/11.json?token=' . $this->accessToken);
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
        //データを生成
        $this->loadFixtureScenario(CustomFieldsScenario::class);
        //APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_fields/list.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('求人分類', $result->customFields->{1});
        $this->assertEquals('この仕事の特徴', $result->customFields->{2});
    }
}
