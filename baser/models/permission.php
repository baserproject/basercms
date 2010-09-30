<?php
/* SVN FILE: $Id$ */
/**
 * パーミッションモデル
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
 * パーミッションモデル
 *
 * @package			baser.models
 */
class Permission extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'Permission';
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
 * permissions
 * ログインしているユーザーの拒否URLリスト
 * キャッシュ用
 * @var mixed
 */
	var $permissions = -1;
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(array(	'rule' => array('minLength',1),
						'message' => "設定名を入力して下さい"));
		$this->validate['user_group_id'] =	array(array('rule' => array('minLength',1),
						'message' => "ユーザーグループを選択して下さい",
						'required'=>true
						));
		$this->validate['url'] = array(	array(	'rule' => array('minLength',1),
						'message' => "設定を入力して下さい"),
				array(	'rule' => 'checkUrl',
						'message' => 'アクセス拒否として設定できるのは認証ページだけです。'));
		return true;

	}
/**
 * 設定をチェックする
 *
 * @return boolean True if the operation should continue, false if it should abort
 * @access public
 */
	function checkUrl($check) {

		if(!$check[key($check)]) {
			return true;
		}
		$url = $check[key($check)];
		if(preg_match('/^[^\/]/is',$url)) {
			$url = '/'.$url;
		}
		if(preg_match('/^(\/[a-z_]+)\*$/is',$url,$matches)) {
			$url = $matches[1].'/'.'*';
		}
		$params = Router::parse($url);
		if(empty($params['prefix'])) {
			$this->invalidate('setting','>> アクセス拒否として設定できるのは認証ページだけです。');
			return false;
		}

		return true;

	}
/**
 * 初期値を取得する
 * @return array
 */
	function getDefaultValue() {
		$data['Permission']['auth'] = 0;
		$data['Permission']['status'] = 1;
		return $data;
	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null) {

		$controlSources['user_group_id'] = $this->UserGroup->find('list',array('conditions'=>array('UserGroup.id <>'=>1)));
		$controlSources['auth'] = array('0'=>'不可','1'=>'可');
		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * beforeSave
 * @param array $options
 */
	function beforeSave($options) {
		if(isset($this->data['Permission'])) {
			$data = $this->data['Permission'];
		}else {
			$data = $this->data;
		}
		if(isset($data['url'])) {
			if(preg_match('/^[^\/]/is',$data['url'])) {
				$data['url'] = '/'.$data['url'];
			}
		}
		$this->data['Permission'] = $data;
		return true;
	}
/**
 * 権限チェックを行う
 * @param string $userGroupId
 * @param array $params
 */
	function check($url, $userGroupId) {

		if($this->permissions === -1) {
			$conditions = array('Permission.user_group_id' => $userGroupId);
			$permissions = $this->find('all',array('conditions'=>$conditions,'order'=>'sort','recursive'=>-1));
			if($permissions) {
				$this->permissions = $permissions;
			}else {
				$this->permissions = array();
				return true;
			}
		}

		$permissions = $this->permissions;

		if($url!='/') {
			$url = preg_replace('/^\//is', '', $url);
		}
		$ret = true;
		foreach($permissions as $permission) {
			if(!$permission['Permission']['status']) {
				continue;
			}
			if($permission['Permission']['url']!='/') {
				$pattern = preg_replace('/^\//is', '', $permission['Permission']['url']);
			}else {
				$pattern = $permission['Permission']['url'];
			}
			$pattern = addslashes($pattern);
			$pattern = str_replace('/', '\/', $pattern);
			$pattern = '/^'.str_replace('*', '.*?', $pattern).'$/is';
			if(preg_match($pattern, $url)) {
				$ret = $permission['Permission']['auth'];
			}
		}
		return $ret;

	}
}
?>