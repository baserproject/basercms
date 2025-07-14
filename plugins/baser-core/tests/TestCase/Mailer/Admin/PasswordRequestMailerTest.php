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

namespace BaserCore\Test\TestCase\Mailer\Admin;

use BaserCore\Mailer\Admin\PasswordRequestMailer;
use BaserCore\Test\Factory\PasswordRequestFactory;
use BaserCore\Test\Factory\SiteConfigFactory;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\TestSuite\BcTestCase;
use Cake\Routing\Router;
use Cake\Utility\Security;

/**
 * Class PasswordRequestMailerTest
 */
class PasswordRequestMailerTest extends BcTestCase
{

    /**
     * @var PasswordRequestMailer
     */
    public $PasswordRequestMailer;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        SiteConfigFactory::make(['name' => 'email', 'value' => 'basertest@example.com'])->persist();
        SiteConfigFactory::make(['name' => 'admin-theme', 'value' => 'test theme'])->persist();
        $this->PasswordRequestMailer = new PasswordRequestMailer();
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
     * test resetPassword
     */
    public function testResetPassword()
    {
        UserFactory::make(['id' => 1])->persist();
        PasswordRequestFactory::make(['id' => 1, 'user_id' => 1, 'request_key' => Security::randomString(40), 'used' => 0,])->persist();

        Router::setRequest(
            $this->getRequest()->withParam('prefix', 'Admin')
                ->withParam('controller', 'MailController')
                ->withParam('plugin', 'Mail')
        );
        //正常テスト　メールが無事に送信できる
        $this->PasswordRequestMailer->resetPassword(UserFactory::get(1), PasswordRequestFactory::get(1));

        //正常テスト　エラーを出る
        $this->expectException('Cake\Datasource\Exception\RecordNotFoundException');
        $this->PasswordRequestMailer->resetPassword(UserFactory::get(2), PasswordRequestFactory::get(1));
    }

}
