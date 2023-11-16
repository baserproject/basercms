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

namespace BcThemeFile\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\ServiceProvider\BcThemeFileServiceProvider;
use Cake\Core\Container;

/**
 * Class BcThemeFileServiceProviderTest
 * @property BcThemeFileServiceProvider $Provider
 */
class BcThemeFileServiceProviderTest extends BcTestCase
{
    /**
     * Set Up
     *
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->Provider = new BcThemeFileServiceProvider();
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->Provider);
        parent::tearDown();
    }

    /**
     * Test services
     */
    public function testServices()
    {
        $container = new Container();
        $this->Provider->services($container);
        $themeFilesService = $container->get('BcThemeFile\Service\ThemeFilesServiceInterface');
        $themeFilesAdminService = $container->get('BcThemeFile\Service\Admin\ThemeFilesAdminServiceInterface');
        $themeFoldersService = $container->get('BcThemeFile\Service\ThemeFoldersServiceInterface');
        $themeFoldersAdminService = $container->get('BcThemeFile\Service\Admin\ThemeFoldersAdminServiceInterface');

        $this->assertEquals('BcThemeFile\Service\ThemeFilesService', get_class($themeFilesService));
        $this->assertEquals('BcThemeFile\Service\Admin\ThemeFilesAdminService', get_class($themeFilesAdminService));

        $this->assertEquals('BcThemeFile\Service\ThemeFoldersService', get_class($themeFoldersService));
        $this->assertEquals('BcThemeFile\Service\Admin\ThemeFoldersAdminService', get_class($themeFoldersAdminService));

    }

}
