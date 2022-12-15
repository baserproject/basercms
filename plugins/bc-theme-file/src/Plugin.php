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

namespace BcThemeFile;

use BaserCore\BcPlugin;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcThemeFile\ServiceProvider\BcThemeFileServiceProvider;
use Cake\Core\ContainerInterface;

/**
 * Class Plugin
 * @package BcThemeFile
 */
class Plugin extends BcPlugin
{

    /**
     * services
     * @param ContainerInterface $container
     * @checked
     * @noTodo
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcThemeFileServiceProvider());
    }

}
