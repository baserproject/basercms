<?php
declare(strict_types=1);
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BcSpaSample;

use BaserCore\BcPlugin;
use Cake\Core\Configure;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * plugin for BcSpaSample
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
                    'BcSpaSample',
                    ['path' => '/bc-spa-sample'],
                    function(RouteBuilder $routes) {
                        $routes->connect('', ['plugin' => 'BcSpaSample', 'controller' => 'Spa', 'action' => 'index']);
                        $routes->fallbacks(InflectedRoute::class);
                    }
                );
            }
        );
        parent::routes($routes);
    }

}
