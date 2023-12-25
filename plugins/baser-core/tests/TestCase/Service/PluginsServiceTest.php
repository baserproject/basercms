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

use BaserCore\Service\PluginsService;
use BaserCore\Test\Factory\PluginFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcFile;
use BaserCore\Utility\BcFolder;
use BaserCore\Utility\BcUtil;
use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Core\App;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Composer\Package\Archiver\ZipArchiver;
use Laminas\Diactoros\UploadedFile;

/**
 * Class PluginsServiceTest
 * @property PluginsService $Plugins
 */
class PluginsServiceTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * @var PluginsService|null
     */
    public $Plugins = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(PluginsScenario::class);
        $this->Plugins = new PluginsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Plugins);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->Plugins->Plugins));
    }

    /**
     * sortModeが1の時、DBに登録されてるプラグインのみ取得
     * sortModeが0の時、DBに登録されてるプラグインとプラグインファイル全て取得
     * @param string $sortMode ソートモードかどうか ｜ DBに登録されてるデータかファイルも含めるか
     * @param string $expectedPlugin 期待されるプラグイン
     * @param string $expectedCount 期待される取得数
     * @return void
     * Test getIndex
     * @dataProvider indexDataprovider
     */
    public function testGetIndex($sortMode, $expectedPlugin): void
    {
        // テスト用のプラグインフォルダ作成
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        $file = new File($pluginPath . DS . 'config.php');
        $file->write("<?php return ['type' => 'Plugin'];");
        $file->close();

        $plugins = $this->Plugins->getIndex($sortMode);
        $pluginNames = [];
        foreach($plugins as $plugin) {
            $pluginNames[] = $plugin->name;
        }
        //期待されるプラグインを含むか
        $this->assertContains($expectedPlugin, $pluginNames);
        $folder->delete($pluginPath);
        if ($sortMode) {
            // フォルダ内プラグインが含まれてないか
            $this->assertNotContains('BcTest', $pluginNames);
        }
    }
    public function indexDataprovider()
    {
        return [
            // 普通の場合 | DBに登録されてるプラグインとプラグインファイル全て
            ["0", 'BcTest'],
            // ソートモードの場合 | DBに登録されてるプラグインのみ
            ["1", 'BcBlog'],
        ];
    }

    /**
     * test install
     */
    public function testInstall()
    {
        $this->markTestIncomplete('このメソッドを利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        // データが初期化されなくなってしまう。dropTableでトリガーが削除されるのが原因の様子

        // 正常な場合
        $this->assertTrue($this->Plugins->install('BcUploader', true, 'test'));
        // プラグインがない場合
        try {
            $this->Plugins->install('UnKnown', true, 'test');
        } catch (\Exception $e) {
            $this->assertEquals("Plugin UnKnown could not be found.", $e->getMessage());
        }
        // フォルダはあるがインストールできない場合
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        try {
            $this->assertNull($this->Plugins->install('BcTest', true, 'test'));
        } catch (\Exception $e) {
            $this->assertEquals("プラグインに Plugin クラスが存在しません。src ディレクトリ配下に作成してください。", $e->getMessage());
        }
        $folder->delete($pluginPath);
    }

    /**
     * test getByName
     */
    public function testGetByName()
    {
        $this->assertEquals('BcBlog', $this->Plugins->getByName('BcBlog')->name);
        $this->assertNull($this->Plugins->getByName('Test'));
    }
    /**
     * test resetDb
     * @throws \Exception
     */
    public function testResetDb()
    {
        $this->markTestIncomplete('このメソッドを利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        // データが初期化されなくなってしまう。dropTableでトリガーが削除されるのが原因の様子

        $this->Plugins->install('BcBlog', true, 'test');
        $blogPosts = $this->getTableLocator()->get('BcBlog.plugins');

        $rs = $blogPosts->find()->where(['name' => 'BcBlog'])->first();
        $this->assertTrue($rs->db_init);

        $this->Plugins->resetDb('BcBlog', 'test');

        $rs = $blogPosts->find()->where(['name' => 'BcBlog'])->first();
        $this->assertFalse($rs->db_init);
    }

    /**
     * test uninstall
     */
    public function testUninstall()
    {
        // TODO インストールの処理とまとめる予定
        $this->markTestIncomplete('テストが未実装です');
    }

    /**
     * test get
     */
    public function testGet()
    {
        $plugin = $this->Plugins->get(1);
        $this->assertEquals('BcBlog', $plugin->name);
    }

    /**
     * test getInstallStatusMessage
     */
    public function testGetInstallStatusMessage()
    {
        $this->assertEquals('既にインストール済のプラグインです。', $this->Plugins->getInstallStatusMessage('BcBlog'));
        $this->assertEquals('インストールしようとしているプラグインのフォルダが存在しません。', $this->Plugins->getInstallStatusMessage('BcTest'));
        $pluginPath = App::path('plugins')[0] . DS . 'BcTest';
        $folder = new Folder($pluginPath);
        $folder->create($pluginPath, 0777);
        $this->assertEquals('', $this->Plugins->getInstallStatusMessage('BcTest'));
        $folder->delete($pluginPath);
    }

    /**
     * test getVersion
     */
    public function test_getVersion()
    {
        $this->assertEquals('', $this->Plugins->getVersion('Hoge'));
        PluginFactory::make(['name' => 'Hoge', 'version' => '2.0.0'])->persist();
        $this->assertEquals('2.0.0', $this->Plugins->getVersion('Hoge'));
    }

    /**
     * test update
     * @throws \Exception
     */
    public function test_update()
    {
        // プラグイン
        $this->Plugins->install('BcPluginSample', true, 'test');
        $pluginPath = Plugin::path('BcPluginSample');
        rename($pluginPath . 'VERSION.txt', $pluginPath . 'VERSION.bak.txt');
        $file = new File($pluginPath . 'VERSION.txt');
        $file->write('10.0.0');
        $this->Plugins->update('BcPluginSample', 'test');
        $this->assertEquals('10.0.0', $this->Plugins->getVersion('BcPluginSample'));
        rename($pluginPath . 'VERSION.bak.txt', $pluginPath . 'VERSION.txt');

        // コア
        rename(BASER . 'VERSION.txt', BASER . 'VERSION.bak.txt');
        $file = new File(BASER . 'VERSION.txt');
        $file->write('10.0.0');
        $this->Plugins->update('BaserCore', 'test');
        $plugins = array_merge(['BaserCore'], Configure::read('BcApp.corePlugins'));
        foreach($plugins as $plugin) {
            $this->assertEquals('10.0.0', BcUtil::getVersion($plugin));
        }
        rename(BASER . 'VERSION.bak.txt', BASER . 'VERSION.txt');
    }

    /**
     * test Update Core Fail
     *
     * @return void
     */
    public function test_updateCoreFails()
    {
        rename(BASER . 'VERSION.txt', BASER . 'VERSION.bak.txt');
        $file = new File(BASER . 'VERSION.txt');
        $file->write('10.0.0');

        // 失敗用のマイグレーションファイルを作成
        $path = $this->createFailMigration();

        // 失敗を確認
        $this->assertFalse($this->Plugins->update('BaserCore', 'test'));

        // 無効化されたプラグインの有効化を確認
        $this->assertEquals(5, $this->getTableLocator()->get('BaserCore.Plugins')->find()->where(['status' => true])->count());

        rename(BASER . 'VERSION.bak.txt', BASER . 'VERSION.txt');
        unlink($path);
    }

    /**
     * コアのアップデートを失敗させるためのマイグレーションファイルを作成する
     *
     * test_updateCoreFail にて利用
     *
     * 既に存在するIDを利用することで失敗させる
     *
     * @return string
     */
    protected function createFailMigration()
    {
        $path = Plugin::path('BaserCore') . 'config' . DS . 'Migrations' . DS . '20230328000000_TestMigration.php';
        $file = new File($path);
        $data = <<< EOF
<?php
use BaserCore\Database\Migration\BcMigration;
class TestMigration extends BcMigration
{
    public function up(){}
}
EOF;
        $file->write($data);
        $file->close();
        return $path;
    }

    /**
     * test detachAll
     */
    public function test_detachAll()
    {
        $result = $this->Plugins->detachAll();
        $this->assertEquals(5, count($result));
    }

    /**
     * test attachAllFromIds
     * @return void
     */
    public function test_attachAllFromIds(){
        $plugins = $this->Plugins->getIndex(false);
        $this->assertTrue($plugins[1]->status);

        $ids = [1,2];

        $this->Plugins->detachAll();

        $this->Plugins->attachAllFromIds($ids);
        $plugin = $this->Plugins->get(1);
        $this->assertTrue($plugin->status);

        $plugin = $this->Plugins->get(2);
        $this->assertTrue($plugin->status);
    }

    /**
     * test attachAllFromIds with 配列：null
     * @return void
     */
    public function test_attachAllFromIds_false(){
        $ids = null;
        $rs = $this->Plugins->attachAllFromIds($ids);

        $this->assertNull($rs);
    }

    /**
     * test getMarketPlugins
     * @return void
     */
    public function testGetMarketPlugins(){
        $this->markTestIncomplete('TODO 直接外部ではなく Mockのテストに切り替える');
        $rs = $this->Plugins->getMarketPlugins();
        $this->assertNotEmpty($rs, 'baserマーケットのデータが読み込めませんでした。テストを再実行してください。');
        $caches = Cache::read('baserMarketPlugins', '_bc_env_');
        $this->assertIsArray($caches);
    }

    /**
     * test getNamesById
     * @return void
     */
    public function testGetNamesById()
    {
        $rs = $this->Plugins->getNamesById([1, 2, 3]);

        $this->assertEquals('ブログ', $rs[1]);
        $this->assertEquals('メール', $rs[2]);
        $this->assertEquals('アップローダー', $rs[3]);
    }

    /**
     * test batch
     * @return void
     */
    public function testBatch()
    {
        PluginFactory::make(['id' => 10, 'name' => 'plugin 1', 'status' => 1])->persist();
        PluginFactory::make(['id' => 11, 'name' => 'plugin 2', 'status' => 1])->persist();
        PluginFactory::make(['id' => 12, 'name' => 'plugin 3', 'status' => 1])->persist();

        $this->Plugins->batch('detach', [10, 11, 12]);

        $this->assertFalse($this->Plugins->get(10)->status);
        $this->assertFalse($this->Plugins->get(11)->status);
        $this->assertFalse($this->Plugins->get(12)->status);
    }

    /**
     * test add
     */
    public function test_add()
    {
        $path = BASER_PLUGINS . 'BcThemeSample';
        $zipSrcPath = TMP . 'zip' . DS;
        $folder = new BcFolder($zipSrcPath);
        $folder->create();
        //copy
        $folder = new BcFolder($path);
        $folder->copy( $zipSrcPath. 'BcThemeSample2');
        $plugin = 'BcThemeSample2';
        $zip = new ZipArchiver();
        $testFile = $zipSrcPath . $plugin . '.zip';
        $zip->archive($zipSrcPath, $testFile, true);
        $size = filesize($path);
        $type = BcUtil::getContentType($testFile);

        $this->setUploadFileToRequest('file', $testFile);

        $files = new UploadedFile(
            $testFile,
            $size,
            UPLOAD_ERR_OK,
            $plugin . '.zip',
            $type
        );

        //成功
        $rs = $this->Plugins->add(["file" => $files]);
        $this->assertEquals('BcThemeSample2', $rs);
        //plugins/ 内に、Zipファイルを展開して配置する。
        $this->assertTrue(is_dir(ROOT . DS . 'plugins' . DS . $plugin));

        //  既に /plugins/ 内に同名のプラグインが存在する場合には、数字付きのディレクトリ名（PluginName2）にリネームする。
        $folder = new BcFolder($zipSrcPath);
        $folder->create();
        //copy
        $folder = new BcFolder($path);
        $folder->copy($zipSrcPath . 'BcThemeSample2');
        $zip = new ZipArchiver();
        $zip->archive($zipSrcPath, $testFile, true);
        $this->setUploadFileToRequest('file', $testFile);
        $files = new UploadedFile(
            $testFile,
            $size,
            UPLOAD_ERR_OK,
            $plugin . '.zip',
            $type
        );

        $rs = $this->Plugins->add(["file" => $files]);
        $this->assertEquals('BcThemeSample3', $rs);

        //テスト実行後不要ファイルを削除
        $folder = new BcFolder(ROOT . DS . 'plugins' . DS . $plugin);
        $folder->delete();
        $folder = new BcFolder(ROOT . DS . 'plugins' . DS . 'BcThemeSample3');
        $folder->delete();
        $folder = new BcFolder($zipSrcPath);
        $folder->delete();

        // TODO ローカルでは成功するが、GitHubActions上でうまくいかないためコメントアウト（原因不明）
        // post_max_size　を超えた場合、サーバーに設定されているサイズ制限を超えた場合、
//        $this->setUploadFileToRequest('file', 'test.zip');
//        $postMaxSizeMega = preg_replace('/M\z/', '', ini_get('post_max_size'));
//        $postMaxSizeByte = $postMaxSizeMega * 1024 * 1024;
//        $_SERVER['CONTENT_LENGTH'] = $postMaxSizeByte + 1;
//        $_SERVER['REQUEST_METHOD'] = 'POST';
//        $files = new UploadedFile(
//            'test.zip',
//            1,
//            UPLOAD_ERR_OK,
//            'test.zip',
//            'zip'
//        );
//        $this->expectException("BaserCore\Error\BcException");
//        $this->expectExceptionMessage("送信できるデータ量を超えています。合計で " . ini_get('post_max_size') . " 以内のデータを送信してください。");
//        $this->Plugins->add(["file" => $files]);
    }

    /**
     * test getAvailableCoreVersionInfo
     * @return void
     * @dataProvider getAvailableCoreVersionInfoDataProvider
     */
    public function test_getAvailableCoreVersionInfo(
        string $currentVersion,
        array $releaseVersions,
        string $expectLatest,
        array $expectAvailable
    )
    {
        $versionPath = Plugin::path('BaserCore') . 'VERSION.txt';
        $versionBakPath = Plugin::path('BaserCore') . 'VERSION.bak.txt';
        $rssPath = WWW_ROOT . 'baser-core.rss';

        // バックアップを取得する
        copy($versionPath, $versionBakPath);
        // オートアップデートを有効化
        SiteConfigFactory::make(['name' => 'use_update_notice', 'value' => true])->persist();
        // BcApp.coreReleaseUrl を書き換える
        Configure::write('BcApp.coreReleaseUrl', $rssPath);
        // バージョンを書き換える
        $file = new BcFile($versionPath);
        $file->write($currentVersion);
        // RSSを生成
        $this->createReleaseRss($releaseVersions);
        // キャッシュを削除
        Cache::delete('coreReleaseInfo', '_bc_update_');

        // 実行
        $versionInfo = $this->Plugins->getAvailableCoreVersionInfo();
        $this->assertEquals($expectLatest, $versionInfo['latest']);
        $this->assertEquals($expectAvailable, $versionInfo['versions']);

        // 初期化
        rename($versionBakPath, $versionPath);
        unlink($rssPath);
    }

    public function getAvailableCoreVersionInfoDataProvider()
    {
        return [
            // 通常
            ['5.0.0', ['5.0.2', '5.0.1', '5.0.0'], '5.0.2', ['5.0.2', '5.0.1']],
            // 現在のバージョンが dev の場合
            ['5.0.0-dev', ['5.0.2', '5.0.1', '5.0.0'], '5.0.2', []],
            // リリースに dev が含まれている場合
            ['5.0.0', ['5.0.2-dev', '5.0.1-dev', '5.0.0'], '5.0.0', []],
            // マイナーアップデートの場合
            ['5.0.0', ['5.1.0', '5.0.0'], '5.1.0', ['5.1.0']],
            // メジャーアップデートの場合
            ['5.0.0', ['6.0.0', '5.5.0'], '6.0.0', ['5.5.0']],
            // アップデート対象が古いものしかない場合
            ['5.0.0', ['5.0.0', '4.9.0'], '5.0.0', []],
            // アップデート対象が全く取れない場合
            ['5.0.0', [], '', []],
        ];
    }

    /**
     * リリース情報のRSSを作成する
     * @param array $versions
     * @return void
     */
    public function createReleaseRss(array $versions)
    {
        $url = WWW_ROOT . 'baser-core.rss';
        $rss = <<< EOF
<?xml version="1.0" encoding="UTF-8"?>
<rss version="2.0" xmlns:slash="http://purl.org/rss/1.0/modules/slash/">
  <channel>
    <title>baserproject/baser-core releases</title>
    <description>Latest releases on Packagist of baserproject/baser-core.</description>
    <pubDate>Wed, 19 Apr 2023 07:17:04 +0000</pubDate>
    <generator>Packagist</generator>
    <link>https://packagist.org/packages/baserproject/baser-core</link>
EOF;
        foreach($versions as $version) {
            $rss .= <<< EOF
    <item>
      <title>baserproject/baser-core ({$version})</title>
      <description><![CDATA[baserCMS plugin for CakePHP]]></description>
      <pubDate>Wed, 19 Apr 2023 07:17:04 +0000</pubDate>
      <link>https://packagist.org/packages/baserproject/baser-core</link>
      <guid isPermaLink="false">baserproject/baser-core {$version}</guid>
      <slash:comments>0</slash:comments>
    </item>
EOF;
        }
        $rss .= <<< EOF
  </channel>
</rss>
EOF;
        $file = new BcFile($url);
        $file->write($rss);
    }

}
