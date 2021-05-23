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
use Cake\ORM\Query;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use Cake\Utility\Hash;

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
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(array $queryParams): Query
    {
        return parent::getIndex($queryParams);
    }

    /**
     * ユーザー登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        return parent::create($postData);
    }

    /**
     * ユーザー情報を更新する
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData)
    {
        return parent::update($target, $postData);
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
     * 整形されたユーザー名を取得する
     * @param EntityInterface $user
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUserName(EntityInterface $user)
    {
        return parent::getUserName($user);
    }

    /**
     * 管理ユーザーかどうか判定する
     * @param EntityInterface|User|null $user
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAdmin(?EntityInterface $user)
    {
        if (empty($user->user_groups)) {
            return false;
        }
        $userGroupId = Hash::extract($user->user_groups, '{n}.id');
        return in_array(Configure::read('BcApp.adminGroupId'), $userGroupId);
    }

    /**
     * 更新対象データがログインユーザー自身の更新かどうか
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isSelfUpdate(?int $id)
    {
        $loginUser = BcUtil::loginUser();
        return (!empty($id) && !empty($loginUser->id) && $loginUser->id === $id);
    }

    /**
     * 更新ができるかどうか
     * 自身の更新、または、管理者であること
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isEditable(?int $id)
    {
        if (empty($id)) {
            return false;
        } else {
            return ($this->isSelfUpdate($id) || $this->isAdmin(BcUtil::loginUser()));
        }
    }

    /**
     * 削除できるかどうか
     * 管理者であること、また、自身は削除できない
     * @param int $id
     * @return false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isDeletable(?int $id)
    {
        if (empty($id)) {
            return false;
        }
        return ($this->isAdmin(BcUtil::loginUser()) && !$this->isSelfUpdate($id));
    }

    /**
     * ユーザーグループ選択用のリスト
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUserGroupList()
    {
        return $this->Users->UserGroups->find('list', ['keyField' => 'id', 'valueField' => 'title'])->toArray();
    }

    /**
     * ログインユーザーが自身のユーザーグループを変更しようとしているかどうか
     * @param array $postData
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function willChangeSelfGroup(array $postData)
    {
        $loginUser = BcUtil::loginUser();
        if (empty($loginUser->user_groups)) {
            return false;
        }
        $loginGroupId = Hash::extract($loginUser->user_groups, '{n}.id');
        $postGroupId = array_map('intval', $postData['user_groups']['_ids']);
        return ($loginGroupId !== $postGroupId);
    }

}
