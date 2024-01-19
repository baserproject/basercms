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

namespace BcWidgetArea\ServiceProvider;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcWidgetArea\Service\Admin\WidgetAreasAdminService;
use BcWidgetArea\Service\Admin\WidgetAreasAdminServiceInterface;
use BcWidgetArea\Service\WidgetAreasService;
use BcWidgetArea\Service\WidgetAreasServiceInterface;
use Cake\Core\ServiceProvider;

/**
 * Class BcWidgetAreaServiceProvider
 */
class BcWidgetAreaServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected array $provides = [
        WidgetAreasServiceInterface::class,
        WidgetAreasAdminServiceInterface::class
    ];

    /**
     * Services
     * @param \Cake\Core\ContainerInterface $container
     * @checked
     * @noTodo
     * @unitTest
     */
    public function services($container): void
    {
        $container->defaultToShared(true);
        // Installations サービス
        $container->add(WidgetAreasServiceInterface::class, WidgetAreasService::class);
        $container->add(WidgetAreasAdminServiceInterface::class, WidgetAreasAdminService::class);
    }

}
