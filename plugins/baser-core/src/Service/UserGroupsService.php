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
use Cake\Http\ServerRequest;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Query;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UserGroupsService
 * @package BaserCore\Service
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
     * @param ServerRequest $request
     * @return \Cake\Datasource\EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function create(ServerRequest $request)
    {
        $userGroup = $this->UserGroups->newEmptyEntity();
        $authPrefix = $request->getData('auth_prefix');
        $request = $request->withData('auth_prefix', !$authPrefix ? 'Admin' : implode(',', $authPrefix));
        $userGroup = $this->UserGroups->patchEntity($userGroup, $request->getData());
        return $this->UserGroups->save($userGroup);
    }

    /**
     * ユーザーグループ情報を更新する
     * @param EntityInterface $target
     * @param ServerRequest $request
     * @return EntityInterface|false
     * @checked
     * @noTodo
     * @unitTest
     */
    public function update(EntityInterface $target, ServerRequest $request)
    {
        $userGroup = $this->UserGroups->patchEntity($target, $request->getData());
        return $this->UserGroups->save($userGroup);
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
}
