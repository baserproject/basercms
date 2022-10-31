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

namespace BaserCore\Test\TestCase\Service\Admin;

use BaserCore\Service\Admin\BcAdminContentsServiceInterface;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Test\Scenario\InitAppScenario;
use BaserCore\Test\Scenario\SmallSetContentsScenario;
use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcContainerTrait;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * Class BcAdminContentsServiceTest
 */
class BcAdminContentsServiceTest extends BcTestCase
{

    /**
     * Trait
     */
    use BcContainerTrait;
    use ScenarioAwareTrait;

    public $fixtures = [
        'plugin.BaserCore.Factory/Sites',
        'plugin.BaserCore.Factory/Users',
        'plugin.BaserCore.Factory/UsersUserGroups',
        'plugin.BaserCore.Factory/UserGroups',
        'plugin.BaserCore.Factory/Contents',
        'plugin.BaserCore.Factory/ContentFolders',
        'plugin.BaserCore.Factory/Pages',
    ];

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcAdmin = $this->getService(BcAdminContentsServiceInterface::class);
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcAdmin);
        parent::tearDown();
    }

    /**
     * test getViewVarsForEdit
     */
    public function test_getViewVarsForEdit()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loadFixtureScenario(SmallSetContentsScenario::class);
        $contentsService = $this->getService(ContentsServiceInterface::class);
        $vars = $this->BcAdmin->getViewVarsForEdit($contentsService->get(5));
        $this->assertInstanceOf('BaserCore\\Model\\Entity\\Content', $vars['content']);
        $this->assertIsBool($vars['related']);
        $this->assertIsInt($vars['currentSiteId']);
        $this->assertArrayHasKey('mainSiteId', $vars);
        $this->assertIsString($vars['publishLink']);
        $this->assertIsArray($vars['parentContents']);
        $this->assertIsString($vars['fullUrl']);
        $this->assertIsArray($vars['authorList']);
        $this->assertIsArray($vars['sites']);
        $this->assertIsArray($vars['relatedContents']);
        $this->assertArrayHasKey('mainSiteDisplayName', $vars);
        $this->assertIsArray($vars['layoutTemplates']);
    }

}
