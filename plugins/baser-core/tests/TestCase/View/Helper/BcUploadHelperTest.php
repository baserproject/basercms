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

namespace BaserCore\Test\TestCase\View\Helper;

use BaserCore\Model\Entity\Content;
use BaserCore\Model\Entity\Page;
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
        $this->BcUpload->setTable('BaserCore.Contents');
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
        $this->assertNotEmpty($this->BcUpload->siteConfigService);
    }

    /**
     * ファイルへのリンクタグを出力する
     */
    public function testFileLink()
    {
        $data = [
            'id' => '1',
            'name' => '画像（左）とテキスト',
            'eyecatch' => 'template1.jpg',
            'description' => '説明文',
            'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
        ];
        $request = $this->getRequest()->withData('Contents', $data);
        $BcUpload = new BcUploadHelper(new BcAdminAppView($request));
        $BcUpload->setTable('BaserCore.Contents');
        $result = $BcUpload->fileLink('eyecatch', new Content($data));
        $this->assertMatchesRegularExpression('/<a href=\"\/files\/contents\/template1\.jpg/', $result);
    }

    /**
     * ファイルへのリンクタグを出力する(hasMany対応)
     */
    public function testFileLinkHasManyField()
    {
        $data = [
            'content' => [
                'id' => '1',
                'name' => '画像（左）とテキスト',
                'eyecatch' => 'template1.jpg',
                'description' => '説明文',
                'modified' => '2013-07-21 01:41:12', 'created' => '2013-07-21 00:53:42',
            ]
        ];
        $request = $this->getRequest()->withData('Contents', $data);
        $BcUpload = new BcUploadHelper(new BcAdminAppView($request));
        $BcUpload->setTable('BaserCore.Contents');
        $result = $BcUpload->fileLink('content.eyecatch', new Page($data));
        $this->assertMatchesRegularExpression('/<a href=\"\/files\/contents\/template1\.jpg/', $result);
    }

    /**
     * アップロードした画像のタグを出力する
     */
    public function testUploadImage()
    {
        // オプションなし
        $result = $this->BcUpload->uploadImage('image', new Content(['image' => 'template1.jpg']));
        $this->assertMatchesRegularExpression('/^<a href=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?><img src=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?><\/a>/', $result);

        // サイズ指定あり
        $options = [
            'width' => '100',
            'height' => '80',
        ];
        $result = $this->BcUpload->uploadImage('image', new Content(['image' => 'template1.jpg']), $options);
        $this->assertMatchesRegularExpression('/^<a href=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?><img src=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?alt="" width="100" height="80"[^>]+?><\/a>/', $result);

        // 一時ファイルへのリンク（デフォルトがリンク付だが、Aタグが出力されないのが正しい挙動）
        $options = [
            'tmp' => true
        ];
        $result = $this->BcUpload->uploadImage('eyecatch', new Content([
            'eyecatch' => 'template1.jpg',
            'eyecatch_tmp' => 'test'
        ]), $options);
        $expects = '<img src="/baser-core/uploads/tmp/medium/test" alt=""/>';
        $this->assertEquals($expects, $result);

        $options = [
            'link' => false,
            'output' => 'tag'
        ];
        $result = $this->BcUpload->uploadImage('image', new Content(['image' => 'template1.jpg']), $options);
        $this->assertMatchesRegularExpression('/^<img src=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?>/', $result);

        // output を urlに
        $options = [
            'output' => 'url'
        ];
        $result = $this->BcUpload->uploadImage('image', new Content(['image' => 'template1.jpg']), $options);
        $this->assertMatchesRegularExpression('/^\/files\/contents\/template1\.jpg\?[0-9]+/', $result);
    }

    /**
     * testGBcUploadSetting
     *
     * @return void
     */
    public function testGetBcUploadSetting()
    {
        // $this->tableの設定
        $this->execPrivateMethod($this->BcUpload, 'initField', ['Contents.test']);
        $this->assertNotEmpty($this->execPrivateMethod($this->BcUpload, 'getBcUploadSetting', []));
    }

    /**
     * testSBcUploadSetting
     *
     * @return void
     */
    public function testSetBcUploadSetting()
    {
        // $this->tableの設定
        $this->execPrivateMethod($this->BcUpload, 'initField', ['Contents.test']);
        $this->execPrivateMethod($this->BcUpload, 'setBcUploadSetting', [['test']]);
        $this->assertEquals([
            0 => 'test',
            'saveDir' => '',
            'existsCheckDirs' => [],
            'fields' => []
        ], $this->execPrivateMethod($this->BcUpload, 'getBcUploadSetting', []));
    }

    /**
     * testInitField
     *
     * @return void
     */
    public function testInitField()
    {
        $this->execPrivateMethod($this->BcUpload, 'initField', [['table' => 'BaserCore.Contents']]);
        $this->assertNotEmpty($this->getPrivateProperty($this->BcUpload, 'table'));
    }

    /**
     * test setTable
     */
    public function testSetTable()
    {
        $this->BcUpload->setTable('contents');
        // テーブルがセットされたかどうか確認する
        $this->assertEquals('contents', $this->getPrivateProperty($this->BcUpload, 'table')->getTable());
    }

}
