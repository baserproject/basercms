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
use Cake\Http\ServerRequest;
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
     * @param array $paginateParams
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex(ServerRequest $request): Query
    {
        $queryParams = $request->getQueryParams();
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
     * @param ServerRequest $request
     * @return \Cake\Datasource\EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(ServerRequest $request)
    {
        $user = $this->Users->newEmptyEntity();
        $request = $request->withData('password', $request->getData('password_1'));
        $user = $this->Users->patchEntity($user, $request->getData(), ['validate' => 'new']);
        return $this->Users->save($user);
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
        $user = $this->Users->patchEntity($target, $request->getData());
        return $this->Users->save($user);
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
        if($user->isAdmin()) {
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

    /**
     * 管理ユーザーかどうか判定する
     * @param EntityInterface|User $user
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function isAdmin(EntityInterface $user)
    {
        if ($user->user_groups) {
            foreach($user->user_groups as $group) {
                if($group->id === Configure::read('BcApp.adminGroupId')) {
                    return true;
                }
            }
        }
        return false;
    }

}
