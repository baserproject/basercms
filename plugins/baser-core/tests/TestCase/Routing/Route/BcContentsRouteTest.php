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

namespace BaserCore\Test\TestCase\Routing\Route;

use BaserCore\Routing\Route\BcContentsRoute;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Test\Scenario\ContentBcContentsRouteScenario;
use BaserCore\Test\Scenario\SiteBcContentsRouteScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Core\Configure;
use Cake\Routing\Router;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcContentsRoute
 *
 * @property BcContentsRoute $BcContentsRoute
 */
class BcContentsRouteTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcContentsRoute = new BcContentsRoute('/', [], []);
    }

    /**
     * tear down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcContentsRoute);
        parent::tearDown();
    }

    /**
     * コンテンツに関連するパラメーター情報を取得する
     */
    public function testGetParams()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * リバースルーティング
     *
     * @param string $current 現在のURL
     * @param string $params URLパラメーター
     * @param string $expects 期待するURL
     * @dataProvider reverseRoutingDataProvider
     */
    public function testMatch($current, $params, $expects)
    {
        $this->loadFixtureScenario(ContentBcContentsRouteScenario::class);
        $this->loadFixtureScenario(SiteBcContentsRouteScenario::class);
        Router::setRequest($this->getRequest($current));
        $this->assertEquals($expects, Router::url($params));
    }

    public static function reverseRoutingDataProvider()
    {
        return [
            // Page
            ['/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'display', 'index'], '/index'],
            ['/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'display', 'service', 'service1'], '/service/service1'],
            // Blog
            ['/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1], '/news/'],
            ['/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 1, 2], '/news/archives/2'],
            ['/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 1, '?' => ['page' => 2], 2], '/news/archives/2?page=2'],
            ['/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 1, 'category', 'release'], '/news/archives/category/release'],
        ];
    }

    /**
     * Router::parse
     *
     * @param string $url URL文字列
     * @param string $expect 期待値
     * @return void
     * @dataProvider routerParseDataProvider
     */
    public function testParse($useSiteDeviceSetting, $host, $ua, $url, $expects)
    {
        $this->loadFixtureScenario(ContentBcContentsRouteScenario::class);
        $this->loadFixtureScenario(SiteBcContentsRouteScenario::class);
        Configure::write('BcEnv.siteUrl', 'http://main.com/');
        Configure::write('BcSite.use_site_device_setting', $useSiteDeviceSetting);
        if ($ua) {
            $_SERVER['HTTP_USER_AGENT'] = $ua;
        }
        if ($host) {
            Configure::write('BcEnv.host', $host);
        }
        $result = Router::parseRequest($this->getRequest($url));
        unset($result['Content']);
        unset($result['Site']);
        unset($result['_route']);
        $this->assertEquals($expects, $result);
    }

    public static function routerParseDataProvider()
    {
        return [
            // PC（ノーマル : デバイス設定無）
            [0, '', '', '/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 1, 'pass' => ['index'], 'named' => [], '_matchedRoute' => '/']],
            [0, '', '', '/index', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 1, 'pass' => ['index'], 'named' => [], '_matchedRoute' => '/*']],
            // 以下、ブログプラグインなどのコントローラークラスを参照するためそちらを移行してから移行する
            [0, '', '', '/news/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/news/index', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/news/archives/1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 1, 'pass' => [1], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/news/index/page:1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => ['page' => 1], '_matchedRoute' => '/*']],
            [0, '', '', '/service/', ['plugin' => 'BaserCore', 'controller' => 'ContentFolders', 'action' => 'view', 'entityId' => 4, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/service/service1', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 3, 'pass' => ['service', 'service1'], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/service/contact/', ['plugin' => 'BcMail', 'controller' => 'Mail', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            // モバイル（別URL : デバイス設定有）
            [1, '', 'SoftBank', '/m/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 4, 'pass' => ['m', 'index'], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'SoftBank', '/m/index', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 4, 'pass' => ['m', 'index'], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'SoftBank', '/m/news/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 2, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'SoftBank', '/m/news/index', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 2, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'SoftBank', '/m/news/archives/1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 2, 'pass' => [1], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'SoftBank', '/m/news/index/page:1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 2, 'pass' => [], 'named' => ['page' => 1], '_matchedRoute' => '/*']],
            [1, '', 'SoftBank', '/m/service/', ['plugin' => 'BaserCore', 'controller' => 'ContentFolders', 'action' => 'view', 'entityId' => 11, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'SoftBank', '/m/service/service1', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 10, 'pass' => ['m', 'service', 'service1'], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'SoftBank', '/m/service/contact/', ['plugin' => 'BcMail', 'controller' => 'Mail', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            // スマホ（同一URL / エイリアス : デバイス設定有）
            [1, '', 'iPhone', '/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 1, 'pass' => ['index'], 'named' => [], '_matchedRoute' => '/']],
            [1, '', 'iPhone', '/index', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 1, 'pass' => ['index'], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'iPhone', '/news/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'iPhone', '/news/index', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'iPhone', '/news/archives/1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 1, 'pass' => [1], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'iPhone', '/news/index/page:1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => ['page' => 1], '_matchedRoute' => '/*']],
            [1, '', 'iPhone', '/service/', ['plugin' => 'BaserCore', 'controller' => 'ContentFolders', 'action' => 'view', 'entityId' => '4', 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'iPhone', '/service/service1', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => '3', 'pass' => ['service', 'service1'], 'named' => [], '_matchedRoute' => '/*']],
            [1, '', 'iPhone', '/service/contact/', ['plugin' => 'BcMail', 'controller' => 'Mail', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            // スマホ（ノーマル : デバイス設定無）
            [0, '', 'iPhone', '/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 1, 'pass' => ['index'], 'named' => [], '_matchedRoute' => '/']],
            [0, '', 'iPhone', '/index', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 1, 'pass' => ['index'], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', 'iPhone', '/news/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', 'iPhone', '/news/index', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', 'iPhone', '/news/archives/1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 1, 'pass' => [1], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', 'iPhone', '/news/index/page:1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => ['page' => 1], '_matchedRoute' => '/*']],
            [0, '', 'iPhone', '/service/', ['plugin' => 'BaserCore', 'controller' => 'ContentFolders', 'action' => 'view', 'entityId' => '4', 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', 'iPhone', '/service/service1', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => '3', 'pass' => ['service', 'service1'], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', 'iPhone', '/service/contact/', ['plugin' => 'BcMail', 'controller' => 'Mail', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            // PC（英語 : デバイス設定無）
            [0, '', '', '/en/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 12, 'pass' => ['en', 'index'], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/en/index', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 12, 'pass' => ['en', 'index'], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/en/news/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 3, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/en/news/index', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 3, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/en/news/archives/1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 3, 'pass' => [1], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/en/news/index/page:1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => '3', 'pass' => [], 'named' => ['page' => 1], '_matchedRoute' => '/*']],
            [0, '', '', '/en/service/', ['plugin' => 'BaserCore', 'controller' => 'ContentFolders', 'action' => 'view', 'entityId' => '8', 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/en/service/service1', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 8, 'pass' => ['en', 'service', 'service1'], 'named' => [], '_matchedRoute' => '/*']],
            [0, '', '', '/en/service/contact/', ['plugin' => 'BcMail', 'controller' => 'Mail', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            // PC（サブドメイン : デバイス設定無）
            [0, 'sub.main.com', '', '/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 13, 'pass' => ['sub', 'index'], 'named' => [], '_matchedRoute' => '/']],
            [0, 'sub.main.com', '', '/index', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 13, 'pass' => ['sub', 'index'], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'sub.main.com', '', '/news/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 4, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'sub.main.com', '', '/news/index', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => '4', 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'sub.main.com', '', '/news/archives/1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 4, 'pass' => [1], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'sub.main.com', '', '/news/index/page:1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => ['page' => 1], '_matchedRoute' => '/*']],
            [0, 'sub.main.com', '', '/service/', ['plugin' => 'BaserCore', 'controller' => 'ContentFolders', 'action' => 'view', 'entityId' => 9, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'sub.main.com', '', '/service/service1', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 9, 'pass' => ['sub', 'service', 'service1'], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'sub.main.com', '', '/service/contact/', ['plugin' => 'BcMail', 'controller' => 'Mail', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            // PC（別ドメイン : デバイス設定無）
            [0, 'another.com', '', '/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 14, 'pass' => ['another.com', 'index'], 'named' => [], '_matchedRoute' => '/']],
            [0, 'another.com', '', '/index', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 14, 'pass' => ['another.com', 'index'], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'another.com', '', '/news/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 5, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'another.com', '', '/news/index', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 5, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'another.com', '', '/news/archives/1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 5, 'pass' => [1], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'another.com', '', '/news/index/page:1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => ['page' => 1], '_matchedRoute' => '/*']],
            [0, 'another.com', '', '/service/', ['plugin' => 'BaserCore', 'controller' => 'ContentFolders', 'action' => 'view', 'entityId' => 10, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'another.com', '', '/service/service1', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 11, 'pass' => ['another.com', 'service', 'service1'], 'named' => [], '_matchedRoute' => '/*']],
            [0, 'another.com', '', '/service/contact/', ['plugin' => 'BcMail', 'controller' => 'Mail', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            // スマホ（別ドメイン / 同一URL / 別コンテンツ : デバイス設定有）
            [1, 'another.com', 'iPhone', '/', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 14, 'pass' => ['another.com', 'index'], 'named' => [], '_matchedRoute' => '/']],
            [1, 'another.com', 'iPhone', '/index', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 14, 'pass' => ['another.com', 'index'], 'named' => [], '_matchedRoute' => '/*']],
            [1, 'another.com', 'iPhone', '/news/', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 5, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [1, 'another.com', 'iPhone', '/news/index', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 5, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [1, 'another.com', 'iPhone', '/news/archives/1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'archives', 'entityId' => 5, 'pass' => [1], 'named' => [], '_matchedRoute' => '/*']],
            [1, 'another.com', 'iPhone', '/news/index/page:1', ['plugin' => 'BcBlog', 'controller' => 'Blog', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => ['page' => 1], '_matchedRoute' => '/*']],
            [1, 'another.com', 'iPhone', '/service/', ['plugin' => 'BaserCore', 'controller' => 'ContentFolders', 'action' => 'view', 'entityId' => 10, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
            [1, 'another.com', 'iPhone', '/service/service1', ['plugin' => 'BaserCore', 'controller' => 'Pages', 'action' => 'view', 'entityId' => 11, 'pass' => ['another.com', 'service', 'service1'], 'named' => [], '_matchedRoute' => '/*']],
            [1, 'another.com', 'iPhone', '/service/contact/', ['plugin' => 'BcMail', 'controller' => 'Mail', 'action' => 'index', 'entityId' => 1, 'pass' => [], 'named' => [], '_matchedRoute' => '/*']],
        ];
    }

    /**
     * test _getContentTypeByParams
     */
    public function test_getContentTypeByParams()
    {
        //configure
        Configure::write('BcContents.items', [
            'YourPlugin' => [
                'item1' => [
                    'routes' => [
                        'view' => [
                            'controller' => 'YourController',
                            'action' => 'view'
                        ]
                    ]
                ],
                'item2' => [
                    'routes' => [
                        'view' => [
                            'controller' => 'YourController',
                            'action' => 'otherAction'
                        ]
                    ]
                ]
            ]
        ]);

        //testGetContentTypeByParamsWithNoMatch
        $params = [
            'plugin' => 'your_plugin',
            'controller' => 'NonExistentController',
            'action' => 'nonExistentAction'
        ];

        $result = $this->execPrivateMethod($this->BcContentsRoute, '_getContentTypeByParams', [$params, true]);
        $this->assertFalse($result);

        //testGetContentTypeByParamsWithNoPlugin
        $params = [
            'plugin' => 'non_existent_plugin',
            'controller' => 'YourController',
            'action' => 'view'
        ];

        $result = $this->execPrivateMethod($this->BcContentsRoute, '_getContentTypeByParams', [$params, true]);
        $this->assertFalse($result);

        //testGetContentTypeByParamsWithAction
        $params = [
            'plugin' => 'your_plugin',
            'controller' => 'YourController',
            'action' => 'view'
        ];

        $result = $this->execPrivateMethod($this->BcContentsRoute, '_getContentTypeByParams', [$params, true]);
        $this->assertEquals('item1', $result);

        //testGetContentTypeByParamsWithoutAction
        $params = [
            'plugin' => 'your_plugin',
            'controller' => 'YourController'
        ];

        $result = $this->execPrivateMethod($this->BcContentsRoute, '_getContentTypeByParams', [$params, false]);
        $this->assertEquals('item1', $result);
    }

}
