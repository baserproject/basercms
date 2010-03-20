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
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){

		$this->validate['name'] = array(array(	'rule' => VALID_NOT_EMPTY,
												'message' => ">> 設定名を入力して下さい"));
		$this->validate['user_group_id'] =	array(array('rule' => VALID_NOT_EMPTY,
														'message' => ">> ユーザーグループを選択して下さい"));
		$this->validate['url'] = array(	array(	'rule' => VALID_NOT_EMPTY,
													'message' => ">> 設定を入力して下さい"),
											array(	'rule' => 'checkUrl',
													'message' => '>> アクセス拒否として設定できるのは認証ページだけです。'));
		return true;

	}
/**
 * 設定をチェックする
 *
 * @return boolean True if the operation should continue, false if it should abort
 * @access public
 */
	function checkUrl($check){

		if(!$check[key($check)]){
			return true;
		}

		$params = Router::parse($check[key($check)]);
		if(empty($params['prefix'])){
			$this->invalidate('setting','>> アクセス拒否として設定できるのは認証ページだけです。');
			return false;
		}

		return true;
		
	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null){

		$controlSources['user_group_id'] = $this->UserGroup->find('list');

		if(isset($controlSources[$field])){
			return $controlSources[$field];
		}else{
			return false;
		}

	}
}
?>