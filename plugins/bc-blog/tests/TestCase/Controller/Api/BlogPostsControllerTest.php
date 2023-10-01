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

namespace BcBlog\Test\TestCase\Controller\Api;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Controller\Api\BlogPostsController;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogPostsControllerTest
 * @property BlogPostsController $BlogPostsController
 */
class BlogPostsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;

    /**
     * Access Token
     * @var string
     */
    public $accessToken = null;

    /**
     * Refresh Token
     * @var null
     */
    public $refreshToken = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $token = $this->apiLoginAdmin(1);
        $this->accessToken = $token['access_token'];
        $this->refreshToken = $token['refresh_token'];
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test index
     */
    public function test_index()
    {
        //データを生成
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '1', 'title' => 'blog post', 'status' => true])->persist();
        //APIを呼ぶ
        $this->get('/baser/api/bc-blog/blog_posts/index/1.json?token=' . $this->accessToken);
        //responseを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(1, $result->blogPosts);
    }

    /**
     * test view
     */
    public function test_view()
    {
        // データを生成
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        BlogPostFactory::make(['id' => '1', 'blog_content_id' => '1', 'title' => 'blog post', 'eye_catch' => 'eye_catch.img', 'status' => true])->persist();
        ContentFactory::make(['type' => 'BlogContent', 'entity_id' => 1])->persist();
        PermissionFactory::make()->allowGuest('/baser/api/*')->persist();

        // APIを呼ぶ
        $this->get('/baser/api/bc-blog/blog_posts/view/1.json');
        // レスポンスを確認
        $this->assertResponseOk();
        // 戻り値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, $result->blogPost->id);
        $this->assertTextContains('/files/blog/1/blog_posts/eye_catch.img', $result->blogPost->_eyecatch);
        $this->assertEquals(1, $result->blogPost->blog_content_id);

        //存在しないBlogPostIDをテスト場合、
        // APIを呼ぶ
        $this->get('/baser/api/bc-blog/blog_posts/view/100.json?token=' . $this->accessToken);
        // レスポンスを確認
        $this->assertResponseCode(404);
        // 戻り値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('データが見つかりません。', $result->message);

        //ログインしていない状態では status パラメーターへへのアクセスを禁止するか確認
        $this->get('/baser/api/bc-blog/blog_posts/view/1.json?status=publish');
        // レスポンスを確認
        $this->assertResponseCode(403);
    }

}
