<?php

namespace BcFavorite\Test\TestCase\ServiceProvider;

use BaserCore\TestSuite\BcTestCase;
use BcFavorite\Service\FavoritesServiceInterface;
use BcFavorite\ServiceProvider\BcFavoriteServiceProvider;
use Cake\Core\Container;

class BcFavoriteServiceProviderTest extends BcTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->BcFavoriteServiceProvider = new BcFavoriteServiceProvider();
    }

    public function tearDown(): void
    {
        unset($this->BcFavoriteServiceProvider);
        parent::tearDown();
    }

    public function test_services()
    {
        $container = new Container();
        $this->BcFavoriteServiceProvider->services($container);
        $this->assertTrue($container->has(FavoritesServiceInterface::class));
    }
}