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

use BaserCore\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Interface UsersServiceInterface
 * @package BaserCore\Service
 */
interface UserManageServiceInterface
{

    /**
     * ユーザーを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * ユーザー一覧を取得
     * @param array $queryParams
     * @return Query
     */
    public function getIndex(array $queryParams): Query;

    /**
     * 新しいデータの初期値を取得する
     * @return EntityInterface
     */
    public function getNew(): User;

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
     * 整形されたユーザー名を取得する
     * @param EntityInterface $user
     * @return string
     */
    public function getUserName(EntityInterface $user);

    /**
     * 管理ユーザーかどうか判定する
     * @param EntityInterface|User $user
     * @return bool
     */
    public function isAdmin(EntityInterface $user);

    /**
     * 更新対象データがログインユーザー自身の更新かどうか
     * @param int $id
     * @return false
     */
    public function isSelfUpdate(int $id);

    /**
     * 更新ができるかどうか
     * @param int $id
     * @return bool
     */
    public function isEditable(int $id);

    /**
     * 削除できるかどうか
     * ログインユーザーが管理グループの場合、自身は削除できない
     * @param int $id
     * @return false
     */
    public function isDeletable(int $id);

    /**
     * ログインユーザーが自身のユーザーグループを変更しようとしているかどうか
     * @param array $postData
     * @return bool
     */
    public function willChangeSelfGroup(array $postData);

}
