<?php
/* SVN FILE: $Id$ */
/**
 * ルーティング定義
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
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
if (file_exists(CONFIGS.'database.php'))
{

    $mobilePrefix = Configure::read('Mobile.prefix');
    $appBaseUrl = Configure::read('App.baseUrl');
    $mobileOn = false;
    $documentRoot = str_replace($_SERVER['SCRIPT_NAME'],'',$_SERVER['SCRIPT_FILENAME']);
/**
 * URL/パラメーターを取得する
 */
    $baseUrl = '';
    $parameter = '';
    $baseUrl = baseUrl();
    if($appBaseUrl){
        $base = dirname($appBaseUrl);
        if(strpos($_SERVER['REQUEST_URI'], $appBaseUrl) !== false){
            $parameter = str_replace($appBaseUrl,'',$_SERVER['REQUEST_URI']);
        }else{
            // トップページ対策
            $parameter = str_replace($base.'/','',$_SERVER['REQUEST_URI']);
        }
        $parameter = preg_replace('/^\//','',$parameter);
    }else{
        $query = $_SERVER['QUERY_STRING'];
        if(!empty($query) && strpos($query, '=')){
            $aryPath = explode('=',$query);
            $parameter = preg_replace('/^\//','',$aryPath[1]);
        }
    }

    // モバイル判定
    if(!empty($parameter)){
        $parameters = explode('/',$parameter);
        if($parameters[0] == $mobilePrefix){
            $parameter = str_replace($mobilePrefix.'/','',$parameter);
            $mobileOn = true;
        }
    }else{
        $parameters[0] = null;
    }

    // モバイル有効設定
    // TODO bootstrapに移動する
    Configure::write('Mobile.on',$mobileOn);

