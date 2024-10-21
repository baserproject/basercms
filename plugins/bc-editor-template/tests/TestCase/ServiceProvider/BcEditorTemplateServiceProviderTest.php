<?php

namespace BcEditorTemplate\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcEditorTemplate\Service\EditorTemplatesServiceInterface;
use BcEditorTemplate\ServiceProvider\BcEditorTemplateServiceProvider;
use Cake\Core\Container;

class BcEditorTemplateServiceProviderTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcEditorTemplateServiceProvider = new BcEditorTemplateServiceProvider();
    }

    public function tearDown(): void
    {
        unset($this->BcEditorTemplateServiceProvider);
        parent::tearDown();
    }

    /**
     * test services
     */
    public function test_services(): void
    {
        $container = new Container();
        $this->BcEditorTemplateServiceProvider->services($container);
        $this->assertTrue($container->has(EditorTemplatesServiceInterface::class));
    }
}
