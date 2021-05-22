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
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
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

    /**
     * ログインユーザー自身の更新かどうか
     * @param ServerRequest $request
     * @return false
     */
    public function isSelfUpdate(ServerRequest $request)
    {
        switch($request->getParam('action')) {
            case 'add':
                return false;
            case 'edit':
                $loginUser = BcUtil::loginUser();
                return ($loginUser->id === $request->getData('id'));
        }
        return false;
    }

    /**
     * 更新ができるかどうか
     * @param ServerRequest $request
     * @return bool
     */
    public function isEditable(ServerRequest $request)
    {
        switch($request->getParam('action')) {
            case 'add':
                return true;
            case 'edit':
                $loginUser = BcUtil::loginUser();
                if(in_array(Configure::read('BcApp.adminGroupId'), $loginUser->user_group_id)) {
                    return true;
                }
                break;
        }
        return false;
    }

    /**
     * 削除できるかどうか
     * 自身が管理グループの場合削除できない
     * @param ServerRequest $request
     * @return false
     */
    public function isDeletable(ServerRequest $request)
    {
        switch($request->getParam('action')) {
            case 'add':
                return false;
            case 'edit':
                $loginUser = BcUtil::loginUser();
                if ($this->isSelfUpdate($request) && in_array(Configure::read('BcApp.adminGroupId'), $loginUser->user_group_id)) {
                    return false;
                } else {
                    return true;
                }
        }
        return false;
    }

    /**
     * ユーザーグループ選択用のリスト
     * @return array
     */
    public function getUserGroupList()
    {
        return $this->Users->UserGroups->find('list', ['keyField' => 'id', 'valueField' => 'title'])->toArray();
    }

    /**
     * ログインユーザーが自身の所属するユーザーグループを変更しようとしているかどうか
     * @param ServerRequest $request
     * @return bool
     */
    public function willChangeSelfGroup(ServerRequest $request)
    {
        $loginUser = BcUtil::loginUser();
        if ($this->isSelfUpdate($request) && $loginUser->user_group_id !== $request->getData('user_group_id')) {
            return true;
        } else {
            return false;
        }
    }

    public function reLogin() {
        // TODO 未実装
    }

}
