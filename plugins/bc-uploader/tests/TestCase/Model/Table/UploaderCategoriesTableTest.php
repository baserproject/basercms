<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

namespace BcUploader\Test\TestCase\Model\Table;

use BaserCore\TestSuite\BcTestCase;
use BcUploader\Model\Table\UploaderCategoriesTable;

/**
 * Class UploaderCategoriesTableTest
 *
 * @property  UploaderCategoriesTable $UploaderCategoriesTable
 */
class UploaderCategoriesTableTest extends BcTestCase
{
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderCategoriesTable = $this->getTableLocator()->get('BcUploader.UploaderCategories');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UploaderCategoriesTable);
        parent::tearDown();
    }

    /**
     * initialize
     */
    public function test_initialize()
    {
        $this->assertEquals('uploader_categories', $this->UploaderCategoriesTable->getTable());
        $this->assertEquals('id', $this->UploaderCategoriesTable->getPrimaryKey());
        $this->assertTrue($this->UploaderCategoriesTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->UploaderCategoriesTable->hasAssociation('UploaderFiles'));
    }

    /**
     * validationDefault
     */
    public function test_validationDefault()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * コピーする
     */
    public function testCopy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
