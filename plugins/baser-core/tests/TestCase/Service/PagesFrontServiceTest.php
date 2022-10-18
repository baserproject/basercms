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

namespace BaserCore\Test\TestCase\Service;

use BaserCore\Controller\PagesController;
use BaserCore\Service\Front\PagesFrontService;
use BaserCore\Service\Front\PagesFrontServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * PagesFrontServiceTest
 */
class PagesFrontServiceTest extends BcTestCase
{

    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/SiteConfigs',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Permissions',
        'plugin.BaserCore.Factory/Pages',
    ];

    /**
     * Trait
     */
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * PagesFront
     * @var PagesFrontService
     */
    public $PagesFront;

    /**
     * Set up
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $this->PagesFront = $this->getService(PagesFrontServiceInterface::class);
    }

    /**
     * Tear down
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->PagesFront);
    }

    /**
     * test getViewVarsForDisplay
     */
    public function test_getViewVarsForView()
    {
        $vars = $this->PagesFront->getViewVarsForView(
            $this->PagesFront->get(2),
            $this->getRequest('/')
        );
        $this->assertArrayHasKey('page', $vars);
        $this->assertArrayHasKey('editLink', $vars);
    }

    /**
     * test setupPreviewForView
     */
    public function test_setupPreviewForView()
    {
        $request = $this->getRequest();
        $controller = new PagesController($request);

        $this->PagesFront->setupPreviewForView($controller);
        $this->assertArrayHasKey('page', $controller->viewBuilder()->getVars());
        $this->assertArrayHasKey('editLink', $controller->viewBuilder()->getVars());
    }
}
