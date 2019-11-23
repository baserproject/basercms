<?php
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
