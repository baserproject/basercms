<?php
/* SVN FILE: $Id$ */
/**
 * XMLヘルパー拡張
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
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
/**
 * XMLヘルパー拡張
 *
 * @package baser.views.helpers
 */
class BcXmlHelper extends AppHelper {
/**
 * XML document version
 *
 * @var string
 * @access private
 */
	private $version = '1.0';

/**
 * XML document encoding
 *
 * @var string
 * @access private
 */
	private $encoding = 'UTF-8';
/**
 * XML宣言を生成
 * IE6以外の場合のみ生成する
 * 
 * @param array $attrib
 * @return string XML宣言
 */
	public function header($attrib = array()) {

		$ua = @$_SERVER['HTTP_USER_AGENT'];
		if (!(ereg("Windows",$ua) && ereg("MSIE",$ua)) || ereg("MSIE 7",$ua)) {
			
			if (Configure::read('App.encoding') !== null) {
				$this->encoding = Configure::read('App.encoding');
			}

			if (is_array($attrib)) {
				$attrib = array_merge(array('encoding' => $this->encoding), $attrib);
			}
			if (is_string($attrib) && strpos($attrib, 'xml') !== 0) {
				$attrib = 'xml ' . $attrib;
			}

			$header = 'xml';
			if (is_string($attrib)) {
				$header = $attrib;
			} else {

				$attrib = array_merge(array('version' => $this->version, 'encoding' => $this->encoding), $attrib);
				foreach ($attrib as $key=>$val) {
					$header .= ' ' . $key . '="' . $val . '"';
				}
			}
			$out = '<' . '?' . $header . ' ?' . '>';

			return $out;
		
		}

	}
	
}
?>