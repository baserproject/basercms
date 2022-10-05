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

use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Test\Factory\ContentFolderFactory;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SearchIndexesFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Factory\UserGroupFactory;
use BaserCore\Test\Factory\UsersUserGroupFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Filesystem\Folder;
use Cake\ORM\TableRegistry;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\Filesystem\File;
use Cake\Utility\Inflector;

/**
 * BcDatabaseServiceTest
 * @property BcDatabaseService $BcDatabaseService
 */
class BcDatabaseServiceTest extends BcTestCase
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
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Pages',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/SearchIndexes',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BcDatabaseService = $this->getService(BcDatabaseServiceInterface::class);
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
     * Gets the database encoding
     * @return void
     */
    public function test_getEncoding()
    {
        $encoding = $this->BcDatabaseService->getEncoding();
        $this->assertEquals('utf8', $encoding);
    }

    /**
     * Gets the database encoding
     * @return void
     */
    public function test_truncate()
    {
        SiteConfigFactory::make(['name' => 'company', 'value' => 'Company A'])->persist();
        SiteConfigFactory::make(['name' => 'address', 'value' => 'Tokyo'])->persist();
        $this->assertEquals(2, SiteConfigFactory::count());
        $this->BcDatabaseService->truncate('site_configs');
        $this->assertEquals(0, SiteConfigFactory::count());
    }

    /**
     * test resetTables
     */
    public function test_resetTables()
    {
        $plugin = 'BaserCore';
        $excludes = ['site_configs', 'sites'];
        SiteConfigFactory::make(['name' => 'test', 'value' => 'test value'])->persist();
        SiteFactory::make(['name' => 'home page', 'title' => 'welcome'])->persist();
        PageFactory::make(['contents' => 'this is the contents', 'draft' => 'trash'])->persist();
        UserFactory::make(['name' => 'Chuong Le', 'email' => 'chuong.le@mediabridge.asia'])->persist();
        $this->BcDatabaseService->resetTables($plugin, $excludes);
        $this->assertEquals(1, SiteConfigFactory::count());
        $this->assertEquals(1, SiteFactory::count());
        $this->assertEquals(0, PageFactory::count());
        $this->assertEquals(0, UserFactory::count());
    }

    /**
     * test loadCsv
     */
    public function test_loadCsv()
    {
        // csvフォルダーを作成する
        $csvFolder = TMP . 'csv' . DS;
        if (!is_dir($csvFolder)) {
            new Folder($csvFolder, true, 0777);
        }
        // csvファイルを作成する
        $table = 'pages';
        $csvFilePath = $csvFolder . $table . '.csv';
        $csvContents = [
            'head' => ['id', 'contents', 'draft', 'page_template', 'modified', 'created'],
            'row1' => ['id' => 1, 'contents' => 'content 1', 'draft' => 'draft 1', 'page_template' => 'temp 1', '', 'created' => '2022-09-15 18:00:00'],
            'row2' => ['id' => 2, 'contents' => 'content 2', 'draft' => 'draft 2', 'page_template' => 'temp 2', '', 'created' => ''],
        ];
        $fp = fopen($csvFilePath, 'w');
        ftruncate($fp, 0);
        foreach ($csvContents as $row) {
            $csvRecord = implode(',', $row) . "\n";
            fwrite($fp, $csvRecord);
        }
        fclose($fp);
        // CSVファイルをDBに読み込む
        $this->BcDatabaseService->loadCsv(['path' => $csvFilePath, 'encoding' => 'UTF-8']);
        // 複数のレコードが読み込まれいている事を確認
        $this->assertEquals(2, PageFactory::count());
        // 反映したデータが正しい事を確認
        $row1 = PageFactory::get(1);
        $this->assertEquals($row1->contents, $csvContents['row1']['contents']);
        $this->assertEquals($row1->created->format('Y-m-d H:i:s'), $csvContents['row1']['created']);
        // createdが空の時に本日の日付が入っている事を確認
        $row2 = PageFactory::get(2);
        $this->assertEquals($row2->created->format('Y-m-d H:i'), date('Y-m-d H:i'));
    }

    /**
     * test loadCsvToArray
     * @return void
     */
    public function test_loadCsvToArray()
    {
        ContentFactory::make(
            [
                'name' => 'BaserCore',
                'type' => 'ContentFolder',
                'entity_id' => 1,
                'title' => 'メインサイト',
                'lft' => 1,
                'right' => 18,
                'level' => 0
            ]
        )->persist();
        $path = TMP . DS . 'contents.csv';
        $options = [
            'path' => $path,
            'encoding' => 'utf8',
            'init' => false,
        ];
        $this->BcDatabaseService->writeCsv('contents', $options);
        //CSVファイルを指定して実行
        $rs = $this->BcDatabaseService->loadCsvToArray($path);
        //戻り値が配列になっていることを確認
        $this->assertIsArray($rs);
        $this->assertEquals('メインサイト', $rs[0]['title']);

        //SJIS のCSVファイルを作成
        $options = [
            'path' => $path,
            'encoding' => 'sjis',
            'init' => false,
        ];
        $this->BcDatabaseService->writeCsv('contents', $options);
        $rs = $this->BcDatabaseService->loadCsvToArray($path);
        $this->assertIsArray($rs);
        // 戻り値の配列の値のエンコードがUTF-8になっている事を確認
        $this->assertEquals('メインサイト', $rs[0]['title']);
        $this->assertTrue(mb_check_encoding($rs[0]['title'], 'UTF-8'));

        $file = new File($path);
        $file->delete();
    }

    /**
     * test initSystemData
     */
    public function test_initSystemData()
    {
        $options = [
            'excludeUsers' => true,
            'email' => 'chuong.le@mediabridge.asia',
            'google_analytics_id' => 'testID',
            'first_access' => '2022-09-21',
            'version' => '1.0.0',
            'theme' => 'BcFront',
            'adminTheme' => 'BcSpaSample'
        ];
        // siteデータを作成する
        SiteFactory::make(['id' => '1', 'theme' => 'BcSpaSample'])->persist();
        // userデータを作成する
        UserFactory::make(['name' => 'C. Le'])->persist();

        $result1 = $this->BcDatabaseService->initSystemData($options);

        // user_groups　テーブルにデータが登録されている事を確認
        $userGroupTable = TableRegistry::getTableLocator()->get('BaserCore.UserGroups');
        $this->assertTrue($userGroupTable->find()->where(['UserGroups.name' => 'admins'])->count() > 0);
        // users_user_groups　テーブルにデータが登録されている事を確認
        $corePath = BcUtil::getPluginPath(Inflector::camelize(Configure::read('BcApp.defaultFrontTheme'), '-')) . 'config' . DS . 'data' . DS . 'default' . DS . 'BaserCore';
        $usersUserGroups = $this->BcDatabaseService->loadCsvToArray($corePath . DS . 'users_user_groups.csv');
        $this->assertCount(UsersUserGroupFactory::count(), $usersUserGroups);
        // site_configs テーブルの email / google_analytics_id / first_access / admin_theme / version の設定状況を確認
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $this->assertEquals($siteConfigsService->getValue('email'), $options['email']);
        $this->assertEquals($siteConfigsService->getValue('google_analytics_id'), $options['google_analytics_id']);
        $this->assertEquals($siteConfigsService->getValue('first_access'), $options['first_access']);
        $this->assertEquals($siteConfigsService->getValue('admin_theme'), $options['adminTheme']);
        $this->assertEquals($siteConfigsService->getValue('version'), $options['version']);
        // sites テーブルの theme の設定状況を確認
        $this->assertEquals($options['theme'], SiteFactory::get(1)->theme);
        // excludeUsers(true) オプションの動作を確認
        $this->assertEquals(1, UserFactory::count());
        // excludeUsers(false) オプションの動作を確認
        $options['excludeUsers'] = false;
        $result2 = $this->BcDatabaseService->initSystemData($options);
        $this->assertEquals(0, UserFactory::count());
        // 戻り値を確認
        $this->assertTrue($result1 && $result2);
    }

    /**
     * test resetAllTables
     */
    public function test_resetAllTables()
    {
        $excludes = ['site_configs', 'sites'];
        SiteConfigFactory::make(['name' => 'test', 'value' => 'test value'])->persist();
        SiteFactory::make(['name' => 'home page', 'title' => 'welcome'])->persist();
        PageFactory::make(['contents' => 'this is the contents', 'draft' => 'trash'])->persist();
        UserFactory::make(['name' => 'Chuong Le', 'email' => 'chuong.le@mediabridge.asia'])->persist();
        UserGroupFactory::make(['name' => 'test group', 'title' => 'test title'])->persist();
        ContentFolderFactory::make(['folder_template' => 'temp1', 'page_template' => 'temp2'])->persist();
        SearchIndexesFactory::make(['type' => 'test type', 'model' => 'test model'])->persist();

        $this->BcDatabaseService->resetAllTables($excludes);
        $this->assertEquals(1, SiteConfigFactory::count());
        $this->assertEquals(1, SiteFactory::count());
        $this->assertEquals(0, PageFactory::count());
        $this->assertEquals(0, UserFactory::count());
        $this->assertEquals(0, UserGroupFactory::count());
        $this->assertEquals(0, ContentFolderFactory::count());
        $this->assertEquals(0, SearchIndexesFactory::count());
    }

    /**
     * test _loadDefaultDataPattern
     */
    public function test_loadDefaultDataPattern()
    {
        $theme = 'BcFront';
        $plugin = 'BaserCore';
        $patterns = ['default', 'empty'];
        $tableList = $this->BcDatabaseService->getAppTableList($plugin);
        foreach ($patterns as $pattern) {
            $this->execPrivateMethod($this->BcDatabaseService, '_loadDefaultDataPattern', [$pattern, $theme]);
            $path = BcUtil::getDefaultDataPath($theme, $pattern);
            $this->assertNotNull($path);
            $Folder = new Folder($path . DS . $plugin);
            $files = $Folder->read(true, true, true);
            $csvList = $files[1];
            foreach ($csvList as $path) {
                $table = basename($path, '.csv');
                if (!in_array($table, $tableList)) continue;
                $records = $this->BcDatabaseService->loadCsvToArray($path);
                $appTable = TableRegistry::getTableLocator()->get('BaserCore.App');
                $schema = $appTable->getConnection()->getSchemaCollection()->describe($table);
                $appTable->setTable($table);
                $appTable->setSchema($schema);
                $this->assertCount($appTable->find()->count(), $records);
            }
            $this->BcDatabaseService->resetTables($plugin);
        }
    }

    /**
     * test getAppTableList
     */
    public function test_getAppTableList()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        Cache::delete('appTableList', '_bc_env_');
        $result = $this->BcDatabase->getAppTableList();
        $this->assertTrue(in_array('plugins', $result['BaserCore']));
        $this->assertTrue(in_array('plugins', Cache::read('appTableList', '_bc_env_')['BaserCore']));
    }

    /**
     * test _convertFieldToCsv
     * @param $value
     * @param $expected
     * @dataProvider convertFieldToCsvDataProvider
     */
    public function test_convertFieldToCsv($value, $expected)
    {
        $rs = $this->execPrivateMethod($this->BcDatabaseService, '_convertFieldToCsv', [$value]);
        $this->assertEquals($expected, $rs);
    }

    public function convertFieldToCsvDataProvider()
    {
        return [
            ['test', '"test"'],
            ['test{CM}testCM', '"test,testCM"'],
            ['test\\testCM', '"test\testCM"'],
        ];
    }

    /**
     * test clearAppTableList
     * @return void
     */
    public function test_clearAppTableList()
    {
        $this->BcDatabaseService->getAppTableList();
        $this->assertTrue(in_array('plugins', Cache::read('appTableList', '_bc_env_')['BaserCore']));
        $this->BcDatabaseService->clearAppTableList();
        $this->assertEquals(0, count(Cache::read('appTableList', '_bc_env_')));
    }

    /**
     * test writeCsv
     * @return void
     */
    public function test_writeCsv()
    {
        ContentFactory::make(
            [
                'name' => 'BaserCore',
                'type' => 'ContentFolder',
                'entity_id' => 1,
                'title' => 'メインサイト',
                'lft' => 1,
                'right' => 18,
                'level' => 0
            ]
        )->persist();
        $path = TMP . DS . 'contents.csv';
        $options = [
            'path' => $path,
            'encoding' => 'sjis',
            'init' => false,
        ];

        $this->BcDatabaseService->writeCsv('contents', $options);

        //テーブルを指定していCSVファイルの書き出しを確認
        $this->assertTrue(file_exists($path));

        // encoding オプションを SJIS にし、書き出しファイルのエンコードを確認
        $this->assertTrue(mb_check_encoding(file_get_contents($path), 'SJIS'));

        // init オプションを指定しない場合、id, modified, created が空になっていないことを確認
        $rs = $this->BcDatabaseService->loadCsvToArray($path);
        $this->assertIsArray($rs);
        $this->assertNotEquals('', $rs[0]['id']);
        $this->assertNotEquals('', $rs[0]['modified']);
        $this->assertNotEquals('', $rs[0]['created']);

        // init オプションを指定して、id, modified, created が空になっていることを確認
        $options = [
            'path' => $path,
            'encoding' => 'sjis',
            'init' => true,
        ];
        $this->BcDatabaseService->writeCsv('contents', $options);

        $this->assertTrue(file_exists($path));
        $rs = $this->BcDatabaseService->loadCsvToArray($path);
        $this->assertEquals('', $rs[0]['id']);
        $this->assertEquals('', $rs[0]['modified']);
        $this->assertEquals('', $rs[0]['created']);

        $file = new File($path);
        $file->delete();
    }
    /**
     * test _dbEncToPhp
     * @param $value
     * @param $expected
     * @dataProvider dbEncToPhpDataProvider
     */
    public function test_dbEncToPhp($value, $expected)
    {
        $rs = $this->execPrivateMethod($this->BcDatabaseService, '_dbEncToPhp', [$value]);
        $this->assertEquals($expected, $rs);
    }

    public function dbEncToPhpDataProvider()
    {
        return [
            ['utf8', 'UTF-8'],
            ['sjis', 'SJIS'],
            ['ujis', 'EUC-JP'],
        ];
    }
    /**
     * test _convertRecordToCsv
     * @return void
     */
    public function test_convertRecordToCsv()
    {
        $record = ['type' => 'test type', 'model' => 'test model'];
        $rs = $this->execPrivateMethod($this->BcDatabaseService, '_convertRecordToCsv', [$record]);
        $this->assertEquals('"test type"', $rs['type']);
        $this->assertEquals('"test model"', $rs['model']);
    }
    /**
     * test _phpEncToDb
     * @param $value
     * @param $expected
     * @dataProvider phpEncToDbDataProvider
     */
    public function test_phpEncToDb($value, $expected)
    {
        $rs = $this->execPrivateMethod($this->BcDatabaseService, '_phpEncToDb', [$value]);
        $this->assertEquals($expected, $rs);
    }

    public function phpEncToDbDataProvider()
    {
        return [
            ['UTF-8', 'utf8'],
            ['SJIS', 'sjis'],
            ['EUC-JP', 'ujis']
        ];
    }

    /**
     * Test loadSchema
     */
    public function test_loadSchema()
    {
        $path = TMP . 'schema' . DS;
        $fileName = 'UserActionsSchema.php';
        $schemaFile = new File($path . $fileName, true);
        $table = 'user_actions';
        // スキーマファイルを生成
        $schemaFile->write("<?php
use BaserCore\Database\Schema\BcSchema;
class UserActionsSchema extends BcSchema
{
    public \$table = '$table';
    public \$fields = [
        'id' => ['type' => 'integer', 'autoIncrement' => true],
        'contents' => ['type' => 'text', 'length' => 100],
        '_constraints' => [
            'primary' => ['type' => 'primary', 'columns' => ['id'], 'length' => []],
        ],
        '_options' => [
            'engine' => 'InnoDB',
            'collation' => 'utf8_general_ci'
        ]
    ];
}");
        // Create処理実行
        $this->BcDatabaseService->loadSchema(['type' => 'create', 'path' => $path, 'file' => $fileName]);
        $tableList = $this->getTableLocator()
            ->get('BaserCore.App')
            ->getConnection()
            ->getSchemaCollection()
            ->listTables();
        $this->assertContains($table, $tableList);
        // Drop処理実行
        $this->BcDatabaseService->loadSchema(['type' => 'drop', 'path' => $path, 'file' => $fileName]);
        $tableList = $this->getTableLocator()
            ->get('BaserCore.App')
            ->getConnection()
            ->getSchemaCollection()
            ->listTables();
        $this->assertNotContains($table, $tableList);
        // スキーマファイルを削除
        $schemaFile->delete();
    }

    /**
     * Test writeSchema
     */
    public function test_writeSchema()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
