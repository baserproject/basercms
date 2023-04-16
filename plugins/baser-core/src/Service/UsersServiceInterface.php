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

use BaserCore\Model\Entity\User;
use Cake\Http\Response;
use Cake\Http\ServerRequest;
use Cake\Core\Exception\Exception;


/**
 * Interface UsersServiceInterface
 */
interface UsersServiceInterface extends CrudBaseServiceInterface
{

    /**
     * ログイン
     *
     * @param ServerRequest $request
     * @param Response $response
     * @param $id
     * @return array|false
     * @checked
     * @unitTest
     * @noTodo
     */
    public function login(ServerRequest $request, Response $response, $id);

    /**
     * ログアウト
     *
     * @param ServerRequest $request
     * @param Response $response
     * @return array|false
     * @checked
     * @unitTest
     * @noTodo
     */
    public function logout(ServerRequest $request, Response $response, $id);

    /**
     * 再ログイン
     *
     * @param ServerRequest $request
     * @param Response $response
     * @return array|false
     * @checked
     * @unitTest
     * @noTodo
     */
    public function reLogin(ServerRequest $request, Response $response);

    /**
     * ログイン状態の保存のキー送信
     *
     * @param Response
     * @param int $id
     * @return Response
     * @see https://book.cakephp.org/4/ja/controllers/request-response.html#response-cookies
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setCookieAutoLoginKey($response, $id): Response;

    /**
     * ログインキーを削除する
     *
     * @param int $id
     * @return int 削除行数
     * @checked
     * @unitTest
     * @noTodo
     */
    public function removeLoginKey($id);

    /**
     * ログイン状態の保存確認
     *
     * @return User|false
     * @checked
     * @unitTest
     * @noTodo
     */
    public function checkAutoLogin(ServerRequest $request, Response $response);

    /**
     * 代理ログインを行う
     *
     * @param ServerRequest $request
     * @param int $id
     * @param string $referer
     * @checked
     * @unitTest
     * @noTodo
     */
    public function loginToAgent(ServerRequest $request, Response $response, $id, $referer = ''): bool;

    /**
     * 代理ログインから元のユーザーに戻る
     *
     * @param ServerRequest $request
     * @param Response $response
     * @return array|mixed|string
     * @throws Exception
     * @checked
     * @unitTest
     * @noTodo
     */
    public function returnLoginUserFromAgent(ServerRequest $request, Response $response);

    /**
     * ログイン情報をリロードする
     *
     * @param ServerRequest $request
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function reload(ServerRequest $request);

    /**
     * ユーザーが有効化チェックする
     *
     * @param int $id
     * @return bool
     * @checked
     * @unitTest
     * @noTodo
     */
    public function isAvailable(int $id): bool;

}
