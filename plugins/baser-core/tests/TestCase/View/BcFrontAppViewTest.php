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

namespace BaserCore\Test\TestCase\View;

use BaserCore\Service\SitesServiceInterface;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\View\BcFrontAppView;
use BaserCore\TestSuite\BcTestCase;

/**
 * BcFrontAppViewTest
 * @property BcFrontAppView $BcFrontAppView
 */
class BcFrontAppViewTest extends BcTestCase
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
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders'
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test initialize
     */
    public function testInitialize()
    {
        $this->getRequest();
        $bcFrontAppView = new BcFrontAppView();
        $this->assertFalse(isset($bcFrontAppView->BcSmartphone));
        /* @var \BaserCore\Service\SitesServiceInterface $siteService */
        $siteService = $this->getService(SitesServiceInterface::class);
        $site = $siteService->get(2);
        $siteService->update($site, ['status' => true]);
        $bcFrontAppView->setRequest($this->getRequest('/s/'));
        $bcFrontAppView->initialize();
        $this->assertTrue(isset($bcFrontAppView->BcSmartphone));
    }

}
