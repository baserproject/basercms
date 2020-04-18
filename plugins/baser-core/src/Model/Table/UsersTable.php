<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS Users Community
 * @link          http://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Model\Table;

use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Users Model
 *
 * @property \Cake\ORM\Association\BelongsTo $UserGroups
 * @method \BaserCore\Model\Entity\User get($primaryKey, $options = [])
 * @method \BaserCore\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \BaserCore\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \BaserCore\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \BaserCore\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \BaserCore\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \BaserCore\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{

    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('UserGroups', [
            'foreignKey' => 'user_group_id',
            'className' => 'BaserCore.UserGroups'
        ]);
    }

    /**
     * Validation Default
     *
     * @param \Cake\Validation\Validator $validator
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', 'create');
        $validator
            ->allowEmptyString('name');
        $validator
            ->allowEmptyString('password');
        $validator
            ->allowEmptyString('real_name_1');
        $validator
            ->allowEmptyString('real_name_2');
        $validator
            ->email('email')
            ->allowEmptyString('email');
        $validator
            ->allowEmptyString('nickname');
        return $validator;
    }

    /**
     * Build Rules
     *
     * @param \Cake\ORM\RulesChecker $rules
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['user_group_id'], 'UserGroups'));
        return $rules;
    }
}
