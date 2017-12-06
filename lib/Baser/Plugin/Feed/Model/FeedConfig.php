<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('FeedAppModel', 'Feed.Model');

/**
 * フィード設定モデル
 *
 * @package Feed.Model
 */
class FeedConfig extends FeedAppModel {

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = ['BcCache'];

/**
 * hasMany
 *
 * @var array
 */
	public $hasMany = ["FeedDetail" =>
		["className" => "Feed.FeedDetail",
			"conditions" => "",
			"order" => "FeedDetail.id ASC",
			"foreignKey" => "feed_config_id",
			"dependent" => true,
			"exclusive" => false,
			"finderQuery" => ""
		]];

/**
 * validate
 *
 * @var array
 */
	public $validate = [
		'name' => [
			['rule' => ['notBlank'],
				'message' => 'フィード設定名を入力してください。',
				'required' => true],
			['rule' => ['maxLength', 50],
				'message' => 'フィード設定名は50文字以内で入力してください。']
		],
		'feed_title_index' => [
			['rule' => ['maxLength', 255],
				'message' => 'フィードタイトルリストは255文字以内で入力してください。']
		],
		'category_index' => [
			['rule' => ['maxLength', 255],
				'message' => 'カテゴリリストは255文字以内で入力してください。']
		],
		'display_number' => [
			['rule' => 'numeric',
				'message' => '数値を入力してください。',
				'required' => true]
		],
		'template' => [
			['rule' => ['notBlank'],
				'message' => 'テンプレート名を入力してください。'],
			['rule' => 'halfText',
				'message' => 'テンプレート名は半角のみで入力してください。'],
			['rule' => ['maxLength', 50],
				'message' => 'テンプレート名は50文字以内で入力してください。']
		]
	];

/**
 * 初期値を取得
 * 
 * @return array
 */
	public function getDefaultValue() {
		$data['FeedConfig']['display_number'] = '5';
		$data['FeedConfig']['template'] = 'default';
		return $data;
	}

}
