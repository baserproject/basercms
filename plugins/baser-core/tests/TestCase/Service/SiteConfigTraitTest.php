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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;

/**
 * Class SiteConfigTraitTest
 * @package BaserCore\Test\TestCase\Service
 */
class SiteConfigTraitTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->trait = $this->getObjectForTrait('BaserCore\Service\SiteConfigTrait');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->trait);
        parent::tearDown();
    }

    /**
     * testGetSiteConfig
     *
     * @return void
     */
    public function testGetSiteConfig(): void
    {
        $this->assertEquals(10, $this->trait->getSiteConfig('admin_list_num'));
    }
    /**
     * testSetSiteConfig
     *
     * @return void
     */
    public function testSetSiteConfig(): void
    {
        $this->assertNotEmpty($this->trait->setSiteConfig('admin_list_num', 30));
        $this->assertEquals(30, $this->trait->getSiteConfig('admin_list_num'));
    }
    /**
     * testresetSiteConfig
     *
     * @return void
     */
    public function testResetSiteConfig(): void
    {
        $this->assertNotEmpty($this->trait->resetSiteConfig('admin_list_num'));
        $this->assertEquals('', $this->trait->getSiteConfig('admin_list_num'));
    }
}
