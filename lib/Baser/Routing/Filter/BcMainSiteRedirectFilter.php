<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Routing.Filter
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * BcMainRedirectFilter class
 *
 * @package Baser.Routing.Filter
 */
class BcMainSiteRedirectFilter extends DispatcherFilter {

/**
 * priority 
 * 
 * Dispacher::parseParams() より後に実行される必要がある
 * 
 * @var int
 */
	public $priority = 10;
	
/**
 * beforeDispatch Event
 *
 * @param CakeEvent $event イベント
 * @return void|CakeResponse
 */
	public function beforeDispatch(CakeEvent $event) {

		$request = $event->data['request'];
		$response = $event->data['response'];
		if(!empty($request->params['Content'])) {
			return;
		} else {
			if($this->_existController($request)) {
				return;
			}
		}
		$Site = ClassRegistry::init('Site');
		$site = $Site->findByUrl($request->url);
		if(!$site) {
			return;
		}
		$mainSite = $Site->getMain($site['Site']['id']);
		if(!$mainSite) {
			return;
		}
		$mainSiteUrl = '/' . preg_replace('/^' . $Site->getPrefix($site) . '\//', '', $request->url);
		$mainSitePrefix = $Site->getPrefix($mainSite);
		if($mainSitePrefix) {
			$mainSiteUrl = '/' . $mainSitePrefix . $mainSiteUrl;
		}
		if($mainSiteUrl) {
			if(Router::parse($mainSiteUrl)) {
				$response->header('Location', $request->base . $mainSiteUrl);
				$response->statusCode(302);
				return $response;
			}
		}
		return;
	}

/**
 * コントローラーが存在するか確認
 * 
 * @param $request
 * @return bool
 */
	protected function _existController($request) {
		$pluginName = $pluginPath = $controller = null;
		if (!empty($request->params['plugin'])) {
			$pluginName = $controller = Inflector::camelize($request->params['plugin']);
			$pluginPath = $pluginName . '.';
		}
		if (!empty($request->params['controller'])) {
			$controller = Inflector::camelize($request->params['controller']);
		}
		if ($pluginPath . $controller) {
			$class = $controller . 'Controller';
			App::uses('AppController', 'Controller');
			App::uses($pluginName . 'AppController', $pluginPath . 'Controller');
			App::uses($class, $pluginPath . 'Controller');
			return class_exists($class);
		}
		return false;
	}
}
