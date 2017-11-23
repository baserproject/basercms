<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('ga', 'Vendor');

/**
 * モバイルコントローラー
 *
 * @package Baser.Controller
 */
class MobileController extends AppController {

/**
 * モデル
 *
 * @var array
 * @access	public
 */
	public $uses = null;

/**
 * モバイル GoogleAnalytics 用 ライブラリを読み込む
 * 
 * return void
 * access public
 */
	public function ga () {
		if (empty($this->siteConfigs['google_analytics_id']) || !version_compare(preg_replace('/[a-z-]/', '', phpversion()), '5', '>=')) {
			header("Content-Type: image/gif");
			header("Cache-Control: " .
				"private, no-cache, no-cache=Set-Cookie, proxy-revalidate");
			header("Pragma: no-cache");
			header("Expires: Wed, 17 Sep 1975 21:32:10 GMT");
			echo join([
				chr(0x47), chr(0x49), chr(0x46), chr(0x38), chr(0x39), chr(0x61),
				chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x80), chr(0xff),
				chr(0x00), chr(0xff), chr(0xff), chr(0xff), chr(0x00), chr(0x00),
				chr(0x00), chr(0x2c), chr(0x00), chr(0x00), chr(0x00), chr(0x00),
				chr(0x01), chr(0x00), chr(0x01), chr(0x00), chr(0x00), chr(0x02),
				chr(0x02), chr(0x44), chr(0x01), chr(0x00), chr(0x3b)
			]);
			exit();
		}
		$_GET["utmac"] = str_replace('UA', 'MO', $this->siteConfigs['google_analytics_id']);
		exit();
	}
	
/**
 * モバイル GoogleAnalytics 用 ライブラリを読み込む
 * 
 * return void
 * access public
 */
	public function mobile_ga() {
		$this->setAction('ga');
	}

}
