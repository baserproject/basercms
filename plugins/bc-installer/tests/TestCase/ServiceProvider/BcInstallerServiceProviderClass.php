<?php

namespace BcInstaller\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcInstaller\Service\Admin\InstallationsAdminServiceInterface;
use BcInstaller\Service\InstallationsServiceInterface;
use BcInstaller\ServiceProvider\BcInstallerServiceProvider;
use Cake\Core\Container;

class BcInstallerServiceProviderClass extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcInstallerServiceProvider = new BcInstallerServiceProvider();
    }

    public function tearDown(): void
    {
        unset($this->BcInstallerServiceProvider);
        parent::tearDown();
    }

    public function test_services()
    {
        $container = new Container();
        $this->BcInstallerServiceProvider->services($container);
        $this->assertTrue($container->has(InstallationsServiceInterface::class));
        $this->assertTrue($container->has(InstallationsAdminServiceInterface::class));
    }
}