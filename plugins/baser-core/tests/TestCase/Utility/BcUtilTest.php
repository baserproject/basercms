<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Utility;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use Cake\Core\App;
use Cake\Core\Configure;
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;
use Cake\Cache\Cache;

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
        Cache::drop('_bc_env_');
        Cache::drop('_cake_core_');
        Cache::drop('_cake_model_');
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
            [false, null]
        ];
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
            $user = $this->loginAdmin($this->getRequest(), $id);
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
            [true, true],
            // インストールモード Off
            [false, false],
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
        $expected = preg_replace('/(.+?)\n/', "$1", $file->read());
        $result = BcUtil::getVersion();
        $this->assertEquals($expected, $result);

        // プラグイン
        $file = new File(Plugin::path('bc-admin-third') . DS . 'VERSION.txt');
        $expected = preg_replace('/(.+?)\n/', "$1", $file->read());
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
        $expects = ['BcBlog', 'BcMail'];
        $result = BcUtil::getEnablePlugins();
        foreach($result as $key => $value) {
            $result[$key] = $value->name;
        }
        $this->assertEquals($expects, $result, 'プラグインの一覧が取得できません。');
    }

    /**
     * testIncludePluginClass
     * @return void
     */
    public function testIncludePluginClass(): void
    {
        $this->assertEquals(true, BcUtil::includePluginClass('BcBlog'));
        $this->assertEquals(false, BcUtil::includePluginClass('BcTest'));
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

        foreach($cacheList as $path => $cacheName) {
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
        foreach($cacheList as $cacheName) {
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
        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<
        $this->_getRequest($url);
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
            ['admin', true],
            ['admin/hoge', true],
            ['/admin/hoge', true],
            ['admin/', true],
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
     */
    public function testLoginUserGroup()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ログインしているユーザーのセッションキーを取得
     */
    public function testGetLoginUserSessionKey()
    {
        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        // セッションキーを未設定の場合
        $result = BcUtil::getLoginUserSessionKey();
        $this->assertEquals('User', $result, 'セッションキーを取得を正しく取得できません');

        // セッションキーを設定した場合
        BcAuthComponent::$sessionKey = 'Auth.Hoge';
        $result = BcUtil::getLoginUserSessionKey();
        $this->assertEquals($result, 'Hoge', 'セッションキーを取得を正しく取得できません');
    }


    /**
     * テーマ梱包プラグインのリストを取得する
     */
    public function testGetThemesPlugins()
    {

        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        $theme = Configure::read('BcSite.theme');
        $path = BASER_THEMES . $theme . DS . 'Plugin';

        // ダミーのプラグインディレクトリを削除
        $Folder = new Folder();
        $Folder->delete($path);

        // プラグインが存在しない場合
        $result = BcUtil::getThemesPlugins($theme);
        $expect = [];
        $this->assertEquals($expect, $result, 'テーマ梱包プラグインのリストを正しく取得できません');

        // プラグインが存在する場合
        // ダミーのプラグインディレクトリを作成
        $Folder->create($path . DS . 'dummy1');
        $Folder->create($path . DS . 'dummy2');

        $result = BcUtil::getThemesPlugins($theme);
        // ダミーのプラグインディレクトリを削除
        $Folder->delete($path);

        $expect = ['dummy1', 'dummy2'];
        $this->assertEquals($expect, $result, 'テーマ梱包プラグインのリストを正しく取得できません');
    }

    /**
     * 現在適用しているテーマ梱包プラグインのリストを取得する
     */
    public function testGetCurrentThemesPlugins()
    {

        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        $theme = Configure::read('BcSite.theme');
        $path = BASER_THEMES . $theme . DS . 'Plugin';
        $Folder = new Folder();
        $Folder->delete($path);
        $this->assertEquals([], BcUtil::getCurrentThemesPlugins(), '現在適用しているテーマ梱包プラグインのリストを正しく取得できません。');
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
        $result = BcUtil::getSchemaPath('Blog');
        $this->assertEquals(BASER_PLUGINS . 'Blog/Config/Schema', $result, 'プラグインのスキーマ情報のパスを正しく取得できません');
    }

    /**
     * 初期データのパスを取得する
     *
     * 初期データのフォルダは アンダースコア区切り推奨
     *
     * @param string $plugin プラグイン名
     * @param string $theme テーマ名
     * @param string $pattern 初期データの類型
     * @param string $expect 期待値
     * @dataProvider getDefaultDataPathDataProvider
     */
    public function testGetDefaultDataPath($plugin, $theme, $pattern, $expect)
    {
        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        $isset_ptt = isset($pattern) && isset($theme);
        $isset_plt = isset($plugin) && isset($theme);
        $isset_plptt = isset($plugin) && isset($pattern) && isset($theme);
        $Folder = new Folder();

        // 初期データ用のダミーディレクトリを作成
        if ($isset_ptt) {
            $Folder->create(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern);
        }
        if ($isset_plt && !$isset_plptt) {
            $Folder->create(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default' . DS . $plugin);
        }
        if ($isset_plptt) {
            $Folder->create(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin);
        }

        $result = BcUtil::getDefaultDataPath($plugin, $theme, $pattern);

        // 初期データ用のダミーディレクトリを削除
        if ($isset_ptt) {
            $Folder->delete(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern);
        }
        if ($isset_plt && !$isset_plptt) {
            $Folder->delete(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . 'default' . DS . $plugin);
        }
        if ($isset_plptt) {
            $Folder->delete(BASER_THEMES . $theme . DS . 'Config' . DS . 'data' . DS . $pattern . DS . $plugin);
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
        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        return [[null, null, null, '']];
        // <<<
        return [
            [null, null, null, BASER_CONFIGS . 'data/default'],
            [null, 'nada-icons', null, BASER_THEMES . 'nada-icons/Config/data/default'],
            [null, 'nada-icons', 'not_default', BASER_THEMES . 'nada-icons/Config/data/not_default'],
            ['Blog', null, null, BASER_PLUGINS . 'Blog/Config/data/default'],
            ['Blog', 'nada-icons', null, BASER_THEMES . 'nada-icons/Config/data/default/Blog'],
            ['Blog', 'nada-icons', 'not_default', BASER_THEMES . 'nada-icons/Config/data/not_default/Blog'],
        ];
    }

    /**
     * シリアライズ / アンシリアライズ
     */
    public function testSerialize()
    {

        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

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
     * URL用に文字列を変換する
     *
     * できるだけ可読性を高める為、不要な記号は除外する
     */
    public function testUrlencode()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コンソールから実行されてるかどうかチェックする
     *
     *
     */
    public function testIsConsole()
    {
        // テストはCliから実行するためtrue
        $this->assertTrue(BcUtil::isConsole());
    }

    /**
     * レイアウトテンプレートのリストを取得する
     */
    public function testGetTemplateList()
    {
        $result = BcUtil::getTemplateList('Admin/element/Dashboard', 'BaserCore', 'BcAdminThird');
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

        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        $themes = BcUtil::getAllThemeList();
        $this->assertTrue(in_array('nada-icons', $themes));
        $this->assertTrue(in_array('admin-third', $themes));
    }

    /**
     * テーマリストを取得する
     */
    public function testGetThemeList()
    {

        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        $themes = BcUtil::getThemeList();
        $this->assertTrue(in_array('nada-icons', $themes));
        $this->assertFalse(in_array('admin-third', $themes));
    }

    /**
     * 管理画面用のテーマリストを取得する
     */
    public function testGetAdminThemeList()
    {

        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

        $themes = BcUtil::getAdminThemeList();
        $this->assertFalse(in_array('nada-icons', $themes));
        $this->assertTrue(array_key_exists('admin-third', $themes));
    }

    /**
     * 指定したURLのドメインを取得する
     */
    public function testGetDomain()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * メインとなるドメインを取得する
     */
    public function testGetMainDomain()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 管理画面用のプレフィックスを取得する
     */
    public function testGetAdminPrefix()
    {
        $result = BcUtil::getAdminPrefix();
        $this->assertEquals('/admin', $result);
    }

    /**
     * 
     * baserコア用のプレフィックスを取得する
     */
    public function testGetBaserCorePrefix()
    {
        $result = BcUtil::getBaserCorePrefix();
        $this->assertEquals('/baser', $result);
    }

    /**
     * 
     * プレフィックス全体を取得する
     */
    public function testGetPrefix()
    {
        $result = BcUtil::getPrefix();
        $this->assertEquals('/baser/admin', $result);
    }


    /**
     * 現在のドメインを取得する
     */
    public function testGetCurrentDomain()
    {

        // TODO ucmitz移行時に未実装のため代替措置
        // >>>
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // <<<

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
        $this->assertEquals(ROOT . '/plugins/BcSample/', BcUtil::getPluginPath('BcSample'));
    }

    /**
     * testGetPluginDir
     */
    public function testGetPluginDir()
    {
        $this->assertEquals('baser-core', BcUtil::getPluginDir('BaserCore'));
        $this->assertEquals('bc-blog', BcUtil::getPluginDir('BcBlog'));
        $this->assertEquals('BcSample', BcUtil::getPluginDir('BcSample'));
    }

}
