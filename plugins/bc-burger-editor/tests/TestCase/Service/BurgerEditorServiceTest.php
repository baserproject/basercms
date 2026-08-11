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

namespace BcBurgerEditor\Test\TestCase\Service;

use BaserCore\TestSuite\BcTestCase;
use BcBurgerEditor\Service\BurgerEditorService;
use Cake\Core\Configure;
use Cake\Core\Configure\Engine\PhpConfig;
use Cake\Core\Plugin;

/**
 * BurgerEditorServiceTest
 */
class BurgerEditorServiceTest extends BcTestCase
{

    /**
     * @var BurgerEditorService
     */
    public $BurgerEditorService;

    /**
     * 一時ファイルの配置先
     *
     * @var string
     */
    private $tmpDir;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BurgerEditorService = new BurgerEditorService();
        // プラグインの設定はテストプロセスには読み込まれないため明示的に読み込む
        // （Bge.fileShare 等が未定義だと保存先の解決結果が実行時と変わる）
        if (Configure::read('Bge') === null) {
            if (!in_array('baser', Configure::configured(), true)) {
                Configure::config('baser', new PhpConfig());
            }
            Configure::load('BcBurgerEditor.setting', 'baser');
        }

        $this->tmpDir = TMP . 'bc_burger_editor_test' . DS;
        if (!is_dir($this->tmpDir)) {
            mkdir($this->tmpDir, 0777, true);
        }
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        foreach(glob($this->tmpDir . '*') as $file) {
            if (is_file($file)) unlink($file);
        }
        if (is_dir($this->tmpDir)) rmdir($this->tmpDir);
        unset($this->BurgerEditorService);
        parent::tearDown();
    }

    /**
     * test rotateImage
     *
     * Exif を持たない画像は回転せずそのまま残る
     */
    public function test_rotateImage_withoutExif()
    {
        $source = Plugin::path('BcBurgerEditor') . 'webroot' . DS . 'img' . DS . 'bg-sample.png';
        $target = $this->tmpDir . 'sample.png';
        copy($source, $target);
        $before = md5_file($target);

        $this->assertTrue($this->BurgerEditorService->rotateImage($target));
        $this->assertSame($before, md5_file($target), '回転対象外の画像が書き換えられています');
    }

    /**
     * test rotateImage
     *
     * 画像以外のファイルを渡しても例外とならない
     */
    public function test_rotateImage_withNotImageFile()
    {
        $target = $this->tmpDir . 'sample.txt';
        file_put_contents($target, 'this is not an image');
        $this->assertTrue($this->BurgerEditorService->rotateImage($target));
    }

    /**
     * test rotateImage
     *
     * 存在しないファイルを渡しても例外とならない
     */
    public function test_rotateImage_withMissingFile()
    {
        $this->assertTrue($this->BurgerEditorService->rotateImage($this->tmpDir . 'not-exists.jpg'));
    }

    /**
     * test getBasePath
     *
     * プラグインの基準パスを返す
     */
    public function test_getBasePath()
    {
        $result = $this->BurgerEditorService->getBasePath();
        $this->assertSame(Plugin::path('BcBurgerEditor'), $result);
        $this->assertDirectoryExists($result);
    }

    /**
     * test getBlockPath
     *
     * 実在するブロックの基準パスを返す
     */
    public function test_getBlockPath()
    {
        $result = $this->BurgerEditorService->getBlockPath();
        $this->assertSame(Plugin::path('BcBurgerEditor') . 'Addon' . DS . 'block' . DS, $result);
        $this->assertDirectoryExists($result);
    }

    /**
     * test getTypePath
     *
     * 実在するタイプの基準パスを返す
     */
    public function test_getTypePath()
    {
        $result = $this->BurgerEditorService->getTypePath();
        $this->assertSame(Plugin::path('BcBurgerEditor') . 'Addon' . DS . 'type' . DS, $result);
        $this->assertDirectoryExists($result);
    }

    /**
     * test element
     *
     * タイプの表示用テンプレートが出力される
     */
    public function test_element()
    {
        ob_start();
        $this->BurgerEditorService->element('embed');
        $result = ob_get_clean();
        $this->assertStringContainsString('<div class="valueembed">', $result);
        $this->assertContains('embed', $this->BurgerEditorService->getAddonList());
    }

    /**
     * test element
     *
     * 存在しないタイプを指定しても何も出力されない
     */
    public function test_element_withMissingType()
    {
        ob_start();
        $this->BurgerEditorService->element('not-exists-type');
        $result = ob_get_clean();
        $this->assertSame('', $result);
    }

    /**
     * test addAddonList / getAddonList
     *
     * 同じ名前は重複して登録されない
     */
    public function test_getAddonList()
    {
        ob_start();
        $this->BurgerEditorService->element('embed');
        $this->BurgerEditorService->element('embed');
        ob_end_clean();
        $result = $this->BurgerEditorService->getAddonList();
        $this->assertSame(['embed'], $result);
    }

    /**
     * test getAddonList
     *
     * 状態がインスタンスごとに独立している
     */
    public function test_getAddonList_isNotShared()
    {
        ob_start();
        $this->BurgerEditorService->element('embed');
        ob_end_clean();

        $other = new BurgerEditorService();
        $this->assertSame([], $other->getAddonList());
    }

    /**
     * test setupSavePath
     *
     * 保存先のパスとURLが解決される
     */
    public function test_setupSavePath()
    {
        // 解決前は空
        $this->assertSame('', $this->BurgerEditorService->getImageFileBaseDir());

        $this->BurgerEditorService->setupSavePath();

        $this->assertSame(realpath(WWW_ROOT) . DS . 'files' . DS . 'bgeditor' . DS . 'img' . DS, $this->BurgerEditorService->getImageFileBaseDir());
        $this->assertSame(realpath(WWW_ROOT) . DS . 'files' . DS . 'bgeditor' . DS . 'other' . DS, $this->BurgerEditorService->getOtherFileBaseDir());
        $this->assertStringEndsWith('files/bgeditor/img/', $this->BurgerEditorService->getImageFileBaseURL());
        $this->assertStringEndsWith('files/bgeditor/other/', $this->BurgerEditorService->getOtherFileBaseURL());
        // 保存先フォルダは作成される
        $this->assertDirectoryExists($this->BurgerEditorService->getImageFileBaseDir());
        $this->assertDirectoryExists($this->BurgerEditorService->getOtherFileBaseDir());
    }

    /**
     * test nextImageFileId / nextOtherFileId
     *
     * 呼び出すたびに採番される
     */
    public function test_nextFileId()
    {
        $this->assertSame(1, $this->BurgerEditorService->nextImageFileId());
        $this->assertSame(2, $this->BurgerEditorService->nextImageFileId());
        $this->assertSame(1, $this->BurgerEditorService->nextOtherFileId());
    }

    /**
     * test getImageList
     *
     * 一覧取得により最大IDが更新され、次の採番に反映される
     */
    public function test_getImageList_updatesMaxId()
    {
        $this->BurgerEditorService->setupSavePath();
        $path = $this->BurgerEditorService->getImageFileBaseDir() . '9999__c2FtcGxl.png';
        file_put_contents($path, 'dummy');

        try {
            $result = $this->BurgerEditorService->getImageList();
            $this->assertContains($path, $result);
            $this->assertSame(10000, $this->BurgerEditorService->nextImageFileId());
        } finally {
            unlink($path);
        }
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
        $this->assertSame($expected, $this->execPrivateMethod($this->BurgerEditorService, 'getFileId', [$fileName]));
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
        $result = BurgerEditorService::getFileListWithPagination($this->createFileList(25), null, null, 10);
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
        $result = BurgerEditorService::getFileListWithPagination($this->createFileList(25), 3, null, 10);
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
        $result = BurgerEditorService::getFileListWithPagination($this->createFileList(25), 99, null, 10);
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
        $result = BurgerEditorService::getFileListWithPagination($this->createFileList(25), null, '15', 10);
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
        $result = BurgerEditorService::getFileListWithPagination([], null, null, 10);
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
