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

$request = null;
if (!empty(self::$_requests[0])) {
	$request = self::$_requests[0];
}
// パラメータ取得
$parameter = getPureUrl($request);

Configure::write('BcRequest.pureUrl', $parameter); // requestAction の場合、bootstrapが実行されないので、urlParamを書き換える
$agent = Configure::read('BcRequest.agent');
$agentAlias = Configure::read('BcRequest.agentAlias');
$agentPrefix = Configure::read('BcRequest.agentPrefix');
$authPrefixes = Configure::read('BcAuthPrefix');

if (BC_INSTALLED && !$isUpdater && !$isMaintenance) {

	$pluginMatch = array();
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
 * プラグインルーティング
 */
	CakePlugin::routes();

/**
 * コンテンツ管理ルーティング
 */
	if(!BcUtil::isAdminSystem()) {
		App::uses('BcContentsRoute', 'Routing/Route');
		Router::connect('*', [], array('routeClass' => 'BcContentsRoute'));
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
 * 携帯標準ルーティング
 */
	if ($agent) {
		// プラグイン
		Router::connect("/{$agentAlias}/:plugin/:controller", array('prefix' => $agentPrefix, 'action' => 'index'), $pluginMatch);
		Router::connect("/{$agentAlias}/:plugin/:controller/:action/*", array('prefix' => $agentPrefix), $pluginMatch);
		Router::connect("/{$agentAlias}/:plugin/:action/*", array('prefix' => $agentPrefix), $pluginMatch);
		// 携帯ノーマル
		Router::connect("/{$agentAlias}/:controller/:action/*", array('prefix' => $agentPrefix));
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
