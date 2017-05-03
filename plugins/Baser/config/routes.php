<?php
use Cake\Routing\RouteBuilder;
use Cake\Routing\Router;
use Cake\Routing\Route\DashedRoute;

Router::plugin(
    'Baser',
    ['path' => '/baser'],
    function (RouteBuilder $routes) {
		$routes->prefix('admin', function ($routes) {
			$routes->fallbacks(DashedRoute::class);
		});
    }
);
//Router::plugin('Baser', function ($routes) {
//	$routes->prefix('admin', function ($routes) {
//		$routes->connect('/:controller');
//	});
//});
//
//Router::prefix('admin', function ($routes) {
//	$routes->plugin('Baser', function ($routes) {
//		$routes->connect('/:controller');
//	});
//});