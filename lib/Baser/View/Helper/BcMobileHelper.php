<?php
/* SVN FILE: $Id$ */
/**
 * モバイルヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.view.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * モバイルヘルパー
 *
 * @package baser.views.helpers
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
		$view = ClassRegistry::getObject('view');

		if(isset($this->request->params['url']['ext']) && $this->request->params['url']['ext'] == 'rss') {
			$rss = true;
		}else {
			$rss = false;
		}

		if($view && !$rss && Configure::read('BcRequest.agent') == 'mobile' && $view->layoutPath != 'email'.DS.'text') {

			$view->output = str_replace('＆', '&amp;', $view->output);
			$view->output = str_replace('＜', '&lt;', $view->output);
			$view->output = str_replace('＞', '&gt;', $view->output);
			$view->output = mb_convert_kana($view->output, "rak", "UTF-8");
			$view->output = mb_convert_encoding($view->output, "SJIS-win", "UTF-8");

			// 内部リンクの自動変換
			if(Configure::read('BcAgent.mobile.autoLink')) {
				$currentAlias = Configure::read('BcRequest.agentAlias');
				// 一旦プレフィックスを除外
				$reg = '/href="'.preg_quote(BC_BASE_URL, '/').'('.$currentAlias.'\/([^\"]*?))\"/';
				$view->output = preg_replace_callback($reg, array($this, '_removeMobilePrefix'), $view->output);
				// プレフィックス追加
				$reg = '/href=\"'.preg_quote(BC_BASE_URL, '/').'([^\"]*?)\"/';
				$view->output = preg_replace_callback($reg, array($this, '_addMobilePrefix'), $view->output);
			}
			
			// 変換した上キャッシュを再保存しないとキャッシュ利用時に文字化けしてしまう
			$caching = (
					isset($view->loaded['cache']) &&
							(($view->cacheAction != false)) && (Configure::read('Cache.check') === true)
			);
			if ($caching) {
				if (is_a($view->loaded['cache'], 'CacheHelper')) {
					$cache = $view->loaded['cache'];
					$this->Cache->base = $view->base;
					$this->Cache->here = $view->here;
					$this->Cache->helpers = $view->helpers;
					$this->Cache->action = $view->action;
					$this->Cache->controllerName = $view->name;
					$this->Cache->layout	= $view->layout;
					$this->Cache->cacheAction = $view->cacheAction;
					$this->Cache->cache($___viewFn, $view->output, true);
				}
			} else{
				// nocache で コンテンツヘッダを出力する場合、逆にキャッシュを利用しない場合に、
				// nocache タグが残ってしまってエラーになるので除去する
				$view->output = str_replace('<cake:nocache>','',$view->output);
				$view->output = str_replace('</cake:nocache>','',$view->output);
			}
			// XMLとして出力する場合、デバッグモードで出力する付加情報で、
			// ブラウザによってはXMLパースエラーとなってしまうので強制的にデバッグモードをオフ
			Configure::write('debug',0);
			
		}
		
	}
/**
 * コンテンツタイプを出力
 * 
 * @return void
 * @access public
 */
	public function header(){
		
		if(Configure::read('BcRequest.agent') == 'mobile') {
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
		if(strpos($matches[1], 'mobile=off') === false) {
			return 'href="'.BC_BASE_URL.$matches[2].'"';
		} else {
			return 'href="'.BC_BASE_URL.$matches[1].'"';
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
		if(strpos($url, 'mobile=off') === false) {
			return 'href="'.BC_BASE_URL.$currentAlias.'/'.$url.'"';
		} else {
			return 'href="'.BC_BASE_URL.$url.'"';
		}
	}	
}
