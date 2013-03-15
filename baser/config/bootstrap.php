<?php
/* SVN FILE: $Id$ */
/**
 * 起動スクリプト
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
	// TODO パスを追加をApp::build に移行したら明示的に読み込まなくてもよいかも
	App::import('Core', 'ConnectionManager', array('file'=>CAKE_CORE_INCLUDE_PATH.DS.'baser'.DS.'connection_manager.php'));
	App::import('Model', 'AppModel', array('file'=>CAKE_CORE_INCLUDE_PATH.DS.'baser'.DS.'models'.DS.'app_model.php'));
	App::import('Behavior', 'BcCache', array('file'=>CAKE_CORE_INCLUDE_PATH.DS.'baser'.DS.'models'.DS.'behaviors'.DS.'bc_cache.php'));
	App::import('Core', 'ClassRegistry');
	App::import('Core', 'Multibyte');
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
 * baserUrl取得
 */
	define('BC_BASE_URL', baseUrl());
/**
 * vendors内の静的ファイルの読み込みの場合はスキップ
 */
	$uri = @$_SERVER['REQUEST_URI'];
	if (preg_match('/^'.preg_quote(BC_BASE_URL, '/').'css\//', $uri) || 
			preg_match('/^'.preg_quote(BC_BASE_URL, '/').'js\//', $uri) || 
			preg_match('/^'.preg_quote(BC_BASE_URL, '/').'img\//', $uri)) {
		$assets = array('js' , 'css', 'gif' , 'jpg' , 'png' );
		$ext = array_pop(explode('.', $uri));
		if(in_array($ext, $assets)){
			Configure::write('BcRequest.asset', true);
			return;
		}
	}
/**
 * 配置パターン
 * Windows対策として、「\」を「/」へ変換してチェックする
 */
	if(!preg_match('/'.preg_quote(str_replace('\\', '/', docRoot()), '/').'/', ROOT)) {
		// CakePHP標準の配置
		define('BC_DEPLOY_PATTERN', 3);
	} elseif(ROOT.DS == WWW_ROOT) {
		// webrootをドキュメントルートにして、その中に app / baser / cake を配置
		define('BC_DEPLOY_PATTERN', 2);
	} else {
		// baserCMS配布時の配置
		define('BC_DEPLOY_PATTERN', 1);
	}
/**
 * インストール状態 
 */
	define('BC_INSTALLED', isInstalled());
/**
 * 設定ファイル読み込み
 * install.php で設定している為、一旦読み込んで再設定
 */
	$baserSettings = array();
	$baserSettings['BcEnv'] = Configure::read('BcEnv');
	$baserSettings['BcApp'] = Configure::read('BcApp');
	if(Configure::load('baser')===false) {
		$config = array();
		include BASER_CONFIGS.'baser.php';
		Configure::write($config);
	}
	if(BC_INSTALLED && $baserSettings) {
		foreach ($baserSettings as $key1 => $settings) {
			if($settings) {
				foreach($settings as $key2 => $setting) {
					Configure::write($key1.'.'.$key2, $setting);
				}
			}
		}
	}
/**
 * クレジット読込 
 */
	$config = array();
	include BASER_CONFIGS.'credit.php';
	Configure::write($config);
/**
 * tmpフォルダ確認
 */
	if(BC_INSTALLED) {
		checkTmpFolders();
	}
/**
 * 文字コードの検出順を指定
 */
	mb_detect_order(Configure::read('BcEncode.detectOrder'));
/**
 * メモリー設定
 */
	$memoryLimit = (int) ini_get('memory_limit');
	if($memoryLimit < 32 && $memoryLimit != -1) {
		ini_set('memory_limit', '32M');
	}
/**
 * パラメーター取得
 */
	$url = getUrlFromEnv();	// 環境変数からパラメータを取得
	$parameter = getUrlParamFromEnv();
	Configure::write('BcRequest.pureUrl',$parameter);	// ※ requestActionに対応する為、routes.php で上書きされる	
/**
 * セッションスタート 
 */
	App::import('Core','Session');
	$Session = new CakeSession();
	$Session->start();
/**
 * パラメーター取得
 * モバイル判定・簡易リダイレクト
 */
	$agentSettings = Configure::read('BcAgent');
	if(!Configure::read('BcApp.mobile')) {
		unset($agentSettings['mobile']);
	}
	if(!Configure::read('BcApp.smartphone')) {
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
					$getParams = str_replace(BC_BASE_URL.$parameter, '', $_SERVER['REQUEST_URI']);
					if($getParams == '/' || $getParams == '/index.php') {
						$getParams = '';
					}
					
					$redirect = true;
					
					// URLによる AUTO REDIRECT 設定
					if(isset($_GET[$setting['prefix'].'_auto_redirect'])) {
						if($_GET[$setting['prefix'].'_auto_redirect'] == 'on') {
							$_SESSION[$setting['prefix'].'_auto_redirect'] = 'on';
						} elseif($_GET[$setting['prefix'].'_auto_redirect'] == 'off') {
							$_SESSION[$setting['prefix'].'_auto_redirect'] = 'off';
						}
					}
					
					if(isset($_SESSION[$setting['prefix'].'_auto_redirect'])) {
						if($_SESSION[$setting['prefix'].'_auto_redirect'] == 'off') {
							$redirect = false;
						}
					}	
					
					if(isset($_GET[$setting['prefix']])) {
						if($_GET[$setting['prefix']] == 'on') {
							$redirect = true;
						} elseif($_GET[$setting['prefix']] == 'off') {
							$redirect = false;
						}
					}

					if($redirect) {
						$redirectUrl = FULL_BASE_URL . BC_BASE_URL . $setting['alias'] . '/' . $parameter . $getParams;
						header("HTTP/1.1 301 Moved Permanently");
						header("Location: ".$redirectUrl);
						exit();
					}
				}
			}
			if($agentOn) {
				Configure::write('BcRequest.agent', $key);
				Configure::write('BcRequest.agentPrefix', $setting['prefix']);
				Configure::write('BcRequest.agentAlias', $setting['alias']);
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
		$_parameter = preg_replace('/^'.Configure::read('BcRequest.agentAlias').'\//', '', $parameter);
		if(preg_match('/^files/', $_parameter)) {
			$redirectUrl = FULL_BASE_URL.'/'.$_parameter;
			header("HTTP/1.1 301 Moved Permanently");
			header("Location: ".$redirectUrl);
			exit();
		}
	}
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
			if(isset($_SESSION['Auth']['User'])) {
				Configure::write('Cache.check', false);
			}
		}
	}
if(BC_INSTALLED) {
/**
 * データキャッシュ
 */
	Cache::config('_cake_data_', array(
			'engine'		=> 'File',
			'duration'		=> Configure::read('BcCache.dataCachetime'),
			'probability'	=> 100,
			'path'			=> CACHE.'datas',
			'prefix'		=> 'cake_',
			'lock'			=> true,
			'serialize'		=> true
	 ));
/**
 * 環境情報キャッシュ
 */
	Cache::config('_cake_env_', array(
			'engine'		=> 'File',
			'duration'		=> Configure::read('BcCache.defaultCachetime'),
			'probability'	=> 100,
			'path'			=> CACHE.'environment',
			'prefix'		=> 'cake_',
			'lock'			=> false,
			'serialize'		=> true
	 ));
/**
 * 管理者グループ
 * 互換性用
 * @deprecated v2.2.0 以降で削除 
 */
	if(!Configure::read('BcApp.adminGroupId')) {
		Configure::write('BcApp.adminGroupId', 1);
	}

}
