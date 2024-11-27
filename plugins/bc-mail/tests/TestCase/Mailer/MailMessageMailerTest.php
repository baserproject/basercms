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

namespace BcMail\Test\TestCase\Mailer;

use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\TestSuite\BcTestCase;
use BcMail\Mailer\MailMessageMailer;
use BcMail\Test\Factory\MailContentFactory;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * MailMessageMailerTest
 *
 * @property MailMessageMailer $MailMessageMailer
 */
class MailMessageMailerTest extends BcTestCase
{

    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        SiteConfigFactory::make(['name' => 'email', 'value' => 'basertest@example.com'])->persist();
        $this->MailMessageMailer = new MailMessageMailer();
    }

    /**
     * tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * test sendFormToAdmin
     */
    public function testSendFormToAdmin()
    {
        //準備
        $data['message'] = 'message test';
        $data['mailContent'] = 'content test';
        $data['mailFields'] = 'fields test';
        $mailContent = MailContentFactory::make([
            'description' => 'description test',
            'sender_1' => 'sender_1',
            'sender_name' => 'name 111',
            'subject_user' => 'subject_user 111',
            'subject_admin' => 'subject_admin 111',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
        ])->getEntity();

        //テスト
        $this->MailMessageMailer->sendFormToAdmin($mailContent, 'abc@example.com', 'abcUser@example.com', $data, [], []);

        //戻り値を確認
        $this->assertEquals(['abc@example.com' => 'abc@example.com'], $this->MailMessageMailer->getTo());
        $this->assertEquals(['abcUser@example.com' => 'abcUser@example.com'], $this->MailMessageMailer->getReplyTo());

        $vars = $this->MailMessageMailer->viewBuilder()->getVars();
        $this->assertEquals('message test', $vars['message']);
        $this->assertEquals('content test', $vars['mailContent']);
        $this->assertEquals('fields test', $vars['mailFields']);
        $this->assertEquals('admin', $vars['other']['mode']);
    }

    /**
     * test sendFormToUser
     */
    public function testSendFormToUser()
    {
        //準備
        $data['message'] = 'message test';
        $data['mailContent'] = 'content test';
        $data['mailFields'] = 'fields test';
        $mailContent = MailContentFactory::make([
            'description' => 'description test',
            'sender_1' => 'sender_1',
            'sender_name' => 'name 111',
            'subject_user' => 'subject_user 111',
            'subject_admin' => 'subject_admin 111',
            'form_template' => 'default',
            'mail_template' => 'mail_default',
            'redirect_url' => '/',
        ])->getEntity();

        //テスト
        $this->MailMessageMailer->sendFormToUser($mailContent, 'abc@example.com', 'abcUser@example.com', $data, [], []);

        //戻り値を確認
        $this->assertEquals(['abc@example.com' => 'abc@example.com'], $this->MailMessageMailer->getReplyTo());
        $this->assertEquals(['abcUser@example.com' => 'abcUser@example.com'], $this->MailMessageMailer->getTo());

        $vars = $this->MailMessageMailer->viewBuilder()->getVars();
        $this->assertEquals('message test', $vars['message']);
        $this->assertEquals('content test', $vars['mailContent']);
        $this->assertEquals('fields test', $vars['mailFields']);
        $this->assertEquals('user', $vars['other']['mode']);
    }

    /**
     * test getFrom
     */
    public function testGetFrom()
    {
        $this->markTestIncomplete('このテストは未実装');
    }
}
