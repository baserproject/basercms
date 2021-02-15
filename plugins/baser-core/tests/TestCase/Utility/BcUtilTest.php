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

class BcUtilTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.Plugins',
    ];

    /**
     * Test loginUser
     * @dataProvider loginUserDataProvider
     */
    public function testLoginUser($isLogin, $expects)
    {
        $this->getRequest();
        if($isLogin) {
            $this->loginAdmin();
        }
        $result = BcUtil::loginUser();
        if($result) {
            $result = $result->toArray()[0]->id;
        }
        $this->assertEquals($expects, $result);
    }

    public function loginUserDataProvider() {
        return [
            // ログインしている状況
            [true, 1],
            // ログインしていない状況
            [false, null]
        ];
    }

    /**
     * Test isSuperUser
     */
    public function testIsSuperUser()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isAgentUser
     */
    public function testIsAgentUser()
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test isInstallMode
     */
    public function testIsInstallMode()
    {
        $this->markTestIncomplete('Not implemented yet.');
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

}
