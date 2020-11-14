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

namespace BaserCore\Model\Table;
use BaserCore\Model\Entity\UserGroup;
use Cake\ORM\Association\BelongsToMany;
use Cake\ORM\Behavior\TimestampBehavior as TimestampBehaviorAlias;
use Cake\Datasource\{EntityInterface, ResultSetInterface as ResultSetInterfaceAlias};
use Cake\ORM\Table;
use Cake\Validation\Validator;

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
class UserGroupsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('user_groups');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsToMany('Users', [
            'foreignKey' => 'user_group_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'users_user_groups',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50, 'ユーザーグループ名は50文字以内で入力してください。')
            ->notEmptyString('name', 'ユーザーグループ名を入力してください。')
            ->add('name', [
                'name_halfText' => [
                    'rule' => 'halfText',
                    'provider' => 'bc',
                    'message' => 'ユーザーグループ名は半角のみで入力してください。'
                ],
                'name_unique'  => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => '既に登録のあるユーザーグループ名です。'
                ]
            ]);

        $validator
            ->scalar('title')
            ->maxLength('title', 50, '表示名は50文字以内で入力してください。')
            ->notEmptyString('title', '表示名を入力してください。');

        $validator
            ->scalar('auth_prefix')
            ->notEmptyString('auth_prefix', '認証プレフィックスを選択してください。');

        $validator
            ->boolean('use_admin_globalmenu')
            ->allowEmptyString('use_admin_globalmenu');

        $validator
            ->scalar('default_favorites')
            ->allowEmptyString('default_favorites');

        $validator
            ->boolean('use_move_contents')
            ->allowEmptyString('use_move_contents');

        return $validator;
    }
}
