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

namespace BaserCore\Test\TestCase\Controller\Api;

use Authentication\Authenticator\Result;
use BaserCore\Controller\Api\BcApiController;
use BaserCore\Service\UserServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\TestSuite\IntegrationTestTrait;

/**
 * BaserCore\Controller\ApiControllerTest Test Case
 */
class BcApiControllerTest extends BcTestCase
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
    }

    /**
     * Test index method
     *
     * @return void
     */
    public function testInitialize()
    {
        $controller = new BcApiController();
        $this->assertTrue(isset($controller->Authentication));
        $this->assertFalse($controller->Security->getConfig('validatePost'));
    }

    /**
     * test getAccessToken
     */
    public function testGetAccessToken()
    {
        $user = $this->getService(UserServiceInterface::class);
        $controller = new BcApiController();
        $result = $controller->getAccessToken(new Result($user->get(1), Result::SUCCESS));
        $this->assertArrayHasKey('access_token', $result);
        $result = $controller->getAccessToken(new Result(null, Result::FAILURE_CREDENTIALS_INVALID));
        $this->assertEquals([], $result);
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
        $this->get('/baser/api/baser-core/users/index.json?token=' . $token['refresh_token']);
        $this->assertResponseCode(401);
        $this->get('/baser/api/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseOk();
        // ユーザーの有効チェック
        $users = $this->getTableLocator()->get('BaserCore.Users');
        $user = $users->get(1);
        $user->status = false;
        $users->save($user);
        $this->get('/baser/api/baser-core/users/index.json?token=' . $token['access_token']);
        $this->assertResponseCode(401);
    }

}
