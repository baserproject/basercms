<?php
/* SVN FILE: $Id$ */
/**
 * フィードヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.feed.views.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * フィードヘルパー
 *
 * @package baser.plugins.feed.views.helpers
 *
 */
class FeedHelper extends BcTextHelper {
/**
 * ヘルパー
 * 
 * @var array
 * @access public
 */
	var $helpers = array(BC_BASER_HELPER);
/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * 
 * @return array
 * @access public
 */
	function getTemplates() {
		
		$templatesPathes = array();
		if($this->BcBaser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->BcBaser->siteConfig['theme'].DS.'feed'.DS;
		}
		$templatesPathes[] = BASER_PLUGINS.'feed'.DS.'views'.DS.'feed'.DS;
		
		$_templates = array();
		foreach($templatesPathes as $templatesPath){
			$folder = new Folder($templatesPath);
			$files = $folder->read(true, true);
			$foler = null;
			if($files[1]){
				if($_templates){
					$_templates = am($_templates,$files[1]);
				}else{
					$_templates = $files[1];
				}
			}
		}
		$templates = array();
		foreach($_templates as $template){
			$ext = Configure::read('BcApp.templateExt');
			if($template != 'ajax'.$ext && $template != 'error'.$ext){
				$template = basename($template, $ext);
				$templates[$template] = $template;
			}
		}
		return $templates;
		
	}
/**
 * フィードのキャッシュタイムをキャッシュファイルに保存
 * <cake:nocache>でキャッシュタイムを参照できるようにする
 *
 * @return void
 * @access public
 */
	function saveCachetime(){
		
		$feedId = $this->params['pass'][0];
		if(isset($this->BcBaser->_view->viewVars['cachetime'])) {
			$cachetime = $this->BcBaser->_view->viewVars['cachetime'];
			cache('views'.DS.'feed_cachetime_'.$feedId.'.php',$cachetime);
		}
		
	}
/**
 * フィードリストのキャッシュヘッダーを出力する
 * キャッシュ時間は管理画面で設定した値
 * ヘッダーを出力するには<cake:nocache>を利用する
 * <cake:nocache>内では動的変数を利用できないのでキャッシュファイルを利用する
 * 事前に $feed->saveCachetime() でキャッシュタイムを保存しておく
 *
 * @return void
 * @access public
 */
	function cacheHeader(){
		
		$feedId = $this->params['pass'][0];
		$this->BcBaser->cacheHeader(cache('views'.DS.'feed_cachetime_'.$feedId.'.php'));
		
	}
	
}
