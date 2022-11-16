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
use BcInstaller\Service\InstallationsService;
use BcInstaller\Service\InstallationsServiceInterface;

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
        $Folder = new Folder();
        $Folder->create($testPluginPath);
        $File = new File($testPluginConfigPath, true);
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

            $File = new File($configPath . 'database.php');
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

            $File = new File($configPath . 'install.php');
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
     *
     * @return boolean
     */
    public function testDeployEditorTemplateImage()
    {
        $this->markTestIncomplete('このテストは未実装です。BcManagerComponentから移植中です。');
        // editor フォルダを削除
        $Folder = new Folder();
        $targetPath = WWW_ROOT . 'files' . DS . 'editor' . DS;
        $Folder->delete($targetPath);

        $this->BcManager->deployEditorTemplateImage();

        $this->assertFileExists($targetPath, 'エディタテンプレート用のアイコン画像をデプロイできません');

    }

    /**
     * アップロード用初期フォルダを作成する
     */
    public function testCreateDefaultFiles()
    {
        $this->markTestIncomplete('このテストは未実装です。BcManagerComponentから移植中です。');
        // 各フォルダを削除
        $Folder = new Folder();
        $path = WWW_ROOT . 'files' . DS;
        $dirs = ['blog', 'editor', 'theme_configs'];

        foreach($dirs as $dir) {
            $Folder->delete($path . $dir);
        }

        $this->BcManager->createDefaultFiles();

        foreach($dirs as $dir) {
            $this->assertFileExists($path . $dir, 'アップロード用初期フォルダを正しく作成できません');
        }
    }

}
