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
use BaserCore\Test\Scenario\ContentFoldersScenario;
use BaserCore\Test\Scenario\ContentsScenario;
use Cake\Event\Event;
use Cake\ORM\Entity;
use BaserCore\TestSuite\BcTestCase;
use Cake\ORM\TableRegistry;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class ContentFoldersTableTest
 */
class ContentFoldersTableTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

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
                'integer' => 'The provided value must be an integer',
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
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
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
        $contentFolder = $this->ContentFolders->get(1, contain: ['Contents']);
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
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
        $this->execPrivateMethod($this->ContentFolders, "setBeforeRecord", [1]);
        $this->assertTrue($this->ContentFolders->beforeStatus);
    }

    /**
     * test beforeCopyEvent
     */
    public function testBeforeCopyEvent()
    {
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
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
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);
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

    /**
     * test copy
     */
    public function test_copy()
    {
        //データを生成
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(ContentFoldersScenario::class);

        //コピーする前にDBのデータを確認
        $contentFolders = $this->getTableLocator()->get('BaserCore.ContentFolders');
        $query = $contentFolders->find()->where(['folder_template' => 'baserCMSサンプル']);
        $this->assertEquals(1, $query->count());

        //対象メソッドを呼ぶ
        $rs = $this->ContentFolders->copy(1, 1, 'new title', 1, 1);

        //戻り値を確認
        $this->assertEquals('new title', $rs->content->title);

        //DBに存在するか確認すること
        $query = $contentFolders->find()->where(['folder_template' => 'baserCMSサンプル']);
        $this->assertEquals(2, $query->count());
    }
}
