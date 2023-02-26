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
use BcBlog\Controller\Api\BlogCategoriesController;
use BcBlog\Service\BlogCategoriesService;
use BcBlog\Test\Factory\BlogCategoryFactory;
use Cake\Core\Configure;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * Class BlogCategoriesControllerTest
 * @package BcBlog\Test\TestCase\Controller\Api
 * @property BlogCategoriesController $BlogCategoriesController
 */
class BlogCategoriesControllerTest extends BcTestCase
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
        'plugin.BaserCore.Factory/Dblogs',
        'plugin.BcBlog.Factory/BlogCategories',
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
     * @return void
     */
    public function test_index()
    {
        BlogCategoryFactory::make(['blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['blog_content_id' => 2])->persist();

        $this->get('/baser/api/bc-blog/blog_categories/index/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertCount(2, $result->blogCategories);
    }

    /**
     * test view
     * @return void
     */
    public function test_view()
    {
        BlogCategoryFactory::make(['id' => 1, 'name' => 'Blog Category 1'])->persist();
        BlogCategoryFactory::make(['id' => 2, 'name' => 'Blog Category 2'])->persist();

        $this->get('/baser/api/bc-blog/blog_categories/view/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('Blog Category 1', $result->blogCategory->name);
        $this->assertEquals(1, $result->blogCategory->id);
    }

    /**
     * test list
     * @return void
     */
    public function test_list()
    {
        BlogCategoryFactory::make(['id' => 3, 'title' => 'title 3', 'name' => 'name-3', 'blog_content_id' => 1])->persist();

        $this->get('/baser/api/bc-blog/blog_categories/list/1.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(get_object_vars($result->blogCategories)[3], 'title 3');
    }

    /**
     * test add
     * @return void
     */
    public function test_add()
    {
        $data = ['name' => 'blog-category-add', 'title' => 'test title'];
        $this->post('/baser/api/bc-blog/blog_categories/add/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('blog-category-add', $result->blogCategory->name);
        $this->assertEquals('test title', $result->blogCategory->title);
        $this->assertEquals('ブログカテゴリー「blog-category-add」を追加しました。', $result->message);

        $this->post('/baser/api/bc-blog/blog_categories/add/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals('入力されたカテゴリ名は既に登録されています。', $result->errors->name->duplicateBlogCategory);
    }

    /**
     * test edit
     * @return void
     */
    public function test_edit()
    {
        BlogCategoryFactory::make(['id' => 10, 'name' => 'Blog-Category-1', 'blog_content_id' => 1, 'title' => 'test title'])->persist();

        $blogCategoriesService = new BlogCategoriesService();
        $data = $blogCategoriesService->get(10);
        $data->name = 'blog-category-edit';
        $this->post('/baser/api/bc-blog/blog_categories/edit/10.json?token=' . $this->accessToken, $data->toArray());
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('blog-category-edit', $result->blogCategory->name);
        $this->assertEquals('ブログカテゴリー「blog-category-edit」を更新しました。', $result->message);

        $data->name = 'blog Category edit';
        $this->post('/baser/api/bc-blog/blog_categories/edit/10.json?token=' . $this->accessToken, $data->toArray());
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals(
            'カテゴリ名はは半角英数字とハイフン、アンダースコアのみが利用可能です。',
            $result->errors->name->alphaNumericDashUnderscore);
    }

    /**
     * test batch
     * @return void
     */
    public function test_batch()
    {
        //成功場合、
        BlogCategoryFactory::make(
            ['id' => 21, 'name' => 'blog-category-delete', 'blog_content_id' => 21, 'title' => 'test title delete', 'lft' => 1, 'rght' => 2]
        )->persist();
        BlogCategoryFactory::make(
            ['id' => 22, 'name' => 'blog-category-delete', 'blog_content_id' => 21, 'title' => 'test title delete', 'lft' => 1, 'rght' => 2]
        )->persist();

        $this->post('/baser/api/bc-blog/blog_categories/batch.json?token=' . $this->accessToken, [
            'batch' => 'delete',
            'batch_targets' => [21, 22]
        ]);
        $this->assertResponseOk();

        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('一括処理が完了しました。', $result->message);

        $blogCategoriesService = new BlogCategoriesService();
        $data = $blogCategoriesService->getIndex(21, [])->count();
        $this->assertEquals(0, $data);

        //失敗場合、
        $this->post('/baser/api/bc-blog/blog_categories/batch.json?token=' . $this->accessToken, [
            'batch' => 'new',
            'batch_targets' => [1, 2]
        ]);
        $this->assertResponseCode(500);
    }

    /**
     * test delete
     * @return void
     */
    public function test_delete()
    {
        BlogCategoryFactory::make(
            ['id' => 11, 'name' => 'blog-category-delete', 'blog_content_id' => 1, 'title' => 'test title delete', 'lft' => 1, 'rght' => 2]
        )->persist();
        $this->post('/baser/api/bc-blog/blog_categories/delete/11.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('blog-category-delete', $result->blogCategory->name);
        $this->assertEquals('ブログカテゴリー「blog-category-delete」を削除しました。', $result->message);

        $this->post('/baser/api/bc-blog/blog_categories/delete/11.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);
    }
}
