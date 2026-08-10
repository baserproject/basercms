<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcBurgerEditor\Test\TestCase\View\Helper;

use BaserCore\TestSuite\BcTestCase;
use BcBurgerEditor\View\Helper\BurgerEditorHelper;
use Cake\View\View;

/**
 * BurgerEditorHelperTest
 */
class BurgerEditorHelperTest extends BcTestCase
{

    /**
     * @var BurgerEditorHelper
     */
    public $BurgerEditor;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BurgerEditor = new BurgerEditorHelper(new View());
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BurgerEditor);
        parent::tearDown();
    }

    /**
     * test shouldLoadStyle
     *
     * 初期状態では読み込む
     */
    public function test_shouldLoadStyle_default()
    {
        $this->assertTrue($this->BurgerEditor->shouldLoadStyle());
    }

    /**
     * test preventLoadingStyle
     *
     * 呼び出すと読み込まなくなる
     */
    public function test_preventLoadingStyle()
    {
        $this->BurgerEditor->preventLoadingStyle();
        $this->assertFalse($this->BurgerEditor->shouldLoadStyle());
    }

    /**
     * test getFileId
     *
     * @param string $fileName
     * @param string|null $expected
     * @dataProvider getFileIdDataProvider
     */
    public function test_getFileId($fileName, $expected)
    {
        $this->assertSame($expected, $this->execPrivateMethod($this->BurgerEditor, 'getFileId', [$fileName]));
    }

    public static function getFileIdDataProvider()
    {
        return [
            ['12__c2FtcGxl.jpg', '12'],
            ['1__c2FtcGxl.jpg', '1'],
            // 先頭が数字＋アンダースコア2つでない場合は取得できない
            ['sample.jpg', null],
            ['__sample.jpg', null],
        ];
    }

    /**
     * test getFileListWithPagination
     *
     * ページ指定が無く選択済みも無い場合は先頭ページを返す
     */
    public function test_getFileListWithPagination_default()
    {
        $result = BurgerEditorHelper::getFileListWithPagination($this->createFileList(25), null, null, 10);
        $this->assertCount(10, $result['data']);
        $this->assertSame('1', $result['data'][0]['fileId']);
        $this->assertSame(3, $result['pagination']['pageMaxNumber']);
        $this->assertSame(1, $result['pagination']['currentPageNumber']);
        $this->assertNull($result['pagination']['selectedFileId']);
    }

    /**
     * test getFileListWithPagination
     *
     * ページを指定した場合は該当ページを返す
     */
    public function test_getFileListWithPagination_withTargetPage()
    {
        $result = BurgerEditorHelper::getFileListWithPagination($this->createFileList(25), 3, null, 10);
        $this->assertCount(5, $result['data']);
        $this->assertSame('21', $result['data'][0]['fileId']);
        $this->assertSame(3, $result['pagination']['currentPageNumber']);
    }

    /**
     * test getFileListWithPagination
     *
     * 存在しないページを指定した場合は先頭ページを返す
     */
    public function test_getFileListWithPagination_withOverflowPage()
    {
        $result = BurgerEditorHelper::getFileListWithPagination($this->createFileList(25), 99, null, 10);
        $this->assertCount(10, $result['data']);
        $this->assertSame('1', $result['data'][0]['fileId']);
        $this->assertSame(1, $result['pagination']['currentPageNumber']);
    }

    /**
     * test getFileListWithPagination
     *
     * 選択済みファイルがある場合はそのファイルを含むページを返す
     */
    public function test_getFileListWithPagination_withSelectedFileId()
    {
        $result = BurgerEditorHelper::getFileListWithPagination($this->createFileList(25), null, '15', 10);
        $this->assertSame(2, $result['pagination']['currentPageNumber']);
        $this->assertSame('11', $result['data'][0]['fileId']);
        $this->assertSame('15', $result['pagination']['selectedFileId']);
    }

    /**
     * test getFileListWithPagination
     *
     * 空のリストでもエラーとならない
     */
    public function test_getFileListWithPagination_withEmptyList()
    {
        $result = BurgerEditorHelper::getFileListWithPagination([], null, null, 10);
        $this->assertSame([], $result['data']);
        $this->assertSame(1, $result['pagination']['pageMaxNumber']);
        $this->assertSame(1, $result['pagination']['currentPageNumber']);
    }

    /**
     * テスト用のファイルリストを生成する
     *
     * @param int $count
     * @return array
     */
    private function createFileList($count)
    {
        $fileList = [];
        for($i = 1; $i <= $count; $i++) {
            $fileList[] = ['fileId' => (string)$i, 'name' => "sample{$i}.jpg"];
        }
        return $fileList;
    }

}
