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

namespace BcInstaller;

use BaserCore\BcPlugin;
use BaserCore\Utility\BcUtil;
use BcInstaller\ServiceProvider\BcInstallerServiceProvider;
use Cake\Core\ContainerInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Routing\Route\InflectedRoute;

/**
 * Class Plugin
 * @package BcInstaller
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
        $container->addServiceProvider(new BcInstallerServiceProvider());
    }

    /**
     * ルーター設定
     *
     * @param \Cake\Routing\RouteBuilder $routes
     */
    public function routes($routes): void
    {
        /**
         * インストーラー
         */
        if (!BcUtil::isInstalled()) {
            $routes->connect('/', ['prefix' => 'Admin', 'plugin' => 'BcInstaller', 'controller' => 'Installations', 'action' => 'index']);
            $routes->connect('/install', ['prefix' => 'Admin', 'plugin' => 'BcInstaller', 'controller' => 'Installations', 'action' => 'index']);
            $routes->fallbacks(InflectedRoute::class);
            parent::routes($routes);
        }
    }

}
