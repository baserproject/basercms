<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          https://cakephp.org CakePHP(tm) Project
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