/**
 * 簡易携帯リダイレクト
 */
    if(!$mobileOn){
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
/**
 * トップページ
 */
    if(!$mobileOn){
	 	Router::connect('/', array('controller' => 'pages', 'action' => 'display', 'pages/'.'index.html'));
	}else{
        Router::connect('/'.$mobilePrefix.'/', array('prefix' => 'mobile','controller' => 'pages', 'action'=>'display', 'pages/'.'index.html'));
	}
/**
 * ページ機能拡張
 * .html付きのアクセスの場合、pagesコントローラーを呼び出す
 */
    if(strpos($parameter, '.html') !== false){
        if($mobileOn){
            Router::connect('/'.$mobilePrefix.'/.*?\.html', array('prefix' => 'mobile','controller' => 'pages', 'action' => 'display','pages/'.$parameter));
        }else{
            Router::connect('.*?\.html', array('controller' => 'pages', 'action' => 'display','pages/'.$parameter));
        }
    }
/**
 * プラグイン名の書き換え
 *
 * DBに登録したデータを元にURLのプラグイン名部分を書き換える。
 * 一つのプラグインで二つのコンテンツを設置した場合に利用する。
 * あらかじめ、plugin_contentsテーブルに、URLに使う名前とコンテンツを特定する。
 * プラグインごとの一意のキー[content_id]を保存しておく。
 *
 * content_idをコントローラーで取得するには、$plugins_controllerのcontentIdプロパティを利用する。
 * Router::connectの引数として値を与えると、$html->linkなどで、
 * Routerを利用する際にマッチしなくなりURLがデフォルトのプラグイン名となるので注意
 */
    if(isset($_SERVER['REQUEST_URI'])){

        require_once(CONFIGS.'database.php');
        $dbConfig = new DATABASE_CONFIG();

        if($dbConfig->baser['driver']){

           /* App::import('Core', 'Router');
            App::import('Core', 'Model');
            App::import('Model', 'AppModel',array('file'=>BASER_MODELS.'app_model.php'));*/
            App::import('Model','PluginContent',array('file'=>BASER_MODELS.'plugin_content.php'));

            // 下記ファイルを読み込んでおかないとデータベースに接続できなかった場合のエラーが正常に表示されない
            // SessionComponentは何故かApp::importでは読み込めない
            /*require_once(LIBS.'controller'.DS.'components'.DS.'session.php');
            require_once(LIBS.'view'.DS.'theme.php');*/
            //App::import('Controller', 'AppController',array('file'=>BASER_CONTROLLERS.'app_controller.php'));
            //App::import('View', 'AppView',array('file'=>BASER_VIEWS.'app_view.php'));

            $db =& ConnectionManager::getDataSource('baser');
            if ($db->isInterfaceSupported('listSources')) {
                $sources = $db->listSources();
                if (is_array($sources) && !in_array(strtolower($dbConfig->baser['prefix'] . 'plugin_contents'), array_map('strtolower', $sources))) {
                    $pluginRouting = false;
                }else{
                    $pluginRouting = true;
                }
            }

            if($pluginRouting){

                $pc = new PluginContent();
                $pluginContents = $pc->findAll();

                if(!empty($_GET['url'])){
                    $docRoot = false;
                    $rewritable = true;
                }else{
                    if(strpos($_SERVER['REQUEST_URI'],'index.php') !== false){
                        $rewritable = false;
                        $_url = str_replace('/index.php','',$_SERVER['REQUEST_URI']);
                        if(strpos($_url,'/') === 0) $_url = substr($_url,1);
                        if($_url){
                            $docRoot = false;
                        }else{
                            $docRoot = true;
                        }
                    }else{
                        $rewritable = true;
                        $docRoot = true;
                    }
                }

                if($pluginContents && !$docRoot){
                    if($rewritable){
                        $_url = $_GET['url'];
                    }
                    if(strpos($_url, '/') !== false){
                        $_path = split('/',$_url);
                    }else{
                        $_path[0] = $_url;
                        $_path[1] = '';
                    }
                    foreach($pluginContents as $pluginContent ){
                        if($pluginContent['PluginContent']['name']){
                            if($_path[0]!=$mobilePrefix && $_path[0] == $pluginContent['PluginContent']['name']){
                                Router::connect('/'.$pluginContent['PluginContent']['name'].'/:action/*',array('plugin'=>$pluginContent['PluginContent']['plugin'],'controller'=>$pluginContent['PluginContent']['plugin']));
                            }elseif($mobileOn && $_path[1]==$pluginContent['PluginContent']['name']){
                                Router::connect('/'.$mobilePrefix.'/'.$pluginContent['PluginContent']['name'].'/:action/*',array('prefix' => 'mobile','plugin'=>$pluginContent['PluginContent']['plugin'],'controller'=>$pluginContent['PluginContent']['plugin']));
                            }
                        }
                    }
                }
            }
        }
    }
/**
 * 携帯のプラグイン判定
 * 携帯からのプラグインへのアクセスで、プラグインが存在すれば、$mobilePlugin = true とする
 */
    App::import('Core','Folder');
    $pluginFolder = new Folder(APP.'plugins');
    $_plugins = $pluginFolder->read(true,true);
    $plugins = $_plugins[0];

    $mobilePlugin = false;
    if(isset($parameters[0]) && isset($parameters[1]) && $parameters[0]==$mobilePrefix){
        foreach($plugins as $plugin){
            if($parameters[1] == $plugin){
                $mobilePlugin = true;
            }
        }
    }
/**
 * メンバー用
 */
 	Router::connect('/member/:controller/:action/*', array('prefix' => 'member','member'=>true));
/**
 * 携帯ルーティング
 */
    if($mobileOn){
        // プラグイン
        if($mobilePlugin){
            // ノーマル
            Router::connect('/'.$mobilePrefix.'/:plugin/:controller/:action/*', array('prefix' => 'mobile'));
            // プラグイン名省略
            Router::connect('/'.$mobilePrefix.'/:plugin/:action/*', array('prefix' => 'mobile'));
        }
        // 携帯ノーマル
        Router::connect('/'.$mobilePrefix.'/:controller/:action/*', array('prefix' => 'mobile'));
    }
/**
 * テストルーティング
 */
	Router::connect('/tests', array('controller' => 'tests', 'action' => 'index'));
/**
 * フィード出力
 * 拡張子rssの場合は、rssディレクトリ内のビューを利用する
 */
 	Router::parseExtensions('rss');

}
else{
    Router::connect('/', array('controller' => 'installations', 'action' => 'index'));
}

Router::connect('/install', array('controller' => 'installations', 'action' => 'index'));
?>