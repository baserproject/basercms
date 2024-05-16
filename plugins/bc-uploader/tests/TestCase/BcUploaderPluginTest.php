<?php

namespace BcUploader\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcUploader\Service\Admin\UploaderFilesAdminServiceInterface;
use BcUploader\Service\UploaderCategoriesServiceInterface;
use BcUploader\Service\UploaderConfigsServiceInterface;
use BcUploader\Service\UploaderFilesServiceInterface;
use Cake\Core\Container;
use Cake\Core\Plugin;

class BcUploaderPluginTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        BcUtil::includePluginClass('BcUploader');
        $plugins = Plugin::getCollection();
        $this->BcUploader = $plugins->create('BcUploader');
        $plugins->add($this->BcUploader);
    }

    public function tearDown(): void
    {
        unset($this->BcUploader);
        parent::tearDown();
    }

    public function test_services()
    {
        $container = new Container();
        $this->BcUploader->services($container);
        $this->assertTrue($container->has(UploaderCategoriesServiceInterface::class));
        $this->assertTrue($container->has(UploaderConfigsServiceInterface::class));
        $this->assertTrue($container->has(UploaderFilesServiceInterface::class));
        $this->assertTrue($container->has(UploaderFilesAdminServiceInterface::class));
    }

    public function test_routes()
    {
        $this->markTestIncomplete('このテストは、まだ実装されていません。');
    }
}