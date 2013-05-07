<?php
/* SVN FILE: $Id$ */
/**
 * モバイルコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * モバイルコントローラー
 *
 * @package baser.controllers
 */
class MobileController extends AppController {
/**
 * モデル
 *
 * @var array
 * @access	public
 */
	var $uses = null;
/**
 * モバイル GoogleAnalytics 用 ライブラリを読み込む
 * 
 * return void
 * access public
 */
	function mobile_ga() {
		
		if(empty($this->siteConfigs['google_analytics_id']) || !version_compare ( preg_replace('/[a-z-]/','', phpversion()), '5','>=')) {
			header("Content-Type: image/gif");
			header("Cache-Control: " .
				"private, no-cache, no-cache=Set-Cookie, proxy-revalidate");
			header("Pragma: no-cache");
			header("Expires: Wed, 17 Sep 1975 21:32:10 GMT");
			echo join(array(
				chr(0x47), chr(0x49), chr(0x46), chr(0x38), chr(0x39), chr(0x61),
				chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x80), chr(0xff),
				chr(0x00), chr(0xff), chr(0xff), chr(0xff), chr(0x00), chr(0x00),
				chr(0x00), chr(0x2c), chr(0x00), chr(0x00), chr(0x00), chr(0x00),
				chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x00), chr(0x02),
				chr(0x02), chr(0x44), chr(0x01), chr(0x00), chr(0x3b)
			));
			exit();
		}
		$_GET["utmac"] = str_replace('UA', 'MO', $this->siteConfigs['google_analytics_id']);
		App::import('Vendor', 'ga');
		exit();
		
	}
	
}
