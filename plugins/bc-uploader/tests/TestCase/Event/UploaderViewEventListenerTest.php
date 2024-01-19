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
use BaserCore\TestSuite\BcTestCase;
use BcUploader\Event\BcUploaderViewEventListener;

/**
 * Class UploaderViewEventListenerTest
 *
 * @property  BcUploaderViewEventListener $UploaderViewEventListener
 */
class UploaderViewEventListenerTest extends BcTestCase
{

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
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
