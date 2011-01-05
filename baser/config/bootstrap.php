<?php
/* SVN FILE: $Id$ */
/**
 * 起動スクリプト
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.config
 * @since			Baser v 0.1.0
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
	$url = $_SERVER['REQUEST_URI'];
	if (strpos($url, 'css/') !== false || strpos($url, 'js/') !== false || strpos($url, 'img/') !== false) {
		$assets = array('js' , 'css', 'gif' , 'jpg' , 'png' );
		$ext = array_pop(explode('.', $url));
		if(in_array($ext, $assets)){
			Configure::write('Baser.Asset',true);
			return;
		}
	}
/**
 * tmpフォルダ確認
 */
	if(isInstalled()) {
		checkTmpFolders();
	}
/**
 * デフォルトタイトル設定
 * インストールの際のエラー時等DB接続まえのエラーで利用
 */
	Configure::write('Baser.title','コーポレートサイトにちょうどいいCMS - BaserCMS - ');
/**
 * 標準キャッシュ時間設定
 */
	Configure::write('Baser.cachetime','1 month');
/**
 * プラグインDBプレフィックス
 */
	Configure::write('Baser.pluginDbPrefix', 'pg_');
/**
 * baserUrl取得
 */
	$baseUrl = baseUrl();
/**
 * 文字コードの検出順を指定
 */
	mb_detect_order("ASCII,JIS,UTF-8,SJIS-win,EUC-JP");
/**
 * Eメールヘッダの改行コード設定
 */
	Configure::write('Email.lfcode',"\n");
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
 * モバイルルーティング設定
 */
	$mobilePrefix = 'm';
	if(!Configure::read('Mobile.prefix')) {
		Configure::write('Mobile.prefix', $mobilePrefix);
	}
/**
 * パラメーター取得
 * モバイル判定
 */
	$parameter = getParamsFromEnv();	// 環境変数からパラメータを取得
	$mobileOn = false;
	$mobilePlugin = false;

	if(!empty($parameter)) {

		$parameters = explode('/',$parameter);
		if($parameters[0] == $mobilePrefix) {

			$parameter = str_replace($mobilePrefix.'/','',$parameter);
			$mobileOn = true;

			if(!empty($parameters[1])) {
				App::import('Core','Folder');
				$pluginFolder = new Folder(APP.'plugins');
				$_plugins = $pluginFolder->read(true,true);
				$plugins = $_plugins[0];
				foreach($plugins as $plugin) {
					if($parameters[1] == $plugin) {
						$mobilePlugin = true;
						break;
					}
				}
			}
		}
	}
	Configure::write('Baser.urlParam',$parameter);
	Configure::write('Mobile.on',$mobileOn);
	Configure::write('Mobile.plugin',$mobilePlugin);
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
 * 簡易携帯リダイレクト
 */
	if(!$mobileOn) {
		// TODO 管理画面で設定できるようにする
		$mobileAgents = array('Googlebot-Mobile','Y!J-SRD','Y!J-MBS','DoCoMo','SoftBank','Vodafone','J-PHONE','UP.Browser');
		foreach($mobileAgents as $mobileAgent) {
			if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], $mobileAgent) !== false) {
				if(empty($_SERVER['HTTPS'])) {
					$protocol = 'http';
				}else {
					$protocol = 'https';
				}
				$redirectUrl = $protocol . '://'.$_SERVER['HTTP_HOST'].$baseUrl.$mobilePrefix.'/'.$parameter;
				header("HTTP/1.1 301 Moved Permanently");
				header("Location: ".$redirectUrl);
				exit();
			}
		}
	}
?>