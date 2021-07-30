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
use Cake\Core\Configure;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcAdminHelper;

/**
 * Class BcAdminHelperTest
 * @package BaserCore\Test\TestCase\View\Helper
 * @property BcAdminHelper $BcAdmin
 */
class BcAdminHelperTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.Dblogs',
    ];

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $BcAdminAppView = new BcAdminAppView($this->getRequest()->withParam('controller', 'users'));
        $BcAdminAppView->setTheme('BcAdminThird');
        $this->BcAdmin = new BcAdminHelper($BcAdminAppView);
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdmin);
        parent::tearDown();
    }

    /**
     * Test isAvailableSideBar
     *
     * @return void
     */
    public function testIsAvailableSideBar()
    {
        // 未ログイン
        $results = $this->BcAdmin->isAvailableSideBar();
        $this->assertEquals(false, $results);
        // ログイン済
        $this->BcAdmin->getView()->setRequest($this->getRequest('/baser/admin'));
        $session = $this->BcAdmin->getView()->getRequest()->getSession();
        $session->write('AuthAdmin', true);
        $results = $this->BcAdmin->isAvailableSideBar();
        $this->assertEquals(true, $results);
        // ログイン画面
        $this->BcAdmin->getView()->setRequest($this->getRequest('/baser/admin/users/login'));
        $results = $this->BcAdmin->isAvailableSideBar();
        $this->assertEquals(false, $results);
        $results = $this->BcAdmin->isAvailableSideBar();
        $this->assertEquals(false, $results);
    }


    /**
     * Test setTitle
     *
     * @return void
     */
    public function testSetTitle()
    {
        $title = 'test';
        $this->BcAdmin->setTitle($title);
        $this->assertEquals($title, $this->BcAdmin->getView()->get('title'));
    }

    /**
     * Test setHelp
     *
     * @return void
     */
    public function testSetHelp()
    {
        $help = 'test';
        $this->BcAdmin->setHelp($help);
        $this->assertEquals($help, $this->BcAdmin->getView()->get('help'));
    }

    /**
     * Test setSearch
     *
     * @return void
     */
    public function testSetSearch()
    {
        $search = 'test';
        $this->BcAdmin->setSearch($search);
        $this->assertEquals($search, $this->BcAdmin->getView()->get('search'));
    }

    /**
     * Test title
     *
     * @return void
     */
    public function testTitle()
    {
        ob_start();
        $this->BcAdmin->title();
        $actualEmpty = ob_get_clean();
        $this->assertEmpty($actualEmpty);

        $title = 'test';
        $this->BcAdmin->getView()->assign('title', $title);
        ob_start();
        $this->BcAdmin->title();
        $actual = ob_get_clean();
        $this->assertEquals($title, $actual);
    }

    /**
     * Test help
     *
     * @return void
     */
    public function testHelp()
    {
        $this->BcAdmin->getView()->setRequest($this->getRequest('/baser/admin'));
        ob_start();
        $this->BcAdmin->help();
        $actualEmpty = ob_get_clean();
        $this->assertEmpty($actualEmpty);

        $help = 'users_index';
        $expected = $this->BcAdmin->getView()->element('help', ['help' => $help]);

        $this->BcAdmin->getView()->set('help', $help);
        ob_start();
        $this->BcAdmin->help();
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);
    }

    /**
     * Test search
     * @return void
     */
    public function testSearch()
    {
        $this->BcAdmin->getView()->setRequest($this->getRequest('/baser/admin'));
        $this->loadRoutes();
        ob_start();
        $this->BcAdmin->search();
        $actual = ob_get_clean();
        $this->assertEmpty($actual);

        $this->BcAdmin->getView()->set('search', 'users_index');
        ob_start();
        $this->BcAdmin->search();
        $actual = ob_get_clean();
        $this->assertRegExp('/class="bca-search">(.*)<form/s', $actual);
    }

    /**
     * Test getAdminMenuGroups
     * @return void
     */
    public function testGetAdminMenuGroups(): void
    {
        $adminMenuGroups = $this->execPrivateMethod($this->BcAdmin, 'getAdminMenuGroups');
        // それぞれのメニューキーを持つか
        $this->assertArrayHasKey('Dashboard', $adminMenuGroups);
        $this->assertArrayHasKey('Users', $adminMenuGroups);
        $this->assertArrayHasKey('Plugin', $adminMenuGroups);
        // adminNavigationがない場合
        Configure::write('BcApp.adminNavigation', null);
        $adminMenuGroups = $this->execPrivateMethod($this->BcAdmin, 'getAdminMenuGroups');
        $this->assertFalse($adminMenuGroups);
    }

    /**
     * Test convertAdminMenuGroups
     * @return void
     */
    public function testConvertAdminMenuGroups(): void
    {
        // $adminMenuGroups = $this->BcAdmin->getAdminMenuGroups();
        // $covertedAdminMenuGroups = $this->BcAdmin->convertAdminMenuGroups($adminMenuGroups);
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test getJsonMenu
     * @return void
     */
    public function testGetJsonMenu(): void
    {
        $jsonMenu = json_decode($this->BcAdmin->getJsonMenu());
        // $currentSiteIdのテスト
        $this->assertEquals($jsonMenu->currentSiteId, "0");
        // $adminMenuGroupsの取得
        $adminMenuGroups = $this->execPrivateMethod($this->BcAdmin, 'getAdminMenuGroups');
        // $menuListに項目が入ってるかテスト
        foreach($jsonMenu->menuList as $menuList) {
            $this->assertContains($menuList->name, array_keys($adminMenuGroups));
        }
        // adminNavigationがnullの場合 nullが返る
        Configure::write('BcApp.adminNavigation', null);
        $this->assertNull(json_decode($this->BcAdmin->getJsonMenu()));

    }

    /**
     * Test contentsMenu
     *
     * @return void
     */
    public function testContentsMenu()
    {
        $this->BcAdmin->getView()->setRequest($this->getRequest('/baser/admin'));
        // ヘルプなし 未ログイン
        $expected = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => false,
            'isLogin' => false
        ]);
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);

        // ヘルプあり 未ログイン
        $expectedIsHelp = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => true,
            'isLogin' => false
        ]);
        $this->BcAdmin->getView()->set('help', 'test');
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsHelp = ob_get_clean();
        $this->assertEquals($expectedIsHelp, $actualIsHelp);

        $session = $this->BcAdmin->getView()->getRequest()->getSession();
        $session->write('AuthAdmin', true);

        // ヘルプなし ログイン済
        $expectedIsLogin = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => false,
            'isLogin' => true
        ]);
        $this->BcAdmin->getView()->set('help', null);
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsLogin = ob_get_clean();
        $this->assertEquals($expectedIsLogin, $actualIsLogin);

        // ヘルプあり ログイン済
        $expectedIsHelpIsLogin = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => true,
            'isLogin' => true
        ]);
        $this->BcAdmin->setHelp('test');
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsHelpIsLogin = ob_get_clean();
        $this->assertEquals($expectedIsHelpIsLogin, $actualIsHelpIsLogin);
    }

    /**
     * Test addAdminMainBodyHeaderLinks
     *
     * @return void
     */
    public function testAddAdminMainBodyHeaderLinks(): void
    {
        $expected = ['url' => 'test', 'confirm' => 'confirm message', 'something attributes' => 'attr value'];
        $this->BcAdmin->addAdminMainBodyHeaderLinks($expected);
        $result = $this->BcAdmin->getView()->get('mainBodyHeaderLinks');
        $this->assertEquals($expected, array_pop($result));
    }

    /**
     * 管理システムグローバルメニューの利用可否確認
     *
     * @param mixed $admin request->params['admin']の値
     * @param int $groupId ユーザーグループID
     * @param boolean $expected 期待値
     * @param string $message テストが失敗した場合に表示されるメッセージ
     * @dataProvider isAdminGlobalmenuUsedDataProvider
     */
    public function testIsAdminGlobalmenuUsed($admin, $groupId, $expected, $message = null)
    {
        $this->markTestIncomplete('Not implemented yet.');
        // TODO : 要コード確認
        /* >>>
		$this->BcAdmin->request = $this->BcAdmin->request->withParam('admin',  $admin);
		$this->BcAdmin->_View->viewVars['user'] = [
			'user_group_id' => $groupId
		];

		$result = $this->BcAdmin->isAdminGlobalmenuUsed();
		$this->assertEquals($expected, $result, $message);
        <<< */
    }

    public function isAdminGlobalmenuUsedDataProvider()
    {
        return [
            ['', null, false, '管理システムグローバルメニューの利用可否確認が正しくありません'],
            [1, null, false, '管理システムグローバルメニューの利用可否確認が正しくありません'],
            ['', 1, true, '管理システムグローバルメニューの利用可否確認が正しくありません'],
            ['1', 1, true, '管理ユーザーの管理システムグローバルメニューの利用可否確認が正しくありません'],
            ['1', 2, 0, '運営ユーザーの管理システムグローバルメニューの利用可否確認が正しくありません'],
        ];
    }

    /**
     * testIsSystemAdmin method
     *
     * @param mixed $admin request->params['admin']の値
     * @param int $groupId ユーザーグループID
     * @param boolean $expected 期待値
     * @param string $message テストが失敗した場合に表示されるメッセージ
     * @dataProvider isSystemAdminDataProvider
     */
    public function testIsSystemAdmin($admin, $groupId, $expected, $message = null)
    {
        $this->markTestIncomplete('Not implemented yet.');
        // TODO : 要コード確認
        /* >>>
        $this->BcAdmin->request = $this->BcAdmin->request->withParam('admin',  $admin);
        $this->BcAdmin->_View->viewVars['user'] = [
            'user_group_id' => $groupId
        ];

        $result = $this->BcAdmin->isSystemAdmin();
        $this->assertEquals($expected, $result, $message);
        <<< */
    }

    public function isSystemAdminDataProvider()
    {
        return [
            ['', null, false, 'ログインユーザーのシステム管理者チェックが正しくありません'],
            [1, null, false, 'ログインユーザーのシステム管理者チェックが正しくありません'],
            ['', 1, false, 'ログインユーザーのシステム管理者チェックが正しくありません'],
            ['1', 1, true, '管理ユーザーのシステム管理者チェックが正しくありません'],
            ['1', 2, false, '運営ユーザーのシステム管理者チェックが正しくありません'],
        ];
    }

    /**
     * Test getDblogs
     */
    public function testGetDblogs()
    {
        $dblogs = $this->BcAdmin->getDblogs(2);
        $this->assertEquals(2, count($dblogs));
    }
}
