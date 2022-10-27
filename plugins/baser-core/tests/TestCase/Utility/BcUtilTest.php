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

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\Event\BcEventListener;
use BaserCore\Test\Factory\SiteConfigFactory;
use Cake\Core\App;
use Cake\Cache\Cache;
use Cake\Core\Plugin;
use Cake\Core\Configure;
use Cake\Event\EventManager;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use BaserCore\Utility\BcUtil;
use BaserCore\TestSuite\BcTestCase;
use Cake\Http\Session;
use Cake\Routing\Router;

/**
 * TODO: $this->getRequest();などをsetupに統一する
 * Class BcUtilTest
 * @package BaserCore\Test\TestCase\Utility
 */
class BcUtilTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Plugins',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->getRequest();
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
     * Test loginUser
     * @return void
     * @dataProvider loginUserDataProvider
     */
    public function testLoginUser($isLogin, $expects): void
    {
        if ($isLogin) {
            $this->loginAdmin($this->getRequest());
        }
        $result = BcUtil::loginUser();
        if ($result) {
            $result = $result->id;
        }
        $this->assertEquals($expects, $result);
    }

    public function loginUserDataProvider()
    {
        return [
            // ログインしている状況
            [true, 1],
            // ログインしていない状況
            [false, false]
        ];
    }

    /**
     * test loginUserFromSession
     */
    public function testLoginUserFromSession()
    {
        $this->assertFalse(BcUtil::loginUserFromSession());
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertEquals('baser admin', BcUtil::loginUserFromSession()->name);
    }

    /**
     * Test isSuperUser
     * @return void
     * @dataProvider isSuperUserDataProvider
     */
    public function testIsSuperUser($id, $expects): void
    {
        if ($id) {
            $this->loginAdmin($this->getRequest(), $id);
        }
        $result = BcUtil::isSuperUser();
        $this->assertEquals($expects, $result);
    }

    public function isSuperUserDataProvider()
    {
        return [
            // ログインしてない場合
            [null, false],
            // システム管理者の場合
            [1, true],
            // サイト運営者などそれ以外の場合
            [2, false]
        ];
    }

    /**
     * Test isAgentUser
     * @return void
     * @dataProvider isAgentUserDataProvider
     */
    public function testIsAgentUser($id, $expects): void
    {

        if ($id) {
            $user = $this->loginAdmin($this->getRequest('/baser/admin'), $id);
            $session = $this->request->getSession();
            $session->write('AuthAgent.User', $user);
        }
        $result = BcUtil::isAgentUser();

        $this->assertEquals($expects, $result);
    }

    public function isAgentUserDataProvider()
    {
        return [
            // ログインしてない場合
            [null, false],
            // システム管理者などAuthAgentが与えられた場合
            [1, true],
        ];
    }

    /**
     * Test isInstallMode
     * @return void
     * @dataProvider isInstallModeDataProvider
     */
    public function testIsInstallMode($mode, $expects): void
    {
        $_SERVER["INSTALL_MODE"] = $mode;
        $result = BcUtil::isInstallMode();
        $this->assertEquals($expects, $result);
    }

    public function isInstallModeDataProvider()
    {
        return [
            // インストールモード On
            ["true", true],
            // インストールモード Off
            ["false", false],
        ];
    }

    /**
     * Test getVersion
     * @return void
     */
    public function testGetVersion(): void
    {
        // BaserCore
        $file = new File(BASER . DS . 'VERSION.txt');
        $expected = preg_replace('/^(.+?)\n.+$/s', "$1", $file->read());
        $result = BcUtil::getVersion();
        $this->assertEquals($expected, $result);

        // プラグイン
        $file = new File(Plugin::path('bc-admin-third') . DS . 'VERSION.txt');
        $expected = preg_replace('/^(.+?)\n.*$/s', "$1", $file->read());
        $result = BcUtil::getVersion('BcAdminThird');
        $this->assertEquals($expected, $result);

        // ダミーのプラグインを作成
        $path = App::path('plugins')[0] . 'hoge' . DS;
        $Folder = new Folder($path, true);
        $File = new File($path . 'VERSION.txt', true);
        $File->write('1.2.3');
        $result = BcUtil::getVersion('Hoge');

        $File->close();
        $Folder->delete();
        $this->assertEquals('1.2.3', $result, 'プラグインのバージョンを取得できません');
    }

    /**
     * バージョンを特定する一意の数値を取得する
     * @return void
     */
    public function testVerpoint(): void
    {
        $version = 'baserCMS 3.0.6.1';
        $result = BcUtil::verpoint($version);
        $this->assertEquals(3000006001, $result, '正しくバージョンを特定する一意の数値を取得できません');

        $version = 'baserCMS 3.0.6.1 beta';
        $result = BcUtil::verpoint($version);
        $this->assertEquals(false, $result, '正しくバージョンを特定する一意の数値を取得できません');
    }

    /**
     * 有効なプラグインの一覧を取得する
     * @return void
     */
    public function testGetEnablePlugins(): void
    {
        $expects = ['BcBlog', 'BcMail', 'BcUploader', 'BcFavorite', 'BcSearchIndex'];
        $result = BcUtil::getEnablePlugins();
        foreach ($result as $value) {
            $this->assertContains($value->name, $expects, 'プラグインの一覧が取得できません。');
        }
    }

    /**
     * testIncludePluginClass
     * @return void
     */
    public function testIncludePluginClass(): void
    {
        $this->assertEquals(true, BcUtil::includePluginClass('BcBlog'));
        $this->assertEquals(false, BcUtil::includePluginClass('BcTest'));
        $this->assertEquals(false, BcUtil::includePluginClass(['BcTest', 'BcBlog']));
        $this->assertEquals(true, BcUtil::includePluginClass(['bc-blog', 'BcBlog']));
    }

    /**
     * test clearAllCache
     * @return void
     */
    public function testClearAllCache(): void
    {
        // cacheファイルのバックアップ作成
        $folder = new Folder();
        $origin = CACHE;
        $backup = str_replace('cache', 'cache_backup', CACHE);
        $folder->move($backup, [
            'from' => $origin,
            'mode' => 0777,
            'schema' => Folder::OVERWRITE,
        ]);

        // cache環境準備
        $cacheList = ['environment' => '_bc_env_', 'persistent' => '_cake_core_', 'models' => '_cake_model_'];

        foreach ($cacheList as $path => $cacheName) {
            Cache::drop($cacheName);
            Cache::setConfig($cacheName, [
                'className' => "File",
                'prefix' => 'myapp' . $cacheName,
                'path' => CACHE . $path . DS,
                'serialize' => true,
                'duration' => '+999 days',
            ]);
            Cache::write($cacheName . 'test', 'testtest', $cacheName);
        }

        // 削除実行
        BcUtil::clearAllCache();
        foreach ($cacheList as $cacheName) {
            $this->assertNull(Cache::read($cacheName . 'test', $cacheName));
        }

        // cacheファイル復元
        $folder->move($origin, [
            'from' => $backup,
            'mode' => 0777,
            'schema' => Folder::OVERWRITE,
        ]);
        $folder->chmod($origin, 0777);
    }

    /**
     * 管理システムかチェック
     *
     * @param string $url 対象URL
     * @param bool $expect 期待値
     * @dataProvider isAdminSystemDataProvider
     */
    public function testIsAdminSystem($url, $expect)
    {
        $this->getRequest($url);
        $result = BcUtil::isAdminSystem();
        $this->assertEquals($expect, $result, '正しく管理システムかチェックできません');
    }

    /**
     * isAdminSystem用データプロバイダ
     *
     * @return array
     */
    public function isAdminSystemDataProvider()
    {
        return [
            ['baser/admin', true],
            ['baser/admin/hoge', true],
            ['/baser/admin/hoge', true],
            ['baser/admin/', true],
            ['hoge', false],
            ['hoge/', false],
        ];
    }

    /**
     * 管理ユーザーかチェック
     *
     * @param string $id ユーザーグループに基づいたid
     * @param bool $expect 期待値
     * @return void
     * @dataProvider isAdminUserDataProvider
     */
    public function testIsAdminUser($id, $expect): void
    {
        $sessionKey = Configure::read('BcPrefixAuth.Admin.sessionKey');
        $session = $this->request->getSession();
        $user = $this->getUser($id);
        $session->write($sessionKey, $user);
        if ($expect) {
            $this->loginAdmin($this->getRequest(), $id);
        }
        $result = BcUtil::isAdminUser();
        $this->assertEquals($expect, $result);
    }

    /**
     * isAdminUser用データプロバイダ
     *
     * @return array
     */
    public function isAdminUserDataProvider()
    {
        return [
            // 管理ユーザー
            [1, true],
            // 運営者ユーザー
            [2, false],
        ];
    }

    /**
     * 現在ログインしているユーザーのユーザーグループ情報を取得する
     * @param string $id ユーザーid
     * @param bool $expect 期待値
     * @return void
     * @dataProvider loginUserGroupDataProvider
     */
    public function testLoginUserGroup($id, $expect): void
    {
        $sessionKey = Configure::read('BcPrefixAuth.Admin.sessionKey');
        $session = $this->request->getSession();
        $user = $this->getUser($id);
        $session->write($sessionKey, $user);
        if ($expect) {
            $this->loginAdmin($this->getRequest(), $id);
        }
        $result = BcUtil::loginUserGroup();

        if ($result === false) {
            $result = [];
        }

        $this->assertCount($expect, $result);
    }

    /**
     * isAdminUser用データプロバイダ
     *
     * @return array
     */
    public function loginUserGroupDataProvider()
    {
        return [
            // ログイン
            [1, 1],
            // 非ログイン
            [0, 0],
        ];
    }

    /**
     * ログインしているユーザー名を取得
     */
    public function testLoginUserName()
    {
        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        // ログインしていない場合
        $result = BcUtil::loginUserName();
        $this->assertEmpty($result, 'ログインユーザーのデータを正しく取得できません');

        // ログインしている場合
        $Session = new CakeSession();
        $Session->write('Auth.' . BcUtil::authSessionKey() . '.name', 'hoge');
        $result = BcUtil::loginUserName();
        $this->assertEquals('hoge', $result, 'ログインユーザーのデータを正しく取得できません');
    }

    /**
     * 認証用のキーを取得
     */
    public function testAuthSessionKey()
    {
        $this->assertEquals('AuthAdmin', BcUtil::authSessionKey());
    }

    /**
     * テーマ梱包プラグインのリストを取得する
     */
    public function testGetThemesPlugins()
    {
        $theme = 'BcSpaSample';
        $plugins = BcUtil::getThemesPlugins($theme);
        $this->assertCount(0, $plugins);

        $themePath = BcUtil::getPluginPath($theme);
        $pluginName = 'test';
        mkdir($themePath . 'Plugin', 0777, true);
        mkdir($themePath . 'Plugin/' . $pluginName, 0777, true);

        $plugins = BcUtil::getThemesPlugins($theme);
        $this->assertCount(1, $plugins);
        $this->assertEquals($pluginName, $plugins[0]);

        $folder = new Folder();
        $folder->delete($themePath . 'Plugin');
    }

    /**
     * 現在適用しているテーマ梱包プラグインのリストを取得する
     */
    public function testGetCurrentThemesPlugins()
    {
        $currentSite = $this->getRequest()->getAttribute('currentSite');
        // 現在のテーマのプラグインを作成する
        $targetTheme = BcUtil::getCurrentTheme();
        $themePath = BcUtil::getPluginPath($targetTheme);
        $pluginName = 'test';
        mkdir($themePath . 'Plugin', 777, true);
        mkdir($themePath . 'Plugin/' . $pluginName, 777, true);
        // プラグインが存在しているかどうか確認する
        $plugins = BcUtil::getCurrentThemesPlugins();
        $this->assertCount(1, $plugins);
        $this->assertEquals($pluginName, $plugins[0]);

        // 現在のテーマを切り替える
        $currentSite->theme = 'BcSpaSample';
        $plugins = BcUtil::getCurrentThemesPlugins();
        // プラグインが存在しないか確認する
        $this->assertCount(0, $plugins);

        // 作成したプラグインを削除する
        $folder = new Folder();
        $folder->delete($themePath . 'Plugin');
    }

    /**
     * スキーマ情報のパスを取得する
     */
    public function testGetSchemaPath()
    {

        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        // Core
        $result = BcUtil::getSchemaPath();
        $this->assertEquals(BASER_CONFIGS . 'Schema', $result, 'Coreのスキーマ情報のパスを正しく取得できません');

        // Blog
        $result = BcUtil::getSchemaPath('BcBlog');
        $this->assertEquals(BASER_PLUGINS . 'Blog/Config/Schema', $result, 'プラグインのスキーマ情報のパスを正しく取得できません');
    }

    /**
     * 初期データのパスを取得する
     *
     * @param string $theme テーマ名
     * @param string $pattern 初期データの類型
     * @param string $expect 期待値
     * @dataProvider getDefaultDataPathDataProvider
     */
    public function testGetDefaultDataPath($theme, $pattern, $expect)
    {
        $Folder = new Folder();
        // 初期データ用のダミーディレクトリを作成
        if (!$pattern) $pattern = 'default';
        if ($theme) {
            $path = BASER_THEMES . $theme . DS . 'config' . DS . 'data' . DS . $pattern;
            $Folder->create($path);
        }
        $result = BcUtil::getDefaultDataPath($theme, $pattern);
        // 初期データ用のダミーディレクトリを削除
        if ($theme) {
            $Folder->delete($path);
        }
        $this->assertEquals($expect, $result, '初期データのパスを正しく取得できません');
    }

    /**
     * getDefaultDataPath用データプロバイダ
     *
     * @return array
     */
    public function getDefaultDataPathDataProvider()
    {
        return [
            [null, null, ROOT . '/plugins/bc-front/config/data/default'],
            ['nada-icons', null, ROOT . '/plugins/nada-icons/config/data/default'],
            ['nada-icons', 'not_default', ROOT . '/plugins/nada-icons/config/data/not_default'],
        ];
    }

    /**
     * シリアライズ / アンシリアライズ
     */
    public function testSerialize()
    {
        // BcUtil::serialize()でシリアライズした場合
        $serialized = BcUtil::serialize('hoge');
        $result = BcUtil::unserialize($serialized);
        $this->assertEquals('hoge', $result, 'BcUtil::serialize()で正しくシリアライズ/アンシリアライズできません');

        // serialize()のみでシリアライズした場合
        $serialized = serialize('hoge');
        $result = BcUtil::unserialize($serialized);
        $this->assertEquals('hoge', $result, 'serializeのみで正しくシリアライズ/アンシリアライズできません');
    }

    /**
     * アンシリアライズ
     * base64_decode が前提
     */
    public function testUnserialize()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test urlencode
     *
     * @param $input
     * @param $expected
     * @dataProvider urlencodeDataProvider
     */
    public function testUrlencode($input, $expected)
    {
        $this->assertEquals($expected, BcUtil::urlencode($input));
    }

    public function urlencodeDataProvider(): array
    {
        return [
            ['a=b+c', 'a_b_c'],
            ['_a=b+c', 'a_b_c'],
            ['_a=b+c_', 'a_b_c'],
            ['__a=b+c_', 'a_b_c'],
            ['!"#$%&a=b+c_', 'a_b_c'],
        ];
    }

    /**
     * コンソールから実行されてるかどうかチェックする
     */
    public function testIsConsole()
    {
        $this->assertTrue(BcUtil::isConsole());
        $_ENV['IS_CONSOLE'] = false;
        $this->assertFalse(BcUtil::isConsole());
        $_ENV['IS_CONSOLE'] = true;
    }

    /**
     * レイアウトテンプレートのリストを取得する
     */
    public function testGetTemplateList()
    {
        // プラグインが一つの場合
        $result = BcUtil::getTemplateList('Pages', 'BcFront');
        $this->assertEquals(["default" => "default"], $result);
        // 複数プラグインがある場合
        $result = BcUtil::getTemplateList('Admin/element/Dashboard', ['BaserCore', 'BcAdminThird']);
        $expected = ['baser_news' => 'baser_news', "contents_info" => "contents_info", "update_log" => "update_log"];
        $this->assertEquals($expected, $result);
    }

    /**
     * templatesのpath取得のテスト
     */
    public function testGetTemplatePath()
    {
        $plugin = 'BaserCore';
        $expected = '/var/www/html/plugins/baser-core/templates/';
        $result = BcUtil::getTemplatePath($plugin);
        $this->assertEquals($expected, $result);
    }

    /**
     * 全てのテーマリストを取得する
     */
    public function testGetAllThemeList()
    {
        $themePath = ROOT . DS . 'plugins' . DS . 'TestTheme';
        $themeConfigPath = $themePath . DS . 'config.php';
        $folder = new Folder();
        $folder->create(ROOT . DS . 'plugins' . DS . 'TestTheme');
        $file = new File($themeConfigPath);
        $file->write('<?php
            return [
                \'type\' => \'Theme\'
            ];
        ');
        $file->close();
        $themes = BcUtil::getAllThemeList();
        $this->assertTrue(in_array('BcFront', $themes));
        $this->assertTrue(in_array('BcAdminThird', $themes));
        $this->assertTrue(in_array('TestTheme', $themes));
        $folder->delete($themePath);
    }

    /**
     * テーマリストを取得する
     */
    public function testGetThemeList()
    {
        $themes = BcUtil::getThemeList();
        $this->assertTrue(in_array('BcFront', $themes));
        $this->assertFalse(in_array('BcAdminThird', $themes));
    }

    /**
     * 管理画面用のテーマリストを取得する
     */
    public function testGetAdminThemeList()
    {
        $themes = BcUtil::getAdminThemeList();
        $this->assertFalse(in_array('BcFront', $themes));
        $this->assertTrue(array_key_exists('BcAdminThird', $themes));
    }

    /**
     * 指定したURLのドメインを取得する
     * @dataProvider getDomainDataProvider
     */
    public function testGetDomain($target, $expected)
    {
        $result = BcUtil::getDomain($target);
        $this->assertEquals($expected, $result);
    }

    public function getDomainDataProvider()
    {
        return [
            ['http', ''],
            ['https://localhost/', 'localhost'],
            ['https://localhost:8000', 'localhost:8000'],
        ];
    }

    /**
     * メインとなるドメインを取得する
     */
    public function testGetMainDomain()
    {
        // BcEnv.mainDomainがある場合
        $domain = "testMainDomain";
        Configure::write('BcEnv.mainDomain', $domain);
        $this->assertEquals(BcUtil::getMainDomain(), $domain);
        Configure::delete('BcEnv.mainDomain');
        // BcEnv.mainDomainがなく、BcEnv.siteUrlがある場合
        $siteUrl = "https://example.com:8000";
        Configure::write('BcEnv.siteUrl', $siteUrl);
        $this->assertEquals(BcUtil::getMainDomain(), "example.com:8000");

    }

    /**
     * 管理画面用のプレフィックスを取得する
     */
    public function testGetAdminPrefix()
    {
        $result = BcUtil::getAdminPrefix();
        $this->assertEquals('admin', $result);
    }

    /**
     *
     * baserコア用のプレフィックスを取得する
     */
    public function testGetBaserCorePrefix()
    {
        $result = BcUtil::getBaserCorePrefix();
        $this->assertEquals('baser', $result);
    }

    /**
     *
     * プレフィックス全体を取得する
     */
    public function testGetPrefix()
    {
        $result = BcUtil::getPrefix();
        $this->assertEquals('/baser/admin', $result);
        // $regex = trueの場合
        $result = BcUtil::getPrefix(true);
        $this->assertMatchesRegularExpression('/^(|\/)' . $result . '/', '/baser/admin');
    }


    /**
     * 現在のドメインを取得する
     */
    public function testGetCurrentDomain()
    {
        $this->assertEmpty(BcUtil::getCurrentDomain(), '$_SERVER[HTTP_HOST] の値が間違っています。');
        Configure::write('BcEnv.host', 'hoge');
        $this->assertEquals('hoge', BcUtil::getCurrentDomain(), 'ホストを変更できません。');
    }

    /**
     * サブドメインを取得する
     *
     * @param string $host
     * @param string $currentHost
     * @param string $expects
     * @dataProvider getSubDomainDataProvider
     */
    public function testGetSubDomain($host, $currentHost, $expects, $message)
    {

        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        Configure::write('BcEnv.mainDomain', 'localhost');
        if ($currentHost) {
            Configure::write('BcEnv.host', $currentHost);
        } else {
            Configure::write('BcEnv.host', '');
        }
        $this->assertEquals($expects, BcUtil::getSubDomain($host), $message);
    }

    public function getSubDomainDataProvider()
    {
        return [
            ['', '', '', '現在のサブドメイン名が不正です。'],
            ['', 'hoge.localhost', 'hoge', '現在のサブドメイン名が取得できません。'],
            ['', 'test.localhost', 'test', '現在のサブドメイン名が取得できません。'],
            ['hoge.localhost', '', 'hoge', '引数を指定してサブドメイン名が取得できません。'],
            ['test.localhost', '', 'test', '引数を指定してサブドメイン名が取得できません。'],
            ['localhost', '', '', '引数を指定してサブドメイン名が取得できません。'],
        ];
    }

    /**
     * testGetPluginPath
     */
    public function testGetPluginPath()
    {
        $this->assertEquals(ROOT . '/plugins/baser-core/', BcUtil::getPluginPath('BaserCore'));
        $this->assertEquals(ROOT . '/plugins/bc-blog/', BcUtil::getPluginPath('BcBlog'));
        $this->assertEquals(ROOT . '/plugins/BcSpaSample/', BcUtil::getPluginPath('BcSpaSample'));
    }

    /**
     * testGetPluginDir
     */
    public function testGetPluginDir()
    {
        $this->assertEquals('baser-core', BcUtil::getPluginDir('BaserCore'));
        $this->assertEquals('bc-blog', BcUtil::getPluginDir('BcBlog'));
        $this->assertEquals('BcSpaSample', BcUtil::getPluginDir('BcSpaSample'));
    }

    /**
     * testGetContentsItem
     *
     * @return void
     */
    public function testGetContentsItem()
    {
        $result = BcUtil::getContentsItem();
        $list = ['Default', 'ContentFolder', 'ContentAlias', 'Page'];
        foreach ($list as $key) {
            $this->assertArrayHasKey($key, $result);
        }
        $this->assertEquals('BaserCore', $result['Default']['plugin']);
        $this->assertEquals('Default', $result['Default']['type']);
    }

    /**
     * Test convertSize
     *
     * @return void
     */
    public function testConvertSize()
    {
        $this->assertEquals(1, BcUtil::convertSize('1B'));
        $this->assertEquals(1024, BcUtil::convertSize('1K'));
        $this->assertEquals(1048576, BcUtil::convertSize('1M'));
        $this->assertEquals(1073741824, BcUtil::convertSize('1G'));
        $this->assertEquals(1099511627776, BcUtil::convertSize('1T'));
        $this->assertEquals(1099511627776, BcUtil::convertSize('1T', 'B'));
        $this->assertEquals(1073741824, BcUtil::convertSize('1T', 'K'));
        $this->assertEquals(1073741824, BcUtil::convertSize('1', 'K', 'T'));
        $this->assertEquals(0, BcUtil::convertSize(null));
    }

    /**
     * Test isOverPostSize
     *
     * @return void
     * @backupGlobals enabled
     * @dataProvider isOverPostSizeDataProvider
     */
    public function testIsOverPostSize($method, $post, $contentLength, $expect)
    {
        $_SERVER['REQUEST_METHOD'] = $method;
        $_POST = $post;
        $_SERVER['CONTENT_LENGTH'] = $contentLength;

        $this->assertEquals($expect, BcUtil::isOverPostSize());
    }

    public function isOverPostSizeDataProvider()
    {
        $postMaxSizeMega = preg_replace('/M\z/', '', ini_get('post_max_size'));
        $postMaxSizeByte = $postMaxSizeMega * 1024 * 1024;

        return [
            ['POST', [], $postMaxSizeByte + 1, true],
            ['POST', ['key' => 'value'], 9, false],
            ['POST', [], 0, false],
            ['GET', [], null, false]

        ];
    }

    /**
     * サイトのトップレベルのURLを取得する
     *
     * @return void
     */
    public function testTopLevelUrl()
    {
        if (BcUtil::isConsole()) {
            $this->assertEquals('https://localhost', BcUtil::topLevelUrl());
        } else {
            $this->assertMatchesRegularExpression('/^http:\/\/.*\/$/', BcUtil::topLevelUrl());
            $this->assertMatchesRegularExpression('/^http:\/\/.*[^\/]$/', BcUtil::topLevelUrl(false));

            // httpsの場合
            $_SERVER['HTTPS'] = 'on';
            $this->assertMatchesRegularExpression('/^https:\/\//', BcUtil::topLevelUrl());
        }
    }

    /**
     * サイトの設置URLを取得する
     */
    public function testSiteUrl()
    {
        if (BcUtil::isConsole()) {
            $this->assertEquals('https://localhost/', BcUtil::siteUrl());
        } else {
            $topLevelUrl = BcUtil::topLevelUrl(false);

            Configure::write('App.baseUrl', '/test/');
            $this->assertEquals($topLevelUrl . '/test/', BcUtil::siteUrl());

            Configure::write('App.baseUrl', '/test/index.php');
            $this->assertEquals($topLevelUrl . '/test/', BcUtil::siteUrl());

            Configure::write('App.baseUrl', '/test/hoge/');
            $this->assertEquals($topLevelUrl . '/test/hoge/', BcUtil::siteUrl());
        }
    }

    /**
     * WebサイトのベースとなるURLを取得する
     *
     * @param string $script App.baseUrlの値
     * @param string $script $_SERVER['SCRIPT_FILENAME']の値
     * @param string $expect 期待値
     * @dataProvider baseUrlDataProvider
     */
    public function testBaseUrl($baseUrl, $expect)
    {
        Configure::write('App.baseUrl', $baseUrl);
        $_SERVER['SCRIPT_NAME'] = DS . 'webroot' . DS . 'index.php';
        $_SERVER['SCRIPT_FILENAME'] = ROOT . $_SERVER['SCRIPT_NAME'];
        $result = BcUtil::baseUrl();
        $this->assertEquals($expect, $result, 'WebサイトのベースとなるURLを正しく取得できません');
    }

    public function baseUrlDataProvider()
    {
        return [
            ['/hoge/test', '/hoge/test/'],
            [null, '/'],
            ['/hoge/test', '/hoge/test/'],
            [null, '/'],
        ];
    }

    /**
     * ドキュメントルートを取得する
     */
    public function testDocRoot()
    {
        $_SERVER['SCRIPT_NAME'] = DS . 'webroot' . DS . 'index.php';
        $_SERVER['SCRIPT_FILENAME'] = ROOT . $_SERVER['SCRIPT_NAME'];
        $path = explode('/', $_SERVER['SCRIPT_NAME']);
        krsort($path);
        $expected = $_SERVER['SCRIPT_FILENAME'];
        foreach ($path as $value) {
            $reg = "/\/" . $value . "$/";
            $expected = preg_replace($reg, '', $expected);
        }
        $result = BcUtil::docRoot();
        $this->assertEquals($expected, $result);
    }

    /**
     * test getDbVersion
     */
    public function test_getDbVersion()
    {
        SiteConfigFactory::make(['name' => 'version', 'value' => '2.0.0'])->persist();
        $this->assertEquals('2.0.0', BcUtil::getDbVersion());
        $this->assertEquals('1.0.0', BcUtil::getDbVersion('BcBlog'));
    }

    /**
     * フォルダの中をフォルダを残して空にする
     */
    public function testEmptyFolder()
    {
        $dummyPath = TMP . 'test' . DS;
        $names = [
            'folder' => ['folder1', 'folder2'],
            'file' => ['file1', 'file2'],
        ];

        // ダミーのフォルダとファイルを作成
        $Folder = new Folder();
        $Folder->create($dummyPath, 0755);
        $Folder->create($dummyPath . $names['folder'][0], 0755);
        $Folder->create($dummyPath . $names['folder'][1], 0755);

        // フォルダtestにファイルを追加する
        new File($dummyPath . $names['file'][0], true);
        new File($dummyPath . $names['file'][1], true);

        // folder1とfolder2にfile1とfile2を追加する
        foreach ($names['folder'] as $folder) {
            $folderPath = $dummyPath . $folder . DS;
            foreach ($names['file'] as $file) {
                new File($folderPath . $file, true);
            }
        }

        BcUtil::emptyFolder($dummyPath);

        $result = true;
        // フォルダが存在しているかチェック
        foreach ($names['folder'] as $key => $name) {
            if (!is_dir($dummyPath . $name)) {
                $result = false;
            }
            @rmdir($dummyPath . $name);
        }
        // ファイルが削除されているかチェック
        foreach ($names['file'] as $key => $name) {
            if (file_exists($dummyPath . $name)) {
                $result = false;
            }
            @unlink($dummyPath . $name);
        }
        // folder1とfolder2にfile1とfile2が削除されているかチェック
        foreach ($names['folder'] as $folder) {
            $folderPath = $dummyPath . $folder . DS;
            foreach ($names['file'] as $file) {
                if (file_exists($folderPath . $file)) {
                    $result = false;
                }
                @unlink($folderPath . $file);
            }
        }
        $Folder->delete($dummyPath);

        $this->assertTrue($result, 'フォルダの中のファイルのみを削除することができません');
    }

    /**
     * ファイルポインタから行を取得し、CSVフィールドを処理する
     *
     * @param string $content CSVの内容
     * @param int $length length
     * @param string $d delimiter
     * @param string $e enclosure
     * @param string $expext 期待値
     * @param string $message テスト失敗時に表示するメッセージ
     * @dataProvider fgetcsvRegDataProvider
     */
    public function testFgetcsvReg($content, $length, $d, $e, $expect, $message)
    {
        $csv = new File(CACHE . 'test.csv');
        $csv->write($content);
        $csv->close();
        $csv->open();

        $result = BcUtil::fgetcsvReg($csv->handle, $length, $d, $e);
        $this->assertEquals($expect, $result, $message);

        $csv->close();
    }

    public function fgetcsvRegDataProvider()
    {
        return [
            ['test1,test2,test3', null, ',', '"', ['test1', 'test2', 'test3'], 'ファイルポインタから行を取得し、CSVフィールドを正しく処理できません'],
            ['test1,test2,test3', 5, ',', '"', ['test'], '読み込む文字列の長さを指定できません'],
            ['test1?test2?test3', null, '?', '"', ['test1', 'test2', 'test3\\'], 'デリミタを指定できません'],
            ['test1,<<test2,test3<<', null, ',', '<<', ['test1', 'test2,test3'], 'enclosureを指定できません'],
        ];
    }

    /**
     * Test onEvent, offEvent
     *
     * @return void
     */
    public function testOnEventOffEvent(): void
    {
        $eventManager = EventManager::instance();
        $eventKey = 'testOnEvent';
        $bcEvenListener = new BcEventListener();
        $bcEvenListener->events = ['event 1', 'event 2'];

        // onEvent() でイベントを設定
        BcUtil::onEvent($eventManager, $eventKey, $bcEvenListener->implementedEvents());
        // listeners() イベントの登録を確認
        $listeners = $eventManager->listeners($eventKey);
        foreach ($bcEvenListener->events as $index => $event) {
            $this->assertEquals($listeners[$index]['callable'], $event);
        }
        // offEvent() でイベントを解除
        BcUtil::offEvent($eventManager, $eventKey);
        // listeners() イベントの解除を確認
        $listeners = $eventManager->listeners($eventKey);
        $this->assertEmpty($listeners);
    }

    /**
     * Test createRequest
     *
     * @return void
     */
    public function testCreateRequest(): void
    {
        // デフォルトURL $url = '/'
        $urlList = ['' => '/*', '/about' => '/*', '/baser/admin/users/login' => '/baser/admin/{controller}/{action}/*'];
        foreach($urlList as $url => $route) {
            $request = BcUtil::createRequest($url);
            $this->assertEquals($route, $request->getParam('_matchedRoute'));
        }
        // テストAttributeとsetRequest
        $request = BcUtil::createRequest();
        $this->assertObjectHasAttribute('params', $request);
        // dataを設定する場合
        $request = BcUtil::createRequest('/', ['testKey' => 'testValue']);
        $data = $request->getParsedBody();
        $this->assertEquals('testValue', $data['testKey']);
        // methodを設定する場合
        $request = BcUtil::createRequest('/', [], 'POST');
        $this->assertEquals('POST', $request->getMethod());
        // configを設定する場合
        $session = new Session();
        $session->write('test', 'testGetRequest');
        $request = BcUtil::createRequest('/', [], 'GET', ['session' => $session]);
        $this->assertEquals('testGetRequest', $request->getSession()->read('test'));
    }

    /**
     * Test getCurrentTheme
     *
     * @return void
     */
    public function testGetCurrentTheme(): void
    {
        $currentSite = $this->getRequest()->getAttribute('currentSite');
        $theme = BcUtil::getCurrentTheme();
        $this->assertEquals($theme, $currentSite->theme);
    }

    /**
     * Test getRootTheme
     *
     * @return void
     */
    public function testGetRootTheme(): void
    {
        $theme = BcUtil::getRootTheme();
        $this->assertEquals('BcFront', $theme);
    }

    /**
     * Test getExtension
     *
     * @return void
     */
    public function testGetExtension(): void
    {
        $this->assertFalse(BcUtil::getExtension('/var/www/html/tmp'));
        $this->assertEquals('ext', BcUtil::getExtension('/var/www/html/tmp/test.ext'));
    }

    /**
     * Test getContentType
     *
     * @return void
     * @dataProvider getContentTypeDataProvider
     */
    public function testGetContentType($fileName, $contentType): void
    {
        $type = BcUtil::getContentType($fileName);
        $this->assertEquals($contentType, $type);
    }

    public function getContentTypeDataProvider(): array
    {
        return [
            [TMP, false],
            [TMP . 'test.jpg', 'image/jpeg'],
            [TMP . 'test.gif', 'image/gif'],
            [TMP . 'test.zip', 'application/zip'],
            [TMP . 'test.bla', false]
        ];
    }

    /**
     * Test decodeContent
     *
     * @param $content
     * @param $fileName
     * @param $extension
     * @return void
     * @dataProvider decodeContentProvider
     */
    public function testDecodeContent($content, $fileName, $extension): void
    {
        $ext = BcUtil::decodeContent($content, $fileName);
        $this->assertEquals($extension, $ext);
    }

    public function decodeContentProvider(): array
    {
        return [
            ['image/gif', null, 'gif'],
            ['image/jpeg', null, 'jpg'],
            ['application/zip', null, 'zip'],
            ['javascript/ts', TMP . 'test.ts', 'ts'],
            ['javascript/ts', null, false],
        ];
    }


    /**
     * 必要な一時フォルダが存在するかチェックし、なければ生成する
     */
    public function testCheckTmpFolders()
    {
        BcUtil::checkTmpFolders();
        $paths = [
            TMP . 'sessions',
            CACHE,
            CACHE . 'models',
            CACHE . 'persistent',
            CACHE . 'environment',
        ];
        // フォルダが生成されているかチェック
        $result = true;
        foreach($paths as $key => $value) {
            if (!is_dir($value)) {
                $result = false;
                echo $value;
            }
        }
        $this->assertTrue($result, '一時フォルダが正しく生成されていません');
    }

    /**
     * プラグインの namespace を書き換える
     */
    public function test_changePluginNameSpace()
    {
        $result = BcUtil::changePluginNameSpace('noExistsPlugin');
        // 処理実行が失敗を確認
        $this->assertFalse($result);
        // 対象ファイルをopen
        $theme = 'BcFront';
        $pluginPath = BcUtil::getPluginPath($theme);
        $file = new File($pluginPath . 'src' . DS . 'Plugin.php');
        // テーマ名とネームスペースが違う状態を作る
        $data = $file->read();
        $file->write(preg_replace('/namespace .+?;/', 'namespace WrongNamespace;', $data));
        // テーマ名とネームスペースが違う状態を確認
        $data = $file->read();
        preg_match('/namespace .+?;/', $data, $match);
        $this->assertNotEquals('namespace ' . $theme . ';', $match[0]);
        // 処理実行が成功を確認
        $result = BcUtil::changePluginNameSpace('BcFront');
        $this->assertTrue($result);
        // 処理実行後、テーマ名とネームスペースが同じになっている事を確認
        $data = $file->read();
        preg_match('/namespace .+?;/', $data, $match);
        $this->assertEquals('namespace ' . $theme . ';', $match[0]);
        // ファイルをclose
        $file->close();
    }

    /**
     * httpからのフルURLを取得する
     */
    public function testFullUrl()
    {
        $this->assertMatchesRegularExpression('/\//', BcUtil::fullUrl('/'));
        $this->assertMatchesRegularExpression('/\/.*blog/', BcUtil::fullUrl('/blog'));
        $this->assertMatchesRegularExpression('/\//', BcUtil::fullUrl(null));
    }

}
