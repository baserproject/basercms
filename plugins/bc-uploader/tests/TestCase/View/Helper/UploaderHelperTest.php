<?php
namespace BcUploader\Test\TestCase\View\Helper;
use App\View\AppView;
use BcUploader\View\Helper\UploaderHelper;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;

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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * リスト用のimgタグを出力する
     */
    public function testFile()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
     */
    public function testIsPublish()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
