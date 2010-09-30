<?php
/* SVN FILE: $Id$ */
/**
 * テーマフォルダモデル
 * DB接続はしない
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
class ThemeFolder extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'ThemeFolder';
/**
 * use table
 * @var		boolean
 * @access	public
 */
	var $useTable = false;
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(array(	'rule' => array('minLength',1),
						'message' => "テーマフォルダ名を入力して下さい。",
						'required' => true),
				array(	'rule' => 'halfText',
						'message' => 'テーマフォルダ名は半角のみで入力して下さい'),
				array(  'rule' => array('duplicateThemeFolder'),
						'message' => '入力されたテーマフォルダ名は、同一階層に既に存在します'));
	}
/**
 * フォルダの重複チェック
 * @param	array	$check
 * @return	boolean
 */
	function duplicateThemeFolder ($check) {

		if(!$check[key($check)]) {
			return true;
		}
		if($check[key($check)] == $this->data['ThemeFolder']['pastname']) {
			return true;
		}
		$targetPath = $this->data['ThemeFolder']['parent'].DS.$check[key($check)];
		if(is_dir($targetPath)) {
			return false;
		}else {
			return true;
		}

	}
}
?>