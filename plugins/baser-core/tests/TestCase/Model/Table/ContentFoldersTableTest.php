<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Test\TestCase\Model\Table;

use ReflectionClass;
use BaserCore\TestSuite\BcTestCase;
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
        ], $contentFolder->getErrors());
    }

    /**
     * testBeforeSave
     *
     * @return void
     */
    public function testBeforeSave(): void
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * testAfterSave
     *
     * @return void
     */
    public function testAfterSave(): void
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
        $reflection = new ReflectionClass($this->ContentFolders);
        $property = $reflection->getProperty('beforeStatus');
        $property->setAccessible(true);
        $beforeStatus =  $property->getValue($this->ContentFolders);
        $this->assertEquals("/", $this->ContentFolders->beforeUrl);
        $this->assertTrue($beforeStatus);
    }
}
