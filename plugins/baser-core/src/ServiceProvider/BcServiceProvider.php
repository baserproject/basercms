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

use BaserCore\Service\BcDatabaseService;
use BaserCore\Service\ThemesAdminService;
use BaserCore\Service\ThemesAdminServiceInterface;
use BaserCore\Service\ThemesService;
use BaserCore\Service\ThemesServiceInterface;
use BaserCore\Service\UtilitiesAdminService;
use BcFavorite\Service\FavoriteService;
use BcFavorite\Service\FavoriteServiceInterface;
use BaserCore\Service\AppService;
use BaserCore\Service\AppServiceInterface;
use BaserCore\Service\BcAdminAppService;
use BaserCore\Service\BcAdminAppServiceInterface;
use BaserCore\Service\ContentFoldersAdminService;
use BaserCore\Service\ContentFoldersAdminServiceInterface;
use BaserCore\Service\ContentsAdminService;
use BaserCore\Service\ContentsAdminServiceInterface;
use BaserCore\Service\DashboardAdminService;
use BaserCore\Service\DashboardAdminServiceInterface;
use BaserCore\Service\PagesDisplayService;
use BaserCore\Service\PagesDisplayServiceInterface;
use BaserCore\Service\PagesFrontService;
use BaserCore\Service\PagesFrontServiceInterface;
use BaserCore\Service\PluginsAdminService;
use BaserCore\Service\PluginsAdminServiceInterface;
use BaserCore\Service\SiteConfigsAdminService;
use BaserCore\Service\SiteConfigsAdminServiceInterface;
use BaserCore\Service\SitesAdminService;
use BaserCore\Service\SitesAdminServiceInterface;
use BaserCore\Service\UsersAdminService;
use BaserCore\Service\UsersAdminServiceInterface;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\PagesService;
use BaserCore\Service\SitesService;
use BaserCore\Service\UsersService;
use BaserCore\Service\DblogsService;
use BaserCore\Service\PluginsService;
use BaserCore\Service\ContentsService;
use BaserCore\Service\UserGroupsService;
use BaserCore\Service\PermissionsService;
use BaserCore\Service\SiteConfigsService;
use BcSearchIndex\Service\SearchIndexesService;
use BaserCore\Service\ContentFoldersService;
use BaserCore\Service\PagesServiceInterface;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\SiteConfigsServiceInterface;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use BaserCore\Service\ContentFoldersServiceInterface;
use BaserCore\Service\BcDatabaseServiceInterface;
use BaserCore\Service\UtilitiesAdminServiceInterface;

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
        FavoriteServiceInterface::class,
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
        PagesServiceInterface::class,
        PagesFrontServiceInterface::class,
        PagesDisplayServiceInterface::class,
        SearchIndexesServiceInterface::class,
        DashboardAdminServiceInterface::class,
        ThemesServiceInterface::class,
        ThemesAdminServiceInterface::class,
        BcDatabaseServiceInterface::class,
        UtilitiesAdminServiceInterface::class
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
        // Dblogsサービス
        $container->add(DblogsServiceInterface::class, DblogsService::class);
        // Favoriteサービス
        $container->add(FavoriteServiceInterface::class, FavoriteService::class);
        // Contentsサービス
        $container->add(ContentsServiceInterface::class, ContentsService::class);
        $container->add(ContentsAdminServiceInterface::class, ContentsAdminService::class);
        // ContentFoldersサービス
        $container->add(ContentFoldersServiceInterface::class, ContentFoldersService::class);
        $container->add(ContentFoldersAdminServiceInterface::class, ContentFoldersAdminService::class);
        // Pagesサービス
        $container->add(PagesServiceInterface::class, PagesService::class);
        $container->add(PagesFrontServiceInterface::class, PagesFrontService::class);
        // PagesDisplayサービス
        $container->add(PagesDisplayServiceInterface::class, PagesDisplayService::class);
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
        $container->add(UtilitiesAdminServiceInterface::class, UtilitiesAdminService::class);

    }

}
