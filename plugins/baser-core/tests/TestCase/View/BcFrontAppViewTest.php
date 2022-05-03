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
        $this->BcFrontAppView = new BcFrontAppView();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->BcFrontAppView);
    }

    /**
     * test initialize
     */
    public function testInitialize()
    {
        $this->assertFalse(isset($this->BcFrontAppView->BcSmartphone));
        /* @var \BaserCore\Service\SitesServiceInterface $siteService */
        $siteService = $this->getService(SitesServiceInterface::class);
        $site = $siteService->get(2);
        $siteService->update($site, ['status' => true]);
        $this->BcFrontAppView->setRequest($this->getRequest('/s/'));
        $this->BcFrontAppView->initialize();
        $this->assertTrue(isset($this->BcFrontAppView->BcSmartphone));
    }

}
