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

namespace BcBlog\ServiceProvider;

use BcBlog\Service\BlogCategoriesAdminService;
use BcBlog\Service\BlogCategoriesAdminServiceInterface;
use BcBlog\Service\BlogCategoriesService;
use BcBlog\Service\BlogCategoriesServiceInterface;
use BcBlog\Service\BlogContentsService;
use BcBlog\Service\BlogContentsServiceInterface;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcBlogServiceProvider
 */
class BcBlogServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        BlogCategoriesServiceInterface::class,
        BlogCategoriesAdminServiceInterface::class,
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
        // BlogCategoriesサービス
        $container->add(BlogCategoriesServiceInterface::class, BlogCategoriesService::class);
        $container->add(BlogCategoriesAdminServiceInterface::class, BlogCategoriesAdminService::class);
        // BlogContentsサービス
        $container->add(BlogContentsServiceInterface::class, BlogContentsService::class);
    }

}
