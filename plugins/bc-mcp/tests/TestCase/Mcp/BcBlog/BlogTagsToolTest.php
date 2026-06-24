<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.7
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcMcp\Test\TestCase\Mcp\BcBlog;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Test\Scenario\BlogTagsScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use BcMcp\Mcp\BcBlog\BlogTagsTool;

/**
 * BlogTagsToolTest
 */
class BlogTagsToolTest extends BcTestCase
{

    use ScenarioAwareTrait;

    /**
     * @var BlogTagsTool
     */
    public $BlogTagsTool;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogTagsTool = new BlogTagsTool();
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        unset($this->BlogTagsTool);
        parent::tearDown();
    }

    /**
     * test addBlogTag
     */
    public function testAddBlogTag()
    {

        $result = $this->BlogTagsTool->addBlogTag('テストタグ');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
        $this->assertEquals('テストタグ', $result['name']);
    }

    /**
     * test getBlogTags
     */
    public function testGetBlogTags()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $result = $this->BlogTagsTool->getBlogTags();

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertArrayHasKey('data', $result);
        $this->assertIsArray($result['data']);
    }

    /**
     * test getBlogTag
     */
    public function testGetBlogTag()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $result = $this->BlogTagsTool->getBlogTag(1);

        $this->assertIsArray($result);
        $this->assertEquals(1, $result['id']);
    }

    /**
     * test editBlogTag
     */
    public function testEditBlogTag()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $result = $this->BlogTagsTool->editBlogTag(1, '更新されたタグ');

        $this->assertIsArray($result);
        $this->assertEquals('更新されたタグ', $result['name']);
    }

    /**
     * test deleteBlogTag
     */
    public function testDeleteBlogTag()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $result = $this->BlogTagsTool->deleteBlogTag(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('message', $result);
        $this->assertEquals('ブログタグを削除しました', $result['message']);
    }

    /**
     * test getBlogTags with search parameters
     */
    public function testGetBlogTagsWithSearch()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $result = $this->BlogTagsTool->getBlogTags(
            name: 'tag1'
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertEquals(1, $result['pagination']['page']);
        $this->assertEquals(10, $result['pagination']['limit']);
    }

    /**
     * test getBlogTags with limit parameter
     */
    public function testGetBlogTagsWithLimit()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $result = $this->BlogTagsTool->getBlogTags(null, 2, 1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertEquals(1, $result['pagination']['page']);
        $this->assertEquals(2, $result['pagination']['limit']);
        $this->assertArrayHasKey('data', $result);
        $this->assertLessThanOrEqual(2, count($result['data']));
    }

    /**
     * test getBlogTags with page parameter
     */
    public function testGetBlogTagsWithPage()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $result = $this->BlogTagsTool->getBlogTags(null, 2, 2);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertEquals(2, $result['pagination']['page']);
        $this->assertEquals(2, $result['pagination']['limit']);
    }

    /**
     * test getBlogTag with invalid ID
     */
    public function testGetBlogTagWithInvalidId()
    {
        $result = $this->BlogTagsTool->getBlogTag(999);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertEquals('Record not found in table `blog_tags`.', $result['content']);
    }

    /**
     * test editBlogTag with invalid ID
     */
    public function testEditBlogTagWithInvalidId()
    {
        $result = $this->BlogTagsTool->editBlogTag(999, 'Test Tag');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertEquals('Record not found in table `blog_tags`.', $result['content']);
    }

    /**
     * test deleteBlogTag with invalid ID
     */
    public function testDeleteBlogTagWithInvalidId()
    {
        $result = $this->BlogTagsTool->deleteBlogTag(999);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertEquals('Record not found in table `blog_tags`.', $result['content']);
    }
}
