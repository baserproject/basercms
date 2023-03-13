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

namespace BcUploader\Test\TestCase\Controller\Api;

use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcUploader\Test\Scenario\UploaderFilesScenario;
use BcUploader\Service\UploaderCategoriesServiceInterface;
use BcUploader\Test\Scenario\UploaderCategoriesScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class UploaderCategoriesControllerTest
 */
class UploaderCategoriesControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

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
        'plugin.BcUploader.Factory/UploaderFiles',
        'plugin.BcUploader.Factory/UploaderCategories',
        'plugin.BcUploader.Factory/UploaderConfigs',
        'plugin.BaserCore.Factory/Dblogs',
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
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test index
     * @return void
     */
    public function test_index()
    {
        $this->loadFixtureScenario(UploaderFilesScenario::class);
        //APIを呼ぶ
        $this->get("/baser/api/bc-uploader/uploader_categories/index.json?token=" . $this->accessToken);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->uploaderCategories);
    }

    /**
     * test add
     * @return void
     */
    public function test_add()
    {
        //アップロードカテゴリを追加
        $data = [
            'name' => 'japan'
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/add.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('新規アップロードカテゴリ「japan」を追加しました。', $result->message);
        $this->assertEquals('japan', $result->uploaderCategory->name);

        //400エラーを確認
        $data = [
            'name' => null
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/add.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('カテゴリ名を入力してください。', $result->errors->name->_empty);

        //500エラーを確認
        $data = [
            'name' => 'name...................................................'
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/add.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(500);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals("データベース処理中にエラーが発生しました。SQLSTATE[22001]: String data, right truncated: 1406 Data too long for column 'name' at row 1", $result->message);
    }

    /**
     * test edit
     * @return void
     */
    public function test_edit()
    {
        //テストデーターを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        $data = [
            'name' => '更新!'
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/edit/1.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('アップロードカテゴリ「更新!」を更新しました。', $result->message);
        $this->assertEquals('更新!', $result->uploaderCategory->name);

        //無効なアップロードカテゴリIDを指定した場合、
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/edit/10.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        //入力内容はヌルの場合、
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/edit/1.json?token=" . $this->accessToken, ['name' => '']);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('カテゴリ名を入力してください。', $result->errors->name->_empty);
    }

    /**
     * test copy
     * @return void
     */
    public function test_copy()
    {
        //テストデーターを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/copy/1.json?token=" . $this->accessToken);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('アップロードカテゴリ「blog_copy」をコピーしました。', $result->message);
        $this->assertEquals('blog_copy', $result->uploaderCategory->name);

        //無効なアップロードカテゴリIDを指定した場合、
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/copy/11.json?token=" . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(500);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データベース処理中にエラーが発生しました。__clone method called on non-object', $result->message);
    }

    /**
     * test delete
     * @return void
     */
    public function test_delete()
    {
        //テストデーターを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/delete/1.json?token=" . $this->accessToken);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('アップロードカテゴリ「blog」を削除しました。', $result->message);
        $this->assertEquals('blog', $result->uploaderCategory->name);

        //無効なアップロードカテゴリIDを指定した場合、
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/delete/10.json?token=" . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);
    }

    /**
     * test batch
     * @return void
     */
    public function test_batch()
    {
        // サービスクラス
        $uploaderCategoriesService = $this->getService(UploaderCategoriesServiceInterface::class);
        $dblogsService = $this->getService(DblogsServiceInterface::class);
        //テストデーターを生成
        $this->loadFixtureScenario(UploaderCategoriesScenario::class);
        $data = [
            'batch' => 'delete',
            'batch_targets' => [1, 2, 3]
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/batch.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseSuccess();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);

        // dblogsが生成されているか確認すること
        $dblogsData = $dblogsService->getDblogs(1)->toArray()[0];
        $this->assertEquals('アップロードカテゴリ「blog」、「contact」、「service」を 削除 しました。', $dblogsData->message);
        $this->assertEquals(1, $dblogsData->user_id);
        $this->assertEquals('UploaderCategories', $dblogsData->controller);
        $this->assertEquals('batch', $dblogsData->action);

        // データが削除されているか確認すること
        $uploaderCategories = $uploaderCategoriesService->getIndex([])->all();
        $this->assertCount(0, $uploaderCategories);

        //存在しないアップロードカテゴリIDを指定した場合、
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/batch.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(404);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        // 無効な$allowMethodを指定の場合は、
        $data = [
            'batch' => 'add',
            'batch_targets' => [1, 2, 3]
        ];
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_categories/batch.json?token=" . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseCode(500);
    }
}
