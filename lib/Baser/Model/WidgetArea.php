<?php

/**
 * ウィジェットエリアモデル
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
/**
 * Include files
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
 * @access public
 */
	public $name = 'WidgetArea';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'ウィジェットエリア名を入力してください。'),
			'maxLength' => array(
				'rule' => array('maxLength', 255),
				'message' => 'ウィジェットエリア名は255文字以内で入力してください。'
			)
		)
	);

/**
 * コントロールソース取得
 * @param string $field
 * @return array
 * @access public
 */
	public function getControlSource($field) {
		$controllSource['id'] = $this->find('list');
		if (isset($controllSource[$field])) {
			return $controllSource[$field];
		} else {
			return array();
		}
	}

}
