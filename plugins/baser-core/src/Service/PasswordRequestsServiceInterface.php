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

use BaserCore\Model\Entity\PasswordRequest;
use Cake\Datasource\EntityInterface;

/**
 * PasswordRequestsServiceInterface
 */
interface PasswordRequestsServiceInterface
{

    /**
     * 新規データを取得する
     *
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface;

    /**
     * 単一データを取得する
     *
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface;

    /**
     * 更新する
     *
     * @param EntityInterface|PasswordRequest $entity
     * @param array $postData
     * @return array|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update($entity, $postData): ?array;

    /**
     * 有効なパスワード変更情報を取得する
     *
     * @param [type] $requestKey
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getEnableRequestData($requestKey): ?EntityInterface;

    /**
     * パスワードを変更する
     *
     * @param EntityInterface|PasswordRequest $passwordRequest
     * @param array $postData
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function updatePassword($passwordRequest, $postData): ?EntityInterface;

}
