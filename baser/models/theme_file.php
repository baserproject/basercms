<?php
/* SVN FILE: $Id$ */
/**
 * テーマファイルモデル
 * DBには接続しない
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.models
 * @since			Baser v 0.1.0
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
?>