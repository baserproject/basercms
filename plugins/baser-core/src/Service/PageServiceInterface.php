<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Service;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Interface PageServiceInterface
 * @package BaserCore\Service
 */
interface PageServiceInterface
{

    /**
     * 固定ページを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * 固定ページをゴミ箱から取得する
     * @param int $id
     * @return EntityInterface|array
     */
    public function getTrash($id);

    // /**
    //  * 新規登録する
    //  * @param array $data
    //  * @return EntityInterface
    //  * @throws \Cake\ORM\Exception\PersistenceFailedException
    //  */
    // public function create(string $message): EntityInterface;

    // /**
    //  * DBログ一覧を取得
    //  * @param array $queryParams
    //  * @return Query
    //  */
    // public function getIndex(array $queryParams): Query;
}
