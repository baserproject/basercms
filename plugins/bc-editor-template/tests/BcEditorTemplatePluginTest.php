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
        // CakePHP 5.2 では二重 add で例外となるため、既に読み込まれている場合はそれを利用する
        if ($plugins->has('BcEditorTemplate')) {
            $this->Plugin = $plugins->get('BcEditorTemplate');
        } else {
            $this->Plugin = $plugins->create('BcEditorTemplate');
            $plugins->add($this->Plugin);
        }
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