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

use BaserCore\Service\UtilitiesService;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\Filesystem\File;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Composer\Package\Archiver\ZipArchiver;

class UtilitiesControllerTest extends BcTestCase
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
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Permissions',
        'plugin.BaserCore.Factory/Pages',
        'plugin.BaserCore.Factory/SearchIndexes',
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
     * test verity_contents_tree
     * @return void
     */
    public function test_verity_contents_tree()
    {
        //ツリー構造チェックが成功
        ContentFactory::make(['id' => 101, 'name' => 'BaserCore 1', 'type' => 'ContentFolder', 'lft' => 1, 'rght' => 2])->persist();
        ContentFactory::make(['id' => 102, 'name' => 'BaserCore 2', 'type' => 'ContentFolder', 'lft' => 3, 'rght' => 4])->persist();

        $this->post('/baser/api/admin/baser-core/utilities/verity_contents_tree.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('コンテンツのツリー構造に問題はありません。', $result->message);

        //ツリー構造チェックが失敗
        ContentFactory::make(['id' => 103, 'name' => 'BaserCore 3', 'type' => 'ContentFolder', 'lft' => 5, 'rght' => 6])->persist();
        ContentFactory::make(['id' => 104, 'name' => 'BaserCore 4', 'type' => 'ContentFolder', 'lft' => 7, 'rght' => 8, 'parent_id' => 103])->persist();

        $this->post('/baser/api/admin/baser-core/utilities/verity_contents_tree.json?token=' . $this->accessToken);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('コンテンツのツリー構造に問題があります。ログを確認してください。', $result->message);
    }

    /**
     * test clear_cache
     * @return void
     */
    public function test_clear_cache()
    {
        $this->post('/baser/api/admin/baser-core/utilities/clear_cache.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('サーバーキャッシュを削除しました。', $result->message);
    }

    /**
     * test reset_contents_tree
     * @return void
     */
    public function test_reset_contents_tree()
    {
        ContentFactory::make(['id' => 1, 'name' => 'BaserCore root', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 1, 'rght' => 2])->persist();
        ContentFactory::make(['name' => 'BaserCore 1', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 11, 'rght' => 12])->persist();
        ContentFactory::make(['name' => 'BaserCore 2', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 13, 'rght' => 14])->persist();

        $this->post('/baser/api/admin/baser-core/utilities/reset_contents_tree.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('コンテンツのツリー構造をリセットしました。', $result->message);
    }

    /**
     * test restore_db
     * @return void
     */
    public function test_restore_db()
    {
        $this->post('/baser/api/admin/baser-core/utilities/restore_db.json?token=' . $this->accessToken, ['encoding' => 'utf8']);
        $this->assertResponseCode(500);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データベース処理中にエラーが発生しました。バックアップファイルが送信されませんでした。', $result->message);

        $zipSrcPath = TMP;

        $this->execPrivateMethod(new UtilitiesService(), '_writeBackup', [$zipSrcPath . 'schema', 'BaserCore', 'utf8']);

        $zip = new ZipArchiver();
        $testFile = $zipSrcPath . 'test.zip';
        $zip->archive($zipSrcPath . 'schema', $testFile, true);

        $this->setUploadFileToRequest('backup', $testFile);

        $this->post('/baser/api/admin/baser-core/utilities/restore_db.json?token=' . $this->accessToken, ['encoding' => 'utf8']);

        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($result->message, 'データの復元が完了しました。');
    }

    /**
     * test download_backup
     * @return void
     */
    public function test_download_backup()
    {
        $this->markTestIncomplete('このテストは、実装方法検討中です。');
        // TODO header を出力するためのエラーが発生するためコメントアウト 2022/10/04 ryuring
        // runInSeparateProcess アノテーションを利用する事で抑制できたが
        // 全体テストに切り替えると、なぜか、
        // plugins/bc-admin-third/templates/Admin/Users/index.php にて
        // 「Using $this when not in object context」というエラーが発生し、解決方法がわからず断念した
        // >>>
//        $this->get('/baser/api/admin/baser-core/utilities/download_backup.json?backup_encoding=utf8&token=' . $this->accessToken);
//        $this->assertResponseOk();
        // <<<
    }

    /**
     * test download_backup
     * @return void
     */
    public function test_delete_log()
    {
        $logPath = LOGS . 'error.log';
        if (!file_exists($logPath)) {
            new File($logPath, true);
        }

        $this->post('/baser/api/admin/baser-core/utilities/delete_log.json?token=' . $this->accessToken);
        $this->assertResponseOk();

        $this->post('/baser/api/admin/baser-core/utilities/delete_log.json?token=' . $this->accessToken);
        $this->assertResponseCode(500);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals($result->message, "データベース処理中にエラーが発生しました。エラーログが存在しません。");
    }


    /**
     * test download_log
     * @return void
     */
    public function test_download_log()
    {
        $this->markTestIncomplete('このテストは、実装方法検討中です。');
        // TODO header を出力するためのエラーが発生するためコメントアウト 2022/10/04 ryuring
        // runInSeparateProcess アノテーションを利用する事で抑制できたが
        // 全体テストに切り替えると、なぜか、
        // plugins/bc-admin-third/templates/Admin/Users/index.php にて
        // 「Using $this when not in object context」というエラーが発生し、解決方法がわからず断念した
        // >>>
//        $this->get('/baser/api/admin/baser-core/utilities/download_log.json?token=' . $this->accessToken);
//        $this->assertResponseOk();
        // <<<
    }

    /**
     * test save_search_opened
     */
    public function test_save_search_opened()
    {
        $this->post('/baser/api/admin/baser-core/utilities/save_search_opened/key1/open1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertTrue($result->result);
        $this->assertEquals($_SESSION["BcApp"]["adminSearchOpened"]["key1"], 'open1');
    }
}
