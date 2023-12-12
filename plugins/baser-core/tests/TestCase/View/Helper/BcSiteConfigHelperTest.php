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

use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\BcAdminAppView;
use BaserCore\View\Helper\BcSiteConfigHelper;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcSiteConfigHelperTest
 *
 */
class BcSiteConfigHelperTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * BcSiteConfigHelper
     * @var BcSiteConfigHelper
     */
    public $BcSiteConfig;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->BcSiteConfig = new BcSiteConfigHelper(new BcAdminAppView($this->getRequest('/')));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcSiteConfig);
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->assertTrue(isset($this->BcSiteConfig->SiteConfigsService));
    }

    /**
     * Test getValue
     */
    public function testGetValue()
    {
        $this->assertEquals('3.0.6.1', $this->BcSiteConfig->getValue('version'));
    }
}
