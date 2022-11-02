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

use BaserCore\Model\Entity\UserGroup;
use BaserCore\Model\Table\UserGroupsTable;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UserGroupsService
 * @property UserGroupsTable $UserGroups
 */
class UserGroupsService implements UserGroupsServiceInterface
{
    /**
     * UserGroups Table
     * @var \Cake\ORM\Table
     */
    public $UserGroups;

    /**
     * UserGroupsService constructor.
     * 
     * @checked
     * @unitTest
     * @noTodo
     */
    public function __construct()
    {
        $this->UserGroups = TableRegistry::getTableLocator()->get('BaserCore.UserGroups');
    }

    /**
     * ユーザーグループを取得する
     * 
     * @param int $id
     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function get($id): EntityInterface
    {
        return $this->UserGroups->get($id, [
            'contain' => ['Users'],
        ]);
    }

    /**
     * ユーザーグループの新規データ用の初期値を含んだエンティティを取得する
     * 
     * @return UserGroup
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface
    {
        return $this->UserGroups->newEntity([
            'auth_prefix' => 'Admin',
        ], [
            'validate' => false,
        ]);
    }

    /**
     * ユーザーグループ全件取得する
     * 
     * @param array $options
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getIndex($options = []): Query
    {
        return $this->UserGroups->find('all', $options);
    }

    /**
     * ユーザーグループ登録
     * 
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData): ?EntityInterface
    {
        $postData['auth_prefix'] = !empty($postData['auth_prefix']) ? implode(',', $postData['auth_prefix']) : "Admin";
        $userGroup = $this->UserGroups->newEmptyEntity();
        $userGroup = $this->UserGroups->patchEntity($userGroup, $postData);
        return $this->UserGroups->saveOrFail($userGroup);
    }

    /**
     * ユーザーグループ情報を更新する
     * 
     * @param EntityInterface $target
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface
    {
        $postData['auth_prefix'] = !empty($postData['auth_prefix']) ? implode(',', $postData['auth_prefix']) : "Admin";
        $userGroup = $this->UserGroups->patchEntity($target, $postData);
        return $this->UserGroups->saveOrFail($userGroup);
    }

    /**
     * ユーザーグループ情報を削除する
     * 
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete(int $id): bool
    {
        $userGroup = $this->UserGroups->get($id);
        return $this->UserGroups->delete($userGroup);
    }

    /**
     * リストを取得する
     * 
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getList(): array
    {
        return $this->UserGroups->find('list', ['keyField' => 'id', 'valueField' => 'title'])->toArray();
    }

}
