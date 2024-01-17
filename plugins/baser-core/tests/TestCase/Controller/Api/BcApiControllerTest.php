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
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\LoginStoresScenario;
use BaserCore\Test\Scenario\SiteConfigsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use Cake\Event\Event;
use Cake\TestSuite\IntegrationTestTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

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
     * test Before Filter
     * @return void
     */
    public function testBeforeFilter()
    {
        // API ON
        $this->get('/baser/api/baser-core/contents/index.json');
        $this->assertResponseCode(200);
        // API OFF
        $_SERVER['USE_CORE_API'] = 'false';
        $this->get('/baser/api/baser-core/contents/index.json');
        $this->assertResponseCode(403);
        $_SERVER['USE_CORE_API'] = 'true';
    }

    /**
     * test getAccessToken
     */
    public function testGetAccessToken()
    {
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
