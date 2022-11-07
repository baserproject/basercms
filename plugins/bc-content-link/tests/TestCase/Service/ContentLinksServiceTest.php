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

namespace BcContentLink\Test\TestCase\Service;

use BcContentLink\Service\ContentLinksService;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class ContentLinksServiceTest
 * @property ContentLinksService $ContentLinksService
 */
class ContentLinksServiceTest extends BcTestCase
{

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentLinksService = new ContentLinksService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentLinksService);
        parent::tearDown();
    }

    /**
     * @test construct
     * @return void
     */
    public function test__construct(): void
    {
        $this->assertTrue(isset($this->ContentLinksService->ContentLinks));
    }

}
