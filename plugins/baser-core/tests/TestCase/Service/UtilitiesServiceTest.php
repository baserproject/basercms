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
use BaserCore\Service\UtilitiesService;
use BaserCore\Service\UtilitiesServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Filesystem\File;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class UtilitiesServiceTest
 * @package BaserCore\Test\TestCase\Service
 * @property UtilitiesService $UtilitiesService
 */
class UtilitiesServiceTest extends BcTestCase
{
    /**
     * Trait
     */
    use BcContainerTrait;
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
    ];

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
        $this->setFixtureTruncate();
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
    }

    /**
     * test deleteLog
     * @return void
     */
    public function testDeleteLog()
    {
        if (!file_exists($this->logPath)) {
            new File($this->logPath, true);
        }

        $this->UtilitiesService->deleteLog();
        $this->assertFalse(file_exists($this->logPath));

        $this->expectExceptionMessage('エラーログが存在しません。');
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
    public function test_getMax(){
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
        if (file_exists($logPath)) {
            unlink($logPath);
        }

        foreach ($dbSample as $item) {
            ContentFactory::make($item)->persist();
        }

        $rs = $this->UtilitiesService->verityContentsTree();
        $this->assertEquals($rs, $expect);

        $file = new File($logPath);
        $logData = $file->read();
        $this->assertStringContainsString($logDataExpect, $logData);
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
        foreach ($dbSample as $item){
            ContentFactory::make($item)->persist();
        }

        $contentsTable = TableRegistry::getTableLocator()->get('BaserCore.Contents');
        $rs = $this->execPrivateMethod($this->UtilitiesService, '_verify', [$contentsTable]);
        $this->assertEquals($rs,$expect);

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
        $this->assertTrue(is_array($rs->centralDirectory));
    }

    /**
     * test backupDb
     * @return void
     */
    public function test_backupDb()
    {
        $rs = $this->UtilitiesService->backupDb('utf8');
        $this->assertTrue(is_array($rs->centralDirectory));
    }

    /**
     * test resetTmpSchemaFolder
     * @return void
     */
    public function test_resetTmpSchemaFolder(){
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test _writeBackup
     * @return void
     */
    public function test_writeBackup(){
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test restoreDb
     * @return void
     */
    public function test_restoreDb(){
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test _loadBackup
     * @return void
     */
    public function test_loadBackup(){
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test resetData
     * @return void
     */
    public function test_resetData(){
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
