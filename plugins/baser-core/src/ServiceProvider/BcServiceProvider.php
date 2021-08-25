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
use BaserCore\Service\BcAdminService;
use BaserCore\Service\BcAdminServiceInterface;
use BaserCore\Service\Front\SiteFrontService;
use BaserCore\Service\Front\SiteFrontServiceInterface;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use BaserCore\Service\SitesService;
use BaserCore\Service\UsersService;
use BaserCore\Service\DblogService;
use BaserCore\Service\PluginService;
use BaserCore\Service\ContentService;
use BaserCore\Service\UserGroupsService;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Service\DblogServiceInterface;
use BaserCore\Service\SiteConfigsService;
use BaserCore\Service\Admin\SiteManageService;
use BaserCore\Service\PluginServiceInterface;
use BaserCore\Service\ContentServiceInterface;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Service\PermissionServiceInterface;
use BaserCore\Service\PermissionService;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\Admin\SiteManageServiceInterface;;
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
        BcAdminServiceInterface::class,
        UsersServiceInterface::class,
        UserGroupsServiceInterface::class,
        PluginServiceInterface::class,
        PluginServiceInterface::class,
        SitesServiceInterface::class,
        SiteFrontServiceInterface::class,
        SiteConfigsServiceInterface::class,
        SiteConfigManageServiceInterface::class,
        PermissionServiceInterface::class,
        DblogServiceInterface::class,
        ContentServiceInterface::class,
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
        // BcAdminサービス
        $container->add(BcAdminServiceInterface::class, BcAdminService::class);
        // Usersサービス
        $container->add(UsersServiceInterface::class, UsersService::class);
        // UserGroupsサービス
        $container->add(UserGroupsServiceInterface::class, UserGroupsService::class, true);
        // Pluginsサービス
        $container->add(PluginServiceInterface::class, PluginService::class, true);
        // Sites サービス
        $container->add(SitesServiceInterface::class, SitesService::class, true);
        $container->add(SiteFrontServiceInterface::class, SiteFrontService::class, true);
        // SiteConfigsサービス
        $container->add(SiteConfigsServiceInterface::class, SiteConfigsService::class, true);
        $container->add(SiteConfigManageServiceInterface::class, SiteConfigManageService::class, true);
        // Permissionsサービス
        $container->add(PermissionServiceInterface::class, PermissionService::class);
        // Dblogsサービス
        $container->add(DblogServiceInterface::class, DblogService::class, true);
        // Contentsサービス
        $container->add(ContentServiceInterface::class, ContentService::class, true);
        // ContentFoldersサービス
        $container->add(ContentFolderManageServiceInterface::class, ContentFolderManageService::class, true);
        $container->add(ContentFoldersServiceInterface::class, ContentFoldersService::class, true);

    }

}
