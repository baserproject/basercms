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
        $this->assertNull($entity['parent_id']);

        // 親カテゴリを指定した場合（子カテゴリを追加）
        $entity = $this->BlogCategories->getNew(1, 2);
        $this->assertEquals(1, $entity['blog_content_id']);
        $this->assertEquals(2, $entity['parent_id']);
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

    /**
     * test move（並び替え・再親付け）
     */
    public function test_move()
    {
        // parent1(1,4) > child1(2,3)、parent2(5,6) の入れ子集合
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'parent1', 'title' => 'P1', 'parent_id' => null, 'lft' => 1, 'rght' => 4])->persist();
        BlogCategoryFactory::make(['id' => 2, 'blog_content_id' => 1, 'name' => 'child1', 'title' => 'C1', 'parent_id' => 1, 'lft' => 2, 'rght' => 3])->persist();
        BlogCategoryFactory::make(['id' => 3, 'blog_content_id' => 1, 'name' => 'parent2', 'title' => 'P2', 'parent_id' => null, 'lft' => 5, 'rght' => 6])->persist();

        // 同一階層の並び替え：parent1 を parent2 の後ろへ（target.id なし＝末尾）
        $this->BlogCategories->move(
            ['id' => 1, 'parentId' => null],
            ['id' => null, 'parentId' => null]
        );
        $names = $this->BlogCategories->BlogCategories->find()->orderBy(['lft'])->all()->extract('name')->toArray();
        $this->assertEquals(['parent2', 'parent1', 'child1'], $names);

        // 再親付け：child1 を parent2 の子へ移動
        $this->BlogCategories->move(
            ['id' => 2, 'parentId' => 1],
            ['id' => null, 'parentId' => 3]
        );
        $child1 = $this->BlogCategories->BlogCategories->get(2);
        $this->assertEquals(3, $child1->parent_id);
        $parent2 = $this->BlogCategories->BlogCategories->get(3);
        $this->assertGreaterThan($parent2->lft, $child1->lft);
        $this->assertLessThan($parent2->rght, $child1->rght);

        // 移動元の親はリクエストの origin.parentId ではなく DB 上の値から判定されること
        // parent1 も parent2 の子にして [child1, parent1] とする
        $this->BlogCategories->move(
            ['id' => 1, 'parentId' => null],
            ['id' => null, 'parentId' => 3]
        );
        // child1 を同階層の末尾へ移動。origin.parentId には移動前の古い親（1）を渡す
        $this->BlogCategories->move(
            ['id' => 2, 'parentId' => 1],
            ['id' => null, 'parentId' => 3]
        );
        $children = $this->BlogCategories->BlogCategories->find()
            ->where(['parent_id' => 3])->orderBy(['lft'])->all()->extract('name')->toArray();
        $this->assertEquals(['parent1', 'child1'], $children);
    }

    /**
     * test move 複数ブログでルートカテゴリが交錯している場合（回帰テスト）
     *
     * lft/rght は全ブログ横断の単一フォレストのため、他ブログのルートカテゴリが
     * 間に挟まっていても 1 回の移動で意図した位置に反映されることを確認する
     */
    public function test_move_multiBlog()
    {
        // ブログ1・ブログ2 へ交互に追加された状態（lft 順で交錯）
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'category-a', 'title' => 'A', 'parent_id' => null, 'lft' => 1, 'rght' => 2])->persist();
        BlogCategoryFactory::make(['id' => 2, 'blog_content_id' => 2, 'name' => 'category-b', 'title' => 'B', 'parent_id' => null, 'lft' => 3, 'rght' => 4])->persist();
        BlogCategoryFactory::make(['id' => 3, 'blog_content_id' => 1, 'name' => 'category-c', 'title' => 'C', 'parent_id' => null, 'lft' => 5, 'rght' => 6])->persist();

        // 上方向：C を A の上へ（1 回の移動で反映されること）
        $this->BlogCategories->move(
            ['id' => 3, 'parentId' => null],
            ['id' => 1, 'parentId' => null]
        );
        $blog1Names = $this->BlogCategories->BlogCategories->find()
            ->where(['blog_content_id' => 1])->orderBy(['lft'])->all()->extract('name')->toArray();
        $this->assertEquals(['category-c', 'category-a'], $blog1Names);

        // 下方向：C を末尾へ（target.id なし）
        $this->BlogCategories->move(
            ['id' => 3, 'parentId' => null],
            ['id' => null, 'parentId' => null]
        );
        $blog1Names = $this->BlogCategories->BlogCategories->find()
            ->where(['blog_content_id' => 1])->orderBy(['lft'])->all()->extract('name')->toArray();
        $this->assertEquals(['category-a', 'category-c'], $blog1Names);

        // ブログ2 の並びに影響が無いこと
        $blog2Names = $this->BlogCategories->BlogCategories->find()
            ->where(['blog_content_id' => 2])->orderBy(['lft'])->all()->extract('name')->toArray();
        $this->assertEquals(['category-b'], $blog2Names);

        // 入れ子集合が壊れていないこと（lft < rght、全ノード重複なし）
        $all = $this->BlogCategories->BlogCategories->find()->orderBy(['lft'])->all();
        $values = [];
        foreach ($all as $category) {
            $this->assertLessThan($category->rght, $category->lft);
            $values[] = $category->lft;
            $values[] = $category->rght;
        }
        $this->assertEquals(count($values), count(array_unique($values)));
    }

    /**
     * test verityTree
     */
    public function test_verityTree()
    {
        // 正常なツリー
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'parent1', 'title' => 'P1', 'parent_id' => null, 'lft' => 1, 'rght' => 4])->persist();
        BlogCategoryFactory::make(['id' => 2, 'blog_content_id' => 1, 'name' => 'child1', 'title' => 'C1', 'parent_id' => 1, 'lft' => 2, 'rght' => 3])->persist();
        $this->assertTrue($this->BlogCategories->verityTree());

        // lft / rght が破損したツリー（重複・rght < lft）
        BlogCategoryFactory::make(['id' => 3, 'blog_content_id' => 1, 'name' => 'broken', 'title' => 'B', 'parent_id' => null, 'lft' => 2, 'rght' => 1])->persist();
        $this->assertFalse($this->BlogCategories->verityTree());
    }

    /**
     * test resetTree
     */
    public function test_resetTree()
    {
        // lft / rght が破損したツリー
        BlogCategoryFactory::make(['id' => 1, 'blog_content_id' => 1, 'name' => 'parent1', 'title' => 'P1', 'parent_id' => null, 'lft' => 1, 'rght' => 4])->persist();
        BlogCategoryFactory::make(['id' => 2, 'blog_content_id' => 1, 'name' => 'child1', 'title' => 'C1', 'parent_id' => 1, 'lft' => 2, 'rght' => 2])->persist();
        BlogCategoryFactory::make(['id' => 3, 'blog_content_id' => 2, 'name' => 'category-b', 'title' => 'B', 'parent_id' => null, 'lft' => 2, 'rght' => 1])->persist();
        $this->assertFalse($this->BlogCategories->verityTree());

        // リセット後は全てルート直下のフラット構造となり、チェックが通る
        $this->assertTrue($this->BlogCategories->resetTree());
        $this->assertTrue($this->BlogCategories->verityTree());
        $roots = $this->BlogCategories->BlogCategories->find()->where(['parent_id IS' => null])->count();
        $this->assertEquals(3, $roots);
    }
}
