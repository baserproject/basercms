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
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\BlogContentsService;
use BcBlog\Test\Factory\BlogContentsFactory;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BlogContentsServiceTest
 * @property BlogContentsService $BlogContentsService
 */
class BlogContentsServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Pages',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/SearchIndexes',
        'plugin.BcBlog.Factory/BlogContents',
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
        BlogContentsFactory::make(['id' => 60, 'description' => 'test get'])->persist();
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
        BlogContentsFactory::make(['id' => 100, 'description' => 'baserCMS inc. [デモ] の最新の情報をお届けします。'])->persist();
        BlogContentsFactory::make(['id' => 101, 'description' => 'ディスクリプション'])->persist();

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
        BlogContentsFactory::make(['id' => 111, 'description' => 'test 1'])->persist();
        BlogContentsFactory::make(['id' => 112, 'description' => 'test 2'])->persist();

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
        BlogContentsFactory::make(['id' => 100, 'description' => '新しい'])->persist();
        $data = [
            'id' => 100,
            'description' => '更新した!',
            'content' => [
                'title' => '更新 ブログ',
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
        $data = [
            'description' => '新しい ブログコンテンツ',
            'content' => [
                'title' => '新しい ブログ'
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        BlogContentsFactory::make(['id' => 70, 'description' => 'test delete'])->persist();
        $rs = $this->BlogContentsService->delete(70);
        //戻り値を確認
        $this->assertTrue($rs);
        //データの削除を確認
        $this->expectException(RecordNotFoundException::class);
        $this->BlogContentsService->get(70);
    }

}
