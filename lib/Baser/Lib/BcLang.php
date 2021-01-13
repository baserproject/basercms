<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Lib
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('BcAbstractDetector', 'Lib');

/**
 * Class BcLang
 *
 * @package Baser.Lib
 */
class BcLang extends BcAbstractDetector
{

	/**
	 * 検出器タイプ
	 *
	 * @var string
	 */
	public $type = 'lang';

	/**
	 * 設定ファイルのキー名
	 *
	 * @var string
	 */
	protected static $_configName = 'BcLang';

	/**
	 * 設定
	 *
	 * @param array $config 設定の配列
	 * @return void
	 */
	protected function _setConfig(array $config)
	{
		$this->decisionKeys = $config['langs'];
	}

	/**
	 * デフォルトの設定値を取得
	 *
	 * @return array
	 */
	protected function _getDefaultConfig()
	{
		return [
			'langs' => []
		];
	}

	/**
	 * 判定用正規表現を取得
	 *
	 * @return string
	 */
	public function getDetectorRegex()
	{
		$regex = '/' . str_replace('\|\|', '|', preg_quote(implode('||', $this->decisionKeys), '/')) . '/i';
		return $regex;
	}

	/**
	 * キーワードを含むかどうかを判定
	 *
	 * @return bool
	 */
	public function isMatchDecisionKey()
	{
		$key = self::parseLang((isset($_SERVER['HTTP_ACCEPT_LANGUAGE']))? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null);
		$regex = $this->getDetectorRegex();
		return (bool)preg_match($regex, $key);
	}

	/**
	 * 言語データを解析する
	 *
	 * 最優先の言語のみ対応
	 *
	 * @param $acceptLanguage
	 * @return array|string
	 */
	static public function parseLang($acceptLanguage)
	{
		if (!$acceptLanguage) {
			return 'ja';
		}
		$keys = explode(',', $acceptLanguage);
		$langs = [];
		if ($keys) {
			foreach($keys as $key) {
				list($lang) = explode(';', $key);
				$lang = preg_replace('/-.*$/', '', $lang);
				if (!in_array($lang, $langs)) {
					$langs[] = $lang;
				}
			}
		}
		if (!$langs) {
			$langs = ['ja'];
		}
		return $langs[0];
	}
}
