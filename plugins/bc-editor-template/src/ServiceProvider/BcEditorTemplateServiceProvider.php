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

namespace BcEditorTemplate\ServiceProvider;

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BcEditorTemplate\Service\EditorTemplatesService;
use BcEditorTemplate\Service\EditorTemplatesServiceInterface;
use Cake\Core\ServiceProvider;

/**
 * Class BcEditorTemplateServiceProvider
 */
class BcEditorTemplateServiceProvider extends ServiceProvider
{

    /**
     * Provides
     * @var string[]
     */
    protected $provides = [
        EditorTemplatesServiceInterface::class
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
        // EditorTemplates サービス
        $container->add(EditorTemplatesServiceInterface::class, EditorTemplatesService::class);
    }

}
