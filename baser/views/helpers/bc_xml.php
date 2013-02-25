<?php
/* SVN FILE: $Id$ */
/**
 * XMLヘルパー拡張
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
App::import("Helper","Xml");
/**
 * XMLヘルパー拡張
 *
 * @package baser.views.helpers
 */
class BcXmlHelper extends XmlHelper {
/**
 * XML宣言を生成
 * IE6以外の場合のみ生成する
 * 
 * @param array $attrib
 * @return string XML宣言
 */
	function header($attrib = array()) {

		$ua = @$_SERVER['HTTP_USER_AGENT'];
		if (!(ereg("Windows",$ua) && ereg("MSIE",$ua)) || ereg("MSIE 7",$ua)) {
			return parent::header($attrib);
		}

	}
	
}