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

namespace BaserCore\Test\TestCase\Service\Front;

use BaserCore\Controller\ContentFoldersController;
use BaserCore\Service\Front\ContentFoldersFrontService;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentFoldersScenario;
use BaserCore\Test\Scenario\SmallSetContentsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * ContentFoldersFrontServiceTest
 *
 * @property ContentFoldersFrontService $ContentFoldersFrontService
 */
class ContentFoldersFrontServiceTest extends BcTestCase
{
    /**
     * Trait
     */
    use BcContainerTrait;
    use ScenarioAwareTrait;

    /**
     * Fixtures
     *
     * @var array
     */
    protected $fixtures = [
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->ContentFoldersFrontService = new ContentFoldersFrontService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ContentFoldersFrontService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForView
     */
    public function test_getViewVarsForView()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $rs = $this->ContentFoldersFrontService->getViewVarsForView($this->ContentFoldersFrontService->get(2), $this->getRequest('/'));
        $this->assertArrayHasKey('contentFolder', $rs);
        $this->assertArrayHasKey('children', $rs);
        $this->assertArrayHasKey('editLink', $rs);
    }

    /**
     * test setupPreviewForView
     */
    public function test_setupPreviewForView()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $request = $this->getRequest();
        $controller = new ContentFoldersController($request);
        $this->ContentFoldersFrontService->setupPreviewForView($controller);

        $this->assertEquals('default', $controller->viewBuilder()->getTemplate());

        $vars = $controller->viewBuilder()->getVars();
        $this->assertArrayHasKey('contentFolder', $vars);
        $this->assertArrayHasKey('children', $vars);
        $this->assertArrayHasKey('editLink', $vars);
        $this->assertEquals('edit', $vars['editLink']['action']);
        $this->assertTrue($vars['editLink']['admin']);
    }

    /**
     * test getTemplateForView
     */
    public function test_getTemplateForView()
    {
        $this->loadFixtureScenario(SmallSetContentFoldersScenario::class);
        //初期の状態
        $rs = $this->ContentFoldersFrontService->getTemplateForView($this->ContentFoldersFrontService->get(1));
        $this->assertEquals('default', $rs);

        // 対象に値が入っている場合
        $rs = $this->ContentFoldersFrontService->getTemplateForView($this->ContentFoldersFrontService->get(2));
        $this->assertEquals('test 1', $rs);

        //対象に値が入っておらず親を参照する場合
        $rs = $this->ContentFoldersFrontService->getTemplateForView($this->ContentFoldersFrontService->get(3));
        $this->assertEquals('test 1', $rs);
    }
}
