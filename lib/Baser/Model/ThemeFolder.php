<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * テーマフォルダモデル
 *
 * @package Baser.Model
 */
class ThemeFolder extends AppModel {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'ThemeFolder';

/**
 * use table
 * 
 * @var boolean
 */
	public $useTable = false;

/**
 * バリデーション
 *
 * @var array
 */
	public $validate = [
		'name' => [
			['rule' => ['notBlank'],
				'message' => 'テーマフォルダ名を入力してください。',
				'required' => true],
			['rule' => ['halfText'],
				'message' => 'テーマフォルダ名は半角のみで入力してください。'],
			['rule' => ['duplicateThemeFolder'],
				'message' => '入力されたテーマフォルダ名は、同一階層に既に存在します。']
		]
	];

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
