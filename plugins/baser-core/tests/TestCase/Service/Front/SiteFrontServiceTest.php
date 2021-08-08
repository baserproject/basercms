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

namespace BaserCore\Test\TestCase\Service\Front;

use BaserCore\Service\Front\SiteFrontService;
use BaserCore\TestSuite\BcTestCase;
use Cake\ORM\TableRegistry;

/**
 * SiteFrontServiceTest
 */
class SiteFrontServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * Set Up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Sites = new SiteFrontService();
    }

    /**
     * Tear Down
     */
    public function tearDown(): void
    {
        unset($this->Sites);
        parent::tearDown();
    }

    /**
     * test findCurrent
     * @dataProvider findCurrentDataProvider
     */
    public function testFindCurrent($url, $expected)
    {
        $this->getRequest($url);
        $site = $this->Sites->findCurrent();
        $this->assertEquals($expected, $site->alias);
    }

    public function findCurrentDataProvider()
    {
        return [
            ['/', ''],
            ['/en/index', 'en'],
            ['/s/test', 's'],
            ['http://basercms.net/about', 'basercms.net'],
            ['http://sub.localhost/about', 'sub'],
        ];
    }

    /**
     * test findCurrentMain
     */
    public function testFindCurrentMain()
    {
        $this->getRequest('/');
        $site = $this->Sites->findCurrentMain();
        $this->assertNull($site);
        $this->getRequest('/en/');
        $site = $this->Sites->findCurrentMain();
        $this->assertEquals(1, $site->id);
    }

    /**
     * test findCurrentSub
     */
    public function testFindCurrentSub()
    {
        // スマホ
        $siteConfigs = TableRegistry::getTableLocator()->get('BaserCore.SiteConfigs');
        $siteConfigs->saveValue('use_site_device_setting', true);
        $_SERVER['HTTP_USER_AGENT'] = 'iPhone';
        $site = $this->Sites->get(2);
        $this->Sites->update($site, ['status' => true]);
        $site = $this->Sites->findCurrentSub();
        $this->assertEquals('s', $site->alias);

        // 英語
        $siteConfigs->saveValue('use_site_lang_setting', true);
        $_SERVER['HTTP_ACCEPT_LANGUAGE'] = 'en';
        $site = $this->Sites->findCurrentSub();
        $this->assertEquals('en', $site->alias);
    }

}
