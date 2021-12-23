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

use BaserCore\Model\Entity\Permission;
use Cake\Http\ServerRequest;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Interface PermissionServiceInterface
 * @package BaserCore\Service
 */
interface PermissionServiceInterface
{

    /**
     * ユーザーを取得する
     * @param int $id
     * @return EntityInterface
     */
    public function get($id): EntityInterface;

    /**
     * パーミッション一覧を取得
     * @param array $queryParams
     * @return Query
     */
    public function getIndex(array $queryParams): Query;

    /**
     * 新しいデータの初期値を取得する
     * @param int $userGroupId
     * @return EntityInterface
     */
    public function getNew($userGroupId): EntityInterface;

    /**
     * 新規登録する
     * @param EntityInterface $permission
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     */
    public function create(array $postData): EntityInterface;

    /**
     * 編集する
     * @param EntityInterface $target
     * @param array $data
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     */
    public function update(EntityInterface $target, array $data): EntityInterface;

    /**
     * 有効状態にする
     * @param int $id
     *
     * @return bool
     */
    public function publish(int $id): bool;

    /**
     * 無効状態にする
     * @param int $id
     *
     * @return bool
     */
    public function unpublish(int $id): bool;

    /**
     * 複製する
     * @param int $permissionId
     *
     * @return EntityInterface|false
     */
    public function copy(int $permissionId);

    /**
     * 削除する
     * @param int $id
     * @return mixed
     */
    public function delete(int $id);

    /**
     * 許可・拒否を指定するメソッドのリストを取得
     * @return array
     */
    public function getMethodList(): array;

    /**
     * 権限リストを取得
     * @return array
     */
    public function getAuthList(): array;

    /**
     * 権限チェックを行う
     *
     * @param string $url
     * @param array $userGroupId
     * @return bool
     */
    public function check(string $url, array $userGroupId): bool;

    /**
     * 権限チェック対象を追加する
     *
     * @param string $url
     * @param bool $auth
     * @return void
     */
    public function addCheck(string $url, bool $auth);

    /**
     * 優先度を変更する
     *
     * @param int $id
     * @param int $offset
     * @return bool
     */
    public function changeSort(int $id, int $offset, array $conditions = []): bool;

}
