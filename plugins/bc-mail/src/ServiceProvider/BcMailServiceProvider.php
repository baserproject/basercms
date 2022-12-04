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

namespace BcMail\ServiceProvider;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcMail\Service\Admin\MailContentsAdminService;
use BcMail\Service\Admin\MailContentsAdminServiceInterface;
use BcMail\Service\Admin\MailFieldsAdminService;
use BcMail\Service\Admin\MailFieldsAdminServiceInterface;
use BcMail\Service\Admin\MailMessagesAdminService;
use BcMail\Service\Admin\MailMessagesAdminServiceInterface;
use BcMail\Service\Front\MailFrontService;
use BcMail\Service\Front\MailFrontServiceInterface;
use BcMail\Service\MailConfigsService;
use BcMail\Service\MailConfigsServiceInterface;
use BcMail\Service\MailContentsService;
use BcMail\Service\MailContentsServiceInterface;
use BcMail\Service\MailFieldsService;
use BcMail\Service\MailFieldsServiceInterface;
use BcMail\Service\MailMessagesService;
use BcMail\Service\MailMessagesServiceInterface;
use Cake\Core\ServiceProvider;

/**
 * Class BcMailServiceProvider
 */
class BcMailServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        MailConfigsServiceInterface::class,
        MailContentsServiceInterface::class,
        MailContentsAdminServiceInterface::class,
        MailFieldsServiceInterface::class,
        MailFieldsAdminServiceInterface::class,
        MailMessagesServiceInterface::class,
        MailMessagesAdminServiceInterface::class,
        MailFrontServiceInterface::class
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
        // MailConfigs サービス
        $container->add(MailConfigsServiceInterface::class, MailConfigsService::class);
        // MailContents サービス
        $container->add(MailContentsServiceInterface::class, MailContentsService::class);
        $container->add(MailContentsAdminServiceInterface::class, MailContentsAdminService::class);
        // MailFields サービス
        $container->add(MailFieldsServiceInterface::class, MailFieldsService::class);
        $container->add(MailFieldsAdminServiceInterface::class, MailFieldsAdminService::class);
        // MailMessages サービス
        $container->add(MailMessagesServiceInterface::class, MailMessagesService::class);
        $container->add(MailMessagesAdminServiceInterface::class, MailMessagesAdminService::class);
        // MailFront サービス
        $container->add(MailFrontServiceInterface::class, MailFrontService::class);
    }

}
