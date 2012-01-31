<?php
/* SVN FILE: $Id$ */
/**
 * 起動スクリプト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
	require ROOT.DS.'baser'.DS.'config'.DS.'paths.php';
	require BASER.'basics.php';
	/* ConnectionManager ハック */
	// baserフォルダ内のデータソースも走査するようにした
	App::import('Core', 'ConnectionManager', array('file'=>CAKE_CORE_INCLUDE_PATH.DS.'baser'.DS.'connection_manager.php'));
/**
 * Baserパス追加
 */
	$modelPaths[] = BASER_MODELS;
	$behaviorPaths[] = BASER_BEHAVIORS;
	$controllerPaths[] = BASER_CONTROLLERS;
	$componentPaths[] = BASER_COMPONENTS;
	$viewPaths[] = BASER_VIEWS;
	$viewPaths[] = WWW_ROOT;
	$helperPaths[] = BASER_HELPERS;
	$pluginPaths[] = BASER_PLUGINS;
	// Rewriteモジュールなしの場合、/index.php/css/style.css 等ではCSSファイルが読み込まれず、
	// $html->css / $javascript->link 等では、/app/webroot/css/style.css というURLが生成される。
	// 上記理由により以下のとおり変更
	// ・HelperのwebrootメソッドをRouter::urlでパス解決をするように変更し、/index.php/css/style.css というURLを生成させる。
	// ・走査URLをvendorsだけではなく、app/webroot内も追加
	$vendorPaths[] = WWW_ROOT;
	$vendorPaths[] = BASER_VENDORS;
	$localePaths[] = BASER_LOCALES;
	//$shellPaths[];
/**
 * vendors内の静的ファイルの読み込みの場合はスキップ
 */
	$uri = @$_SERVER['REQUEST_URI'];
	$baseUrl = baseUrl();
	if (strpos($uri, $baseUrl.'css/') !== false || strpos($uri, $baseUrl.'js/') !== false || strpos($uri, $baseUrl.'img/') !== false) {
		$assets = array('js' , 'css', 'gif' , 'jpg' , 'png' );
		$ext = array_pop(explode('.', $uri));
		if(in_array($ext, $assets)){
			Configure::write('Baser.Asset', true);
			return;
		}
	}
/**
 * 配置パターン
 */
	if(!preg_match('/'.preg_quote(docRoot(), '/').'/', ROOT)) {
		// CakePHP標準の配置
		define('DEPLOY_PATTERN', 3);
	} elseif(ROOT.DS == WWW_ROOT) {
		// webrootをドキュメントルートにして、その中に app / baser / cake を配置
		define('DEPLOY_PATTERN', 2);
	} else {
		// baserCMS配布時の配置
		define('DEPLOY_PATTERN', 1);
	}
/**
 * 設定ファイル読み込み
 * install.php で設定している為、一旦読み込んで再設定
 */
	$baserSettings = Configure::read('Baser');
	if(Configure::load('baser')===false) {
		include BASER_CONFIGS.'baser.php';
		Configure::write($config);
	}
	if($baserSettings) {
		foreach ($baserSettings as $key => $value) {
			Configure::write('Baser.'.$key, $value);
		}
	}
/**
 * tmpフォルダ確認
 */
	if(isInstalled()) {
		checkTmpFolders();
	}
/**
 * baserUrl取得
 */
	$baseUrl = baseUrl();
/**
 * 文字コードの検出順を指定
 */
	mb_detect_order(Configure::read('Baser.detectOrder'));
/**
 * セッションタイムアウト設定
 * core.php で設定された値よりも早い段階でログアウトしてしまうのを防止
 */
	if (function_exists('ini_set')) {
		$sessionTimeouts = array('high'=>10,'medium'=>100,'low'=>300);
		$securityLevel = Configure::read('Security.level');
		if (isset($sessionTimeouts[$securityLevel])) {
			$sessionTimeout = $sessionTimeouts[$securityLevel] * Configure::read('Session.timeout');
			ini_set('session.gc_maxlifetime', $sessionTimeout);
		} else {
			trigger_error('Security.level の設定が間違っています。', E_USER_WARNING);
		}
	}
/**
 * パラメーター取得
 */
	$url = getUrlFromEnv();	// 環境変数からパラメータを取得
	$parameter = getUrlParamFromEnv();
	Configure::write('Baser.urlParam',$parameter);	// ※ requestActionに対応する為、routes.php で上書きされる	
