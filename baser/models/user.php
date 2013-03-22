<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーモデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * ユーザーモデル
 *
 * @package baser.models
 */
class User extends AppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'User';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('BcCache');
/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'baser';
/**
 * belongsTo
 * 
 * @var array
 * @access public
 */
	var $belongsTo = array('UserGroup' =>   array(  'className'=>'UserGroup',
							'foreignKey'=>'user_group_id'));
/**
 * hasMany
 * 
 * @var array
 * @access public
 */
	var $hasMany = array('Favorite' => array(
		'className'		=> 'Favorite',
		'order'			=> 'Favorite.sort',
		'foreignKey'	=> 'user_id',
		'dependent'		=> false,
		'exclusive'		=> false,
		'finderQuery'	=> ''
	));
/**
 * validate
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name'=>array(
			'notEmpty' => array(
				'rule'		=> array('notEmpty'),
				'message'	=> 'アカウント名を入力してください。'
			),
			'alphaNumericPlus' => array(
				'rule'		=>	'alphaNumericPlus',
				'message'	=> 'アカウント名は半角英数字とハイフン、アンダースコアのみで入力してください。'
			),
			'duplicate' => array(
				'rule'		=>	array('duplicate','name'),
				'message'	=> '既に登録のあるアカウント名です。'
			),
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> 'アカウント名は255文字以内で入力してください。'
			)
		),
		'real_name_1' => array(
			'notEmpty' => array(
				'rule'		=> array('notEmpty'),
				'message'	=> '名前[姓]を入力してください。'),
			'maxLength' => array(
				'rule'		=> array('maxLength', 50),
				'message'	=> '名前[姓]は50文字以内で入力してください。'
			)
		),
		'real_name_2' => array(
			'maxLength' => array(
				'rule'		=> array('maxLength', 50),
				'message'	=> '名前[名]は50文字以内で入力してください。'
			)
		),
		'password' => array(
			'minLength' => array(
				'rule'		=> array('minLength',6),
				'allowEmpty'=> false,
				'message'	=> 'パスワードは６文字以上で入力してください。'
			),
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> 'パスワードは255文字以内で入力してください。'
			),
			'alphaNumeric' => array(
				'rule'		=> 'alphaNumericPlus',
				'message'	=> 'パスワードは半角英数字とハイフン、アンダースコアのみで入力してください。'
			),
			'confirm' => array(
				'rule'		=> array('confirm', array('password_1', 'password_2')),
				'message'	=> 'パスワードが同じものではありません。'
			)
		),
		'email' => array(
			'email' => array(
				'rule'		=> array('email'),
				'message'	=> 'Eメールの形式が不正です。',
				'allowEmpty'=> true),
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> 'Eメールは255文字以内で入力してください。')
		),
		'user_group_id'=>array(
			'rule'		=> array('notEmpty'),
			'message'	=> 'グループを選択してください。'
		)
	);
/**
 * validates
 *
 * @param string $options An optional array of custom options to be made available in the beforeValidate callback
 * @return boolean True if there are no errors
 * @access public
 */
	function validates($options = array()) {
		
		$result = parent::validates($options);
		if(isset($this->validationErrors['password'])) {
			$this->invalidate('password_1');
			$this->invalidate('password_2');
		}
		return $result;
		
	}
/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access public
 */
	function getControlSource($field) {

		switch($field) {
			
			case 'user_group_id':
				$controlSources['user_group_id'] = $this->UserGroup->find('list');
				break;
		
		}
		
		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * ユーザーリストを取得する
 * 条件を指定する場合は引数を指定する
 * 
 * @param array $authUser
 * @return array
 * @access public
 */
	function getUserList($conditions = array()) {

		$users = $this->find("all",array('fields'=>array('id','real_name_1','real_name_2', 'nickname'), 'conditions'=>$conditions));
		$list = array();
		if ($users) {
			App::import('Helper', 'BcBaser');
			$BcBaser = new BcBaserHelper();
			foreach($users as $key => $user) {
				$list[$user[$this->alias]['id']] = $BcBaser->getUserName($user);;
			}
		}
		return $list;
		
	}
/**
 * フォームの初期値を設定する
 *
 * @return array 初期値データ
 * @access public
 */
	function getDefaultValue() {

		$data[$this->alias]['user_group_id'] = Configure::read('BcApp.adminGroupId');
		return $data;

	}
/**
 * afterFind
 *
 * @param array 結果セット
 * @param array $primary
 * @return array 結果セット
 * @access	public
 */
	function afterFind($results, $primary = false) {

		if(isset($results[0][$this->alias][0])) {
			$results[0][$this->alias] = $this->convertResults($results[0][$this->alias]);
		}else {
			$results = $this->convertResults($results);
		}
		return parent::afterFind($results,$primary);

	}
/**
 * 取得結果を変換する
 * HABTM対応
 *
 * @param array 結果セット
 * @return array 結果セット
 * @access public
 */
	function convertResults($results) {

		if($results) {
			if(isset($result[$this->alias])||isset($results[0][$this->alias])) {
				foreach($results as $key => $result) {
					if(isset($result[$this->alias])) {
						if($result[$this->alias]) {
							$results[$key][$this->alias] = $this->convertToView($result[$this->alias]);
						}
					}elseif(!empty($result)) {
						$results[$key] = $this->convertToView($result);
					}
				}
			}else {
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
 * @access public
 */
	function convertToView($data) {

		return $data;

	}
/**
 * ユーザーが許可されている認証プレフィックスを取得する
 *
 * @param string $userName
 * @return string
 */
	function getAuthPrefix($userName) {

		$user = $this->find('first', array(
			'conditions'	=> array("{$this->alias}.name"=>$userName),
			'recursive'		=> 1
		));

		if(isset($user['UserGroup']['auth_prefix'])) {
			return $user['UserGroup']['auth_prefix'];
		} else {
			return '';
		}

	}
/**
 * afterSave
 * 
 * @param boolean $created 
 * @access public
 */
	function afterSave($created) {
		parent::afterSave($created);
		if($created && !empty($this->UserGroup)) {
			$defaultFavorites = $this->UserGroup->field('default_favorites', array('UserGroup.id' => $this->data[$this->alias]['user_group_id']));
			if($defaultFavorites) {
				$defaultFavorites = unserialize($defaultFavorites);
				if($defaultFavorites) {
					$userId = $this->getLastInsertID();
					foreach($defaultFavorites as $favorites) {
						$favorites['user_id'] = $userId;
						$favorites['sort'] = $this->Favorite->getMax('sort')+1;
						$this->Favorite->create($favorites);
						$this->Favorite->save();
					}
				}
			}
		}
	}
}