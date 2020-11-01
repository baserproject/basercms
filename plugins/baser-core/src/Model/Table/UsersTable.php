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

use BaserCore\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Association\BelongsTo;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Class UsersTable
 * @package BaserCore\Model\Table
 * @property BelongsTo $UserGroups
 * @method User get($primaryKey, $options = [])
 * @method User newEntity($data = null, array $options = [])
 * @method User[] newEntities(array $data, array $options = [])
 * @method User|bool save(EntityInterface $entity, $options = [])
 * @method User patchEntity(EntityInterface $entity, array $data, array $options = [])
 * @method User[] patchEntities($entities, array $data, array $options = [])
 * @method User findOrCreate($search, callable $callback = null, $options = [])
 * @mixin TimestampBehavior
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
        $this->belongsToMany('UserGroups', [
            'className' => 'BaserCore.UserGroups',
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'user_group_id',
            'joinTable' => 'users_user_groups',
            'joinType' => 'left'
        ]);
    }

    /**
     * Validation Default
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('name')
            ->maxLength('name', 255)
            ->notEmptyString('name');
        $validator
            ->scalar('password')
            ->maxLength('password', 255)
            ->notEmptyString('password');
        $validator
            ->scalar('real_name_1')
            ->maxLength('real_name_1', 50)
            ->notEmptyString('real_name_1');
        $validator
            ->scalar('real_name_2')
            ->maxLength('real_name_2', 50)
            ->allowEmptyString('real_name_2');
        $validator
            ->email('email')
            ->notEmptyString('email');
        $validator
            ->scalar('nickname')
            ->maxLength('nickname', 255)
            ->allowEmptyString('nickname');
        return $validator;
    }

    /**
     * Build Rules
     *
     * @param RulesChecker $rules
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email']));
        return $rules;
    }

    /**
     * 初期化されたエンティティを取得する
     */
    public function getNew()
    {
        return $this->newEntity([
                'user_groups' => [
                    '_ids' => [1]
        ]]);
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field フィールド名
     * @param array $options オプション
     * @return array コントロールソース
     */
	public function getControlSource($field, $options) {
		switch ($field) {
			case 'user_group_id':
				$controlSources['user_group_id'] = $this->UserGroups->find('list');
				break;
		}
		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return [];
		}
	}

	public function createWhere($query, $request): Query
    {
        $get = $request->getQuery();
        if(!empty($get['user_group_id'])) {
            $query->matching('UserGroups', function($q) use($get) {
                return $q->where(['UserGroups.id' => $get['user_group_id']]);
            });
        }
        return $query;
    }

}
