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

namespace BcUploader\Test\TestCase\Event;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BcBlog\View\BlogAdminAppView;
use BcUploader\Event\BcUploaderViewEventListener;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class UploaderViewEventListenerTest
 *
 * @property  BcUploaderViewEventListener $UploaderViewEventListener
 */
class UploaderViewEventListenerTest extends BcTestCase
{
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->UploaderViewEventListener = new BcUploaderViewEventListener();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    public function testPagesBeforeRender()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * afterLayout
     */
    public function testAfterLayout()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * CKEditorのアップローダーを組み込む為のJavascriptを返す
     */
    public function test__getCkeditorUploaderScript()
    {
        //準備
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->loginAdmin($this->getRequest("/baser/admin/bc-blog/blog_posts/add/1"));
        $BcAdminAppView = new BlogAdminAppView($request);
        //対象メソッドをコール
        $rs = $this->execPrivateMethod($this->UploaderViewEventListener, '__getCkeditorUploaderScript', [$BcAdminAppView->helpers()->get('BcHtml'), 1]);
        //戻り値を確認
        $this->assertMatchesRegularExpression('/.*CKEDITOR.config.contentsCss instanceof Array.*editor_1.+?/s', $rs);
    }

    /**
     * 画像タグをモバイル用に置き換える
     */
    public function test__mobileImageReplace()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * アンカータグのリンク先が画像のものをモバイル用に置き換える
     */
    public function test__mobileImageAnchorReplace()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
