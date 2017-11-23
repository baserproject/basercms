<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * よく使う項目　モデル
 *
 * @package Baser.Model
 */
class Favorite extends AppModel {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'Favorite';

/**
 * belongsTo
 * 
 * @var array
 */
	public $belongsTo = [
		'User' => [
			'className' => 'User',
			'foreignKey' => 'user_id'
	]];

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = ['BcCache'];

/**
 * セッション
 *
 * @var Session
 */
	public $_Session;

/**
 * バリデーション
 *
 * @var array
 */
	public $validate = [
		'url' => [
			['rule' => ['isPermitted'],
				'message' => 'このURLの登録は許可されていません。']
		]
	];

/**
 * セッションをセットする
 * 
 * @param SessionComponent $Session
 */
	public function setSession(SessionComponent $Session) {
		$this->_Session = $Session;
	}

/**
 * アクセス権があるかチェックする
 * 
 * @param array $check
 */
	public function isPermitted($check) {
		if (!$this->_Session) {
			return true;
		}
		$url = $check[key($check)];
		$prefix = BcUtil::authSessionKey('admin');
		$userGroupId = $this->_Session->read('Auth.' . $prefix . '.user_group_id');
		if ($userGroupId == Configure::read('BcApp.adminGroupId')) {
			return true;
		}
		$Permission = ClassRegistry::init('Permission');
		return $Permission->check($url, $userGroupId);
	}

}
