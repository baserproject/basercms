<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * XMLヘルパー拡張
 *
 * @package Baser.View.Helper
 */
class BcXmlHelper extends AppHelper
{

	/**
	 * XML document version
	 *
	 * @var string
	 */
	private $version = '1.0';

	/**
	 * XML document encoding
	 *
	 * @var string
	 */
	private $encoding = 'UTF-8';

	/**
	 * XML宣言を生成
	 * IE6以外の場合のみ生成する
	 *
	 * @param array $attrib
	 * @return string XML宣言
	 */
	public function header($attrib = [])
	{
		$ua = "";
		if (!empty($_SERVER['HTTP_USER_AGENT'])) {
			$ua = $_SERVER['HTTP_USER_AGENT'];
		}
		if (!(preg_match("/Windows/", $ua) && preg_match("/MSIE/", $ua)) || !(preg_match("/MSIE 6/", $ua))) {
			if (Configure::read('App.encoding') !== null) {
				$this->encoding = Configure::read('App.encoding');
			}

			if (is_array($attrib)) {
				$attrib = array_merge(['encoding' => $this->encoding], $attrib);
			}
			if (is_string($attrib) && strpos($attrib, 'xml') !== 0) {
				$attrib = 'xml ' . $attrib;
			}

			$header = 'xml';
			if (is_string($attrib)) {
				$header = $attrib;
			} else {

				$attrib = array_merge(['version' => $this->version, 'encoding' => $this->encoding], $attrib);
				foreach($attrib as $key => $val) {
					$header .= ' ' . $key . '="' . $val . '"';
				}
			}
			$out = '<' . '?' . $header . ' ?' . '>';

			return $out;
		}
	}

}
