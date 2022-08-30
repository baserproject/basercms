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
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Interface PermissionsServiceInterface
 */
interface PermissionsServiceInterface extends CrudBaseServiceInterface
{

    /**
     * 有効状態にする
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function publish(int $id): bool;

    /**
     * 無効状態にする
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function unpublish(int $id): bool;

    /**
     * 複製する
     * @param int $permissionId
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy(int $permissionId);

    /**
     * 許可・拒否を指定するメソッドのリストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getMethodList(): array;

    /**
     * 権限リストを取得
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAuthList(): array;

    /**
     * 権限チェックを行う
     *
     * @param string $url
     * @param array $userGroupId
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function check(string $url, array $userGroupId): bool;

    /**
     * 権限チェック対象を追加する
     *
     * @param string $url
     * @param bool $auth
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function addCheck(string $url, bool $auth);

    /**
     * 優先度を変更する
     *
     * @param int $id
     * @param int $offset
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function changeSort(int $id, int $offset, array $conditions = []): bool;

    /**
     * 一括処理
     * @param array $ids
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function batch($method, array $ids): bool;

    /**
     * IDを指定して名前リストを取得する
     * @param $ids
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNamesById($ids): array;

}
