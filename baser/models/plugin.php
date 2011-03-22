<?php
/* SVN FILE: $Id$ */
/**
 * プラグインモデル
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
 * プラグインモデル
 *
 * @package			baser.models
 */
class Plugin extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'Plugin';
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
	var $useDbConfig = 'baser';
/**
 * バリデーション
 *
 * @var		array
 * @access	public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('alphaNumericPlus'),
					'message'	=> 'プラグイン名は半角英数字、ハイフン、アンダースコアのみで入力してください。',
					'reqquired'	=> true),
			array(	'rule'		=> array('isUnique'),
					'on'		=> 'create',
					'message'	=>	'指定のプラグインは既に使用されています。'),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'プラグイン名は50文字以内としてください。')
		),
		'title' => array(
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'プラグインタイトルは50文字以内としてください。')
		)
	);
/**
 * データベースを初期化する
 *
 * 既存のテーブルは上書きしない
 *
 * @param	string	$plugin
 * @return	boolean
 * @access	public
 */
	function initDb($plugin) {
		return parent::initDb('plugin', $plugin);
	}
}
?>