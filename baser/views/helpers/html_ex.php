<?php
/* SVN FILE: $Id$ */
/**
 * Htmlヘルパーの拡張クラス
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
App::import('Helper', 'Html');
/**
 * Htmlヘルパーの拡張クラス
 *
 * @package			baser.views.helpers
 */
class HtmlExHelper extends HtmlHelper {
/**
 * Included helpers.
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Javascript');
/**
 * 画像の即時反映用のパラメータとして乱数を出力
 *
 * @return	int		乱数
 * @access	public
 */
	function rand() {
		return rand();
	}

}
?>