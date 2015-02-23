<?php

/**
 * プラグイン拡張クラス
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
App::uses('AppController', 'Controller');

/**
 * プラグイン拡張クラス
 * プラグインのコントローラーより継承して利用する
 *
 * @package Baser.Controller
 */
class BcPluginAppController extends AppController {

/**
 * コンテンツID
 *
 * @var int
 */
	public $contentId = null;

/**
 * beforeFilter
 *
 * @return void
 * @access private
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Plugin = ClassRegistry::init('Plugin');
		$this->PluginContent = ClassRegistry::init('PluginContent');

		// 有効でないプラグインを実行させない
		$name = Inflector::camelize($this->request->params['plugin']);
		if (!$this->Plugin->find('all', array('conditions' => array('name' => $name, 'status' => true)))) {
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
	public function getContentId() {
		// 管理画面の場合には取得しない
		if (!empty($this->request->params['admin'])) {
			return null;
		}

		if (!isset($this->request->url)) {
			return null;
		}

		$contentName = '';
		$url = preg_replace('/^\//', '', $this->request->url);

		if (!$url) {
			return null;
		}
		
		// コンテンツ名取得
		$request = new CakeRequest($url);
		$pureUrl = getPureUrl($request);
		$aryPureUrl = explode('/', $pureUrl);
		$contentName = $aryPureUrl[0];

		// プラグインと同じ名前のコンテンツ名の場合に正常に動作しないので
		// とりあえずコメントアウト
		/* if( Inflector::camelize($url) == $this->name){
		  return null;
		  } */
		$pluginContent = $this->PluginContent->findByName($contentName);
		if ($pluginContent) {
			return $pluginContent['PluginContent']['content_id'];
		} else {
			return null;
		}
	}

}
