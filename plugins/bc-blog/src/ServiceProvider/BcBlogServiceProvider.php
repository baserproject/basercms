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

use BcBlog\Service\Admin\BlogCategoriesAdminService;
use BcBlog\Service\Admin\BlogCategoriesAdminServiceInterface;
use BcBlog\Service\Admin\BlogCommentsAdminService;
use BcBlog\Service\Admin\BlogCommentsAdminServiceInterface;
use BcBlog\Service\Admin\BlogContentsAdminService;
use BcBlog\Service\Admin\BlogContentsAdminServiceInterface;
use BcBlog\Service\Admin\BlogPostsAdminService;
use BcBlog\Service\Admin\BlogPostsAdminServiceInterface;
use BcBlog\Service\BlogCategoriesService;
use BcBlog\Service\BlogCategoriesServiceInterface;
use BcBlog\Service\BlogCommentsService;
use BcBlog\Service\BlogCommentsServiceInterface;
use BcBlog\Service\BlogContentsService;
use BcBlog\Service\BlogContentsServiceInterface;
use BcBlog\Service\BlogPostsService;
use BcBlog\Service\BlogPostsServiceInterface;
use BcBlog\Service\BlogTagsService;
use BcBlog\Service\BlogTagsServiceInterface;
use BcBlog\Service\Front\BlogFrontService;
use BcBlog\Service\Front\BlogFrontServiceInterface;
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
        BlogContentsServiceInterface::class,
        BlogContentsAdminServiceInterface::class,
        BlogFrontServiceInterface::class,
        BlogPostsServiceInterface::class,
        BlogCommentsServiceInterface::class,
        BlogTagsServiceInterface::class,
        BlogCommentsAdminServiceInterface::class,
        BlogPostsAdminServiceInterface::class
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
        // BlogCategoriesサービス
        $container->add(BlogCategoriesServiceInterface::class, BlogCategoriesService::class);
        $container->add(BlogCategoriesAdminServiceInterface::class, BlogCategoriesAdminService::class);
        // BlogContentsサービス
        $container->add(BlogContentsServiceInterface::class, BlogContentsService::class);
        $container->add(BlogContentsAdminServiceInterface::class, BlogContentsAdminService::class);
        // BlogPostsサービス
        $container->add(BlogPostsServiceInterface::class, BlogPostsService::class);
        $container->add(BlogPostsAdminServiceInterface::class, BlogPostsAdminService::class);
        // BlogCommentsサービス
        $container->add(BlogCommentsServiceInterface::class, BlogCommentsService::class);
        $container->add(BlogCommentsAdminServiceInterface::class, BlogCommentsAdminService::class);
        // BlogTagsサービス
        $container->add(BlogTagsServiceInterface::class, BlogTagsService::class);
        // Blogサービス
        $container->add(BlogFrontServiceInterface::class, BlogFrontService::class);
    }

}
