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

namespace BaserCore\Service\Admin;

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use BaserCore\Model\Entity\UserGroup;

/**
 * Interface UserGroupsServiceInterface
 * @package BaserCore\Service
 */
interface UserGroupManageServiceInterface
{
    /**
     * ユーザーグループを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * ユーザーグループの新規データ用の初期値を含んだエンティティを取得する
     * @return UserGroup
     */
    public function getNew(): UserGroup;
    /**
     * ユーザーグループ全件取得する
     * @param array $options
     * @return Query
     */
    public function getIndex($options = []): Query;

    /**
     * 新規登録する
     * @param array $postData
     * @return EntityInterface|false
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
     * 管理画面の一覧の表示件数を取得する
     * @return mixed
     */
    public function getAdminListNum(): int;

}
