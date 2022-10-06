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

use BaserCore\Service\Admin\BcAdminAppServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcAdminHelper;
use BaserCore\View\Helper\BcPageHelper;
use Cake\Core\Configure;

/**
 * Class BcAdminHelperTest
 * @property BcAdminHelper $BcAdmin
 */
class BcAdminHelperTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Permissions',
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
        $request = $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->BcAdmin->getView()->setRequest($request);
        $session = $this->BcAdmin->getView()->getRequest()->getSession();
        $session->write('AuthAdmin', true);
        $results = $this->BcAdmin->isAvailableSideBar();
        $this->assertEquals(true, $results);
        // ログイン画面
        $request = $this->getRequest('/baser/admin/users/login');
        $this->BcAdmin->getView()->setRequest($request);
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
        $this->assertEquals($title, $this->BcAdmin->getView()->fetch('title'));
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
        $this->assertMatchesRegularExpression('/class="bca-search">(.*)<form/s', $actual);
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
        // 未ログイン
        $result = $this->BcAdmin->getJsonMenu();
        $this->assertNull($result);

        // ログイン済
        $request = $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->BcAdmin->getView()->setRequest($request);
        $session = $this->BcAdmin->getView()->getRequest()->getSession();
        $session->write('AuthAdmin', true);

        $jsonMenu = json_decode($this->BcAdmin->getJsonMenu());

        // $currentSiteIdのテスト
        $this->assertEquals($jsonMenu->currentSiteId, 1);
        // $adminMenuGroupsの取得
        $adminMenuGroups = $this->execPrivateMethod($this->BcAdmin, 'getAdminMenuGroups');
        // $menuListに項目が入ってるかテスト
        foreach($jsonMenu->menuList as $menuList) {
            $this->assertContains($menuList->name, array_keys($adminMenuGroups));
        }
        // adminNavigationがnullの場合 nullが返る
        Configure::write('BcApp.adminNavigation', null);
        $this->assertNull($this->BcAdmin->getJsonMenu());

    }

    /**
     * Test contentsMenu
     *
     * @return void
     */
    public function testContentsMenu()
    {
        $this->BcAdmin->getView()->set($this->getService(BcAdminAppServiceInterface::class)->getViewVarsForAll());
        $this->BcAdmin->getView()->setRequest($this->getRequest('/baser/admin'));
        // ヘルプなし 未ログイン
        $expected = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => false,
            'isLogin' => false,
            'isSuperUser' => false
        ]);
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);

        // ヘルプあり 未ログイン
        $expectedIsHelp = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => true,
            'isLogin' => false,
            'isSuperUser' => false
        ]);
        $this->BcAdmin->getView()->set('help', 'test');
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsHelp = ob_get_clean();
        $this->assertEquals($expectedIsHelp, $actualIsHelp);

        $session = $this->BcAdmin->getView()->getRequest()->getSession();
        $session->write('AuthAdmin', true);

        // ヘルプなし ログイン済 スーパーユーザー以外
        $this->BcAdmin->getView()->setRequest($this->loginAdmin($this->getRequest('/baser/admin'), 2));
        $expectedIsLogin = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => false,
            'isLogin' => true,
            'isSuperUser' => false
        ]);
        $this->BcAdmin->getView()->set('help', null);
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsLogin = ob_get_clean();
        $this->assertEquals($expectedIsLogin, $actualIsLogin);

        // ヘルプあり ログイン済 スーパーユーザー以外
        $this->BcAdmin->getView()->setRequest($this->loginAdmin($this->getRequest('/baser/admin'), 2));
        $expectedIsHelpIsLogin = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => true,
            'isLogin' => true,
            'isSuperUser' => false
        ]);
        $this->BcAdmin->setHelp('test');
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsHelpIsLogin = ob_get_clean();
        $this->assertEquals($expectedIsHelpIsLogin, $actualIsHelpIsLogin);

        // ヘルプあり ログイン済 スーパーユーザー
        $this->BcAdmin->getView()->setRequest($this->loginAdmin($this->getRequest('/baser/admin')));
        $expectedIsSuperUser = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => true,
            'isLogin' => true,
            'isSuperUser' => true
        ]);
        $needText = '<div id="PermissionDialog" title="アクセス制限登録" hidden>';
        $this->assertNotFalse(strpos($expectedIsSuperUser, $needText));
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
     * test setEditLink
     */
    public function testSetEditLink()
    {
        $this->BcAdmin->setEditLink('aaa');
        $this->assertEquals('aaa', $this->BcAdmin->getView()->get('editLink'));
    }

    /**
     * test setPublishLink
     */
    public function testSetPublishLink()
    {
        $this->BcAdmin->setPublishLink('aaa');
        $this->assertEquals('aaa', $this->BcAdmin->getView()->get('publishLink'));
    }

    /**
     * 編集画面へのリンクが存在するかチェックする
     */
    public function testExistsEditLink()
    {
        // 存在しない
        $request = $this->loginAdmin($this->getRequest('/hoge'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('editLink', '');
        $this->assertEquals(false, $this->BcAdmin->existsEditLink());
        // 存在する
        $request = $this->loginAdmin($this->getRequest('/service/service1'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('editLink', 'hoge');
        new BcPageHelper($this->BcAdmin->getView());
        $this->assertEquals(true, $this->BcAdmin->existsEditLink());
    }

    /**
     * 公開ページへのリンクが存在するかチェックする
     * @return void
     */
    public function testExistsPublishLink()
    {
        // 存在しない
        $request = $this->loginAdmin($this->getRequest('/hoge'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('publishLink', '');
        $this->assertEquals(false, $this->BcAdmin->existsPublishLink());
        // 存在する
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/pages/edit/2'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('publishLink', 'test');
        $this->assertEquals(true, $this->BcAdmin->existsPublishLink());
    }

    /**
     * 編集画面へのリンクを出力する
     *
     * @return void
     */
    public function testEditLink()
    {
        // リンクなし
        $request = $this->loginAdmin($this->getRequest('/hoge'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('editLink', '');
        ob_start();
        $this->BcAdmin->editLink();
        $result = ob_get_clean();
        $this->assertEmpty($result);
        // リンクあり
        $request = $this->loginAdmin($this->getRequest('/service/service1'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('editLink', '/baser/admin/baser-core/pages/edit/5');
        ob_start();
        $this->BcAdmin->editLink();
        $result = ob_get_clean();
        $this->assertEquals('<a href="/baser/admin/baser-core/pages/edit/5" class="tool-menu">編集する</a>', $result);
    }

    /**
     * 公開ページへのリンクを出力する
     * @return void
     */
    public function testPublishLink()
    {
        // リンクなし
        $request = $this->loginAdmin($this->getRequest('/hoge'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('publishLink', '');
        ob_start();
        $this->BcAdmin->publishLink();
        $result = ob_get_clean();
        $this->assertEmpty($result);
        // リンクあり
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/pages/edit/2'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('publishLink', 'https://localhost/');
        ob_start();
        $this->BcAdmin->publishLink();
        $result = ob_get_clean();
        $this->assertEquals('<a href="https://localhost/" class="tool-menu">サイト確認</a>', $result);
    }

    /**
     * test getTitle
     * @return void
     */
    public function testGetTitle()
    {
        $title = 'test';
        $this->BcAdmin->setTitle($title);
        $rs = $this->BcAdmin->getTitle();

        $this->assertEquals($rs, $title);
    }
}
