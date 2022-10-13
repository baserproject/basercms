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
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [

    ];

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
        $this->setFixtureTruncate();
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test getIndex
     */
    public function testGetIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test getTreeIndex
     */
    public function testGetTreeIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、動作の確認が必要です。');
        $result = $this->BlogCategory->getControlSource($field, $options);
        $this->assertEquals($expected, $result, 'コントロールソースを正しく取得できません');
    }

    public function getControlSourceDataProvider()
    {
        return [
            ['parent_id', ['blogContentId' => 1], [
                1 => 'プレスリリース',
                2 => '　　　└子カテゴリ',
                3 => '親子関係なしカテゴリ'
            ]],
            ['parent_id', ['blogContentId' => 0], []],
            ['parent_id', ['blogContentId' => 1, 'excludeParentId' => true], [3 => '親子関係なしカテゴリ']],
            ['parent_id', ['blogContentId' => 1, 'ownerId' => 2], []],
            ['parent_id', ['blogContentId' => 1, 'ownerId' => 1], [
                1 => 'プレスリリース',
                2 => '　　　└子カテゴリ',
                3 => '親子関係なしカテゴリ'
            ]],
            ['owner_id', [], [
                1 => 'システム管理',
                2 => 'サイト運営'
            ]],
        ];
    }

    /**
     * Test getNew
     */
    public function testGetNew()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test create
     */
    public function testCreate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test update
     */
    public function testUpdate()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test delete
     */
    public function testDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test batch
     */
    public function testBatch()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * Test getNamesById
     */
    public function testGetNamesById()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getList
     * @return void
     */
    public function test_getList()
    {
        BlogCategoryFactory::make(['id' => 100, 'title' => 'title 100', 'name' => 'name-100', 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 101, 'title' => 'title 101', 'name' => 'name-101', 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 102, 'title' => 'title 102', 'name' => 'name-102', 'blog_content_id' => 1])->persist();
        BlogCategoryFactory::make(['id' => 103, 'title' => 'title 103', 'name' => 'name-103', 'blog_content_id' => 2])->persist();

        $rs = $this->BlogCategories->getList(1);
        $this->assertEquals($rs[100], 'title 100');
        $this->assertEquals($rs[101], 'title 101');
        $this->assertEquals($rs[102], 'title 102');
    }
}
