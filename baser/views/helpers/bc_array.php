<?php
/* SVN FILE: $Id$ */
/**
 * 配列操作ヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.view.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Helper', 'Form');
/**
 * ArrayHelper
 *
 * @package baser.views.helpers
 */
class BcArrayHelper extends AppHelper {
/**
 * 配列の最初の要素かどうか調べる
 *
 * @param array $array 配列
 * @param mixed $key 現在のキー
 * @return boolean
 * @access public
 */
	function first($array, $key) {

		reset($array);
		$first = key($array);
		if($key===$first) {
			return true;
		}else {
			return false;
		}

	}
/**
 * 配列の最後の要素かどうか調べる
 *
 * @param array $array 配列
 * @param mixed $key 現在のキー
 * @return boolean
 * @access public
 */
	function last($array, $key) {

		end($array);
		$end = key($array);
		if($key===$end) {
			return true;
		}else {
			return false;
		}

	}
/**
 * 配列にテキストを追加する
 *
 * @param	array	$array
 * @param	string	$prefix
 * @param	string	$suffix
 * @return	array
 * @access	public
 */
	function addText($array, $prefix = '', $suffix = '') {
		if($prefix || $suffix) {
			array_walk($array, array($this, '__addText'), $prefix.','.$suffix);
		}
		return $array;
	}
/**
 * addTextToArrayのコールバックメソッド
 *
 * @param	string	$value
 * @param	string	$key
 * @param	string	$add
 * @return	string
 * @access	private
 */
	function __addText(&$value, $key, $add) {
		if($add) {
			list($prefix, $suffix) = explode(',',$add);
		}
		$value = $prefix.$value.$suffix;
	}
	
}