<?php

namespace BcFavorite\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BaserCore\Utility\BcUtil;
use BcFavorite\Service\FavoritesServiceInterface;
use Cake\Core\Container;
use Cake\Core\Plugin;

class BcFavoritePluginTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        BcUtil::includePluginClass('BcFavorite');
        $plugins = Plugin::getCollection();
        $this->Plugin = $plugins->create('BcFavorite');
        $plugins->add($this->Plugin);
    }

    public function tearDown(): void
    {
        unset($this->Plugin);
        parent::tearDown();
    }

    public function test_services(): void
    {
        $container = new Container();
        $this->Plugin->services($container);
        $this->assertTrue($container->has(FavoritesServiceInterface::class));
    }
}