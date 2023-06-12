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

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentsScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Model\Table\BlogTagsTable;
use BcBlog\Service\BlogTagsService;
use BcBlog\Test\Scenario\BlogTagsScenario;
use Cake\Database\ValueBinder;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BlogTagsServiceTest
 * @property BlogTagsService $BlogTagsService
 * @property BlogTagsTable $BlogTags
 */
class BlogTagsServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BcBlog.Factory/BlogTags',
        'plugin.BcBlog.Factory/BlogPosts',
        'plugin.BcBlog.Factory/BlogComments',
        'plugin.BcBlog.Factory/BlogContents',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogTagsService = new BlogTagsService();
        $this->BlogTags = TableRegistry::getTableLocator()->get("BcBlog.BlogTags");
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BlogTagsService);
        parent::tearDown();
    }

    /**
     * test create
     */
    public function testCreate()
    {
        // 準備
        $data = [
            'name' => 'Nghiem'
        ];
        // 正常系実行
        $result = $this->BlogTagsService->create($data);
        $this->assertEquals('Nghiem', $result->name);
        $data = [];
        // データがないと失敗するのを確認する
        $this->expectException("Cake\ORM\Exception\PersistenceFailedException");
        $this->BlogTagsService->create($data);
    }
    /**
     * test update
     */
    public function testUpdate()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $blogTag = $this->BlogTagsService->get(1);
        $this->assertEquals('tag1', $blogTag->name);
        $data = [
            'name' => 'Nghiem'
        ];
        $result = $this->BlogTagsService->update($blogTag, $data);
        $this->assertEquals('Nghiem', $result->name);
        $data = [
            'name' => ''
        ];
        $this->expectException("Cake\ORM\Exception\PersistenceFailedException");
        $this->BlogTagsService->update($blogTag, $data);
    }

    /**
     * test createIndexOrder
     */
    public function testCreateIndexOrder()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $params = [
            'conditions' => [],
            'direction' => 'ASC',
            'sort' => 'name',
            'contentId' => 1,
            'contentUrl' => 'test',
            'siteId' => 1,
            'name' => 'tag',
            'contain' => ['BlogPosts' => ['BlogContents' => ['Contents']]]
        ];
        $query = $this->BlogTags->find();
        $result = $this->BlogTagsService->createIndexOrder($query, $params);
        $sortSql = $result->clause('order')->sql(new ValueBinder());
        $this->assertStringContainsString('BlogTags.name ASC', $sortSql);
    }

    /**
     * test batch
     */
    public function test_batch()
    {
        // データを生成
        $this->loadFixtureScenario(BlogTagsScenario::class);

        //// 正常系のテスト

        // サービスメソッドを呼ぶ
        $result = $this->BlogTagsService->batch('delete', [1, 2, 3]);
        // 戻り値を確認
        $this->assertTrue($result);
        // データが削除されていることを確認
        $blogTags = $this->BlogTagsService->getIndex([])->toArray();
        $this->assertCount(0, $blogTags);

        //// 異常系のテスト

        // delete で id が指定されていない場合は true を返すこと
        // サービスメソッドを呼ぶ
        $result = $this->BlogTagsService->batch('delete', []);
        // 戻り値を確認
        $this->assertTrue($result);

        // 存在しない id を指定された場合は例外が発生すること
        // サービスメソッドを呼ぶ
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->BlogTagsService->batch('delete', [1, 2, 3]);
    }

    /**
     * test getNew
     */
    public function testGetNew()
    {
        $result = $this->BlogTagsService->getNew();
        $this->assertInstanceOf("Cake\Datasource\EntityInterface", $result);
    }

    /**
     * test getIndex
     */
    public function testGetIndex()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $params = [
            'conditions' => [],
            'direction' => 'ASC',
            'sort' => 'name',
            'contentId' => 1,
            'contentUrl' => 'test',
            'siteId' => 1,
            'name' => 'tag',
            'contain' => ['BlogPosts' => ['BlogContents' => ['Contents']]]
        ];
        $result = $this->BlogTagsService->getIndex($params);
        $whereSql = $result->clause('where')->sql(new ValueBinder());
        $this->assertStringContainsString('BlogTags.name like', $whereSql);
        $this->assertStringContainsString('Contents.site_id =', $whereSql);
        $this->assertStringContainsString('Contents.url =', $whereSql);
        $sortSql = $result->clause('order')->sql(new ValueBinder());
        $this->assertStringContainsString('BlogTags.name ASC', $sortSql);
        $params['direction'] = 'DESC';
        $params['sort'] = 'id';
        $result = $this->BlogTagsService->getIndex($params);
        $sortSql = $result->clause('order')->sql(new ValueBinder());
        $this->assertStringContainsString('BlogTags.id DESC', $sortSql);

    }

    /**
     * test createIndexConditions
     */
    public function testCreateIndexConditions()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $params = [
            'conditions' => [],
            'contentId' => 1,
            'contentUrl' => 'test',
            'siteId' => 1,
            'name' => 'tag',
            'contain' => ['BlogPosts' => ['BlogContents' => ['Contents']]]
        ];
        $query = $this->BlogTags->find();
        $result = $this->BlogTagsService->createIndexConditions($query, $params);
        $sql = $result->clause('where')->sql(new ValueBinder());
        $this->assertStringContainsString('BlogTags.name like', $sql);
        $this->assertStringContainsString('Contents.site_id =', $sql);
        $this->assertStringContainsString('Contents.url =', $sql);

    }

    /**
     * test get
     */
    public function testGet()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $blogTag = $this->BlogTagsService->get(1);
        $this->assertEquals(1, $blogTag->id);
        $this->expectException(RecordNotFoundException::class);
        $this->BlogTagsService->get(11);
    }

    /**
     * test delete
     */
    public function testDelete()
    {
        $this->loadFixtureScenario(BlogTagsScenario::class);
        $blogTag = $this->BlogTagsService->get(1);
        $this->assertEquals(1, $blogTag->id);
        $this->assertTrue($this->BlogTagsService->delete(1));
        $this->expectException(RecordNotFoundException::class);
        $this->BlogTagsService->get(1);
    }

    /**
     * test getTitlesById
     */
    public function test_getTitlesById()
    {
        //データを生成
        $this->loadFixtureScenario(BlogTagsScenario::class);
        //対象メソッドをコール
        $rs = $this->BlogTagsService->getTitlesById([1, 2]);
        //戻る値を確認
        $this->assertEquals($rs[1], 'tag1');
        $this->assertEquals($rs[2], 'tag2');
    }
}
