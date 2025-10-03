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

use BcBlog\Service\BlogCategoriesService;
use BcBlog\Test\Factory\BlogCategoryFactory;
use BcBlog\Test\Factory\BlogPostFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BlogCategoriesServiceTest
 * @property BlogCategoriesService $BlogCategories
 */
class BlogCategoriesServiceTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * @var BlogCategoriesService|null
     */
    public $BlogCategories = null;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogCategories = new BlogCategoriesService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogCategories);
        parent::tearDown();
    }

    /**
     * Test __construct
     */
    public function test__construct()
    {
        // テーブルがセットされている事を確認
        $this->assertEquals('BlogCategories', $this->BlogCategories->BlogCategories->getAlias());
    }

    /**
     * Test get
     */
    public function testGet()
    {
        $data = [
            'id' => '59',
            'blog_content_id' => '1',
            'no' => '1',
            'name' => 'release',
            'title' => 'プレスリリース',
            'status' => '1',
            'parent_id' => null,
            'lft' => '1',
            'rght' => '4',
            'owner_id' => '1',
            'created' => '2015-01-27 12:56:53',
            'modified' => null
        ];
        BlogCategoryFactory::make($data)->persist();
        $blogCategory = $this->BlogCategories->get($data['id']);
        $this->assertEquals($data['id'], $blogCategory['id']);
        $this->assertEquals($data['name'], $blogCategory['name']);
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        BlogPostFactory::make(['id' => 1, 'blog_content_id'=> 1, 'blog_category_id'=> 1, 'status' => true])->persist();
        BlogCategoryFactory::make(['blog_content_id' => 1, 'name' => 'data1'])->persist();
        BlogCategoryFactory::make(['blog_content_id' => 2, 'name' => 'data2'])->persist();
        BlogCategoryFactory::make(['blog_content_id' => 1, 'name' => 'data3'])->persist();
        $blogCategories = $this->BlogCategories->getIndex(1, []);
        $this->assertCount(2, $blogCategories);
    }

    /**
     * Test getTreeIndex
     */
    public function testGetTreeIndex()
    {
        BlogCategoryFactory::make(['id' => 59, 'blog_content_id' => 19, 'title' => 'test'])->persist();
        $categories = $this->BlogCategories->getTreeIndex(19, []);
        $this->assertEquals('test', $categories[0]->layered_title);

        BlogCategoryFactory::make(['id' => 60, 'blog_content_id' => 29, 'title' => '_test'])->persist();
        $categories = $this->BlogCategories->getTreeIndex(29, []);
        $this->assertEquals('　└_test', $categories[0]->layered_title);
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field フィールド名
     * @param array $option オプション
     * @param array $expected 期待値
     * @dataProvider getControlSourceDataProvider
     */
    public function testGetControlSource($field, $options, $expected)
    {
        $rows = [
            ['id' => 58, 'blog_content_id' => 39, 'lft' => 1, 'rght' => 2, 'status' => 1, 'title' => 'test'],
            ['id' => 59, 'blog_content_id' => 19, 'status' => 1, 'title' => 'test', 'lft' => 3, 'rght' => 4],
            ['id' => 60, 'blog_content_id' => 19, 'status' => 1, 'parent_id' => 58, 'title' => '_test']
        ];
        foreach ($rows as $row) {
            BlogCategoryFactory::make($row)->persist();
        }
        $result = $this->BlogCategories->getControlSource($field, $options);
        $this->assertEquals($expected, $result, 'コントロールソースを正しく取得できません');
    }

    public static function getControlSourceDataProvider(): array
    {
        return [
            ['parent_id', [], false],
            [
                'parent_id',
                ['conditions' => ['BlogCategories.status' => 1], 'blogContentId' => 19],
                [59 => 'test', 60 => '　└test']
            ],
            ['parent_id',['blogContentId' => 19], [59 => 'test', 60 => '　└test']],
            ['parent_id', ['blogContentId' => 19, 'excludeParentId' => 59], [60 => '　└test']],
        ];
    }

    /**
     * Test getNew
     */
    public function testGetNew()
    {
        $entity = $this->BlogCategories->getNew(1);
        $this->assertEquals(1, $entity['blog_content_id']);
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        BlogCategoryFactory::make(['blog_content_id' => 19, 'no' => 9])->persist();
        $result = $this->BlogCategories->create(19, ['id' => 59, 'name' => 'testName', 'title' => 'testTitle']);
        $this->assertEquals(10, $result['no']);
        $this->assertEquals('testName', $result['name']);
        $createdBlogCategories = $this->BlogCategories->BlogCategories->find()->where(['blog_content_id' => 19, 'no'=> 10])->toArray();
        $this->assertCount(1, $createdBlogCategories);
        $this->assertEquals('testName', $createdBlogCategories[0]['name']);
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        BlogCategoryFactory::make(['id' => 59, 'name' => 'testName'])->persist();
        $updateData = ['name' => 'testNameUpdated', 'blog_content_id' => 1];
        $blogCategory = BlogCategoryFactory::get(59);
        $result = $this->BlogCategories->update($blogCategory, $updateData);
        // 戻り値を確認
        $this->assertEquals($updateData['name'], $result['name']);
        $this->assertEquals($updateData['blog_content_id'], $result['blog_content_id']);
        // データの変更を確認
        $blogCategory = BlogCategoryFactory::get(59);
        $this->assertEquals($updateData['name'], $blogCategory['name']);
        $this->assertEquals($updateData['blog_content_id'], $blogCategory['blog_content_id']);
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        BlogCategoryFactory::make([
            'id' => 59,
            'name' => 'testName',
            'blog_content_id' => 1,
            'title' => 'testTitle',
            'lft' => 1,
            'rght' => 2
        ])->persist();
        $result = $this->BlogCategories->delete(59);
        // 戻り値を確認
        $this->assertTrue($result);
        // データの削除を確認
        $blogCategories = $this->BlogCategories->BlogCategories->find()->where(['id' => 59])->toArray();
        $this->assertCount(0, $blogCategories);
    }

    /**
     * Test batch
     */
    public function testBatch()
    {
        BlogCategoryFactory::make([
            'id' => 59,
            'name' => 'testName1',
            'blog_content_id' => 19,
            'title' => 'testTitle1',
            'lft' => 1,
            'rght' => 2
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 60,
            'name' => 'testName2',
            'blog_content_id' => 19,
            'title' => 'testTitle2',
            'lft' => 3,
            'rght' => 4
        ])->persist();
        $result = $this->BlogCategories->batch('delete', [59, 60]);
        // 戻り値を確認
        $this->assertTrue($result);
        // データの削除を確認（複数）
        $blogCategories = $this->BlogCategories->BlogCategories->find()->where(['blog_content_id' => 19])->toArray();
        $this->assertCount(0, $blogCategories);
    }

    /**
     * Test getNamesById
     */
    public function testGetNamesById()
    {
        BlogCategoryFactory::make([
            'id' => 59,
            'name' => 'testName1',
            'blog_content_id' => 19,
            'title' => 'testTitle1',
            'lft' => 1,
            'rght' => 2
        ])->persist();
        BlogCategoryFactory::make([
            'id' => 60,
            'name' => 'testName2',
            'blog_content_id' => 19,
            'title' => 'testTitle2',
            'lft' => 3,
            'rght' => 4
        ])->persist();
        $result = $this->BlogCategories->getNamesById([59, 60]);
        $this->assertEquals([59 => 'testTitle1', 60 => 'testTitle2'], $result);
    }

    /**
     * test getList
     * @return void
     */
    public function test_getList()
    {
        $this->loadFixtureScenario(
            BlogContentScenario::class,
            1,  // id
            1, // siteId
            null, // parentId
            'news1', // name
            '/news/' // url
        );
        BlogPostFactory::make(['id' => 1, 'blog_content_id'=> 1, 'blog_category_id'=> 1, 'status' => true])->persist();
        BlogCategoryFactory::make(['id' => 100, 'title' => 'title 100', 'name' => 'name-100', 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 101, 'title' => 'title 101', 'name' => 'name-101', 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 102, 'title' => 'title 102', 'name' => 'name-102', 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 103, 'title' => 'title 103', 'name' => 'name-103', 'blog_content_id' => 2])->persist();

        $rs = $this->BlogCategories->getList(1);
        $this->assertEquals($rs[100], 'title 100');
        $this->assertEquals($rs[101], 'title 101');
        $this->assertEquals($rs[102], 'title 102');
    }

    /**
     * createIndexConditionsのテスト
     */
    public function test_createIndexConditions()
    {
        $table = $this->BlogCategories->BlogCategories;
        $query = $table->find();
        $blogContentId = 1;
        $params = [
            'name' => 'cat',
            'title' => 'タイトル',
            'status' => 'publish'
        ];
        $resultQuery = $this->execPrivateMethod($this->BlogCategories, 'createIndexConditions', [$query, $blogContentId, $params]);
        $sql = $resultQuery->sql();
        $this->assertStringContainsString('BlogCategories.name LIKE', $sql);
        $this->assertStringContainsString('BlogCategories.title LIKE', $sql);
        $this->assertStringContainsString('BlogCategories.blog_content_id', $sql);
        $this->assertStringContainsString('BlogCategories.status', $sql);
    }

    /**
     * getIndexのページネーションテスト
     */
    public function test_getIndexWithPagination()
    {
        // テストデータの準備
        $this->loadFixtureScenario(BlogContentScenario::class);

        for ($i = 1; $i <= 10; $i++) {
            BlogCategoryFactory::make([
                'id' => $i,
                'blog_content_id' => 1,
                'title' => "テストカテゴリ{$i}",
                'name' => "test-category-{$i}",
                'status' => 1
            ])->persist();
        }

        // limit=3でテスト
        $query = $this->BlogCategories->getIndex(1, ['limit' => 3]);
        $results = $query->toArray();
        $this->assertCount(3, $results);

        // page=2, limit=3でテスト
        $query = $this->BlogCategories->getIndex(1, ['limit' => 3, 'page' => 2]);
        $results = $query->toArray();
        $this->assertCount(3, $results);

        // page=4, limit=3でテスト（空の結果）
        $query = $this->BlogCategories->getIndex(1, ['limit' => 3, 'page' => 4]);
        $results = $query->toArray();
        $this->assertCount(1, $results); // 10件のうち最後の1件

        // page=5, limit=3でテスト（空の結果）
        $query = $this->BlogCategories->getIndex(1, ['limit' => 3, 'page' => 5]);
        $results = $query->toArray();
        $this->assertCount(0, $results);
    }

    /**
     * createIndexConditionsのページネーションテスト
     */
    public function test_createIndexConditionsWithPagination()
    {
        $table = $this->BlogCategories->BlogCategories;
        $query = $table->find();
        $blogContentId = 1;

        // limitパラメータのテスト
        $params = ['limit' => 5];
        $resultQuery = $this->execPrivateMethod($this->BlogCategories, 'createIndexConditions', [$query, $blogContentId, $params]);
        $sql = $resultQuery->sql();
        $this->assertStringContainsString('LIMIT 5', $sql);

        // limit + pageパラメータのテスト
        $query = $table->find();
        $params = ['limit' => 3, 'page' => 2];
        $resultQuery = $this->execPrivateMethod($this->BlogCategories, 'createIndexConditions', [$query, $blogContentId, $params]);
        $sql = $resultQuery->sql();
        $this->assertStringContainsString('LIMIT 3', $sql);
        $this->assertStringContainsString('OFFSET 3', $sql); // (page-1) * limit = (2-1) * 3 = 3
    }
}
