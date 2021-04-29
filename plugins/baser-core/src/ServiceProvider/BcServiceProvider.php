<?php
namespace BaserCore\ServiceProvider;

use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use Cake\Core\ServiceProvider;

class BcServiceProvider extends ServiceProvider
{
    protected $provides = [
        UsersServiceInterface::class,
        UsersService::class
    ];

    public function services($container): void
    {
        $container->add(UsersServiceInterface::class, UsersService::class);
    }
}
