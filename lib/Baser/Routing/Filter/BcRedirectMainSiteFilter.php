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

/**
 * Class BcRedirectMainSiteFilter
 *
 * サブサイトにコンテンツが存在しない場合、同階層のメインサイトのコンテンツを確認し、
 * 存在していれば、メインサイトへリダイレクトをする。
 *
 * （例）
 * /s/service → /service
 *
 * @package Baser.Routing.Filter
 */
class BcRedirectMainSiteFilter extends DispatcherFilter
{

	/**
	 * priority
	 *
	 * URLの存在確認が完了しているタイミングを前提としている為、
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
	public function beforeDispatch(CakeEvent $event)
	{
		$request = $event->data['request'];
		$response = $event->data['response'];
		if (!empty($request->params['Content'])) {
			return;
		} else {
			if ($this->_existController($request)) {
				return;
			}
		}
		$site = BcSite::findCurrent();
		if (!$site || !$site->enabled) {
			return;
		}
		$mainSite = $site->getMain();
		if (!$mainSite) {
			return;
		}
		$mainSiteUrl = '/' . preg_replace('/^' . $site->alias . '\//', '', $request->url);
		if ($mainSite->alias) {
			$mainSiteUrl = '/' . $mainSite->alias . $mainSiteUrl;
		}
		if ($mainSiteUrl) {
			$request = new CakeRequest($mainSiteUrl);
			$params = Router::parse($request->url);
			$request->addParams($params);
			if ($this->_existController($request)) {
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
	protected function _existController($request)
	{
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
