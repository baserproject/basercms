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
use BaserCore\Utility\BcContainerTrait;
use BcMail\Test\Factory\MailContentFactory;
use BcMail\Test\Factory\MailFieldsFactory;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

class MailControllerTest extends BcTestCase
{

    use ScenarioAwareTrait;
    use IntegrationTestTrait;
    use BcContainerTrait;

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
     */
    public function test_index()
    {
        //準備
        SiteFactory::make(['id' => 1])->persist();
        MailFieldsFactory::make(['mail_content_id' => 1, 'no' => 1])->persist();
        ContentFactory::make(['id' => 1, 'plugin' => 'BcMail', 'type' => 'MailContent', 'entity_id' => 1, 'url' => '/contact/', 'site_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        MailContentFactory::make(['id' => 1, 'form_template' => 'default', 'mail_template' => 'mail_default'])->persist();
        //正常テスト
        $this->get('/contact/');

        $this->assertResponseOk();
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertNotNull($vars['mailContent']);
        $this->assertNotNull($vars['mailFields']);
        $this->assertNotNull($vars['mailMessage']);
        $this->assertNotNull($vars['description']);
        $this->assertTrue($_SESSION["BcMail"]["valid"]);

        //異常テスト
        $this->get('/contact-test/');
        $this->assertResponseCode(404);
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
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
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
