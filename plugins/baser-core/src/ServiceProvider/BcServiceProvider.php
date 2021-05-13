<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\ServiceProvider;

use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use Cake\Core\ServiceProvider;

/**
 * Class BcServiceProvider
 * @package BaserCore\ServiceProvider
 */
class BcServiceProvider extends ServiceProvider
{
    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        UsersServiceInterface::class
    ];

    /**
     * Services
     * @param \Cake\Core\ContainerInterface $container
     */
    public function services($container): void
    {
        $container->add(UsersServiceInterface::class, UsersService::class);
    }
}
