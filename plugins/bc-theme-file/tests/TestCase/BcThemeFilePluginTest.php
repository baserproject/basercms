<?php

namespace BcThemeFile\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BcThemeFile\BcThemeFilePlugin;
use BcThemeFile\Service\Admin\ThemeFilesAdminServiceInterface;
use BcThemeFile\Service\Admin\ThemeFoldersAdminServiceInterface;
use BcThemeFile\Service\ThemeFilesServiceInterface;
use BcThemeFile\Service\ThemeFoldersServiceInterface;
use Cake\Core\Container;

class BcThemeFilePluginTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcThemeFilePlugin = new BcThemeFilePlugin(['name' => 'BcThemeFile']);
    }

    public function tearDown(): void
    {
        unset($this->BcThemeFilePlugin);
        parent::tearDown();
    }

    /**
     * test services
     */
    public function test_services()
    {
        $container = new Container();
        $this->BcThemeFilePlugin->services($container);
        $this->assertTrue($container->has(ThemeFilesServiceInterface::class));
        $this->assertTrue($container->has(ThemeFilesAdminServiceInterface::class));
        $this->assertTrue($container->has(ThemeFoldersServiceInterface::class));
        $this->assertTrue($container->has(ThemeFoldersAdminServiceInterface::class));
    }
}