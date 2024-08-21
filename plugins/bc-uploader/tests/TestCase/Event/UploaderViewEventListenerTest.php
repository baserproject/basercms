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
use Cake\Event\Event;
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
        $this->loadFixtureScenario(InitAppScenario::class);

        //BcUploaderViewがある場合、
        $request = $this->loginAdmin($this->getRequest("/baser/admin/bc-blog/blog_posts/add/1"));
        $BcAdminAppView = new BlogAdminAppView($request);
        $BcAdminAppView->loadHelper('BaserCore.BcCkeditor');
        $BcAdminAppView->assign('content', '</head>{"ckeditorField":"editor_content"');
        $event = new Event('View.afterLayout', $BcAdminAppView);

        $this->UploaderViewEventListener->afterLayout($event);

        $content = $BcAdminAppView->fetch('content');
        //JSを読み込むできるか確認すること
        $this->assertTextContains('画像を選択するか、URLを直接入力して下さい。', $content);

        //BcUploaderViewがない場合、
        $BcAdminAppView = new BlogAdminAppView($this->getRequest("/"));
        $BcAdminAppView->assign('content', '</head>{"ckeditorField":"editor_content"');
        $event = new Event('View.afterLayout', $BcAdminAppView);

        $this->UploaderViewEventListener->afterLayout($event);

        $content = $BcAdminAppView->fetch('content');
        //JSを読み込むできないか確認すること
        $this->assertEquals('</head>{"ckeditorField":"editor_content"', $content);
    }

    /**
     * CKEditorのアップローダーを組み込む為のJavascriptを返す
     */
    public function test__getCkeditorUploaderScript()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
