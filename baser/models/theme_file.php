<?php
/* SVN FILE: $Id$ */
/**
 * テーマファイルモデル
 * DBには接続しない
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
class ThemeFile extends AppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'ThemeFile';
/**
 * use table
 * 
 * @var boolean
 * @access	public
 */
	var $useTable = false;
/**
 * バリデーション
 *
 * @var array
 * @access	public
 */
	var $validate = array(
			'name' => array(
				array(	'rule'		=> array('notEmpty'),
						'message'	=> "テーマファイル名を入力してください。",
						'required'	=> true),
				array(  'rule'		=> array('duplicateThemeFile'),
						'message'	=> '入力されたテーマファイル名は、同一階層に既に存在します。')
			)
	);
/**
 * フォルダの重複チェック
 * 
 * @param	array	$check
 * @return	boolean
 * @access public
 */
	function duplicateThemeFile ($check) {

		if(!$check[key($check)]) {
			return true;
		}
		$targetPath = $this->data['ThemeFile']['parent'].DS.$check[key($check)];
		if(is_dir($targetPath)) {
			return false;
		}else {
			return true;
		}

	}
	
}
