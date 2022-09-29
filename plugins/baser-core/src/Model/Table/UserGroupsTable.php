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

namespace BaserCore\Model\Table;

use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Model\Entity\UserGroup;
use Cake\Core\Configure;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Behavior\TimestampBehavior as TimestampBehaviorAlias;
use Cake\Datasource\{EntityInterface, ResultSetInterface as ResultSetInterfaceAlias};
use Cake\Validation\Validator;
use BaserCore\Model\Table\Exception\CopyFailedException;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class UserGroupsTable
 * @package BaserCore\Model\Table
 * @property UsersTable&BelongsToMany $Users
 * @method UserGroup newEmptyEntity()
 * @method UserGroup newEntity(array $data, array $options = [])
 * @method UserGroup[] newEntities(array $data, array $options = [])
 * @method UserGroup get($primaryKey, $options = [])
 * @method UserGroup findOrCreate($search, ?callable $callback = null, $options = [])
 * @method UserGroup patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method UserGroup[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method UserGroup|false save(EntityInterface $entity, $options = [])
 * @method UserGroup saveOrFail(EntityInterface $entity, $options = [])
 * @method UserGroup[]|ResultSetInterfaceAlias|false saveMany(iterable $entities, $options = [])
 * @method UserGroup[]|ResultSetInterfaceAlias saveManyOrFail(iterable $entities, $options = [])
 * @method UserGroup[]|ResultSetInterfaceAlias|false deleteMany(iterable $entities, $options = [])
 * @method UserGroup[]|ResultSetInterfaceAlias deleteManyOrFail(iterable $entities, $options = [])
 * @mixin TimestampBehaviorAlias
 * @uses UserGroupsTable
 */
class UserGroupsTable extends AppTable
{

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     * @checked
     * @noTodo
     * @unitTest
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('user_groups');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsToMany('Users', [
            'className' => 'BaserCore.Users',
            'foreignKey' => 'user_group_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'users_user_groups',
        ]);
        $this->hasMany('Permissions', [
            'className' => 'BaserCore.Permissions',
            'order' => 'id',
            'foreignKey' => 'user_group_id',
            'dependent' => true,
            'exclusive' => false,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50, __d('baser', 'ユーザーグループ名は50文字以内で入力してください。'))
            ->requirePresence('name', 'create', __d('baser', 'ユーザーグループ名を入力してください。'))
            ->notEmptyString('name', __d('baser', 'ユーザーグループ名を入力してください。'))
            ->add('name', [
                'name_halfText' => [
                    'rule' => 'halfText',
                    'provider' => 'bc',
                    'message' => __d('baser', 'ユーザーグループ名は半角のみで入力してください。')
                ],
                'name_unique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser', '既に登録のあるユーザーグループ名です。')
                ]
            ]);

        $validator
            ->scalar('title')
            ->maxLength('title', 50, __d('baser', '表示名は50文字以内で入力してください。'))
            ->requirePresence('title', 'create', __d('baser', '表示名を入力してください。'))
            ->notEmptyString('title', __d('baser', '表示名を入力してください。'));

        $validator
            ->scalar('auth_prefix')
            ->requirePresence('auth_prefix', 'create', __d('baser', '認証プレフィックスを選択してください。'))
            ->notEmptyString('auth_prefix', __d('baser', '認証プレフィックスを選択してください。'));

        $validator
            ->boolean('use_move_contents')
            ->allowEmptyString('use_move_contents');

        return $validator;
    }

    /**
     * ユーザーグループデータをコピーする
     *
     * @param int $id ユーザーグループID
     * @return EntityInterface|false
     * @throws CopyFailedException When copy failed.
     * @checked
     * @noTodo
     * @unitTest
     */
    public function copy($id)
    {
        if (is_numeric($id)) {
            $userGroup = $this->get($id);
        }

        $userGroup->name .= '_copy';
        $userGroup->title .= '_copy';

        unset($userGroup->id, $userGroup->created, $userGroup->modified);

        $entity = $this->newEntity($userGroup->toArray());
        if ($errors = $entity->getErrors()) {
            $exception = new CopyFailedException(__d('baser', '処理に失敗しました。'));
            $exception->setErrors($errors);
            throw $exception;
        }

        if ($result = $this->save($entity)) {
            $permissions = $this->Permissions->find()->where(['user_group_id' => $id])->order(['sort'])->all();
            if ($permissions) {
                foreach($permissions as $permission) {
                    $permission->user_group_id = $result->id;
                    $this->Permissions->copy(null, $permission->toArray());
                }
            }
            return $result;
        }
    }

    /**
     * 関連するユーザーを管理者グループに変更し保存する
     *
     * @param boolean $cascade
     * @return boolean
     */
    public function beforeDelete($cascade = true)
    {
        if (!empty($this->data['UserGroup']['id'])) {
            $id = $this->data['UserGroup']['id'];
            $this->User->unBindModel(['belongsTo' => ['UserGroup']]);
            $datas = $this->User->find('all', ['conditions' => ['User.user_group_id' => $id]]);
            if ($datas) {
                foreach($datas as $data) {
                    $data['User']['user_group_id'] = Configure::read('BcApp.adminGroupId');
                    $this->User->set($data);
                    if (!$this->User->save()) {
                        $cascade = false;
                    }
                }
            }
        }
        return $cascade;
    }

    /**
     * 認証プレフィックスを取得する
     *
     * @param int $id ユーザーグループID
     * @return    string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getAuthPrefix(int $id): ?string
    {
        $userGroup = $this->find()->where(['id' => $id])->first();
        if (isset($userGroup->auth_prefix)) {
            return $userGroup->auth_prefix;
        }
        return null;
    }

}
