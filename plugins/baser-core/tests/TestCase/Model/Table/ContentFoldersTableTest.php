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

namespace BaserCore\Test\TestCase\Model\Table;

use ArrayObject;
use Cake\Event\Event;
use Cake\ORM\Entity;
use BaserCore\TestSuite\BcTestCase;
use Cake\ORM\TableRegistry;

/**
 * Class ContentFoldersTableTest
 */
class ContentFoldersTableTest extends BcTestCase
{
    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.ContentFolders',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.SiteConfigs',
        'plugin.BaserCore.Contents',
//        'plugin.BaserCore.Service/SearchIndexesService/ContentsReconstruct',
//        'plugin.BaserCore.Service/SearchIndexesService/PagesReconstruct',
//        'plugin.BaserCore.Service/SearchIndexesService/ContentFoldersReconstruct',
    ];

    // TODO loadFixtures を利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要
//    public $autoFixtures = false;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentFolders = $this->getTableLocator()->get('BaserCore.ContentFolders');
        $this->SearchIndexes = $this->getTableLocator()->get('BcSearchIndex.SearchIndexes');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentFolders);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize(): void
    {
        $this->assertTrue($this->ContentFolders->hasBehavior('BcContents'));
        $this->assertTrue($this->ContentFolders->hasBehavior('Timestamp'));
    }

    /**
     * testValidationDefault
     *
     * @return void
     */
    public function testValidationDefault(): void
    {
        $contentFolder = $this->ContentFolders->newEntity(['id' => 'test']);
        $this->assertSame([
            'id' => [
                'integer' => 'The provided value is invalid',
                'valid' => 'IDに不正な値が利用されています。'
            ],
            // BcContentsBehaviorのafterMarshalにて、contentを他のフィールド同様必要前提としている
            'content' => [
                '_required' => '関連するコンテンツがありません'
            ]
        ], $contentFolder->getErrors());
    }

    /**
     * testBeforeSave
     *
     * @return void
     */
    public function testBeforeSave(): void
    {
        $data = new Entity(['id' => 1]);
        $this->ContentFolders->dispatchEvent('Model.beforeSave', ['entity' => $data, 'options' => new ArrayObject()]);
        $this->assertTrue($this->ContentFolders->beforeStatus);
    }

    /**
     * testAfterSave
     *
     * @return void
     */
    public function testAfterSave(): void
    {
        $this->markTestIncomplete('loadFixtures を利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
        $this->loadFixtures(
            'Service\SearchIndexesService\ContentsReconstruct',
            'Service\SearchIndexesService\PagesReconstruct',
            'Service\SearchIndexesService\ContentFoldersReconstruct',
        );
        $contentFolder = $this->ContentFolders->get(1, ['contain' => ['Contents']]);
        $this->SearchIndexes->deleteAll([]);
        // $this->Pages->delete($page);
        $this->ContentFolders->dispatchEvent('Model.afterSave', ['entity' => $contentFolder, 'options' => new ArrayObject(['reconstructSearchIndices' => true])]);
        $this->assertTrue($this->ContentFolders->isMovableTemplate);
        // reconstructされてるか
        $this->assertEquals(4, $this->SearchIndexes->find()->count());

    }

    /**
     * testAfterMove
     *
     * @return void
     */
    public function testAfterMove(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * testSetBeforeRecord
     *
     * @return void
     */
    public function testSetBeforeRecord(): void
    {
        $this->execPrivateMethod($this->ContentFolders, "setBeforeRecord", [1]);
        $this->assertTrue($this->ContentFolders->beforeStatus);
    }

    /**
     * test beforeCopyEvent
     */
    public function testBeforeCopyEvent()
    {
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BaserCore.ContentFolders.beforeCopy', function (Event $event) {
            $data = $event->getData('data');
            $data['folder_template'] = 'beforeCopy';
            $event->setData('data', $data);
        });
        $this->ContentFolders->copy(1, 1, 'new title', 1, 1);
        //イベントに入るかどうか確認
        $contentFolders = $this->getTableLocator()->get('BaserCore.ContentFolders');
        $query = $contentFolders->find()->where(['folder_template' => 'beforeCopy']);
        $this->assertEquals(1, $query->count());
    }

    /**
     * test AfterCopyEvent
     */
    public function testAfterCopyEvent()
    {
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BaserCore.ContentFolders.afterCopy', function (Event $event) {
            $data = $event->getData('data');
            $contentFolders = TableRegistry::getTableLocator()->get('BaserCore.ContentFolders');
            $data->folder_template = 'AfterCopy';
            $contentFolders->save($data);
        });
        $this->ContentFolders->copy(1, 1, 'new title', 1, 1);
        //イベントに入るかどうか確認
        $contentFolders = $this->getTableLocator()->get('BaserCore.ContentFolders');
        $query = $contentFolders->find()->where(['folder_template' => 'AfterCopy']);
        $this->assertEquals(1, $query->count());
    }
}
