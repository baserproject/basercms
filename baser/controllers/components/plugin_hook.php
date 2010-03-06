<?php
/* SVN FILE: $Id$ */
/**
 * プラグインフックコンポーネント
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
 * @package			baser.controllers.components
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class PluginHookComponent extends Object{
/**
 * プラグインフックオブジェクト
 * @var array
 */
	var $pluginHooks = array();
/**
 * initialize
 * @param Controller $controller
 */
	function initialize(&$controller){

		if(!file_exists(CONFIGS.'database.php')){
			return;
		}else{
	        require_once(CONFIGS.'database.php');
    	    $dbConfig = new DATABASE_CONFIG();
	        if(!$dbConfig->baser['driver']) return;
		}
		
		if(!empty($controller->enablePlugins)){
			$plugins = $controller->enablePlugins;
		}else{
            $plugins = array();
            /* プラグインディレクトリをチェックしてプラグイン名のリストを取得 */
            /*$folder = new Folder(APP.'plugins');
            $files = $folder->read(true,true);
            $plugins = $files[0];*/

            // エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
            $db =& ConnectionManager::getDataSource('baser');
            if ($db->isInterfaceSupported('listSources')) {
                $sources = $db->listSources();
                if (!is_array($sources) || in_array(strtolower($db->config['prefix'] . 'plugins'), array_map('strtolower', $sources))) {
                    /* DBに登録されているものだけに変更した */
                    $Plugin =& ClassRegistry::init('Plugin','Model');
                    $plugins = $Plugin->find('all');
                    $controller->enablePlugins = $plugins = Set::extract('/Plugin/name',$plugins);
                }
            }

		}
        
        /* プラグインフックコンポーネントが実際に存在するかチェックしてふるいにかける */
        $pluginHooks = array();
        foreach($plugins as $plugin){
            $pluginName = Inflector::camelize($plugin);
            if(App::import('Component',$pluginName.'.'.$pluginName.'Hook')){
                $pluginHooks[] = $pluginName.'HookComponent';
            }
        }
        
        /* プラグインフックを初期化 */
        foreach($pluginHooks as $pluginHook){
            $this->pluginHooks[] =& new $pluginHook();
        }

        /* initialize のフックを実行 */
        foreach($this->pluginHooks as $key => $pluginHook){
            if(method_exists($this->pluginHooks[$key],"initialize")){
                $this->pluginHooks[$key]->initialize($controller);
            }
        }
        
	}
/**
 * startup
 * @param Controller $controller 
 */
    function startup(&$controller){
        foreach($this->pluginHooks as $key => $pluginHook){
            if(method_exists($this->pluginHooks[$key],"startup")){
                $this->pluginHooks[$key]->startup($controller);
            }
        }
    }
/**
 * beforeFilter
 * @param Controller $controller
 */
    function beforeFilter(&$controller){
        foreach($this->pluginHooks as $key => $pluginHook){
            if(method_exists($this->pluginHooks[$key],"beforeFilter")){
                $this->pluginHooks[$key]->beforeFilter($controller);
            }
        }
    }
/**
 * beforeRedirect
 * @param Controller $controller
 */
    function beforeRedirect(&$controller, $url, $status = null, $exit = true){
        foreach($this->pluginHooks as $key => $pluginHook){
            if(method_exists($this->pluginHooks[$key],"beforeRedirect")){
                $this->pluginHooks[$key]->beforeRedirect($controller, $url, $status, $exit);
            }
        }
    }
/**
 * shutdown
 * @param Controller $controller
 */
    function shutdown(&$controller){
        foreach($this->pluginHooks as $key => $pluginHook){
            if(method_exists($this->pluginHooks[$key],"shutdown")){
                $this->pluginHooks[$key]->shutdown($controller);
            }
        }
    }
}
?>