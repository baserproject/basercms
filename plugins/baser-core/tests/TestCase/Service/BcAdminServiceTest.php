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

use BaserCore\Service\BcAdminService;
use BaserCore\Service\BcAdminServiceInterface;
use BaserCore\Utility\BcContainerTrait;

/**
 * Class BcAdminServiceTest
 */
class BcAdminServiceTest extends \BaserCore\TestSuite\BcTestCase
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
    protected $fixtures = [
        'plugin.BaserCore.Sites',
    ];

    /**
     * BcAdminService
     * @var BcAdminService
     */

    public $BcAdminService;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdminService = $this->getService(BcAdminServiceInterface::class);
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcAdminSite);
        parent::tearDown();
    }

    /**
     * test setCurrentSite And getCurrentSite
     */
    public function testSetCurrentSiteAndGetCurrentSite()
    {
        $this->getRequest('/?current_site_id=3');
        $this->BcAdminService->setCurrentSite();
        $this->assertEquals(3, $this->BcAdminService->getCurrentSite()->id);
    }

    /**
     * test getOtherSiteList
     */
    public function testGetOtherSiteList()
    {
        $this->getRequest('/?current_site_id=3');
        $this->BcAdminService->setCurrentSite();
        $siteList = $this->BcAdminService->getOtherSiteList();
        $this->assertArrayNotHasKey(3, $siteList);
    }

}
