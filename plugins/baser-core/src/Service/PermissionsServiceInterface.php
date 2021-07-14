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
 * Interface PermissionsServiceInterface
 * @package BaserCore\Service
 */
interface PermissionsServiceInterface
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
     * @param int $userGroupId
     * @return EntityInterface
     */
    public function getNew($userGroupId): EntityInterface;

    /**
     * パーミッションを設定する
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     */
    public function set($data): EntityInterface;

    /**
     * 新規登録する
     * @param EntityInterface $permission
     * @return EntityInterface|false
     */
    public function create(EntityInterface $permission);

    /**
     * 編集する
     * @param EntityInterface $target
     * @param array $data
     * @return mixed
     */
    public function update(EntityInterface $target, array $data);
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
}
