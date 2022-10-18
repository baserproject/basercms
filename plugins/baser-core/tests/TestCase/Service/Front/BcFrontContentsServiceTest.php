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

namespace BaserCore\Test\TestCase\Service\Front;

use BaserCore\Service\Front\BcFrontContentsService;
use BaserCore\TestSuite\BcTestCase;

/**
 * BcFrontContentsServiceTest
 *
 * @property BcFrontContentsService $BcFrontContentsService
 */
class BcFrontContentsServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcFrontContentsService = new BcFrontContentsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcFrontContentsService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForFront
     */
    public function test_getViewVarsForFront()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getCrumbs
     */
    public function test_getCrumbs()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
