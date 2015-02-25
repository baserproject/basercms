<?php
/**
 * モバイルヘルパー
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
 * モバイルヘルパー
 *
 * @package Baser.View.Helper
 */
class BcMobileHelper extends Helper {

/**
 * afterLayout
 *
 * @return void
 * @access public
 */
	public function afterLayout($layoutFile) {

		/* 出力データをSJISに変換 */
		$View = $this->_View;

		if (isset($this->request->params['ext']) && $this->request->params['ext'] == 'rss') {
			$rss = true;
		} else {
			$rss = false;
		}

		if (!$rss && Configure::read('BcRequest.agent') == 'mobile' && $View->layoutPath != 'Emails' . DS . 'text') {

			$View->output = str_replace('＆', '&amp;', $View->output);
			$View->output = str_replace('＜', '&lt;', $View->output);
			$View->output = str_replace('＞', '&gt;', $View->output);
			$View->response->charset('Shift_JIS');
			$View->output = mb_convert_kana($View->output, "rak", "UTF-8");
			$View->output = mb_convert_encoding($View->output, "SJIS-win", "UTF-8");

			// 内部リンクの自動変換
			if (Configure::read('BcAgent.mobile.autoLink')) {
				$currentAlias = Configure::read('BcRequest.agentAlias');
				// 一旦プレフィックスを除外
				$reg = '/href="' . preg_quote(BC_BASE_URL, '/') . '(' . $currentAlias . '\/([^\"]*?))\"/';
				$View->output = preg_replace_callback($reg, array($this, '_removeMobilePrefix'), $View->output);
				// プレフィックス追加
				$reg = '/href=\"' . preg_quote(BC_BASE_URL, '/') . '([^\"]*?)\"/';
				$View->output = preg_replace_callback($reg, array($this, '_addMobilePrefix'), $View->output);
			}
			// XMLとして出力する場合、デバッグモードで出力する付加情報で、
			// ブラウザによってはXMLパースエラーとなってしまうので強制的にデバッグモードをオフ
			Configure::write('debug', 0);
		}
	}

/**
 * コンテンツタイプを出力
 * 
 * @return void
 * @access public
 */
	public function header() {
		if (Configure::read('BcRequest.agent') == 'mobile') {
			$this->_View->response->charset('Shift-JIS');
			header("Content-type: application/xhtml+xml");
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
	protected function _removeMobilePrefix($matches) {
		if (strpos($matches[1], 'mobile=off') === false) {
			return 'href="' . BC_BASE_URL . $matches[2] . '"';
		} else {
			return 'href="' . BC_BASE_URL . $matches[1] . '"';
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
	protected function _addMobilePrefix($matches) {
		$currentAlias = Configure::read('BcRequest.agentAlias');
		$url = $matches[1];
		if (strpos($url, 'mobile=off') === false) {
			return 'href="' . BC_BASE_URL . $currentAlias . '/' . $url . '"';
		} else {
			return 'href="' . BC_BASE_URL . $url . '"';
		}
	}

}
