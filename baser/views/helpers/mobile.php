<?php
/* SVN FILE: $Id$ */
/**
 * モバイルヘルパー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.view.helpers
 * @since			Baser v 0.1.0
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
class MobileHelper extends Helper {
/**
 * afterLayout
 *
 * @return void
 * @access public
 */
	function afterLayout() {

		/* 出力データをSJISに変換 */
		$view =& ClassRegistry::getObject('view');

		if(isset($this->params['url']['ext']) && $this->params['url']['ext'] == 'rss') {
			$rss = true;
		}else {
			$rss = false;
		}

		if($view && !$rss && Configure::read('AgentPrefix.currentAgent') == 'mobile' && $view->layoutPath != 'email'.DS.'text') {

			$view->output = str_replace('＆', '&amp;', $view->output);
			$view->output = str_replace('＜', '&lt;', $view->output);
			$view->output = str_replace('＞', '&gt;', $view->output);
			$view->output = mb_convert_kana($view->output, "rak", "UTF-8");
			$view->output = mb_convert_encoding($view->output, "SJIS-win", "UTF-8");

			// 内部リンクの自動変換
			$baseUrl = baseUrl();
			$view->output = preg_replace('/href=\"'.str_replace('/', '\/', $baseUrl).'([^\"]+?)\"/', "href=\"".$baseUrl."m/$1\"", $view->output);
			$view->output = preg_replace('/href=\"'.str_replace('/', '\/', $baseUrl).'m\/m\//', "href=\"".$baseUrl."m/", $view->output);

			// 変換した上キャッシュを再保存しないとキャッシュ利用時に文字化けしてしまう
			$caching = (
					isset($view->loaded['cache']) &&
							(($view->cacheAction != false)) && (Configure::read('Cache.check') === true)
			);
			if ($caching) {
				if (is_a($view->loaded['cache'], 'CacheHelper')) {
					$cache =& $view->loaded['cache'];
					$cache->base = $view->base;
					$cache->here = $view->here;
					$cache->helpers = $view->helpers;
					$cache->action = $view->action;
					$cache->controllerName = $view->name;
					$cache->layout	= $view->layout;
					$cache->cacheAction = $view->cacheAction;
					$cache->cache($___viewFn, $view->output, true);
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
	function header(){
		
		header("Content-type: application/xhtml+xml");
		
	}
	
}
?>