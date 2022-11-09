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

namespace BcContentLink\Test\TestCase\Controller;

use BcContentLink\Controller\ContentLinksController;
use BaserCore\TestSuite\BcTestCase;

/**
 * ContentLinksControllerTest
 * @property ContentLinksController $ContentLinksController
 */
class ContentLinksControllerTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentLinksController = new ContentLinksController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentLinksController);
        parent::tearDown();
    }

    /**
     * Test initialize method
     */
    public function test_initialize()
    {
        $this->assertNotEmpty($this->ContentLinksController->BcFrontContents);
    }

}
