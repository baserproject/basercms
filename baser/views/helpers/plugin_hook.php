<?php
/* SVN FILE: $Id$ */
/**
 * プラグインフックヘルパー
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
 * @package			baser.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class PluginHookHelper extends AppHelper{
/**
 * プラグインフックオブジェクト
 * @var array
 */
	var $pluginHooks = array();
/**
 * beforeRender
 */
	function beforeRender(){
        
		if(!file_exists(CONFIGS.'database.php')){
			return;
		}else{
	        require_once(CONFIGS.'database.php');
    	    $dbConfig = new DATABASE_CONFIG();
	        if(!$dbConfig->baser['driver']) return;
		}
		
		$view = ClassRegistry::getObject('view');
		if(!empty($view->enablePlugins)){
			$plugins = $view->enablePlugins;
		}else{
            $plugins = array();
            // エラーの際も呼び出される事があるので、テーブルが実際に存在するかチェックする
            $db =& ConnectionManager::getDataSource('baser');
            if ($db->isInterfaceSupported('listSources')) {
                $sources = $db->listSources();
                if (!is_array($sources) || in_array(strtolower($db->config['prefix'] . 'plugins'), array_map('strtolower', $sources))) {
                    $Plugin =& ClassRegistry::init('Plugin','Model');
                    $plugins = $Plugin->find('all');
                    $plugins = Set::extract('/Plugin/name',$plugins);
                }
            }

		}

        /* プラグインフックコンポーネントが実際に存在するかチェックしてふるいにかける */
        $pluginHooks = array();
        foreach($plugins as $plugin){
            $pluginName = Inflector::camelize($plugin);
            if(App::import('Helper',$pluginName.'.'.$pluginName.'Hook')){
                $pluginHooks[] = $pluginName.'HookHelper';
            }
        }

		/* プラグインフックを初期化 */
        $vars = array(
            'base', 'webroot', 'here', 'params', 'action', 'data', 'themeWeb', 'plugin'
        );
        $c = count($vars);
        foreach($pluginHooks as $key => $pluginHook){
            $this->pluginHooks[$key] =& new $pluginHook();
            for ($j = 0; $j < $c; $j++) {
				if(isset($view->{$vars[$j]})){
					$this->pluginHooks[$key]->{$vars[$j]} = $view->{$vars[$j]};
				}
            }
        }

        /* beforeRenderをフック */
        if($this->pluginHooks){
            foreach($this->pluginHooks as $key => $pluginHook){
                if(method_exists($this->pluginHooks[$key],"beforeRender")){
                    $this->pluginHooks[$key]->beforeRender();
                }
            }
        }
        
	}
/**
 * afterRender
 */
	function afterRender(){
		foreach($this->pluginHooks as $key => $pluginHook){
            if(method_exists($this->pluginHooks[$key],"afterRender")){
                $this->pluginHooks[$key]->afterRender();
            }
		}
	}
/**
 * beforeLayout
 */
	function beforeLayout(){
		foreach($this->pluginHooks as $key => $pluginHook){
            if(method_exists($this->pluginHooks[$key],"beforeLayout")){
                $this->pluginHooks[$key]->beforeLayout();
            }
		}
	}
/**
 * afterLayout
 */
	function afterLayout(){
		foreach($this->pluginHooks as $key => $pluginHook){
            if(method_exists($this->pluginHooks[$key],"afterLayout")){
                $this->pluginHooks[$key]->afterLayout();
            }
		}
	}
}
?>