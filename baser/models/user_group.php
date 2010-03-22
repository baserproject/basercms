<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーグループモデル
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
 * ユーザーグループモデル
 *
 * @package			baser.models
 */
class UserGroup extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'UserGroup';
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
    var $useDbConfig = 'baser';
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){

		$this->validate['name'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => ">> グループ識別名を入力して下さい"),
										array('rule' => 'halfText',
											'message' => '>> グループ識別名は半角のみで入力して下さい'));
		$this->validate['title'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => ">> グループ名を入力して下さい"));
		return true;

	}
}
?>