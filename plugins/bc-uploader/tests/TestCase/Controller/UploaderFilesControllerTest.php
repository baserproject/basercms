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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Filesystem\File;
use BcUploader\Test\Scenario\UploaderFilesScenario;
use BcUploader\Test\Factory\UploaderFileFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class UploaderFilesControllerTest
 */
class UploaderFilesControllerTest extends BcTestCase
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
        //データを生成
        $this->loadFixtureScenario(UploaderFilesScenario::class);

        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_files/index.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        // 戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(6, $result->uploaderFiles);
    }

    /**
     * test upload
     */
    public function test_upload()
    {
        $pathTest = TMP . 'test' . DS;
        $pathUpload = WWW_ROOT . DS . 'files' . DS . 'uploads' . DS;

        //テストファイルを作成
        new File($pathTest . 'testUpload.txt', true);
        $testFile = $pathTest . 'testUpload.txt';

        //アップロードファイルを準備
        $this->setUploadFileToRequest('file', $testFile);
        $this->setUnlockedFields(['file']);

        //APIをコル
        $this->post("/baser/api/bc-uploader/uploader_files/upload.json?token=" . $this->accessToken);

        //レスポンスステータスを確認
        $this->assertResponseOk();

        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('アップロードファイル「testUpload.txt」を追加しました。', $result->message);
        $this->assertNotNull($result->uploaderFile);

        //ファイルがアップロードできるか確認
        $this->assertTrue(file_exists($pathUpload . 'testUpload.txt'));

        //不要ファイルを削除
        unlink($pathUpload . 'testUpload.txt');
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        //データを生成
        UploaderFileFactory::make(['id' => 1, 'name' => '2_2.jpg', 'atl' => '2_2.jpg', 'user_id' => 1])->persist();
        $data = UploaderFileFactory::get(1);
        $data->alt = 'test edit';
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_files/edit/1.json?token=" . $this->accessToken, $data->toArray());
        // レスポンスコードを確認する
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        //メッセージを確認
        $this->assertEquals($result->message, 'アップロードファイル「2_2.jpg」を更新しました。');
        //値が変更されるか確認
        $this->assertEquals($result->uploaderFile->alt, 'test edit');
    }

    /**
     * test delete
     * @return void
     */
    public function test_delete()
    {
        $pathImg = WWW_ROOT . DS . 'files' . DS . 'uploads' . DS;
        //テストファイルを作成
        new File($pathImg . '2_2.jpg', true);
        //データを生成
        UploaderFileFactory::make(['id' => 1, 'name' => '2_2.jpg', 'atl' => '2_2.jpg', 'user_id' => 1])->persist();
        //APIを呼ぶ
        $this->post("/baser/api/bc-uploader/uploader_files/delete/1.json?token=" . $this->accessToken);
        // レスポンスコードを確認する
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($result->message, 'アップロードファイル「2_2.jpg」を削除しました。');
        $this->assertEquals($result->uploadFile->name, '2_2.jpg');
        //ファイルが削除できるか確認
        $this->assertFalse(file_exists($pathImg . '2_2.jpg'));
    }
}
