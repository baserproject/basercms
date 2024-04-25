<?php

namespace BcThemeConfig\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BcThemeConfig\BcThemeConfigPlugin;
use BcThemeConfig\Service\ThemeConfigsServiceInterface;
use Cake\Core\Container;

class BcThemeConfigPluginTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcThemeConfigPlugin = new BcThemeConfigPlugin(['name' => 'BcThemeConfig']);
    }

    public function tearDown(): void
    {
        unset($this->BcThemeConfigPlugin);
        parent::tearDown();
    }

    /**
     * test services
     */

    public function test_services()
    {
        $container = new Container();
        $this->BcThemeConfigPlugin->services($container);
        $this->assertTrue($container->has(ThemeConfigsServiceInterface::class));
    }

    /**
     * test modifyDownloadDefaultData
     */
    public function test_modifyDownloadDefaultData()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }

}