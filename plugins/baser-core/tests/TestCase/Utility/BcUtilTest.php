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
use Cake\Core\Plugin;
use Cake\Filesystem\File;
use Cake\Filesystem\Folder;

/**
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
     * Test loginUser
     * @dataProvider loginUserDataProvider
     */
    public function testLoginUser($isLogin, $expects)
    {
        $this->getRequest();
        if ($isLogin) {
            $this->loginAdmin();
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
     * @dataProvider isSuperUserDataProvider
     */
    public function testIsSuperUser($id, $expects)
    {
        $this->getRequest();
        if ($id) {
            $this->loginAdmin($id);
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
     * @dataProvider isAgentUserDataProvider
     */
    public function testIsAgentUser($id, $expects)
    {

        $request = $this->getRequest();
        if ($id) {
            $user = $this->loginAdmin($id);
            $session = $request->getSession();
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
     * @dataProvider isInstallModeDataProvider
     */
    public function testIsInstallMode($mode, $expects)
    {
        $_SERVER["INSTALL_MODE"] = $mode;
        $result = BcUtil::isInstallMode();
        $this->assertEquals($expects, $result);
    }

    public function isInstallModeDataProvider()
    {
        return [
            // インストールモード On
            ['true', 'true'],
            // インストールモード Off
            ['false', 'false'],
        ];
    }

    /**
     * Test getVersion
     */
    public function testGetVersion()
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
     */
    public function testVerpoint()
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
     */
    public function testGetEnablePlugins()
    {
        $expects = ['BcBlog'];
        $result = BcUtil::getEnablePlugins();
        $this->assertEquals($expects, $result, 'プラグインの一覧が取得できません。');
    }

    /**
     * testIncludePluginClass
     */
    public function testIncludePluginClass()
    {
        $this->assertEquals(true, BcUtil::includePluginClass('BcBlog'));
        $this->assertEquals(false, BcUtil::includePluginClass('BcTest'));
    }

}
