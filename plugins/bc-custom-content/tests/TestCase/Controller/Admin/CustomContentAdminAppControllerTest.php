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

namespace BcCustomContent\Test\TestCase\Controller\Admin;

use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use BcCustomContent\Controller\Admin\CustomContentAdminAppController;
use Cake\Core\Configure;
use Cake\Event\Event;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * CustomContentAdminAppControllerTest
 */
class CustomContentAdminAppControllerTest extends BcTestCase
{

    /**
     * Trait
     */
    use ScenarioAwareTrait;
    use BcContainerTrait;


    /**
     * Test subject
     *
     * @var CustomContentAdminAppController
     */
    public $CustomContentAdminAppController;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $request = $this->getRequest('/baser/admin/bc-custom-content/custom_contents/');
        $this->loginAdmin($request);
        $this->CustomContentAdminAppController = new CustomContentAdminAppController($request);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        Configure::clear();
        parent::tearDown();
    }

    /**
     * test beforeRender
     */
    public function test_beforeRender()
    {
        $event = new Event('Controller.beforeRender', $this->CustomContentAdminAppController);
        $this->CustomContentAdminAppController->beforeRender($event);
        $this->assertEquals('BcCustomContent.CustomContentAdminApp', $this->CustomContentAdminAppController->viewBuilder()->getClassName());
    }
}
