<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('HtmlHelper', 'View/Helper');

/**
 * Htmlヘルパーの拡張クラス
 *
 * @package Baser.View.Helper
 */
class BcHtmlHelper extends HtmlHelper {
	
// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>
/**
 * Included helpers.
 *
 * @var array
 */
	public $helpers = ['Js'];

/**
 * BcHtmlHelper constructor.
 *
 * @param \View $View
 * @param array $settings
 */
	public function __construct(\View $View, array $settings = []) {
		parent::__construct($View, $settings);
		$this->scriptBlock('var bcI18n = {}', ['inline' => false]);
	}
	
/**
 * タグにラッピングされていないパンくずデータを取得する
 * @return array
 */
	public function getStripCrumbs() {
		return $this->_crumbs;
	}

/**
 * JavaScript に変数を引き渡す
 *
 * @param string $variable 変数名（グローバル変数）
 * @param array $value 値（連想配列）
 */
	public function setScript($variable, $value, $options = []) {
		$options = array_merge(['inline' => false], $options);
		$code = h($variable) . ' = ' . json_encode(h($value));
		$result = $this->scriptBlock($code, $options);
		if($options['inline']) {
			return $result;
		}
		return '';
	}

/**
 * JavaScript に、翻訳データを引き渡す
 * `bcI18n.キー名` で参照可能
 * （例）bcI18n.alertMessage
 *
 * @param array $value 値（連想配列）
 */
	public function i18nScript($data, $options = []) {
		$options = array_merge(['inline' => false], $options);
		if(is_array($data)) {
			$result = '';
			foreach($data as $key => $value) {
				$result .= $this->setScript('bcI18n.' . $key, $value, $options) . "\n";
			}
			if($options['inline']) {
				return $result;
			}
		}
		return '';
	}
// <<<
}
