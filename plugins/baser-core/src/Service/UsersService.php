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
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\ORM\Query;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UsersService
 * @package BaserCore\Service
 * @property UsersTable $Users
 */
class UsersService implements UsersServiceInterface
{

    /**
     * Users Table
     * @var \Cake\ORM\Table
     */
    public $Users;

    /**
     * UsersService constructor.
     */
    public function __construct()
    {
        $this->Users = TableRegistry::getTableLocator()->get('BaserCore.Users');
    }

    /**
     * ユーザーの新規データ用の初期値を含んだエンティティを取得する
     * @return User
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): User
    {
        return $this->Users->newEntity([
            'user_groups' => [
                '_ids' => [1]
            ]]);
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
        return $this->Users->get($id, [
            'contain' => ['UserGroups'],
        ]);
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
        $options = [];
        if (!empty($queryParams['num'])) {
            $options = ['limit' => $queryParams['num']];
        }
        $query = $this->Users->find('all', $options)->contain('UserGroups');
        if (!empty($queryParams['user_group_id'])) {
            $query->matching('UserGroups', function($q) use ($queryParams) {
                return $q->where(['UserGroups.id' => $queryParams['user_group_id']]);
            });
        }
        if (!empty($queryParams['name'])) {
            $query->where(['name LIKE' => '%' . $queryParams['name'] . '%']);
        }
        return $query;
    }

    /**
     * ユーザー登録
     * @param array $data
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $user = $this->Users->newEmptyEntity();
        $user = $this->Users->patchEntity($user, $postData, ['validate' => 'new']);
        return ($result = $this->Users->save($user))? $result : $user;
    }

    /**
     * ユーザー情報を更新する
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData)
    {
        $user = $this->Users->patchEntity($target, $postData);
        return ($result = $this->Users->save($target))? $result : $user;
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
        $user = $this->Users->get($id, ['contain' => ['UserGroups']]);
        if ($user->isAdmin()) {
            $count = $this->Users
                ->find('all', ['conditions' => ['UsersUserGroups.user_group_id' => Configure::read('BcApp.adminGroupId')]])
                ->join(['table' => 'users_user_groups',
                    'alias' => 'UsersUserGroups',
                    'type' => 'inner',
                    'conditions' => 'UsersUserGroups.user_id = Users.id'])
                ->count();
            if ($count === 1) {
                throw new Exception(__d('baser', '最後のシステム管理者は削除できません'));
            }
        }
        return $this->Users->delete($user);
    }

}
