<?php

namespace BcWidgetArea\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BcWidgetArea\BcWidgetAreaPlugin;
use BcWidgetArea\Service\Admin\WidgetAreasAdminServiceInterface;
use BcWidgetArea\Service\WidgetAreasServiceInterface;
use Cake\Core\Container;

class BcWidgetAreaPluginTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcWidgetAreaPlugin = new BcWidgetAreaPlugin(['name' => 'BcWidgetArea']);
    }

    public function tearDown(): void
    {
        unset($this->BcWidgetAreaPlugin);
        parent::tearDown();
    }

    public function test_services(): void
    {
        $container = new Container();
        $this->BcWidgetAreaPlugin->services($container);
        $this->assertTrue($container->has(WidgetAreasServiceInterface::class));
        $this->assertTrue($container->has(WidgetAreasAdminServiceInterface::class));
    }
}