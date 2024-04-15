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

namespace BcCustomContent\ServiceProvider;

use BcCustomContent\Service\Admin\CustomContentsAdminService;
use BcCustomContent\Service\Admin\CustomContentsAdminServiceInterface;
use BcCustomContent\Service\Admin\CustomEntriesAdminService;
use BcCustomContent\Service\Admin\CustomEntriesAdminServiceInterface;
use BcCustomContent\Service\Admin\CustomFieldsAdminService;
use BcCustomContent\Service\Admin\CustomFieldsAdminServiceInterface;
use BcCustomContent\Service\Admin\CustomTablesAdminService;
use BcCustomContent\Service\Admin\CustomTablesAdminServiceInterface;
use BcCustomContent\Service\Admin\CustomLinksAdminService;
use BcCustomContent\Service\Admin\CustomLinksAdminServiceInterface;
use BcCustomContent\Service\CustomContentsService;
use BcCustomContent\Service\CustomContentsServiceInterface;
use BcCustomContent\Service\CustomEntriesService;
use BcCustomContent\Service\CustomEntriesServiceInterface;
use BcCustomContent\Service\CustomFieldsService;
use BcCustomContent\Service\CustomFieldsServiceInterface;
use BcCustomContent\Service\CustomLinksService;
use BcCustomContent\Service\CustomLinksServiceInterface;
use BcCustomContent\Service\CustomTablesService;
use BcCustomContent\Service\CustomTablesServiceInterface;
use BcCustomContent\Service\Front\CustomContentFrontService;
use BcCustomContent\Service\Front\CustomContentFrontServiceInterface;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcCustomContentServiceProvider
 */
class BcCustomContentServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected array $provides = [
        CustomContentsServiceInterface::class,
        CustomTablesServiceInterface::class,
        CustomFieldsServiceInterface::class,
        CustomEntriesServiceInterface::class,
        CustomContentsAdminServiceInterface::class,
        CustomTablesAdminServiceInterface::class,
        CustomFieldsAdminServiceInterface::class,
        CustomEntriesAdminServiceInterface::class,
        CustomContentFrontServiceInterface::class,
        CustomLinksServiceInterface::class,
        CustomLinksAdminServiceInterface::class
    ];

    /**
     * Services
     *
     * サービスのインターフェイスとの紐付けをコンテナに追加する
     *
     * @param \Cake\Core\ContainerInterface $container
     * @checked
     * @noTodo
     * @unitTest
     */
    public function services($container): void
    {
        $container->defaultToShared(true);

        // CustomContentsService
        $container->add(CustomContentsServiceInterface::class, CustomContentsService::class);
        $container->add(CustomContentsAdminServiceInterface::class, CustomContentsAdminService::class);
        // CustomTablesService
        $container->add(CustomTablesServiceInterface::class, CustomTablesService::class);
        $container->add(CustomTablesAdminServiceInterface::class, CustomTablesAdminService::class);
        // CustomFieldsService
        $container->add(CustomFieldsServiceInterface::class, CustomFieldsService::class);
        $container->add(CustomFieldsAdminServiceInterface::class, CustomFieldsAdminService::class);
        // CustomEntriesService
        $container->add(CustomEntriesServiceInterface::class, CustomEntriesService::class);
        $container->add(CustomEntriesAdminServiceInterface::class, CustomEntriesAdminService::class);
        // CustomContentFrontService
        $container->add(CustomContentFrontServiceInterface::class, CustomContentFrontService::class);
        // CustomLinksService
        $container->add(CustomLinksServiceInterface::class, CustomLinksService::class);
        $container->add(CustomLinksAdminServiceInterface::class, CustomLinksAdminService::class);
    }

}
