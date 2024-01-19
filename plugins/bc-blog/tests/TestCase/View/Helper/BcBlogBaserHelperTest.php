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
        $expected = [
            'blogPosts' => ['Blog', 'posts'],
            'getBlogPosts' => ['Blog', 'getPosts'],
            'isBlogCategory' => ['Blog', 'isCategory'],
            'isBlogTag' => ['Blog', 'isTag'],
            'isBlogDate' => ['Blog', 'isDate'],
            'isBlogMonth' => ['Blog', 'isMonth'],
            'isBlogYear' => ['Blog', 'isYear'],
            'isBlogSingle' => ['Blog', 'isSingle'],
            'isBlogHome' => ['Blog', 'isHome'],
            'getBlogs' => ['Blog', 'getContents'],
            'isBlog' => ['Blog', 'isBlog'],
            'getBlogCategories' => ['Blog', 'getCategories'],
            'hasChildBlogCategory' => ['Blog', 'hasChildCategory'],
            'getBlogTagList' => ['Blog', 'getTagList'],
            'blogTagList' => ['Blog', 'tagList'],
            'getBlogContentsUrl' => ['Blog', 'getContentsUrl'],
            'getBlogPostCount' => ['Blog', 'getPostCount'],
            'getBlogTitle' => ['Blog', 'getTitle'],
            'getBlogPostLinkUrl' => ['Blog', 'getPostLinkUrl'],
            'blogPostEyeCatch' => ['Blog', 'eyeCatch'],
            'blogPostDate' => ['Blog', 'postDate'],
            'blogPostTitle' => ['Blog', 'postTitle'],
            'blogPostCategory' => ['Blog', 'category'],
            'blogPostContent' => ['Blog', 'postContent'],
            'blogDescriptionExists' => ['Blog', 'descriptionExists'],
            'blogDescription' => ['Blog', 'description'],
            'getBlogPostContent' => ['Blog', 'getPostContent'],
            'blogPostPrevLink' => ['Blog', 'prevLink'],
            'blogPostNextLink' => ['Blog', 'nextLink'],
        ];
        $methods = $this->BcBlogBaserHelper->methods();
        $this->assertEquals($expected, $methods);
    }
}
