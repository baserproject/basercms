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

namespace BcThemeFile\ServiceProvider;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcThemeFile\Service\Admin\ThemeFilesAdminService;
use BcThemeFile\Service\Admin\ThemeFilesAdminServiceInterface;
use BcThemeFile\Service\Admin\ThemeFoldersAdminService;
use BcThemeFile\Service\Admin\ThemeFoldersAdminServiceInterface;
use BcThemeFile\Service\ThemeFilesService;
use BcThemeFile\Service\ThemeFilesServiceInterface;
use BcThemeFile\Service\ThemeFoldersService;
use BcThemeFile\Service\ThemeFoldersServiceInterface;
use Cake\Core\ServiceProvider;

/**
 * Class BcThemeFileServiceProvider
 */
class BcThemeFileServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        ThemeFilesServiceInterface::class,
        ThemeFilesAdminServiceInterface::class,
        ThemeFoldersServiceInterface::class,
        ThemeFoldersAdminServiceInterface::class
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
        // ThemeFiles サービス
        $container->add(ThemeFilesServiceInterface::class, ThemeFilesService::class);
        $container->add(ThemeFilesAdminServiceInterface::class, ThemeFilesAdminService::class);
        // ThemeFolders サービス
        $container->add(ThemeFoldersServiceInterface::class, ThemeFoldersService::class);
        $container->add(ThemeFoldersAdminServiceInterface::class, ThemeFoldersAdminService::class);
    }

}
