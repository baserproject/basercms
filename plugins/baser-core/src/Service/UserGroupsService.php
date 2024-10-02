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
use BaserCore\Utility\BcContainerTrait;
use BaserCore\Utility\BcUtil;
use Cake\Core\Configure;
use Cake\ORM\Table;
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
     * Trait
     */
    use BcContainerTrait;

    /**
     * UserGroups Table
     * @var \Cake\ORM\Table|UserGroupsTable
     */
    public UserGroupsTable|Table $UserGroups;

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
        return $this->UserGroups->get($id, contain: ['Users']);
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
            'auth_prefix_settings' => '{"Admin":{"type":"2"},"Api":{"type":"2"}}'
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
        $options = array_merge([
            'finder' => 'all',
            'exclude_admin' => false,
            'order' => null
        ], $options);

        $query = $this->UserGroups->find($options['finder']);

        if($options['exclude_admin']) {
            $query->where(['id <>' => Configure::read('BcApp.adminGroupId')]);
        }

        if(!is_null($options['order'])) $query->orderBy($options['order']);

        return $query;
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
        if(!empty($postData['auth_prefix_settings'])) {
            $postData['auth_prefix_settings'] = json_encode($postData['auth_prefix_settings']);
        }
        $postData['auth_prefix'] = !empty($postData['auth_prefix'])? implode(',', $postData['auth_prefix']) : "";
        $userGroup = $this->UserGroups->newEmptyEntity();
        $userGroup = $this->UserGroups->patchEntity($userGroup, $postData);
        $userGroup = $this->UserGroups->saveOrFail($userGroup);
        /** @var PermissionGroupsService $permissionGroupsService */
        $permissionGroupsService = $this->getService(PermissionGroupsServiceInterface::class);
        $permissionGroupsService->buildByUserGroup($userGroup->id);
        return $userGroup;
    }

    /**
     * ユーザーグループ情報を更新する
     *
     * @param EntityInterface|UserGroup $target
     * @param array $postData
     * @return EntityInterface
     * @throws \Cake\ORM\Exception\PersistenceFailedException
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, array $postData): ?EntityInterface
    {
        if(!empty($postData['auth_prefix_settings'])) {
            $current = $target->getAuthPrefixSettingsArray();
            if($current) $postData = array_merge($current, $postData);
            $postData['auth_prefix_settings'] = json_encode($postData['auth_prefix_settings']);
        }
        $postData['auth_prefix'] = !empty($postData['auth_prefix'])? implode(',', $postData['auth_prefix']) : "";
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
        return $this->UserGroups->find('list', keyField: 'id', valueField: 'title')->toArray();
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field
     * @return array
     * @noTodo
     * @checked
     * @unitTest
     */
    public function getControlSource(string $field): array
    {
        if ($field === 'auth_prefix') {
            return BcUtil::getAuthPrefixList();
        }
        return [];
    }

}
