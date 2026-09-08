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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Api\BlogTagsController;
use BaserCore\Test\Factory\ContentFactory;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Factory\BlogPostBlogTagFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Factory\BlogTagFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BlogTagsControllerTest
 * @property BlogTagsController $BlogTagsController
 */
class BlogTagsControllerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

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
    public function testIndex()
    {
        // ５件タグを作成する
        BlogTagFactory::make([])->persist();
        BlogTagFactory::make([])->persist();
        BlogTagFactory::make([])->persist();
        BlogTagFactory::make([])->persist();
        BlogTagFactory::make([])->persist();

        // クエリはトークンの以外で何も設定しない場合、全てのタグを取得する
        $this->get('/baser/api/bc-blog/blog_tags/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // タグ一覧は全て５件が返す
        $this->assertCount(5, $result->blogTags);

        // クエリを設定し(limit = 4)、該当の結果が返す
        $this->get('/baser/api/bc-blog/blog_tags/index.json?limit=4&token=' . $this->accessToken);
        $result = json_decode((string)$this->_response->getBody());
        // タグ一覧は４件が返す
        $this->assertCount(4, $result->blogTags);
    }

    /**
     * test index - contain を指定したリクエストは拒否される（GHSA-636g-rgj6-542v）
     *
     * リクエストの contain（ORM内部構造）が素通りすると contain[...][fields] 経由で
     * 任意 SQL が SELECT 句へ注入される。ガードが正しく機能し 403 になることを確認する。
     */
    public function testIndex_rejectsContain()
    {
        BlogTagFactory::make([])->persist();
        // contain[BlogPosts][fields] に SQL を仕込む攻撃パターン
        $this->get('/baser/api/bc-blog/blog_tags/index.json?contain[BlogPosts][fields][leak]=(SELECT+password+FROM+users+LIMIT+1)&token=' . $this->accessToken);
        $this->assertResponseCode(403);
    }

    /**
     * test view
     */
    public function testView()
    {
        // ブログタグのデータを作成する
        BlogTagFactory::make(['id' => 1, 'name' => 'tag1'])->persist();
        // 単一ブログタグー取得APIを叩く
        $this->get('/baser/api/bc-blog/blog_tags/view/1.json?token=' . $this->accessToken);
        // OKレスポンスを確認する
        $this->assertResponseOk();
        // レスポンスのデータを確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, $result->blogTag->id);
        $this->assertEquals('tag1', $result->blogTag->name);
    }

    /**
     * test view - contain を指定したリクエストは拒否される（GHSA-636g-rgj6-542v）
     */
    public function testView_rejectsContain()
    {
        BlogTagFactory::make(['id' => 1, 'name' => 'tag1'])->persist();
        $this->get('/baser/api/bc-blog/blog_tags/view/1.json?contain[BlogPosts]=1&token=' . $this->accessToken);
        $this->assertResponseCode(403);
    }


    /**
     * test index - 未認証の nested contain によるSQLインジェクション拒否
     * (GHSA-636g-rgj6-542v / GHSA-v9g2-xmwr-23vc)
     *
     * 本脆弱性の本質は「未認証到達」であるため、token を付けず Origin ヘッダのみで検証する。
     * fields sink（SELECT句注入）・conditions sink（WHERE句注入）のいずれの payload でも
     * 403 で拒否され、認証情報が漏洩しないことを確認する。
     *
     * @return void
     */
    public function test_index_reject_contain_injection_unauthenticated()
    {
        BlogTagFactory::make(['id' => 1, 'name' => 'tag1'])->persist();
        ContentFactory::make(['id' => 1, 'url' => '/news/', 'site_id' => 1, 'status' => true,
            'entity_id' => 1, 'plugin' => 'BcBlog', 'type' => 'BlogContent', 'lft' => 1, 'rght' => 2,
            'publish_begin' => '2020-01-27 12:00:00', 'publish_end' => '9000-01-27 12:00:00'])->persist();
        BlogContentFactory::make(['id' => 1])->persist();
        BlogPostFactory::make(['id' => 2, 'blog_content_id' => 1, 'no' => 2, 'user_id' => 1,
            'name' => 'p2', 'title' => 'post2', 'posted' => '2023-01-12 12:57:59', 'status' => true])->persist();
        BlogPostBlogTagFactory::make(['id' => 10, 'blog_post_id' => 2, 'blog_tag_id' => 1])->persist();

        // fields sink（GHSA-636g）: 未認証で SELECT 句へ生SQLを注入する payload
        $this->configRequest(['headers' => ['Origin' => 'http://localhost']]);
        $leak = rawurlencode("(SELECT CONCAT(email,':',password) FROM users LIMIT 1)");
        $this->get('/baser/api/bc-blog/blog_tags/index.json?siteId=1'
            . '&contain%5BBlogPosts%5D%5Bfields%5D%5B0%5D=BlogPosts.id'
            . '&contain%5BBlogPosts%5D%5Bfields%5D%5Bleak%5D=' . $leak);
        $this->assertResponseCode(403);
        $this->assertStringNotContainsString('@example.com', (string)$this->_response->getBody());

        // conditions sink（GHSA-v9g2）: 未認証で WHERE 句へ生SQLを注入する payload
        $this->configRequest(['headers' => ['Origin' => 'http://localhost']]);
        $this->get('/baser/api/bc-blog/blog_tags/index.json?siteId=1'
            . '&contain%5BBlogPosts%5D%5Bconditions%5D%5B0%5D=' . rawurlencode('IF(1=1,SLEEP(0),0)=0'));
        $this->assertResponseCode(403);
    }

    /**
     * test view - 未認証の contain によるSQLインジェクション拒否 (GHSA-636g-rgj6-542v)
     *
     * @return void
     */
    public function test_view_reject_contain_injection_unauthenticated()
    {
        BlogTagFactory::make(['id' => 1, 'name' => 'tag1'])->persist();
        $this->configRequest(['headers' => ['Origin' => 'http://localhost']]);
        $this->get('/baser/api/bc-blog/blog_tags/view/1.json'
            . '?contain%5BBlogPosts%5D%5Bfields%5D%5B0%5D=BlogPosts.id');
        $this->assertResponseCode(403);
    }

}
