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

		$extend = false;

		//管理システムにログインしているかつプレビューの場合は公開状態のステータスは無視する
		$publish = true;
		if(!empty($request->query['preview'])) {
			$publish = false;
		}

		$content = $this->getContent($url, $publish);
		if(!$content) {
			$content = $this->getContent($url, $publish, true);
			if($content) {
				$extend = true;	
			}
		}

		if (!$content) {
			return false;
		}

		// データが存在してもプレビューで管理システムにログインしていない場合はログイン画面に遷移
		if(!empty($request->query['preview']) &&  !BcUtil::isAdminUser()) {
			$_SESSION['Auth']['redirect'] = $_SERVER['REQUEST_URI'];
			header('Location: ' . topLevelUrl(false) . baseUrl() . Configure::read('BcAuthPrefix.admin.alias') . '/users/login');
			exit();
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
			$conditions = [
				'Content.url' => $condUrls,
				'or' => [
					['Site.status' => true],
					['Site.status' => null]
				]
			];
		} else {
			$conditions = [
				'Content.url' => $this->getUrlPattern($url),
				'or' => [
					['Site.status' => true],
					['Site.status' => null]
				]
			];	
		}
		if($publish) {
			$conditions = $conditions + $Content->getConditionAllowPublish();
		}
		$content = $Content->find('first', ['conditions' => $conditions, 'order' => 'Content.url DESC']);
		if($content && empty($content['Site']['id'])) {
			$content['Site'] = $Content->Site->getRootMain()['Site'];
		}
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

/**
 * Reverse route
 *
 * TODO ryuring リバースルーティングについて pass や named を付加できていない為
 * 途中までの処理をコメントアウトとして残す
 *
 * @param array $url Array of parameters to convert to a string.
 * @return mixed either false or a string URL.
 */
//	public function match($url) {
//		$request = Router::getRequest();
//		$plugin = $request->params['plugin'];
//		if(!$plugin) {
//			$plugin = 'Core';
//		} else {
//			$plugin = Inflector::camelize($plugin);
//		}
//
//		$viewParams = Configure::read('BcContents.items.' . $plugin);
//		$type = '';
//		foreach($viewParams as $type => $param) {
//			if(empty($param['routes']['view'])) {
//				continue;
//			}
//			$viewParam = $param['routes']['view'];
//			if($plugin == $type && $url['controller'] == $viewParam['controller'] && $url['action'] == $viewParam['action']) {
//				$type = $key;
//				break;
//			}
//		}
//		if(!$type) {
//			return false;
//		}
//		$Content = ClassRegistry::init('Content');
//		$entryId = null;
//		if(!empty($request->params['entityId'])) {
//			$entryId = $request->params['entityId'];
//		}
//		$content = $Content->findByType($plugin . '.' . $type, $entryId);
//		$result = $content['Content']['url'];
//		return $result;
//	}
}
