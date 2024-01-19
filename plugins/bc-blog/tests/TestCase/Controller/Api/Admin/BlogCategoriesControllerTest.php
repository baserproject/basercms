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

namespace BcBlog\Test\TestCase\Controller\Api\Admin;

use BaserCore\Test\Factory\PermissionFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Controller\Api\BlogCategoriesController;
use BcBlog\Service\BlogCategoriesService;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BlogCategoriesControllerTest
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
        //準備
        PermissionFactory::make()->allowGuest('/baser/api/admin/*')->persist();
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        BlogCategoryFactory::make(['id' => 100, 'title' => 'title test', 'name' => 'name-test', 'blog_content_id' => 1])->persist();
        BlogPostFactory::make(['id' => 100, 'blog_content_id' => 1, 'blog_category_id' => 100, 'status' => true])->persist();

        //正常系実行
        $this->get('/baser/api/admin/bc-blog/blog_categories/index.json?blog_content_id=1&token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('title test', $result->blogCategories[0]->title);

        //異常系実行：blog_content_id指定しない
        $this->get('/baser/api/admin/bc-blog/blog_categories/index.json?blog_content_id=&token=' . $this->accessToken);
        $this->assertResponseCode(500);
    }

    /**
     * test list
     * @return void
     */
    public function test_list()
    {
        PermissionFactory::make()->allowGuest('/baser/api/admin/*')->persist();
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        BlogPostFactory::make(['id' => 1, 'blog_content_id' => 1, 'blog_category_id' => 1, 'status' => true])->persist();
        BlogCategoryFactory::make(['id' => 3, 'title' => 'title 3', 'name' => 'name-3', 'blog_content_id' => 1])->persist();

        $this->get('/baser/api/admin/bc-blog/blog_categories/list.json?blog_content_id=1&token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals(get_object_vars($result->blogCategories)[3], 'title 3');
    }

    /**
     * test view
     */
    public function test_view()
    {
        //準備
        PermissionFactory::make()->allowGuest('/baser/api/admin/*')->persist();
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        BlogCategoryFactory::make(['id' => 99, 'title' => 'title 99', 'name' => 'name-99', 'blog_content_id' => 1])->persist();
        //正常系実行
        $this->get('/baser/api/admin/bc-blog/blog_categories/view/99.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('title 99', $result->blogCategory->title);
        //異常系実行
        $this->get('/baser/api/admin/bc-blog/blog_categories/view/111.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);


    }

    /**
     * test add
     * @return void
     */
    public function test_add()
    {
        $data = ['blog_content_id' => 1, 'name' => 'blog-category-add', 'title' => 'test title'];
        $this->post('/baser/api/admin/bc-blog/blog_categories/add/1.json?token=' . $this->accessToken, $data);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('blog-category-add', $result->blogCategory->name);
        $this->assertEquals('test title', $result->blogCategory->title);
        $this->assertEquals('ブログカテゴリー「blog-category-add」を追加しました。', $result->message);

        $this->post('/baser/api/admin/bc-blog/blog_categories/add.json?token=' . $this->accessToken, $data);
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
        $this->post('/baser/api/admin/bc-blog/blog_categories/edit/10.json?token=' . $this->accessToken, $data->toArray());
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('blog-category-edit', $result->blogCategory->name);
        $this->assertEquals('ブログカテゴリー「blog-category-edit」を更新しました。', $result->message);

        $data->name = 'blog Category edit';
        $this->post('/baser/api/admin/bc-blog/blog_categories/edit/10.json?token=' . $this->accessToken, $data->toArray());
        $this->assertResponseCode(400);
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('入力エラーです。内容を修正してください。', $result->message);
        $this->assertEquals(
            'カテゴリ名は半角英数字とハイフン、アンダースコアのみが利用可能です。',
            $result->errors->name->alphaNumericPlus);
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

        $this->post('/baser/api/admin/bc-blog/blog_categories/batch.json?token=' . $this->accessToken, [
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
        $this->post('/baser/api/admin/bc-blog/blog_categories/batch.json?token=' . $this->accessToken, [
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
        $this->post('/baser/api/admin/bc-blog/blog_categories/delete/11.json?token=' . $this->accessToken);
        $this->assertResponseOk();
        $result = json_decode((string)$this->_response->getBody());
        $this->assertEquals('blog-category-delete', $result->blogCategory->name);
        $this->assertEquals('ブログカテゴリー「blog-category-delete」を削除しました。', $result->message);

        $this->post('/baser/api/admin/bc-blog/blog_categories/delete/11.json?token=' . $this->accessToken);
        $this->assertResponseCode(404);
    }

}
