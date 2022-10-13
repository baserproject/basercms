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
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcBlog\Service\BlogContentsService;
use BcBlog\Test\Factory\BlogContentsFactory;
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test get
     */
    public function test_get()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test update
     */
    public function test_update()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test create
     */
    public function test_create()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}
