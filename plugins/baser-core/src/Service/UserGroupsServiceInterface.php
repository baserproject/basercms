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
 * Interface UserGroupsServiceInterface
 * @package BaserCore\Service
 */
interface UserGroupsServiceInterface
{
    /**
     * ユーザーグループを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * ユーザーグループの新規データ用のエンティティを取得する
     * @return EntityInterface
     */
    public function getNew(): EntityInterface;

    /**
     * ユーザーグループ全件取得する
     * @param array $options
     * @return Query
     */
    public function getIndex($options = []): Query;

    /**
     * 新規登録する
     * @param array $postData
     * @return EntityInterface
     */
    public function create(array $postData);

    /**
     * 編集する
     * @param EntityInterface $target
     * @param array $postData
     * @return mixed
     */
    public function update(EntityInterface $target, array $postData);

    /**
     * 削除する
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * リストを取得する
     * @return array
     */
    public function getList(): array;

}
