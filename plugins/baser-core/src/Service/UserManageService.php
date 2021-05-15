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

use BaserCore\Model\Entity\User;
use BaserCore\Model\Table\UsersTable;
use Cake\Http\ServerRequest;
use Cake\ORM\Query;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UsersService
 * @package BaserCore\Service
 * @property UsersTable $Users
 */
class UserManageService extends UsersService implements UserManageServiceInterface
{

    /**
     * ユーザーの新規データ用の初期値を含んだエンティティを取得する
     * @return User
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): User
    {
        return parent::getNew();
    }

    /**
     * ユーザーを取得する
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return parent::get($id);
    }

    /**
     * ユーザー管理の一覧用のデータを取得
     * @param array $queryParams
     * @param array $paginateParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(ServerRequest $request): Query
    {
        return parent::getIndex($request);
    }

    /**
     * ユーザー登録
     * @param ServerRequest $request
     * @return \Cake\Datasource\EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(ServerRequest $request)
    {
        return parent::create($request);
    }

    /**
     * ユーザー情報を更新する
     * @param EntityInterface $target
     * @param ServerRequest $request
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, ServerRequest $request)
    {
        return parent::update($target, $request);
    }

    /**
     * ユーザー情報を削除する
     * 最後のシステム管理者でなければ削除
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id)
    {
        return parent::delete($id);
    }

}
