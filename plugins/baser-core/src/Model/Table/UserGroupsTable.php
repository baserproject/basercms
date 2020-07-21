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
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Class UserGroupsTable
 * @package BaserCore\Model\Table
 * @property \App\Model\Table\UsersTable&\Cake\ORM\Association\BelongsToMany $Users
 * @method \App\Model\Entity\UserGroup newEmptyEntity()
 * @method \App\Model\Entity\UserGroup newEntity(array $data, array $options = [])
 * @method \App\Model\Entity\UserGroup[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\UserGroup get($primaryKey, $options = [])
 * @method \App\Model\Entity\UserGroup findOrCreate($search, ?callable $callback = null, $options = [])
 * @method \App\Model\Entity\UserGroup patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\UserGroup[] patchEntities(iterable $entities, array $data, array $options = [])
 * @method \App\Model\Entity\UserGroup|false save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserGroup saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\UserGroup[]|\Cake\Datasource\ResultSetInterface|false saveMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserGroup[]|\Cake\Datasource\ResultSetInterface saveManyOrFail(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserGroup[]|\Cake\Datasource\ResultSetInterface|false deleteMany(iterable $entities, $options = [])
 * @method \App\Model\Entity\UserGroup[]|\Cake\Datasource\ResultSetInterface deleteManyOrFail(iterable $entities, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
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
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('name')
            ->maxLength('name', 50)
            ->allowEmptyString('name');

        $validator
            ->scalar('title')
            ->maxLength('title', 50)
            ->allowEmptyString('title');

        $validator
            ->scalar('auth_prefix')
            ->maxLength('auth_prefix', 20)
            ->allowEmptyString('auth_prefix');

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
