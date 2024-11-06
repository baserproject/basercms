<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

namespace BcMail\Test\TestCase\Controller;

use BaserCore\Test\Factory\ContentFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Test\Factory\MailContentFactory;
use BcMail\Test\Factory\MailFieldsFactory;

class MailControllerTest extends BcTestCase
{
    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * set up
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * beforeFilter.
     */
    public function testBeforeFilter()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * beforeRender
     */
    public function testBeforeRender()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [test_index description]
     * @return [type] [description]
     */
    public function test_index()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [PUBIC] フォームを表示する
     *
     * @return void
     */
    public function testIndex()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [PUBIC] データの確認画面を表示
     */
    public function testConfirm()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * [PUBIC] データ送信
     */
    public function testSubmit()
    {
        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        SiteFactory::make(['id' => 1])->persist();
        MailFieldsFactory::make(['mail_content_id' => 1, 'field_name' => 'sex'])->persist();
        ContentFactory::make(['id' => 1, 'plugin' => 'BcMail', 'type' => 'MailContent', 'entity_id' => 1, 'url' => '/contact/', 'site_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        MailContentFactory::make(['id' => 1, 'form_template' => 'default', 'mail_template' => 'mail_default', 'sender_1' => 't@gm.com'])->persist();

        $this->session(['BcMail' => ['valid' => true]]);
        $this->post('/contact/submit/', ['sex' => 1]);
        $this->assertResponseCode(302);
    }

    /**
     * [private] 確認画面から戻る
     */
    public function test_back()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

    /**
     * 認証用のキャプチャ画像を表示する
     */
    public function testCaptcha()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}
