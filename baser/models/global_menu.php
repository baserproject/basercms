<?php
/* SVN FILE: $Id$ */
/**
 * グローバルメニューモデル
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
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
 * @package baser.models
 */
class GlobalMenu extends AppModel {
/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'baser';
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'GlobalMenu';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('Cache');
/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'メニュー名を入力してください。'),
			array(	'rule'		=> array('maxLength', 20),
					'message'	=> 'メニュー名は20文字以内で入力してください。')
		),
		'link' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'リンクURLを入力してください。'),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'リンクURLは255文字以内で入力してください。')
		)
	);
/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access public
 */
	function getControlSource($field = null) {

		$controlSources['menu_type'] = array('default'=>'公開ページ','admin'=>'管理画面');
		return $controlSources[$field];

	}

}
?>