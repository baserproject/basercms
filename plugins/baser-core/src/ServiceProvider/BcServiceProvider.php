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

use BaserCore\Service\Admin\PluginManageService;
use BaserCore\Service\Admin\SiteManageService;
use BaserCore\Service\Admin\SiteManageServiceInterface;
use BaserCore\Service\Admin\UserGroupManageService;
use BaserCore\Service\Admin\UserManageService;
use BaserCore\Service\Api\UserApiService;
use BaserCore\Service\Api\UserApiServiceInterface;
use BaserCore\Service\SiteConfigsMockService;
use BaserCore\Service\SiteConfigsServiceInterface;
use BaserCore\Service\Admin\UserManageServiceInterface;
use BaserCore\Service\SitesService;
use BaserCore\Service\SitesServiceInterface;
use BaserCore\Service\UsersService;
use BaserCore\Service\UsersServiceInterface;
use BaserCore\Service\Admin\UserGroupManageServiceInterface;
use BaserCore\Service\UserGroupsService;
use BaserCore\Service\UserGroupsServiceInterface;
use BaserCore\Service\PermissionsServiceInterface;
use BaserCore\Service\PermissionsService;
use BaserCore\Service\PluginsServiceInterface;
use BaserCore\Service\PluginsService;
use BaserCore\Service\DblogsServiceInterface;
use BaserCore\Service\DblogsService;
use BaserCore\Service\Admin\PluginManageServiceInterface;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
        UserManageServiceInterface::class,
        UserApiServiceInterface::class,
        UserGroupsServiceInterface::class,
        UserGroupManageServiceInterface::class,
        PluginsServiceInterface::class,
        PluginManageServiceInterface::class,
        SitesServiceInterface::class,
        SiteManageServiceInterface::class,
        SiteConfigsServiceInterface::class,
        PermissionsServiceInterface::class,
        DblogsServiceInterface::class,
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
        $container->add(UserManageServiceInterface::class, UserManageService::class);
        $container->add(UserApiServiceInterface::class, UserApiService::class);
        // UserGroupsサービス
        $container->add(UserGroupsServiceInterface::class, UserGroupsService::class, true);
        $container->add(UserGroupManageServiceInterface::class, UserGroupManageService::class, true);
        // Pluginsサービス
        $container->add(PluginsServiceInterface::class, PluginsService::class, true);
        $container->add(PluginManageServiceInterface::class, PluginManageService::class, true);
        // Sites サービス
        $container->add(SitesServiceInterface::class, SitesService::class, true);
        $container->add(SiteManageServiceInterface::class, SiteManageService::class, true);
        // SiteConfigsサービス
        // TODO 未実装のためモックを利用
        $container->add(SiteConfigsServiceInterface::class, SiteConfigsMockService::class, true);
        // Permissionsサービス
        $container->add(PermissionsServiceInterface::class, PermissionsService::class);
        // Dblogsサービス
        $container->add(DblogsServiceInterface::class, DblogsService::class, true);
    }

}
