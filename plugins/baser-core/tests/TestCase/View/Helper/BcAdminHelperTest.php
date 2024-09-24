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

use BaserCore\Middleware\BcAdminMiddleware;
use BaserCore\Service\Admin\BcAdminAppServiceInterface;
use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcAdminHelper;
use BaserCore\View\Helper\BcPageHelper;
use Cake\Core\Configure;
use Cake\Http\ServerRequest;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

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

    use ScenarioAwareTrait;

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
        $this->loadFixtureScenario(InitAppScenario::class);
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
        //データー生成
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->loginAdmin($this->getRequest('/baser/admin'));
        $this->BcAdmin->getView()->setRequest($request);

        //対象メソッドを実行
        $adminMenuGroups = $this->execPrivateMethod($this->BcAdmin, 'getAdminMenuGroups');
        $covertedAdminMenuGroups = $this->execPrivateMethod($this->BcAdmin, 'convertAdminMenuGroups', [$adminMenuGroups]);

        //戻り値を確認
        $this->assertEquals('ダッシュボード', $covertedAdminMenuGroups[0]['title']);
        $this->assertEquals('/baser/admin', $covertedAdminMenuGroups[0]['url']);
        $this->assertEquals('dashboard', $covertedAdminMenuGroups[0]['type']);
        $this->assertEquals('bca-icon--file', $covertedAdminMenuGroups[0]['icon']);
    }

    /**
     * Test getJsonMenu
     * @return void
     */
    public function testGetJsonMenu(): void
    {
        $this->loadFixtureScenario(InitAppScenario::class);
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
        $this->loadFixtureScenario(InitAppScenario::class);
        UserFactory::make(['id' => 2])->persist();

        $this->BcAdmin->getView()->set($this->getService(BcAdminAppServiceInterface::class)->getViewVarsForAll());
        $this->BcAdmin->getView()->setRequest($this->getRequest('/baser/admin'));
        // ヘルプなし 未ログイン
        $expected = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => false,
            'isLogin' => false,
            'isAdminUser' => false
        ]);
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actual = ob_get_clean();
        $this->assertEquals($expected, $actual);

        // ヘルプあり 未ログイン
        $expectedIsHelp = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => true,
            'isLogin' => false,
            'isAdminUser' => false
        ]);
        $this->BcAdmin->getView()->set('help', 'test');
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsHelp = ob_get_clean();
        $this->assertEquals($expectedIsHelp, $actualIsHelp);

        $session = $this->BcAdmin->getView()->getRequest()->getSession();
        $session->write('AuthAdmin', true);

        // ヘルプなし ログイン済 管理ユーザー以外
        $this->BcAdmin->getView()->setRequest($this->loginAdmin($this->getRequest('/baser/admin'), 2));
        $expectedIsLogin = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => false,
            'isLogin' => true,
            'isAdminUser' => false
        ]);
        $this->BcAdmin->getView()->set('help', null);
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsLogin = ob_get_clean();
        $this->assertEquals($expectedIsLogin, $actualIsLogin);

        // ヘルプあり ログイン済 管理ユーザー以外
        $this->BcAdmin->getView()->setRequest($this->loginAdmin($this->getRequest('/baser/admin'), 2));
        $expectedIsHelpIsLogin = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => true,
            'isLogin' => true,
            'isAdminUser' => false
        ]);
        $this->BcAdmin->setHelp('test');
        ob_start();
        $this->BcAdmin->contentsMenu();
        $actualIsHelpIsLogin = ob_get_clean();
        $this->assertEquals($expectedIsHelpIsLogin, $actualIsHelpIsLogin);

        // ヘルプあり ログイン済 管理ユーザー
        $this->BcAdmin->getView()->setRequest($this->loginAdmin($this->getRequest('/baser/admin')));
        $expectedIsSuperUser = $this->BcAdmin->getView()->element('contents_menu', [
            'isHelp' => true,
            'isLogin' => true,
            'isAdminUser' => true
        ]);
        $needText = '<div id="PermissionDialog" title="アクセスルール登録" hidden>';
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
        $this->loadFixtureScenario(InitAppScenario::class);
        // 存在しない
        $request = $this->loginAdmin($this->getRequest('/hoge'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('editLink', '');
        $this->assertEquals(false, $this->BcAdmin->existsEditLink());
        // 存在する
        $request = $this->loginAdmin($this->getRequest('/baser/admin'));
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
        $this->loadFixtureScenario(InitAppScenario::class);
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
     * test existsAddLink
     */
    public function testExistsAddLink()
    {
        //データを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFactory::make(['type' => 'ContentFolder', 'url' => '/service'])->persist();
        ContentFactory::make(['type' => 'ContentLink', 'url' => '/service-1'])->persist();

        //isAdminSystem = true, return false
        $request = $this->loginAdmin($this->getRequest('/baser/admin/baser-core/pages/edit/2'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->assertFalse($this->BcAdmin->existsAddLink());

        //isAdminSystem = false, return false
        $this->BcAdmin->getView()->setRequest($this->getRequest('/'));
        $this->assertFalse($this->BcAdmin->existsAddLink());

        //isAdminSystem = true && type !== ContentFolder, return false
        $request = $this->loginAdmin($this->getRequest('/service-1'));
        $request->getSession()->write('AuthAdmin', UserFactory::get(1));
        $this->BcAdmin->getView()->setRequest($request);
        $this->assertFalse($this->BcAdmin->existsAddLink());

        //isAdminSystem = true && type == ContentFolder, return true
        $request = $this->loginAdmin($this->getRequest('/service'));
        $request->getSession()->write('AuthAdmin', UserFactory::get(1));
        $this->BcAdmin->getView()->setRequest($request);
        $this->assertTrue($this->BcAdmin->existsAddLink());
    }

    /**
     * test addLink
     */
    public function testAddLink()
    {
        //データを生成
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFactory::make(['type' => 'ContentFolder', 'url' => '/service'])->persist();

        //isAdminSystem = true, return ''
        $this->BcAdmin->getView()->setRequest($this->loginAdmin($this->getRequest('/baser/admin/baser-core/pages/edit/2')));
        ob_start();
        $this->BcAdmin->addLink();
        $actualEmpty = ob_get_clean();
        $this->assertEmpty($actualEmpty);

        //$content == null, return ''
        $this->BcAdmin->getView()->setRequest($this->getRequest('/service-1'));
        ob_start();
        $this->BcAdmin->addLink();
        $actualEmpty = ob_get_clean();
        $this->assertEmpty($actualEmpty);

        //$content != null, 固定ページ新規追加画面へのリンクを出力する
        $this->BcAdmin->getView()->setRequest($this->getRequest('/service'));
        ob_start();
        $this->BcAdmin->addLink();
        $actualEmpty = ob_get_clean();
        $this->assertTextContains('新規ページ追加', $actualEmpty);
    }

    /**
     * 編集画面へのリンクを出力する
     *
     * @return void
     */
    public function testEditLink()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        // リンクなし
        $request = $this->loginAdmin($this->getRequest('/hoge'));
        $this->BcAdmin->getView()->setRequest($request);
        $this->BcAdmin->getView()->set('editLink', '');
        ob_start();
        $this->BcAdmin->editLink();
        $result = ob_get_clean();
        $this->assertEmpty($result);
        // リンクあり
        $request = $this->loginAdmin($this->getRequest('/baser/admin'));
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
        $this->loadFixtureScenario(InitAppScenario::class);
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
     * test firstAccess
     */
    public function testFirstAccess()
    {
        //controller == installations, return ''
        $request = $this->getRequest('/')->withParam('controller', 'installations');
        $this->BcAdmin->getView()->setRequest($request);
        ob_start();
        $this->BcAdmin->firstAccess();
        $actualEmpty = ob_get_clean();
        $this->assertEmpty($actualEmpty);

        //controller != installations, 初回アクセス時のメッセージ表示
        $this->BcAdmin->getView()->setRequest($this->getRequest('/baser/admin/baser-core/pages/edit/2'))->set('firstAccess', true);
        ob_start();
        $this->BcAdmin->firstAccess();
        $actualEmpty = ob_get_clean();
        $this->assertTextContains('baserCMSへようこそ', $actualEmpty);
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

    /**
     * test getCurrentSite
     */
    public function testGetCurrentSite()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        SiteFactory::make(['id' => '2', 'name' => 'smartphone'])->persist();

        // メインサイト
        $request = $this->getRequest('/baser/admin');
        $this->BcAdmin->getView()->setRequest($request);
        $entity = $this->BcAdmin->getCurrentSite();
        $this->assertNotNull($entity->title);

        // サブサイト
        $request = $this->getRequest('/baser/admin?site_id=2');
        $bcAdmin = new BcAdminMiddleware();
        /* @var ServerRequest $request */
        $request = $bcAdmin->setCurrentSite($request);
        $this->BcAdmin->getView()->setRequest($request);
        $entity = $this->BcAdmin->getCurrentSite();
        $this->assertEquals('smartphone', $entity->name);

        // フロントページの場合
        $request = $this->getRequest('/');
        $this->BcAdmin->getView()->setRequest($request);
        $this->assertFalse($this->BcAdmin->getCurrentSite());
    }

}
