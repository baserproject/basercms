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
 * テーマファイルモデル
 *
 * @package Baser.Model
 */
class ThemeFile extends AppModel {

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
				'message' => "テーマファイル名を入力してください。",
				'required' => true],
			['rule' => ['duplicateThemeFile'],
				'message' => '入力されたテーマファイル名は、同一階層に既に存在します。']
		]
	];

/**
 * ファイルの重複チェック
 * 
 * @param	array	$check
 * @return	boolean
 */
	public function duplicateThemeFile($check) {
		if (!$check[key($check)]) {
			return true;
		}
		$targetPath = $this->data['ThemeFile']['parent'] . $check[key($check)] . '.' . $this->data['ThemeFile']['ext'];
		if (is_file($targetPath)) {
			return false;
		} else {
			return true;
		}
	}

}
