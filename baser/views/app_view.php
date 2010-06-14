<?php
/* SVN FILE: $Id$ */
/**
 * view 拡張クラス
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
 * @package			baser
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('View','Theme');
/**
 * view 拡張クラス
 *
 * @package			baser.views
 */
class AppView extends ThemeView {
/**
 * List of variables to collect from the associated controller
 *
 * @var array
 * @access protected
 */
	var $__passedVars = array(
		'viewVars', 'action', 'autoLayout', 'autoRender', 'ext', 'base', 'webroot',
		'helpers', 'here', 'layout', 'name', 'pageTitle', 'layoutPath', 'viewPath',
		'params', 'data', 'plugin', 'passedArgs', 'cacheAction', 'subDir','enablePlugins'
	);
/**
 * テンプレートのファイル名を取得する
 * プレフィックスが設定されている場合は、プレフィックスを除外する
 * @param	string	$name
 * @return	string	$fileName
 * @access	protected
 */
	function _getViewFileName($name = null){
		
		if(!$name && isset($this->params['prefix'])){
			$prefix = $this->params['prefix'];
			$name = str_replace($prefix.'_','',$this->action);
		}
		if($this->name == 'CakeError' && $this->viewPath == 'errors'){
			// CakeErrorの場合はサブフォルダを除外
			$subDir = $this->subDir;
			$this->subDir = '';
			$fileName = parent::_getViewFileName($name);
			$this->subDir = $subDir;
			return $fileName;
		}else{
			return parent::_getViewFileName($name);
		}
		
	}

}
?>