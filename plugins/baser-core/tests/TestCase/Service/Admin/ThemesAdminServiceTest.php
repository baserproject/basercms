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

use BaserCore\Service\Admin\ThemesAdminService;
use BaserCore\Service\ThemesService;
use BaserCore\Test\Scenario\InitAppScenario;
use CakephpFixtureFactories\Scenario\ScenarioAwareTrait;

/**
 * ThemesAdminServiceTest
 * @property ThemesAdminService $ThemesAdminService;
 */
class ThemesAdminServiceTest extends \BaserCore\TestSuite\BcTestCase
{
    /**
     * Trait
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
        $this->ThemesAdminService = new ThemesAdminService();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->ThemesAdminService);
        parent::tearDown();
    }

    /**
     * test getViewVarsForIndex
     * @return void
     */
    public function testGetViewVarsForIndex()
    {
        $this->loadFixtureScenario(InitAppScenario::class);
        $this->loginAdmin($this->getRequest('/baser/admin'));
        $themesService = new ThemesService();
        $rs = $this->ThemesAdminService->getViewVarsForIndex($themesService->getIndex());
        $this->assertIsArray($rs['themes']);
        $this->assertNotNull($rs['currentTheme']);
        $this->assertIsArray($rs['defaultDataPatterns']);
    }

}
