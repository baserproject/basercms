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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Service\PagesFrontService;
use BaserCore\Service\PagesFrontServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;

/**
 * PagesFrontServiceTest
 */
class PagesFrontServiceTest extends BcTestCase
{

    public $fixtures = [
        'plugin.BaserCore.Pages',
    ];

    /**
     * Trait
     */
    use BcContainerTrait;

    /**
     * PagesFront
     * @var PagesFrontService
     */
    public $PagesFront;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->PagesFront = $this->getService(PagesFrontServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->PagesFront);
    }

    /**
     * test getViewVarsForDisplay
     */
    public function test_getViewVarsForDisplay()
    {
        $vars = $this->PagesFront->getViewVarsForDisplay(
            $this->PagesFront->get(2),
            $this->getRequest('/')
        );
        $this->assertArrayHasKey('page', $vars);
        $this->assertArrayHasKey('editLink', $vars);
    }
}
