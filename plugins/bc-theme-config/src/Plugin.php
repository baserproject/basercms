<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcThemeConfig;

use BaserCore\BcPlugin;
use BcThemeConfig\ServiceProvider\BcThemeConfigServiceProvider;
use Cake\Core\ContainerInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class Plugin
 * @package BcThemeConfig
 */
class Plugin extends BcPlugin
{

    /**
     * services
     * @param ContainerInterface $container
     * @noTodo
     * @checked
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcThemeConfigServiceProvider());
    }

}
