<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class User
 *
 * ユーザーモデル
 *
 * @package Baser.Model
 */
class User extends AppModel
{

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * belongsTo
	 *
	 * @var array
	 */
	public $belongsTo = [
		'UserGroup' => [
			'className' => 'UserGroup',
			'foreignKey' => 'user_group_id']
	];

	/**
	 * hasMany
	 *
	 * @var array
	 */
	public $hasMany = ['Favorite' => [
		'className' => 'Favorite',
		'order' => 'Favorite.sort',
		'foreignKey' => 'user_id',
		'dependent' => false,
		'exclusive' => false,
		'finderQuery' => ''
	]];

	/**
	 * User constructor.
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
				'notBlank' => ['rule' => ['notBlank'], 'message' => __d('baser', 'アカウント名を入力してください。')],
				'alphaNumericPlus' => ['rule' => 'alphaNumericPlus', 'message' => __d('baser', 'アカウント名は半角英数字とハイフン、アンダースコアのみで入力してください。')],
				'duplicate' => ['rule' => ['duplicate', 'name'], 'message' => __d('baser', '既に登録のあるアカウント名です。')],
				'maxLength' => ['rule' => ['maxLength', 255], 'message' => __d('baser', 'アカウント名は255文字以内で入力してください。')]
			],
			'real_name_1' => [
				'notBlank' => ['rule' => ['notBlank'], 'message' => __d('baser', '名前[姓]を入力してください。')],
				'maxLength' => ['rule' => ['maxLength', 50], 'message' => __d('baser', '名前[姓]は50文字以内で入力してください。')]],
			'real_name_2' => [
				'maxLength' => ['rule' => ['maxLength', 50], 'message' => __d('baser', '名前[名]は50文字以内で入力してください。')]],
			'password' => [
				'minLength' => ['rule' => ['minLength', 6], 'allowEmpty' => false, 'message' => __d('baser', 'パスワードは6文字以上で入力してください。')],
				'maxLength' => ['rule' => ['maxLength', 255], 'message' => __d('baser', 'パスワードは255文字以内で入力してください。')],
				'alphaNumericPlus' => ['rule' => ['alphaNumericPlus', [' \.:\/\(\)#,@\[\]\+=&;\{\}!\$\*']], 'message' => __d('baser', 'パスワードは半角英数字(英字は大文字小文字を区別)とスペース、記号(._-:/()#,@[]+=&;{}!$*)のみで入力してください。')],
				'confirm' => ['rule' => ['confirm', ['password_1', 'password_2']], 'message' => __d('baser', 'パスワードが同じものではありません。')]],
			'email' => [
				'email' => ['rule' => ['email'], 'message' => __d('baser', 'Eメールの形式が不正です。'), 'allowEmpty' => true],
				'duplicate' => ['rule' => ['duplicate', 'email'], 'message' => __d('baser', '既に登録のあるEメールです。')],
				'maxLength' => ['rule' => ['maxLength', 255], 'message' => __d('baser', 'Eメールは255文字以内で入力してください。')]],
			'user_group_id' => [
				'rule' => ['notBlank'], 'message' => __d('baser', 'グループを選択してください。')]
		];
	}

	/**
	 * validates
	 *
	 * @param string $options An optional array of custom options to be made available in the beforeValidate callback
	 * @return boolean True if there are no errors
	 */
	public function validates($options = [])
	{
		$result = parent::validates($options);
		if (isset($this->validationErrors['password'])) {
			$this->invalidate('password_1');
			$this->invalidate('password_2');
		}
		return $result;
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field フィールド名
	 * @return array コントロールソース
	 */
	public function getControlSource($field)
	{
		switch($field) {
			case 'user_group_id':
				$controlSources['user_group_id'] = $this->UserGroup->find('list');
				break;
		}

		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
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
	 * beforeSave
	 *
	 * @param type $options
	 * @return boolean
	 */
	public function beforeSave($options = [])
	{
		if (isset($this->data[$this->alias]['password'])) {
			App::uses('AuthComponent', 'Controller/Component');
			$this->data[$this->alias]['password'] = AuthComponent::password($this->data[$this->alias]['password']);
		}
		return true;
	}

	/**
	 * afterSave
	 *
	 * @param boolean $created
	 */
	public function afterSave($created, $options = [])
	{
		parent::afterSave($created);
		if ($created && !empty($this->UserGroup)) {
			$this->applyDefaultFavorites($this->getLastInsertID(), $this->data[$this->alias]['user_group_id']);
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
