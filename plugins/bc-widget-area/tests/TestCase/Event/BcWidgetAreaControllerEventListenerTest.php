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

namespace BcWidgetArea\Test\TestCase\Event;

use BaserCore\Controller\Admin\UsersController;
use BaserCore\Test\Scenario\InitAppScenario;
use BcWidgetArea\Event\BcWidgetAreaControllerEventListener;
use BaserCore\TestSuite\BcTestCase;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcWidgetAreaControllerEventListener
 *
 * @property  BcWidgetAreaControllerEventListener $BcWidgetAreaControllerEventListener
 */
class BcWidgetAreaControllerEventListenerTest extends BcTestCase
{
    /**
     * ScenarioAwareTrait
     */
    use ScenarioAwareTrait;

    /**
     * @var UsersController
     */
    public $UsersController;

    /**
     * set up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->BcWidgetAreaControllerEventListener = new BcWidgetAreaControllerEventListener();
        $this->UsersController = new UsersController($this->loginAdmin($this->getRequest()));
    }

    /**
     * tearDown
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcWidgetAreaControllerEventListener, $this->UsersController);
        parent::tearDown();
    }

    /**
     * startup
     */
    public function testStartup()
    {
        $this->BcWidgetAreaControllerEventListener->startup(new Event('Controller.startup', $this->UsersController));
        $vars = $this->UsersController->viewBuilder()->getVars();
        $this->assertArrayHasKey('currentWidgetAreaId', $vars);
    }
}
