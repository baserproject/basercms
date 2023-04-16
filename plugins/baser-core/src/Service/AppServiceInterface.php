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
namespace BaserCore\Service;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * AppServiceInterface
 * 
 * @checked
 * @noTodo
 * @unitTest
 */
interface AppServiceInterface
{

    /**
     * アプリケーション全体で必要な変数を取得
     * @return array
     */
    public function getViewVarsForAll(): array;

}
