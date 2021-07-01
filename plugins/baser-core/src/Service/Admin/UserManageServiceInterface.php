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

use BaserCore\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Exception;
use Psr\Http\Message\ResponseInterface;

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

    /**
     * ログイン
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @param $id
     * @return array|false
     */
    public function login(ServerRequest $request, ResponseInterface $response, $id);

    /**
     * ログアウト
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|false
     */
    public function logout(ServerRequest $request, ResponseInterface $response, $id);

    /**
     * 認証用のセッションキーを取得
     * @param string $prefix
     * @return false|string
     */
    public function getAuthSessionKey($prefix);

    /**
     * 再ログイン
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|false
     */
    public function reLogin(ServerRequest $request, ResponseInterface $response);

    /**
     * ログイン状態の保存のキー送信
     * @param ResponseInterface
     * @param int $id
     * @return ResponseInterface
     */
    public function setCookieAutoLoginKey($response, $id): ResponseInterface;

    /**
     * ログインキーを削除する
     * @param int $id
     * @return int 削除行数
     */
    public function removeLoginKey($id);

    /**
     * ログイン状態の保存確認
     * @return ResponseInterface
     */
    public function checkAutoLogin(ServerRequest $request, ResponseInterface $response): ResponseInterface;

    /**
     * 代理ログインを行う
     * @param ServerRequest $request
     * @param int $id
     * @param string $referer
     */
    public function loginToAgent(ServerRequest $request, ResponseInterface $response, $id, $referer = '');

    /**
     * 代理ログインから元のユーザーに戻る
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|mixed|string
     * @throws Exception
     */
    public function returnLoginUserFromAgent(ServerRequest $request, ResponseInterface $response);

    /**
     * サイト全体の設定値を取得する
     * @param string $name
     * @return mixed
     */
    public function getSiteConfig($name);

}
