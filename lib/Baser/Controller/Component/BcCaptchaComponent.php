<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller.Component
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::import('Vendor', 'kcaptcha/kcaptcha');

/**
 * Class BcCaptchaComponent
 *
 * キャプチャコンポーネント
 *
 * @package Baser.Controller.Component
 */
class BcCaptchaComponent extends Component
{

	/**
	 * Vendorsフォルダのパス
	 * kcaptchaの設定ファイルを読み込む為に利用
	 *
	 * @var string
	 */
	public $vendorsPath = BASER_VENDORS;

	/**
	 * アルファベットの組み合わせ（半角記号も可）
	 * kcaptcha_config.php で設定されたものを読み込む為に利用
	 *
	 * @var string
	 */
	public $alphabet = '';

	/**
	 * 代替文字の組み合わせ
	 * kcaptcha_config.php で設定されたものを読み込む為に利用
	 *
	 * @var string
	 */
	public $convert = '';

	/**
	 * startup
	 *
	 * @param Controller $controller
	 * @return void
	 */
	public function startup(Controller $controller)
	{
		$this->controller = $controller;
	}

	/**
	 * キャプチャ画象を表示する
	 *
	 * @return void
	 */
	public function render($token = null)
	{
		$kcaptcha = new KCAPTCHA();
		$key = 'captcha';
		if (!$token) {
			$token = '0';
		}
		$key .= '.' . $token;
		$this->controller->Session->write($key, $kcaptcha->getKeyString());
	}

	/**
	 * 認証を行う
	 *
	 * @param string $value フォームから送信された文字列
	 * @return    boolean
	 */
	public function check($value, $token = null)
	{
		include $this->vendorsPath . 'kcaptcha/kcaptcha_config.php';
		$this->alphabet = $alphabet;
		$this->convert = $convert;
		$key = 'captcha';
		if (!$token) {
			$token = '0';
		}
		$key .= '.' . $token;
		$_value = $this->convert($this->controller->Session->read($key));
		if (!$_value) {
			return false;
		} else {
			return ($value == $_value);
		}
	}

	/**
	 * kcaptchaで定義されたアルファベットを $convert に定義された任意の文字列に変換する
	 *
	 * @param string $key
	 * @return    string
	 * @access    public
	 */
	public function convert($key)
	{
		$alphabets = $this->strSplit($this->alphabet);
		$converts = $this->strSplit($this->convert);

		$value = '';
		$keys = $this->strSplit($key);
		foreach($keys as $key) {
			$idx = array_search($key, $alphabets);
			if ($idx === false) {
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
	 * @param string $str
	 * @return    array
	 * @access    public
	 */
	public function strSplit($str)
	{
		$arr = [];
		if (is_string($str)) {
			$len = mb_strlen($str, 'UTF-8');
			for($i = 0; $i < $len; $i++) {
				array_push($arr, mb_substr($str, $i, 1, 'UTF-8'));
			}
		}
		return $arr;
	}

}
