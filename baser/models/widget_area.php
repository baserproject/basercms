<?php
/* SVN FILE: $Id$ */
/**
 * ウィジェットエリアモデル
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
 * ウィジェットエリアモデル
 *
 * @package baser.models
 */
class WidgetArea extends AppModel {
/**
 * クラス名
 * @var string
 * @access public
 */
	var $name = 'WidgetArea';
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
			'notEmpty' => array(
				'rule'		=> array('notEmpty'),
				'message'	=> 'ウィジェットエリア名を入力してください。'),
			'maxLength' => array(
				'rule'		=> array('maxLength', 255),
				'message'	=> 'ウィジェットエリア名は255文字以内で入力してください。'
			)
		)
	);
/**
 * コントロールソース取得
 * @param string $field
 * @return array
 * @access public
 */
	function getControlSource($field) {

		$controllSource['id'] = $this->find('list');
		if (isset($controllSource[$field])) {
			return $controllSource[$field];
		} else {
			return array();
		}
		
	}
	
}
?>