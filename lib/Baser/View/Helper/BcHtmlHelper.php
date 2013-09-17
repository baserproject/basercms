<?php
/* SVN FILE: $Id$ */
/**
 * Htmlヘルパーの拡張クラス
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
App::uses('HtmlHelper', 'View/Helper');
/**
 * Htmlヘルパーの拡張クラス
 *
 * @package baser.views.helpers
 */
class BcHtmlHelper extends HtmlHelper {

/**
 * Included helpers.
 *
 * @var array
 * @access public
 */
	public $helpers = array('Js');
/**
 * タグにラッピングされていないパンくずデータを取得する
 * @return array
 */
	public function getStripCrumbs() {
		return $this->_crumbs;
	}
}
