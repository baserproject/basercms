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
     * test index
     */
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
        $this->get('/baser/api/bc-custom-content/custom_entries/index.json?custom_table_id=1&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->entries);

        //custom_table_idを指定しない場合、
        $this->get('/baser/api/bc-custom-content/custom_entries/index.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに custom_table_id を指定してください。', $result->message);

        //ログインしていない状態では status パラメーターへへのアクセスを禁止するか確認
        $this->get('/baser/api/bc-custom-content/custom_entries/index.json?custom_table_id=1&status=publish');
        // レスポンスを確認
        $this->assertResponseCode(403);

        //ログインしている状態では status パラメーターへへのアクセできるか確認
        $this->get('/baser/api/bc-custom-content/custom_entries/index.json?custom_table_id=1&status=publish&token=' . $this->accessToken);
        // レスポンスを確認
        $this->assertResponseOk();

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
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
        $this->get('/baser/api/bc-custom-content/custom_entries/view/1.json?custom_table_id=1&token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertNotNull($result->entry);

        //custom_table_idを指定しない場合、
        $this->get('/baser/api/bc-custom-content/custom_entries/view/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('パラメーターに custom_table_id を指定してください。', $result->message);

        //ログインしていない状態では status パラメーターへへのアクセスを禁止するか確認
        $this->get('/baser/api/bc-custom-content/custom_entries/view/1.json?custom_table_id=1&status=publish');
        // レスポンスを確認
        $this->assertResponseCode(403);

        //ログインしている状態では status パラメーターへへのアクセできるか確認
        $this->get('/baser/api/bc-custom-content/custom_entries/view/1.json?custom_table_id=1&status=publish&token=' . $this->accessToken);
        // レスポンスを確認
        $this->assertResponseOk();

        //存在しないIDを指定した場合、
        // APIを呼ぶ
        $this->get('/baser/api/bc-custom-content/custom_entries/view/11.json?custom_table_id=11&status=publish&token=' . $this->accessToken);
        $this->assertResponseCode(404);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        //不要なテーブルを削除
        $dataBaseService->dropTable('custom_entry_1_recruit_categories');
    }

}
