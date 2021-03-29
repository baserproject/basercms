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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcAuthHelper;
use Cake\Core\Configure;

/**
 * Class BcAuthHelperTest
 * @package BaserCore\Test\TestCase\View\Helper
 * @property BcAuthHelper $BcAuth
 */
class BcAuthHelperTest extends BcTestCase {

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
    ];
    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        // adminの場合
        $BcAdminAppView = new BcAdminAppView();
        $BcAdminAppView->setRequest($this->getRequest()->withParam('prefix', 'Admin'));
        $this->BcAuth = new BcAuthHelper($BcAdminAppView);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminAppView);
        unset($this->BcAuth);
        parent::tearDown();
    }


    /**
     * Test getCurrentPrefix
     * @return void
     */
    public function testGetCurrentPrefix()
    {
        // adminの場合
        $result = $this->BcAuth->getCurrentPrefix();
        $this->assertEquals('Admin', $result);
        // その他の場合
        $this->BcAuth->getView()->setRequest($this->getRequest()->withParam('prefix', null));
        $result = $this->BcAuth->getCurrentPrefix();
        $this->assertEquals('front', $result);
    }

    /**
     * Test getCurrentPrefixSetting
     * @return void
     */
    public function testGetCurrentPrefixSetting()
    {
        // 管理画面の場合
        $result = $this->BcAuth->getCurrentPrefixSetting();
        $this->assertEquals($result['name'], "管理システム");
        // その他の場合
        $this->BcAuth->getView()->setRequest($this->getRequest()->withParam('prefix', null));
        $currentPrefix = $this->BcAuth->getCurrentPrefix();
        Configure::write('BcPrefixAuth.' . $currentPrefix,[]);
        $result = $this->BcAuth->getCurrentPrefixSetting();
        $this->assertEmpty($result);
    }

    /**
     * Test getCurrentLoginUrl
     * @return void
     */
    public function testGetCurrentLoginUrl()
    {
        // Adminの場合
        $expected = "/baser/admin/users/login";
        $result = $this->BcAuth->getCurrentLoginUrl();
        $this->assertEquals($expected, $result);
        // ログインページURLを変更した場合
        $expected = "/test/users/login";
        Configure::write('BcPrefixAuth.Admin.loginAction', $expected);
        $result = $this->BcAuth->getCurrentLoginUrl();
        $this->assertEquals($expected, $result);

    }
    /**
     * Test getCurrentUserPrefixSettings
     * @return void
     * @todo getCurrentUserPrefixSettings() の実装が完了したら別パターンのテストを追加する
     */
    public function testGetCurrentUserPrefixSettings()
    {
        $result = $this->BcAuth->getCurrentUserPrefixSettings();
        $this->assertEquals(['admin'], $result);
    }
    /**
     * Test isCurrentUserAdminAvailable
     * @return void
     */
    public function testIsCurrentUserAdminAvailable()
    {
        $result = $this->BcAuth->isCurrentUserAdminAvailable();
        $this->assertTrue($result);
    }
    /**
     * Test getCurrentLoginAction
     * @return void
     */
    public function testGetCurrentLoginAction()
    {
        $this->markTestIncomplete('テスト対象のメソッドが未実装です');
    }
    /**
     * Test getCurrentName
     * @return void
     */
    public function testGetCurrentName()
    {
        // prefix(admin)の場合
        $expected = Configure::read('BcPrefixAuth.Admin')['name'];
        $result = $this->BcAuth->getCurrentName();
        $this->assertEquals($expected, $result);
    }
    /**
     * Test isAdminLogin
     * @return void
     */
    public function testIsAdminLogin()
    {
        // ログインしない場合;
        $result = $this->BcAuth->isAdminLogin();
        $this->assertFalse($result);
        // ログインした場合
        $this->loginAdmin();
        $result = $this->BcAuth->isAdminLogin();
        $this->assertTrue($result);
    }
    /**
     * Test getCurrentLogoutUrl
     * @return void
     */
    public function testGetCurrentLogoutUrl()
    {
        // Adminの場合
        $expected = "/baser/admin/users/logout";
        $result = $this->BcAuth->getCurrentLogoutUrl();
        $this->assertEquals($expected, $result);
        // ログアウトページURLを変更した場合
        $expected = "/test/users/logout";
        Configure::write('BcPrefixAuth.Admin.logoutAction', $expected);
        $result = $this->BcAuth->getCurrentLogoutUrl();
        $this->assertEquals($expected, $result);
    }
    /**
     * Test getCurrentLoginRedirectUrl
     * @return void
     */
    public function testGetCurrentLoginRedirectUrl()
    {
        // Adminの場合
        $expected = "/baser/admin";
        $result = $this->BcAuth->getCurrentLoginRedirectUrl();
        $this->assertEquals($expected, $result);
        // 認証後リダイレクト先URLを変更した場合
        $expected = "/test/users/redirect";
        Configure::write('BcPrefixAuth.Admin.loginRedirect', $expected);
        $result = $this->BcAuth->getCurrentLoginRedirectUrl();
        $this->assertEquals($expected, $result);
    }
    /**
     * Test getCurrentLoginUser
     * @return void
     */
    public function testGetCurrentLoginUser()
    {
        $ids = [1, 2];
        foreach ($ids as $id) {
            $expected = $this->getUser($id);

            $this->loginAdmin($id);
            $result = $this->BcAuth->getCurrentLoginUser();
            $this->assertEquals($result, $expected);
        }
    }
    /**
     * Test isSuperUser
     * @dataProvider isSuperUserDataProvider
     * @return void
     */
    public function testIsSuperUser($id, $expected)
    {
        if($id) {
            $this->loginAdmin($id);
        }
        $result = $this->BcAuth->isSuperUser();
        $this->assertEquals($result, $expected);
    }
    public function isSuperUserDataProvider()
    {
        return [
            // ログインしない場合
            [null, false],
            // システム管理者の場合
            [1, true],
            // サイト運営者などそれ以外の場合
            [2, false],
        ];
    }

    /**
     * Test isAgentUser
     * @return void
     */
    public function testIsAgentUser()
    {
        // AuthAgent.Userが書き込まれてない場合
        $result = $this->BcAuth->isAgentUser();
        $this->assertFalse($result);
        // 書き込まれてる場合
        $request = $this->getRequest();
        $session = $request->getSession();
        $session->write('AuthAgent.User', "test");
        $result = $this->BcAuth->isAgentUser();
        $this->assertTrue($result);
    }
}
