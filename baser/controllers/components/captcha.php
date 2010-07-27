<?php
/* SVN FILE: $Id$ */
/**
 * キャプチャコンポーネント
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.feed.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class CaptchaComponent extends Object {
/**
 * Vendorsフォルダのパス
 * kcaptchaの設定ファイルを読み込む為に利用
 * @var		string
 * @access	public
 */
	var $vendorsPath = BASER_VENDORS;
/**
 * アルファベットの組み合わせ（半角記号も可）
 * kcaptcha_config.php で設定されたものを読み込む為に利用
 * @var		string
 * @access	public
 */
	var $alphabet = '';
/**
 * 代替文字の組み合わせ
 * kcaptcha_config.php で設定されたものを読み込む為に利用
 * @var		string
 * @access	public
 */
	var $convert = '';
/**
 * startup
 * @param	Controller $controller
 * @access	public
 */
	function startup(&$controller) {
        $this->controller = $controller;
	}
/**
 * キャプチャ画象を表示する
 * @return	void
 * @access	public
 */
	function render()
	{
		App::import('Vendor','kcaptcha/kcaptcha');
		$kcaptcha = new KCAPTCHA();
		$this->controller->Session->write('captcha', $kcaptcha->getKeyString());
	}
/**
 * 認証を行う
 * @param	string	$value	フォームから送信されたもじれる
 * @return	boolean
 */
	function check($value){
		include $this->vendorsPath.'kcaptcha/kcaptcha_config.php';
		$this->alphabet = $alphabet;
		$this->convert = $convert;
		$_value = $this->convert($this->controller->Session->read('captcha'));
		if(!$_value){
			return false;
		}else{
			return ($value == $_value);
		}
	}
/**
 * kcaptchaで定義されたアルファベットを $convert に定義された任意の文字列に変換する
 * @param	string	$key
 * @return	string
 * @access	public
 */
	function convert($key){
		$alphabets = str_split($this->alphabet);
		
		$converts = array();
		for($i=0;$i<mb_strlen( $this->convert ,'UTF-8' );$i++){
			$converts[] = mb_substr( $this->convert , $i, 1,"UTF-8" );
		}

		$value = '';
		$keys = str_split($key);
		foreach ($keys as $key){
			$idx = array_search($key, $alphabets);
			if($idx === false){
				return false;
			} else {
				$value .= $converts[$idx];
			}
		}
		return $value;
	}
}
?>