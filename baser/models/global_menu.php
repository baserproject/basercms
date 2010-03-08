<?php
/* SVN FILE: $Id$ */
/**
 * グローバルメニューモデル
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
 * グローバルメニューモデル
 *
 * @package			baser.models
 */
class GlobalMenu extends AppModel {
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
    var $useDbConfig = 'baser';
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'GlobalMenu';
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){

		$this->validate['name'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => ">> メニュー名を入力して下さい"));
		$this->validate['link'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => '>> リンクURLを入力して下さい'));
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
        
        $controlSources['menu_type'] = array('default'=>'公開ページ','admin'=>'管理画面');
		return $controlSources[$field];

	}
    
}
?>