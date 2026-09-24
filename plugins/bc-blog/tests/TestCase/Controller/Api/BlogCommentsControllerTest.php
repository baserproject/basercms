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

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Controller\Api\BlogCommentsController;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogCommentsScenario;
use BcBlog\Test\Scenario\BlogCommentsServiceScenario;
use BcBlog\Test\Scenario\BlogContentScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BlogCommentsControllerTest
 * @property BlogCommentsController $BlogCommentsController
 */
class BlogCommentsControllerTest extends BcTestCase
{
    /**
     * Trait
     */
    use BcContainerTrait;

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
    public function test_index()
    {
        // コメントを作成する
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'status' => true])->persist();
        $this->loadFixtureScenario(BlogCommentsScenario::class,);


        // フロント公開APIは status=publish 固定のため、承認済み（status=true）の2件のみ返す
        $this->get('/baser/api/bc-blog/blog_comments/index.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // 公開コメント2件が返す（id=3 は status=false のため除外）
        $this->assertCount(2, $result->blogComments);

        // クエリを設定し(limit = 4)、該当の結果が返す
        $this->get('/baser/api/bc-blog/blog_comments/index.json?limit=4&token=' . $this->accessToken);
        $result = json_decode((string)$this->_response->getBody());
        // 公開コメント2件が返す
        $this->assertCount(2, $result->blogComments);

        //ログインしていない状態ではステータス＝trueのコメントしか取得できない（承認待ちコメントは除外）
        PermissionFactory::make()->allowGuest('/baser/api/*')->persist();
        $this->get('/baser/api/bc-blog/blog_comments/index.json');
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        // 公開（status=true）のコメント2件のみ返す（id=3 は status=false のため除外）
        $this->assertCount(2, $result->blogComments);
        // 未認証の公開一覧では投稿者のメールアドレスを返さない
        foreach ($result->blogComments as $comment) {
            $this->assertObjectNotHasProperty('email', $comment);
        }

        //ログインしていない状態では status パラメーターへへのアクセスを禁止するか確認
        $this->get('/baser/api/bc-blog/blog_comments/index.json?status=unpublish');
        // レスポンスを確認
        $this->assertResponseCode(403);
    }

    /**
     * test view
     */
    public function test_view()
    {
        // ブログコメントのデータを作成する
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        $this->loadFixtureScenario(BlogCommentsServiceScenario::class);
        // 単一ブログコメント取得APIを叩く
        $this->get('/baser/api/bc-blog/blog_comments/view/1.json?token=' . $this->accessToken);
        // OKレスポンスを確認する
        $this->assertResponseOk();
        // レスポンスのデータを確認する
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(1, $result->blogComment->id);
        $this->assertEquals('ホームページの開設おめでとうございます。（ダミー）', $result->blogComment->message);
    }

}
