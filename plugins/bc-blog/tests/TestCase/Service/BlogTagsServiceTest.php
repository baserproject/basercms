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
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
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
        $data = [
            'name' => 'test create'
        ];
        $result = $this->BlogTagsService->create($data);
        $this->assertEquals(1, $result->id);
        $data = [];
        $this->expectException("Cake\ORM\Exception\PersistenceFailedException");
        $this->BlogTagsService->create($data);
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
        $this->assertStringContainsString('Content.url =', $whereSql);
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
        $this->assertStringContainsString('Content.url =', $sql);

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
}
