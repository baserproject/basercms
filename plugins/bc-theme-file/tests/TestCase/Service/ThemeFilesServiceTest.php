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

use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\Service\ThemeFilesService;

/**
 * ThemeFilesServiceTest
 */
class ThemeFilesServiceTest extends BcTestCase
{

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
    ];

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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        //データを生成
        $this->getRequest()->getAttribute('currentSite');
        SiteFactory::make(['id' => 1, 'status' => true, 'theme' => 'bc-column'])->persist();
        //パラメーターを作成
        $param = [
            'plugin' => 'BaserCore',
            'theme' => 'BcFront',
            'type' => 'css',
            'path' => 'bge_style.css',
            'fullpath' => '/var/www/html/plugins/bc-front/webroot/css/bge_style.css',
            'assets' => true
        ];
        //対象メソッドをコール
        $rs = $this->ThemeFileService->copyToTheme($param);
        //戻る値を確認
        $this->assertEquals($rs, '/plugins/bc-column/webroot/css/bge_style.css');
        $copiedFilePath = '/var/www/html/plugins/bc-column/webroot/css/bge_style.css';
        //実際にファイルが作成されいてるか確認すること
        $this->assertTrue(file_exists($copiedFilePath));
        //ファイルの中身を確認
        $this->assertTextContains('.cke_editable {
  padding: 15px;
}', file_get_contents($copiedFilePath));
        //作成されたファイルを削除
        unlink($copiedFilePath);
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
