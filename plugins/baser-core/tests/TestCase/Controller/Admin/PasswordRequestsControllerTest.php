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

namespace BaserCore\Test\TestCase\Controller\Admin;

use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\TestEmailTransport;
use BaserCore\TestSuite\BcTestCase;

/**
 * Class PasswordRequestsControllerTest
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
        'plugin.BaserCore.LoginStores',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.Dblogs',
        'plugin.BaserCore.SiteConfigs'
    ];


    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test initialize
     */
    public function testInitialize()
    {
        $this->get('/baser/admin/baser-core/password_requests/entry');
        $this->assertResponseOk();
    }

    /**
     * Test entry
     */
    public function testEntry()
    {
        TestEmailTransport::clearMessages();

        $this->enableSecurityToken();
        $this->enableCsrfToken();

        $this->get('/baser/admin/baser-core/password_requests/entry');
        $this->assertResponseOk();


        // メールアドレス送信
        $this->post('/baser/admin/baser-core/password_requests/entry', [
            'email' => 'testuser1@example.com',
        ]);
        $this->assertRedirect('/baser/admin/baser-core/password_requests/entry');
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
        $this->assertRedirect('/baser/admin/baser-core/password_requests/done');
    }

    /**
     * Test apply
     */
    public function testApply()
    {
        $this->get('/baser/admin/baser-core/password_requests/apply');
        $this->assertResponseCode(404);
    }

    /**
     * Test done
     */
    public function testDone()
    {
        $this->get('/baser/admin/baser-core/password_requests/done');
        $this->assertResponseOk();
        $this->assertResponseContains('パスワードを変更しました。');
    }
}
