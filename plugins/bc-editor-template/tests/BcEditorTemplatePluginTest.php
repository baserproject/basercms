<?php

namespace BcEditorTemplate\Test;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcEditorTemplate\Service\EditorTemplatesServiceInterface;
use Cake\Core\Container;
use Cake\Core\Plugin;

class BcEditorTemplatePluginTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        BcUtil::includePluginClass('BcEditorTemplate');
        $plugins = Plugin::getCollection();
        $this->Plugin = $plugins->create('BcEditorTemplate');
        $plugins->add($this->Plugin);
    }

    public function tearDown(): void
    {
        unset($this->Plugin);
        parent::tearDown();
    }

    /**
     * test services
     */
    public function test_services()
    {
        $container = new Container();
        $this->Plugin->services($container);
        $this->assertTrue($container->has(EditorTemplatesServiceInterface::class));
    }
}