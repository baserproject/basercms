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

use ArrayObject;
use Cake\ORM\Query;
use Cake\ORM\Table;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use BaserCore\Model\Entity\User;
use BaserCore\View\BcAdminAppView;
use Cake\ORM\Association\BelongsTo;
use Cake\Datasource\EntityInterface;
use Cake\ORM\Behavior\TimestampBehavior;
use BaserCore\Event\BcEventDispatcherTrait;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

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
     * Trait
     */
    use BcEventDispatcherTrait;

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
     * @checked
     * @noTodo
     * @unitTest
     */
    public function beforeMarshal(Event $event, ArrayObject $data, ArrayObject $options)
    {
        if (!empty($data['password_1']) || !empty($data['password_2'])) {
            $data['password'] = $data['password_1'];
        }
    }

    /**
     * beforeSave
     *
     * @param type $options
     * @return boolean
     */
    public function beforeSave($options = [])
    {
        // TODO ucmitz 暫定措置
        // >>>
        return true;
        // <<<

        if (isset($this->data[$this->getAlias()]['password'])) {
            App::uses('AuthComponent', 'Controller/Component');
            $this->data[$this->getAlias()]['password'] = AuthComponent::password($this->data[$this->getAlias()]['password']);
        }
        return true;
    }

    /**
     * After Marshal
     *
     * @param Event $event
     * @param User $user
     * @param ArrayObject $data
     * @param ArrayObject $options
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterMarshal(Event $event, User $user, ArrayObject $data, ArrayObject $options)
    {
        if ($user->getError('password')) {
        }
    }

    /**
     * afterSave
     *
     * @param boolean $created
     * @checked
     * @noTodo
     * @unitTest
     */
    public function afterSave(Event $event, EntityInterface $entity, ArrayObject $options)
    {
        // ユーザデータが変更された場合は自動ログインのデータを削除する
        $loginStores = TableRegistry::get('BaserCore.LoginStores');
        $loginStores->deleteAll([
            'user_id' => $entity->id
        ]);
    }

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
        $validator->setProvider('user', 'BaserCore\Model\Validation\UserValidation');

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');
        $validator
            ->scalar('name')
            ->allowEmptyString('name')
            ->maxLength('name', 255, __d('baser', 'アカウント名は255文字以内で入力してください。'))
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
            ->requirePresence('real_name_1', 'create', __d('baser', '名前[姓]を入力してください。'))
            ->notEmptyString('real_name_1', __d('baser', '名前[姓]を入力してください。'));
        $validator
            ->scalar('real_name_2')
            ->maxLength('real_name_2', 50, __d('baser', '名前[名]は50文字以内で入力してください。'))
            ->allowEmptyString('real_name_2');
        $validator
            ->scalar('nickname')
            ->maxLength('nickname', 255, __d('baser', 'ニックネームは255文字以内で入力してください。'))
            ->allowEmptyString('nickname');
        $validator
            ->requirePresence('user_groups', 'create', __d('baser', 'グループを選択してください。'))
            ->add('user_groups', [
                'userGroupsNotEmptyMultiple' => [
                    'rule' => 'notEmptyMultiple',
                    'provider' => 'bc',
                    'message' => __d('baser', 'グループを選択してください。')
                ]
            ])
            ->add('user_groups', [
                'willChangeSelfGroup' => [
                    'rule' => 'willChangeSelfGroup',
                    'provider' => 'user',
                    'on' => 'update',
                    'message' => __d('baser', '自分のアカウントのグループは変更できません。')
                ]
            ]);
        $validator
            ->requirePresence('email', 'create', __d('baser', 'Eメールを入力してください。'))
            ->scalar('email')
            ->email('email', true, __d('baser', 'Eメールの形式が不正です。'))
            ->maxLength('email', 255, __d('baser', 'Eメールは255文字以内で入力してください。'))
            ->notEmptyString('email', __d('baser', 'Eメールを入力してください。'))
            ->add('email', [
                'nameUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser', '既に登録のあるEメールです。')
                ]]);
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

        return $validator;
    }

    /**
     * Validation New
     * @param Validator $validator
     * @return Validator
     * @checked
     * @noTodo
     * @unitTest
     */
    public function validationNew(Validator $validator): Validator
    {
        $this->validationDefault($validator)
            ->requirePresence('password', 'create', __d('baser', 'パスワードを入力してください。'))
            ->notEmptyString('password', __d('baser', 'パスワードを入力してください。'));
        return $validator;
    }

    /**
     * validationPasswordUpdate
     * @param Validator $validator
     * @return Validator
     * @checked
     * @unitTest
     * @noTodo
     */
    public function validationPasswordUpdate(Validator $validator): Validator
    {
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

        return $validator;
    }

    /**
     * コントロールソースを取得する
     *
     * @param string $field フィールド名
     * @param array $options オプション
     * @return Query コントロールソース
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource($field, $options = [])
    {
        switch($field) {
            case 'id':
                $controlSources['id'] = $this->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'name'
                ]);
                break;
            case 'user_group_id':
                $controlSources['user_group_id'] = $this->UserGroups->find('list', [
                    'keyField' => 'id',
                    'valueField' => 'title'
                ]);
                break;
        }
        if (isset($controlSources[$field])) {
            return $controlSources[$field];
        } else {
            return null;
        }
    }

    /**
     * ユーザーリストを取得する
     * 条件を指定する場合は引数を指定する
     *
     * @param array $conditions 取得条件
     * @return array
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getUserList($conditions = [])
    {
        $users = $this->find("all", [
            'fields' => ['id', 'real_name_1', 'real_name_2', 'nickname'],
            'conditions' => $conditions,
        ]);
        $list = [];
        if ($users) {
            $appView = new BcAdminAppView();
            $appView->loadHelper('BaserCore.BcBaser');
            foreach($users as $user) {
                $list[$user->id] = $appView->BcBaser->getUserName($user);
            }
        }
        return $list;
    }

    /**
     * 利用可能なユーザーを取得する
     * @param Query $query
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findAvailable(Query $query)
    {
        return $query->where(['status' => true])->contain('UserGroups');
    }

}
