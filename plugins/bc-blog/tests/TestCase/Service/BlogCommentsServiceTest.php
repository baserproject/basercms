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

namespace BcBlog\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcBlog\Service\BlogCommentsService;
use BcBlog\Test\Scenario\BlogCommentsServiceScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BlogCommentsServiceTest
 * @property BlogCommentsService $BlogCommentsService
 */
class BlogCommentsServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcBlog.Factory/BlogPosts',
        'plugin.BcBlog.Factory/BlogComments',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->BlogCommentsService = new BlogCommentsService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogCommentsService);
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->BlogCommentsService->BlogComments));
    }

    /**
     * test getIndex
     */
    public function testGetIndex()
    {
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);

        // ブログコメント一覧データを取得できるテスト
        $query = $this->BlogCommentsService->getIndex(['blog_post_id' => 1, 'num' => 2]);
        $this->assertCount(2, $query->toArray());
        $this->assertEquals(1, $query->toArray()[0]['blog_post']['id']);

        // ブログコメント一覧データを取得できないテスト
        $query = $this->BlogCommentsService->getIndex(['blog_post_id' => 9, 'num' => 2]);
        $this->assertEmpty($query->toArray());
    }

    /**
     * test get
     */
    public function testGet()
    {
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);

        // ブログコメントの単一データを取得するテスト
        $comment = $this->BlogCommentsService->get(1);
        $this->assertEquals(1, $comment->id);
        // BlogPostsのデータが含まれるテスト
        $this->assertEquals(1, $comment->blog_post->id);
    }

    /**
     * test publish
     */
    public function testPublish()
    {
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);

        $comment = $this->BlogCommentsService->publish(3);
        $this->assertTrue($comment->status);
    }

    /**
     * test unpublish
     */
    public function testUnpublish()
    {
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);

        $comment = $this->BlogCommentsService->unpublish(1);
        $this->assertFalse($comment->status);
    }

    /**
     * test delete
     */
    public function testDelete()
    {
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);
        $count = $this->BlogCommentsService->getIndex(['blog_post_id' => 1])->count();

        // ブログコメントを削除するテスト
        $comment = $this->BlogCommentsService->delete(1);
        $this->assertTrue($comment);

        // 削除が成功ならコメント数が１単位減る
        $this->assertEquals($count - 1, $this->BlogCommentsService->getIndex(['blog_post_id' => 1])->count());
    }

    /**
     * test batch
     */
    public function testBatch()
    {
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);
        $ids = [1, 2, 3];

        // 一括でブログコメントを非公開するテスト
        $result = $this->BlogCommentsService->batch('unpublish', $ids);
        $this->assertTrue($result);
        foreach ($ids as $id) {
            $comment = $this->BlogCommentsService->get($id);
            $this->assertFalse($comment->status);
        }

        // 一括でブログコメントを公開するテスト
        $result = $this->BlogCommentsService->batch('publish', $ids);
        $this->assertTrue($result);
        foreach ($ids as $id) {
            $comment = $this->BlogCommentsService->get($id);
            $this->assertTrue($comment->status);
        }

        // 一括でブログコメントを削除するテスト
        $count = $this->BlogCommentsService->getIndex(['blog_post_id' => 1])->count();
        $result = $this->BlogCommentsService->batch('delete', $ids);
        $this->assertTrue($result);
        $this->assertEquals($count - 3, $this->BlogCommentsService->getIndex(['blog_post_id' => 1])->count());
    }

}
