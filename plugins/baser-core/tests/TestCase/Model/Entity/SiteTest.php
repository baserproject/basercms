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

namespace BaserCore\Test\TestCase\Model\Entity;

use BaserCore\Model\Entity\Site;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class SiteTest
 */
class SiteTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
//        'plugin.BaserCore.Model/Entity/Site/ContentShouldRedirects',
//        'plugin.BaserCore.Model/Entity/Site/SiteShouldRedirects'
    ];

    /**
     * autoFixtures
     * @var bool
     */
    // TODO loadFixtures を利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要
//    public $autoFixtures = false;

    /**
     * @var Site
     */
    public $Site;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Sites = $this->getTableLocator()->get('BaserCore.Sites');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Sites);
        parent::tearDown();
    }

    /**
     * test _getDomainType
     */
    public function test_getDomainType()
    {
        $site = new Site([
            'use_subdomain' => true,
            'domain_type' => null
        ]);
        $this->assertEquals(1, $site->domain_type);
    }

    /**
     * test _getAlias
     */
    public function test_getAlias()
    {
        $site = new Site([
            'name' => 'test'
        ]);
        $this->assertEquals('test', $site->alias);
    }

    /**
     * test _getHost
     */
    public function test_getHost()
    {
        $site = new Site([
            'use_subdomain' => true,
            'domain_type' => null,
            'alias' => 'sub'
        ]);
        $this->assertEquals('sub.localhost', $site->host);
        $site = new Site([
            'use_subdomain' => true,
            'domain_type' => 2,
            'alias' => 'basercms.net'
        ]);
        $this->assertEquals('basercms.net', $site->host);
    }

    /**
     * test getMain
     */
    public function testGetMain()
    {
        $site = new Site(['id' => 1]);
        $this->assertEquals(1, $site->getMain()->id);
        $site = new Site([
            'id' => '3',
        ]);
        $this->assertEquals(1, $site->getMain()->id);
    }

    /**
     * test getPureUrl
     */
    public function testGetPureUrl()
    {
        $site = new Site([
            'alias' => 's'
        ]);
        $this->assertEquals('/about/index', $site->getPureUrl('/s/about/index'));
    }

    /**
     * エイリアスを反映したURLを生成
     * 同一URL設定のみ利用可
     * @dataProvider makeUrlDataProvider
     */
    public function testMakeUrl($alias, $url, $expected)
    {
        $request = $this->getRequest($url);
        $site = $this->Sites->findByAlias($alias)->first();
        $url = $site->makeUrl($request);
        $this->assertEquals($expected, $url);
    }

    public function makeUrlDataProvider()
    {
        return [
            ['', '/', '/'],
            ['', '/index', '/'],
            ['', '/about', '/about'],
            ['s', '/', '/s/'],
            ['s', '/index', '/s/'],
            ['s', '/about', '/s/about'],
        ];
    }

    /**
     * test existsUrl
     *
     * @dataProvider existsUrlDataProvider
     */
    public function testExistsUrl($url, $expected)
    {
        $request = $this->getRequest($url);
        $site = $this->Sites->findByUrl($url);
        $actual = $site->existsUrl($request);
        $this->assertEquals($expected, $actual);
    }

    public function existsUrlDataProvider()
    {
        return [
            ['/', true],
            ['/index', true],
            ['/index?query=1', true],
            ['/about', true],
            ['/404', false]
        ];
    }

    /**
     * 与えられたリクエストに対して自動リダイレクトすべきかどうかを返す
     *
     * @param bool $expect 期待値
     * @param string $url URL文字列
     * @param array $query クエリパラメータの配列
     * @return void
     * @dataProvider shouldRedirectsDataProvider
     */
    public function testShouldRedirects($expect, $url, array $query = [])
    {
        $this->markTestIncomplete('loadFixtures を利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        $this->loadFixtures(
            'Model\Entity\Site\ContentShouldRedirects',
            'Model\Entity\Site\SiteShouldRedirects'
        );
        $request = $this->getRequest($url);
        $request = $request->withQueryParams($query);
        $this->assertEquals($expect, $this->Sites->get(2)->shouldRedirects($request));
    }

    public function shouldRedirectsDataProvider()
    {
        return [
            [true, '/'],
            [true, '/news/index'],
            [false, '/news/index', ['smartphone' => 'off']],
            [true, '/news/index', ['smartphone' => 'on']],
            [false, '/s/'],
            [false, '/s/news/index'],
        ];
    }

}
