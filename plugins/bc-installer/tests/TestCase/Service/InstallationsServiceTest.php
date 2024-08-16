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

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BcInstaller\Service\InstallationsService;
use BcInstaller\Service\InstallationsServiceInterface;
use Cake\Core\Configure;

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
     *
     * @return array
     */
    public function testCheckEnv()
    {
        $this->markTestIncomplete('このテストは未実装です。BcManagerComponentから移植中です。');
        $result = $this->BcManager->checkEnv();
        $this->assertNotEmpty($result, '環境情報を取得できません');
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
     * test setSecuritySalt
     */
    public function testSetSecuritySalt()
    {
        // Test default length (40 characters)
        $salt = $this->Installations->setSecuritySalt();
        $this->assertEquals(40, strlen($salt));

        // Test custom length (e.g., 50 characters)
        $customLength = 50;
        $customSalt = $this->Installations->setSecuritySalt($customLength);
        $this->assertEquals($customLength, strlen($customSalt));

        // Verify that the salt is correctly written to the configuration
        $config = Configure::read('Security.salt');
        $this->assertEquals($customSalt, $config);
    }

    /**
     * test addDefaultUser
     */
    public function testAddDefaultUser()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test setSiteName
     */
    public function testSetSiteName()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
     */
    public function testCreateDefaultFiles()
    {
        $this->markTestIncomplete('このテストは未実装です。BcManagerComponentから移植中です。');
        // 各フォルダを削除
        $path = WWW_ROOT . 'files' . DS;
        $dirs = ['blog', 'editor', 'theme_configs'];

        foreach($dirs as $dir) {
            (new BcFolder($path . $dir))->delete($path . $dir);
        }

        $this->BcManager->createDefaultFiles();

        foreach($dirs as $dir) {
            $this->assertFileExists($path . $dir, 'アップロード用初期フォルダを正しく作成できません');
        }
    }


    /**
     * test createJwt
     */
    public function testCreateJwt()
    {
        //check if the keys exists then delete them
        $keyPath = CONFIG . 'jwt.key';
        $pemPath = CONFIG . 'jwt.pem';

        if (file_exists($keyPath)) {
            unlink($keyPath);
        }

        if (file_exists($pemPath)) {
            unlink($pemPath);
        }

        //create the keys
        $result = $this->Installations->createJwt();
        $this->assertTrue($result);

        //check if the keys exists
        $this->assertFileExists($keyPath);
        $this->assertFileExists($pemPath);
    }
}
