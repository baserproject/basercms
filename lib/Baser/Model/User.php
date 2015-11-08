<?php


/**
 * ユーザーモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
class User extends AppModel {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'User';

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = array('BcCache');

/**
 * データベース接続
 *
 * @var string
 */
	public $useDbConfig = 'baser';

/**
 * belongsTo
 * 
 * @var array
 */
	public $belongsTo = array(
		'UserGroup' => array(
			'className' => 'UserGroup',
			'foreignKey' => 'user_group_id')
	);

/**
 * hasMany
 * 
 * @var array
 */
	public $hasMany = array('Favorite' => array(
			'className' => 'Favorite',
			'order' => 'Favorite.sort',
			'foreignKey' => 'user_id',
			'dependent' => false,
			'exclusive' => false,
			'finderQuery' => ''
	));

/**
 * validate
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'アカウント名を入力してください。'
			),
			'alphaNumericPlus' => array(
				'rule' => 'alphaNumericPlus',
				'message' => 'アカウント名は半角英数字とハイフン、アンダースコアのみで入力してください。'
			),
			'duplicate' => array(
				'rule' => array('duplicate', 'name'),
				'message' => '既に登録のあるアカウント名です。'
			),
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'アカウント名は255文字以内で入力してください。'
			)
		),
		'real_name_1' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => '名前[姓]を入力してください。'),
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => '名前[姓]は50文字以内で入力してください。'
			)
		),
		'real_name_2' => array(
			'maxLength' => array(
				'rule' => array('maxLength', 50),
				'message' => '名前[名]は50文字以内で入力してください。'
			)
		),
		'password' => array(
			'minLength' => array(
				'rule' => array('minLength', 6),
				'allowEmpty' => false,
				'message' => 'パスワードは6文字以上で入力してください。'
			),
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'パスワードは255文字以内で入力してください。'
			),
			'alphaNumeric' => array(
				'rule' => 'alphaNumericPlus',
				'message' => 'パスワードは半角英数字とハイフン、アンダースコアのみで入力してください。'
			),
			'confirm' => array(
				'rule' => array('confirm', array('password_1', 'password_2')),
				'message' => 'パスワードが同じものではありません。'
			)
		),
		'email' => array(
			'email' => array(
				'rule' => array('email'),
				'message' => 'Eメールの形式が不正です。',
				'allowEmpty' => true),
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'Eメールは255文字以内で入力してください。')
		),
		'user_group_id' => array(
			'rule' => array('notEmpty'),
			'message' => 'グループを選択してください。'
		)
	);

/**
 * validates
 *
 * @param string $options An optional array of custom options to be made available in the beforeValidate callback
 * @return boolean True if there are no errors
 */
	public function validates($options = array()) {
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
	public function getControlSource($field) {
		switch ($field) {
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
	public function getUserList($conditions = array()) {
		$users = $this->find("all", array(
			'fields' => array('id', 'real_name_1', 'real_name_2', 'nickname'),
			'conditions' => $conditions,
			'recursive' => -1
		));
		$list = array();
		if ($users) {
			App::uses('BcBaserHelper', 'View/Helper');
			$BcBaser = new BcBaserHelper(new View());
			foreach ($users as $key => $user) {
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
	public function getDefaultValue() {
		$data[$this->alias]['user_group_id'] = Configure::read('BcApp.adminGroupId');
		return $data;
	}

/**
 * afterFind
 *
 * @param array 結果セット
 * @param array $primary
 * @return array 結果セット
 */
	public function afterFind($results, $primary = false) {
		if (isset($results[0][$this->alias][0])) {
			$results[0][$this->alias] = $this->convertResults($results[0][$this->alias]);
		} else {
			$results = $this->convertResults($results);
		}
		return parent::afterFind($results, $primary);
	}

/**
 * 取得結果を変換する
 * HABTM対応
 *
 * @param array 結果セット
 * @return array 結果セット
 */
	public function convertResults($results) {
		if ($results) {
			if (isset($result[$this->alias]) || isset($results[0][$this->alias])) {
				foreach ($results as $key => $result) {
					if (isset($result[$this->alias])) {
						if ($result[$this->alias]) {
							$results[$key][$this->alias] = $this->convertToView($result[$this->alias]);
						}
					} elseif (!empty($result)) {
						$results[$key] = $this->convertToView($result);
					}
				}
			} else {
				$results = $this->convertToView($results);
			}
		}
		return $results;
	}

/**
 * View用のデータを取得する
 *
 * @param array 結果セット
 * @return array 結果セット
 */
	public function convertToView($data) {
		return $data;
	}

/**
 * ユーザーが許可されている認証プレフィックスを取得する
 *
 * @param string $userName ユーザーの名前
 * @return string
 */
	public function getAuthPrefix($userName) {
		$user = $this->find('first', array(
			'conditions' => array("{$this->alias}.name" => $userName),
			'recursive' => 1
		));

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
	public function beforeSave($options = array()) {
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
	public function afterSave($created, $options = array()) {
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
	public function applyDefaultFavorites($userId, $userGroupId) {
		$result = true;
		$defaultFavorites = $this->UserGroup->field('default_favorites', array(
			'UserGroup.id' => $userGroupId
		));
		if ($defaultFavorites) {
			$defaultFavorites = BcUtil::unserialize($defaultFavorites);
			if ($defaultFavorites) {
				$this->deleteFavorites($userId);
				$this->Favorite->Behaviors->detach('BcCache');
				foreach ($defaultFavorites as $favorites) {
					$favorites['user_id'] = $userId;
					$favorites['sort'] = $this->Favorite->getMax('sort', array('Favorite.user_id' => $userId)) + 1;
					$this->Favorite->create($favorites);
					if(!$this->Favorite->save()) {
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
	public function deleteFavorites($userId) {
		return $this->Favorite->deleteAll(array('Favorite.user_id' => $userId), false);
	}

}
