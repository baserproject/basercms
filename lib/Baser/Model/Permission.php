<?php

/**
 * パーミッションモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * パーミッションモデル
 *
 * @package Baser.Model
 */
class Permission extends AppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Permission';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	public $useDbConfig = 'baser';

/**
 * belongsTo
 * @var array
 * @access public
 */
	public $belongsTo = array('UserGroup' => array('className' => 'UserGroup',
			'foreignKey' => 'user_group_id'));

/**
 * permissionsTmp
 * ログインしているユーザーの拒否URLリスト
 * キャッシュ用
 * 
 * @var mixed
 * @access public
 */
	public $permissionsTmp = -1;

/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notEmpty'),
				'message' => '設定名を入力してください。'),
			array('rule' => array('maxLength', 255),
				'message' => '設定名は255文字以内で入力してください。')
		),
		'user_group_id' => array(
			array('rule' => array('notEmpty'),
				'message' => 'ユーザーグループを選択してください。',
				'required' => true)
		),
		'url' => array(
			array('rule' => array('notEmpty'),
				'message' => '設定URLを入力してください。'),
			array('rule' => array('maxLength', 255),
				'message' => '設定URLは255文字以内で入力してください。'),
			array('rule' => array('checkUrl'),
				'message' => 'アクセス拒否として設定できるのは認証ページだけです。')
		)
	);

/**
 * 設定をチェックする
 *
 * @param array $check
 * @return boolean True if the operation should continue, false if it should abort
 * @access public
 */
	public function checkUrl($check) {
		if (!$check[key($check)]) {
			return true;
		}

		$url = $check[key($check)];

		if (preg_match('/^[^\/]/is', $url)) {
			$url = '/' . $url;
		}

		// ルーティング設定に合わせて変換
		$url = preg_replace('/^\/admin\//', '/' . Configure::read('Routing.prefixes.0') . '/', $url);

		if (preg_match('/^(\/[a-z_]+)\*$/is', $url, $matches)) {
			$url = $matches[1] . '/' . '*';
		}

		$params = Router::parse($url);

		if (empty($params['prefix'])) {
			return false;
		}

		return true;
	}

/**
 * 認証プレフィックスを取得する
 *
 * @param int $id
 * @return string
 * @access public
 */
	public function getAuthPrefix($id) {
		$data = $this->find('first', array(
			'conditions' => array('Permission.id' => $id),
			'recursive' => 1
		));
		if (isset($data['UserGroup']['auth_prefix'])) {
			return $data['UserGroup']['auth_prefix'];
		} else {
			return '';
		}
	}

/**
 * 初期値を取得する
 * @return array
 * @access public
 */
	public function getDefaultValue() {
		$data['Permission']['auth'] = 0;
		$data['Permission']['status'] = 1;
		return $data;
	}

/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access	public
 */
	public function getControlSource($field = null) {
		$controlSources['user_group_id'] = $this->UserGroup->find('list', array('conditions' => array('UserGroup.id <>' => Configure::read('BcApp.adminGroupId'))));
		$controlSources['auth'] = array('0' => '不可', '1' => '可');
		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
			return false;
		}
	}

/**
 * beforeSave
 * 
 * @param array $options
 * @return boolean
 * @access public
 */
	public function beforeSave($options = array()) {
		if (isset($this->data['Permission'])) {
			$data = $this->data['Permission'];
		} else {
			$data = $this->data;
		}
		if (isset($data['url'])) {
			if (preg_match('/^[^\/]/is', $data['url'])) {
				$data['url'] = '/' . $data['url'];
			}
		}
		$this->data['Permission'] = $data;
		return true;
	}

/**
 * 権限チェックを行う
 * 
 * @param array $url
 * @param string $userGroupId
 * @param array $params
 * @return boolean
 * @access public
 */
	public function check($url, $userGroupId) {
		if ($this->permissionsTmp === -1) {
			$conditions = array('Permission.user_group_id' => $userGroupId);
			$permissions = $this->find('all', array('conditions' => $conditions, 'order' => 'sort', 'recursive' => -1));
			if ($permissions) {
				$this->permissionsTmp = $permissions;
			} else {
				$this->permissionsTmp = array();
				return true;
			}
		}

		$permissions = $this->permissionsTmp;

		if ($url != '/') {
			$url = preg_replace('/^\//is', '', $url);
		}

		$adminPrefix = Configure::read('Routing.prefixes.0');

		$url = preg_replace("/^{$adminPrefix}\//", 'admin/', $url);

		// ダッシュボード、ログインユーザーの編集とログアウトは強制的に許可とする
		$allows = array(
			'/^admin$/',
			'/^admin\/$/',
			'/^admin\/dashboard\/.*?/',
			'/^admin\/users\/logout$/',
			'/^admin\/user_groups\/set_default_favorites$/'
		);

		if (!empty($_SESSION['Auth']['User']['id'])) {
			$allows[] = '/^admin\/users\/edit\/' . $_SESSION['Auth']['User']['id'] . '$/';
		}

		foreach ($allows as $allow) {
			if (preg_match($allow, $url)) {
				return true;
			}
		}

		$ret = true;
		foreach ($permissions as $permission) {
			if (!$permission['Permission']['status']) {
				continue;
			}
			if ($permission['Permission']['url'] != '/') {
				$pattern = preg_replace('/^\//is', '', $permission['Permission']['url']);
			} else {
				$pattern = $permission['Permission']['url'];
			}
			$pattern = addslashes($pattern);
			$pattern = str_replace('/', '\/', $pattern);
			$pattern = str_replace('*', '.*?', $pattern);
			$pattern = '/^' . str_replace('\/.*?', '(|\/.*?)', $pattern) . '$/is';
			if (preg_match($pattern, $url)) {
				$ret = $permission['Permission']['auth'];
			}
		}
		return $ret;
	}

/**
 * アクセス制限データをコピーする
 * 
 * @param int $id
 * @param array $data
 * @return mixed UserGroup Or false
 */
	public function copy($id, $data = array()) {
		if ($id) {
			$data = $this->find('first', array('conditions' => array('Permission.id' => $id), 'recursive' => -1));
		}

		if ($this->find('count', array('conditions' => array('Permission.user_group_id' => $data['Permission']['user_group_id'], 'Permission.name' => $data['Permission']['name'])))) {
			$data['Permission']['name'] .= '_copy';
			return $this->copy(null, $data); // 再帰処理
		}

		unset($data['Permission']['id']);
		unset($data['Permission']['modified']);
		unset($data['Permission']['created']);

		$data['Permission']['no'] = $this->getMax('no', array('user_group_id' => $data['Permission']['user_group_id'])) + 1;
		$data['Permission']['sort'] = $this->getMax('sort', array('user_group_id' => $data['Permission']['user_group_id'])) + 1;
		$this->create($data);
		$result = $this->save();
		if ($result) {
			$result['Permission']['id'] = $this->getInsertID();
			return $result;
		} else {
			return false;
		}
	}

}
