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

namespace BcBlog\Test\TestCase\Model\Entity;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Entity\BlogPost;
use BcBlog\Test\Scenario\BlogContentScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogPostTest
 */
class BlogPostTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * @var BlogPost
     */
    public $BlogPost;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogPost = $this->getTableLocator()->get('BlogPost.BlogPosts');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogPost);
        parent::tearDown();
    }

    /**
     * test _get_eyecatch
     */
    public function test_get_eyecatch()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        $blogPost = new BlogPost([
            'id' => 1,
            'blog_content_id' => 1,
            'no' => 1,
            'title' => 'blog post title',
            'status' => true,
            'eye_catch' => 'test.png'
        ]);
        $_eyecatch = $this->execPrivateMethod($blogPost, '_get_eyecatch', []);
        $this->assertTextContains('/files/blog/1/blog_posts/test.png', $_eyecatch);
    }

}
