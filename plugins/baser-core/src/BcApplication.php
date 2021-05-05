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

namespace BaserCore;

use Cake\Core\Configure;
use Cake\Http\MiddlewareQueue;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;
use Cake\Http\BaseApplication;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcApplication
 * @package BaserCore
 */
class BcApplication extends BaseApplication
{
    /**
     * bootstrap
     * @checked
     * @noTodo
     */
    public function bootstrap(): void
    {
        parent::bootstrap();
        $this->addPlugin('BaserCore');
    }

    /**
     * Routes
     * @param \Cake\Routing\RouteBuilder $routes
     * @checked
     * @noTodo
     */
    public function routes($routes): void
    {
        $routes->prefix(
            'Admin',
            ['path' => Configure::read('BcApp.baserCorePrefix') . '/' . Configure::read('BcApp.adminPrefix')],
            function(RouteBuilder $routes) {
                $routes->connect('', ['controller' => 'Dashboard', 'action' => 'index']);
                // CakePHPのデフォルトで /index が省略する仕様のため、URLを生成する際は、強制的に /index を付ける仕様に変更
                $routes->connect('/{controller}/index', [], ['routeClass' => InflectedRoute::class]);
                $routes->fallbacks(InflectedRoute::class);
            }
        );
        parent::routes($routes);
    }

    /**
     * Setup the middleware queue your application will use.
     *
     * @param \Cake\Http\MiddlewareQueue $middlewareQueue The middleware queue to setup.
     * @return \Cake\Http\MiddlewareQueue The updated middleware queue.
     * @checked
     * @noTodo
     */
    public function middleware(MiddlewareQueue $middlewareQueue): MiddlewareQueue
    {
        return $middlewareQueue;
    }

}
