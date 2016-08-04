<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Routing.Filter
 * @since			baserCMS v 4.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('CakeRoute', 'Routing/Route');

/**
 * BcContentsRoute
 *
 * @package Baser.Routing.Route
 */
class BcContentsRoute extends CakeRoute {

/**
 * Parses a string URL into an array. If a plugin key is found, it will be copied to the
 * controller parameter
 *
 * @param string $url The URL to parse
 * @return mixed false on failure, or an array of request parameters
 */
	public function parse($url) {
		if(is_array($url)) {
			return false;
		}
		$Content = ClassRegistry::init('Content');
		$request = new CakeRequest();
		//管理システムにログインしているかつプレビューの場合は公開状態のステータスは無視する
		$extend = false;
		if(!empty($request->query['preview'])) {
			$content = $this->getContent($url, false);
			if($content && !BcUtil::isAdminUser()) {
				$_SESSION['Auth']['redirect'] = $_SERVER['REQUEST_URI'];
				header('Location: ' . topLevelUrl(false) . baseUrl() . Configure::read('BcAuthPrefix.admin.alias') . '/users/login');
				exit();
			}
		} else {
			$content = $this->getContent($url, true);
			if(!$content) {
				$content = $this->getContent($url, true, true);
				$extend = true;
			}
		}
		if (!$content) {
			return false;
		}
		if($content['Content']['alias_id'] && !$Content->isPublishById($content['Content']['alias_id'])) {
			return false;
		}
		$request = Router::getRequest();
		$request->params['Content'] = $content['Content'];
		$request->params['Site'] = $content['Site'];
		if(!$extend) {
			$url = $content['Content']['url'];
		}
		$params = $this->getParams($url, $content['Content']['url'], $content['Content']['plugin'], $content['Content']['type'], $content['Content']['entity_id']);
		if($params) {
			return $params;
		}
		return false;
	}

/**
 * URLに関連するコンテンツ情報を取得する
 *
 * @param $url
 * @param bool $publish
 * @param bool $extend
 * @return mixed
 */
	public function getContent($url, $publish = true, $extend = false) {
		$url = preg_replace('/^\//', '', $url);
		$Content = ClassRegistry::init('Content');
		$subDomain = BcUtil::getSubDomain();
		if($extend) {
			$params = explode('/', $url);
			$condUrls = [];
			$count = count($params);
			for ($i = $count; $i > 0; $i--) {
				unset($params[$i]);
				if($subDomain) {
					$condUrls[] = '/' . $subDomain . '/' . implode('/', $params);
				} else {
					$condUrls[] = '/' . implode('/', $params);
				}
			}
			$conditions = ['Content.url' => $condUrls];
		} else {
			$conditions = ['Content.url' => $this->getUrlPattern($url)];
		}
		if($publish) {
			$conditions = $conditions + $Content->getConditionAllowPublish();
		}
		$content = $Content->find('first', ['conditions' => $conditions, 'order' => 'Content.url DESC']);
		return $content;
	}

/**
 * コンテンツに関連するパラメーター情報を取得する
 *
 * @param $requestUrl
 * @param $entryUrl
 * @param $plugin
 * @param $type
 * @param $entityId
 * @return array
 */
	public function getParams($requestUrl, $entryUrl, $plugin, $type, $entityId) {
		$viewParams = Configure::read('BcContents.items.' . $plugin . '.' . $type . '.routes.view');
		if (!$viewParams) {
			$viewParams = Configure::read('BcContents.items.Core.Default.routes.view');
			$params = [
				'controller' => $viewParams['controller'],
				'action' => $viewParams['action'],
				'pass' => [$plugin, $type]
			];
		} else {
			$pass = [];
			$action = $viewParams['action'];
			if($type == 'Page') {
				$url = preg_replace('/^\//', '', $entryUrl);
				$pass = explode('/', $url);
			} elseif($requestUrl != $entryUrl) {
				$url = preg_replace('/^' . preg_quote($entryUrl, '/') . '/', '', $requestUrl);
				$url = preg_replace('/^\//', '', $url);
				$urlAry = explode('/', $url);
				$action = $urlAry[0];
				array_shift($urlAry);
				if($urlAry) {
					$pass = $urlAry;
				}
			}
			if($plugin == 'Core') {
				$plugin = '';
			}
			$params = [
				'plugin' => Inflector::underscore($plugin),
				'controller' => $viewParams['controller'],
				'action' => $action,
				'pass' => $pass,
				'entityId' => $entityId
			];
		}
		return $params;
	}

/**
 * コンテンツのURLにマッチする候補を取得する
 *
 * @param $url
 * @return array
 */
	public function getUrlPattern($url) {
		$parameter = preg_replace('/^\//', '', $url);
		$subDomain = BcUtil::getSubDomain();
		$paths = [];
		if($subDomain) {
			$paths[] = '/' . $subDomain . '/' . $parameter;
		} else {
			$paths[] = '/' . $parameter;
		}
		if(preg_match('/\/$/', $paths[0])) {
			$paths[] = $paths[0] . 'index';
			$paths[] = preg_replace('/\/$/', '', $paths[0]);
		} elseif(preg_match('/^(.*?\/)index$/', $paths[0], $matches)) {
			$paths[] = $matches[1];
			$paths[] = preg_replace('/\/$/', '', $matches[1]);
		} elseif (preg_match('/^(.+?)\.html$/', $paths[0], $matches)) {
			$paths[] = $matches[1];
			if(preg_match('/^(.*?\/)index$/', $matches[1], $matches)) {
				$paths[] = $matches[1];
			}
		}
		return $paths;
	}

}
