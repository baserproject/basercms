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

/**
 * スマホヘルパー
 *
 * @package Baser.View.Helper
 */
class BcSmartphoneHelper extends Helper {
	
/**
 * ヘルパ
 * 
 * @var array
 */
	public $helpers = ['BcHtml'];

/**
 * After Render
 * 
 * @param string $viewFile
 */
	public function afterRender($viewFile) {
		parent::afterRender($viewFile);
		$site = BcSite::findCurrent();
		if($site->device != 'smartphone' || $site->sameMainUrl) {
			return;
		}
		// 別URLの場合、canonicalを出力
		$pureUrl = $site->getPureUrl($this->request->url);
		$mainSite = BcSite::findCurrentMain();
		$url = $mainSite->makeUrl(new CakeRequest($pureUrl));
		$this->_View->set('meta',
			$this->BcHtml->meta('canonical',
				$this->BcHtml->url($url, true),
				[
					'rel' => 'canonical',
					'type' => null,
					'title' => null,
					'inline' => false
				]
			)
		);	
	}

	/**
 * afterLayout
 *
 * @return void
 */
	public function afterLayout($layoutFile) {

		if (isset($this->request->params['ext']) && $this->request->params['ext'] == 'rss') {
			$rss = true;
		} else {
			$rss = false;
		}
		$site = BcSite::findCurrent();
		if (!$rss && $site->device == 'smartphone' && $this->_View->layoutPath != 'Emails' . DS . 'text') {
			if(empty($this->request->params['Site'])) {
				return;
			}
			// 内部リンクの自動変換
			if ($site->autoLink) {
				$bcBaseUrl = BC_BASE_URL;
				if ($this->_View->BcBaser->isSSL()) {
					$bcBaseUrl = Configure::read('BcEnv.siteUrl');
				}
				$currentAlias = $this->request->params['Site']['alias'];
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
 */
	protected function _addPrefix($matches) {
		$bcBaseUrl = BC_BASE_URL;
		if ($this->_View->BcBaser->isSSL()) {
			$bcBaseUrl = Configure::read('BcEnv.siteUrl');
		}
		$currentAlias = $this->request->params['Site']['alias'];
		$etc = $matches[1];
		$url = $matches[2];
		if (strpos($url, 'smartphone=off') === false) {
			return 'a' . $etc . 'href="' . $bcBaseUrl . $currentAlias . '/' . $url . '"';
		} else {
			return 'a' . $etc . 'href="' . $bcBaseUrl . $url . '"';
		}
	}

}
