<?php
/* SVN FILE: $Id$ */
/**
 * ウィジェットエリアモデル
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
 * ウィジェットエリアモデル
 *
 * @package			baser.models
 */
class WidgetArea extends AppModel {
/**
 * クラス名
 * @var		string
 * @access	public
 */
	var $name = 'WidgetArea';
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(array(	'rule' => VALID_NOT_EMPTY,
						'message' => "ウィジェットエリア名を入力して下さい"));
	}
/**
 * コントロールソース取得
 * @param	string	$field
 * @return	array
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