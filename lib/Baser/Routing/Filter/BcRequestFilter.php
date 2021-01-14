<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Routing.Filter
 * @since           baserCMS v 3.0.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('DispatcherFilter', 'Routing');

/**
 * Class BcRequestFilter
 *
 * CakeRequestに検出器を追加するためのフィルター
 *
 * （例）$request->is('admin')
 *      $request->is('smartphone')
 *        $request->is(array('smartphone', 'mobile')) // OR
 *        $request->isAll(array('smartphone', 'page_display')) // AND
 *
 * @package Baser.Routing.Filter
 */
class BcRequestFilter extends DispatcherFilter
{

	/**
	 * Default priority for all methods in this filter
	 * This filter should run before the request gets parsed by router
	 * @var int
	 */
	public $priority = 6;

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
		$this->addDetectors($request);

		// アセットならスキップ
		if ($this->isAsset($request)) {
			Configure::write('BcRequest.asset', true);
			return;
		}
		if (Configure::read('BcRequest.isUpdater')) {
			return;
		}

		// URLからエージェントを取得
		$site = BcSite::findCurrent(true);
		if ($site && $site->device) {
			/*
			 * =========================================================
			 * /m/files/... へのアクセスの場合、/files/... へ自動リダイレクト
			 * CMSで作成するページ内のリンクは、モバイルでアクセスすると、
			 * 自動的に、/m/ 付のリンクに書き換えられてしまう為、
			 * files内のファイルへのリンクがリンク切れになってしまうので暫定対策。
			 *
			 * 2014/12/30 nakae bootstrap.phpから移行
			 * =========================================================
			 */
			$param = preg_replace('/^' . $site->alias . '\//', '', $request->url);
			if (preg_match('/^files/', $param)) {
				$response->statusCode(301);
				$response->header('Location', "{$request->base}/{$param}");
				return $response;
			}
		}

		//bootstrapから移動する
		//Configure::write('BcRequest.isUpdater', $this->isUpdate($request));
		//Configure::write('BcRequest.isMaintenance', $this->isMaintenance($request));
	}

	/**
	 * リクエスト検出器の設定を取得
	 *
	 * @return array
	 */
	public function getDetectorConfigs()
	{
		$configs = [];

		$configs['admin'] = ['callback' => [$this, 'isAdmin']];
		$configs['asset'] = ['callback' => [$this, 'isAsset']];
		$configs['install'] = ['callback' => [$this, 'isInstall']];
		$configs['maintenance'] = ['callback' => [$this, 'isMaintenance']];
		$configs['update'] = ['callback' => [$this, 'isUpdate']];
		$configs['page'] = ['callback' => [$this, 'isPage']];
		$configs['requestview'] = ['callback' => [$this, 'isRequestView']];

		$agents = BcAgent::findAll();
		foreach($agents as $agent) {
			$configs[$agent->name] = ['env' => 'HTTP_USER_AGENT', 'pattern' => $agent->getDetectorRegex()];
		}

		return $configs;
	}

	/**
	 * リクエスト検出器を追加する
	 *
	 * @param CakeRequest $request リクエスト
	 * @return void
	 */
	public function addDetectors(CakeRequest $request)
	{
		foreach($this->getDetectorConfigs() as $name => $callback) {
			$request->addDetector($name, $callback);
		}
	}

	/**
	 * 管理画面のURLかどうかを判定
	 *
	 * @param CakeRequest $request リクエスト
	 * @return bool
	 */
	public function isAdmin(CakeRequest $request)
	{
		$adminPrefix = Configure::read('BcAuthPrefix.admin.alias');
		$regex = '/^' . $adminPrefix . '($|\/)/';
		return (bool)preg_match($regex, $request->url);
	}

	/**
	 * アセットのURLかどうかを判定
	 *
	 * @param CakeRequest $request リクエスト
	 * @return bool
	 */
	public function isAsset(CakeRequest $request)
	{
		$dirs = ['css', 'js', 'img'];
		$exts = ['css', 'js', 'gif', 'jpg', 'jpeg', 'png', 'ico', 'svg', 'swf'];

		$dirRegex = implode('|', $dirs);
		$extRegex = implode('|', $exts);

		$assetRegex = '/^(' . $dirRegex . ')\/.+\.(' . $extRegex . ')$/';
		$themeAssetRegex = '/^theme\/[^\/]+?\/(' . $dirRegex . ')\/.+\.(' . $extRegex . ')$/';

		$uri = $request->url;
		return preg_match($assetRegex, $uri) || preg_match($themeAssetRegex, $uri);
	}

	/**
	 * インストール用のURLかどうかを判定
	 * [注]ルーターによるURLパース後のみ
	 *
	 * @param CakeRequest $request リクエスト
	 * @return bool
	 */
	public function isInstall(CakeRequest $request)
	{
		return $request->params['controller'] === 'installations';
	}

	/**
	 * メンテナンス用のURLかどうかを判定
	 *
	 * @param CakeRequest $request リクエスト
	 * @return bool
	 */
	public function isMaintenance(CakeRequest $request)
	{
		$slug = 'maintenance';
		return in_array($request->url, [$slug, "{$slug}/", "{$slug}/index"]);
	}

	/**
	 * アップデート用のURLかどうかを判定
	 *
	 * @param CakeRequest $request リクエスト
	 * @return bool
	 */
	public function isUpdate(CakeRequest $request)
	{
		$slug = Configure::read('BcApp.updateKey');
		return in_array($request->url, [$slug, "{$slug}/", "{$slug}/index"]);
	}

	/**
	 * 固定ページ表示用のURLかどうかを判定
	 * [注]ルーターによるURLパース後のみ
	 *
	 * @param CakeRequest $request リクエスト
	 * @return bool
	 */
	public function isPage(CakeRequest $request)
	{
		return $request->params['controller'] === 'pages'
			&& $request->params['action'] === 'display';
	}

	/**
	 * baserCMSの基本処理を必要とするかどうか
	 *
	 * @param CakeRequest $request
	 * @return bool
	 */
	public function isRequestView(CakeRequest $request)
	{
		return !(isset($request->query['requestview']) && $request->query['requestview'] === "false");
	}
}
