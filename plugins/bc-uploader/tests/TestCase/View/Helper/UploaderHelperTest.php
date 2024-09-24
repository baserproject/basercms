<?php
namespace BcUploader\Test\TestCase\View\Helper;
use App\View\AppView;
use BaserCore\View\Helper\BcUploadHelper;
use BcUploader\Test\Factory\UploaderFileFactory;
use BcUploader\View\Helper\UploaderHelper;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;
use Cake\I18n\FrozenTime;
use Cake\View\View;

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 4.0.9
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class UploaderHelperTest
 *
 * @property  UploaderHelper $UploaderHelper
 */
class UploaderHelperTest extends BcTestCase
{
    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderHelper = new UploaderHelper(new AppView($this->getRequest('/')));
        $this->UploaderHelper->beforeRender(new Event('beforeRender'), '');
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Before Render
     */
    public function testBeforeRender()
    {
        $this->assertEquals('/files/uploads/', $this->UploaderHelper->savedUrl);
        $this->assertEquals('/var/www/html/webroot/files/uploads/', $this->UploaderHelper->savePath);
    }

    /**
     * リスト用のimgタグを出力する
     */
    public function testFile()
    {
        $uploaderFile = UploaderFileFactory::make(['name' => 'test.jpg', 'alt' => 'Example Image Alt Text'])->getEntity();
        $rs = $this->UploaderHelper->file($uploaderFile, ['size' => 'small']);
        $this->assertEquals('<img src="/files/uploads/test.jpg" alt="Example Image Alt Text" size="small">', $rs);

        //options empty
        $rs = $this->UploaderHelper->file($uploaderFile);
        $this->assertEquals('<img src="/files/uploads/test.jpg" alt="Example Image Alt Text">', $rs);

        //extension don't have ['gif', 'jpg', 'png']
        $uploaderFile = UploaderFileFactory::make(['name' => 'example.pdf'])->getEntity();
        $rs = $this->UploaderHelper->file($uploaderFile);
        $this->assertEquals('<img src="/bc_uploader/img/icon_upload_file.png" alt="">', $rs);
    }

    /**
     * ファイルが保存されているURLを取得する
     * @dataProvider getFileUrlProviderData
     */
    public function testGetFileUrl($fileName, $expected)
    {
        $rs = $this->UploaderHelper->getFileUrl($fileName);
        $this->assertEquals($expected, $rs);
    }

    public static function getFileUrlProviderData()
    {
        return [
            ['', ''],
            ['test.jpg', '/files/uploads/test.jpg']
        ];
    }

    /**
     * ダウンロードリンクを表示
     */
    public function testDownload()
    {
        $uploaderFile = UploaderFileFactory::make(['name' => 'test.jpg'])->getEntity();
        $linkText = 'click here to download';
        $rs = $this->UploaderHelper->download($uploaderFile, $linkText);
        $this->assertEquals('<a href="/files/uploads/test.jpg" target="_blank">click here to download</a>', $rs);

        $rs = $this->UploaderHelper->download($uploaderFile);
        $this->assertEquals('<a href="/files/uploads/test.jpg" target="_blank">≫ ダウンロード</a>', $rs);
    }

    /**
     * ファイルの公開制限期間が設定されているか判定する
     * @dataProvider isLimitSettingDataProvider
     */
    public function testIsLimitSetting($data, $expected)
    {
        $rs = $this->UploaderHelper->isLimitSetting($data);
        $this->assertEquals($expected, $rs);
    }

    public static function isLimitSettingDataProvider()
    {
        return [
            [['UploaderFile' => []], false],
            [['UploaderFile' => ['publish_begin' => '2023-01-01']], true],
            [['UploaderFile' => ['publish_end' => '2023-12-31']], true],
            [['UploaderFile' => ['publish_begin' => '2023-01-01', 'publish_end' => '2023-12-31']], true],
            [['publish_begin' => '2023-01-01'], true],
            [['publish_end' => '2023-12-31'], true],
            [[], false],
        ];
    }

    /**
     * ファイルの公開状態を取得する
     * test isPublish
     * @param $publishBegin
     * @param $publishEnd
     * @param $expected
     * @dataProvider isPublishDataProvider
     */
    public function testIsPublish($publishBegin, $publishEnd, $expected)
    {
        $uploaderFile = UploaderFileFactory::make(['publish_begin' => $publishBegin, 'publish_end' => $publishEnd])->getEntity();
        $rs = $this->UploaderHelper->isPublish($uploaderFile);
        $this->assertEquals($expected, $rs);
    }

    public static function isPublishDataProvider()
    {
        return [
            [null, null, true],
            [new FrozenTime('+1 day'), null, false],
            [null, new FrozenTime('-1 day'), false],
            [new FrozenTime('-1 day'), new FrozenTime('+1 day'), true],
            [new FrozenTime('+1 day'), new FrozenTime('+2 day'), false],
            [new FrozenTime('now'), new FrozenTime('now'), false]
        ];
    }

    /**
     * test getBasePath
     * @param $settings
     * @param $isTheme
     * @param $expected
     * @dataProvider getBasePathDataProvider
     */
    public function testGetBasePath($settings, $isTheme, $expected)
    {
        $helper = new BcUploadHelper(new View());

        $result = $helper->getBasePath($settings, $isTheme);
        $this->assertEquals($expected, $result);
    }

    public static function getBasePathDataProvider()
    {
        return [
            [['saveDir' => 'uploads/images'], false, '/files/uploads/images/'],
            [['saveDir' => 'uploads/images'], true, '/bc_front/files/uploads/images/'],
            [['saveDir' => 'documents/files'], false, '/files/documents/files/'],
            [['saveDir' => 'documents/files'], true, '/bc_front/files/documents/files/'],
        ];
    }
}
