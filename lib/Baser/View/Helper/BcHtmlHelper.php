<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('HtmlHelper', 'View/Helper');

/**
 * Htmlヘルパーの拡張クラス
 *
 * @package Baser.View.Helper
 */
class BcHtmlHelper extends HtmlHelper {
	
// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>
/**
 * Included helpers.
 *
 * @var array
 */
	public $helpers = array('Js');

/**
 * タグにラッピングされていないパンくずデータを取得する
 * @return array
 */
	public function getStripCrumbs() {
		return $this->_crumbs;
	}
// <<<
}
