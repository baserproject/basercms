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

namespace BaserCore\Test\TestCase\Controller\Api;

use Authentication\Authenticator\Result;
use BaserCore\Controller\Api\BcApiController;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Event\Event;
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
     * test getAccessToken
     */
    public function testGetAccessToken()
    {
        $this->loadFixtures('Users', 'UserGroups', 'UsersUserGroups');
        $user = $this->getService(UsersServiceInterface::class);
        $controller = new BcApiController($this->getRequest());
        $result = $controller->getAccessToken(new Result($user->get(1), Result::SUCCESS));
        $this->assertArrayHasKey('access_token', $result);
        $result = $controller->getAccessToken(new Result(null, Result::FAILURE_CREDENTIALS_INVALID));
        $this->assertEquals([], $result);
    }

    /**
     * test beforeRender
     */
    public function testBeforeRender()
    {
        $controller = new BcApiController($this->getRequest());
        $controller->beforeRender(new Event('beforeRender'));
        $this->assertEquals(JSON_UNESCAPED_UNICODE, $controller->viewBuilder()->getOption('jsonOptions'));
    }

}
