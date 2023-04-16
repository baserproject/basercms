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

namespace BcUploader\ServiceProvider;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcUploader\Service\Admin\UploaderFilesAdminService;
use BcUploader\Service\Admin\UploaderFilesAdminServiceInterface;
use BcUploader\Service\UploaderCategoriesService;
use BcUploader\Service\UploaderCategoriesServiceInterface;
use BcUploader\Service\UploaderConfigsService;
use BcUploader\Service\UploaderConfigsServiceInterface;
use BcUploader\Service\UploaderFilesService;
use BcUploader\Service\UploaderFilesServiceInterface;
use Cake\Core\ServiceProvider;

/**
 * Class BcUploaderServiceProvider
 */
class BcUploaderServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        UploaderCategoriesServiceInterface::class,
        UploaderConfigsServiceInterface::class,
        UploaderFilesServiceInterface::class,
        UploaderFilesAdminServiceInterface::class
    ];

    /**
     * Services
     * @param \Cake\Core\ContainerInterface $container
     * @checked
     * @noTodo
     */
    public function services($container): void
    {
        $container->defaultToShared(true);
        // UploaderCategories サービス
        $container->add(UploaderCategoriesServiceInterface::class, UploaderCategoriesService::class);
        // UploaderConfigs サービス
        $container->add(UploaderConfigsServiceInterface::class, UploaderConfigsService::class);
        // UploaderFiles サービス
        $container->add(UploaderFilesServiceInterface::class, UploaderFilesService::class);
        $container->add(UploaderFilesAdminServiceInterface::class, UploaderFilesAdminService::class);
    }

}
