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

namespace BaserCore\ServiceProvider;

use BaserCore\Service\Admin\SiteConfigManageService;
use BaserCore\Service\Admin\SiteConfigManageServiceInterface;
use BaserCore\Service\Front\SiteFrontService;
use BaserCore\Service\Front\SiteFrontServiceInterface;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\SitesService;
use BaserCore\Service\UsersService;
use BaserCore\Service\DblogsService;
use BaserCore\Service\PluginsService;
use BaserCore\Service\ContentsService;
use BaserCore\Service\UserGroupsService;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Service\SiteConfigsService;
use BaserCore\Service\Admin\SiteManageService;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Service\ContentsServiceInterface;
use BaserCore\Service\Admin\PluginManageService;
use BaserCore\Service\Admin\ContentManageService;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Service\PermissionServiceInterface;
use BaserCore\Service\PermissionService;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\Admin\SiteManageServiceInterface;
use BaserCore\Service\Admin\PluginManageServiceInterface;
use BaserCore\Service\Admin\ContentManageServiceInterface;
use BaserCore\Service\Admin\ContentFolderManageService;
use BaserCore\Service\Admin\ContentFolderManageServiceInterface;
use BaserCore\Service\ContentFoldersService;
use BaserCore\Service\ContentFoldersServiceInterface;

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
        UsersServiceInterface::class,
        UserGroupsServiceInterface::class,
        PluginsServiceInterface::class,
        PluginManageServiceInterface::class,
        SitesServiceInterface::class,
        SiteManageServiceInterface::class,
        SiteFrontServiceInterface::class,
        SiteConfigsServiceInterface::class,
        SiteConfigManageServiceInterface::class,
        PermissionServiceInterface::class,
        DblogsServiceInterface::class,
        ContentManageServiceInterface::class,
        ContentsServiceInterface::class,
        ContentFoldersServiceInterface::class,
        ContentFolderManageServiceInterface::class,
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
        // Usersサービス
        $container->add(UsersServiceInterface::class, UsersService::class);
        // UserGroupsサービス
        $container->add(UserGroupsServiceInterface::class, UserGroupsService::class, true);
        // Pluginsサービス
        $container->add(PluginsServiceInterface::class, PluginsService::class, true);
        $container->add(PluginManageServiceInterface::class, PluginManageService::class, true);
        // Sites サービス
        $container->add(SitesServiceInterface::class, SitesService::class, true);
        $container->add(SiteManageServiceInterface::class, SiteManageService::class, true);
        $container->add(SiteFrontServiceInterface::class, SiteFrontService::class, true);
        // SiteConfigsサービス
        $container->add(SiteConfigsServiceInterface::class, SiteConfigsService::class, true);
        $container->add(SiteConfigManageServiceInterface::class, SiteConfigManageService::class, true);
        // Permissionsサービス
        $container->add(PermissionServiceInterface::class, PermissionService::class);
        // Dblogsサービス
        $container->add(DblogsServiceInterface::class, DblogsService::class, true);
        // Contentsサービス
        $container->add(ContentManageServiceInterface::class, ContentManageService::class, true);
        $container->add(ContentsServiceInterface::class, ContentsService::class, true);
        // ContentFoldersサービス
        $container->add(ContentFolderManageServiceInterface::class, ContentFolderManageService::class, true);
        $container->add(ContentFoldersServiceInterface::class, ContentFoldersService::class, true);

    }

}
