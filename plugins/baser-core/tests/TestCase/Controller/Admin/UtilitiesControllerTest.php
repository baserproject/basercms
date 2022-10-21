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

use BaserCore\Controller\Admin\UtilitiesController;
use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\SitesService;
use BaserCore\Service\UtilitiesService;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use Cake\Datasource\ConnectionManager;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\TestSuite\IntegrationTestTrait;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Composer\Package\Archiver\ZipArchiver;

/**
 * class UtilitiesControllerTest
 * @package BaserCore\Controller\Admin\UtilitiesController;
 */
class UtilitiesControllerTest extends BcTestCase
{
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/Dblogs'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        ConnectionManager::alias('test', 'default');
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/baser-core/utilities/');
        $this->loginAdmin($request);
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test log_maintenance
     *
     * @return void
     */
    public function testLog_maintenance(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // ---- 引数 $mode が download の場合 start ----
        // ログが存在するテスト

        // TODO header を出力するためのエラーが発生するためコメントアウト 2022/10/04 ryuring
        // runInSeparateProcess アノテーションを利用する事で抑制できたが
        // 全体テストに切り替えると、なぜか、
        // plugins/bc-admin-third/templates/Admin/Users/index.php にて
        // 「Using $this when not in object context」というエラーが発生し、解決方法がわからず断念した
        // >>>
        // $this->get('/baser/admin/baser-core/utilities/log_maintenance/download');
        // ステータスを確認
        // $this->assertResponseOk();
        // <<<

        // ログが存在しないテスト
        $logsFolder = new Folder(LOGS);
        $backupPath = ROOT . DS . 'logsBackup' . DS;
        $logsFolder->copy($backupPath); // 念の為ログフォルダをバックアップする
        $logsFolder->delete();
        $this->get('/baser/admin/baser-core/utilities/log_maintenance/download');
        // ステータスを確認
        $this->assertResponseCode(302);
        // リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'log_maintenance'
        ]);
        // ログが存在しない場合のメッセージを確認
        $this->assertFlashMessage("エラーログが存在しません。");
        $backupFolder = new Folder($backupPath);
        $backupFolder->copy(LOGS); // ログフォルダのファイルを復元する
        $backupFolder->delete(); // バックアップフォルダを削除する
        // ---- 引数 $mode が download の場合 end ----

        // ---- 引数 $mode が delete の場合 start ----
        // 削除が成功のテスト
        $logPath = LOGS . 'error.log';
        if (!file_exists($logPath)) {
            new File($logPath, true);
        }
        $this->post('/baser/admin/baser-core/utilities/log_maintenance/delete');
        // ステータスを確認
        $this->assertResponseCode(302);
        // リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'log_maintenance'
        ]);
        // 削除が成功の場合のメッセージを確認
        $this->assertFlashMessage("エラーログを削除しました。");

        // 削除がエラーのテスト
        $this->post('/baser/admin/baser-core/utilities/log_maintenance/delete');
        // ステータスを確認
        $this->assertResponseCode(302);
        // リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'log_maintenance'
        ]);
        // エラーの場合のメッセージを確認
        $this->assertFlashMessage("エラーログが存在しません。");
        // ---- 引数 $mode が delete の場合 end ----
    }

    /**
     * test clear_cache
     *
     * @return void
     */
    public function testClear_cache(): void
    {

        $this->get('/baser/admin/baser-core/utilities/clear_cache');
        $this->assertResponseCode(302);
    }

    /**
     * test ajax_save_search_box
     *
     * @return void
     */
    public function testAjax_save_search_box(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test index
     *
     * @return void
     */
    public function testIndex(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->post('/baser/admin/baser-core/utilities/index/');
        $this->assertResponseOk();
    }

    /**
     * test reset_contents_tree
     *
     * @return void
     */
    public function testReset_contents_tree(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        ContentFactory::make(['id' => 1, 'name' => 'BaserCore root', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 1, 'rght' => 2])->persist();
        ContentFactory::make(['name' => 'BaserCore 1', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 11, 'rght' => 12])->persist();
        ContentFactory::make(['name' => 'BaserCore 2', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 13, 'rght' => 14])->persist();

        $this->post('/baser/admin/baser-core/utilities/reset_contents_tree/');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("コンテンツのツリー構造をリセットしました。");
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $utilitiesController = new UtilitiesController($this->getRequest());
        $this->assertEquals(['credit'], $utilitiesController->Authentication->getUnauthenticatedActions());
        $this->assertNotEmpty($utilitiesController->Authentication->getConfig('logoutRedirect'));
    }

    /**
     * test credit
     *
     * @return void
     */
    public function testCredit(): void
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->post('/baser/admin/baser-core/utilities/credit/');
        $this->assertResponseOk();
    }

    /**
     * test info
     */
    public function test_info()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/baser-core/utilities/info');
        $this->assertResponseOk();
    }

    /**
     * test reset_data
     */
    public function test_reset_data()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $site = SiteFactory::get(1);
        $siteService = new SitesService();
        $siteService->update($site, ['theme' => 'BcFront']);
        $this->post('/baser/admin/baser-core/utilities/reset_data/');
        // ステータスを確認
        $this->assertResponseCode(302);
        // リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'maintenance'
        ]);
    }

    /**
     * test info
     */
    public function test_verity_contents_tree()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // コンテンツのデータ作成
        ContentFactory::make([
            'id' => 1,
            'url' => '/',
            'name' => '',
            'plugin' =>
                'BaserCore',
            'type' =>
                'ContentFolder',
            'site_id' => 1,
            'parent_id' => null,
            'entity_id' => 1
        ])->persist();

        // コンテンツのツリー構造に問題がある場合
        $this->post('/baser/admin/baser-core/utilities/verity_contents_tree/');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("コンテンツのツリー構造に問題があります。ログを確認してください。");

        // コンテンツのツリー構造に問題がない場合
        $BcDatabaseService = new BcDatabaseService();
        $BcDatabaseService->truncate('contents');
        $this->post('/baser/admin/baser-core/utilities/verity_contents_tree/');
        $this->assertResponseCode(302);
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'index'
        ]);
        $this->assertFlashMessage("コンテンツのツリー構造に問題はありません。");
    }

    /**
     * Test maintenance
     */
    public function testMaintenance()
    {
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        // TODO header を出力するためのエラーが発生するためコメントアウト 2022/10/04 ryuring
        // runInSeparateProcess アノテーションを利用する事で抑制できたが
        // 全体テストに切り替えると、なぜか、
        // plugins/bc-admin-third/templates/Admin/Users/index.php にて
        // 「Using $this when not in object context」というエラーが発生し、解決方法がわからず断念した
        // >>>
        // backup のステータスを確認
//        $this->post('/baser/admin/baser-core/utilities/maintenance/backup');
//        $this->assertResponseOk();
        // <<<

        // restore の失敗ステータスを確認
        $this->post('/baser/admin/baser-core/utilities/maintenance/restore');
        $this->assertResponseCode(302);
        // restore の失敗リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'maintenance'
        ]);
        // restore の失敗メッセージを確認
        $this->assertFlashMessage('データの復元に失敗しました。ログの確認を行なって下さい。バックアップファイルが送信されませんでした。');
        // restore の成功ステータスを確認
        $zipSrcPath = TMP;
        $this->execPrivateMethod(new UtilitiesService(), '_writeBackup', [$zipSrcPath . 'schema', 'BaserCore', 'utf8']);
        $zip = new ZipArchiver();
        $testFile = $zipSrcPath . 'test.zip';
        $zip->archive($zipSrcPath . 'schema', $testFile, true);
        $this->setUploadFileToRequest('backup', $testFile);
        $this->setUnlockedFields(['backup']);
        $this->post('/baser/admin/baser-core/utilities/maintenance/restore');
        $this->assertResponseCode(302);
        // restore の成功リダイレクトを確認
        $this->assertRedirect([
            'plugin' => 'BaserCore',
            'prefix' => 'Admin',
            'controller' => 'utilities',
            'action' => 'maintenance'
        ]);
        // restore の成功メッセージを確認
        $this->assertFlashMessage('データの復元が完了しました。');
    }
}
