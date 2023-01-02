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

use BaserCore\Service\Admin\BcAdminAppService;
use BaserCore\Service\Admin\BcAdminAppServiceInterface;
use BaserCore\Service\Admin\BcAdminContentsService;
use BaserCore\Service\Admin\BcAdminContentsServiceInterface;
use BaserCore\Service\Admin\ContentFoldersAdminService;
use BaserCore\Service\Admin\ContentFoldersAdminServiceInterface;
use BaserCore\Service\Admin\ContentsAdminService;
use BaserCore\Service\Admin\ContentsAdminServiceInterface;
use BaserCore\Service\Admin\DashboardAdminService;
use BaserCore\Service\Admin\DashboardAdminServiceInterface;
use BaserCore\Service\Admin\PagesAdminService;
use BaserCore\Service\Admin\PagesAdminServiceInterface;
use BaserCore\Service\Admin\PermissionGroupsAdminService;
use BaserCore\Service\Admin\PermissionGroupsAdminServiceInterface;
use BaserCore\Service\Admin\PluginsAdminService;
use BaserCore\Service\Admin\PluginsAdminServiceInterface;
use BaserCore\Service\Admin\SiteConfigsAdminService;
use BaserCore\Service\Admin\SiteConfigsAdminServiceInterface;
use BaserCore\Service\Admin\SitesAdminService;
use BaserCore\Service\Admin\SitesAdminServiceInterface;
use BaserCore\Service\Admin\ThemesAdminService;
use BaserCore\Service\Admin\ThemesAdminServiceInterface;
use BaserCore\Service\Admin\UsersAdminService;
use BaserCore\Service\Admin\UsersAdminServiceInterface;
use BaserCore\Service\Admin\UtilitiesAdminService;
use BaserCore\Service\Admin\UtilitiesAdminServiceInterface;
use BaserCore\Service\AppService;
use BaserCore\Service\AppServiceInterface;
use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Service\ContentFoldersService;
use BaserCore\Service\ContentFoldersServiceInterface;
use BaserCore\Service\ContentsService;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\DblogsService;
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Service\Front\BcFrontContentsService;
use BaserCore\Service\Front\BcFrontContentsServiceInterface;
use BaserCore\Service\Front\ContentFoldersFrontService;
use BaserCore\Service\Front\ContentFoldersFrontServiceInterface;
use BaserCore\Service\Front\PagesFrontService;
use BaserCore\Service\Front\PagesFrontServiceInterface;
use BaserCore\Service\PagesService;
use BaserCore\Service\PagesServiceInterface;
use BaserCore\Service\PasswordRequestsService;
use BaserCore\Service\PasswordRequestsServiceInterface;
use BaserCore\Service\PermissionGroupsService;
use BaserCore\Service\PermissionGroupsServiceInterface;
use BaserCore\Service\PermissionsService;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\PluginsService;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Service\SiteConfigsService;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\SitesService;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\ThemesService;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Service\UserGroupsService;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Service\UtilitiesService;
use BaserCore\Service\UtilitiesServiceInterface;
use BcFavorite\Service\FavoritesService;
use BcFavorite\Service\FavoritesServiceInterface;
use BcSearchIndex\Service\SearchIndexesService;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

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
        FavoritesServiceInterface::class,
        AppServiceInterface::class,
        BcAdminAppServiceInterface::class,
        UsersServiceInterface::class,
        UsersAdminServiceInterface::class,
        UserGroupsServiceInterface::class,
        PluginsServiceInterface::class,
        PluginsAdminServiceInterface::class,
        SitesServiceInterface::class,
        SitesAdminServiceInterface::class,
        SiteConfigsServiceInterface::class,
        SiteConfigsAdminServiceInterface::class,
        PermissionsServiceInterface::class,
        DblogsServiceInterface::class,
        ContentsServiceInterface::class,
        ContentsAdminServiceInterface::class,
        ContentFoldersServiceInterface::class,
        ContentFoldersAdminServiceInterface::class,
        ContentFoldersFrontServiceInterface::class,
        PagesServiceInterface::class,
        PagesAdminServiceInterface::class,
        PagesFrontServiceInterface::class,
        SearchIndexesServiceInterface::class,
        DashboardAdminServiceInterface::class,
        ThemesServiceInterface::class,
        ThemesAdminServiceInterface::class,
        BcDatabaseServiceInterface::class,
        UtilitiesAdminServiceInterface::class,
        UtilitiesServiceInterface::class,
        BcFrontContentsServiceInterface::class,
        BcAdminContentsServiceInterface::class,
        PasswordRequestsServiceInterface::class,
        PermissionGroupsServiceInterface::class,
        PermissionGroupsAdminServiceInterface::class
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
        // Appサービス
        $container->add(AppServiceInterface::class, AppService::class);
        // BcAdminサービス
        $container->add(BcAdminAppServiceInterface::class, BcAdminAppService::class);
        // Usersサービス
        $container->add(UsersServiceInterface::class, UsersService::class);
        $container->add(UsersAdminServiceInterface::class, UsersAdminService::class);
        // UserGroupsサービス
        $container->add(UserGroupsServiceInterface::class, UserGroupsService::class);
        // Pluginsサービス
        $container->add(PluginsServiceInterface::class, PluginsService::class);
        $container->add(PluginsAdminServiceInterface::class, PluginsAdminService::class);
        // Sites サービス
        $container->add(SitesServiceInterface::class, SitesService::class);
        $container->add(SitesAdminServiceInterface::class, SitesAdminService::class);
        // SiteConfigsサービス
        $container->add(SiteConfigsServiceInterface::class, SiteConfigsService::class);
        $container->add(SiteConfigsAdminServiceInterface::class, SiteConfigsAdminService::class);
        // Permissionsサービス
        $container->add(PermissionsServiceInterface::class, PermissionsService::class);
        // PermissionGroupsサービス
        $container->add(PermissionGroupsServiceInterface::class, PermissionGroupsService::class);
        $container->add(PermissionGroupsAdminServiceInterface::class, PermissionGroupsAdminService::class);
        // Dblogsサービス
        $container->add(DblogsServiceInterface::class, DblogsService::class);
        // Favoriteサービス
        $container->add(FavoritesServiceInterface::class, FavoritesService::class);
        // Contentsサービス
        $container->add(ContentsServiceInterface::class, ContentsService::class);
        $container->add(ContentsAdminServiceInterface::class, ContentsAdminService::class);
        // ContentFoldersサービス
        $container->add(ContentFoldersServiceInterface::class, ContentFoldersService::class);
        $container->add(ContentFoldersAdminServiceInterface::class, ContentFoldersAdminService::class);
        $container->add(ContentFoldersFrontServiceInterface::class, ContentFoldersFrontService::class);
        // Pagesサービス
        $container->add(PagesServiceInterface::class, PagesService::class);
        $container->add(PagesAdminServiceInterface::class, PagesAdminService::class);
        $container->add(PagesFrontServiceInterface::class, PagesFrontService::class);
        // SearchIndexesサービス
        $container->add(SearchIndexesServiceInterface::class, SearchIndexesService::class);
        // Dashboardサービス
        $container->add(DashboardAdminServiceInterface::class, DashboardAdminService::class);
        // Themes サービス
        $container->add(ThemesServiceInterface::class, ThemesService::class);
        $container->add(ThemesAdminServiceInterface::class, ThemesAdminService::class);
        // BcDatabase サービス
        $container->add(BcDatabaseServiceInterface::class, BcDatabaseService::class);
        // Utilities サービス
        $container->add(UtilitiesServiceInterface::class, UtilitiesService::class);
        $container->add(UtilitiesAdminServiceInterface::class, UtilitiesAdminService::class);
        // BcFrontContents サービス
        $container->add(BcFrontContentsServiceInterface::class, BcFrontContentsService::class);
        // BcAdminContents サービス
        $container->add(BcAdminContentsServiceInterface::class, BcAdminContentsService::class);
        // PasswordRequests サービス
        $container->add(PasswordRequestsServiceInterface::class, PasswordRequestsService::class);
    }

}
