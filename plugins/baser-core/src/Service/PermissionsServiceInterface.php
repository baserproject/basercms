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

use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;

/**
 * Interface PermissionsServiceInterface
 */
interface PermissionsServiceInterface extends CrudBaseServiceInterface
{

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
