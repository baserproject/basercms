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
 * Class UserGroup
 *
 * ユーザーグループモデル
 *
 * @package Baser.Model
 * @property \Permission $Permission
 */
class UserGroup extends AppModel
{

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

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
	 * ユーザーグループデータをコピーする
	 *
	 * @param int $id ユーザーグループID
	 * @param array $data DBに挿入するデータ
	 * @param boolean $recursive 関連したPermissionもcopyをするかしないか
	 * @return mixed UserGroup Or false
	 */
	public function copy($id, $data = [], $recursive = true)
	{
		if ($id) {
			$data = $this->find('first', ['conditions' => ['UserGroup.id' => $id], 'recursive' => -1]);
		} else {
			if (!empty($data['UserGroup']['id'])) {
				$id = $data['UserGroup']['id'];
			}
		}
		$data['UserGroup']['name'] .= '_copy';
		$data['UserGroup']['title'] .= '_copy';

		unset($data['UserGroup']['id']);
		unset($data['UserGroup']['modified']);
		unset($data['UserGroup']['created']);

		$this->create($data);
		$result = $this->save();
		if ($result) {
			$result['UserGroup']['id'] = $this->getInsertID();
			if ($recursive) {
				$permissions = $this->Permission->find('all', [
					'conditions' => ['Permission.user_group_id' => $id],
					'order' => ['Permission.sort'],
					'recursive' => -1
				]);
				if ($permissions) {
					foreach($permissions as $permission) {
						$permission['Permission']['user_group_id'] = $result['UserGroup']['id'];
						$this->Permission->copy(null, $permission);
					}
				}
			}
			return $result;
		} else {
			if (!isset($this->validationErrors['name'])) {
				return $this->copy(null, $data, $recursive);
			} else {
				return false;
			}
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
