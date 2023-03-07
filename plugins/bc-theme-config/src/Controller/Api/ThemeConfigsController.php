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

namespace BcThemeConfig\Controller\Api;

use BaserCore\Controller\Api\BcApiController;
use BcThemeConfig\Service\ThemeConfigsServiceInterface;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\ORM\Exception\PersistenceFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class ThemeConfigsController
 *
 * [API] テーマ設定コントローラー
 */
class ThemeConfigsController extends BcApiController
{

    /**
     * [API] 取得
     *
     * @param ThemeConfigsServiceInterface $service
     * @checked
     * @noTodo
     */
    public function view(ThemeConfigsServiceInterface $service)
    {
        //todo [API] 取得
    }

    /**
     * [API] 保存
     *
     * @param ThemeConfigsServiceInterface $service
     * @checked
     * @noTodo
     */
    public function edit(ThemeConfigsServiceInterface $service)
    {
        //todo [API] 保存
    }

}
