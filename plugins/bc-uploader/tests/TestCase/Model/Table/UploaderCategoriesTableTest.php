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
use BcUploader\Test\Factory\UploaderCategoryFactory;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

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
        $this->truncateTable('uploader_categories');
        $this->truncateTable('uploader_files');
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
        $validator = $this->UploaderCategoriesTable->getValidator('default');
        //必須チェック、
        $errors = $validator->validate([
            'name' => ''
        ]);
        //戻り値を確認
        $this->assertEquals('カテゴリ名を入力してください。', current($errors['name']));

        //スペースだけ登録する
        $errors = $validator->validate([
            'name' => '     '
        ]);
        //戻り値を確認
        $this->assertEquals('カテゴリ名を入力してください。', current($errors['name']));
    }

    /**
     * コピーする
     */
    public function testCopy()
    {
        UploaderCategoryFactory::make(['id' => 1, 'name' => 'test'])->persist();
        $rs = $this->UploaderCategoriesTable->copy(1);
        $this->assertEquals('test_copy', $rs->name);
    }

    /**
     * test beforeCopyEvent
     */
    public function testBeforeCopyEvent()
    {
        UploaderCategoryFactory::make(['id' => 1, 'name' => 'test'])->persist();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BcUploader.UploaderCategories.beforeCopy', function (Event $event) {
            $data = $event->getData('data');
            $data->name = 'beforeCopy';
            $event->setData('data', $data);
        });

        $rs = $this->UploaderCategoriesTable->copy(1);
        //イベントに入るかどうか確認
        $this->assertEquals('beforeCopy_copy', $rs->name);
    }

    /**
     * test AfterCopyEvent
     */
    public function testAfterCopyEvent()
    {
        UploaderCategoryFactory::make(['id' => 1, 'name' => 'test'])->persist();
        //イベントをコル
        $this->entryEventToMock(self::EVENT_LAYER_MODEL, 'BcUploader.UploaderCategories.afterCopy', function (Event $event) {
            $data = $event->getData('data');
            $uploaderCategories = TableRegistry::getTableLocator()->get('BcUploader.UploaderCategories');
            $data->name = 'AfterCopy';
            $uploaderCategories->save($data);
        });
        $rs = $this->UploaderCategoriesTable->copy(1);
        //イベントに入るかどうか確認
        $this->assertEquals('AfterCopy', $rs->name);
    }
}
