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

namespace BaserCore\Controller\Api;

use BaserCore\Service\UsersServiceInterface;
use Cake\Core\Exception\Exception;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UsersController
 *
 * https://localhost/baser/api/baser-core/users/action_name.json で呼び出す
 *
 * @package BaserCore\Controller\Api
 */
class UsersController extends BcApiController
{

    /**
     * ユーザー情報一覧取得
     * @param UsersServiceInterface $users
     * @checked
     * @noTodo
     * @unitTest
     */
    public function index(UsersServiceInterface $users)
    {
        $this->set([
            'users' => $this->paginate($users->getIndex($this->request))
        ]);
        $this->viewBuilder()->setOption('serialize', ['users']);
    }

    /**
     * ユーザー情報取得
     * @param UsersServiceInterface $users
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function view(UsersServiceInterface $users, $id)
    {
        $this->set([
            'user' => $users->get($id)
        ]);
        $this->viewBuilder()->setOption('serialize', ['user']);
    }

    /**
     * ユーザー情報登録
     * @param UsersServiceInterface $users
     * @checked
     * @noTodo
     * @unitTest
     */
    public function add(UsersServiceInterface $users)
    {
        if ($user = $users->create($this->request)) {
            $message = __d('baser', 'ユーザー「{0}」を追加しました。', $user->name);
        } else {
            $message = __d('baser', '入力エラーです。内容を修正してください。');
        }
        $this->set([
            'message' => $message,
            'user' => $user
        ]);
        $this->viewBuilder()->setOption('serialize', ['message', 'user']);
    }

    /**
     * ユーザー情報編集
     * @param UsersServiceInterface $users
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function edit(UsersServiceInterface $users, $id)
    {
        $user = $users->get($id);
        if ($this->request->is(['post', 'put'])) {
            if ($user = $users->update($user, $this->request)) {
                $message = __d('baser', 'ユーザー「{0}」を更新しました。', $user->name);
            } else {
                $message = __d('baser', '入力エラーです。内容を修正してください。');
            }
        }
        $this->set([
            'message' => $message,
            'user' => $user
        ]);
        $this->viewBuilder()->setOption('serialize', ['user', 'message']);
    }

    /**
     * ユーザー情報削除
     * @param UsersServiceInterface $users
     * @param $id
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(UsersServiceInterface $users, $id)
    {
        $user = $users->get($id);
        try {
            if ($users->delete($id)) {
                $message = __d('baser', 'ユーザー: {0} を削除しました。', $user->name);
            }
        } catch (Exception $e) {
            $message = __d('baser', 'データベース処理中にエラーが発生しました。') . $e->getMessage();
        }
        $this->set([
            'message' => $message,
            'user' => $user
        ]);
        $this->viewBuilder()->setOption('serialize', ['user', 'message']);
    }

}
