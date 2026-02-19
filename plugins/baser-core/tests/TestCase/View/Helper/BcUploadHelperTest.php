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
use BaserCore\Test\Scenario\ContentsScenario;
use BaserCore\Test\Scenario\PagesScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\View\BcAdminAppView;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\View\Helper\BcUploadHelper;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * test for BcUploadHelper
 *
 * @property  BcUploadHelper $BcUpload
 */
class BcUploadHelperTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * setUp
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(ContentsScenario::class);
        $this->loadFixtureScenario(SitesScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(PagesScenario::class);
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
        $this->assertMatchesRegularExpression('/^<a href=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?><img src=\"\/files\/contents\/template1\.jpg[^>]+?\"[^>]+?alt="" width="100" height="80"[^>]*?><\/a>/', $result);

        // 一時ファイルへのリンク（デフォルトでは colorbox 付きリンクで拡大できる）
        $options = [
            'tmp' => true
        ];
        $result = $this->BcUpload->uploadImage('eyecatch', new Content([
            'eyecatch' => 'template1.jpg',
            'eyecatch_tmp' => 'test'
        ]), $options);
        $expects = '<a href="/baser-core/uploads/tmp/test" rel="colorbox"><img src="/baser-core/uploads/tmp/medium/test" alt=""></a>';
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

    /**
     * test fileLink with _tmp value
     * {field}_tmp値がある場合、/baser-core/uploads/tmp/パスを使って優先表示する
     */
    public function testFileLinkWithTmpValue()
    {
        $data = [
            'eyecatch' => 'original_image.jpg',
            'eyecatch_tmp' => 'session_eyecatch_key.jpg',
        ];
        $result = $this->BcUpload->fileLink('eyecatch', new Content($data));

        // セッション画像のパス(/baser-core/uploads/tmp/)が使われていることを確認
        $this->assertStringContainsString('/baser-core/uploads/tmp/', $result, '_tmp値がある場合は/baser-core/uploads/tmp/パスを使うべきです');
        // セッションキー変換: '.' と '/' が '_' に変換される
        $expectedKey = str_replace('/', '_', 'session_eyecatch_key.jpg');
        $this->assertStringContainsString($expectedKey, $result, 'セッションキーが正しく変換されていません');
        $this->assertStringContainsString('rel="colorbox"', $result, 'tmp画像でもcolorboxリンクが付与されるべきです');
    }

    /**
     * test uploadImage auto-detects _tmp value
     * {field}_tmpがエンティティにセットされていれば、tmp=trueを明示しなくてもtmpパスで画像URLが生成される
     */
    public function testUploadImageAutoTmpValue()
    {
        $data = [
            'eyecatch' => 'original_image.jpg',
            'eyecatch_tmp' => 'session_og_image.jpg',
        ];

        // tmp=trueを明示しない（自動検出のテスト）
        $result = $this->BcUpload->uploadImage('eyecatch', new Content($data));

        // tmpパスが自動的に使われることを確認
        $this->assertStringContainsString('/baser-core/uploads/tmp/', $result, '_tmp値がある場合は自動的にtmpパスを使うべきです');
        // セッションキー変換: '.' と '/' が '_' に変換される
        $expectedKey = str_replace('/', '_', 'session_og_image.jpg');
        $this->assertStringContainsString($expectedKey, $result, 'セッションキーが正しく変換されていません');
        $this->assertStringContainsString('href="/baser-core/uploads/tmp/' . $expectedKey . '"', $result, 'tmp画像の拡大リンク先は元サイズであるべきです');
        $this->assertStringContainsString('rel="colorbox"', $result, 'tmp画像でもcolorboxリンクが付与されるべきです');
    }

    /**
     * test uploadImage tmp with link false
     * tmp画像でも link=false が明示された場合はリンクを出力しない
     */
    public function testUploadImageTmpWithLinkFalse()
    {
        $result = $this->BcUpload->uploadImage('eyecatch', new Content([
            'eyecatch' => 'template1.jpg',
            'eyecatch_tmp' => 'test'
        ]), [
            'tmp' => true,
            'link' => false,
        ]);

        $this->assertEquals('<img src="/baser-core/uploads/tmp/medium/test" alt="">', $result);
    }

}
