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

use Cake\Http\ServerRequest;
use Cake\Core\Exception\Exception;
use Psr\Http\Message\ResponseInterface;

/**
 * Interface UsersServiceInterface
 * @package BaserCore\Service
 */
interface UsersServiceInterface extends CrudBaseServiceInterface
{

    /**
     * ログイン
     * 
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @param $id
     * @return array|false
     * @checked
     * @unitTest
     * @noTodo
     */
    public function login(ServerRequest $request, ResponseInterface $response, $id);

    /**
     * ログアウト
     * 
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|false
     * @checked
     * @unitTest
     * @noTodo
     */
    public function logout(ServerRequest $request, ResponseInterface $response, $id);

    /**
     * 再ログイン
     * 
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|false
     * @checked
     * @unitTest
     * @noTodo
     */
    public function reLogin(ServerRequest $request, ResponseInterface $response);

    /**
     * ログイン状態の保存のキー送信
     * 
     * @param ResponseInterface
     * @param int $id
     * @return ResponseInterface
     * @see https://book.cakephp.org/4/ja/controllers/request-response.html#response-cookies
     * @checked
     * @unitTest
     * @noTodo
     */
    public function setCookieAutoLoginKey($response, $id): ResponseInterface;

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
     * @return ResponseInterface
     * @checked
     * @unitTest
     * @noTodo
     */
    public function checkAutoLogin(ServerRequest $request, ResponseInterface $response): ResponseInterface;

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
    public function loginToAgent(ServerRequest $request, ResponseInterface $response, $id, $referer = ''): bool;

    /**
     * 代理ログインから元のユーザーに戻る
     * 
     * @param ServerRequest $request
     * @param ResponseInterface $response
     * @return array|mixed|string
     * @throws Exception
     * @checked
     * @unitTest
     * @noTodo
     */
    public function returnLoginUserFromAgent(ServerRequest $request, ResponseInterface $response);

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
