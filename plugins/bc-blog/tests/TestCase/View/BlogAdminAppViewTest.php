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

use BcBlog\View\BlogAdminAppView;
use BaserCore\TestSuite\BcTestCase;

/**
 * BcContents helper library.
 *
 * @package Baser.Test.Case
 * @property BlogAdminAppView $BlogAdminAppView
 */
class BlogAdminAppViewTest extends BcTestCase
{

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogAdminAppView = new BlogAdminAppView($this->getRequest());
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogAdminAppView);
        parent::tearDown();
    }

    /**
     * test initialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->BlogAdminAppView->initialize();
        $this->assertNotEmpty($this->BlogAdminAppView->Blog);
    }

}
