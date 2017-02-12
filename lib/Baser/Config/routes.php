<?php

/**
 * ルーティング定義
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
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

// パラメータ取得
$parameter = getPureUrl(Router::getRequest(true));
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
 * プラグイン判定 ＆ プラグイン名の書き換え
 * 
 * DBに登録したデータを元にURLのプラグイン名部分を書き換える。
 * 一つのプラグインで二つのコンテンツを設置した場合に利用する。
 * あらかじめ、plugin_contentsテーブルに、URLに使う名前とコンテンツを特定する。
 * プラグインごとの一意のキー[content_id]を保存しておく。
 *
 * content_idをコントローラーで取得するには、$plugins_controllerのcontentIdプロパティを利用する。
 * Router::connectの引数として値を与えると、$this->Html->linkなどで、
 * Routerを利用する際にマッチしなくなりURLがデフォルトのプラグイン名となるので注意
 * 
 * DBに接続できない場合、CakePHPのエラーメッセージが表示されてしまう為、 try を利用
 */
	try {
		$PluginContent = ClassRegistry::init('PluginContent');
	} catch (Exception $ex) {
		$PluginContent = null;
	}
	
	if ($PluginContent) {
		$pluginContent = $PluginContent->currentPluginContent($parameter);
		if ($pluginContent) {
			$pluginContentName = $pluginContent['PluginContent']['name'];
			$pluginName = $pluginContent['PluginContent']['plugin'];
			if (!$agent) {
				Router::connect("/{$pluginContentName}/:action/*", array('plugin' => $pluginName, 'controller' => $pluginName));
				Router::connect("/{$pluginContentName}", array('plugin' => $pluginName, 'controller' => $pluginName, 'action' => 'index'));
			} else {
				Router::connect("/{$agentAlias}/{$pluginContentName}/:action/*", array('prefix' => $agentPrefix, 'plugin' => $pluginName, 'controller' => $pluginName));
				Router::connect("/{$agentAlias}/{$pluginContentName}", array('prefix' => $agentPrefix, 'plugin' => $pluginName, 'controller' => $pluginName, 'action' => 'index'));
			}
		}
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
 * ページ機能拡張
 * cakephp の ページ機能を利用する際、/pages/xxx とURLである必要があるが
 * それを /xxx で呼び出す為のルーティング
 */
	$adminPrefix = Configure::read('Routing.prefixes.0');
	if (!preg_match("/^{$adminPrefix}/", $parameter)) {
		/* 1.5.10 以降 */
		App::uses('Page', 'Model');
		$Page = ClassRegistry::init('Page');
		if ($Page) {

			$parameter = urldecode($parameter);

			if (!$parameter) {
				$params = array('index');
			} elseif (preg_match('/\/$/is', $parameter)) {
				$params = array($parameter . 'index');
			} else {
				$params = array($parameter, $parameter . '/index');
			}

			foreach ($params as $param) {

				$linkedPages = $Page->isLinked($agentPrefix, '/' . $param);

				if (!$agent || $linkedPages) {
					$url = "/{$param}";
				} else {
					$url = "/{$agentPrefix}/{$param}";
				}

				if ($Page->isPageUrl($url)) {
					if (!$agent) {
						Router::connect("/{$parameter}", array_merge(array('controller' => 'pages', 'action' => 'display'), explode('/', $param)));
					} else {
						Router::connect("/{$agentAlias}/{$parameter}", array_merge(array('prefix' => $agentPrefix, 'controller' => 'pages', 'action' => 'display'), explode('/', $param)));
					}
					break;
				} else {

					// 拡張子付き（.html）の場合も透過的にマッチングさせる
					if (preg_match('/^(.+?)\.html$/', $url, $matches)) {
						$url = $matches[1];
						if ($Page->isPageUrl($url) && ($Page->checkPublish($url) || !empty($_SESSION['Auth']['User']))) {
							$param = str_replace('.html', '', $param);
							if (!$agent) {
								Router::connect("/{$parameter}", am(array('controller' => 'pages', 'action' => 'display'), $param));
							} else {
								Router::connect("/{$agentAlias}/{$parameter}", am(array('prefix' => $agentPrefix, 'controller' => 'pages', 'action' => 'display'), explode('/', $param)));
							}
							break;
						}
					}
				}
			}
		}
	}

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
