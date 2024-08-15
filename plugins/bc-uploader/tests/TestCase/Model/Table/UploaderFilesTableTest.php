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
use BaserCore\Utility\BcFile;
use BcUploader\Model\Table\UploaderFilesTable;
use BcUploader\Test\Factory\UploaderFileFactory;

/**
 * Class UploaderFileTest
 *
 * @property  UploaderFilesTable $UploaderFilesTable
 */
class UploaderFilesTableTest extends BcTestCase
{
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderFilesTable = $this->getTableLocator()->get('BcUploader.UploaderFiles');
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->UploaderFilesTable);
        parent::tearDown();
    }

    /**
     * initialize
     */
    public function test_initialize()
    {
        $this->assertEquals('uploader_files', $this->UploaderFilesTable->getTable());
        $this->assertEquals('id', $this->UploaderFilesTable->getPrimaryKey());
        $this->assertTrue($this->UploaderFilesTable->hasBehavior('Timestamp'));
        $this->assertTrue($this->UploaderFilesTable->hasBehavior('BcUpload'));
        $this->assertTrue($this->UploaderFilesTable->hasAssociation('UploaderCategories'));
    }
    /**
     * 公開期間をチェックする
     * @dataProvider periodDataProvider
     */
    public function testCheckPeriod($publishBegin, $publishEnd, $expected)
    {
        $context = [
            'data' => [
                'publish_begin' => $publishBegin,
                'publish_end' => $publishEnd,
            ]
        ];
        $rs = $this->UploaderFilesTable->checkPeriod(null, $context);
        $this->assertEquals($expected, $rs);
    }

    public static function periodDataProvider()
    {
        return [
            ['2021-01-01 00:00:00', '2021-01-02 00:00:00', true],
            ['2021-01-02 00:00:00', '2021-01-01 00:00:00', false],
        ];
    }
    /**
     * Before Save
     */
    public function testBeforeSave()
    {
        //準備
        $file = new BcFile('/var/www/html/webroot/files/uploads/limited/2_2_test.jpg');
        $file->create();
        UploaderFileFactory::make(['id' => 1, 'name' => '2_2_test.jpg', 'atl' => '2_2_test.jpg', 'user_id' => 1])->persist();

        //対象メソードをコール
        $this->UploaderFilesTable->dispatchEvent('Model.beforeSave', ['entity' => UploaderFileFactory::get(1), 'options' => new \ArrayObject()]);
        // beforeSaveに入ったかどうかを確認
        $this->assertTrue(file_exists('/var/www/html/webroot/files/uploads/2_2_test.jpg'));

        //不要データを削除
        unlink('/var/www/html/webroot/files/uploads/2_2_test.jpg');
    }

    /**
     * ファイルの存在チェックを行う
     */
    public function testFileExists()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 複数のファイルの存在チェックを行う
     */
    public function testFilesExists()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * ソースファイルの名称を取得する
     * @dataProvider getSourceFileNameDataProvider
     */
    public function testGetSourceFileName($fileName, $expected)
    {
        $this->assertEquals($expected, $this->UploaderFilesTable->getSourceFileName($fileName));
    }

    public static function getSourceFileNameDataProvider()
    {
        return [
            ['example__large.jpg', 'example.jpg'],
            ['example__midium.png', 'example.png'],
            ['example__small.jpg', 'example.jpg'],
            ['example__mobile_large.jpg', 'example.jpg'],
            ['example__mobile_small.git', 'example.git'],
        ];
    }


    /**
     * Before Delete
     */
    public function testBeforeDelete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
