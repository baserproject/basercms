<?php


/**
 * テーマフォルダモデル
 * DB接続はしない
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
class ThemeFolder extends AppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'ThemeFolder';

/**
 * use table
 * 
 * @var boolean
 * @access public
 */
	public $useTable = false;

/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notEmpty'),
				'message' => 'テーマフォルダ名を入力してください。',
				'required' => true),
			array('rule' => array('halfText'),
				'message' => 'テーマフォルダ名は半角のみで入力してください。'),
			array('rule' => array('duplicateThemeFolder'),
				'message' => '入力されたテーマフォルダ名は、同一階層に既に存在します。')
		)
	);

/**
 * フォルダの重複チェック
 * 
 * @param array $check
 * @return boolean
 */
	public function duplicateThemeFolder($check) {
		if (!$check[key($check)]) {
			return true;
		}
		if ($check[key($check)] == $this->data['ThemeFolder']['pastname']) {
			return true;
		}
		$targetPath = $this->data['ThemeFolder']['parent'] . DS . $check[key($check)];
		if (is_dir($targetPath)) {
			return false;
		} else {
			return true;
		}
	}

}
