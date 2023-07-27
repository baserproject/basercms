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

namespace BcThemeFile\Test\TestCase\Service;

use BaserCore\Error\BcFormFailedException;
use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Service\ThemeFilesService;
use Cake\Filesystem\File;

/**
 * ThemeFilesServiceTest
 */
class ThemeFilesServiceTest extends BcTestCase
{

    public $ThemeFileService = null;

    /**
     * set up
     */
    public function setUp(): void
    {
        $this->setFixtureTruncate();
        parent::setUp();
        $this->ThemeFileService = new ThemeFilesService();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test __construct
     */
    public function test__construct()
    {
        $this->assertTrue(isset($this->ThemeFileService->ThemeFileForm));
    }

    /**
     * test getNew
     */
    public function test_getNew()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test get
     */
    public function test_get()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getForm
     */
    public function test_getForm()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test create
     */
    public function test_create()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test update
     */
    public function test_update()
    {
        //POSTデータを生成
        $fullpath = BASER_PLUGINS . 'BcThemeSample' . '/templates/layout/';
        new File($fullpath . 'test.php', true);
        $data = [
            'fullpath' => $fullpath . 'test.php',
            'parent' => $fullpath,
            'base_name' => 'test_update',
            'ext' => 'php',
            'contents' => "<?php echo 'test' ?>"
        ];
        //正常系テスト
        $rs = $this->ThemeFileService->update($data);
        //戻る値を確認
        $this->assertEquals($rs->getData('fullpath'), $fullpath . 'test_update.php');
        //実際にファイルが変更されいてるか確認すること
        $this->assertTrue(file_exists($fullpath . 'test_update.php'));
        //ファイルの中身を確認
        $this->assertEquals(file_get_contents($fullpath . 'test_update.php'), "<?php echo 'test' ?>");
        //変更した前にファイル名が存在しないか確認すること
        $this->assertFalse(file_exists($fullpath . 'test.php'));
        //作成されたファイルを削除
        unlink($fullpath . 'test_update.php');

        //異常系テスト・ファイル名を入力しない
        $postData['base_name'] = '';
        $this->expectException(BcFormFailedException::class);
        $this->expectExceptionMessage('ファイルの保存に失敗しました。');
        $this->ThemeFileService->update($postData);
    }

    /**
     * test delete
     */
    public function test_delete()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test copy
     */
    public function test_copy()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test upload
     */
    public function test_upload()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test copyToTheme
     */
    public function test_copyToTheme()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * test getImg
     */
    public function test_getImg()
    {
        $data = [
            'theme' => 'BcFront',
            'type' => 'img',
            'path' => 'logo.png',
            'fullpath' => '/var/www/html/plugins/bc-front/webroot/img/logo.png'
        ];

        $img = $this->ThemeFileService->getImg($data);
        $this->assertNotNull($img);
    }

    /**
     * test getImgThumb
     */
    public function test_getImgThumb()
    {
        $data = [
            'theme' => 'BcFront',
            'type' => 'img',
            'path' => 'logo.png',
            'fullpath' => '/var/www/html/plugins/bc-front/webroot/img/logo.png'
        ];

        $imgThumb = $this->ThemeFileService->getImgThumb($data, 100, 100);
        //戻る値を確認
        $this->assertNotNull($imgThumb['imgThumb']);
        $this->assertEquals('png', $imgThumb['extension']);
    }
}
