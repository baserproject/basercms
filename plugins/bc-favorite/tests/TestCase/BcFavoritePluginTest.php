<?php

namespace BcFavorite\Test\TestCase;

use BaserCore\TestSuite\BcTestCase;
use BcFavorite\BcFavoritePlugin;
use BcFavorite\Service\FavoritesServiceInterface;
use Cake\Core\Container;

class BcFavoritePluginTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcFavoritePlugin = new BcFavoritePlugin(['name' => 'BcFavorite']);
    }

    public function tearDown(): void
    {
        unset($this->BcFavoritePlugin);
        parent::tearDown();
    }

    public function test_services(): void
    {
        $container = new Container();
        $this->BcFavoritePlugin->services($container);
        $this->assertTrue($container->has(FavoritesServiceInterface::class));
    }
}