<?php
/* SVN FILE: $Id$ */
/**
 * キャプチャコンポーネント
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.models
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class BcCaptchaComponent extends Object {
/**
 * Vendorsフォルダのパス
 * kcaptchaの設定ファイルを読み込む為に利用
 *
 * @var string
 * @access public
 */
	var $vendorsPath = BASER_VENDORS;
/**
 * アルファベットの組み合わせ（半角記号も可）
 * kcaptcha_config.php で設定されたものを読み込む為に利用
 * 
 * @var string
 * @access public
 */
	var $alphabet = '';
/**
 * 代替文字の組み合わせ
 * kcaptcha_config.php で設定されたものを読み込む為に利用
 * 
 * @var string
 * @access public
 */
	var $convert = '';
/**
 * startup
 * 
 * @param Controller $controller
 * @return void
 * @access public
 */
	function startup($controller) {
		
		$this->controller =& $controller;
	
	}
/**
 * キャプチャ画象を表示する
 * 
 * @return void
 * @access public
 */
	function render()
	{
		
		App::import('Vendor','kcaptcha/kcaptcha');
		$kcaptcha = new KCAPTCHA();
		$this->controller->Session->write('captcha', $kcaptcha->getKeyString());
		
	}
/**
 * 認証を行う
 * 
 * @param	string	$value	フォームから送信された文字列
 * @return	boolean
 * @access public
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
 * 
 * @param	string	$key
 * @return	string
 * @access	public
 */
	function convert($key){

		$alphabets = $this->strSplit($this->alphabet);
		$converts = $this->strSplit($this->convert);

		$value = '';
		$keys = $this->strSplit($key);
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
/**
 * 文字列を１文字づつ分割して配列にする
 * PHP5であれば、str_splitが使える
 * 
 * @param	string	$str
 * @return	array
 * @access	public
 */
	function strSplit($str) {
		
		$arr = array();
		if (is_string($str)) {
			for ($i = 0; $i < mb_strlen($str,'UTF-8'); $i++) {
				array_push($arr, mb_substr($str, $i, 1, 'UTF-8'));
			}
		}
		return $arr;
		
	}
	
}
