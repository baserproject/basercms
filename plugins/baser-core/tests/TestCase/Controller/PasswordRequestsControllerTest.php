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

namespace BaserCore\Test\TestCase\Controller;

use BaserCore\Test\Scenario\PasswordRequestsScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\Test\Scenario\SitesScenario;
use BaserCore\Test\Scenario\UsersScenario;
use BaserCore\TestSuite\BcTestCase;
use Cake\TestSuite\EmailTrait;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestEmailTransport;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PasswordRequestsControllerTest
 */
class PasswordRequestsControllerTest extends BcTestCase
{
    use IntegrationTestTrait;
    use EmailTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(SitesScenario::class);
        $this->loadFixtureScenario(UsersScenario::class);
        $this->loadFixtureScenario(PasswordRequestsScenario::class);
    }

    /**
     * Test entry
     *
     * 登録の有無で表示を変えず、メールアドレスが登録済みかを判別できないようにする
     */
    public function testEntry()
    {
        TestEmailTransport::clearMessages();
        $this->enableSecurityToken();
        $this->enableCsrfToken();
        $message = 'パスワードのリセットを受け付けました。該当するメールアドレスが登録されている場合は、変更用のURLを送信しました。';

        // 登録済みのメールアドレス
        $this->post('/baser-core/password_requests/entry', ['email' => 'testuser1@example.com']);
        $this->assertRedirect('/baser-core/password_requests/entry');
        $this->assertFlashMessage($message);
        $this->assertMailSentTo('testuser1@example.com');

        // 未登録のメールアドレス
        TestEmailTransport::clearMessages();
        $this->post('/baser-core/password_requests/entry', ['email' => 'notfound@example.com']);
        $this->assertRedirect('/baser-core/password_requests/entry');
        $this->assertFlashMessage($message);
        $this->assertNoMailSent();
    }

}
