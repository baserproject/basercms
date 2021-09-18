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
     */
    public function create(array $postData): EntityInterface;

    /**
     * 編集する
     * @param EntityInterface $target
     * @param array $data
     * @return EntityInterface
     */
    public function update(EntityInterface $target, array $data): EntityInterface;

    /**
     * 有効状態にする
     * @param int $id
     *
     * @return EntityInterface
     */
    public function publish(int $id): EntityInterface;

    /**
     * 無効状態にする
     * @param int $id
     *
     * @return EntityInterface
     */
    public function unpublish(int $id): EntityInterface;

    /**
     * 複製する
     * @param int $permissionId
     *
     * @return EntityInterface
     */
    public function copy(int $permissionId): EntityInterface;

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
     * 権限チェックを行う
     *
     * @param array $url
     * @param string $userGroupId
     * @return boolean
     */
    public function check($url, $userGroupId): bool;
    
    /**
     * 優先度を変更する
     * 
     * @param int $id
     * @param int $offset
     * @return bool
     */
    public function changeSort(int $id, int $offset, array $conditions = []): bool;
    
}
