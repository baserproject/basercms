<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Routing.Filter
 * @since           baserCMS v 4.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('CakeRoute', 'Routing/Route');
App::uses('BcSite', 'Lib');

/**
 * Class BcContentsRoute
 *
 * @package Baser.Routing.Route
 */
class BcContentsRoute extends CakeRoute
{

	/**
	 * Parses a string URL into an array. If a plugin key is found, it will be copied to the
	 * controller parameter
	 *
	 * @param string $url The URL to parse
	 * @return mixed false on failure, or an array of request parameters
	 */
	public function parse($url)
	{
		if (is_array($url)) {
			return false;
		}

		if (BcUtil::isAdminSystem($url)) {
			return false;
		}

		$request = Router::getRequest(true);
		if (!$request) {
			return false;
		}

		//管理システムにログインしているかつプレビューの場合は公開状態のステータスは無視する
		$publish = true;
		if ((!empty($request->query['preview']) || !empty($request->query['force'])) && BcUtil::loginUser()) {
			$publish = false;
			if (!empty($request->query['host'])) {
				Configure::write('BcEnv.host', $request->query['host']);
			} else {
				Configure::write('BcEnv.host', '');
			}
		}

		$sameUrl = false;
		$site = BcSite::findCurrentSub(true);
		if ($site) {
			// 同一URL対応
			$sameUrl = true;
			$checkUrl = $site->makeUrl($request);
			@header('Vary: User-Agent');
		} else {
			$site = BcSite::findCurrent(true);
			if ($site && !is_null($site->name)) {
				if ($site->useSubDomain) {
					$checkUrl = '/' . $site->alias . (($url)? $url : '/');
				} else {
					$checkUrl = (($url)? $url : '/');
				}
			} else {
				if (!empty($request->query['force']) && BcUtil::isAdminUser()) {
					// =================================================================================================
					// 2016/11/10 ryuring
					// 別ドメインの際に、固定ページのプレビューで、正しくサイト情報を取得できない。
					// そのため、文字列でリクエストアクションを送信し、URLでホストを判定する。
					// =================================================================================================
					$tmpSite = BcSite::findByUrl($url);
					if (!is_null($tmpSite)) {
						$site = $tmpSite;
					}
				}
				$checkUrl = (($url)? $url : '/');
			}
		}

		$Content = ClassRegistry::init('Content');
		$content = $Content->findByUrl($checkUrl, $publish, false, $sameUrl, $site->useSubDomain);
		if (!$content) {
			$content = $Content->findByUrl($checkUrl, $publish, true, $sameUrl, $site->useSubDomain);
		}

		if (!$content) {
			return false;
		}

		// 管理画面にログインしていないとき、リダイレクトする設定ならば処理をする
		$redirect = Configure::read('BcAuthPrefix.admin.previewRedirect');
		if ($redirect) {
			// データが存在してもプレビューで管理システムにログインしていない場合はログイン画面に遷移
			if ((!empty($request->query['preview']) || !empty($request->query['force'])) && !BcUtil::loginUser()) {
				$_SESSION['Auth']['redirect'] = $_SERVER['REQUEST_URI'];
				header('Location: ' . topLevelUrl(false) . baseUrl() . Configure::read('BcAuthPrefix.admin.alias') . '/users/login');
				exit();
			}
		}

		if ($content['Content']['alias_id'] && !$Content->isPublishById($content['Content']['alias_id'])) {
			return false;
		}
		$request->params['Content'] = isset($content['Content'])? $content['Content'] : null;
		$request->params['Site'] = $content['Site'];
		$url = $site->getPureUrl($url);
		$params = $this->getParams($url, $content['Content']['url'], $content['Content']['plugin'], $content['Content']['type'], $content['Content']['entity_id'], $site->alias);
		if ($params) {
			return $params;
		}
		return false;
	}

