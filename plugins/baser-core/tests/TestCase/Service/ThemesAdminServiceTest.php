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

use BaserCore\Service\ThemesAdminService;
use BaserCore\Service\ThemesService;
use BaserCore\Test\Factory\ThemeFactory;

/**
 * ThemesAdminServiceTest
 * @property ThemesAdminService $ThemesAdminService;
 */
class ThemesAdminServiceTest extends \BaserCore\TestSuite\BcTestCase
{

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
        $themesService = new ThemesService();
        $rs = $this->ThemesAdminService->getViewVarsForIndex($themesService->get());
        $this->assertIsArray($rs['themes']);
        $this->assertNull($rs['currentTheme']);
        $this->assertIsArray($rs['defaultDataPatterns']);
    }

}
