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

namespace BaserCore\ServiceProvider;

use BaserCore\Service\PagesDisplayService;
use BaserCore\Service\PagesDisplayServiceInterface;
use BaserCore\Service\UsersAdminService;
use BaserCore\Service\UsersAdminServiceInterface;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\PageService;
use BaserCore\Service\SiteService;
use BaserCore\Service\UserService;
use BaserCore\Service\DblogService;
use BaserCore\Service\PluginService;
use BaserCore\Service\ContentService;
use BaserCore\Service\UserGroupService;
use BaserCore\Service\PermissionService;
use BaserCore\Service\SiteConfigService;
use BaserCore\Service\SearchIndexService;
use BaserCore\Service\ContentFolderService;
use BaserCore\Service\PageServiceInterface;
use BaserCore\Service\SiteServiceInterface;
use BaserCore\Service\UserServiceInterface;
use BaserCore\Service\DblogServiceInterface;
use BaserCore\Service\PluginServiceInterface;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Service\UserGroupServiceInterface;
use BaserCore\Service\PermissionServiceInterface;
use BaserCore\Service\SiteConfigServiceInterface;
use BaserCore\Service\SearchIndexServiceInterface;
use BaserCore\Service\ContentFolderServiceInterface;

/**
 * Class BcServiceProvider
 * @package BaserCore\ServiceProvider
 */
class BcServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        UserServiceInterface::class,
        UsersAdminServiceInterface::class,
        UserGroupServiceInterface::class,
        PluginServiceInterface::class,
        PluginServiceInterface::class,
        SiteServiceInterface::class,
        SiteConfigServiceInterface::class,
        PermissionServiceInterface::class,
        DblogServiceInterface::class,
        ContentServiceInterface::class,
        ContentFolderServiceInterface::class,
        PageServiceInterface::class,
        PagesDisplayServiceInterface::class,
        SearchIndexServiceInterface::class,
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
        // Usersサービス
        $container->add(UserServiceInterface::class, UserService::class);
        $container->add(UsersAdminServiceInterface::class, UsersAdminService::class);
        // UserGroupsサービス
        $container->add(UserGroupServiceInterface::class, UserGroupService::class);
        // Pluginsサービス
        $container->add(PluginServiceInterface::class, PluginService::class);
        // Sites サービス
        $container->add(SiteServiceInterface::class, SiteService::class);
        // SiteConfigsサービス
        $container->add(SiteConfigServiceInterface::class, SiteConfigService::class);
        // Permissionsサービス
        $container->add(PermissionServiceInterface::class, PermissionService::class);
        // Dblogsサービス
        $container->add(DblogServiceInterface::class, DblogService::class);
        // Contentsサービス
        $container->add(ContentServiceInterface::class, ContentService::class);
        // ContentFoldersサービス
        $container->add(ContentFolderServiceInterface::class, ContentFolderService::class);
        // Pagesサービス
        $container->add(PageServiceInterface::class, PageService::class);
        // PagesDisplayサービス
        $container->add(PagesDisplayServiceInterface::class, PagesDisplayService::class);
        // SearchIndexesサービス
        $container->add(SearchIndexServiceInterface::class, SearchIndexService::class);

    }

}
