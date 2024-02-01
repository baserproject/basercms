<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcBlog\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\View\Helper\BcBlogBaserHelper;
use Cake\View\View;

/**
 * BcBlogBaserHelper test
 *
 * @property BcBlogBaserHelper $BcBlogBaserHelper
 */
class BcBlogBaserHelperTest extends BcTestCase
{
    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcBlogBaserHelper = new BcBlogBaserHelper(new View());
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function test_methods()
    {
        $methods = $this->BcBlogBaserHelper->methods();
        $this->assertEquals(['Blog', 'posts'], $methods['blogPosts']);
        $this->assertEquals(['Blog', 'getPosts'], $methods['getBlogPosts']);
        $this->assertEquals(['Blog', 'nextLink'], $methods['blogPostNextLink']);
    }
}
