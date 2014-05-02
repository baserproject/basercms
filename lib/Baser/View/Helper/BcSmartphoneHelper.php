<?php

/* SVN FILE: $Id$ */
/**
 * スマホヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */

/**
 * スマホヘルパー
 *
 * @package Web.helpers
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
				$currentAlias = Configure::read('BcRequest.agentAlias');
				// 一旦プレフィックスを除外
				$reg = '/a(.*?)href="' . preg_quote(BC_BASE_URL, '/') . '(' . $currentAlias . '\/([^\"]*?))\"/';
				$this->_View->output = preg_replace_callback($reg, array($this, '_removePrefix'), $this->_View->output);
				// プレフィックス追加
				$reg = '/a(.*?)href=\"' . preg_quote(BC_BASE_URL, '/') . '([^\"]*?)\"/';
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
		$etc = $matches[1];
		if (strpos($matches[2], 'smartphone=off') === false) {
			$url = $matches[3];
		} else {
			$url = $matches[2];
		}
		if (strpos($matches[1], 'smartphone=off') === false) {
			return 'a' . $etc . 'href="' . BC_BASE_URL . $url . '"';
		} else {
			return 'a' . $etc . 'href="' . BC_BASE_URL . $url . '"';
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
		$currentAlias = Configure::read('BcRequest.agentAlias');
		$etc = $matches[1];
		$url = $matches[2];
		if (strpos($url, 'smartphone=off') === false) {
			return 'a' . $etc . 'href="' . BC_BASE_URL . $currentAlias . '/' . $url . '"';
		} else {
			return 'a' . $etc . 'href="' . BC_BASE_URL . $url . '"';
		}
	}

}
