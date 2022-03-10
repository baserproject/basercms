<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */
namespace BaserCore\Test\TestCase\Model\Behavior;

use ArrayObject;
use Cake\ORM\Entity;
use ReflectionClass;
use Cake\Filesystem\File;
use BaserCore\TestSuite\BcTestCase;
use Laminas\Diactoros\UploadedFile;
use BaserCore\Model\Table\PagesTable;
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Model\Table\ContentsTable;
use BaserCore\Model\Behavior\BcSearchIndexManager;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Model\Behavior\BcSearchIndexManagerBehavior;

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
        'plugin.BaserCore.SearchIndexes',
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
        parent::setUp();
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->table, $this->BcSearchIndexManager);
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
        $this->assertCount(8, $this->BcSearchIndexManager->SearchIndexes->find()->all());
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
        $this->assertTrue($this->table->deleteSearchIndex(2));
        $this->assertTrue($this->BcSearchIndexManager->SearchIndexes->findByModelId(2)->isEmpty());
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
