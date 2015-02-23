<?php

/**
 * フィード設定モデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Feed.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('FeedAppModel', 'Feed.Model');

/**
 * フィード設定モデル
 *
 * @package Feed.Model
 */
class FeedConfig extends FeedAppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'FeedConfig';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

/**
 * DB設定
 * @var string
 * @access public
 */
	public $useDbConfig = 'plugin';

/**
 * hasMany
 *
 * @var array
 * @access public
 */
	public $hasMany = array("FeedDetail" =>
		array("className" => "Feed.FeedDetail",
			"conditions" => "",
			"order" => "FeedDetail.id ASC",
			"foreignKey" => "feed_config_id",
			"dependent" => true,
			"exclusive" => false,
			"finderQuery" => ""));

/**
 * validate
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notEmpty'),
				'message' => 'フィード設定名を入力してください。',
				'required' => true),
			array('rule' => array('maxLength', 50),
				'message' => 'フィード設定名は50文字以内で入力してください。')
		),
		'feed_title_index' => array(
			array('rule' => array('maxLength', 255),
				'message' => 'フィードタイトルリストは255文字以内で入力してください。')
		),
		'category_index' => array(
			array('rule' => array('maxLength', 255),
				'message' => 'カテゴリリストは255文字以内で入力してください。')
		),
		'display_number' => array(
			array('rule' => 'numeric',
				'message' => '数値を入力してください。',
				'required' => true)
		),
		'template' => array(
			array('rule' => array('notEmpty'),
				'message' => 'テンプレート名を入力してください。'),
			array('rule' => 'halfText',
				'message' => 'テンプレート名は半角のみで入力してください。'),
			array('rule' => array('maxLength', 50),
				'message' => 'テンプレート名は50文字以内で入力してください。')
		)
	);

/**
 * 初期値を取得
 * 
 * @return array
 * @access public
 */
	public function getDefaultValue() {
		$data['FeedConfig']['display_number'] = '5';
		$data['FeedConfig']['template'] = 'default';
		return $data;
	}

}
