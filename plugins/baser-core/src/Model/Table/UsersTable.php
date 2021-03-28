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
use Cake\ORM\Table;
use Cake\Validation\Validator;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
        // TODO 暫定措置
        // >>>
        return true;
        // <<<

        if (isset($this->data[$this->alias]['password'])) {
            App::uses('AuthComponent', 'Controller/Component');
            $this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
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
     */
    public function afterSave($created, $options = [])
    {
        // TODO 暫定措置
        // >>>
        return;
        // <<<

        parent::afterSave($created);
        if ($created && !empty($this->UserGroup)) {
            $this->applyDefaultFavorites($this->getLastInsertID(), $this->data[$this->alias]['user_group_id']);
        }
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
            ->scalar('nickname')
            ->maxLength('nickname', 255, __d('baser', 'ニックネームは255文字以内で入力してください。'))
            ->allowEmptyString('nickname');
        $validator
            ->add('user_groups', [
                'userGroupsNotEmptyMultiple' => [
                    'rule' => 'notEmptyMultiple',
                    'provider' => 'bc',
                    'message' => __d('baser', 'グループを選択してください。')
                ]
            ]);
        $validator
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
            ->notEmptyString('password', __d('baser', 'パスワードを入力してください。'));
        return $validator;
    }

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
     * 初期化されたエンティティを取得する
     * @checked
     * @noTodo
     * @unitTest
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
     * @return Query コントロールソース
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getControlSource($field, $options = [])
    {
        switch($field) {
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
     * Where 条件を作成する
     * @param $query
     * @param $request
     * @return Query
     * @checked
     * @noTodo
     * @unitTest
     */
    public function createWhere($query, $request): Query
    {
        $get = $request->getQuery();
        if (!empty($get['user_group_id'])) {
            $query->matching('UserGroups', function($q) use ($get) {
                return $q->where(['UserGroups.id' => $get['user_group_id']]);
            });
        }
        return $query;
    }

    /**
     * ログイン時のユーザデータを取得する
     *
     * @param [type] $id
     * @return User
     * @checked
     * @noTodo
     * @unitTest
     */
    public function getLoginFormatData($id): User
    {
        return $this->get($id, [
            'contain' => ['UserGroups'],
        ]);
    }

    /**
     * ユーザーリストを取得する
     * 条件を指定する場合は引数を指定する
     *
     * @param array $conditions 取得条件
     * @return array
     */
    public function getUserList($conditions = [])
    {
        $users = $this->find("all", [
            'fields' => ['id', 'real_name_1', 'real_name_2', 'nickname'],
            'conditions' => $conditions,
            'recursive' => -1
        ]);
        $list = [];
        if ($users) {
            App::uses('BcBaserHelper', 'View/Helper');
            $BcBaser = new BcBaserHelper(new View());
            foreach($users as $key => $user) {
                $list[$user[$this->alias]['id']] = $BcBaser->getUserName($user);
            }
        }
        return $list;
    }

    /**
     * フォームの初期値を設定する
     *
     * @return array 初期値データ
     */
    public function getDefaultValue()
    {
        $data[$this->alias]['user_group_id'] = Configure::read('BcApp.adminGroupId');
        return $data;
    }

    /**
     * ユーザーが許可されている認証プレフィックスを取得する
     *
     * @param string $userName ユーザーの名前
     * @return string
     */
    public function getAuthPrefix($userName)
    {
        $user = $this->find('first', [
            'conditions' => ["{$this->alias}.name" => $userName],
            'recursive' => 1
        ]);

        if (isset($user['UserGroup']['auth_prefix'])) {
            return $user['UserGroup']['auth_prefix'];
        } else {
            return '';
        }
    }

    /**
     * よく使う項目の初期データをユーザーに適用する
     *
     * @param type $userId ユーザーID
     * @param type $userGroupId ユーザーグループID
     */
    public function applyDefaultFavorites($userId, $userGroupId)
    {
        $result = true;
        $defaultFavorites = $this->UserGroup->field('default_favorites', [
            'UserGroup.id' => $userGroupId
        ]);
        if ($defaultFavorites) {
            $defaultFavorites = BcUtil::unserialize($defaultFavorites);
            if ($defaultFavorites) {
                $this->deleteFavorites($userId);
                $this->Favorite->Behaviors->detach('BcCache');
                foreach($defaultFavorites as $favorites) {
                    $favorites['user_id'] = $userId;
                    $favorites['sort'] = $this->Favorite->getMax('sort', ['Favorite.user_id' => $userId]) + 1;
                    $this->Favorite->create($favorites);
                    if (!$this->Favorite->save()) {
                        $result = false;
                    }
                }
            }
        }
        return $result;
    }

    /**
     * ユーザーに関連するよく使う項目を削除する
     *
     * @param int $userId ユーザーID
     * @return boolean
     */
    public function deleteFavorites($userId)
    {
        return $this->Favorite->deleteAll(['Favorite.user_id' => $userId], false);
    }

}
