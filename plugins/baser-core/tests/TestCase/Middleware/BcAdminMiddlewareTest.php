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

namespace BaserCore\Test\TestCase\Middleware;

use BaserCore\Middleware\BcAdminMiddleware;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcAdminMiddlewareTest
 * @property BcAdminMiddleware $BcAdminMiddleware
 */
class BcAdminMiddlewareTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BcAdminMiddleware = new BcAdminMiddleware();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdminMiddleware);
        parent::tearDown();
    }

    /**
     * Process
     */
    public function testProcess(): void
    {
        $request = $this->getRequest('/baser/admin/?site_id=1');
        $request = $this->execPrivateMethod($this->BcAdminMiddleware, 'setCurrentSite', [$request]);
        $this->assertEquals(1, $request->getAttribute('currentSite')->id);
    }

}
