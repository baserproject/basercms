<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

// CakeRequest 判定できる関数があるが、CakeRequest での判定は、
// routes.php の処理が完了している事が前提である為利用できない
$isMaintenance = Configure::read('BcRequest.isMaintenance');
$isUpdater = Configure::read('BcRequest.isUpdater');
$isInstalled = Configure::read('BcRequest.isInstalled');

// ==================================================================
// Object::cakeError() の為、router.php が読み込まれた事をマークしておく
// BaserAppModel::cakeError で利用
// ==================================================================
Configure::write('BcRequest.routerLoaded', true);

/**
 * vendors内の静的ファイルの読み込みの場合はスキップ
 */
if (Configure::read('BcRequest.asset') || $isMaintenance) {
	return;
}

/**
 * インストーラー
 */
if (!$isInstalled) {
	Router::connect('/', array('controller' => 'installations', 'action' => 'index'));
	Router::connect('/install', array('controller' => 'installations', 'action' => 'index'));
	return;
}

// プラグインの基底クラス読み込み
// bootstrapで読み込む場合、継承元のクラスが読み込まれていない為エラーとなる。
App::uses('BaserPluginApp', 'Controller');
App::uses('BaserPluginAppModel', 'Model');


/**
 * アップデーター
 */
if($isUpdater) {
	$updateKey = Configure::read('BcApp.updateKey');
	Router::connect('/' . $updateKey, array('controller' => 'updaters', 'action' => 'index'));
	Router::connect('/' . $updateKey . '/index', array('controller' => 'updaters', 'action' => 'index'));
	return;
}

/**
 * プラグイン
 *
 * コンテンツ管理ルーティングよりも優先させる為に先に記述
 */
 	$pluginMatch = [];
	$plugins = CakePlugin::loaded();
	if ($plugins) {
		foreach($plugins as $key => $value) {
			$plugins[$key] = Inflector::underscore($value);
		}
		$pluginMatch = array('plugin' => implode('|', $plugins));
		Router::connect("/:plugin/:controller/:action/*", [], $pluginMatch);
	}

/**
 * 名前付きパラメータを追加
 */
	Router::connectNamed(array('sortmode', 'num', 'page', 'sort', 'direction'));

/**
 * 認証プレフィックス
 */
	$authPrefixes = Configure::read('BcAuthPrefix');
	if ($authPrefixes && is_array($authPrefixes)) {
		foreach($authPrefixes as $prefix => $authPrefix) {
			if (!empty($authPrefix['alias'])) {
				$alias = $authPrefix['alias'];
			} else {
				$alias = $prefix;
			}
			Router::connect("/{$alias}", array('prefix' => $prefix, $prefix => true, 'controller' => 'dashboard', 'action' => 'index'));
			if (CakePlugin::loaded()) {
				Router::connect("/{$alias}/:plugin/:controller/:action/*", array('prefix' => $prefix, $prefix => true), $pluginMatch);
				Router::connect("/{$alias}/:plugin/:controller/", array('prefix' => $prefix, $prefix => true), $pluginMatch);
				Router::connect("/{$alias}/:plugin/:action/*", array('prefix' => $prefix, $prefix => true), $pluginMatch);
			}
			Router::connect("/{$alias}/:controller/:action/*", array('prefix' => $prefix, $prefix => true));
			Router::connect("/{$alias}/:controller/", ['prefix' => $prefix, $prefix => true]);
		}
	}

/**
 * コンテンツ管理ルーティング
 */
	App::uses('BcContentsRoute', 'Routing/Route');
	Router::connect('*', [], array_merge($pluginMatch, array('routeClass' => 'BcContentsRoute')));
	Router::promote();	// 優先順位を最優先とする	

	if (!BcUtil::isAdminSystem()) {

	/**
	 * サブサイト標準ルーティング
	 */
	try {
		$Site = ClassRegistry::init('Site');
		$request = new CakeRequest();
		$site = $Site->findByUrl($request->url);
		$siteAlias = $sitePrefix = '';
		if($site) {
			$siteAlias = $site['Site']['alias'];
			$sitePrefix = $site['Site']['name'];
		}
		if ($siteAlias) {
			// プラグイン
			Router::connect("/{$siteAlias}/:plugin/:controller", array('prefix' => $sitePrefix, 'action' => 'index'), $pluginMatch);
			Router::connect("/{$siteAlias}/:plugin/:controller/:action/*", array('prefix' => $sitePrefix), $pluginMatch);
			Router::connect("/{$siteAlias}/:plugin/:action/*", array('prefix' => $sitePrefix), $pluginMatch);
			// モバイルノーマル
			Router::connect("/{$siteAlias}/:controller/:action/*", array('prefix' => $sitePrefix));
			Router::connect("/{$siteAlias}/:controller", array('prefix' => $sitePrefix, 'action' => 'index'));
		}
	} catch (Exception $e) {}
		
	/**
	 * フィード出力
	 * 拡張子rssの場合は、rssディレクトリ内のビューを利用する
	 */
		Router::parseExtensions('rss');

	}
