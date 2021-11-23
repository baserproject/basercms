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

use BaserCore\Model\Entity\UserGroup;
use BaserCore\Model\Table\UserGroupsTable;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UserGroupService
 * @package BaserCore\Service
 * @property UserGroupsTable $UserGroups
 */
class UserGroupService implements UserGroupServiceInterface
{
    /**
     * UserGroups Table
     * @var \Cake\ORM\Table
     */
    public $UserGroups;

    /**
     * UserGroupService constructor.
     */
    public function __construct()
    {
        $this->UserGroups = TableRegistry::getTableLocator()->get('BaserCore.UserGroups');
    }

    /**
     * ユーザーグループを取得する
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
     * @return UserGroup
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getNew(): EntityInterface
    {
        return $this->UserGroups->newEntity([
            'auth_prefix' => 'Admin',
        ]);
    }

    /**
     * ユーザーグループ全件取得する
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
     * @param array $postData
     * @return \Cake\Datasource\EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(array $postData)
    {
        $postData['auth_prefix'] = isset($postData['auth_prefix']) ? implode(',', $postData['auth_prefix']) : "Admin";
        $userGroup = $this->UserGroups->newEmptyEntity();
        $userGroup = $this->UserGroups->patchEntity($userGroup, $postData);
        return ($result = $this->UserGroups->save($userGroup))? $result : $userGroup;
    }

    /**
     * ユーザーグループ情報を更新する
     * @param EntityInterface $target
     * @param array $postData     * @return EntityInterface
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData)
    {
        $userGroup = $this->UserGroups->patchEntity($target, $postData);
        return ($result = $this->UserGroups->save($userGroup))? $result : $userGroup;
    }

    /**
     * ユーザーグループ情報を削除する
     * @param int $id
     * @return bool
     * @checked
     * @noTodo
     * @unitTest
     */
    public function delete($id)
    {
        $userGroup = $this->UserGroups->get($id);
        return $this->UserGroups->delete($userGroup);
    }

    /**
     * リストを取得する
     * @return array
     */
    public function getList(): array
    {
        return $this->UserGroups->find('list', ['keyField' => 'id', 'valueField' => 'title'])->toArray();
    }

}
