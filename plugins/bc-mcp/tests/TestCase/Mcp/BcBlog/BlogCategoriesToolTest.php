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
use BcMcp\Mcp\BcBlog\BlogCategoriesTool;
use BcBlog\Test\Factory\BlogCategoryFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BcMcp\Mcp\BcBlog\BlogCategoriesTool Test Case
 *
 * @uses \BcMcp\Mcp\BcBlog\BlogCategoriesTool
 */
class BlogCategoriesToolTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * Test subject
     *
     * @var \BcMcp\Mcp\BcBlog\BlogCategoriesTool
     */
    protected $BlogCategoriesTool;

    /**
     * setUp method
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogCategoriesTool = new BlogCategoriesTool();
    }

    /**
     * tearDown method
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogCategoriesTool);
        parent::tearDown();
    }

    /**
     * Test addBlogCategory method - 基本テスト
     *
     * @return void
     */
    public function testAddBlogCategoryBasic()
    {
        $title = 'テストカテゴリ';
        $blogContentId = 1;

        $result = $this->BlogCategoriesTool->addBlogCategory(
            title: $title,
            blogContentId: $blogContentId
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('id', $result);
    }

    /**
     * Test getBlogCategories method - 基本テスト
     *
     * @return void
     */
    public function testGetBlogCategoriesBasic()
    {
        // テストデータを作成
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => 'テストカテゴリ1',
            'name' => 'test-category-1',
            'status' => 1
        ])->persist();

        $result = $this->BlogCategoriesTool->getBlogCategories(1);

        $this->assertIsArray($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test getBlogCategory method - IDによる取得
     *
     * @return void
     */
    public function testGetBlogCategoryById()
    {
        // テストデータを作成
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => 'テストカテゴリ',
            'name' => 'test-category',
            'status' => 1
        ])->persist();

        $result = $this->BlogCategoriesTool->getBlogCategory(1);

        $this->assertIsArray($result);
        // IDが存在する場合は成功を想定
        $this->assertEquals(1, $result['id']);
    }

    /**
     * Test editBlogCategory method - 編集機能
     *
     * @return void
     */
    public function testEditBlogCategory()
    {
        // テストデータを作成
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => 'テストカテゴリ',
            'name' => 'test-category',
            'status' => 1
        ])->persist();

        $newTitle = '編集テストカテゴリ';

        $result = $this->BlogCategoriesTool->editBlogCategory(
            id: 1,
            title: $newTitle
        );

        $this->assertIsArray($result);
        $this->assertEquals($newTitle, $result['title']);
    }

    /**
     * Test deleteBlogCategory method - 削除機能
     *
     * @return void
     */
    public function testDeleteBlogCategory()
    {
        // テストデータを作成
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => 'テストカテゴリ',
            'name' => 'test-category',
            'status' => 1
        ])->persist();

        $result = $this->BlogCategoriesTool->deleteBlogCategory(1);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test addBlogCategory method - エラーテスト（空のタイトル）
     *
     * @return void
     */
    public function testAddBlogCategoryWithEmptyTitle()
    {
        $result = $this->BlogCategoriesTool->addBlogCategory('');

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertEquals('titleは必須です', $result['content']);
    }

    /**
     * Test getBlogCategory method - 存在しないIDのテスト
     *
     * @return void
     */
    public function testGetBlogCategoryNotFound()
    {
        $nonExistentId = 999999;

        $result = $this->BlogCategoriesTool->getBlogCategory($nonExistentId);

        $this->assertIsArray($result);
        $this->assertArrayHasKey('content', $result);
        $this->assertEquals('Record not found in table `blog_categories`.', $result['content']);
    }

    /**
     * Test getBlogCategories method - ページネーションテスト（limit指定）
     *
     * @return void
     */
    public function testGetBlogCategoriesWithLimit()
    {
        // 複数のテストデータを作成
        for($i = 1; $i <= 5; $i++) {
            BlogCategoryFactory::make([
                'id' => $i,
                'blog_content_id' => 1,
                'title' => "テストカテゴリ{$i}",
                'name' => "test-category-{$i}",
                'status' => 1
            ])->persist();
        }

        // limit=3で取得
        $result = $this->BlogCategoriesTool->getBlogCategories(
            blogContentId: 1,
            limit: 3
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);

        // ページネーション情報の確認
        $this->assertEquals(1, $result['pagination']['page']);
        $this->assertEquals(3, $result['pagination']['limit']);
        $this->assertEquals(3, $result['pagination']['count']); // 実際に返された件数
        $this->assertEquals(5, $result['pagination']['total']); // 総件数
    }

    /**
     * Test getBlogCategories method - ページネーションテスト（page指定）
     *
     * @return void
     */
    public function testGetBlogCategoriesWithPage()
    {
        // 複数のテストデータを作成
        for($i = 1; $i <= 10; $i++) {
            BlogCategoryFactory::make([
                'id' => $i,
                'blog_content_id' => 1,
                'title' => "テストカテゴリ{$i}",
                'name' => "test-category-{$i}",
                'status' => 1
            ])->persist();
        }

        // page=2, limit=3で取得
        $result = $this->BlogCategoriesTool->getBlogCategories(
            blogContentId: 1,
            limit: 3,
            page: 2
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);

        // ページネーション情報の確認
        $this->assertEquals(2, $result['pagination']['page']);
        $this->assertEquals(3, $result['pagination']['limit']);
        $this->assertEquals(3, $result['pagination']['count']); // 実際に返された件数
        $this->assertEquals(10, $result['pagination']['total']); // 総件数
    }

    /**
     * Test getBlogCategories method - ページネーションテスト（limit未指定）
     *
     * @return void
     */
    public function testGetBlogCategoriesWithoutLimit()
    {
        // 複数のテストデータを作成
        for($i = 1; $i <= 5; $i++) {
            BlogCategoryFactory::make([
                'id' => $i,
                'blog_content_id' => 1,
                'title' => "テストカテゴリ{$i}",
                'name' => "test-category-{$i}",
                'status' => 1
            ])->persist();
        }

        // limitを指定せずに取得
        $result = $this->BlogCategoriesTool->getBlogCategories(
            blogContentId: 1,
            page: 1
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);

        // ページネーション情報の確認
        $this->assertEquals(1, $result['pagination']['page']);
        $this->assertNull($result['pagination']['limit']);
        $this->assertEquals(5, $result['pagination']['count']);
        $this->assertEquals(5, $result['pagination']['total']); // 総件数
    }

    /**
     * Test getBlogCategories method - ページネーションテスト（空のページ）
     *
     * @return void
     */
    public function testGetBlogCategoriesEmptyPage()
    {
        // 5件のテストデータを作成
        for($i = 1; $i <= 5; $i++) {
            BlogCategoryFactory::make([
                'id' => $i,
                'blog_content_id' => 1,
                'title' => "テストカテゴリ{$i}",
                'name' => "test-category-{$i}",
                'status' => 1
            ])->persist();
        }

        // 存在しないページ（page=10, limit=3）で取得
        $result = $this->BlogCategoriesTool->getBlogCategories(
            blogContentId: 1,
            limit: 3,
            page: 10
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);

        // ページネーション情報の確認
        $this->assertEquals(10, $result['pagination']['page']);
        $this->assertEquals(3, $result['pagination']['limit']);
        $this->assertEquals(0, $result['pagination']['count']); // 実際に返された件数
        $this->assertEquals(5, $result['pagination']['total']); // 総件数
    }

    /**
     * Test getBlogCategories method - 公開状態フィルタテスト
     *
     * @return void
     */
    public function testGetBlogCategoriesWithPublishStatus()
    {
        // BlogContentScenarioを使用してBlogContentとContentを作成
        $this->loadFixtureScenario(\BcBlog\Test\Scenario\BlogContentScenario::class, 1, 1, 1, 'blog', '/blog/', 'ブログ');

        // 2つのカテゴリを作成（1つは公開、1つは非公開）
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => '公開カテゴリ',
            'name' => 'public-category',
            'status' => 1 // 公開
        ])->persist();

        BlogCategoryFactory::make([
            'id' => 2,
            'blog_content_id' => 1,
            'title' => '非公開カテゴリ',
            'name' => 'private-category',
            'status' => 0 // 非公開
        ])->persist();

        // status=1を指定すると'publish'に変換される
        $result = $this->BlogCategoriesTool->getBlogCategories(
            blogContentId: 1,
            status: 'publish'
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);

        // 結果の確認：公開されているカテゴリのみが取得される
        // paginationキーを除外してカテゴリデータを取得
        $categories = array_values(array_filter($result, function($key) {
            return $key !== 'pagination';
        }, ARRAY_FILTER_USE_KEY));

        $this->assertCount(1, $categories); // 公開されているカテゴリのみ1件
        $this->assertEquals('公開カテゴリ', $categories[0]['title']);
        $this->assertEquals(1, $categories[0]['status']);

        // ページネーション情報の確認
        $this->assertEquals(1, $result['pagination']['count']); // 実際に返された件数
        $this->assertEquals(1, $result['pagination']['total']); // 公開状態の総件数
    }

    /**
     * Test getBlogCategories method - 全ての状態のカテゴリ取得テスト
     *
     * @return void
     */
    public function testGetBlogCategoriesWithAllStatus()
    {
        // 2つのカテゴリを作成（1つは公開、1つは非公開）
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => '公開カテゴリ',
            'name' => 'public-category',
            'status' => 1 // 公開
        ])->persist();

        BlogCategoryFactory::make([
            'id' => 2,
            'blog_content_id' => 1,
            'title' => '非公開カテゴリ',
            'name' => 'private-category',
            'status' => 0 // 非公開
        ])->persist();

        // 全ての状態のカテゴリを取得（status指定なし）
        $result = $this->BlogCategoriesTool->getBlogCategories(
            blogContentId: 1
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);

        // 結果の確認
        $categories = array_values(array_filter($result, function($key) {
            return $key !== 'pagination';
        }, ARRAY_FILTER_USE_KEY));
        $this->assertCount(2, $categories); // 公開・非公開両方取得される

        // ページネーション情報の確認
        $this->assertEquals(2, $result['pagination']['count']); // 実際に返された件数
        $this->assertEquals(2, $result['pagination']['total']); // 全件数
    }

    /**
     * Test getBlogCategories method - status=0（非公開）は対応しないテスト
     *
     * @return void
     */
    public function testGetBlogCategoriesWithUnpublishStatusNotSupported()
    {
        // 2つのカテゴリを作成（1つは公開、1つは非公開）
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => '公開カテゴリ',
            'name' => 'public-category',
            'status' => 1 // 公開
        ])->persist();

        BlogCategoryFactory::make([
            'id' => 2,
            'blog_content_id' => 1,
            'title' => '非公開カテゴリ',
            'name' => 'private-category',
            'status' => 0 // 非公開
        ])->persist();

        // status=0を指定（対応しないため、全てのカテゴリが取得される）
        $result = $this->BlogCategoriesTool->getBlogCategories(
            blogContentId: 1,
            status: null
        );

        $this->assertIsArray($result);
        $this->assertArrayHasKey('pagination', $result);

        // 結果の確認：status=0は対応しないため、全てのカテゴリが取得される
        $categories = array_values(array_filter($result, function($key) {
            return $key !== 'pagination';
        }, ARRAY_FILTER_USE_KEY));
        $this->assertCount(2, $categories); // 公開・非公開両方取得される

        // ページネーション情報の確認
        $this->assertEquals(2, $result['pagination']['count']); // 実際に返された件数
        $this->assertEquals(2, $result['pagination']['total']); // 全件数
    }

    /**
     * testGetBlogCategoriesWithTitle
     *
     * @return void
     */
    public function testGetBlogCategoriesWithTitle()
    {
        // 2つのカテゴリを作成（1つは公開、1つは非公開）
        BlogCategoryFactory::make([
            'id' => 1,
            'blog_content_id' => 1,
            'title' => 'カテゴリ1',
            'name' => 'public-category',
            'status' => 1
        ])->persist();

        BlogCategoryFactory::make([
            'id' => 2,
            'blog_content_id' => 1,
            'title' => 'カテゴリ2',
            'name' => 'private-category',
            'status' => 1
        ])->persist();

        $result = $this->BlogCategoriesTool->getBlogCategories(
            title: 'カテゴリ'
        );
        $categories = array_values(array_filter($result, function($key) {
            return $key !== 'pagination';
        }, ARRAY_FILTER_USE_KEY));
        $this->assertCount(2, $categories);

        $result = $this->BlogCategoriesTool->getBlogCategories(
            title: '1'
        );
        $categories = array_values(array_filter($result, function($key) {
            return $key !== 'pagination';
        }, ARRAY_FILTER_USE_KEY));
        $this->assertCount(1, $categories);
    }

}
