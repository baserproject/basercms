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
 * ウィジェットエリアモデル
 *
 * @package Baser.Model
 */
class WidgetArea extends AppModel {

/**
 * クラス名
 * @var string
 */
	public $name = 'WidgetArea';

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = ['BcCache'];

/**
 * バリデーション
 *
 * @var array
 */
	public $validate = [
		'name' => [
			'notBlank' => [
				'rule' => ['notBlank'],
				'message' => 'ウィジェットエリア名を入力してください。'],
			'maxLength' => [
				'rule' => ['maxLength', 255],
				'message' => 'ウィジェットエリア名は255文字以内で入力してください。'
			]
		]
	];

/**
 * コントロールソース取得
 * @param string $field
 * @return array
 */
	public function getControlSource($field) {
		$controllSource['id'] = $this->find('list');
		if (isset($controllSource[$field])) {
			return $controllSource[$field];
		} else {
			return [];
		}
	}

}
