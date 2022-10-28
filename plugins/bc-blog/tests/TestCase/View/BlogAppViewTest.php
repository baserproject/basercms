<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Test.Case.View.Helper
 * @since           baserCMS v 3.0.6
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\View;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\View\BlogFrontAppView;

/**
 * @property BlogFrontAppView $BlogFrontAppView
 */
class BlogAppViewTest extends BcTestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogFrontAppView = new BlogFrontAppView($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogFrontAppView);
        parent::tearDown();
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function test_initialize(): void
    {
        $this->assertNotEmpty($this->BlogFrontAppView->Blog);
    }

}
