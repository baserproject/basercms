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

namespace BcBlog\Test\TestCase\Controller;

use BcBlog\Controller\BlogFrontAppController;
use Cake\Event\Event;
use BaserCore\TestSuite\BcTestCase;

/**
 * BlogFrontAppControllerTest
 *
 * @property  BlogFrontAppController $BlogFrontAppController
 */
class BlogFrontAppControllerTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogFrontAppController = new BlogFrontAppController($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogFrontAppController);
        parent::tearDown();
    }

    /**
     * Test beforeRender
     */
    public function test_beforeRender()
    {
        $this->BlogFrontAppController->beforeRender(new Event('beforeRender'));
        $this->assertEquals('BcBlog.BlogFrontApp', $this->BlogFrontAppController->viewBuilder()->getClassName());
    }

}