	/**
	 * コンテンツに関連するパラメーター情報を取得する
	 *
	 * @param $requestUrl
	 * @param $entryUrl
	 * @param $plugin
	 * @param $type
	 * @param $entityId
	 * @return mixed false|array
	 */
	public function getParams($requestUrl, $entryUrl, $plugin, $type, $entityId, $alias)
	{
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
			// 別ドメインの場合は１階層目を除外
			$pureEntryUrl = $entryUrl;
			if ($alias) {
				$pureEntryUrl = preg_replace('/^\/' . preg_quote($alias, '/') . '\//', '/', $pureEntryUrl);
			}
			if ($type == 'Page' || $type == 'ContentLink') {
				$url = preg_replace('/^\//', '', $entryUrl);
				$pass = explode('/', $url);
			} elseif ($requestUrl != $pureEntryUrl) {
				// コントローラーとなり得る部分までの文字列を除外してアクションを取得
				$url = preg_replace('/^' . preg_quote($pureEntryUrl, '/') . '/', '', $requestUrl);
				$url = preg_replace('/^\//', '', $url);
				$urlAry = explode('/', $url);
				$action = $urlAry[0];
				// アクション部分を除外
				array_shift($urlAry);
				// パラメーターを取得（pass / named）
				if ($urlAry) {
					foreach($urlAry as $param) {
						if (strpos($param, ':') !== false) {
							list($key, $value) = explode(':', $param);
							$named[$key] = $value;
						} else {
							$pass[] = $param;
						}
					}
				}
			}
			if ($plugin == 'Core') {
				$plugin = '';
			}
			$controllerClass = Inflector::camelize($viewParams['controller']) . 'Controller';
			App::uses($controllerClass, ($plugin)? $plugin . '.Controller' : 'Controller');
			$methods = get_class_methods($controllerClass);
			if (!in_array($action, $methods)) {
				return false;
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
	 * Reverse route
	 *
	 * @param array $url Array of parameters to convert to a string.
	 * @return mixed either false or a string URL.
	 */
	public function match($url)
	{

		// フロント以外のURLの場合にマッチしない
		if (!empty($url['admin'])) {
			return false;
		}

		// プラグイン確定
		if (empty($url['plugin'])) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($url['plugin']);
		}

		// アクション確定
		if (!empty($url['action'])) {
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
		if ($type) {
			unset($params['action']);
		} else {
			$type = $this->_getContentTypeByParams($url, false);
		}
		if (!$type) {
			return false;
		}

		// エンティティID確定
		$entityId = $contentId = null;
		if (isset($url['entityId'])) {
			$entityId = $url['entityId'];
		} else {
			$request = Router::getRequest(true);
			if (!empty($request->params['entityId'])) {
				$entityId = $request->params['entityId'];
			}
			if (!empty($request->params['Content']['alias_id'])) {
				$contentId = $request->params['Content']['id'];
			}
		}

		// コンテンツ確定、できなければスルー
		if ($type == 'Page') {
			$pass = [];
			foreach($params as $key => $param) {
				if (!is_string($key)) {
					$pass[] = $param;
					unset($params[$key]);
				}
			}
			$strUrl = '/' . implode('/', $pass);
		} else {
			$Content = ClassRegistry::init('Content');
			if ($contentId) {
				$conditions = ['Content.id' => $contentId];
			} else {
				$conditions = [
					'Content.plugin' => $plugin,
					'Content.type' => $type,
					'Content.entity_id' => $entityId
				];
			}
			$strUrl = $Content->field('url', $conditions);
		}

		if (!$strUrl) {
			return false;
		}

		// URL生成
		$site = BcSite::findByUrl($strUrl);
		if ($site && $site->useSubDomain) {
			$strUrl = preg_replace('/^\/' . preg_quote($site->alias, '/') . '\//', '/', $strUrl);
		}
		$pass = [];
		$named = [];
		$setting = Configure::read('BcContents.items.' . $plugin . '.' . $type);
		if (!$params) {
			if (empty($setting['omitViewAction']) && $setting['routes']['view']['action'] != $action) {
				$strUrl .= '/' . $action;
			}
		} else {
			if (empty($setting['omitViewAction'])) {
				$strUrl .= '/' . $action;
			}
			foreach($params as $key => $param) {
				if (!is_numeric($key)) {
					if ($key == 'page' && !$param) {
						$param = 1;
					}
					if (!is_array($param)) {
						$named[] = $key . ':' . $param;
					}
				} else {
					$pass[] = $param;
				}
			}
		}
		if ($pass) {
			$strUrl .= '/' . implode('/', $pass);
		}
		if ($named) {
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
	protected function _getContentTypeByParams($params, $useAction = true)
	{
		if (empty($params['plugin'])) {
			$plugin = 'Core';
		} else {
			$plugin = Inflector::camelize($params['plugin']);
		}
		$settings = Configure::read('BcContents.items.' . $plugin);
		if (!$settings) {
			return false;
		}
		foreach($settings as $key => $setting) {
			if (empty($setting['routes']['view'])) {
				continue;
			}
			$viewParams = $setting['routes']['view'];
			if ($useAction) {
				if ($params['controller'] == $viewParams['controller'] && $params['action'] == $viewParams['action']) {
					return $key;
				}
			} else {
				if ($params['controller'] == $viewParams['controller']) {
					return $key;
				}
			}
		}
		return false;
	}

}
