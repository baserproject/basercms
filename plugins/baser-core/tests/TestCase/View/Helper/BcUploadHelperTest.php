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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcUploadHelper;

/**
 * test for BcUploadHelper
 *
 * @package         Baser.Test.Case.View.Helper
 * @property  BcUploadHelper $BcUpload
 */
class BcUploadHelperTest extends BcTestCase
{

    /**
     * Fixtures
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Contents',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Pages',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcUpload = new BcUploadHelper(new BcAdminAppView($this->getRequest()));
    }

    /**
     * tearDown
     */
    public function tearDown(): void
    {
        unset($this->BcUpload);
        parent::tearDown();
    }

    /**
     * testInitialize
     *
     * @return void
     */
    public function testInitialize()
    {
        $this->assertNotEmpty($this->BcUpload->BcAdminForm);
        $this->assertNotEmpty($this->BcUpload->Html);
        $this->assertNotEmpty($this->BcUpload->table);
        $this->assertNotEmpty($this->BcUpload->siteConfigService);
    }

    /**
     * ファイルへのリンクタグを出力する
     */
    public function testFileLink()
    {
        $request = $this->getRequest()->withData('Contents', [
            'id' => '1',
            'name' => '画像（左）とテキスト',
            'eyecatch' => 'template1.jpg',
            'description' => '説明文',
            'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
        ]);
        $BcUpload = new BcUploadHelper(new BcAdminAppView($request));
        $result = $BcUpload->fileLink('Contents.eyecatch');
        $this->assertRegExp('/<a href=\"\/files\/contents\/template1\.jpg/', $result);
    }

    /**
     * ファイルへのリンクタグを出力する(hasMany対応)
     */
    public function testFileLinkHasManyField()
    {
        $request = $this->getRequest()->withData('Contents', [
            [
                'id' => '1',
                'name' => '画像（左）とテキスト',
                'eyecatch' => 'template1.jpg',
                'description' => '説明文',
                'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
            ]
        ]);
        $BcUpload = new BcUploadHelper(new BcAdminAppView($request));
        $result = $BcUpload->fileLink('Contents.0.eyecatch');
        $this->assertRegExp('/<a href=\"\/files\/contents\/template1\.jpg/', $result);
    }

    /**
     * アップロードした画像のタグを出力する
     */
    public function testUploadImage()
    {
        // オプションなし
        $result = $this->BcUpload->uploadImage('Contents.image', 'template1.jpg');
        $this->assertRegExp('/^<a href=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?><img src=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?><\/a>/', $result);

        // サイズ指定あり
        $options = [
            'width' => '100',
            'height' => '80',
        ];
        $result = $this->BcUpload->uploadImage('Contents.image', 'template1.jpg', $options);
        $expects = '<img src="/uploads/tmp/medium/template1.jpg" alt="" width="100" height="80" />';
        $this->assertRegExp('/^<a href=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?><img src=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?alt="" width="100" height="80"[^>]+?><\/a>/', $result);

        // 一時ファイルへのリンク（デフォルトがリンク付だが、Aタグが出力されないのが正しい挙動）
        $options = [
            'tmp' => true
        ];
        $result = $this->BcUpload->uploadImage('Contents.image', 'template1.jpg', $options);
        $expects = '<img src="/uploads/tmp/medium/template1_jpg" alt=""/>';
        $this->assertEquals($expects, $result);

        $options = [
            'link' => false,
            'output' => 'tag'
        ];
        $result = $this->BcUpload->uploadImage('Contents.image', 'template1.jpg', $options);
        $this->assertRegExp('/^<img src=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?>/', $result);

        // output を urlに
        $options = [
            'output' => 'url'
        ];
        $result = $this->BcUpload->uploadImage('Contents.image', 'template1.jpg', $options);
        $this->assertRegExp('/^\/files\/contents\/template1\.jpg\?[0-9]+/', $result);
    }

    /**
     * testGBcUploadSetting
     *
     * @return void
     */
    public function testGetBcUploadSetting()
    {
        $this->assertNotEmpty($this->execPrivateMethod($this->BcUpload, 'getBcUploadSetting', []));
    }

    /**
     * testSBcUploadSetting
     *
     * @return void
     */
    public function testSetBcUploadSetting()
    {
        $this->execPrivateMethod($this->BcUpload, 'setBcUploadSetting', ['test']);
        $this->assertEquals('test', $this->execPrivateMethod($this->BcUpload, 'getBcUploadSetting', []));
    }

}
