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

namespace BaserCore\Test\TestCase\Controller\Api\Admin;

use Authentication\Identity;
use BaserCore\Controller\Api\Admin\BcAdminApiController;
use BaserCore\Test\Factory\UserFactory;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\LoginStoresScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * BaserCore\Controller\ApiControllerTest Test Case
 */
class BcAdminApiControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use IntegrationTestTrait;
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SiteConfigsScenario::class);
        $this->loadFixtureScenario(LoginStoresScenario::class);

    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testInitialize()
    {
        $controller = new BcAdminApiController($this->getRequest());
        $this->assertNotNull($controller->Authentication);
        $this->assertFalse($controller->FormProtection->getConfig('validate'));
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $token = $this->apiLoginAdmin(1);
        $sessionKey = \Cake\Core\Configure::read('BcPrefixAuth.Api/Admin.sessionKey');

        // トークンタイプチェック（USE_CORE_ADMIN_API はテスト既定で true）
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['refresh_token']);
        $this->assertResponseCode(401);
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseOk();

        // GHSA-wgvx-x5g3-9v29: APIを無効化（USE_CORE_ADMIN_API=false）した際の挙動
        $_SERVER['USE_CORE_ADMIN_API'] = 'false';

        // (1) 外部クライアント想定：有効 JWT・セッション無し → 403（本丸：Referer 偽装では通らない）
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseCode(403);

        // (2) Referer のみ設定・未認証 → 403（Referer なりすまし無効化＝CWE-290 是正の核心）
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://localhost';
        $this->get('/baser/api/admin/baser-core/users/index.json');
        $this->assertResponseCode(403);
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['HTTP_REFERER']);

        // (3) 管理画面 SPA 想定：セッション認証（JWT 無し）→ 200
        $this->session([$sessionKey => $this->getUser(1)]);
        $this->get('/baser/api/admin/baser-core/users/index.json');
        $this->assertResponseCode(200);

        // 初期化
        $_SERVER['USE_CORE_ADMIN_API'] = 'true';

        // ユーザーの有効チェック
        $users = $this->getTableLocator()->get('BaserCore.Users');
        $user = $users->get(1);
        $user->status = false;
        $users->save($user);
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseCode(401);
    }

    /**
     * test isAdminApiEnabled
     */
    public function test_isAdminApiEnabled()
    {
        $controller = new BcAdminApiController($this->getRequest());
        $controller->loadComponent('Authentication.Authentication');

        // USE_CORE_ADMIN_API = 'true';
        $_SERVER['USE_CORE_ADMIN_API'] = 'true';

        // - 認証済
        $controller->setRequest($controller->getRequest()->withAttribute('identity', new Identity([])));
        $this->assertTrue($controller->isAdminApiEnabled());

        // - 未認証
        $controller->setRequest($controller->getRequest()->withAttribute('identity', null));
        $this->assertFalse($controller->isAdminApiEnabled());

        // USE_CORE_ADMIN_API = 'false';
        $_SERVER['USE_CORE_ADMIN_API'] = 'false';
        $this->assertFalse($controller->isAdminApiEnabled());

        $_SERVER['USE_CORE_ADMIN_API'] = 'true';
    }

    /**
     * test isAvailableUser
     */
    public function testIsAvailableUser()
    {
        UserFactory::make(['id' => 2])->persist();
        $request = $this->getRequest('/baser/admin/baser-core/themes/');

        $controller = new BcAdminApiController($this->loginAdmin($request));
        $this->assertTrue($controller->isAvailableUser());

        $controller = new BcAdminApiController($this->loginAdmin($request, 2));
        $this->assertFalse($controller->isAvailableUser());
    }
}
