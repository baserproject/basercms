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
namespace BcSearchIndex\Test\TestCase\Model\Behavior;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Model\Table\PagesTable;
use BcSearchIndex\Model\Behavior\BcSearchIndexManagerBehavior;
use Cake\Event\Event;

/**
 * Class BcSearchIndexManagerBehavioreTest
 *
 * @package Baser.Test.Case.Model
 */
class BcSearchIndexManagerBehaviorTest extends BcTestCase
{

    public $fixtures = [
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.ContentFolders',
        'plugin.BcSearchIndex.SearchIndexes',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * @var PagesTable|BcSearchIndexManagerBehavior
     */
    public $table;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        $this->table = $this->getTableLocator()->get('BaserCore.Pages');
        $this->table->setPrimaryKey(['id']);
        $this->table->addBehavior('BaserCore.BcSearchIndexManager');
        $this->BcSearchIndexManager = $this->table->getBehavior('BcSearchIndexManager');
        $this->SearchIndexes = $this->getTableLocator()->get('SearchIndexes');
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->table, $this->BcSearchIndexManager, $this->SearchIndexes);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertNotEmpty($this->BcSearchIndexManager->Contents);
        $this->assertNotEmpty($this->BcSearchIndexManager->SearchIndexes);
        $this->assertNotEmpty($this->BcSearchIndexManager->SiteConfigs);
        $this->assertInstanceOf("BaserCore\Model\Table\PagesTable", $this->BcSearchIndexManager->table);
        $Contents = $this->getTableLocator()->get('BaserCore.Contents');
        $this->expectExceptionMessage("BaserCore\Model\Table\ContentsTable::createSearchIndex()が実装されてません");
        $Contents->addBehavior('BcSearchIndex.BcSearchIndexManager');
    }


    /**
     * コンテンツデータを登録する
     *
     * @param Model $model
     * @param array $data
     * @return boolean
     */
    public function testSaveSearchIndex()
    {
        $page = $this->table->find()->contain(['Contents' => ['Sites']])->first();
        // 新規の場合
        $pageSearchIndex = $this->table->createSearchIndex($page);
        unset($pageSearchIndex['model_id']); // model_idがない場合は新規追加
        $this->assertTrue($this->table->saveSearchIndex($pageSearchIndex));
        // SearchIndexesが新規で追加されているかを確認
        $this->assertCount(11, $this->BcSearchIndexManager->SearchIndexes->find()->all());
        // 更新の場合
        $pageSearchIndex = $this->table->createSearchIndex($page);
        $this->assertTrue($this->table->saveSearchIndex($pageSearchIndex));
        $searchIndex = $this->BcSearchIndexManager->SearchIndexes->findByModelId($page->id)->first();
        $content = $this->BcSearchIndexManager->Contents->findById($page->content->id)->first();
        // searchIndexが新規のcontentに合わせて書き換わってるかを確認する
        $this->assertEquals($content->title, $searchIndex->title);
    }

    /**
     * コンテンツデータを削除する
     */
    public function testDeleteSearchIndex()
    {
        $this->assertTrue($this->table->deleteSearchIndex(5));
        $this->assertTrue($this->BcSearchIndexManager->SearchIndexes->findByModelId(5)->isEmpty());
    }

    /**
     * test afterDelete
     * @return void
     */
    public function testAfterDelete()
    {
        $event = new Event("afterSave");
        $page = $this->table->find()->contain(['Contents' => ['Sites']])->first();
        $this->BcSearchIndexManager->afterDelete($event, $page, new \ArrayObject());
        $this->assertEquals(true, $this->SearchIndexes->findByModelId($page->id)->isEmpty());
    }

    /**
     * コンテンツメタ情報を更新する
     */
    public function testUpdateSearchIndexMeta()
    {
        $this->BcSearchIndexManager->SiteConfigs->saveValue('content_types', '');
        $this->assertTrue($this->table->updateSearchIndexMeta());
        $this->assertNotEmpty($this->BcSearchIndexManager->SiteConfigs->getValue('content_types'));
    }

}
