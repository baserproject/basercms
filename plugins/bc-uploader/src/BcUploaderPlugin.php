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

namespace BcUploader;

use BaserCore\BcPlugin;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcUploader\ServiceProvider\BcUploaderServiceProvider;
use Cake\Core\ContainerInterface;
use Cake\Routing\Route\InflectedRoute;
use Cake\Routing\RouteBuilder;

/**
 * plugin for BcUploader
 */
class BcUploaderPlugin extends BcPlugin
{

    /**
     * services
     * @param ContainerInterface $container
     * @noTodo
     * @checked
     */
    public function services(ContainerInterface $container): void
    {
        $container->addServiceProvider(new BcUploaderServiceProvider());
    }

    /**
     * Routes
     * @param RouteBuilder $routes
     * @checked
     * @noTodo
     */
    public function routes(RouteBuilder $routes): void
    {
        $routes->connect('/files/uploads/*', [
            'plugin' => 'BcUploader',
            'controller' => 'UploaderFiles',
            'action' => 'view_limited_file'
        ], ['routeClass' => InflectedRoute::class]);
        parent::routes($routes);
    }

}
