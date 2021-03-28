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
class UserGroupsTable extends Table
{


	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = [
		'Permission' => [
			'className' => 'Permission',
			'order' => 'id',
			'foreignKey' => 'user_group_id',
			'dependent' => true,
			'exclusive' => false,
			'finderQuery' => ''
		],
		'User' => [
			'className' => 'User',
			'order' => 'id',
			'foreignKey' => 'user_group_id',
			'dependent' => false,
			'exclusive' => false,
			'finderQuery' => ''
		]
	];

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
            'foreignKey' => 'user_group_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'users_user_groups',
        ]);
    }



	/**
	 * UserGroup constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'name' => [
				['rule' => ['notBlank'], 'message' => __d('baser', 'ユーザーグループ名を入力してください。')],
				['rule' => ['halfText'], 'message' => __d('baser', 'ユーザーグループ名は半角のみで入力してください。')],
				['rule' => ['duplicate', 'name'], 'message' => __d('baser', '既に登録のあるユーザーグループ名です。')],
				['rule' => ['maxLength', 50], 'message' => __d('baser', 'ユーザーグループ名は50文字以内で入力してください。')]],
			'title' => [
				['rule' => ['notBlank'], 'message' => __d('baser', '表示名を入力してください。')],
				['rule' => ['maxLength', 50], 'message' => __d('baser', '表示名は50文字以内で入力してください。')]],
			'auth_prefix' => [
				['rule' => ['notBlank'], 'message' => __d('baser', '認証プレフィックスを入力してください。')]]
		];
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
            ->maxLength('name', 50, 'ユーザーグループ名は50文字以内で入力してください。')
            ->notEmptyString('name', 'ユーザーグループ名を入力してください。')
            ->add('name', [
                'name_halfText' => [
                    'rule' => 'halfText',
                    'provider' => 'bc',
                    'message' => 'ユーザーグループ名は半角のみで入力してください。'
                ],
                'name_unique' => [
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

    /**
     * ユーザーグループデータをコピーする
     *
     * @param int $id ユーザーグループID
     * @param array $data DBに挿入するデータ
     * @param bool $recursive 関連したPermissionもcopyするかしないか
     * @return mixed UserGroups Or false
     * @throws CopyFailedException When copy failed.
     * @checked
     * @unitTest
     */
    public function copy($id = null, $data = [], $recursive = true)
    {
        if ($id && is_numeric($id)) {
            $data = $this->get($id)->toArray();
        } else {
            if (!empty($data['id'])) {
                $id = $data['id'];
            }
        }
        $data['name'] .= '_copy';
        $data['title'] .= '_copy';

        unset($data['id']);
        unset($data['created']);
        unset($data['modified']);

        $entity = $this->newEntity($data);
        $errors = $entity->getErrors();
        if ($errors) {
            $exception = new CopyFailedException(__d('baser', '処理に失敗しました。'));
            $exception->setErrors($errors);
            throw $exception;
        }

        $result = $this->save($entity);
        if ($result) {
            // TODO: Permissionのコピー
//			$result['UserGroup']['id'] = $this->getInsertID();
//			if ($recursive) {
//				$permissions = $this->Permission->find('all', [
//					'conditions' => ['Permission.user_group_id' => $id],
//					'order' => ['Permission.sort'],
//					'recursive' => -1
//				]);
//				if ($permissions) {
//					foreach($permissions as $permission) {
//						$permission['Permission']['user_group_id'] = $result['UserGroup']['id'];
//						$this->Permission->copy(null, $permission);
//					}
//				}
//			}
            return $result;
        } else {
            if (!isset($errors['name'])) {
                return $this->copy(null, $data, $recursive);
            } else {
                return false;
            }
        }
    }

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * 関連するユーザーを管理者グループに変更し保存する
	 *
	 * @param boolean $cascade
	 * @return boolean
	 */
	public function beforeDelete($cascade = true)
	{
		parent::beforeDelete($cascade);
		$ret = true;
		if (!empty($this->data['UserGroup']['id'])) {
			$id = $this->data['UserGroup']['id'];
			$this->User->unBindModel(['belongsTo' => ['UserGroup']]);
			$datas = $this->User->find('all', ['conditions' => ['User.user_group_id' => $id]]);
			if ($datas) {
				foreach($datas as $data) {
					$data['User']['user_group_id'] = Configure::read('BcApp.adminGroupId');
					$this->User->set($data);
					if (!$this->User->save()) {
						$ret = false;
					}
				}
			}
		}
		return $ret;
	}

	/**
	 * 管理者グループ以外のグループが存在するかチェックする
	 * @return    boolean
	 */
	public function checkOtherAdmins()
	{
		if ($this->find('first', ['conditions' => ['UserGroup.id <>' => 1]])) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * 認証プレフィックスを取得する
	 *
	 * @param int $id ユーザーグループID
	 * @return    string
	 */
	public function getAuthPrefix($id)
	{
		$data = $this->find('first', [
			'conditions' => ['UserGroup.id' => $id],
			'fields' => ['UserGroup.auth_prefix'],
			'recursive' => -1
		]);
		if (isset($data['UserGroup']['auth_prefix'])) {
			return $data['UserGroup']['auth_prefix'];
		} else {
			return '';
		}
	}

	/**
	 * グローバルメニューを利用可否確認
	 *
	 * @param string $id ユーザーグループID
	 * @return boolean
	 */
	public function isAdminGlobalmenuUsed($id)
	{
		return $this->field('use_admin_globalmenu', ['UserGroup.id' => $id]);
	}

}
