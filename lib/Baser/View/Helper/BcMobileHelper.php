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
 * モバイルヘルパー
 *
 * @package Baser.View.Helper
 * @deprecated  5.0.0 since 4.3.3 ガラケーは非対応とする
 */
class BcMobileHelper extends Helper
{

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
	 * @deprecated  5.0.0 since 4.3.3 ガラケーは非対応とする
	 */
	public function afterRender($viewFile)
	{
		parent::afterRender($viewFile);
		$site = BcSite::findCurrent();
		if ($site->device != 'mobile' || $site->sameMainUrl) {
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
	 * @deprecated  5.0.0 since 4.3.3 ガラケーは非対応とする
	 */
	public function afterLayout($layoutFile)
	{

		/* 出力データをSJISに変換 */
		$View = $this->_View;

		if (isset($this->request->params['ext']) && $this->request->params['ext'] == 'rss') {
			$rss = true;
		} else {
			$rss = false;
		}

		$site = BcSite::findCurrent(true);
		if (!$rss && $site && $site->device == 'mobile' && $View->layoutPath != 'Emails' . DS . 'text') {

			$View->output = str_replace('＆', '&amp;', $View->output);
			$View->output = str_replace('＜', '&lt;', $View->output);
			$View->output = str_replace('＞', '&gt;', $View->output);
			$View->response->charset('Shift_JIS');
			$View->output = mb_convert_kana($View->output, "rak", "UTF-8");
			$View->output = mb_convert_encoding($View->output, "SJIS-win", "UTF-8");

			// 内部リンクの自動変換
			if ($site->autoLink) {
				$currentAlias = $this->request->params['Site']['alias'];
				// 一旦プレフィックスを除外
				$reg = '/href="' . preg_quote(BC_BASE_URL, '/') . '(' . $currentAlias . '\/([^\"]*?))\"/';
				$View->output = preg_replace_callback($reg, [$this, '_removeMobilePrefix'], $View->output);
				// プレフィックス追加
				$reg = '/href=\"' . preg_quote(BC_BASE_URL, '/') . '([^\"]*?)\"/';
				$View->output = preg_replace_callback($reg, [$this, '_addMobilePrefix'], $View->output);
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
	 * @deprecated  5.0.0 since 4.3.3 ガラケーは非対応とする
	 */
	public function header()
	{
		if ($this->request->params['Site']['device'] == 'mobile') {
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
	 * @deprecated  5.0.0 since 4.3.3 ガラケーは非対応とする
	 */
	protected function _removeMobilePrefix($matches)
	{
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
	 * @deprecated  5.0.0 since 4.3.3 ガラケーは非対応とする
	 */
	protected function _addMobilePrefix($matches)
	{
		$currentAlias = $this->request->params['Site']['alias'];
		$url = $matches[1];
		if (strpos($url, 'mobile=off') === false) {
			return 'href="' . BC_BASE_URL . $currentAlias . '/' . $url . '"';
		} else {
			return 'href="' . BC_BASE_URL . $url . '"';
		}
	}

}
