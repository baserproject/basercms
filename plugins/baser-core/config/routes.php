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

use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;

/**
 * Admin Prefix
 */
Router::plugin(
    'BaserCore',
    ['path' =>  env('BC_BASER_CORE_PATH', '/baser')],
    function (RouteBuilder $routes) {
		$routes->prefix(
            'Admin',
            ['path' => env('BC_ADMIN_PREFIX', '/admin')],
            function (RouteBuilder $routes) {
                // CakePHPのデフォルトで /index が省略する仕様のため、URLを生成する際は、強制的に /index を付ける仕様に変更
                $routes->connect('/{controller}/index', [], ['routeClass' => InflectedRoute::class]);
			    $routes->fallbacks(InflectedRoute::class);
		    }
		);
		$routes->prefix('api', function (RouteBuilder $routes) {
			$routes->fallbacks(InflectedRoute::class);
    		$routes->setExtensions(['json']);
		});
    }
);
