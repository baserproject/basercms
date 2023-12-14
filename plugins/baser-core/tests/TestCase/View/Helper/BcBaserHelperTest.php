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

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\PageFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\PermissionsScenario;
use BaserCore\Test\Scenario\PluginsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UserGroupsScenario;
use BaserCore\Test\Scenario\UserScenario;
use BaserCore\Test\Scenario\UsersUserGroupsScenario;
use Cake\Http\Exception\NotFoundException;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BaserCore\Utility\BcFile;
use ReflectionClass;
use Cake\Event\Event;
use Cake\Core\Configure;
use Cake\Routing\Router;
use Cake\Event\EventManager;
use Cake\View\Helper\UrlHelper;
use Cake\View\Helper\HtmlHelper;
use Cake\View\Helper\FlashHelper;
use Cake\ORM\TableRegistry;
use BaserCore\View\BcFrontAppView;
use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcBaserHelper;


// use BaserCore\View\BcAdminAppView;
// use Cake\Core\Configure;

/**
 * Class BcBaserHelperTest
 * @property HtmlHelper $Html
 * @property BcBaserHelper $BcBaser
 * @property FlashHelper $Flash
 * @property UrlHelper $Url
 * @property BcAdminAppView $BcAdminAppView
 */
class BcBaserHelperTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * __construct
     * @param string $name
     * @param array $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
            $_SERVER['HTTP_USER_AGENT'] = 'iPhone';
    }

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(UserScenario::class);
        $this->loadFixtureScenario(UserGroupsScenario::class);
        $this->loadFixtureScenario(UsersUserGroupsScenario::class);
        $this->loadFixtureScenario(SitesScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
        $this->loadFixtureScenario(PermissionsScenario::class);
        $this->loadFixtureScenario(PluginsScenario::class);
        $this->BcAdminAppView = new BcAdminAppView($this->getRequest(), null, null, [
            'name' => 'Pages',
            'plugin' => 'BaserCore'
        ]);
        $this->BcBaser = new BcBaserHelper($this->BcAdminAppView);
        $this->Html = new HtmlHelper($this->BcAdminAppView);
        $this->Flash = new FlashHelper($this->BcAdminAppView);
        $this->Url = new UrlHelper($this->BcAdminAppView);
        $this->Contents = $this->getTableLocator()->get('BaserCore.Contents');

        // TODO: basercms4より移植
        // $this->_View = new BcAppView();
        // $this->_View->request = $this->_getRequest('/');
        // $SiteConfig = ClassRegistry::init('SiteConfig');
        // $siteConfig = $SiteConfig->findExpanded();
        // $this->_View->set('widgetArea', $siteConfig['widget_area']);
        // $this->_View->set('siteConfig', $siteConfig);
        // $this->_View->helpers = ['BcBaser'];
        // $this->_View->loadHelpers();
        // $this->BcBaser = $this->_View->BcBaser;


    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminAppView, $this->BcBaser, $this->Html, $this->Flash, $this->Url, $this->Contents);
        Router::reload();
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcBaser->PermissionsService);
    }

    /**
     * Test BcBaser->jsが適切な<script>を取得できているかテスト
     * @return void
     * @todo basercms4系を統合する
     */
    public function testJs()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * JSの読み込みタグを出力する
     * @param string $expected 期待値
     * @param string $url URL
     * @return void
     * @dataProvider jsDataProvider
     */
    public function testJs_Version4($expected, $url)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString($expected);
        $this->BcBaser->js($url);
    }

    public function jsDataProvider()
    {
        return [
            ['<script type="text/javascript" src="/js/admin/startup.js"></script>', 'admin/startup'],
            ['<script type="text/javascript" src="/js/admin/startup.js"></script>', 'admin/startup.js']
        ];
    }

    /**
     * Test BcBaser->elementが機能してるかテスト
     *
     * @return void
     */
    public function testElement()
    {
        $element = 'flash/default';

        ob_start();
        $this->BcBaser->element($element, ['key'=>'sampleKey', 'message' => 'sampletest']);
        $result = ob_get_clean();

        $expected = $this->BcAdminAppView->element($element, ['key'=>'sampleKey', 'message' => 'sampletest']);

        $this->assertEquals($expected, $result);
    }

    /**
     * Test BcBaser->getElementが適切なelementを取得できているかテスト
     * @return void
     * @todo basercms4系と統合する
     */
    public function testGetElement()
    {
        // フロント
        $this->assertTextContains('<span class="bs-footer__banner">', $this->BcBaser->getElement('footer'));

        // 管理画面
        $view = $this->BcBaser->getView();
        $view->setRequest($this->getRequest('/baser/admin'));
        $view->setTheme('BcAdminThird');
        $reflectionClass = new ReflectionClass($view);
        $pathsForPlugin = $reflectionClass->getProperty('_pathsForPlugin');
        $pathsForPlugin->setAccessible(true);
        $pathsForPlugin->setValue($view, []);
        $this->assertTextContains('<div id="Footer" class="bca-footer" data-loggedin="">', $this->BcBaser->getElement('footer'));

        // 管理画面用のテンプレートがなくフロントのテンプレートがある場合
        $templateDir = ROOT . DS . 'plugins' . DS . 'bc-admin-third' . DS . 'templates'. DS;
        $fileFront = new BcFile($templateDir . 'element' . DS . 'test.php');
        $fileFront->create();
        $fileFront->write('front');
        $this->assertTextContains('front', $this->BcBaser->getElement('test'));

        // 管理画面用のテンプレートとフロントのテンプレートの両方がある場合
        $fileAdmin = new BcFile($templateDir . 'Admin' . DS . 'element' . DS . 'test.php');
        $fileAdmin->create();
        $fileAdmin->write('admin');
        $this->assertTextContains('admin', $this->BcBaser->getElement('test'));
        $fileFront->delete();
        $fileAdmin->delete();

        // View.beforeElement でテーマを変更した場合
        $listener = $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'beforeElement', function(Event $event) {
            $event->getSubject()->setTheme('BcFront');
            $options = $event->getData('options');
            $options['plugin'] = false; // 現在のプラグイン BaserCore のテンプレートを対象外にする
            $event->setData('options', $options);
        });
        $this->assertTextContains('<span class="bs-footer__banner">', $this->BcBaser->getElement('footer'));
        EventManager::instance()->off($listener);

        // View.Pages.beforeElement でテーマを変更した場合
        $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'BaserCore.Pages.beforeElement', function(Event $event) {
            $event->getSubject()->setTheme('BcFront');
            $options = $event->getData('options');
            $options['plugin'] = false;
            $event->setData('options', $options);
        });
        $this->assertTextContains('<span class="bs-footer__banner">', $this->BcBaser->getElement('footer'));

        // View.afterElement 結果を書き換えた場合
        $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'afterElement', function(Event $event) {
            $event->setData('out', '');
        });
        $this->assertEmpty('', $this->BcBaser->getElement('footer'));

        // View.Pages.afterElement でテーマを変更した場合
        $this->entryEventToMock(self::EVENT_LAYER_VIEW, 'BaserCore.Pages.afterElement', function(Event $event) {
            $event->setData('out', 'hoge');
        });
        $this->assertEquals('hoge', $this->BcBaser->getElement('footer'));
    }

    /**
     * Test BcBaser->imgが適切な画像を出力できてるかテスト
     * @return void
     * @todo basercms4系統合が必要
     */
    public function testImg()
    {
        $img = 'sampletest.png';
        ob_start();
        $this->BcBaser->img($img);
        $result = ob_get_clean();
        $expected = $this->Html->image($img);
        $this->assertEquals($expected, $result);
    }

    /**
     * 画像読み込みタグを出力する
     * @return void
     */
    public function testImg_Version4()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString('<img src="/img/baser.power.gif" alt=""/>');
        $this->BcBaser->img('baser.power.gif');
    }

    /**
     * Test BcBaser->getImgが適切な画像を取得できているかテスト
     * @return void
     * @todo basercms4系統合必要
     */
    public function testgetImg()
    {
        $img = 'sampletest.png';
        $result = $this->BcBaser->getImg($img);
        $expected = $this->Html->image($img);
        $this->assertEquals($expected, $result);
    }

    /**
     * 画像タグを取得する
     * @param string $path 画像のパス
     * @param array $options オプション
     * @param string $expected 結果
     * @return void
     * @dataProvider getImgDataProvider
     */
    public function testGetImg_Version4($path, $options, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $result = $this->BcBaser->getImg($path, $options);
        $this->assertEquals($expected, $result);
    }

    public function getImgDataProvider()
    {
        return [
            ['baser.power.gif', ['alt' => "baserCMSロゴ"], '<img src="/img/baser.power.gif" alt="baserCMSロゴ"/>'],
            ['baser.power.gif', ['title' => "baserCMSロゴ"], '<img src="/img/baser.power.gif" title="baserCMSロゴ" alt=""/>']
        ];
    }


    /**
     * Test BcBaser->linkが適切なリンクを出力できてるかテスト
     *
     * @return void
     */
    public function testLink()
    {
        $link = 'sampletest';
        ob_start();
        $this->BcBaser->link($link, $link);
        $result = ob_get_clean();
        $this->assertMatchesRegularExpression('/<a href="\/sampletest">sampletest<\/a>/', $result);
    }

    /**
     * アンカータグを取得する
     * @param string $title タイトル
     * @param string $url URL
     * @param array $option オプション
     * @param string $expected 結果
     * @return void
     * @dataProvider getLinkDataProvider
     */
    public function testGetLink($title, $url, $option, $expected)
    {
        $this->loginAdmin($this->getRequest());
        if (!empty($option['prefix'])) {
            $this->BcBaser->getView()->setRequest($this->getRequest('/admin'));
        }
        if (!empty($option['forceTitle'])) {
            $this->loginAdmin($this->getRequest('/baser/admin'), 2);
        }
        if (!empty($option['ssl'])) {
            Configure::write('BcEnv.sslUrl', 'https://localhost/');
        }
        $result = $this->BcBaser->getLink($title, $url, $option);
        $this->assertEquals($expected, $result);
        Configure::write('BcEnv.sslUrl', '');
    }

    public function getLinkDataProvider()
    {
        return [
            ['', '/', [], '<a href="/"></a>'],
            ['会社案内', '/about', [], '<a href="/about">会社案内</a>'],
            ['会社案内 & 会社データ', '/about', ['escape' => true], '<a href="/about">会社案内 &amp; 会社データ</a>'],    // エスケープ
            ['<b>title</b>', 'https://example.com/<b>link</b>', [], '<a href="https://example.com/&lt;b&gt;link&lt;/b&gt;">&lt;b&gt;title&lt;/b&gt;</a>'], // エスケープ
            ['<b>title</b>', 'https://example.com/<b>link</b>', ['escape' => false], '<a href="https://example.com/<b>link</b>"><b>title</b></a>'], // エスケープ
            ['<b>title</b>', 'https://example.com/<b>link</b>', ['escapeTitle' => false], '<a href="https://example.com/&lt;b&gt;link&lt;/b&gt;"><b>title</b></a>'], // エスケープ
            ['固定ページ管理', ['prefix' => 'Admin', 'controller' => 'pages', 'action' => 'index'], [], '<a href="/baser/admin/baser-core/pages/index">固定ページ管理</a>'],    // プレフィックス
            ['システム設定', ['Admin' => true, 'controller' => 'site_configs', 'action' => 'index'], ['forceTitle' => true], '<span>システム設定</span>'],    // 強制タイトル
            ['会社案内', '/about', ['ssl' => true], '<a href="https://localhost/about">会社案内</a>'], // SSL
            ['テーマファイル管理', ['controller' => 'themes', 'action' => 'manage', 'jsa'], ['ssl' => true], '<a href="https://localhost/baser-core/themes/manage/jsa">テーマファイル管理</a>'], // SSL
            ['画像', '/img/test.jpg', ['ssl' => true], '<a href="https://localhost/img/test.jpg">画像</a>'], // SSL
        ];
    }

    /**
     * 現在のログインユーザーが管理者グループかどうかチェックする
     * @param int|null $id ユーザーグループID
     * @param boolean $expected 期待値
     * @dataProvider isAdminUserDataProvider
     */
    public function testIsAdminUser($id, $expected)
    {
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $user = $id? $this->getuser($id) : null;
        $result = $this->BcBaser->isAdminUser($user);
        $this->assertEquals($expected, $result);
    }

    public function isAdminUserDataProvider()
    {
        return [
            // 管理者グループ
            [1, true],
            // 運営者グループ
            [2, false],
            // 引数を持たない場合
            [null, true]
        ];
    }

    /**
     * Test url
     *
     * @return void
     */
    public function testUrl()
    {
        $url = '/sampletest';
        ob_start();
        $this->BcBaser->url($url, false);
        $result = ob_get_clean();
        $this->assertEquals('/sampletest', $result);
        // フルパスかどうか
        ob_start();
        $this->BcBaser->url($url, true);
        $result = ob_get_clean();
        $this->assertEquals("https://localhost/sampletest", $result);
    }

    /**
     * Test i18nScript
     *
     * @return void
     */
    public function testI18nScript()
    {
        $this->BcBaser->i18nScript([
            'commonCancel' => __d('baser_core', 'キャンセル'),
            'commonSave' => __d('baser_core', '保存')
        ]);
        $encoded1 = "commonCancel = ". json_encode('キャンセル', JSON_UNESCAPED_UNICODE);
        $encoded2 = "commonSave = " . json_encode('保存', JSON_UNESCAPED_UNICODE);

        $result = $this->BcAdminAppView->fetch('script');
        $this->assertStringContainsString($encoded1, $result);
        $this->assertStringContainsString($encoded2, $result);
    }

    /**
     * Test BcBaser->flashが適切なflashメッセージを出力してるかテスト
     *
     * @return void
     * @todo basercms4系を統合する
     */
    public function testFlash()
    {
        // sessionにメッセージがない場合
        $result = $this->BcBaser->flash();
        $this->assertNull($result);
        // sessionにメッセージがある場合
        $session = $this->BcBaser->getView()->getRequest()->getSession();
        $flash = [
            'Flash' => [
                'flash' => [
                    [
                        'message' => "sampletest",
                        'key' => 'flash',
                        'element' => 'flash/default',
                        'params' => ['class' => 'sampletest-message']
                    ]
                ]
            ]];
        // BcBaser->flash
        $session->write($flash);
        ob_start();
        $this->BcBaser->flash();
        $result = ob_get_clean();
        // Flash->render
        $session->write($flash);
        $expected = $this->Flash->render();

        $this->assertStringContainsString($expected, $result);
    }

    /**
     * セッションメッセージを出力する
     * @return void
     */
    public function testFlash_Version4()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // TODO コンソールからのセッションのテストをどうするか？そもそもするか？ ryuring
        if (isConsole()) {
            return;
        }

        $message = 'エラーが発生しました。';
        $this->expectOutputString('<div id="MessageBox"><div id="flashMessage" class="message">' . $message . '</div></div>');
        App::uses('SessionComponent', 'Controller/Component');
        App::uses('ComponentCollection', 'Controller/Component');
        $Session = new SessionComponent(new ComponentCollection());
        $Session->setFlash($message);
        $this->BcBaser->flash();
    }

    /**
     * コンテンツタイトルを取得する
     * @return void
     */
    public function testGetContentsTitle(): void
    {
        // 設定なし
        $this->assertEmpty($this->BcBaser->getContentsTitle());

        // 設定あり
        $this->BcBaser->setTitle('会社データ');
        $this->assertEquals('会社データ', $this->BcBaser->getContentsTitle());
    }

    /**
     * Test BcBaser->contentsNameが適切なコンテンツ名を出力してるかテスト
     *
     * @return void
     */
    public function testContentsName()
    {
        ob_start();
        $this->BcBaser->contentsName();
        $result = ob_get_clean();
        $this->assertEquals('Home', $result);
    }

    /**
     * Test BcBaser->getContentsNameが適切なコンテンツ名を取得してるかテスト
     * @return void
     * @dataProvider getContentsNameDataProvider
     */
    public function testGetContentsName($expects, $url, $detail = false, $options = [])
    {

        if (!empty($options['device'])){
            $_SERVER['HTTP_USER_AGENT'] = $options['device'];
            unset($options['device']);

            $sites = TableRegistry::getTableLocator()->get('BaserCore.Sites');
            $site = $sites->findById(2)->first();
            $site = $sites->patchEntity($site, ['status' => true]);
            $sites->save($site);
        }

        if (!empty($options['language'])){
            $_SERVER['HTTP_ACCEPT_LANGUAGE'] = $options['language'];
            unset($options['device']);
        }

        $this->BcBaser = new BcBaserHelper(new BcFrontAppView());
        $this->BcBaser->getView()->setRequest($this->getRequest($url));

        if (!empty($options['error'])) {
            $reflectionClass = new ReflectionClass(get_class($this->BcBaser->getView()));
            $property = $reflectionClass->getProperty('name');
            $property->setAccessible(true);
            $property->setValue($this->BcBaser->getView(), 'CakeError');
        }

        $this->assertEquals($expects, $this->BcBaser->getContentsName($detail, $options));

    }

    /**
     * コンテンツを特定するIDを取得する
     * ・キャメルケースで取得
     * ・URLのコントローラー名までを取得
     * ・ページの場合は、カテゴリ名（カテゴリがない場合は Default）
     * ・トップページは、Home
     * @param string $url URL
     * @param string $expects コンテンツ名
     * @dataProvider getContentsNameDataProvider
     */
    public function getContentsNameDataProvider()
    {
        return [
            //PC
            ['Home', '/'],
            ['News', '/news/'],
            ['Contact', '/contact/'],
            ['Default', '/about'],
            ['Service', '/service/'],
            ['Service', '/service/service1'],
            ['Home', '/', true],
            ['NewsIndex', '/news/', true],
            ['ContactIndex', '/contact/', true],
            ['About', '/about', true],
            ['ServiceIndex', '/service/', true],
            ['ServiceService1', '/service/service1', true],
            ['Hoge', '/', false, ['home' => 'Hoge']],
            ['Hoge', '/about', false, ['default' => 'Hoge']],
            ['service_service1', '/service/service1', true, ['underscore' => true]],
            ['Error!!!', '/', false, ['error' => 'Error!!!']],
            // スマートフォン
            ['Home', '/s/', false, ['device' => 'iPhone']],
            // 英語サイト
            ['Home', '/en/', false, ['language' => 'en']],
        ];
    }

    /**
     * Test BcBaser->getUrlが適切なURLを取得してるかテスト
     * @dataProvider getUrlDataProvider
     */
    public function testGetUrl($url, $full, $base, $expected)
    {
        if($base) {
            $this->BcBaser->getView()->setRequest($this->getRequest('/', [], 'GET', ['base' => $base]));
        }
        $this->assertEquals($expected, $this->BcBaser->getUrl($url, $full));
    }

    public function getUrlDataProvider()
    {
        return [
            // ノーマル
            ['/sampletest', false, false, '/sampletest'],
            // フルパス
            ['/sampletest', true, false, 'https://localhost/sampletest'],
            // 省略
            [null, false, false, '/'],
            // 配列
            [[
                'prefix' => 'Admin',
                'plugin' => 'BcBlog',
                'controller' => 'BlogPosts',
                'action' => 'edit',
                1
            ], false, false, '/baser/admin/bc-blog/blog_posts/edit/1'],
            // サブフォルダ
            ['/sampletest', false, '/sub', '/sub/sampletest'],
        ];
    }

    /**************** TODO ucmitz :下記basercms4系より移行 メソッドが準備され次第　調整して上記のテストコードに追加してください　********************/

    /**
     * ログイン状態にする
     * @return void
     */
    protected function _login()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $User = ClassRegistry::init('User');
        $user = $User->find('first', ['conditions' => ['User.id' => 1]]);
        unset($user['User']['password']);
        $this->BcBaser->set('user', $user['User']);
        $user['User']['UserGroup'] = $user['UserGroup'];
        $sessionKey = BcUtil::authSessionKey('Admin');
        $_SESSION['Auth'][$sessionKey] = $user['User'];
    }

    /**
     * ログイン状態を解除する
     * @return void
     */
    protected function _logout()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->set('user', '');
    }

    /**
     * タイトルを設定する
     * @return void
     */
    public function testSetTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $topTitle = '｜baserCMS inc. [デモ]';
        $this->BcBaser->request = $this->_getRequest('/about');
        // カテゴリがない場合
        $this->BcBaser->setTitle('会社案内');
        $this->assertEquals("会社案内{$topTitle}", $this->BcBaser->getTitle());

        // カテゴリがある場合
        $this->BcBaser->request = $this->_getRequest('/service/service2');
        $this->BcBaser->_View->set('crumbs', [
            ['name' => '会社案内', 'url' => '/service/index'],
            ['name' => '会社データ', 'url' => '/service/data']
        ]);
        $this->BcBaser->setTitle('会社沿革');
        $this->assertEquals("会社沿革｜会社データ｜会社案内{$topTitle}", $this->BcBaser->getTitle());

        // カテゴリは存在するが、カテゴリの表示をオフにした場合
        $this->BcBaser->setTitle('会社沿革', false);
        $this->assertEquals("会社沿革{$topTitle}", $this->BcBaser->getTitle());
    }

    /**
     * タイトルをセットする
     */
    public function testSetHomeTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->setHomeTitle();
        $this->assertEquals(null, $this->_View->viewVars['homeTitle'], 'タイトルをセットできません。');

        $this->BcBaser->setHomeTitle('hoge');
        $this->assertEquals('hoge', $this->_View->viewVars['homeTitle'], 'タイトルをセットできません。');
    }

    /**
     * meta タグのキーワードを設定する
     * @return void
     */
    public function testSetKeywords()
    {
        $this->BcBaser->setKeywords('baserCMS,国産,オープンソース');
        $this->assertEquals('baserCMS,国産,オープンソース', $this->BcBaser->getKeywords());
    }

    /**
     * meta タグの説明文を設定する
     * @return void
     */
    public function testSetDescription()
    {
        $this->BcBaser->setDescription('国産オープンソースのホームページです');
        $this->assertEquals('国産オープンソースのホームページです', $this->BcBaser->getDescription());
    }

    /**
     * レイアウトで利用する為の変数を設定する
     * @return void
     */
    public function testSet()
    {
        $this->BcBaser->set('keywords', 'baserCMS,国産,オープンソース');
        $this->assertEquals('baserCMS,国産,オープンソース', $this->BcBaser->getKeywords());
    }

    /**
     * タイトルへのカテゴリタイトルの出力有無を設定する
     * @return void
     */
    public function testSetCategoryTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $topTitle = '｜baserCMS inc. [デモ]';
        $this->BcBaser->request = $this->_getRequest('/about');
        $this->BcBaser->_View->set('crumbs', [
            ['name' => '会社案内', 'url' => '/company/index'],
            ['name' => '会社データ', 'url' => '/company/data']
        ]);
        $this->BcBaser->setTitle('会社沿革');

        // カテゴリをオフにした場合
        $this->BcBaser->setCategoryTitle(false);
        $this->assertEquals("会社沿革{$topTitle}", $this->BcBaser->getTitle());

        // カテゴリをオンにした場合
        $this->BcBaser->setCategoryTitle(true);
        $this->assertEquals("会社沿革｜会社データ｜会社案内{$topTitle}", $this->BcBaser->getTitle());

        // カテゴリを指定した場合
        $this->BcBaser->setCategoryTitle('店舗案内');
        $this->assertEquals("会社沿革｜店舗案内{$topTitle}", $this->BcBaser->getTitle());

        // パンくず用にリンクも指定した場合
        $this->BcBaser->setCategoryTitle([
            'name' => '店舗案内',
            'url' => '/shop/index'
        ]);
        $expected = [
            [
                'name' => '店舗案内',
                'url' => '/shop/index'
            ],
            [
                'name' => '会社沿革',
                'url' => ''
            ]
        ];
        $this->assertEquals($expected, $this->BcBaser->getCrumbs());
    }

    /**
     * meta タグ用のキーワードを取得する
     * @param string $expected 期待値
     * @param string|null $keyword 設定されるキーワードの文字列
     * @dataProvider getKeywordsDataProvider
     */
    public function testGetKeywords($expected, $keyword = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        if ($keyword !== null) {
            $this->BcBaser->setKeywords($keyword);
        }
        $this->assertEquals($expected, $this->BcBaser->getKeywords());
    }

    public function getKeywordsDataProvider()
    {
        return [
            ['baser,CMS,コンテンツマネジメントシステム,開発支援'],
            ['baser,CMS,コンテンツマネジメントシステム,開発支援', ''],
            ['baserCMS,国産,オープンソース', 'baserCMS,国産,オープンソース'],
        ];
    }

    /**
     * meta タグ用のページ説明文を取得する
     * @param string $expected 期待値
     * @param string|null $description 設定されるキーワードの文字列
     * @return void
     * @dataProvider getDescriptionDataProvider
     */
    public function testGetDescription($expected, $description = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        if ($description !== null) {
            $this->BcBaser->setDescription($description);
        }
        $this->assertEquals($expected, $this->BcBaser->getDescription());
    }

    public function getDescriptionDataProvider()
    {
        return [
            ['baserCMS は、CakePHPを利用し、環境準備の素早さに重点を置いた基本開発支援プロジェクトです。Webサイトに最低限必要となるプラグイン、そしてそのプラグインを組み込みやすい管理画面、認証付きのメンバーマイページを最初から装備しています。', ''],
            ['国産オープンソースのホームページです', '国産オープンソースのホームページです']
        ];
    }

    /**
     * タイトルタグを取得する
     * @return void
     */
    public function testGetTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $topTitle = 'baserCMS inc. [デモ]';
        $this->BcBaser->request = $this->_getRequest('/about');
        // 通常
        $this->BcBaser->_View->set('crumbs', [
            ['name' => '会社案内', 'url' => '/company/index'],
            ['name' => '会社データ', 'url' => '/company/data']
        ]);
        $this->BcBaser->setTitle('会社沿革');
        $this->assertEquals("会社沿革｜会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle());

        // 区切り文字を ≫ に変更
        $this->assertEquals("会社沿革≫会社データ≫会社案内≫{$topTitle}", $this->BcBaser->getTitle('≫'));

        // カテゴリタイトルを除外
        $this->assertEquals("会社沿革｜{$topTitle}", $this->BcBaser->getTitle('｜', false));

        // カテゴリが対象ページと同じ場合に省略する
        $this->BcBaser->setTitle('会社データ');
        $this->assertEquals("会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle('｜', true));

        // strip_tagの機能確認 tag付
        $this->BcBaser->setTitle('会社<br>沿革<center>真ん中</center>');
        $this->assertEquals("会社<br>沿革<center>真ん中</center>｜会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle('｜', true));

        // strip_tagの機能確認 tagを削除
        $options = [
            'categoryTitleOn' => true,
            'tag' => false
        ];
        $this->assertEquals("会社沿革真ん中｜会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle('｜', $options));

        // 一部タグだけ削除
        $options = [
            'categoryTitleOn' => true,
            'tag' => false,
            'allowableTags' => '<center>'
        ];
        $this->assertEquals("会社沿革<center>真ん中</center>｜会社データ｜会社案内｜{$topTitle}", $this->BcBaser->getTitle('｜', $options));
    }

    /**
     * パンくずリストの配列を取得する
     * @return void
     */
    public function testGetCrumbs()
    {
        // パンくずが設定されてない場合
        $result = $this->BcBaser->getCrumbs(true);
        $this->assertEmpty($result);

        // パンくずが設定されている場合
        $this->BcBaser->getView()->set('crumbs', [
            ['name' => '会社案内', 'url' => '/company/index'],
            ['name' => '会社データ', 'url' => '/company/data']
        ]);
        $this->BcBaser->setTitle('会社沿革');
        $expected = [
            ['name' => '会社案内', 'url' => '/company/index'],
            ['name' => '会社データ', 'url' => '/company/data'],
            ['name' => '会社沿革', 'url' => '']
        ];
        $this->assertEquals($expected, $this->BcBaser->getCrumbs(true));

        // パンくずは設定されているが、オプションでカテゴリをオフにした場合
        $expected = [
            ['name' => '会社沿革', 'url' => '']
        ];
        $this->assertEquals($expected, $this->BcBaser->getCrumbs(false));
    }

    /**
     * コンテンツタイトルを出力する
     * @return void
     */
    public function testContentsTitle()
    {
        $this->expectOutputString('会社データ');
        $this->BcBaser->setTitle('会社データ');
        $this->BcBaser->contentsTitle();
    }

    /**
     * コンテンツメニューを取得する
     */
    public function testGetContentsMenu()
    {
        $this->assertMatchesRegularExpression('/<ul class="menu ul-level-1">/s', $this->BcBaser->getContentsMenu());
        $this->assertMatchesRegularExpression('/<ul class="menu ul-level-1">/s', $this->BcBaser->getContentsMenu(1, 1));
        $this->assertMatchesRegularExpression('/<ul class="menu ul-level-1">/s', $this->BcBaser->getContentsMenu(1, 1, 1));
    }

    /**
     * タイトルタグを出力する
     * @return void
     */
    public function testTitle()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $topTitle = 'baserCMS inc. [デモ]';
        $title = '会社データ';
        $this->BcBaser->request = $this->_getRequest('/about');
        $this->expectOutputString('<title>' . $title . '｜' . $topTitle . '</title>' . PHP_EOL);
        $this->BcBaser->setTitle($title);
        $this->BcBaser->title();
    }

    /**
     * キーワード用のメタタグを出力する
     * @return void
     */
    public function testMetaKeywords()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->setKeywords('baserCMS,国産,オープンソース');
        ob_start();
        $this->BcBaser->metaKeywords();
        $result = ob_get_clean();
        $excepted = [
            'meta' => [
                'name' => 'keywords',
                'content' => 'baserCMS,国産,オープンソース'
            ]
        ];

        $this->assertTags($result, $excepted);
    }

    /**
     * ページ説明文用のメタタグを出力する
     * @return void
     */
    public function testMetaDescription()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->setDescription('国産オープンソースのホームページです');
        ob_start();
        $this->BcBaser->metaDescription();
        $result = ob_get_clean();
        $excepted = [
            'meta' => [
                'name' => 'description',
                'content' => '国産オープンソースのホームページです'
            ]
        ];
        $this->assertTags($result, $excepted);
    }

    /**
     * RSSフィードのリンクタグを出力する
     * @return void
     */
    public function testRss()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        ob_start();
        $this->BcBaser->rss('ブログ', 'http://localhost/blog/');
        $result = ob_get_clean();
        $excepted = [
            'link' => [
                'href' => 'http://localhost/blog/',
                'type' => 'application/rss+xml',
                'rel' => 'alternate',
                'title' => 'ブログ'
            ]
        ];
        $this->assertTags($result, $excepted);
    }

    /**
     * 現在のページがトップページかどうかを判定する
     * @param bool $expected 期待値
     * @param string $url リクエストURL
     * @return void
     * @dataProvider isHomeDataProvider
     */
    public function testIsHome($expected, $url)
    {
        $this->BcBaser->getView()->setRequest($this->getRequest($url));
        $this->assertEquals($expected, $this->BcBaser->isHome());
    }

    public function isHomeDataProvider()
    {
        return [
            //PC
            [true, '/'],
            [true, '/index'],
            [false, '/news/index'],
            // 英語ページ
            [true, '/en/'],
            [true, '/en/index'],
            [false, '/en/news/index'],
            // モバイルページ
            // [true, '/m/'],
            // [true, '/m/index'],
            // [false, '/m/news/index'],
            // スマートフォンページ
            // [true, '/s/'],
            // [true, '/s/index'],
            // [false, '/s/news/index'],
            // [false, '/s/news/index']
        ];
    }

    /**
     * ヘッダーテンプレートを出力する
     *
     * @return void
     */
    public function testHeader()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputRegex('/<div id="Header">.*<a href="\/sitemap">サイトマップ<\/a>.*<\/li>.*<\/ul>.*<\/div>.*<\/div>/s');
        $this->BcBaser->header();
    }

    /**
     * フッターテンプレートを出力する
     * @return void
     */
    public function testFooter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputRegex('/<div id="Footer">.*<img src="\/img\/cake.power.gif".*<\/a>.*<\/p>.*<\/div>/s');
        $this->BcBaser->footer();
    }

    /**
     * ページネーションを出力する
     * @return void
     */
    public function testPagination()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputRegex('/<div class="pagination">/');
        $this->BcBaser->getRequest()->withParam('paging.Model', [
            'count' => 100,
            'pageCount' => 3,
            'page' => 2,
            'limit' => 10,
            'current' => null,
            'prevPage' => 1,
            'nextPage' => 3,
            'options' => [],
            'paramType' => 'named'
        ]);
        $this->BcBaser->pagination();
    }

    /**
     * コンテンツ本体を出力する
     * @return void
     */
    public function testContent()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString('コンテンツ本体');
        $this->_View->assign('content', 'コンテンツ本体');
        $this->BcBaser->content();
    }

    /**
     * コンテンツ内で設定した CSS や javascript をレイアウトテンプレートに出力する
     * @return void
     */
    public function testScripts()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $themeConfigTag = '<link rel="stylesheet" type="text/css" href="/files/theme_configs/config.css" />';

        // CSS
        $expected = "\n" . '<meta name="generator" content="basercms"/><link rel="stylesheet" type="text/css" href="/css/admin/layout.css"/>';
        $this->BcBaser->css('admin/layout', ['inline' => false]);
        ob_start();
        $this->BcBaser->scripts();
        $result = ob_get_clean();
        $result = str_replace($themeConfigTag, '', $result);
        $this->assertEquals($expected, $result);
        $this->_View->assign('css', '');

        Configure::write('BcApp.outputMetaGenerator', false);

        // Javascript
        $expected = '<script type="text/javascript" src="/js/admin/startup.js"></script>';
        $this->BcBaser->js('admin/startup', false);
        ob_start();
        $this->BcBaser->scripts();
        $result = ob_get_clean();
        $result = str_replace($themeConfigTag, '', $result);
        $this->assertEquals($expected, $result);
        $this->_View->assign('script', '');

        // meta
        $expected = '<meta name="description" content="説明文"/>';
        App::uses('BcHtmlHelper', 'View/Helper');
        $BcHtml = new BcHtmlHelper($this->_View);
        $BcHtml->meta('description', '説明文', ['inline' => false]);
        ob_start();
        $this->BcBaser->scripts();
        $result = ob_get_clean();
        $result = str_replace($themeConfigTag, '', $result);
        $this->assertEquals($expected, $result);
        $this->_View->assign('meta', '');

        // ツールバー
        $expected = '<link rel="stylesheet" type="text/css" href="/css/admin/toolbar.css"/>';
        $this->BcBaser->set('user', ['User']);
        ob_start();
        $this->BcBaser->scripts();
        $result = ob_get_clean();
        $result = str_replace($themeConfigTag, '', $result);
        $this->assertEquals($expected, $result);
    }

    /**
     * ツールバーエレメントや CakePHP のデバッグ出力を表示
     * @return void
     */
    public function testFunc()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        Configure::write('debug', 0);

        // 未ログイン
        ob_start();
        $this->BcBaser->func();
        $result = ob_get_clean();
        $this->assertEquals('', $result);

        // ログイン中
        $expects = '<div id="ToolBar">';
        $this->_login();
        $this->BcBaser->set('currentPrefix', 'admin');
        $this->BcBaser->set('currentUserAuthPrefixes', ['admin']);
        ob_start();
        $this->BcBaser->func();
        $result = ob_get_clean();
        $this->assertTextContains($expects, $result);
        $this->_logout();

        // デバッグモード２
        $expects = '<table class="cake-sql-log"';
        Configure::write('debug', 2);
        ob_start();
        $this->BcBaser->func();
        $result = ob_get_clean();
        $this->assertTextContains($expects, $result);
    }

    /**
     * XMLヘッダタグを出力する
     * @param string $expected 期待値
     * @param string $url URL
     * @return void
     * @dataProvider xmlDataProvider
     */
    public function testXmlHeader($expected, $url = null)
    {
        $this->BcBaser->getView()->setRequest($this->getRequest($url));
        $this->expectOutputString($expected);
        $this->BcBaser->xmlHeader();
    }

    public function xmlDataProvider()
    {
        return [
            ['<?xml version="1.0" encoding="UTF-8" ?>' . "\n", '/']
        ];
    }

    /**
     * アイコン（favicon）タグを出力する
     * @return void
     */
    public function testIcon()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString('<link href="/favicon.ico" type="image/x-icon" rel="icon"/><link href="/favicon.ico" type="image/x-icon" rel="shortcut icon"/>' . "\n");
        $this->BcBaser->icon();
    }

    /**
     * CSSの読み込みタグを出力する
     * @return void
     */
    public function testCss()
    {
        // ノーマル
        ob_start();
        $this->BcBaser->css('admin/import');
        $result = ob_get_clean();
        $expected = '<link rel="stylesheet" href="/css/admin/import.css">';
        $this->assertEquals($expected, $result);
        // // 拡張子あり
        ob_start();
        $this->BcBaser->css('admin/import.css');
        $result = ob_get_clean();
        $expected = '<link rel="stylesheet" href="/css/admin/import.css">';
        $this->assertEquals($expected, $result);
        // インライン
        ob_start();
        $this->BcBaser->css('admin/import2.css', true);
        $result = ob_get_clean();
        $expected = '<link rel="stylesheet" href="/css/admin/import2.css">';
        $this->assertEquals($expected, $result);
        // ブロック
        ob_start();
        $this->BcBaser->css('admin/import3.css', false);
        $result = ob_get_clean();
        $this->assertEmpty($result);
        $this->assertEquals('<link rel="stylesheet" href="/css/admin/import3.css">',
            $this->BcAdminAppView->fetch('css'));
        // ブロック指定
        ob_start();
        $this->BcBaser->css('admin/import4.css', false, ['block' => 'testblock']);
        $result = ob_get_clean();
        $this->assertEmpty($result);
        $this->assertEquals('<link rel="stylesheet" href="/css/admin/import4.css">',
            $this->BcAdminAppView->fetch('testblock'));
        ob_start();
        $this->BcBaser->css('admin/import5.css', true, ['block' => 'testblock2']);
        $result = ob_get_clean();
        $this->assertEmpty($result);
        $this->assertEquals('<link rel="stylesheet" href="/css/admin/import5.css">',
            $this->BcAdminAppView->fetch('testblock2'));
    }

    /**
     * JSの読み込みタグを出力する（インラインオフ）
     * @return void
     */
    public function testJsNonInline()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        // インラインオフ（boolean）
        $this->BcBaser->js('admin/function', false);
        $expected = '<script type="text/javascript" src="/js/admin/function.js"></script>';
        $result = $this->_View->fetch('script');
        $this->assertEquals($expected, $result);
    }

    /**
     * SSL通信かどうか判定する
     * @return void
     */
    public function testIsSSL()
    {
        $_SERVER['HTTPS'] = true;

        $this->BcBaser->getView()->setRequest($this->getRequest('https://localhost/'));
        $this->assertEquals(true, $this->BcBaser->isSSL());
    }

    /**
     * charset メタタグを出力する
     * @param string $expected 期待値
     * @param string $encoding エンコード
     * @param string $url URL
     * @return void
     * @dataProvider charsetDataProvider
     */
    public function testCharset($expected, $encoding, $url = null)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->request = $this->_getRequest($url);
        $this->expectOutputString($expected);
        if ($encoding !== null) {
            $this->BcBaser->charset($encoding);
        } else {
            $this->BcBaser->charset();
        }
    }

    public function charsetDataProvider()
    {
        return [
            ['<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />', 'UTF-8', '/'],
            ['<meta http-equiv="Content-Type" content="text/html; charset=Shift-JIS" />', null, '/m/']
        ];
    }

    /**
     * コピーライト用の年を出力する
     * @param string $expected 期待値
     * @param mixed $begin 開始年
     * @return void
     * @dataProvider copyYearDataProvider
     */
    public function testCopyYear($expected, $begin)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->expectOutputString($expected);
        $this->BcBaser->copyYear($begin);
    }

    public function copyYearDataProvider()
    {
        $year = date('Y');
        return [
            ["2000 - {$year}", 2000],
            [$year, 'はーい']
        ];
    }

    /**
     *
     * @return void
     */
    public function testGetContentCreatedDate()
    {
        $this->assertEquals('2016/07/29 18:13', $this->BcBaser->getContentCreatedDate());
    }

    /**
     *
     * @return void
     */
    public function testGetContentModifiedDate()
    {
        $this->assertEquals('2020/09/14 20:13', $this->BcBaser->getContentModifiedDate());
    }

    /**
     * パンくずリストのHTMLレンダリング結果を表示する
     * @return void
     */
    public function testCrumbs()
    {
        // パンくずが設定されてない場合
        $result = $this->BcBaser->crumbs();
        $this->assertEmpty($result);

        // パンくずが設定されている場合
        $crumbs = [
            ['name' => '会社案内', 'url' => '/company/'],
            ['name' => '会社データ', 'url' => '/company/data'],
            ['name' => '会社沿革', 'url' => '']
        ];
        foreach($crumbs as $crumb) {
            $this->BcBaser->addCrumb($crumb['name'], $crumb['url']);
        }
        ob_start();
        $this->BcBaser->crumbs();
        $this->assertEquals('<a href="/company/">会社案内</a>&raquo;<a href="/company/data">会社データ</a>&raquo;会社沿革', ob_get_clean());

        // 区切り文字を変更、先頭にホームを追加
        ob_start();
        $this->BcBaser->crumbs(' | ', 'ホーム');
        $this->assertEquals('<a href="/">ホーム</a> | <a href="/company/">会社案内</a> | <a href="/company/data">会社データ</a> | 会社沿革', ob_get_clean());
    }

    /**
     * パンくずリストの要素を追加する
     * @return void
     */
    public function testAddCrumbs()
    {
        $this->BcBaser->addCrumb('会社案内', '/company/');
        ob_start();
        $this->BcBaser->crumbs();
        $this->assertEquals('<a href="/company/">会社案内</a>', ob_get_clean());
    }

    /**
     * ブラウザにキャッシュさせる為のヘッダーを出力する
     * @param boolean $expected 期待値
     * @dataProvider cacheHeaderDataProvider
     */
    public function testCacheHeader($expire, $type, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');

        $this->BcBaser->cacheHeader($expire, $type);
        $result = xdebug_get_headers();

        $CacheControl = $result[4];
        $this->assertMatchesRegularExpression('/' . $expected . '/', $CacheControl, 'ブラウザにキャッシュさせる為のヘッダーを出力できません');

        $ContentType = $result[2];
        $this->assertMatchesRegularExpression('/' . $type . '/', $ContentType, 'キャッシュの対象を指定できません');
    }

    public function cacheHeaderDataProvider()
    {
        return [
            [null, 'html', 'Cache-Control: max-age=14'],
            [null, 'css', 'Cache-Control: max-age=14'],
            [10, 'html', 'Cache-Control: max-age=10'],
        ];
    }

    /**
     * httpから始まるURLを取得する
     * @param mixed $url 文字列のURL、または、配列形式のURL
     * @param bool $sessionId セッションIDを付加するかどうか
     * @param string $host $_SERVER['HTTP_HOST']の要素
     * @param string $https $_SERVER['HTTPS']の要素
     * @param boolean $expected 期待値
     * @dataProvider getUriDataProvider
     */
    public function testGetUri($url, $sessionId, $host, $https, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $_SERVER['HTTPS'] = $https;
        Configure::write('BcEnv.host', $host);

        $result = $this->BcBaser->getUri($url, $sessionId);
        $this->assertEquals($expected, $result);
    }

    public function getUriDataProvider()
    {
        return [
            ['/', true, 'localhost', '', 'http://localhost/'],
            ['/about', true, 'localhost', '', 'http://localhost/about'],
            ['/about', true, 'test', '', 'http://test/about'],
            ['/about', false, 'localhost', '', 'http://localhost/about'],
            ['/about', false, 'localhost', 'on', 'https://localhost/about'],
        ];
    }

    /**
     * 文字列を検索しマークとしてタグをつける
     * @param string $search 検索文字列
     * @param string $text 検索対象文字列
     * @param string $name マーク用タグ
     * @param array $attributes タグの属性
     * @param bool $escape エスケープ有無
     * @param boolean $expected 期待値
     * @dataProvider markDataProvider
     */
    public function testMark($search, $text, $name, $attributes, $escape, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $result = $this->BcBaser->mark($search, $text, $name, $attributes, $escape);
        $this->assertEquals($expected, $result);
    }

    public function markDataProvider()
    {
        return [
            ['大切', 'とても大切です', 'strong', [], false, 'とても<strong>大切</strong>です'],
            [['大切', '本当'], 'とても大切です本当です', 'strong', [], false, 'とても<strong>大切</strong>です<strong>本当</strong>です'],
            ['大切', 'とても大切です', 'b', [], false, 'とても<b>大切</b>です'],
            ['大切', 'とても大切です', 'b', ['class' => 'truth'], false, 'とても<b class="truth">大切</b>です'],
            ['<<大切>>', 'とても<<大切>>です', 'b', [], true, 'とても<b>&lt;&lt;大切&gt;&gt;</b>です'],
        ];
    }

    /**
     * サイトマップを出力する
     *
     * @param mixed $pageCategoryId 固定ページカテゴリID（初期値 : null）
     *    - 0 : 仕様確認要
     *    - null : 仕様確認要
     * @param string $recursive 取得する階層
     * @param boolean $expected 期待値
     * @dataProvider getSitemapDataProvider
     * @TODO : 階層($recursive)を指定した場合のテスト
     */

    public function testGetSitemap($siteId, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $message = 'サイトマップを正しく出力できません';
        $this->assertMatchesRegularExpression('/' . $expected . '/s', $this->BcBaser->getSitemap($siteId));
    }

    public function getSitemapDataProvider()
    {
        return [
            [0, '<li class="menu-content li-level-1">.*?<a href="\/">トップページ<\/a>.*?<\/li>'],
            [1, '<a href="\/m\/">トップページ.*<\/li>.*<\/ul>'],
            [2, '<a href="\/s\/">トップページ.*<\/li>.*<\/ul>']
        ];
    }

    /**
     * 現在のページが固定ページかどうかを判定する
     * @param  bool $expected
     * @param  string $requestUrl
     * @return void
     * @dataProvider getIsPageProvider
     */
    public function testIsPage($expected, $requestUrl)
    {
        $_SERVER['HTTP_USER_AGENT'] = 'iPhone';
        $this->BcBaser->getView()->setRequest($this->getRequest($requestUrl));
        $this->assertEquals($expected, $this->BcBaser->isPage());
    }

    public function getIsPageProvider()
    {
        return [
            // PCページ
            [true, '/'],
            [true, '/index'],
            [false, '/news/index'],
            [false, '/blog/blog/index'],
        ];
    }

    /**
     * 現在のページの純粋なURLを取得する
     * @param string $url 現在のURL
     * @param string $expected 期待値
     * @return void
     * @dataProvider getHereDataProvider
     */
    public function testGetHere($url, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->BcBaser->request = $this->_getRequest($url);
        $this->assertEquals($expected, $this->BcBaser->getHere());
    }

    public function getHereDataProvider()
    {
        return [
            ['/', '/'],
            ['/index', '/index'],
            ['/contact/index', '/contact/index'],
            ['/blog/blog/index', '/blog/blog/index']
        ];
    }

    /**
     * 現在のページがページカテゴリのトップかどうかを判定する
     * @param string $url 現在のURL
     * @param string $expected 期待値
     * @return void
     * @dataProvider isCategoryTopDataProvider
     */
    public function testIsCategoryTop($url, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->BcBaser->request = $this->_getRequest($url);
        $this->assertEquals($expected, $this->BcBaser->isCategoryTop());
    }

    public function isCategoryTopDataProvider()
    {
        return [
            // PCページ
            ['/', false],
            ['/index', false],
            ['/contact/index', true],
            ['/contact/test', false],
        ];
    }

    /**
     * test page
     * @return void
     */
    public function testPage()
    {
        PageFactory::make(['id' => 5, 'contents' => 'test'])->persist();

        // 正常系
        $this->expectOutputRegex('/test/');
        $this->BcBaser->page('/service/service1');

        // 存在チェックを行わない場合
        $this->expectOutputRegex('//');
        $this->BcBaser->page('/aaa', [], ['checkExists' => false]);

        // 存在チェックを行う場合
        $this->expectException(NotFoundException::class);
        $this->BcBaser->page('/aaa');
    }

    /**
     * 指定したURLが現在のURLかどうか判定する
     * @param string $currentUrl 現在のURL
     * @param string $url 引数として与えられるURL
     * @param bool $expects メソッドの返り値
     * @return void
     *
     * @dataProvider isCurrentUrlDataProvider
     */
    public function testIsCurrentUrl($currentUrl, $url, $expects)
    {
        $this->BcBaser->getView()->setRequest($this->getRequest($currentUrl));
        $this->assertEquals($expects, $this->BcBaser->isCurrentUrl($url));
        // --- サブフォルダ+スマートURLオフ ---
        Configure::write('App.baseUrl', '/basercms/index.php');
        $this->BcBaser->getView()->setRequest($this->getRequest($currentUrl));
        $this->assertEquals($expects, $this->BcBaser->isCurrentUrl($url));
    }

    public function isCurrentUrlDataProvider()
    {
        return [
            ['/', '/', true],
            ['/index', '/', true],
            ['/', '/index', true],
            ['/company', '/company', true],
            ['/news', '/news', true],
            ['/news/', '/news', false],
            ['/news/index', '/news', false],
            ['/news', '/news/', false],
            ['/news/', '/news/', true],
            ['/news/index', '/news/', true],
            ['/news', '/news/index', false],
            ['/news/', '/news/index', true],
            ['/news/index', '/news/index', true],
            ['/', '/company', false],
            ['/company', '/', false],
            ['/news', '/', false]
        ];
    }

    /**
     * テーマのURLを取得する
     * @return void
     */
    public function testGetThemeUrl()
    {
        $this->BcBaser->getView()->setTheme('bc-admin-third');
        $this->assertEquals('/bc_admin_third/', $this->BcBaser->getThemeUrl());
    }

    /**
     * test themeUrl
     */
    public function testThemeUrl()
    {
        $this->BcBaser->getView()->setTheme('bc-admin-third');
        $this->BcBaser->themeUrl();
        $this->expectOutputString('/bc_admin_third/');
    }

    /**
     * ベースとなるURLを取得する
     * @param string $baseUrl サブディレクトリ配置
     * @param string $url アクセスした時のURL
     * @param string $expects 期待値
     * @return void
     *
     * @dataProvider getBaseUrlDataProvider
     */
    public function testGetBaseUrl($baseUrl, $url, $expects)
    {
        $request = $this->getRequest($url)->withAttribute('base', $baseUrl);
        $this->BcBaser->getView()->setRequest($request);
        $this->assertEquals($expects, $this->BcBaser->getBaseUrl());
    }

    public function getBaseUrlDataProvider()
    {
        return [
            // ノーマル
            ['', '/', '/'],
            ['', '/index', '/'],
            ['', '/contact/index', '/'],
            ['', '/blog/blog/index', '/'],
            // サブフォルダ+スマートURLオン
            ['/basercms', '/', '/basercms/'],
            ['/basercms', '/index', '/basercms/'],
            ['/basercms', '/contact/index', '/basercms/'],
            ['/basercms', '/blog/blog/index', '/basercms/']
        ];
    }

    /**
     * コンテンツナビを出力する
     * @return void
     */
    public function testContentsNavi()
    {
        $this->BcBaser->getView()->setRequest($this->getRequest('/about'));
        $this->expectOutputRegex('/<div class=\"bs-contents-navi\">/');
        $this->BcBaser->contentsNavi();
    }

    /**
     * パンくずリストを出力する
     * @return void
     */
    public function testCrumbsList()
    {
        $this->expectOutputRegex('/ホーム/');
        $this->BcBaser->crumbsList();
    }

    /**
     * グローバルメニューを取得する
     * @return void
     */
    public function testGetGlobalMenu()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->assertMatchesRegularExpression('/<ul class="global-menu .*?">.*<a href="\/sitemap">サイトマップ<\/a>.*<\/li>.*<\/ul>/s', $this->BcBaser->getGlobalMenu());
    }

    /**
     * Google Analytics のトラッキングコードを出力する
     * @return void
     */
    public function testGoogleAnalytics()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->expectOutputRegex('/<script>.*gtag\(\'config\', \'hoge\'\)\;/s', $this->BcBaser->googleAnalytics());
    }

    /**
     * Google Maps を取得する
     * @return void
     * タイミングによってTravisCI上でテストが失敗するので一時的にコメントアウト
     * GoogleAPI側の問題の可能性あり、テスト内容または、処理内容を見直す必要あり
     */
    public function testGetGoogleMaps()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        for($i = 0; $i < 3; $i++) {
            $result = $this->BcBaser->getGoogleMaps();
            if ($result && !preg_match('/^Google Maps を読み込めません。/', $result)) {
                break;
            }
        }
        $this->assertMatchesRegularExpression('/<div id="map"/', $result);
    }

    /**
     * 表示件数設定機能を出力する
     *
     * BcContentsRoute::match() に途中までの処理を記述している
     *
     * @return void
     * @TODO 現在の仕様として、Contents テーブルで管理しているURLの場合、URLが解決できない
     */
    public function testListNum()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->BcBaser->request = $this->_getRequest('/search_indices/search');
        $this->expectOutputRegex('/<div class="list-num">.*<span><a href="\/search_indices\/search\/num:100">100<\/a><\/span><\/p>.*<\/div>/s');
        $this->BcBaser->listNum();
    }

    /**
     * サイト内検索フォームを取得
     * @return void
     */
    public function testGetSiteSearchForm()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->assertMatchesRegularExpression('/<div class="section search-box">.*<input.*?type="submit" value="検索"\/>.*<\/form><\/div>/s', $this->BcBaser->getSiteSearchForm());
    }

    /**
     * Webサイト名を取得する
     * @return void
     */
    public function testGetSiteName()
    {
        $this->assertEquals('メインサイト', $this->BcBaser->getSiteName());
        $this->BcBaser->getView()->setRequest($this->getRequest('/en/'));
        $this->assertEquals('英語サイト', $this->BcBaser->getSiteName());
    }

    /**
     * WebサイトURLを取得する
     * @return void
     */
    public function testGetSiteUrl()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        Configure::write('BcEnv.siteUrl', 'https://basercms.net/');
        Configure::write('BcEnv.sslUrl', 'https://basercms.net/');

        // http
        $this->assertEquals('https://basercms.net/', $this->BcBaser->getSiteUrl());
        //https
        $this->assertEquals('https://basercms.net/', $this->BcBaser->getSiteUrl(true));
    }

    /**
     * パラメータ情報を取得する
     * @return void
     */
    public function testGetParams()
    {
        $this->BcBaser->getView()->setRequest($this->getRequest('/'));
        $params = $this->BcBaser->getParams();
        $this->assertEquals('BaserCore', $params['plugin']);
        $this->assertEquals('Pages', $params['controller']);
        $this->assertEquals('view', $params['action']);
        $this->assertEquals(['index'], $params['pass']);
    }

    /**
     * URL情報を取得する
     * @return void
     */
    public function testGetUrlParams()
    {
        $this->BcBaser->getView()->setRequest($this->getRequest('/?a=b'));
        $urlParams = $this->BcBaser->getUrlParams();
        $this->assertEquals([
            'url' => 'https://localhost/?a=b',
            'here' => '/',
            'path' => '/',
            'webroot' => '/',
            'base' => '',
            'query' => [
                'a' => 'b',
            ],
        ], $urlParams);

        $this->BcBaser->getView()->setRequest(
            $this->getRequest('/test', [], null, ['base' => '/baser', 'webroot' => '/baser/'])
        );
        $urlParams = $this->BcBaser->getUrlParams();
        $this->assertEquals([
            'url' => 'https://localhost/baser/test',
            'here' => '/baser/test',
            'path' => '/test',
            'webroot' => '/baser/',
            'base' => '/baser',
            'query' => [],
        ], $urlParams);
    }

    /**
     * @return void
     * プラグインの Baser ヘルパを取得する
     */
    public function testGetPluginBaser()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $PluginBaser = $this->BcBaser->getPluginBaser('BcBlog');
        $this->assertEquals('BlogBaserHelper', get_class($PluginBaser));
        $this->assertFalse($this->BcBaser->getPluginBaser('hoge'));
    }

    /**
     * コンテンツ管理用のURLより、正式なURLを取得する
     * @return void
     */
    public function testGetContentsUrl()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        // URLが設定されていない場合
        $this->BcBaser->request = $this->_getRequest('/news/');
        $this->assertEquals('/news/', $this->BcBaser->getContentsUrl());
        // URLの指定がある場合
        $this->BcBaser->request = $this->_getRequest('/');
        $this->assertEquals('/news/', $this->BcBaser->getContentsUrl('/news/'));
        // サブドメインの指定がない場合
        Configure::write('BcEnv.host', 'another.com');
        $this->BcBaser->request = $this->_getRequest('/news/');
        $this->assertEquals('http://another.com/news/', $this->BcBaser->getContentsUrl(null, true));
        // サブドメインの指定がある場合
        Configure::write('BcEnv.host', 'localhost');
        $this->BcBaser->request = $this->_getRequest('/');
        $this->assertEquals('http://another.com/news/', $this->BcBaser->getContentsUrl('/another.com/news/', true, true));
        // サブドメインの指定がないのに指定ありとした場合
        $siteUrl = Configure::read('BcEnv.siteUrl');
        Configure::write('BcEnv.siteUrl', 'http://main.com');
        $this->assertEquals('http://main.com/news/', $this->BcBaser->getContentsUrl('/news/', true, true));
        Configure::write('BcEnv.siteUrl', $siteUrl);
    }

    /**
     * @return void
     */
    public function testGetUpdateInfo()
    {
        $this->assertMatchesRegularExpression('//', $this->BcBaser->getUpdateInfo());
    }

    /**
     * @return void
     */
    public function testGetRelatedSiteLinks()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        $this->assertMatchesRegularExpression('/<ul class="related-site-links">/s', $this->BcBaser->getRelatedSiteLinks());
    }

    /**
     * @return void
     */
    public function test__call()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * @return void
     */
    public function test__construct()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * @return void
     */
    public function test_unsetIndexInContentsMenu()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * @return void
     */
    public function testAfterRender()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * @return void
     */
    public function testCurrentPrefix()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * @return void
     */
    public function testGetSitePrefix()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * @return void
     */
    public function testWebClipIcon()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        ob_start();
        $this->BcBaser->webClipIcon('', false);
        $result = ob_get_clean();
        $this->assertMatchesRegularExpression('/<link rel="apple-touch-icon-precomposed/s', $result);

        ob_start();
        $this->BcBaser->webClipIcon('', true);
        $result = ob_get_clean();
        $this->assertMatchesRegularExpression('/<link rel="apple-touch-icon/s', $result);
    }

    /**
     * testSetAlternateUrl
     * @dataProvider setAlternateUrlDataProvider
     */
    public function testSetAlternateUrl($url, $expected)
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
        Configure::write('BcSite.use_site_device_setting', true);
        $this->BcBaser->request = $this->_getRequest($url);
        $this->BcBaser->setAlternateUrl();
        $this->assertEquals($expected, $this->_View->fetch('meta'));
    }

    public function setAlternateUrlDataProvider()
    {
        return [
            ['/', '<link href="http://localhost/s/" rel="alternate" media="only screen and (max-width: 640px)"/>'],
            ['/s/', '']
        ];
    }

    /**
     * testSetAlternateUrl
     * @dataProvider setCanonicalUrlDataProvider
     */
    public function testSetCanonicalUrl($siteId, $url, $expected)
    {
        // TODO FixtureManager が削除できたら、ここも削除する
        // >>>
        $this->truncateTable('sites');
        $this->truncateTable('contents');
        $this->truncateTable('content_folders');
        $this->truncateTable('users');
        $this->truncateTable('user_groups');
        // <<<

        $this->loadFixtureScenario(InitAppScenario::class);
        SiteFactory::make([
            'id' => 2,
            'main_site_id' => 1,
            'alias' => 's',
            'device' => 'smartphone'
        ])->persist();
        ContentFactory::make([
            'site_id' => $siteId,
            'url' => $url
        ])->persist();

        $this->BcBaser->getView()->setRequest($this->getRequest($url));
        $this->BcBaser->setCanonicalUrl();
        $this->assertEquals($expected, $this->BcBaser->getView()->fetch('meta'));
    }

    public function setCanonicalUrlDataProvider()
    {
        return [
            [1, '/', '<link href="https://localhost/" rel="canonical">'],
            [1, '/index.html', '<link href="https://localhost/" rel="canonical">'],
            [1, '/about/index.html', '<link href="https://localhost/about/" rel="canonical">'],
            [2, '/s/', '<link href="https://localhost/" rel="canonical">'],
        ];
    }

}
