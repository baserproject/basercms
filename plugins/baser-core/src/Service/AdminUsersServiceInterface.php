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

/**
 * Interface AdminUsersServiceInterface
 */
interface AdminUsersServiceInterface
{

    /**
     * ログインユーザー自身のIDか確認
     * @param int $id
     * @return bool
     */
    public function isSelf(?int $id): bool;

    /**
     * 更新ができるかどうか
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEditable(?int $id): bool;

    /**
     * ユーザーグループが更新可能かどうか
     * @param $id
     * @return bool
     */
    public function isUserGroupEditable(?int $id): bool;

    /**
     * 削除ができるかどうか
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isDeletable(?int $id): bool;

    /**
     * 編集画面に必要なデータを取得する
     * @param int $id
     * @return array
     */
    public function getEditViewVars(?int $id): array;

    /**
     * 新規登録画面にに必要なデータを取得する
     * @return array
     */
    public function getAddViewVars(): array;

}
