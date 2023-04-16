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
     * Auto Fixtures
     * @var bool
     */
    public $autoFixtures = false;

    /**
     * set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtures('Sites', 'SiteConfigs');
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
        $this->loadFixtures('Users', 'UserGroups', 'UsersUserGroups', 'LoginStores');
        $token = $this->apiLoginAdmin(1);
        // トークンタイプチェック
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['refresh_token']);
        $this->assertResponseCode(401);
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseOk();
        // ユーザーの有効チェック
        $users = $this->getTableLocator()->get('BaserCore.Users');
        $user = $users->get(1);
        $user->status = false;
        $users->save($user);
        $this->get('/baser/api/admin/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseCode(401);
    }

}
