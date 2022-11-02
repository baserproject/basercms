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

use Cake\Datasource\ResultSetInterface;

/**
 * Interface DblogsServiceInterface
 * @package BaserCore\Service
 */
interface DblogsServiceInterface extends CrudBaseServiceInterface
{

    /**
     * 最新のDBログ一覧を取得
     * 
     * @param int $limit
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getDblogs(int $limit): ResultSetInterface;

    /**
     * DBログをすべて削除
     * 
     * @return int
     * @checked
     * @noTodo
     * @unitTest
     */
    public function deleteAll(): int;

}
