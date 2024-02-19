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

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentsScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\Service\BlogContentsService;
use BcBlog\Test\Factory\BlogContentFactory;
use BcBlog\Test\Scenario\BlogContentScenario;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Entity;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BlogContentsServiceTest
 * @property BlogContentsService $BlogContentsService
 */
class BlogContentsServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use IntegrationTestTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BlogContentsService = new BlogContentsService();
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
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->BlogContentsService->BlogContents));
    }

    /**
     * test get
     */
    public function test_get()
    {
        BlogContentFactory::make(['id' => 60, 'description' => 'test get'])->persist();
        ContentFactory::make(['id' => 60, 'type' => 'BlogContent', 'entity_id' => 60, 'title' => 'title test get', 'site_id' => 60])->persist();
        SiteFactory::make(['id' => 60, 'theme' => 'BcBlog'])->persist();
        $rs = $this->BlogContentsService->get(60);

        $this->assertEquals('test get', $rs['description']);
        $this->assertEquals('title test get', $rs['content']['title']);
        $this->assertEquals('BcBlog', $rs['content']['site']['theme']);
    }

    /**
     * test getIndex
     */
    public function test_getIndex()
    {
        BlogContentFactory::make(['id' => 100, 'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。'])->persist();
        BlogContentFactory::make(['id' => 101, 'description' => 'ディスクリプション'])->persist();

        $result = $this->BlogContentsService->getIndex([])->toArray();
        $this->assertEquals('baserCMS inc. [デモ] の最新の情報をお届けします。', $result[0]['description']);
        $this->assertEquals('ディスクリプション', $result[1]['description']);

        $result = $this->BlogContentsService->getIndex(['description' => 'ディスク'])->toArray();
        $this->assertEquals('ディスクリプション', $result[0]['description']);
    }

    /**
     * test getList
     */
    public function test_getList()
    {
        BlogContentFactory::make(['id' => 111, 'description' => 'test 1'])->persist();
        BlogContentFactory::make(['id' => 112, 'description' => 'test 2'])->persist();

        ContentFactory::make(['id' => 111, 'type' => 'BlogContent', 'entity_id' => 111, 'alias_id' => NULL, 'title' => 'baserCMSサンプル',])->persist();
        ContentFactory::make(['id' => 112, 'type' => 'BlogContent', 'entity_id' => 112, 'alias_id' => NULL, 'title' => 'baserCMSテスト',])->persist();

        $result = $this->BlogContentsService->getList();
        $this->assertEquals('baserCMSサンプル', $result[111]);
        $this->assertEquals('baserCMSテスト', $result[112]);
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $rs = $this->BlogContentsService->getNew();
        $this->assertTrue($rs['comment_use']);
        $this->assertFalse($rs['comment_approve']);
        $this->assertEquals('default', $rs['layout']);
        $this->assertEquals(600, $rs['eye_catch_size_thumb_width']);
    }

    /**
     * test update
     */
    public function test_update()
    {
        BlogContentFactory::make(['id' => 100, 'description' => '新しい'])->persist();
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $data = [
            'id' => 100,
            'description' => '更新した!',
            'content' => [
                'title' => '更新 ブログ',
                'site_id' => 1,
                'parent_id' => 1
            ]
        ];

        $record = $this->BlogContentsService->getIndex([])->first();
        $blogContent = $this->BlogContentsService->update($record, $data);
        $this->assertEquals('更新した!', $blogContent['description']);

        $data = [
            'id' => 100,
            'description' => '更新した!'
        ];
        $this->expectException("Cake\ORM\Exception\PersistenceFailedException");
        $this->expectExceptionMessage("関連するコンテンツがありません");
        $this->BlogContentsService->update($record, $data);
    }

    /**
     * test create
     */
    public function test_create()
    {
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $data = [
            'description' => '新しい ブログコンテンツ',
            'content' => [
                'title' => '新しい ブログ',
                'site_id' => 1,
                'parent_id' => 1
            ]
        ];
        $blogContent = $this->BlogContentsService->create($data);
        $this->assertEquals('新しい ブログコンテンツ', $blogContent['description']);

        $data = [
            'description' => '新しい ブログコンテンツ'
        ];
        $this->expectException("Cake\ORM\Exception\PersistenceFailedException");
        $this->expectExceptionMessage("関連するコンテンツがありません");
        $this->BlogContentsService->create($data);
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        BlogContentFactory::make([
            'id' => 2,
            'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。',
            'template' => 'default',
            'list_count' => '10',
            'list_direction' => 'DESC',
            'feed_count' => '10',
            'tag_use' => '1',
            'comment_use' => '1',
            'comment_approve' => '0',
            'widget_area' => '2',
            'eye_catch_size' => '',
            'use_content' => '1'
        ])->persist();
        ContentFactory::make([
            'id' => 2,
            'title' => 'news',
            'plugin' => 'BcBlog',
            'type' => 'BlogContent',
            'entity_id' => 2,
            'url' => '/test',
            'site_id' => 1,
            'alias_id' => null,
            'main_site_content_id' => null,
            'parent_id' => null,
            'lft' => 1,
            'rght' => 2,
            'level' => 1,

        ])->persist();
        SiteConfigFactory::make([
            'name' => 'contents_sort_last_modified',
            'value' => ''
        ])->persist();
        $data = [
            'entity_id' => 2,
            'parent_id' => 2,
            'site_id' => 1,
            'title' => 'news',
        ];
        $request = $this->getRequest('/baser/admin/baser-core/blog_contents/');
        $this->loginAdmin($request);
        $rs = $this->BlogContentsService->copy($data);
        $this->assertEquals($rs['description'], 'baserCMS inc. [デモ] の最新の情報をお届けします。');
        $this->assertEquals($rs['list_count'], 10);
        $this->assertEquals($rs['content']['title'], 'news');
        $this->assertEquals($rs['content']['type'], 'BlogContent');
        $this->assertNotEquals($rs['id'], 2);
    }

    /**
     * test checkRequireSearchIndexReconstruction
     * @dataProvider checkRequireSearchIndexReconstructionProvider
     */
    public function test_checkRequireSearchIndexReconstruction($beforeValue, $afterValue, $expected)
    {
        $before = new Entity($beforeValue);
        $after = new Entity($afterValue);
        $rs = $this->BlogContentsService->checkRequireSearchIndexReconstruction($before, $after);
        $this->assertEquals($rs, $expected);
    }

    public static function checkRequireSearchIndexReconstructionProvider()
    {
        return [
            [['name' => 'name 1'], ['name' => 'name 2'], true], //$before->name !== $after->name; return true
            [['name' => 'name 1'], ['name' => 'name 1'], false], //$before->name == $after->name; return false
            [['status' => 'status 1'], ['status' => 'status 2'], true], //$before->status !== $after->status; return true
            [['status' => 'status 1'], ['status' => 'status 1'], false], //$before->status == $after->status; return false
            [['parent_id' => 1], ['parent_id' => 2], true], //$before->parent_id !== $after->parent_id; return true
            [['parent_id' => 1], ['parent_id' => 1], false], //$before->status == $after->status; return false
        ];
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        BlogContentFactory::make(['id' => 70, 'description' => 'test delete'])->persist();
        $rs = $this->BlogContentsService->delete(70);
        //戻り値を確認
        $this->assertTrue($rs);
        //データの削除を確認
        $this->expectException(RecordNotFoundException::class);
        $this->BlogContentsService->get(70);
    }

    /**
     * コントロールソースを取得する
     *
     * @dataProvider getControlSourceDataProvider
     */
    public function testGetControlSource($field, $expected)
    {
        if ($field == 'id') {
            BlogContentFactory::make(['id' => 2])->persist();
            ContentFactory::make([
                'id' => 2,
                'title' => 'news',
                'plugin' => 'BcBlog',
                'type' => 'BlogContent',
                'entity_id' => 2
            ])->persist();
        }
        $result = $this->BlogContentsService->getControlSource($field);
        $this->assertEquals($result, $expected);
    }

    public static function getControlSourceDataProvider()
    {
        return [
            [null, false], //$field = null; return false
            ['', false], //$field = ''; return false
            ['hoge', false], //$field が存在しない; return false
            ['id', ['2' => 'news']], //$field がid; return コンテンツタイトル
        ];
    }

    /**
     * test getContentsTemplateRelativePath
     */
    public function test_getContentsTemplateRelativePath()
    {
        //データを生成
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');

        //contentsTemplateは値がある場合、
        $rs = $this->BlogContentsService->getContentsTemplateRelativePath(['contentsTemplate' => 'contentsTemplate']);
        $this->assertEquals($rs, 'BcBlog.../Blog/contentsTemplate/posts');

        //contentsTemplateは値がない、かつBlogContentsにcontentUrlが存在する場合、
        $rs = $this->BlogContentsService->getContentsTemplateRelativePath(['contentUrl' => ['/test']]);
        $this->assertEquals($rs, 'BcBlog.../Blog/default/posts');

        //contentsTemplateは値がない、かつBlogContentsにcontentUrlが存在しない場合、
        $rs = $this->BlogContentsService->getContentsTemplateRelativePath(['contentUrl' => ['/test3']]);
        $this->assertEquals($rs, 'BcBlog.../Blog/default/posts');

    }

    /**
     * test findByName
     * @return void
     */
    public function testFindByName()
    {
        $this->loadFixtureScenario(BlogContentScenario::class, 1, 1, null, 'test', '/test');
        $blogContent = $this->BlogContentsService->findByName('test');
        $this->assertEquals($blogContent->id, 1);
        $this->assertEquals($blogContent->content->url, '/test');
        $this->assertNull($this->BlogContentsService->findByName('non'));
    }
}
