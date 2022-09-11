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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Http\ServerRequest;

/**
 * UtilitiesServiceInterface
 */
interface UtilitiesServiceInterface
{

    /**
     * コンテンツツリーの構造をチェックする
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function verityContentsTree(): bool;

    /**
     * クレジットを取得する
     * @return mixed|null
     * @checked
     * @noTodo
     */
    public function getCredit();

}
