<?php

/**
 * よく使う項目　モデル
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

/**
 * よく使う項目　モデル
 *
 * @package Baser.Model
 */
class Favorite extends AppModel {

/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	public $useDbConfig = 'baser';

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Favorite';

/**
 * belongsTo
 * 
 * @var array
 * @access public
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
	));

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

/**
 * セッション
 */
	public $_Session;

/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'url' => array(
			array('rule' => array('isPermitted'),
				'message' => 'このURLの登録は許可されていません。')
		)
	);

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
		$userGroupId = $this->_Session->read('Auth.User.user_group_id');
		if ($userGroupId == Configure::read('BcApp.adminGroupId')) {
			return true;
		}
		$Permission = ClassRegistry::init('Permission');
		return $Permission->check($url, $userGroupId);
	}

}
