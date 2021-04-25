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

namespace BaserCore\Test\TestCase\Controller;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestEmailTransport;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PasswordRequestsControllerTest
 * @package BaserCore\Test\TestCase\Controller
 */
class PasswordRequestsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use EmailTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.PasswordRequests',
        'plugin.BaserCore.Users',
    ];

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->get('/baser/admin/password_requests/entry');
        $this->assertResponseOk();
    }

    /**
     * Test entry
     */
    public function testEntry()
    {
        $messages = TestEmailTransport::clearMessages();

        $this->enableSecurityToken();

        $this->get('/baser/admin/password_requests/entry');
        $this->assertResponseOk();

        // メールアドレス送信
        $this->post('/baser/admin/password_requests/entry', [
            'email' => 'testuser1@example.com',
        ]);
        $this->assertResponseOk();
        $this->assertMailSentTo('testuser1@example.com');

        // メール内のURLにアクセス
        $messages = TestEmailTransport::getMessages();
        preg_match('/https?:\/\/[^\s]*(\/baser\/admin\/[^\s]+)/', $messages[0]->getBodyText(), $matches);
        if (!$matches) {
            $this->fail('パスワード変更のURLの取得に失敗');
        }
        $passwordEditUrl = $matches[1];
        $this->assertNotEmpty($matches);
        $this->get($passwordEditUrl);
        $this->assertResponseOk();

        // パスワード変更
        $this->post($passwordEditUrl, [
            'password_1' => 'new-password',
            'password_2' => 'new-password',
        ]);
        $this->assertRedirect('/baser/admin/password_requests/done');
    }

    /**
     * Test apply
     */
    public function testApply()
    {
        $this->get('/baser/admin/password_requests/apply');
        $this->assertResponseFailure();
    }

    /**
     * Test done
     */
    public function testDone()
    {
        $this->get('/baser/admin/password_requests/done');
        $this->assertResponseOk();
        $this->assertResponseContains('パスワードを変更しました。');
    }
}
