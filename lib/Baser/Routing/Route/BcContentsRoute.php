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
App::uses('BcSite', 'Lib');

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

		if(BcUtil::isAdminSystem($url)) {
			return false;
		}

		$request = Router::getRequest(true);
		$extend = false;

		//管理システムにログインしているかつプレビューの場合は公開状態のステータスは無視する
		$publish = true;
		if(!empty($request->query['preview']) || !empty($request->query['force'])) {
			$publish = false;
		}

		// 同一URL対応
		$sameUrl = false;
		$subSite = BcSite::findCurrentSub(true);
		if($subSite && $subSite->existsUrl($request)) {
			$sameUrl = true;
			$url = $subSite->makeUrl($request);
			header('Vary: User-Agent');
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
		if((!empty($request->query['preview']) || !empty($request->query['force'])) &&  !BcUtil::isAdminUser()) {
			$_SESSION['Auth']['redirect'] = $_SERVER['REQUEST_URI'];
			header('Location: ' . topLevelUrl(false) . baseUrl() . Configure::read('BcAuthPrefix.admin.alias') . '/users/login');
			exit();
		}

		$Content = ClassRegistry::init('Content');
		if($content['Content']['alias_id'] && !$Content->isPublishById($content['Content']['alias_id'])) {
			return false;
		}
		$request->params['Content'] = $content['Content'];
		$request->params['Site'] = $content['Site'];
		if(!$extend) {
			$url = $content['Content']['url'];
		}
		
		if($sameUrl && $subSite) {
			$content['Content']['url'] = $subSite->getPureUrl($url);
		}
		
		$site = BcSite::findCurrent();
		$params = $this->getParams($url, $content['Content']['url'], $content['Content']['plugin'], $content['Content']['type'], $content['Content']['entity_id'], $site->domainType);
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
		$site = BcSite::findCurrent();
		$domainKey = '';
		if($site->useSubDomain) {
			$domainKey = $site->alias . '/';
		}
		if($extend) {
			$params = explode('/', $url);
			$condUrls = [];
			$condUrls[] = '/' . $domainKey . implode('/', $params);
			$count = count($params);
			for ($i = $count; $i > 1; $i--) {
				unset($params[$i - 1]);
				$path = implode('/', $params);
				$condUrls[] = '/' . $domainKey . $path . '/';
				$condUrls[] = '/' . $domainKey . $path;
			}
			// 固定ページはURL拡張はしない
			$conditions = [
				'Content.type <>' => 'Page',
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
		$content = $Content->find('first', ['conditions' => $conditions, 'order' => 'Content.url DESC', 'cache' => false]);
		if(!$content) {
			return false;
		}
		if($extend && $content['Content']['type'] == 'ContentFolder') {
			return false;
		}
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
	public function getParams($requestUrl, $entryUrl, $plugin, $type, $entityId, $domainType = 0) {
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
			$named = [];
			$action = $viewParams['action'];
			if($type == 'Page') {
				$url = preg_replace('/^\//', '', $entryUrl);
				$pass = explode('/', $url);
			} elseif($requestUrl != $entryUrl) {
				if($domainType) {
					$entryUrl = preg_replace('/^\/.+?\//', '/', $entryUrl);
				}
				$url = preg_replace('/^' . preg_quote($entryUrl, '/') . '/', '', $requestUrl);
				$url = preg_replace('/^\//', '', $url);
				$urlAry = explode('/', $url);
				$action = $urlAry[0];
				array_shift($urlAry);
				if($urlAry) {
					foreach($urlAry as $param) {
						if(strpos($param, ':') !== false) {
							list($key, $value) = explode(':', $param);
							$named[$key] = $value;
						} else {
							$pass[] = $param;
						}
					}
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
				'named' => $named,
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
		$site = BcSite::findCurrent();
		$domainKey = '';
		if($site->useSubDomain) {
			$domainKey = $site->alias . '/';
		}
		$paths = [];
		$paths[] = '/' . $domainKey . $parameter;
		if(preg_match('/\/$/', $paths[0])) {
			$paths[] = $paths[0] . 'index';
		} elseif(preg_match('/^(.*?\/)index$/', $paths[0], $matches)) {
			$paths[] = $matches[1];
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
 * @param array $url Array of parameters to convert to a string.
 * @return mixed either false or a string URL.
 */
	public function match($url) {
		// フロント以外のURLの場合にマッチしない
		if(!empty($url['admin'])) {
			return false;
		}
		
		// プラグイン確定
		if(empty($url['plugin'])) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($url['plugin']);
		}

		// アクション確定
		if(!empty($url['action'])) {
			$action = $url['action'];
		} else {
			$action = 'index';
		}

		$params = $url;
		unset($params['plugin']);
		unset($params['controller']);
		unset($params['action']);
		unset($params['entityId']);

		// コンテンツタイプ確定、できなければスルー
		$type = $this->_getContentTypeByParams($url);
		if($type) {
			unset($params['action']);
		} else {
			$type = $this->_getContentTypeByParams($url, false);
		}
		if(!$type) {
			return false;
		}

		// エンティティID確定
		$entityId = null;
		if(isset($url['entityId'])) {
			$entityId = $url['entityId'];
			unset($params['entityId']);
		}

		// コンテンツ確定、できなければスルー
		$Content = ClassRegistry::init('Content');
		$content = $Content->findByType($plugin . '.' . $type, $entityId);
		if(!$content) {
			return false;
		}
		
		// URL生成
		$strUrl = $content['Content']['url'];
		$pass = [];
		$named = [];
		$setting = Configure::read('BcContents.items.' . $plugin . '.' . $type);
		if(!$params) {
			if(empty($setting['omitViewAction']) && $setting['routes']['view']['action'] != $action) {
				$strUrl .= '/' . $action;
			}
		} else {
			if(empty($setting['omitViewAction'])) {
				$strUrl .= '/' . $action;
			}
			foreach($params as $key => $param) {
				if(!is_numeric($key)) {
					if($key == 'page' && !$param) {
						$param = 1;
					}
					if(!is_array($param)) {
						$named[] = $key . ':' . $param;	
					}
				} else {
					$pass[] = $param;
				}
			}
		}
		if($pass) {
			$strUrl .= '/' . implode('/', $pass);
		}
		if($named) {
			$strUrl .= '/' . implode('/', $named);
		}
		return $strUrl;
	}

/**
 * パラメーターよりコンテンツタイプを取得する
 *
 * @param array $params パラメーター
 * @param bool $useAction アクションを判定に入れるかどうか
 * @return bool|string
 */
	protected function _getContentTypeByParams($params, $useAction = true) {
		if(empty($params['plugin'])) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($params['plugin']);
		}
		$settings = Configure::read('BcContents.items.' . $plugin);
		if(!$settings) {
			return false;
		}
		foreach($settings as $key => $setting) {
			if(empty($setting['routes']['view'])) {
				continue;
			}
			$viewParams = $setting['routes']['view'];
			if($useAction) {
				if($params['controller'] == $viewParams['controller'] && $params['action'] == $viewParams['action']) {
					return $key;
				}
			} else {
				if($params['controller'] == $viewParams['controller']) {
					return $key;
				}
			}
		}
		return false;
	}

}
