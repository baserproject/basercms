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
 * タイトル
 * インストールの際のエラー時等DB接続まえのエラーで利用
 */
    Configure::write('Baser.title','コーポレートサイトにちょうどいいCMS - BaserCMS - ');
/**
 * Include files
 */
	require ROOT.DS.'baser'.DS.'config'.DS.'paths.php';
    require BASER.'basics.php';
	/* ConnectionManager ハック */
	// baserフォルダ内のデータソースも走査するようにした
	// DataSource も baser フォルダ内を参照
	App::import('Core', 'ConnectionManager', array('file'=>'..'.DS.'baser'.DS.'models'.DS.'connection_manager.php'));	
/**
 * ベース URL を編集
 * エックスサーバーではSCRIPT_NAMEが正常に取得できなかったのでここで書き換える
 * install.phpを優先する事
 */
 	$scriptName = env('SCRIPT_NAME');
	if(!preg_match('/(.*?)\/index.php$/s',$scriptName) && strpos($scriptName,'app/webroot/index.php') === false){
		$pos = strpos($scriptName,'index.php');
		$baseUrl = '';
		if($pos !== false){
			$baseUrl = substr($scriptName,0,$pos);
			$scriptName = $baseUrl.'app/webroot/index.php';
		}else{
			$scriptName = '';
		}
	}
	Configure::write('App.baseUrl', $scriptName);
/**
 * インストール時の設定ファイル読み込み
 */
    if (file_exists(CONFIGS . 'install.php'))
        include_once CONFIGS . 'install.php';
/**
 * タイムゾーン設定
 */
	@putenv("TZ=JST-9");
/**
 * 文字コードの検出順を指定
 */
	mb_detect_order("eucJP-win,UTF-8,SJIS-win");
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
 * デバッグモードの場合は、Viewのキャッシュを無効にする
 */
	if(Configure::read('debug') > 0){
		Configure::write('Cache.check', false);
	}else{
        if(Configure::read('Session.start')){
            // 管理ユーザーでログインしている場合、ページ機能の編集ページへのリンクを表示する為、キャッシュをオフにする。
            // ただし、現在の仕様としては、セッションでチェックしているので、ブラウザを閉じてしまった場合、一度管理画面を表示する必要がある。
            // ブラウザを閉じても最初から編集ページへのリンクを表示する場合は、クッキーのチェックを行い、認証処理を行う必要がある。
            session_start();
            if(isset($_SESSION['Auth']['AdminUser'])){
                Configure::write('Cache.check', false);
            }
        }
    }
/**
 * モバイルルーティングの設定をする
 */
	$mobilePrefix = 'm';
	if(!Configure::read('Mobile.prefix')){
		Configure::write('Mobile.prefix', $mobilePrefix);
	}
/**
 * パラメーター取得
 * モバイル判定
 */
	$parameter = getParamsFromEnv();	// 環境変数からパラメータを取得
	$mobileOn = false;
	$mobilePlugin = false;

    if(!empty($parameter)){

        $parameters = explode('/',$parameter);
        if($parameters[0] == $mobilePrefix){

            $parameter = str_replace($mobilePrefix.'/','',$parameter);
            $mobileOn = true;

			if(!empty($parameters[1])){
				App::import('Core','Folder');
				$pluginFolder = new Folder(APP.'plugins');
				$_plugins = $pluginFolder->read(true,true);
				$plugins = $_plugins[0];
				foreach($plugins as $plugin){
					if($parameters[1] == $plugin){
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
 * 簡易携帯リダイレクト
 */
    if(!$mobileOn){
		$baseUrl = baseUrl();
        $mobileAgents = array('Googlebot-Mobile','Y!J-SRD','Y!J-MBS','DoCoMo','SoftBank','Vodafone','J-PHONE','UP.Browser');
        foreach($mobileAgents as $mobileAgent){
            if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], $mobileAgent) !== false){
                if(empty($_SERVER['HTTPS'])){
                    $protocol = 'http';
                }else{
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