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

use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Controller\Api\BlogPostsController;
use BcBlog\Test\Factory\BlogPostFactory;
use Cake\Core\Configure;
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
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BcBlog.Factory/BlogPosts',
    ];

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
        $this->setFixtureTruncate();
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
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test index
     */
    public function test_index()
    {
        //データを生成
        BlogPostFactory::make(['blog_content_id' => 1])->persist();
        BlogPostFactory::make(['blog_content_id' => 2])->persist();

        //APIを呼ぶ
        $this->get('/baser/api/bc-blog/blog_posts/index/1.json?token=' . $this->accessToken);
        //responseを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->blogPosts);
    }

    /**
     * test view
     */
    public function test_view()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test add
     */
    public function test_add()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test edit
     */
    public function test_edit()
    {
        //データを生成
        BlogPostFactory::make(['id' => 1])->persist();

        //正常の時を確認
        //編集データーを生成
        $data = ['title' => 'blog post edit'];
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/edit/1.json?token=' . $this->accessToken, $data);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('blog post edit', $result->blogPost->title);
        $this->assertEquals('記事「blog post edit」を更新しました。', $result->message);

        //dataは空にする場合を確認
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/edit/1.json?token=' . $this->accessToken, []);
        //ステータスを確認
        $this->assertResponseCode(400);
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('タイトルを入力してください。', $result->errors->title->_required);
    }

    /**
     * test edit
     */
    public function test_copy()
    {
        //データを生成
        BlogPostFactory::make(['id' => 1, 'title' => 'test'])->persist();

        //正常の時を確認
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/copy/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('ブログ記事「test」をコピーしました。', $result->message);
        $this->assertEquals('test_copy', $result->blogPost->title);

        //存在しないBlogPostIDをコビー場合、
        $this->post('/baser/api/bc-blog/blog_posts/copy/100000.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
    }

    /**
     * test publish
     */
    public function test_publish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test unpublish
     */
    public function test_unpublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test batch
     */
    public function test_batch()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        //データを生成
        SiteConfigFactory::make(['name' => 'content_types', 'value' => ''])->persist();
        BlogPostFactory::make(['id' => 1])->persist();

        //正常の時を確認
        //APIをコル
        $this->post('/baser/api/bc-blog/blog_posts/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseOk();
        //戻る値を確認
        $result = json_decode((string)$this->_response->getBody());
        $this->assertMatchesRegularExpression('/ブログ記事「.+」を削除しました。/', $result->message);
        $this->assertNotNull($result->blogPost->title);

        //存在しないBlogPostIDを削除場合、
        $this->post('/baser/api/bc-blog/blog_posts/delete/1.json?token=' . $this->accessToken);
        //ステータスを確認
        $this->assertResponseCode(404);
    }
}
