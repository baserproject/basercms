<?php

namespace BcUploader\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcUploader\Service\Admin\UploaderFilesAdminServiceInterface;
use BcUploader\Service\UploaderCategoriesServiceInterface;
use BcUploader\Service\UploaderConfigsServiceInterface;
use BcUploader\Service\UploaderFilesServiceInterface;
use BcUploader\ServiceProvider\BcUploaderServiceProvider;
use Cake\Core\Container;

class BcUploaderServiceProviderTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcUploaderServiceProvider = new BcUploaderServiceProvider();
    }

    public function tearDown(): void
    {
        unset($this->BcUploaderServiceProvider);
        parent::tearDown();
    }

    public function test_services()
    {
        $container = new Container();
        $this->BcUploaderServiceProvider->services($container);
        $this->assertTrue($container->has(UploaderCategoriesServiceInterface::class));
        $this->assertTrue($container->has(UploaderConfigsServiceInterface::class));
        $this->assertTrue($container->has(UploaderFilesServiceInterface::class));
        $this->assertTrue($container->has(UploaderFilesAdminServiceInterface::class));
    }
}