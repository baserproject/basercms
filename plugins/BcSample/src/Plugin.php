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

namespace BcSample;

use BaserCore\BcPlugin;
use Cake\Core\Configure;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;

/**
 * plugin for BcSample
 */
class Plugin extends BcPlugin
{

    /**
     * Install
     *
     * @param array $options
     * @return bool
     */
    public function install($options = []) : bool
    {
        return parent::install($options);
    }

    public function routes($routes):void
    {
       // プラグインの管理画面用ルーティング
        $routes->prefix(
            'Admin',
            ['path' => '/baser' . Configure::read('BcApp.adminPrefix')],
            function(RouteBuilder $routes) {
                $routes->plugin(
                    'BcSample',
                    ['path' => '/bc-sample'],
                    function(RouteBuilder $routes) {
                        $routes->connect('', ['plugin' => 'BcSample', 'controller' => 'Spa', 'action' => 'index']);
                        $routes->fallbacks(InflectedRoute::class);
                    }
                );
            }
        );
        parent::routes($routes);
    }

}
