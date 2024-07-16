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

namespace BaserCore\Test\TestCase\Event;

use BaserCore\Event\BcAuthenticationEventListener;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Test\Scenario\InitAppScenario;
use Cake\Event\Event;
use Cake\Http\Exception\HttpException;
use Cake\Http\Exception\RedirectException;
use Cake\Http\Exception\UnauthorizedException;
use Cake\TestSuite\EmailTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcAuthenticationEventListenerTest
 */
class BcAuthenticationEventListenerTest extends BcTestCase
{
    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use EmailTrait;

    /**
     * @var BcAuthenticationEventListener
     */
    public $BcAuthenticationEventListener;

    /**
     * @var TwoFactorAuthenticationsTable
     */
    public $TwoFactorAuthentications;

    /**
     * setUp
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAuthenticationEventListener = new BcAuthenticationEventListener();
        $this->TwoFactorAuthentications = $this->getTableLocator()->get('BaserCore.TwoFactorAuthentications');
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        $this->BcAuthenticationEventListener = null;
        $this->TwoFactorAuthentications = null;
        parent::tearDown();
    }

    /**
     * test implementedEvents
     */
    public function testImplementedEvents()
    {
        $this->assertTrue(is_array($this->BcAuthenticationEventListener->implementedEvents()));
    }

    /**
     * test afterIdentify 管理画面
     */
    public function testAfterIdentify()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $event = new Event('Authentication.afterIdentify', null, []);

        $request = $this->getRequest('/baser/admin/baser-core/users/login');
        $this->loginAdmin($request);

        // 二段階認証無効時
        $siteConfigsService->setValue('use_two_factor_authentication', 0);
        $this->assertNull($this->BcAuthenticationEventListener->afterIdentify($event));

        // 二段階認証有効時
        $siteConfigsService->setValue('use_two_factor_authentication', 1);
        $siteConfigsService->setValue('email', 'from@example.com');

        try {
            $this->BcAuthenticationEventListener->afterIdentify($event);
            throw new \Exception();
        } catch (RedirectException $e) {
            $this->assertEquals('https://localhost/baser/admin/baser-core/users/login_code', $e->getMessage());
            $this->assertMailSentTo('admin@example.com');
            $this->assertMailContainsText('認証コード');
        }
    }

    /**
     * test afterIdentify API
     */
    public function testAfterIdentifyApi()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $siteConfigsService = $this->getService(SiteConfigsServiceInterface::class);
        $event = new Event('Authentication.afterIdentify', null, []);

        $request = $this->getRequest('/baser/api/admin/baser-core/users/login.json');
        $this->loginAdmin($request);

        // 二段階認証無効時
        $siteConfigsService->setValue('use_two_factor_authentication', 0);
        $this->assertNull($this->BcAuthenticationEventListener->afterIdentify($event));

        // 二段階認証有効時
        $siteConfigsService->setValue('use_two_factor_authentication', 1);
        $siteConfigsService->setValue('email', 'from@example.com');

        try {
            $this->BcAuthenticationEventListener->afterIdentify($event);
            throw new \Exception();
        } catch (UnauthorizedException $e) {
            $this->assertEquals('send_codeキーを付与すると認証コードをメールで送信します。', $e->getMessage());
            $this->assertNoMailSent();
        }

        // 認証コード送信要求
        $request = $this->getRequest('/baser/api/admin/baser-core/users/login.json',
            ['send_code' => '1']);
        $this->loginAdmin($request);

        try {
            $this->BcAuthenticationEventListener->afterIdentify($event);
            throw new \Exception();
        } catch (HttpException $e) {
            $this->assertEquals('メールで受信した認証コードをcodeキーの値として送信してください。', $e->getMessage());
            $this->assertMailSentTo('admin@example.com');
            $this->assertMailContainsText('認証コード');
        }

        // 認証コード送信

        // - 失敗
        $request = $this->getRequest('/baser/api/admin/baser-core/users/login.json',
            ['code' => '1234']);
        $this->loginAdmin($request);
        try {
            $this->BcAuthenticationEventListener->afterIdentify($event);
            throw new \Exception();
        } catch (UnauthorizedException $e) {
            $this->assertEquals('認証コードが間違っているか有効期限切れです。', $e->getMessage());
        }

        // - 成功
        $twoFactorAuthentication = $this->TwoFactorAuthentications->find()
            ->orderDesc('modified')
            ->first();
        $request = $this->getRequest('/baser/api/admin/baser-core/users/login.json',
            ['code' => $twoFactorAuthentication->code]);
        $this->loginAdmin($request);
        $this->assertNull($this->BcAuthenticationEventListener->afterIdentify($event));
    }
}
