<?php
/* SVN FILE: $Id$ */
/**
 * プラグイン拡張クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * プラグイン拡張クラス
 * プラグインのコントローラーより継承して利用する
 *
 * @package baser.controllers
 */
class BaserPluginAppController extends AppController {
/**
 * コンテンツID
 *
 * @var int
 */
	var $contentId = null;
/**
 * beforeFilter
 *
 * @return void
 * @access private
 */
	function beforeFilter() {

		parent::beforeFilter();

		$this->Plugin = ClassRegistry::init('Plugin');
		$this->PluginContent = ClassRegistry::init('PluginContent');

		// 有効でないプラグインを実行させない
		if(!$this->Plugin->find('all',array('conditions'=>array('name'=>$this->params['plugin'], 'status'=>true)))) {
			$this->notFound();
		}

		$this->contentId = $this->getContentId();
		
	}
/**
 * コンテンツIDを取得する
 * 一つのプラグインで複数のコンテンツを実装する際に利用する。
 *
 * @return int $pluginNo
 * @access public
 */
	function getContentId() {

		// 管理画面の場合には取得しない
		if(!empty($this->params['admin'])){
			return null;
		}

		if(!isset($this->params['url']['url'])) {
			return null;
		}
		
		$contentName = '';
		$url = preg_replace('/^\//', '', $this->params['url']['url']);
		$url = explode('/', $url);
		
		if($url[0]!=Configure::read('BcRequest.agentAlias')) {
			if(!empty($this->params['prefix']) && $url[0] == $this->params['prefix']) {
				if(isset($url[1])) {
					$contentName = $url[1];
				}
			}else {
				$contentName = $url[0];
			}
		}else {
			if(!empty($this->params['prefix']) && $url[0] == $this->params['prefix']) {
				$contentName = $url[2];
			}else {
				$contentName = $url[1];
			}
		}

		// プラグインと同じ名前のコンテンツ名の場合に正常に動作しないので
		// とりあえずコメントアウト
		/*if( Inflector::camelize($url) == $this->name){
			return null;
		}*/
		$pluginContent = $this->PluginContent->findByName($contentName);
		if($pluginContent) {
			return $pluginContent['PluginContent']['content_id'];
		}else {
			return null;
		}

	}
	
}