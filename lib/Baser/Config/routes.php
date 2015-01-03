<?php

/**
 * ルーティング定義
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * todo ルートの優先順位について整理する
 */

/**
 * vendors内の静的ファイルの読み込みの場合はスキップ
 */
if (Configure::read('BcRequest.asset')) {
	return;
}

if (!BC_INSTALLED && !isConsole()) {
	/**
	 * 未インストールならインストール用のルートのみで十分
	 */
	Router::connect('/', array('controller' => 'installations', 'action' => 'index'));
	Router::connect('/install', array('controller' => 'installations', 'action' => 'index'));
	return;
}

//互換のため
Router::connect('/install', array('controller' => 'installations', 'action' => 'index'));

/**
 * Routerによるルートのパースはそれ自体がswitch-caseのようなものなので徒らにifを使うと逆に難解になる
 * 同じルートを処理するアクションをサイトの設定の状態によって変更したいような場合を除き、routes.phpの中での分岐は最低限に抑える
 * できるだけルートの優先順位で対応し、Routerに登録するルート自体はあまり変わらないようにする
 */
//$isMaintenance = Configure::read('BcRequest.isMaintenance');
//$isUpdater = Configure::read('BcRequest.isUpdater');

/**
 * アップデーター用
 * todo アップデート画面を表示する条件について整理する
 * そもそもアップデート用のURLは管理画面においたほうがいいのではないか？
 */
$updateKey = Configure::read('BcApp.updateKey');
Router::connect('/' . $updateKey, array('controller' => 'updaters', 'action' => 'index'));
Router::connect('/' . $updateKey . '/index', array('controller' => 'updaters', 'action' => 'index'));

/**
 * メンテナンスモード用
 * todo サイトのメンテナンスモードが有効な場合はフロント側をすべてmaintenanceに飛ばすようにする
 * 事前にDispatcherFilterでリダイレクトする選択肢もある
 * 現状、BcAppController::beforeFilter()で判定しているため責務が集まりすぎている
 */
$maintenanceKey = 'maintenance';
Router::connect('/' . $maintenanceKey, array('controller' => 'maintenance', 'action' => 'index'));
Router::connect('/' . $maintenanceKey . '/index', array('controller' => 'maintenance', 'action' => 'index'));



/**
 * Object::cakeError() の為、router.php が読み込まれた事をマークしておく
 * BaserAppModel::cakeError で利用
 * todo cakeError()は削除されたはずなので不要なら削る
 */
Configure::write('BcRequest.routerLoaded', true);

// プラグインの基底クラス読み込み
// bootstrapで読み込むの場合、継承元のクラスが読み込まれていない為エラーとなる。
App::uses('BaserPluginApp', 'Controller');
App::uses('BaserPluginAppModel', 'Model');


//アップデートとメンテナンスモードは先にマッチさせておけばこのifも不要
//if (!$isUpdater && !$isMaintenance) {

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
 * todo カスタムルートクラスを用いてここでのCakeRequest参照を止める
 */
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
 * todo DBに接続できないケースはより早期の段階、bootstrapかDispatcherFilterで弾いておく
 * todo カスタムルートクラスに移行
 * 現状、管理画面より優先順位が高いが下げるべきではないか
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




/**
 * ページ機能拡張
 * CakePHP の ページ機能を利用する際、/pages/xxx とURLである必要があるが
 * それを /xxx で呼び出す為のルーティング
 * todo カスタムルートクラスに移行
 */
//$adminPrefix = Configure::read('Routing.prefixes.0');
//認証プレフィックスのルートは既にマッチしているはずなのでこのifは不要
//if (!preg_match("/^{$adminPrefix}/", $parameter)) {

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

		if ($Page->isPageUrl($url) && $Page->checkPublish($url)) {
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
//}

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
