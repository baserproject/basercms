<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

/**
 * Admin Prefix
 */
Router::plugin(
    'BaserCore',
    ['path' => '/baser'],
    function (RouteBuilder $routes) {
		$routes->prefix('admin', function (RouteBuilder $routes) {
			$routes->fallbacks(DashedRoute::class);
		});
		$routes->prefix('api', function (RouteBuilder $routes) {
			$routes->fallbacks(DashedRoute::class);
    		$routes->setExtensions(['json']);
		});
    }
);
