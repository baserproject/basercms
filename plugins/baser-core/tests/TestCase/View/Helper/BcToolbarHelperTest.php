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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcToolbarHelper;
use Cake\Core\Configure;
use Cake\View\View;

/**
 * Class BcToolbarHelperTest
 */
class BcToolbarHelperTest extends BcTestCase
{
    /**
     * BcToolbarHelper
     * @var BcToolbarHelper
     */
    public $BcToolbar;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];

//    public $autoFixtures = false;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcToolbar = new BcToolbarHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcToolbar);
        parent::tearDown();
    }

    /**
     * test isAvailableEditLink
     */
    public function testIsAvailableEditLink()
    {
        // editLink 設定なし
        $this->assertFalse($this->BcToolbar->isAvailableEditLink());
        $this->BcToolbar->getView()->set('editLink', 'test');
        // editLink 設定あり
        $this->assertFalse($this->BcToolbar->isAvailableEditLink());
        // ログイン完了
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertTrue($this->BcToolbar->isAvailableEditLink());
        // プレビュー
        $this->BcToolbar->getView()->setRequest($this->getRequest('/?preview=true'));
        $this->assertFalse($this->BcToolbar->isAvailableEditLink());
    }

    /**
     * test isAvailableEditLink
     */
    public function testIsAvailablePublishLink()
    {
        // publishLink 設定なし
        $this->assertFalse($this->BcToolbar->isAvailablePublishLink());
        $this->BcToolbar->getView()->set('publishLink', 'test');
        // publishLink 設定あり
        $this->assertFalse($this->BcToolbar->isAvailablePublishLink());
        // ログイン完了
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertTrue($this->BcToolbar->isAvailablePublishLink());
    }

    /**
     * test isAvailableMode
     */
    public function testisAvailableMode()
    {
        $debug = Configure::read('debug');
        Configure::write('debug', true);
        $this->assertTrue($this->BcToolbar->isAvailableMode());
        Configure::write('debug', false);
        $this->assertFalse($this->BcToolbar->isAvailableMode());
        Configure::write('debug', $debug);
    }

    /**
     * test isAvailableClearCache
     */
    public function testIsAvailableClearCache()
    {
        $this->assertFalse($this->BcToolbar->isAvailableClearCache());
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertTrue($this->BcToolbar->isAvailableClearCache());
    }

    /**
     * test isAvailableBackAgent
     */
    public function testIsAvailableBackAgent()
    {
        $this->assertFalse($this->BcToolbar->isAvailableBackAgent());
        $session = $this->getRequest()->getSession();
        $session->write('AuthAgent', ['test']);
        $this->assertTrue($this->BcToolbar->isAvailableBackAgent());
    }

    /**
     * test isAvailableLogin
     */
    public function testIsAvailableLogin()
    {
        $this->assertTrue($this->BcToolbar->isAvailableLogin());
        // インストーラーの場合
        $toolbar = new BcToolbarHelper(new View(null, null, null, ['name' => 'Installations']));
        $this->assertFalse($toolbar->isAvailableLogin());
        // アップデーターの場合
        Configure::write('BcRequest.isUpdater', true);
        $this->assertFalse($this->BcToolbar->isAvailableLogin());
        Configure::write('BcRequest.isUpdater', false);
        // ログイン画面の場合
        $toolbar = new BcToolbarHelper(new View($this->getRequest('/baser/admin/baser-core/users/login')));
        $this->assertFalse($toolbar->isAvailableLogin());
        // ログイン済の場合
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertFalse($this->BcToolbar->isAvailableLogin());
    }

    /**
     * test isAvailableAccountSetting
     */
    public function testIsAvailableAccountSetting()
    {
        $this->assertFalse($this->BcToolbar->isAvailableAccountSetting());
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertTrue($this->BcToolbar->isAvailableAccountSetting());
    }

    /**
     * test getAccountSettingUrl
     * TODO プレフィックスを変更した場合のテスト追加要（mypageなど）
     */
    public function testGetAccountSettingUrl()
    {
        $this->assertEquals('', $this->BcToolbar->getAccountSettingUrl());
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertEquals(['plugin' => 'BaserCore', 'prefix' => 'Admin', 'controller' => 'Users', 'action' => 'edit', 1], $this->BcToolbar->getAccountSettingUrl());
    }

    /**
     * test getLoginUrl
     * TODO プレフィックスを変更した場合のテスト追加要（mypageなど）
     */
    public function testGetLoginUrl()
    {
        $this->assertEquals('', $this->BcToolbar->getLoginUrl());
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertEquals('/baser/admin/baser-core/users/login', $this->BcToolbar->getLoginUrl());
    }

    /**
     * test getLogoutUrl
     */
    public function testGetLogoutUrl()
    {
        $this->assertEquals('', $this->BcToolbar->getLogoutUrl());
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertEquals([
            'prefix' => 'Admin',
            'plugin' => 'BaserCore',
            'controller' => 'Users',
            'action' => 'logout'
        ], $this->BcToolbar->getLogoutUrl());
    }

    /**
     * test getMode
     */
    public function testGetMode()
    {
        $debug = Configure::read('debug');
        // デバッグ
        Configure::write('debug', true);
        $this->assertEquals('debug', $this->BcToolbar->getMode());
        // インストール
        Configure::write('debug', false);
        $_SERVER['INSTALL_MODE'] = "true";
        $this->assertEquals('install', $this->BcToolbar->getMode());
        // なし
        $_SERVER['INSTALL_MODE'] = "false";
        $this->assertEquals('', $this->BcToolbar->getMode());
        Configure::write('debug', $debug);
    }

    /**
     * test getModeTitle
     */
    public function testGetModeTitle()
    {
        $debug = Configure::read('debug');
        // デバッグ
        Configure::write('debug', true);
        $this->assertEquals('デバッグモード', $this->BcToolbar->getModeTitle());
        // インストール
        Configure::write('debug', false);
        $_SERVER['INSTALL_MODE'] = "true";
        $this->assertEquals('インストールモード', $this->BcToolbar->getModeTitle());
        // なし
        $_SERVER['INSTALL_MODE'] = "false";
        $this->assertEquals('', $this->BcToolbar->getModeTitle());
        Configure::write('debug', $debug);
    }

    /**
     * test getModeDescription
     */
    public function testGetModeDescription()
    {
        $debug = Configure::read('debug');
        // デバッグ
        Configure::write('debug', true);
        $this->assertMatchesRegularExpression('/デバッグモードです。/', $this->BcToolbar->getModeDescription());
        // インストール
        Configure::write('debug', false);
        $_SERVER['INSTALL_MODE'] = "true";
        $this->assertMatchesRegularExpression('/インストールモードです。/', $this->BcToolbar->getModeDescription());
        // なし
        $_SERVER['INSTALL_MODE'] = "false";
        $this->assertEquals('', $this->BcToolbar->getModeDescription());
        Configure::write('debug', $debug);
    }

    /**
     * test isLoginUrl
     */
    public function testIsLoginUrl()
    {
        $this->assertFalse($this->BcToolbar->isLoginUrl());
        $bcToolbar = new BcToolbarHelper(new View($this->getRequest('/baser/admin/baser-core/users/login')));
        $this->assertTrue($bcToolbar->isLoginUrl());
    }

    /**
     * test getLogoType
     */
    public function testGetLogoType()
    {
        // フロントで管理画面利用不可
        $this->assertEquals('frontAdminNotAvailable', $this->BcToolbar->getLogoType());
        // フロントで管理画面利用可能
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertEquals('frontAdminAvailable', $this->BcToolbar->getLogoType());
        // ノーマル
        $bcToolbar = new BcToolbarHelper(new View($this->getRequest('/baser/admin/baser-core/users/login')));
        $this->assertEquals('normal', $bcToolbar->getLogoType());
        // アップデーター
        Configure::write('BcRequest.isUpdater', true);
        $this->assertEquals('update', $bcToolbar->getLogoType());
        Configure::write('BcRequest.isUpdater', false);
        // インストーラー
        $bcToolbar = new BcToolbarHelper(new View(null, null, null, ['name' => 'Installations']));
        $this->assertEquals('install', $bcToolbar->getLogoType());
    }

    /**
     * test getLogoLink
     */
    public function testGetLogoLink()
    {
        // フロントで管理画面利用不可
        $this->assertEquals('', $this->BcToolbar->getLogoLink());
        // フロントで管理画面利用可能
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertEquals(['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'dashboard', 'action' => 'index'], $this->BcToolbar->getLogoLink());
        // ノーマル
        $bcToolbar = new BcToolbarHelper(new View($this->getRequest('/baser/admin/baser-core/users/login')));
        $this->assertEquals('https://localhost/', $bcToolbar->getLogoLink());
        // アップデーター
        Configure::write('BcRequest.isUpdater', true);
        $this->assertEquals('https://wiki.basercms.net/%E3%83%90%E3%83%BC%E3%82%B8%E3%83%A7%E3%83%B3%E3%82%A2%E3%83%83%E3%83%97%E3%82%AC%E3%82%A4%E3%83%89', $bcToolbar->getLogoLink());
        Configure::write('BcRequest.isUpdater', false);
        // インストーラー
        Configure::write('BcRequest.isInstalled', false);
        $bcToolbar = new BcToolbarHelper(new View(null, null, null, ['name' => 'Installations']));
        $this->assertEquals('https://wiki.basercms.net/%E3%82%A4%E3%83%B3%E3%82%B9%E3%83%88%E3%83%BC%E3%83%AB%E3%82%AC%E3%82%A4%E3%83%89', $bcToolbar->getLogoLink());
        Configure::write('BcRequest.isInstalled', true);
    }

    /**
     * test getLogoText
     */
    public function testGetLogoText()
    {
        // フロントで管理画面利用不可
        $this->assertEquals('baserCMS', $this->BcToolbar->getLogoText());
        // フロントで管理画面利用可能
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->assertEquals('ダッシュボード', $this->BcToolbar->getLogoText());
        // ノーマル
        $bcToolbar = new BcToolbarHelper(new View($this->getRequest('/baser/admin/baser-core/users/login')));
        $this->assertEquals('サイト表示', $bcToolbar->getLogoText());
        // アップデーター
        Configure::write('BcRequest.isUpdater', true);
        $this->assertEquals('アップデートマニュアル', $bcToolbar->getLogoText());
        Configure::write('BcRequest.isUpdater', false);
        // インストーラー
        $bcToolbar = new BcToolbarHelper(new View(null, null, null, ['name' => 'Installations']));
        $this->assertEquals('インストールマニュアル', $bcToolbar->getLogoText());
    }

    /**
     * test getLogoLinkOptions
     */
    public function testGetLogoLinkOptions()
    {
        // フロントで管理画面利用不可
        $this->assertEquals(['title' => 'baserCMS'], $this->BcToolbar->getLogoLinkOptions());
        // フロントで管理画面利用可能
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $options = $this->BcToolbar->getLogoLinkOptions();
        $this->assertTrue(in_array('bca-toolbar__logo-link', $options));
        // ノーマル
        $bcToolbar = new BcToolbarHelper(new View($this->getRequest('/baser/admin/baser-core/users/login')));
        $options = $bcToolbar->getLogoLinkOptions();
        $this->assertTrue(in_array('bca-toolbar__logo-link', $options));
        // アップデーター
        Configure::write('BcRequest.isUpdater', true);
        $options = $bcToolbar->getLogoLinkOptions();
        $this->assertTrue(in_array('bca-toolbar__logo-link', $options));
        $this->assertTrue(in_array('_blank', $options));
        Configure::write('BcRequest.isUpdater', false);
        // インストーラー
        $bcToolbar = new BcToolbarHelper(new View(null, null, null, ['name' => 'Installations']));
        $options = $bcToolbar->getLogoLinkOptions();
        $this->assertTrue(in_array('bca-toolbar__logo-link', $options));
        $this->assertTrue(in_array('_blank', $options));
    }

}
