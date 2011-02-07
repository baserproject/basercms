<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーモデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.models
 * @since			Baser v 0.1.0
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
 * @package			baser.models
 */
class User extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'User';
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
	var $useDbConfig = 'baser';
/**
 * belongsTo
 * @var 	array
 * @access	public
 */
	var $belongsTo = array('UserGroup' =>   array(  'className'=>'UserGroup',
							'foreignKey'=>'user_group_id'));
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(
				array(	'rule' => VALID_NOT_EMPTY,
						'message' => "アカウント名を入力して下さい"),
				array(	'rule' => 'halfText',
						'message' => 'アカウント名は半角のみで入力して下さい'),
				array(	'rule' => array('duplicate','name'),
						'message' => '既に登録のあるアカウント名です'));

		$this->validate['real_name_1'] = array(
				array(	'rule' => VALID_NOT_EMPTY,
						'message' => "名前[姓]を入力して下さい"));

		$this->validate['password'] = array(
				'minLength' =>
					array(	'rule' => array('minLength',6),
							'allowEmpty' => false,
							'message' => 'パスワードは６文字以上で入力して下さい。'),
				'alphaNumeric' =>
					array(	'rule' => 'alphaNumeric',
							'message' => 'パスワードは半角英数字のみで入力して下さい'));

		$this->validate['email'] = array(
				array(	'rule' => array('email'),
						'message' => "Eメールの形式が不正です",
						'allowEmpty' => true));

		$this->validate['user_group_id'] =	array(
				array(	'rule' => VALID_NOT_EMPTY,
						'message' => "グループを選択して下さい"));

		return true;

	}
/**
 * バリデート処理
 *
 * @param	array	$options
 * @return	boolean True if there are no errors
 * @access 	public
 * TODO beforeValidateに移行する事
 */
	function invalidFields($options = array()) {

		$data = $this->data;

		/*** パスワードチェック ***/

		if(isset($data['User']['password_1']) && isset($data['User']['password_2'])) {

			// 入力ミスチェック
			if($data['User']['password_1'] != $data['User']['password_2']) {
				$this->invalidate('password','パスワードが同じものではありません');
			}

		}

		$ret = parent::invalidFields($options);

		// テキストボックスにエラークラスを表示させる
		// TODO invalidFields は、エラーのあるフィールドリストを返す関数の為、処理見直しが必要かも
		if(isset($this->validationErrors['password'])) {
			$this->invalidate('password_1');
			$this->invalidate('password_2');
		}

		return $ret;

	}
/**
 * アカウント重複チェック
 *
 * @param	string	user_name
 * @return	boolean
 * @access	public
 */
	function checkRepeatedId($id) {

		$ret = $this->find(array('name'=>$id));
		if($ret) {
			return true;
		}else {
			return false;
		}

	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null) {

		$controlSources['user_group_id'] = $this->UserGroup->find('list');

		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * ユーザーリストを取得する
 *
 * 条件を指定する場合は引数を指定する
 * @param array $authUser
 */
	function getUserList($conditions = array()) {

		$users = $this->find("all",array('fields'=>array('id','real_name_1','real_name_2'), 'conditions'=>$conditions));
		$list = array();
		if ($users) {
			// 苗字が同じ場合にわかりにくいので、foreachで生成
			//$this->set('users',Set::combine($users, '{n}.User.id', '{n}.User.real_name_1'));
			foreach($users as $key => $user) {
				if($user['User']['real_name_2']) {
					$name = $user['User']['real_name_1']." ".$user['User']['real_name_2'];
				}else {
					$name = $user['User']['real_name_1'];
				}
				$list[$user['User']['id']] = $name;
			}
		}
		return $list;
	}
/**
 * フォームの初期値を設定する
 *
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue() {

		$data['User']['user_group_id'] = 1;
		return $data;

	}
/**
 * afterFind
 *
 * @param	array	結果セット
 * @param	array	$primary
 * @return	array	結果セット
 * @access	public
 */
	function afterFind($results, $primary = false) {

		if(isset($results[0]['User'][0])) {
			$results[0]['User'] = $this->convertResults($results[0]['User']);
		}else {
			$results = $this->convertResults($results);
		}
		return parent::afterFind($results,$primary);

	}
/**
 * 取得結果を変換する
 *
 * HABTM対応
 *
 * @param	array	結果セット
 * @return	array	結果セット
 * @access	public
 */
	function convertResults($results) {

		if($results) {
			if(isset($result['User'])||isset($results[0]['User'])) {
				foreach($results as $key => $result) {
					if(isset($result['User'])) {
						if($result['User']) {
							$results[$key]['User'] = $this->convertToView($result['User']);
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
 * @param	array	結果セット
 * @return	array	結果セット
 * @access	public
 */
	function convertToView($data) {

		return $data;

	}
/**
 * ユーザーが許可されている認証プレフィックスを取得する
 *
 * @param	string	$userName
 * @return	string
 */
	function getAuthPrefix($userName) {
<<<<<<< HEAD

=======
		
>>>>>>> プレフィックス認証追加の仕組みを実装
		$user = $this->find('first', array(
			'fields'		=> array('UserGroup.auth_prefix'),
			'conditions'	=> array('User.name'=>$userName),
			'recursive'		=> 0
		));
<<<<<<< HEAD

		if(isset($user['UserGroup']['auth_prefix'])) {
			return $user['UserGroup']['auth_prefix'];
		} else {
			return '';
		}

	}
=======
>>>>>>> プレフィックス認証追加の仕組みを実装

		if(isset($user['UserGroup']['auth_prefix'])) {
			return $user['UserGroup']['auth_prefix'];
		} else {
			return '';
		}
		
	}
	
}
?>