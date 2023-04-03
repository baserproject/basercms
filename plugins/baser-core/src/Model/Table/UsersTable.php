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
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;
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
class UsersTable extends AppTable
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
            'through' => 'BaserCore.UsersUserGroups',
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
        if ((isset($data['password_1']) && $data['password_1'] !== '') ||
            (isset($data['password_2']) && $data['password_2'] !== '')) {
            $data['password'] = $data['password_1'];
        }
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
            ->maxLength('name', 255, __d('baser_core', 'アカウント名は255文字以内で入力してください。'))
            ->add('name', [
                'nameUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser_core', '既に登録のあるアカウント名です。')
                ]])
            ->add('name', [
                'nameAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'アカウント名は半角英数字とハイフン、アンダースコアのみで入力してください。')
                ]]);
        $validator
            ->scalar('real_name_1')
            ->maxLength('real_name_1', 50, __d('baser_core', '名前[姓]は50文字以内で入力してください。'))
            ->requirePresence('real_name_1', 'create', __d('baser_core', '名前[姓]を入力してください。'))
            ->notEmptyString('real_name_1', __d('baser_core', '名前[姓]を入力してください。'));
        $validator
            ->scalar('real_name_2')
            ->maxLength('real_name_2', 50, __d('baser_core', '名前[名]は50文字以内で入力してください。'))
            ->allowEmptyString('real_name_2');
        $validator
            ->scalar('nickname')
            ->maxLength('nickname', 255, __d('baser_core', 'ニックネームは255文字以内で入力してください。'))
            ->allowEmptyString('nickname');
        $validator
            ->requirePresence('user_groups', 'create', __d('baser_core', 'グループを選択してください。'))
            ->add('user_groups', [
                'userGroupsNotEmptyMultiple' => [
                    'rule' => 'notEmptyMultiple',
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'グループを選択してください。')
                ]
            ])
            ->add('user_groups', [
                'willChangeSelfGroup' => [
                    'rule' => 'willChangeSelfGroup',
                    'provider' => 'user',
                    'on' => 'update',
                    'message' => __d('baser_core', '自分のアカウントのグループは変更できません。')
                ]
            ]);
        $validator
            ->requirePresence('email', 'create', __d('baser_core', 'Eメールを入力してください。'))
            ->scalar('email')
            ->email('email', true, __d('baser_core', 'Eメールの形式が不正です。'))
            ->maxLength('email', 255, __d('baser_core', 'Eメールは255文字以内で入力してください。'))
            ->notEmptyString('email', __d('baser_core', 'Eメールを入力してください。'))
            ->add('email', [
                'nameUnique' => [
                    'rule' => 'validateUnique',
                    'provider' => 'table',
                    'message' => __d('baser_core', '既に登録のあるEメールです。')
                ]]);
        $validator
            ->scalar('password')
            ->minLength('password', 6, __d('baser_core', 'パスワードは6文字以上で入力してください。'))
            ->maxLength('password', 255, __d('baser_core', 'パスワードは255文字以内で入力してください。'))
            ->add('password', [
                'passwordAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus', ' \.:\/\(\)#,@\[\]\+=&;\{\}!\$\*'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。')
                ]])
            ->add('password', [
                'passwordConfirm' => [
                    'rule' => ['confirm', ['password_1', 'password_2']],
                    'provider' => 'bc',
                    'message' => __d('baser_core', __d('baser_core', 'パスワードが同じものではありません。'))
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
            ->requirePresence('password', 'create', __d('baser_core', 'パスワードを入力してください。'))
            ->notEmptyString('password', __d('baser_core', 'パスワードを入力してください。'));
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
            ->minLength('password', 6, __d('baser_core', 'パスワードは6文字以上で入力してください。'))
            ->maxLength('password', 255, __d('baser_core', 'パスワードは255文字以内で入力してください。'))
            ->add('password', [
                'passwordAlphaNumericPlus' => [
                    'rule' => ['alphaNumericPlus', ' \.:\/\(\)#,@\[\]\+=&;\{\}!\$\*'],
                    'provider' => 'bc',
                    'message' => __d('baser_core', 'パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。')
                ]])
            ->add('password', [
                'passwordConfirm' => [
                    'rule' => ['confirm', ['password_1', 'password_2']],
                    'provider' => 'bc',
                    'message' => __d('baser_core', __d('baser_core', 'パスワードが同じものではありません。'))
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
     *
     * プレフィックスが Api の場合は、Admin に対しての許可があるかどうかで判定する
     *
     * @param Query $query
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function findAvailable(Query $query)
    {
        $prefix = Router::getRequest()->getParam('prefix');
        return $query->where([
                'Users.status' => true
            ])
            ->matching('UserGroups', function($q) use ($prefix) {
                return $q->where(['UserGroups.auth_prefix LIKE' => '%' . $prefix . '%']);
            })->contain(['UserGroups']);
    }

}
