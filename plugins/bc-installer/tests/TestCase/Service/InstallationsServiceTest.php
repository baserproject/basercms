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

namespace BcInstaller\Test\TestCase\Service;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\ContentFolderFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BcInstaller\Service\InstallationsService;
use BcInstaller\Service\InstallationsServiceInterface;
use Cake\Core\Configure;
use Cake\ORM\Exception\PersistenceFailedException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * InstallationsServiceTest
 * @property InstallationsService $Installations
 */
class InstallationsServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * setup
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Installations = $this->getService(InstallationsServiceInterface::class);
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->Installations);
    }

    /**
     * test constructor
     */
    public function test__construct()
    {
        $this->assertNotEmpty($this->Installations->BcDatabase);
    }

    /**
     * 環境チェック
     * test checkEnv
     *
     */
    public function testCheckEnv()
    {
        Configure::write([
                'BcRequire' => [
                    'phpVersion' => "8.0.0",
                    'phpMemory' => "128",
                ]
            ]
        );
        $result = $this->Installations->checkEnv();
        $this->assertEquals('/var/www/html/config',$result['configDir']);
        $this->assertEquals('/var/www/html/webroot/files',$result['filesDir']);
        $this->assertEquals('/var/www/html/plugins',$result['pluginDir']);
        $this->assertEquals('/var/www/html/tmp/',$result['tmpDir']);
        $this->assertEquals('/var/www/html/db',$result['dbDir']);
        $this->assertEquals('8.0.0',$result['requirePhpVersion']);
        $this->assertEquals('128',$result['requirePhpMemory']);
        $this->assertEquals('UTF-8',$result['encoding']);
        $this->assertEquals('8.1.5',$result['phpVersion']);
        $this->assertEquals('-1',$result['phpMemory']);
        $this->assertTrue($result['safeModeOff']);
        $this->assertTrue($result['configDirWritable']);
        $this->assertTrue($result['pluginDirWritable']);
        $this->assertTrue($result['filesDirWritable']);
        $this->assertTrue($result['tmpDirWritable']);
        $this->assertTrue($result['dbDirWritable']);
        $this->assertEquals('8.1.5',$result['phpActualVersion']);
        $this->assertTrue($result['phpGd']);
        $this->assertTrue($result['phpPdo']);
        $this->assertTrue($result['phpXml']);
        $this->assertTrue($result['phpZip']);
        $this->assertEquals('-1',$result['apacheRewrite']);
        $this->assertTrue($result['encodingOk']);
        $this->assertTrue($result['gdOk']);
        $this->assertTrue($result['pdoOk']);
        $this->assertTrue($result['xmlOk']);
        $this->assertTrue($result['zipOk']);
        $this->assertTrue($result['phpVersionOk']);
        $this->assertTrue($result['phpMemoryOk']);
        $this->assertTrue($result['blRequirementsMet']);
    }

    /**
     * test _getMemoryLimit
     */
    public function test_getMemoryLimit()
    {
        $size = ini_get('memory_limit');
        ini_set('memory_limit', '1024M');
        $this->assertEquals(1024, $this->execPrivateMethod($this->Installations, '_getMemoryLimit'));

        ini_set('memory_limit', '1024m');
        $this->assertEquals(1024, $this->execPrivateMethod($this->Installations, '_getMemoryLimit'));

        ini_set('memory_limit', '1g');
        $this->assertEquals(1024, $this->execPrivateMethod($this->Installations, '_getMemoryLimit'));

        ini_set('memory_limit', '1G');
        $this->assertEquals(1024, $this->execPrivateMethod($this->Installations, '_getMemoryLimit'));

        //元に戻る
        ini_set('memory_limit', $size);
    }

    /**
     * test constructionDb
     */
    public function testConstructionDb()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getRealDbName
     * @param string $type
     * @param string $dbName
     * @param string $expected
     * @dataProvider getRealDbNameDataProvider
     */
    public function testGetRealDbName($type, $dbName, $expected)
    {
        $result = $this->Installations->getRealDbName($type, $dbName);
        $this->assertEquals($expected, $result);
    }

    public static function getRealDbNameDataProvider()
    {
        $path = ROOT . DS . 'db' . DS . 'sqlite' . DS;
        return [
            ['mysql', '/var/db/mydatabase', '/var/db/mydatabase'],
            ['sqlite', 'mydatabase', $path . 'mydatabase.db'],
            ['mysql', 'mydatabase', 'mydatabase'],
            ['sqlite', '', ''],
            ['', 'mydatabase', 'mydatabase'],
        ];
    }

    /**
     * test testConnectDb
     */
    public function testTestConnectDb()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test setAdminEmail
     */
    public function testSetAdminEmailAndVersion()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test addDefaultUser
     */
    public function testAddDefaultUser()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/'));

        //data
        $userData = [
            'name' => 'testuser',
            'email' => 'testuser@example.com',
            'password_1' => 'Password1234',
            'password_2' => 'Password1234'
        ];

        $result = $this->Installations->addDefaultUser($userData);
        $this->assertEquals('testuser', $result['name']);
        $this->assertEquals('testuser@example.com', $result['email']);
        $this->assertEquals('testuser', $result['real_name_1']);
        $this->assertCount(1, $result['user_groups']);
    }

    public function testAddDefaultUserThrowsException()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/'));

        $userData = [
            'email' => 'testuser@example.com',
            'password_1' => 'password123',
            'password_2' => 'differentpassword'
        ];

        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (password.minLength: "パスワードは12文字以上で入力してください。", password.passwordConfirm: "パスワードが同じものではありません。", password.passwordRequiredCharacterType: "パスワード');
        $this->Installations->addDefaultUser($userData);
    }

    /**
     * test setSiteName
     */
    public function testSetSiteName()
    {
        SiteFactory::make(['id' => '1'])->persist();
        ContentFactory::make([
                'id' => 1,
                'type' => 'ContentFolder',
                'entity_id' => 1,
                'parent_id' => 0,
                'rght' => 48,
                'site_root' => true
            ])->persist();
        ContentFolderFactory::make(['id' => '1'])->persist();

        $siteName = 'testSiteName';
        $result = $this->Installations->setSiteName($siteName);

        $this->assertEquals($siteName, $result['title']);
        $this->assertEquals('testSiteName', $result['display_name']);

        // case error
        $this->expectException(PersistenceFailedException::class);
        $this->expectExceptionMessage('Entity save failure. Found the following errors (display_name._empty: "サイト名を入力してください。", title._empty: "サイトタイトルを入力してください。")');
        $this->Installations->setSiteName('');

    }

    public function test_deployAdminAssets()
    {
        $this->markTestIncomplete('このテストは未実装です。');
    }

    /**
     * データベースのデータに初期更新を行う
     */
    public function testExecuteDefaultUpdates()
    {
        $this->markTestIncomplete('このテストは未実装です。BcManagerComponentから移植中です。');
        $dbConfig = [
            'datasource' => 'Database/BcMysql',
            'persistent' => false,
            'host' => 'localhost',
            'port' => '8889',
            'login' => 'root',
            'password' => 'root',
            'database' => 'basercms',
            'schema' => '',
            'prefix' => 'mysite_',
            'encoding' => 'utf8',
        ];

        // プラグイン有効化チェック用準備(ダミーのプラグインディレクトリを作成)
        $testPluginPath = BASER_PLUGINS . 'Test' . DS;
        $testPluginConfigPath = $testPluginPath . 'config.php';
        $Folder = new BcFolder($testPluginPath);
        $Folder->create();
        $File = new BcFile($testPluginConfigPath);
        $File->write('<?php $title = "テスト";');

        Configure::write('BcApp.corePlugins', ['BcBlog', 'BcFeed', 'BcMail', 'Test']);


        // 初期更新を実行
        $result = $this->BcManager->executeDefaultUpdates($dbConfig);


        // =====================
        // プラグイン有効化チェック
        // =====================
        $File->delete();
        $Folder->delete($testPluginPath);

        $this->Plugin = ClassRegistry::init('Plugin');
        $plugin = $this->Plugin->find('first', [
                'conditions' => ['id' => 4],
                'fields' => ['title', 'status'],
            ]
        );
        $expected = [
            'Plugin' => [
                'title' => 'テスト',
                'status' => 1,
            ]
        ];
        $this->Plugin->delete(4);
        unset($this->Plugin);
        $this->assertEquals($expected, $plugin, 'プラグインのステータスを正しく更新できません');
        $this->assertTrue($result, 'データベースのデータに初期更新に失敗しました');
    }

    /**
     * test installCorePlugin
     */
    public function testInstallCorePlugin()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test installPlugin
     */
    public function testInstallPlugin()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * データベース設定ファイル[database.php]を保存する
     *
     * @param array $options
     * @return boolean
     */
    public function testCreateDatabaseConfig()
    {
        $this->markTestIncomplete('このテストは未実装です。BcManagerComponentから移植中です。');
        // database.phpをバックアップ
        $configPath = APP . 'Config' . DS;
        $copy = copy($configPath . 'database.php', $configPath . 'database.php.copy');

        if ($copy) {
            $options = [
                'datasource' => 'mysql',
                'host' => 'hoge',
                'port' => '0000',
            ];
            $this->BcManager->createDatabaseConfig($options);

            $File = new BcFile($configPath . 'database.php');
            $result = $File->read();

            // 生成されたファイルを削除し、バックアップしたファイルに置き換える
            $File->delete();
            $File->close();
            rename($configPath . 'database.php.copy', $configPath . 'database.php');

            $this->assertMatchesRegularExpression("/\\\$default.*'datasource' => 'Database\/BcMysql'.*'host' => 'hoge'.*'port' => '0000'/s", $result, 'データベース設定ファイル[database.php]を正しく保存できません');

        } else {
            $this->markTestIncomplete('database.phpのバックアップに失敗したため、このテストをスキップします。');
        }
    }

    /**
     * インストール設定ファイルを生成する
     */
    public function testCreateInstallFile()
    {
        $this->markTestIncomplete('このテストは未実装です。BcManagerComponentから移植中です。');
        // install.phpをバックアップ
        $configPath = APP . 'Config' . DS;
        $copy = copy($configPath . 'install.php', $configPath . 'install.php.copy');

        if ($copy) {

            $this->BcManager->createInstallFile('hogeSalt', 'hogeSeed', 'hogeUrl');

            $File = new BcFile($configPath . 'install.php');
            $result = $File->read();

            // 生成されたファイルを削除し、バックアップしたファイルに置き換える
            $File->delete();
            $File->close();
            rename($configPath . 'install.php.copy', $configPath . 'install.php');

            $this->assertMatchesRegularExpression("/'Security.salt', 'hogeSalt'.*'Security.cipherSeed', 'hogeSeed'.*'BcEnv.siteUrl', 'hogeUrl'/s", $result, 'インストール設定ファイルを正しく生成できません');

        } else {
            $this->markTestIncomplete('install.phpのバックアップに失敗したため、このテストをスキップします。');

        }

    }

    /**
     * エディタテンプレート用のアイコン画像をデプロイ
     * test deployEditorTemplateImage
     */
    public function testDeployEditorTemplateImage()
    {
        // editor フォルダを削除
        $targetPath = WWW_ROOT . 'files' . DS . 'editor' . DS;
        $Folder = new \BaserCore\Utility\BcFolder($targetPath);
        $Folder->delete();

        $this->Installations->deployEditorTemplateImage();

        $this->assertFileExists($targetPath, 'エディタテンプレート用のアイコン画像をデプロイできません');

        //check file exists in editor folder
        $this->assertFileExists($targetPath . 'template1.gif');
        $this->assertFileExists($targetPath . 'template2.gif');
        $this->assertFileExists($targetPath . 'template3.gif');
    }

    /**
     * test _getDbSource
     */
    public function test_getDbSource()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getAllDefaultDataPatterns
     */
    public function testGetAllDefaultDataPatterns()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
    /**
     * アップロード用初期フォルダを作成する
     * test createDefaultFiles
     */
    public function testCreateDefaultFiles()
    {
        //create backup folder
        $backupPath = WWW_ROOT . 'files_backup' . DS;
        if (!is_dir($backupPath)) {
            mkdir($backupPath, 0777, true);
        }

        $dirs = ['blog', 'editor', 'theme_configs'];
        foreach ($dirs as $dir) {
            $path = WWW_ROOT . 'files' . DS . $dir;
            if (is_dir($path)) {
                // Backup folder if exists
                rename($path, $backupPath . $dir);
            }
        }

        $result = $this->Installations->createDefaultFiles();
        $this->assertTrue($result);

        //check folder is created
        foreach ($dirs as $dir) {
            $this->assertTrue(is_dir(WWW_ROOT . 'files' . DS . $dir));
        }

        //delete created folders
        foreach ($dirs as $dir) {
            $newDir = WWW_ROOT . 'files' . DS . $dir;
            if (is_dir($newDir) && !is_dir($backupPath . $dir)) {
                rmdir($newDir);
            }
        }

        // Restore backup folder
        foreach ($dirs as $dir) {
            $backupDir = $backupPath . $dir;
            if (is_dir($backupDir)) {
                rename($backupDir, WWW_ROOT . 'files' . DS . $dir);
            }
        }
        //delete backup folder
        rmdir($backupPath);
    }

}
