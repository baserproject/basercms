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

use ArrayObject;
use BaserCore\Model\Entity\User;
use Cake\Datasource\EntityInterface;
use Cake\Event\Event;
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
     * Before Marshal
     *
     * @param Event $event
     * @param ArrayObject $data
     * @param ArrayObject $options
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options) {
        if (!empty($data['password_1']) || !empty($data['password_2'])) {
            $data['password'] = $data['password_1'];
        }
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
            ->maxLength('name', 255, __d('baser', 'アカウント名は255文字以内で入力してください。'))
            ->notEmptyString('name', __d('baser', 'アカウント名を入力してください。'))
            ->add('name', [
                'nameUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser', '既に登録のあるアカウント名です。')
            ]])
            ->add('name', [
                'nameAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus'],
                    'provider' => 'bc',
                    'message' => __d('baser', 'アカウント名は半角英数字とハイフン、アンダースコアのみで入力してください。')
            ]]);
        $validator
            ->scalar('real_name_1')
            ->maxLength('real_name_1', 50, __d('baser', '名前[姓]は50文字以内で入力してください。'))
            ->notEmptyString('real_name_1', __d('baser', '名前[姓]を入力してください。'));
        $validator
            ->scalar('real_name_2')
            ->maxLength('real_name_2', 50, __d('baser', '名前[名]は50文字以内で入力してください。'))
            ->allowEmptyString('real_name_2');
        $validator
            ->scalar('password')
            ->minLength('password', 6, __d('baser', 'パスワードは6文字以上で入力してください。'))
            ->maxLength('password', 255, __d('baser', 'パスワードは255文字以内で入力してください。'))
            ->add('password', [
                'passwordAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus', ' \.:\/\(\)#,@\[\]\+=&;\{\}!\$\*'],
                    'provider' => 'bc',
                    'message' => __d('baser', 'パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。')
            ]])
            ->add('password', [
                'passwordConfirm' => [
                    'rule' => ['confirm', ['password_1', 'password_2']],
                    'provider' => 'bc',
                    'message' => __d('baser', __d('baser', 'パスワードが同じものではありません。'))
            ]]);
        $validator
            ->email('email')
            ->notEmptyString('email');
        $validator
            ->scalar('nickname')
            ->maxLength('nickname', 255, __d('baser', 'ニックネームは255文字以内で入力してください。'))
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
