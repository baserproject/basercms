<?php

namespace BcWidgetArea\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcWidgetArea\Service\Admin\WidgetAreasAdminServiceInterface;
use BcWidgetArea\Service\WidgetAreasServiceInterface;
use BcWidgetArea\ServiceProvider\BcWidgetAreaServiceProvider;
use Cake\Core\Container;

/**
 * Class SearchIndexesServiceTest
 * @property BcWidgetAreaServiceProvider $BcWidgetAreaServiceProvider
 */
class BcWidgetAreaServiceProviderTest extends BcTestCase
{

    /**
     * set up
     * @return void
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->BcWidgetAreaServiceProvider = new BcWidgetAreaServiceProvider();
    }

    /**
     * Tear Down
     * @return void
     */
    public function tearDown(): void
    {
        unset($this->BcWidgetAreaServiceProvider);
        parent::tearDown();
    }

    /**
     * test testServices
     */
    public function test_services()
    {
        $container = new Container();
        $this->BcWidgetAreaServiceProvider->services($container);
        $this->assertTrue($container->has(WidgetAreasServiceInterface::class));
        $this->assertTrue($container->has(WidgetAreasAdminServiceInterface::class));
    }
}
