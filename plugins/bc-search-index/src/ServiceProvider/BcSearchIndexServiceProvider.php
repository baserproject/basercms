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

namespace BcSearchIndex\ServiceProvider;


use BcSearchIndex\Service\Admin\SearchIndexesAdminService;
use BcSearchIndex\Service\Admin\SearchIndexesAdminServiceInterface;
use BcSearchIndex\Service\Front\SearchIndexesFrontService;
use BcSearchIndex\Service\Front\SearchIndexesFrontServiceInterface;
use BcSearchIndex\Service\SearchIndexesService;
use BcSearchIndex\Service\SearchIndexesServiceInterface;
use Cake\Core\ServiceProvider;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class BcSearchIndexServiceProvider
 */
class BcSearchIndexServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        SearchIndexesServiceInterface::class,
        SearchIndexesAdminServiceInterface::class,
        SearchIndexesFrontServiceInterface::class
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
        // SearchIndexesサービス
        $container->add(SearchIndexesServiceInterface::class, SearchIndexesService::class);
        $container->add(SearchIndexesAdminServiceInterface::class, SearchIndexesAdminService::class);
        $container->add(SearchIndexesFrontServiceInterface::class, SearchIndexesFrontService::class);
    }

}
