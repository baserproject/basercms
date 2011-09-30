<?php
/* SVN FILE: $Id$ */
/**
 * フィードヘルパー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.feed.views.helpers
 * @since			Baser v 0.1.0
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
class FeedHelper extends TextExHelper {
/**
 * ヘルパー
 * 
 * @var array
 * @access public
 */
	var $helpers = array('Baser');
/**
 * レイアウトテンプレートを取得
 * コンボボックスのソースとして利用
 * 
 * @return array
 * @access public
 */
	function getTemplates() {
		
		$templatesPathes = array();
		if($this->Baser->siteConfig['theme']){
			$templatesPathes[] = WWW_ROOT.'themed'.DS.$this->Baser->siteConfig['theme'].DS.'feed'.DS;
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
			if($template != 'ajax.ctp' && $template != 'error.ctp'){
				$template = basename($template, '.ctp');
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
		if(isset($this->Baser->_view->viewVars['cachetime'])) {
			$cachetime = $this->Baser->_view->viewVars['cachetime'];
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
		$this->Baser->cacheHeader(cache('views'.DS.'feed_cachetime_'.$feedId.'.php'));
		
	}
	
}
?>