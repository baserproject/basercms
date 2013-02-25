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
 * @package			baser.view.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * スマホヘルパー
 *
 * @package baser.views.helpers
 */
class BcSmartphoneHelper extends Helper {
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

		if($view && !$rss && Configure::read('BcRequest.agent') == 'smartphone' && $view->layoutPath != 'email'.DS.'text') {	
			// 内部リンクの自動変換
			if(Configure::read('BcAgent.smartphone.autoLink')) {
				$currentAlias = Configure::read('BcRequest.agentAlias');
				// 一旦プレフィックスを除外
				$reg = '/href="'.preg_quote(BC_BASE_URL, '/').'('.$currentAlias.'\/([^\"]*?))\"/';
				$view->output = preg_replace_callback($reg, array($this, '_removePrefix'), $view->output);
				// プレフィックス追加
				$reg = '/href=\"'.preg_quote(BC_BASE_URL, '/').'([^\"]*?)\"/';
				$view->output = preg_replace_callback($reg, array($this, '_addPrefix'), $view->output);
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
	function _removePrefix($matches) {
		
		if(strpos($matches[1], 'smartphone=off') === false) {
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
	function _addPrefix($matches) {
		
		$currentAlias = Configure::read('BcRequest.agentAlias');
		$url = $matches[1];
		if(strpos($url, 'smartphone=off') === false) {
			return 'href="'.BC_BASE_URL.$currentAlias.'/'.$url.'"';
		} else {
			return 'href="'.BC_BASE_URL.$url.'"';
		}
		
	}
	
}