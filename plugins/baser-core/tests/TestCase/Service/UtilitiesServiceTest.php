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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Error\BcException;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Service\SitesService;
use BaserCore\Service\UtilitiesService;
use BaserCore\Service\UtilitiesServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use BaserCore\Utility\BcZip;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Composer\Package\Archiver\ZipArchiver;
use Laminas\Diactoros\UploadedFile;

/**
 * Class UtilitiesServiceTest
 * @property UtilitiesService $UtilitiesService
 */
class UtilitiesServiceTest extends BcTestCase
{
    /**
     * Trait
     */
    use BcContainerTrait;
    use IntegrationTestTrait;
    use ScenarioAwareTrait;

    /**
     * ログのパス
     * @var string
     */
    public $logPath = LOGS . 'error.log';

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UtilitiesService = $this->getService(UtilitiesServiceInterface::class);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UtilitiesService);
        parent::tearDown();

        if (!file_exists(LOGS)) {
            mkdir(LOGS, 0777);
        }
        if (!file_exists(LOGS . '.gitkeep')) {
            touch(LOGS . '.gitkeep');
        }
    }

    /**
     * test deleteLog
     * @return void
     */
    public function testDeleteLog()
    {
        if (!file_exists(LOGS)) {
            mkdir(LOGS, 0777);
        }
        if (!file_exists($this->logPath)) {
            file_put_contents($this->logPath, 'log txt.');
        }

        $this->UtilitiesService->deleteLog();
        $this->assertFalse(file_exists($this->logPath));

        rmdir(LOGS);
        $this->expectExceptionMessage('ログフォルダが存在しません。');
        $this->expectException(BcException::class);
        $this->UtilitiesService->deleteLog();
    }

    /**
     * test getMin
     * @return void
     */
    public function test_getMin()
    {
        ContentFactory::make(['id' => 100, 'name' => 'BaserCore 1', 'type' => 'ContentFolder', 'lft' => 5, 'rght' => 6])->persist();
        ContentFactory::make(['id' => 101, 'name' => 'BaserCore 2', 'type' => 'ContentFolder', 'lft' => 1, 'rght' => 4])->persist();
        ContentFactory::make(['id' => 102, 'name' => 'BaserCore 3', 'type' => 'ContentFolder', 'lft' => 7, 'rght' => 8])->persist();
        ContentFactory::make(['id' => 103, 'name' => 'BaserCore 4', 'type' => 'ContentFolder', 'lft' => 9, 'rght' => 10])->persist();
        ContentFactory::make(['id' => 104, 'name' => 'BaserCore 5', 'type' => 'ContentFolder', 'lft' => 2, 'rght' => 3, 'parent_id' => 101])->persist();

        $left = 'lft';
        $scope = '1 = 1';
        $rs = $this->execPrivateMethod($this->UtilitiesService, '_getMin', [new ContentsTable(), $scope, $left]);

        $this->assertEquals(1, $rs);
    }

    /**
     * test _getMax
     * @return void
     */
    public function test_getMax()
    {
        ContentFactory::make(['id' => 200, 'name' => 'BaserCore 6', 'type' => 'ContentFolder', 'lft' => 5, 'rght' => 6])->persist();
        ContentFactory::make(['id' => 201, 'name' => 'BaserCore 7', 'type' => 'ContentFolder', 'lft' => 1, 'rght' => 4])->persist();
        ContentFactory::make(['id' => 202, 'name' => 'BaserCore 8', 'type' => 'ContentFolder', 'lft' => 7, 'rght' => 8])->persist();
        ContentFactory::make(['id' => 203, 'name' => 'BaserCore 9', 'type' => 'ContentFolder', 'lft' => 9, 'rght' => 12])->persist();
        ContentFactory::make(['id' => 204, 'name' => 'BaserCore 10', 'type' => 'ContentFolder', 'lft' => 2, 'rght' => 3, 'parent_id' => 201])->persist();
        ContentFactory::make(['id' => 206, 'name' => 'BaserCore 11', 'type' => 'ContentFolder', 'lft' => 10, 'rght' => 11, 'parent_id' => 203])->persist();


        $right = 'rght';
        $scope = '1 = 1';
        $rs = $this->execPrivateMethod($this->UtilitiesService, '_getMax', [new ContentsTable(), $scope, $right]);

        $this->assertEquals(12, $rs);
    }

    /**
     * test verityContentsTree
     *
     * @param $dbSample
     * @param $expect
     * @param $logDataExpect
     *
     * @dataProvider verityContentsTreeProvider
     */
    public function test_verityContentsTree($dbSample, $expect, $logDataExpect)
    {
        $logPath = LOGS . 'cli-error.log';
        if (!file_exists(LOGS)) {
            mkdir(LOGS, 0777);
        }
        if (file_exists($logPath)) {
            unlink($logPath);
        }

        foreach($dbSample as $item) {
            ContentFactory::make($item)->persist();
        }

        $rs = $this->UtilitiesService->verityContentsTree();
        $this->assertEquals($rs, $expect);

        if (file_exists($logPath)) {
            $file = new File($logPath);
            $logData = $file->read();
            $this->assertStringContainsString($logDataExpect, $logData);
        }

    }

    public function verityContentsTreeProvider()
    {
        return
            [
                //成功
                [
                    [
                        ['id' => 300, 'name' => 'BaserCore 6', 'type' => 'ContentFolder', 'lft' => 1, 'rght' => 2],
                        ['id' => 301, 'name' => 'BaserCore 7', 'type' => 'ContentFolder', 'lft' => 3, 'rght' => 4],
                    ],
                    true,
                    false
                ],
                //失敗
                [
                    [
                        ['id' => 300, 'name' => 'BaserCore 6', 'type' => 'ContentFolder', 'lft' => 1, 'rght' => 2],
                        ['id' => 301, 'name' => 'BaserCore 7', 'type' => 'ContentFolder', 'lft' => 1, 'rght' => 2],
                    ],
                    false,
                    'error: index, 1, duplicate'
                ],
            ];
    }

    /**
     * test _verify
     * @param $dbSample
     * @param $expect
     *
     * @dataProvider _verifyProvider
     */
    public function test_verify($dbSample, $expect)
    {
        foreach($dbSample as $item) {
            ContentFactory::make($item)->persist();
        }

        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $rs = $this->execPrivateMethod($this->UtilitiesService, '_verify', [$contentsTable]);
        $this->assertEquals($rs, $expect);

    }

    public function _verifyProvider()
    {

        return
            [
                //成功
                [
                    [
                        ['id' => 300, 'name' => 'BaserCore 6', 'type' => 'ContentFolder', 'lft' => 5, 'rght' => 6],
                        ['id' => 301, 'name' => 'BaserCore 7', 'type' => 'ContentFolder', 'lft' => 1, 'rght' => 4],
                        ['id' => 302, 'name' => 'BaserCore 8', 'type' => 'ContentFolder', 'lft' => 7, 'rght' => 8],
                        ['id' => 303, 'name' => 'BaserCore 9', 'type' => 'ContentFolder', 'lft' => 9, 'rght' => 12],
                        ['id' => 304, 'name' => 'BaserCore 10', 'type' => 'ContentFolder', 'lft' => 2, 'rght' => 3, 'parent_id' => 301],
                        ['id' => 306, 'name' => 'BaserCore 11', 'type' => 'ContentFolder', 'lft' => 10, 'rght' => 11, 'parent_id' => 303],
                    ],
                    true
                ],
                //parent_id が設定されているのに子の lft と rght になっていない
                [
                    [
                        ['id' => 300, 'name' => 'BaserCore 6', 'type' => 'ContentFolder', 'lft' => 5, 'rght' => 6],
                        ['id' => 301, 'name' => 'BaserCore 7', 'type' => 'ContentFolder', 'lft' => 1, 'rght' => 4],
                        ['id' => 302, 'name' => 'BaserCore 8', 'type' => 'ContentFolder', 'lft' => 7, 'rght' => 8],
                        ['id' => 303, 'name' => 'BaserCore 9', 'type' => 'ContentFolder', 'lft' => 9, 'rght' => 12],
                        ['id' => 304, 'name' => 'BaserCore 10', 'type' => 'ContentFolder', 'lft' => 2, 'rght' => 3, 'parent_id' => 301],
                        ['id' => 306, 'name' => 'BaserCore 11', 'type' => 'ContentFolder', 'lft' => 10, 'rght' => 11, 'parent_id' => 303],
                        ['id' => 307, 'name' => 'BaserCore 12', 'type' => 'ContentFolder', 'lft' => 13, 'rght' => 14, 'parent_id' => 303]
                    ],
                    [0 => [0 => "node", 1 => 307, 2 => "right greater than parent (node 303)."]]
                ],
                //同じレコードで lft と rght が同じ
                [
                    [
                        ['id' => 300, 'name' => 'BaserCore 6', 'type' => 'ContentFolder', 'lft' => 5, 'rght' => 5],
                    ],
                    [0 => [0 => "node", 1 => 300, 2 => "left and right values identical"]]
                ],
                //連番になっていない
                [
                    [
                        ['id' => 300, 'name' => 'BaserCore 6', 'type' => 'ContentFolder', 'lft' => 5, 'rght' => 6],
                        ['id' => 301, 'name' => 'BaserCore 6', 'type' => 'ContentFolder', 'lft' => 1, 'rght' => 2],
                    ],
                    [
                        0 => [0 => "index", 1 => 3, 2 => "missing"],
                        1 => [0 => "index", 1 => 4, 2 => "missing"],
                    ]
                ],
            ];
    }

    /**
     * test resetContentsTree
     * @return void
     */
    public function test_resetContentsTree()
    {
        ContentFactory::make(['id' => 1, 'name' => 'BaserCore root', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 1, 'rght' => 2])->persist();
        ContentFactory::make(['name' => 'BaserCore 1', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 11, 'rght' => 12])->persist();
        ContentFactory::make(['name' => 'BaserCore 2', 'type' => 'ContentFolder', 'site_root' => 1, 'lft' => 13, 'rght' => 14])->persist();

        $rs = $this->UtilitiesService->resetContentsTree();
        $this->assertTrue($rs);
    }

    /**
     * test getCredit
     * @return void
     */
    public function test_getCredit()
    {
        Configure::write('debug', false);
        Cache::write('specialThanks', '', '_bc_env_');
        $rs = $this->UtilitiesService->getCredit();

        //戻り値を確認
        $this->assertEquals("中村 美鈴", $rs->designers[0]->name);
        $this->assertEquals("滝下 真玄", $rs->developers[0]->name);
        $this->assertEquals("本間 忍", $rs->supporters[0]->name);
        $this->assertEquals("オガワ", $rs->publishers[0]->name);

        //Configure debug を false に設定してキャッシュの保存を確認
        $specialThanks = Cache::read('specialThanks', '_bc_env_');
        $this->assertEquals($specialThanks, json_encode($rs));
    }

    /**
     * test createLogZip
     * @return void
     */
    public function test_createLogZip()
    {
        $rs = $this->UtilitiesService->createLogZip();
        $this->assertTrue(file_exists($rs));
    }

    /**
     * test backupDb
     * @return void
     */
    public function test_backupDb()
    {
        $rs = $this->UtilitiesService->backupDb('utf8');
        $this->assertTrue(file_exists($rs));
    }

    /**
     * test resetTmpSchemaFolder
     * @return void
     */
    public function test_resetTmpSchemaFolder()
    {
        $this->UtilitiesService->backupDb('utf8');
        $this->UtilitiesService->resetTmpSchemaFolder();
        $tmpDir = TMP . 'schema' . DS;
        $Folder = new Folder($tmpDir);
        $files = $Folder->read(true, true, false);
        $this->assertEquals(0, count($files[0]));
        $this->assertEquals(0, count($files[1]));
    }

    /**
     * test _writeBackup
     * @return void
     */
    public function test_writeBackup()
    {
        $this->UtilitiesService->resetTmpSchemaFolder();

        $zipSrcPath = TMP . 'schema' . DS;
        $this->execPrivateMethod(new UtilitiesService(), '_writeBackup', [$zipSrcPath, 'BaserCore', 'utf8']);

        $this->assertTrue(file_exists($zipSrcPath . 'PermissionsSchema.php'));
        $this->assertTrue(file_exists($zipSrcPath . 'pages.csv'));

        //不要ファイルを削除
        $this->UtilitiesService->resetTmpSchemaFolder();
    }

    /**
     * test restoreDb
     * @return void
     */
    public function test_restoreDb()
    {
        $this->markTestIncomplete('このテストを利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        $this->loadFixtureScenario(InitAppScenario::class);
        // バックアップファイルを作成してアップロード
        $zipSrcPath = TMP;
        $this->execPrivateMethod(new UtilitiesService(), '_writeBackup', [$zipSrcPath . 'schema/', 'BaserCore', 'utf8']);
        $zip = new ZipArchiver();
        $testFile = $zipSrcPath . 'test.zip';
        $zip->archive($zipSrcPath . 'schema', $testFile, true);
        $this->setUploadFileToRequest('backup', $testFile);
        $file = new UploadedFile(
            $testFile,
            filesize($testFile),
            UPLOAD_ERR_OK,
            'test.zip',
            BcUtil::getContentType($testFile)
        );
        $this->UtilitiesService->restoreDb(['encoding' => 'utf8'], ['backup' => $file]);

        // テーブルが作成されデータが作成されている事を確認
        $list = $this->getTableLocator()->get('BaserCore.App')->getConnection()->getSchemaCollection()->listTables();
        $this->assertContains('sites', $list);
        $this->assertEquals(1, SiteFactory::count());
    }

    /**
     * test _loadBackup
     * @return void
     */
    public function test_loadBackup()
    {
        $this->markTestIncomplete('loadFixtures を利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        // データを作成
        $this->loadFixtureScenario(InitAppScenario::class);
        // バックアップを作成し、展開
        $zipPath = $this->UtilitiesService->backupDb('utf8');
        $tmpDir = TMP . 'schema' . DS;
        $bcZip = new BcZip();
        $bcZip->extract($zipPath, $tmpDir);

        // データを削除
        $this->truncateTable('users');
        $this->truncateTable('sites');
        // 実行
        $this->execPrivateMethod($this->UtilitiesService, '_loadBackup', [$tmpDir, 'utf8']);
        $this->UtilitiesService->resetTmpSchemaFolder();

        // データが復元されているか確認
        $this->assertEquals(1, UserFactory::count());
        $this->assertEquals(1, SiteFactory::count());
    }

    /**
     * test resetData
     * @return void
     */
    public function test_resetData()
    {
//        $this->markTestIncomplete('loadFixtures を利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        SiteFactory::make(['id' => 100, 'title' => 'test title', 'display_name' => 'test display_name', 'theme' => 'BcPluginSample'])->persist();
        SiteFactory::make(['id' => 101, 'title' => 'test title　101', 'display_name' => 'test display_name　101', 'theme' => 'BcPluginSample101'])->persist();
        $this->getRequest();

        $rs = $this->UtilitiesService->resetData();
        $this->assertTrue($rs);

        $siteService = new SitesService();
        $site = $siteService->getIndex([])->toArray();
        $this->assertCount(1, $site);
        $this->assertEquals('BcPluginSample', $site[0]->theme);
        $this->assertEquals('メインサイト', $site[0]->title);
        $this->assertEquals('メインサイト', $site[0]->display_name);
    }

}
