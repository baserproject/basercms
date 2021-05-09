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
 * スマホヘルパー
 *
 * @package Baser.View.Helper
 */
class BcSmartphoneHelper extends Helper
{

	/**
	 * ヘルパ
	 *
	 * @var array
	 */
	public $helpers = ['BcHtml'];

	/**
	 * afterLayout
	 *
	 * @return void
	 */
	public function afterLayout($layoutFile)
	{

		if (isset($this->request->params['ext']) && $this->request->params['ext'] == 'rss') {
			$rss = true;
		} else {
			$rss = false;
		}
		$site = BcSite::findCurrent();
		if (!$rss && $site->device == 'smartphone' && $this->_View->layoutPath != 'Emails' . DS . 'text') {
			if (empty($this->request->params['Site'])) {
				return;
			}
			// 内部リンクの自動変換
			if ($site->autoLink) {
				$siteUrl = Configure::read('BcEnv.siteUrl');
				$sslUrl = Configure::read('BcEnv.sslUrl');
				$currentAlias = $this->request->params['Site']['alias'];
				$regBaseUrls = [
					preg_quote(BC_BASE_URL, '/'),
					preg_quote(preg_replace('/\/$/', '', $siteUrl) . BC_BASE_URL, '/'),
				];
				if ($sslUrl) {
					$regBaseUrls[] = preg_quote(preg_replace('/\/$/', '', $sslUrl) . BC_BASE_URL, '/');
				}
				$regBaseUrl = implode('|', $regBaseUrls);

				// 一旦プレフィックスを除外
				$reg = '/<a([^<]*?)href="((' . $regBaseUrl . ')(' . $currentAlias . '\/([^\"]*?)))\"/';
				$this->_View->output = preg_replace_callback($reg, [$this, '_removePrefix'], $this->_View->output);

				// プレフィックス追加
				$reg = '/<a([^<]*?)href=\"(' . $regBaseUrl . ')([^\"]*?)\"/';
				$this->_View->output = preg_replace_callback($reg, [$this, '_addPrefix'], $this->_View->output);
			}
		}
	}

	/**
	 * リンクからモバイル用のプレフィックスを除外する
	 * preg_replace_callback のコールバック関数
	 *
	 * @param array $matches
	 * @return string
	 * @access protected
	 */
	protected function _removePrefix($matches)
	{
		$etc = $matches[1];
		$baseUrl = $matches[3];
		if (strpos($matches[2], 'smartphone=off') !== false) {
			$url = $matches[2];
		} else {
			$url = $matches[5];
		}
		return '<a' . $etc . 'href="' . $baseUrl . $url . '"';
	}

	/**
	 * リンクにモバイル用のプレフィックスを追加する
	 * preg_replace_callback のコールバック関数
	 *
	 * @param array $matches
	 * @return string
	 */
	protected function _addPrefix($matches)
	{
		$currentAlias = $this->request->params['Site']['alias'];
		$baseUrl = $matches[2];
		$etc = $matches[1];
		$url = $matches[3];
		if (strpos($url, 'smartphone=off') !== false) {
			return '<a' . $etc . 'href="' . $baseUrl . $url . '"';
		} else {
			// 指定した絶対URLを記載しているリンクは変換しない
			$excludeList = Configure::read('BcApp.excludeAbsoluteUrlAddPrefix');
			if ($excludeList) {
				foreach($excludeList as $exclude) {
					if (strpos($baseUrl, $exclude) !== false) {
						return '<a' . $etc . 'href="' . $baseUrl . $url . '"';
					}
				}
			}
			// 指定したディレクトリURLを記載しているリンクは変換しない
			$excludeList = Configure::read('BcApp.excludeListAddPrefix');
			if ($excludeList) {
				foreach($excludeList as $exclude) {
					if (strpos($url, $exclude) !== false) {
						return '<a' . $etc . 'href="' . $baseUrl . $url . '"';
					}
				}
			}

			return '<a' . $etc . 'href="' . $baseUrl . $currentAlias . '/' . $url . '"';
		}
	}

}
