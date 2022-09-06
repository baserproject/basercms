<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Model\Table;

use ArrayObject;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Validation\Validator;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;
use Cake\ORM\TableRegistry;
use Cake\Datasource\EntityInterface;
use Cake\Datasource\ConnectionManager;
use BaserCore\Model\Table\Exception\CopyFailedException;

/**
 * Class PermissionTable
 *
 * @package BaserCore\Model\Table
 */
class PermissionsTable extends AppTable
{
    // 許可/拒否する対象メソッド
    const METHOD_LIST = [
        '*' => '全て',
        'GET' => '表示のみ',
        'POST' => '表示と編集',
    ];

    // 許可/拒否
    const AUTH_LIST = [
        0 => '拒否',
        1 => '許可',
    ];

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('permissions');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('UserGroups', [
            'className' => 'BaserCore.UserGroups',
            'foreignKey' => 'user_group_id',
            'targetForeignKey' => 'id',
            'joinTable' => 'user_groups',
            'joinType' => 'left'
        ]);
    }

    /**
     * permissionsTmp
     * ログインしているユーザーの拒否URLリスト
     * キャッシュ用
     * @var array $_targetPermissions
     */
    protected $_targetPermissions = [];

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 255, __d('baser', '設定名は255文字以内で入力してください。'))
            ->notEmptyString('name', __d('baser', '設定名を入力してください。'))
            ->requirePresence('name', true);
        $validator
            ->integer('user_group_id')
            ->notEmptyString('user_group_id', __d('baser', 'ユーザーグループを選択してください。'))
            ->requirePresence('user_group_id', true);
        $validator
            ->scalar('url')
            ->maxLength('url', 255, __d('baser', '設定URLは255文字以内で入力してください。'))
            ->notEmptyString('url', __d('baser', '設定URLを入力してください。'))
            ->requirePresence('user_group_id', true);
        return $validator;
    }

    /**
     * 実際には保存しないプレーンな新規データ表示用で使うバリデーション
     *
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationPlain($validator)
    {
        $collection = ConnectionManager::get('default')->getSchemaCollection();
        $columns = $collection->describe('permissions')->columns();
        $required = ['user_group_id'];

        $validator
            ->integer('user_group_id')
            ->notEmptyString('user_group_id', __d('baser', 'ユーザーグループを選択してください。'))
            ->requirePresence('user_group_id', true);

        foreach($columns as $column) {
            if (!in_array($column, $required)) {
                $validator->allowEmptyFor($column, Validator::EMPTY_STRING, Validator::WHEN_CREATE);
            }
        }
        return $validator;
    }

    /**
     * コントロールソースを取得する
     *
     * @param string フィールド名
     * @return array コントロールソース
     *
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource($field = null)
    {
        if ($field === 'user_group_id') {
            $userGroups = TableRegistry::getTableLocator()->get('BaserCore.UserGroups');
            $groupList = $userGroups->find('list', [
                'keyField' => 'id',
                'valueField' => 'title',
            ])->where([
                'UserGroups.id !=' => Configure::read('BcApp.adminGroupId')
            ]);
            return $groupList->toArray();
        }
        return false;
    }

    /**
     * beforeSave
     * urlの先頭に / を付けて絶対パスにする
     *
     * @param EventInterface $event
     * @param EntityInterface $entity
     * @param ArrayObject $options
     * @return boolean
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeSave(EventInterface $event, EntityInterface $entity, ArrayObject $options)
    {
        $data = $event->getData();
        if (preg_match('/^[^\/]/is', $data["entity"]->get("url"))) {
            $data["entity"]->set("url", '/' . $data["entity"]->get("url"));
        }
        return true;
    }

    /**
     * アクセス制限データをコピーする
     *
     * @param int $id
     * @param array $data
     * @return mixed $permission | false
     * TODO: copyをServiceに移行する
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy($id, $data = [])
    {
        if ($id) {
            $data = $this->get($id)->toArray();
        }
        if (empty($data['user_group_id']) || empty($data['name'])) {
            return false;
        }
        // $idが存在する場合アクセス制限コピー
        $idExists = $this->find()->where([
            'Permissions.user_group_id' => $data['user_group_id'],
            'Permissions.name' => $data['name'],
        ])->count();
        if ($idExists) {
            $data['name'] .= '_copy';
            return $this->copy(null, $data);
        }
        // $idがない場合新規でアクセス制限作成
        unset($data['id'], $data['modified'], $data['created']);
        // 新規の場合
        $data['no'] = $this->getMax('no', ['user_group_id' => $data['user_group_id']]) + 1;
        $data['sort'] = $this->getMax('sort', ['user_group_id' => $data['user_group_id']]) + 1;
        $permission = $this->newEntity($data);
        if ($errors = $permission->getErrors()) {
            $exception = new CopyFailedException(__d('baser', '処理に失敗しました。'));
            $exception->setErrors($errors);
            throw $exception;
        }
        return ($result = $this->save($permission))? $result : false;
    }

    /**
     * 検証対象者のPermissionsを設定する
     * @param array $userGroups
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setTargetPermissions(array $userGroups)
    {
        $permissions = $this->find('all')
            ->select(['url', 'auth', 'method', 'user_group_id'])
            ->where([
                'Permissions.user_group_id in' => $userGroups,
                'Permissions.status' => true,
            ])
            ->order([
                'user_group_id' => 'asc',
                'sort' => 'asc',
            ]);
        $permissionGroupList = [];
        foreach($userGroups as $groupId) {
            $permissionGroupList[$groupId] = [];
        }
        foreach($permissions as $permission) {
            $permissionGroupList[$permission->user_group_id][] = $permission;
        }
        $this->_targetPermissions = $permissionGroupList;
    }

    /**
     * 検証対象者のPermissionsを取得する
     *
     * @param array $userGroups
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getTargePermissions(array $userGroups): array
    {
        foreach($userGroups as $groupId) {
            if (!isset($this->_targetPermissions[$groupId])) {
                $this->setTargetPermissions($userGroups);
                break;
            }
        }
        return $this->_targetPermissions;
    }

}
