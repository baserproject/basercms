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
        'plugin.BcBlog.Factory/BlogTags',
        'plugin.BaserCore.Factory/Dblogs',
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

}
