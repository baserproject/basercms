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
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\TestSuite\IntegrationTestTrait;

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

    /**
     * Fixtures
     *
     * @var array
     */
    public $fixtures = [
        'plugin.BaserCore.Users',
        'plugin.BaserCore.UsersUserGroups',
        'plugin.BaserCore.UserGroups',
        'plugin.BaserCore.LoginStores',
        'plugin.BaserCore.Sites',
        'plugin.BaserCore.SiteConfigs',
    ];

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testInitialize()
    {
        $controller = new BcAdminApiController($this->getRequest());
        $this->assertTrue(isset($controller->Authentication));
        $this->assertFalse($controller->Security->getConfig('validatePost'));
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testBeforeFilter()
    {
        $token = $this->apiLoginAdmin(1);
        // トークンタイプチェック
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['refresh_token']);
        $this->assertResponseCode(401);
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseOk();

        // APIを無効
        $_SERVER['USE_CORE_ADMIN_API'] = 'false';
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseCode(403);

        // API無効、かつ、同じサイトからのリクエスト
        $_SERVER['HTTP_HOST'] = 'localhost';
        $_SERVER['HTTP_REFERER'] = 'https://localhost';
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseCode(200);

        // 初期化
        unset($_SERVER['HTTP_HOST']);
        unset($_SERVER['HTTP_REFERER']);
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


}