/**
 * パラメーター取得
 * モバイル判定・簡易リダイレクト
 */
	$agentSettings = Configure::read('AgentSettings');
	if(!Configure::read('Baser.mobile')) {
		unset($agentSettings['mobile']);
	}
	if(!Configure::read('Baser.smartphone')) {
		unset($agentSettings['smartphone']);
	}
	$agentOn = false;
	if($agentSettings) {
		foreach($agentSettings as $key => $setting) {
			$agentOn = false;
			if(!empty($url)) {
				$parameters = explode('/',$url);
				if($parameters[0] == $setting['alias']) {
					$agentOn = true;
				}
			}
			if(!$agentOn && $setting['autoRedirect']) {
				$agentAgents = $setting['agents'];
				$agentAgents = implode('||', $agentAgents);
				$agentAgents = preg_quote($agentAgents, '/');
				$regex = '/'.str_replace('\|\|', '|', $agentAgents).'/i';
				if(isset($_SERVER['HTTP_USER_AGENT']) && preg_match($regex, $_SERVER['HTTP_USER_AGENT'])) {
					$getParams = str_replace($baseUrl.$parameter, '', $_SERVER['REQUEST_URI']);
					if($getParams == '/' || '/index.php') {
						$getParams = '';
					}
					$redirectUrl = FULL_BASE_URL.$baseUrl.$setting['alias'].'/'.$parameter.$getParams;
					header("HTTP/1.1 301 Moved Permanently");
					header("Location: ".$redirectUrl);
					exit();
				}
			}
			if($agentOn) {
				Configure::write('AgentPrefix.currentAgent', $key);
				Configure::write('AgentPrefix.currentPrefix', $setting['prefix']);
				Configure::write('AgentPrefix.currentAlias', $setting['alias']);
			}
			if($agentOn) {
				break;
			}
		}
	}
	if($agentOn) {
		//======================================================================
		// /m/files/... へのアクセスの場合、/files/... へ自動リダイレクト
		// CMSで作成するページ内のリンクは、モバイルでアクセスすると、
		// 自動的に、/m/ 付のリンクに書き換えられてしまう為、
		// files内のファイルへのリンクがリンク切れになってしまうので暫定対策。
		//======================================================================
		$_parameter = preg_replace('/^'.Configure::read('AgentPrefix.currentAlias').'\//', '', $parameter);
		if(preg_match('/^files/', $_parameter)) {
			$redirectUrl = FULL_BASE_URL.'/'.$_parameter;
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$redirectUrl);
			exit();
		}
	}
	Configure::write('AgentPrefix.on', $agentOn);
/**
 * Viewのキャッシュ設定
 */
	if(Configure::read('debug') > 0) {
		Configure::write('Cache.check', false);
		clearViewCache();
	}else {
		if(Configure::read('Session.start')) {
			// 管理ユーザーでログインしている場合、ページ機能の編集ページへのリンクを表示する為、キャッシュをオフにする。
			// ただし、現在の仕様としては、セッションでチェックしているので、ブラウザを閉じてしまった場合、一度管理画面を表示する必要がある。
			// TODO ブラウザを閉じても最初から編集ページへのリンクを表示する場合は、クッキーのチェックを行い、認証処理を行う必要があるが、
			// セキュリティ上の問題もあるので実装は検討が必要。
			// bootstrapで実装した場合、他ページへの負荷の問題もある
			App::import('Core','Session');
			$Session = new CakeSession();
			$Session->start();
			if(isset($_SESSION['Auth']['User'])) {
				Configure::write('Cache.check', false);
			}
		}
	}
/**
 * データキャッシュ
 */
if(isInstalled()) {
	Cache::config('_cake_data_', array(
			'engine'		=> 'File',
			'duration'		=> Configure::read('Baser.dataCachetime'),
			'probability'	=> 100,
			'path'			=> CACHE.'datas',
			'prefix'		=> 'cake_',
			'lock'			=> false,
			'serialize'		=> true
	 ));
}
/**
 * 利用可能プラグインの設定
 *
 * PluginHookBehavior::setup() で、Baser.enablePlugins を参照できるように、
 * ClassRegistry::removeObject('Plugin'); で一旦 Plugin オブジェクトを削除
 * エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
 */
 	if(isInstalled()) {
		$db =& ConnectionManager::getDataSource('baser');
		$sources = $db->listSources();
		$pluginTable = $db->config['prefix'] . 'plugins';
		$enablePlugins = array();
		if (!is_array($sources) || in_array(strtolower($pluginTable), array_map('strtolower', $sources))) {
			App::import('Core', 'ClassRegistry');
			// TODO パスを追加をApp::build に移行したら明示的に読み込まなくてもよいかも
			App::import('Model', 'AppModel', array('file'=>CAKE_CORE_INCLUDE_PATH.DS.'baser'.DS.'models'.DS.'app_model.php'));
			App::import('Behavior', 'Cache', array('file'=>CAKE_CORE_INCLUDE_PATH.DS.'baser'.DS.'models'.DS.'behaviors'.DS.'cache.php'));
			$Plugin = ClassRegistry::init('Plugin');
			$plugins = $Plugin->find('all', array('fields' => array('Plugin.name'), 'conditions' => array('Plugin.status' => true)));
			ClassRegistry::removeObject('Plugin');
			if($plugins) {
				$enablePlugins = Set::extract('/Plugin/name',$plugins);
				Configure::write('Baser.enablePlugins', $enablePlugins);
			}
		}
/**
 * プラグインの bootstrap を実行する
 */
		$_pluginPaths = array(
			APP.'plugins'.DS,
			BASER_PLUGINS
		);
		foreach($enablePlugins as $enablePlugin) {
			foreach($_pluginPaths as $_pluginPath) {
				$pluginBootstrap = $_pluginPath.$enablePlugin.DS.'config'.DS.'bootstrap.php';
				if(file_exists($pluginBootstrap)) {
					include $pluginBootstrap;
				}
			}
		}
	}
?>