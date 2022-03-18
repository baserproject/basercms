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

namespace BaserCore\Test\TestCase\Model\Table;

use ArrayObject;
use Cake\ORM\Entity;
use ReflectionClass;
use Cake\Event\Event;
use BaserCore\TestSuite\BcTestCase;
use Cake\Datasource\EntityInterface;
use BaserCore\Model\Entity\ContentFolder;
use BaserCore\Model\Table\ContentFoldersTable;

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
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.ContentFolders',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $config = $this->getTableLocator()->exists('ContentFolders')? [] : ['className' => 'BaserCore\Model\Table\ContentFoldersTable'];
        $this->ContentFolders = $this->getTableLocator()->get('ContentFolders', $config);
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
        $contentFolder = $this->ContentFolders->get(1, ['contain' => ['Contents']]);
        $this->ContentFolders->save($contentFolder);
        $this->assertTrue($this->ContentFolders->isMovableTemplate);
        // TODO: reconstructSearchIndicesのテスト
    }

    /**
     * testBeforeMove
     *
     * @return void
     */
    public function testBeforeMove(): void
    {
        $this->ContentFolders->dispatchEvent('Controller.Contents.beforeMove', [new ContentFolder(), new ArrayObject(), 'data.currentType' => 'ContentFolder', 'data.entityId' => 1]);
        $this->assertTrue($this->ContentFolders->beforeStatus);
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
}
