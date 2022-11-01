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
use BcUploader\Service\UploadCategoriesService;
use BcUploader\Service\UploadCategoriesServiceInterface;
use BcUploader\Service\UploadConfigsService;
use BcUploader\Service\UploadConfigsServiceInterface;
use BcUploader\Service\UploadFilesService;
use BcUploader\Service\UploadFilesServiceInterface;
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
        UploadCategoriesServiceInterface::class,
        UploadConfigsServiceInterface::class,
        UploadFilesServiceInterface::class
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
        // UploadCategories サービス
        $container->add(UploadCategoriesServiceInterface::class, UploadCategoriesService::class);
        // UploadConfigs サービス
        $container->add(UploadConfigsServiceInterface::class, UploadConfigsService::class);
        // UploadFiles サービス
        $container->add(UploadFilesServiceInterface::class, UploadFilesService::class);
    }

}
