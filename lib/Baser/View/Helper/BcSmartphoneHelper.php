<?php
/**
 * スマホヘルパー
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * スマホヘルパー
 *
 * @package Baser.View.Helper
 */
class BcSmartphoneHelper extends Helper {

/**
 * afterLayout
 *
 * @return void
 * @access public
 */
	public function afterLayout($layoutFile) {

		if (isset($this->request->params['ext']) && $this->request->params['ext'] == 'rss') {
			$rss = true;
		} else {
			$rss = false;
		}

		if (!$rss && Configure::read('BcRequest.agent') == 'smartphone' && $this->_View->layoutPath != 'Emails' . DS . 'text') {
			// 内部リンクの自動変換
			if (Configure::read('BcAgent.smartphone.autoLink')) {
				$bcBaseUrl = BC_BASE_URL;
				if ($this->_View->BcBaser->isSSL()) {
					$bcBaseUrl = Configure::read('BcEnv.siteUrl');
				}
				$currentAlias = Configure::read('BcRequest.agentAlias');
				// 一旦プレフィックスを除外
				$reg = '/a(.*?)href="' . preg_quote($bcBaseUrl, '/') . '(' . $currentAlias . '\/([^\"]*?))\"/';
				$this->_View->output = preg_replace_callback($reg, array($this, '_removePrefix'), $this->_View->output);
				// プレフィックス追加
				$reg = '/a(.*?)href=\"' . preg_quote($bcBaseUrl, '/') . '([^\"]*?)\"/';
				$this->_View->output = preg_replace_callback($reg, array($this, '_addPrefix'), $this->_View->output);
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
	protected function _removePrefix($matches) {
		$bcBaseUrl = BC_BASE_URL;
		if ($this->_View->BcBaser->isSSL()) {
			$bcBaseUrl = Configure::read('BcEnv.siteUrl');
		}
		$etc = $matches[1];
		if (strpos($matches[2], 'smartphone=off') === false) {
			$url = $matches[3];
		} else {
			$url = $matches[2];
		}
		if (strpos($matches[1], 'smartphone=off') === false) {
			return 'a' . $etc . 'href="' . $bcBaseUrl . $url . '"';
		} else {
			return 'a' . $etc . 'href="' . $bcBaseUrl . $url . '"';
		}
	}

/**
 * リンクにモバイル用のプレフィックスを追加する
 * preg_replace_callback のコールバック関数
 * 
 * @param array $matches
 * @return string 
 * @access protected
 */
	protected function _addPrefix($matches) {
		$bcBaseUrl = BC_BASE_URL;
		if ($this->_View->BcBaser->isSSL()) {
			$bcBaseUrl = Configure::read('BcEnv.siteUrl');
		}
		$currentAlias = Configure::read('BcRequest.agentAlias');
		$etc = $matches[1];
		$url = $matches[2];
		if (strpos($url, 'smartphone=off') === false) {
			return 'a' . $etc . 'href="' . $bcBaseUrl . $currentAlias . '/' . $url . '"';
		} else {
			return 'a' . $etc . 'href="' . $bcBaseUrl . $url . '"';
		}
	}

}
