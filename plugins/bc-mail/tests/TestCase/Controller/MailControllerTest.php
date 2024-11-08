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
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\SiteFactory;
use BaserCore\Test\Scenario\InitAppScenario;
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

        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        SiteFactory::make(['id' => 1])->persist();
        MailFieldsFactory::make(['mail_content_id' => 1, 'field_name' => 'sex'])->persist();
        ContentFactory::make(['id' => 1, 'plugin' => 'BcMail', 'type' => 'MailContent', 'entity_id' => 1, 'url' => '/contact/', 'site_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        MailContentFactory::make(['id' => 1, 'form_template' => 'default', 'mail_template' => 'mail_default'])->persist();

        //正常テスト GET METHOD
        $this->get('/contact/confirm');
        $this->assertResponseCode(302);
        $this->assertRedirect('/contact/');
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertNotNull($vars['title']);
        $this->assertNotNull($vars['description']);

        //異常テスト　valid＝false
        $this->post('/contact/confirm/', ['sex' => 1]);
        $this->assertResponseCode(302);
        $this->assertRedirect('/contact/');
        $this->assertFlashMessage('エラーが発生しました。もう一度操作してください。');

        //正常テスト 　valid＝true
        $this->session(['BcMail' => ['valid' => true]]);
        $this->post('/contact/confirm/', ['sex' => 1]);
        $this->assertResponseCode(200);
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertNotNull($vars['mailContent']);
        $this->assertNotNull($vars['mailFields']);
        $this->assertNotNull($vars['mailMessage']);
        $this->assertNotNull($vars['description']);

        //異常テスト
        $this->post('/contact/confirm/');
        $this->assertResponseCode(500);
    }

    /**
     * [PUBIC] データ送信
     */
    public function testSubmit()
    {
        //準備
        $this->enableSecurityToken();
        $this->enableCsrfToken();

        SiteConfigFactory::make(['name' => 'email', 'value' => 'abc@gmail.com'])->persist();
        SiteConfigFactory::make(['name' => 'admin-theme', 'value' => 'test theme'])->persist();
        SiteFactory::make(['id' => 1])->persist();
        MailFieldsFactory::make(['mail_content_id' => 1, 'field_name' => 'sex'])->persist();
        ContentFactory::make(['id' => 1, 'plugin' => 'BcMail', 'type' => 'MailContent', 'entity_id' => 1, 'url' => '/contact/', 'site_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        MailContentFactory::make([
            'id' => 1,
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'subject_user' => '【baserCMS】お問い合わせ頂きありがとうございます。',
            'subject_admin' => '【baserCMS】お問い合わせを受け付けました',
            'sender_1' => 't@gm.com'
        ])->persist();

        $this->session(['BcMail' => ['valid' => true]]);
        $this->post('/contact/submit/', ['sex' => 1]);
        $this->assertResponseCode(302);
        $this->assertRedirect('/contact/thanks');
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

    /**
     * [PUBIC] メール送信完了
     */
    public function test_thanks()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        ContentFactory::make(['id' => 1, 'plugin' => 'BcMail', 'type' => 'MailContent', 'entity_id' => 1, 'url' => '/contact/', 'site_id' => 1, 'lft' => 1, 'rght' => 2])->persist();
        MailContentFactory::make(['id' => 1, 'form_template' => 'default', 'mail_template' => 'mail_default'])->persist();

        $this->session(['BcMail.MailContent' => MailContentFactory::get(1)]);
        //正常テスト
        $this->get('/contact/thanks');

        $this->assertResponseOk();
        $vars = $this->_controller->viewBuilder()->getVars();
        $this->assertNotNull($vars['mailContent']);
    }
}
