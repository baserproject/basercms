<?php


/**
 * テーマファイルモデル
 * DBには接続しない
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
class ThemeFile extends AppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'ThemeFile';

/**
 * use table
 * 
 * @var boolean
 * @access	public
 */
	public $useTable = false;

/**
 * バリデーション
 *
 * @var array
 * @access	public
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notEmpty'),
				'message' => "テーマファイル名を入力してください。",
				'required' => true),
			array('rule' => array('duplicateThemeFile'),
				'message' => '入力されたテーマファイル名は、同一階層に既に存在します。')
		)
	);

/**
 * ファイルの重複チェック
 * 
 * @param	array	$check
 * @return	boolean
 * @access public
 */
	public function duplicateThemeFile($check) {
		if (!$check[key($check)]) {
			return true;
		}
		$targetPath = $this->data['ThemeFile']['parent'] . DS . $check[key($check)];
		if (is_file($targetPath)) {
			return false;
		} else {
			return true;
		}
	}

}
