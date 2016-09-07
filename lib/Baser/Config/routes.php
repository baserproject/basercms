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

/**
 * vendors内の静的ファイルの読み込みの場合はスキップ
 */
if (Configure::read('BcRequest.asset')) {
	return;
}
if (BC_INSTALLED || isConsole()) {
	$isMaintenance = Configure::read('BcRequest.isMaintenance');
	$isUpdater = Configure::read('BcRequest.isUpdater');
}
/**
 * Object::cakeError() の為、router.php が読み込まれた事をマークしておく
 * BaserAppModel::cakeError で利用
 */
Configure::write('BcRequest.routerLoaded', true);
// プラグインの基底クラス読み込み
// bootstrapで読み込むの場合、継承元のクラスが読み込まれていない為エラーとなる。
App::uses('BaserPluginApp', 'Controller');
App::uses('BaserPluginAppModel', 'Model');

$request = new CakeRequest();
$authPrefixes = Configure::read('BcAuthPrefix');
$pluginMatch = [];

if (BC_INSTALLED && !$isUpdater && !$isMaintenance) {
	$plugins = CakePlugin::loaded();
	if ($plugins) {
		foreach ($plugins as $key => $value) {
			$plugins[$key] = Inflector::underscore($value);
		}
		$pluginMatch = array('plugin' => implode('|', $plugins));
	}
	
/**
 * 名前付きパラメータを追加 
 */
	Router::connectNamed(array('sortmode', 'num', 'page', 'sort', 'direction'));
	
/**
 * プラグイン
 * 
 * コンテンツ管理ルーティングよりも優先させる為に先に記述
 */
	Router::connect("/:plugin/:controller/:action/*", [], $pluginMatch);
	
/**
 * コンテンツ管理ルーティング
 */
	if(!BcUtil::isAdminSystem()) {
		App::uses('BcContentsRoute', 'Routing/Route');
		Router::connect('*', [], array_merge($pluginMatch, array('routeClass' => 'BcContentsRoute')));
	}
	
/**
 * 認証プレフィックス
 */
	if ($authPrefixes && is_array($authPrefixes)) {
		foreach ($authPrefixes as $key => $authPrefix) {
			$prefix = $key;
			if (!empty($authPrefix['alias'])) {
				$alias = $authPrefix['alias'];
			} else {
				$alias = $prefix;
			}
			if ($alias) {
				Router::connect("/{$alias}", array('prefix' => $prefix, $prefix => true, 'controller' => 'dashboard', 'action' => 'index'));
				if (CakePlugin::loaded()) {
					Router::connect("/{$alias}/:plugin/:controller", array('prefix' => $prefix, $prefix => true), $pluginMatch);
					Router::connect("/{$alias}/:plugin/:controller/:action/*", array('prefix' => $prefix, $prefix => true), $pluginMatch);
					Router::connect("/{$alias}/:plugin/:action/*", array('prefix' => $prefix, $prefix => true), $pluginMatch);
				}
				Router::connect("/{$alias}/:controller/:action/*", array('prefix' => $prefix, $prefix => true));
			}
		}
	}
}

if (BC_INSTALLED || isConsole()) {

/**
 * サブサイト標準ルーティング
 */
	$Site = ClassRegistry::init('Site');
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
		// 携帯ノーマル
		Router::connect("/{$siteAlias}/:controller/:action/*", array('prefix' => $sitePrefix));
	}

/**
 * フィード出力
 * 拡張子rssの場合は、rssディレクトリ内のビューを利用する
 */
	Router::parseExtensions('rss');
} else {
	Router::connect('/', array('controller' => 'installations', 'action' => 'index'));
}
/**
 * アップデーター用 
 */
$updateKey = Configure::read('BcApp.updateKey');
Router::connect('/' . $updateKey, array('controller' => 'updaters', 'action' => 'index'));
Router::connect('/' . $updateKey . '/index', array('controller' => 'updaters', 'action' => 'index'));
/**
 * インストーラー用
 */
Router::connect('/install', array('controller' => 'installations', 'action' => 'index'));
