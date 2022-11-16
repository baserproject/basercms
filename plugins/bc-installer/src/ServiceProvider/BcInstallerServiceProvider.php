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

namespace BcInstaller\ServiceProvider;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcInstaller\Service\Admin\InstallationsAdminService;
use BcInstaller\Service\Admin\InstallationsAdminServiceInterface;
use BcInstaller\Service\InstallationsService;
use BcInstaller\Service\InstallationsServiceInterface;
use Cake\Core\ServiceProvider;

/**
 * Class BcEditorTemplateServiceProvider
 */
class BcInstallerServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        InstallationsServiceInterface::class,
        InstallationsAdminServiceInterface::class
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
        // Installations サービス
        $container->add(InstallationsServiceInterface::class, InstallationsService::class);
        $container->add(InstallationsAdminServiceInterface::class, InstallationsAdminService::class);
    }

}
