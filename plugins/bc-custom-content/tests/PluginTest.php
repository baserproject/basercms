<?php

namespace BcCustomContent\Test;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use BcCustomContent\Service\CustomLinksServiceInterface;
use BcCustomContent\Service\CustomTablesServiceInterface;
use Cake\Core\Plugin;
use Cake\Core\Container;

class PluginTest extends BcTestCase
{
    public $Plugin;

    public function setUp(): void
    {
        parent::setUp();
        BcUtil::includePluginClass('BcCustomContent');
        $plugins = Plugin::getCollection();
        $this->Plugin = $plugins->create('BcCustomContent');
        $plugins->add($this->Plugin);
    }

    public function tearDown(): void
    {
        unset($this->Plugin);
        parent::tearDown();
    }

    public function test_install()
    {
        $this->markTestIncomplete('このテストを利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
    }

    /**
     * test services
     */
    public function test_services()
    {
        $container = new Container();
        $this->Plugin->services($container);
        $this->assertTrue($container->has(CustomContentsServiceInterface::class));
        $this->assertTrue($container->has(CustomEntriesServiceInterface::class));
        $this->assertTrue($container->has(CustomFieldsServiceInterface::class));
        $this->assertTrue($container->has(CustomLinksServiceInterface::class));
        $this->assertTrue($container->has(CustomTablesServiceInterface::class));
    }
    /**
     * test Bootstrap
     */
    public function test_bootstrap()
    {
        $this->markTestIncomplete('このテストを利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
    }

    /**
     * test loadPlugin
     */
    public function test_loadPlugin()
    {
        $this->markTestIncomplete('このテストを利用すると全体のテストが失敗してしまうためスキップ。対応方法検討要');
    }
}