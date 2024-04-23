<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BcFavorite;

use BaserCore\BcPlugin;
use Cake\Core\ContainerInterface;
use BcFavorite\ServiceProvider\BcFavoriteServiceProvider;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * plugin for BcFavorite
 */
class BcFavoritePlugin extends BcPlugin
{
    /**
     * services
     * @param ContainerInterface $container
     * @checked
     * @noTodo
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcFavoriteServiceProvider());
    }
}
