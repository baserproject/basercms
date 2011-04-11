<?php
/* SVN FILE: $Id$ */
/**
 * 配列操作ヘルパー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.view.helpers
 * @since			Baser v 0.1.0
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
 * @package			baser.views.helpers
 */
class ArrayHelper extends AppHelper {
/**
 * 配列の最初の要素かどうか調べる
 *
 * @param 	array	配列
 * @param 	mixed	現在のキー
 * @return 	boolean
 * @access	public
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
 * @param 	array	配列
 * @param 	mixed	現在のキー
 * @return 	boolean
 * @access	public
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

}
?>