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

App::uses('HtmlHelper', 'View/Helper');

/**
 * Htmlヘルパーの拡張クラス
 *
 * @package Baser.View.Helper
 */
class BcHtmlHelper extends HtmlHelper
{

// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>
	/**
	 * Included helpers.
	 *
	 * @var array
	 */
	public $helpers = ['Js'];

	/**
	 * タグにラッピングされていないパンくずデータを取得する
	 * @return array
	 */
	public function getStripCrumbs()
	{
		return $this->_crumbs;
	}

	/**
	 * JavaScript に変数を引き渡す
	 *
	 * @param string $variable 変数名（グローバル変数）
	 * @param array $value 値（連想配列）
	 *  - `inline` : インラインに出力するかどうか。（初期値 : false）
	 *  - `declaration` : var 宣言を行うかどうか（初期値 : true）
	 *  - `escape` : 値のエスケープを行うかどうか（初期値 : true）
	 */
	public function setScript($variable, $value, $options = [])
	{
		$options = array_merge([
			'inline' => false,
			'declaration' => true,
			'escape' => true
		], $options);
		$code = '';
		if ($options['declaration']) {
			$code = 'var ';
		}
		if ($options['escape']) {
			$value = h($value);
		}
		$code .= h($variable) . ' = ' . json_encode($value) . ';';
		unset($options['declaration'], $options['escape']);
		$result = $this->scriptBlock($code, $options);
		if ($options['inline']) {
			return $result;
		}
		return '';
	}

	/**
	 * i18n 用の変数を宣言する
	 * @return string
	 */
	public function declarationI18n()
	{
		return $this->setScript('bcI18n', [], ['inline' => true]);
	}

	/**
	 * JavaScript に、翻訳データを引き渡す
	 * `bcI18n.キー名` で参照可能
	 * （例）bcI18n.alertMessage
	 *
	 * @param array $value 値（連想配列）
	 *  - `inline` : インラインに出力するかどうか。（初期値 : false）
	 *  - `declaration` : var 宣言を行うかどうか（初期値 : false）
	 *  - `escape` : 値のエスケープを行うかどうか（初期値 : true）
	 */
	public function i18nScript($data, $options = [])
	{
		$options = array_merge([
			'inline' => false,
			'declaration' => false,
			'escape' => true
		], $options);
		if (is_array($data)) {
			$result = '';
			foreach($data as $key => $value) {
				$result .= $this->setScript('bcI18n.' . $key, $value, $options) . "\n";
			}
			if ($options['inline']) {
				return $result;
			}
		}
		return '';
	}
// <<<
}
