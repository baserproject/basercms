<?php
namespace Baser\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\BelongsTo $UserGroups
 *
 * @method \Baser\Model\Entity\User get($primaryKey, $options = [])
 * @method \Baser\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \Baser\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \Baser\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Baser\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Baser\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \Baser\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('UserGroups', [
            'foreignKey' => 'user_group_id',
            'className' => 'Baser.UserGroups'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create');

        $validator
            ->allowEmpty('name');

        $validator
            ->allowEmpty('password');

        $validator
            ->allowEmpty('real_name_1');

        $validator
            ->allowEmpty('real_name_2');

        $validator
            ->email('email')
            ->allowEmpty('email');

        $validator
            ->allowEmpty('nickname');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['user_group_id'], 'UserGroups'));

        return $rules;
    }
}
