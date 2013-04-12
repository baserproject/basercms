<?php
/* SVN FILE: $Id$ */
/**
 * view 拡張クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Core','Theme');
/**
 * view 拡張クラス
 *
 * @package			baser.views
 */
class BaserAppView extends ThemeView {
/**
 * List of variables to collect from the associated controller
 *
 * @var array
 * @access protected
 */
	var $__passedVars = array(
			'viewVars', 'action', 'autoLayout', 'autoRender', 'ext', 'base', 'webroot',
			'helpers', 'here', 'layout', 'name', 'pageTitle', 'layoutPath', 'viewPath',
			'params', 'data', 'plugin', 'passedArgs', 'cacheAction', 'subDir', 'adminTheme'
	);
/**
 * Return all possible paths to find view files in order
 *
 * @param string $plugin
 * @return array paths
 * @access private
 */
	function _paths($plugin = null, $cached = true) {
		
		$paths = $this->__paths($plugin, $cached);

		// >>> CUSTOMIZE MODIFY 2011/03/24 ryuring
		// プラグインパスにテーマのパスを追加した為、
		// テーマのパスをさらにテーマのパスに整形しないように調整
		// 
		//  >>> CUSTOMIZE MODIFY 2011/03/24 ryuring
		// 管理画面のテーマをフロントのテーマで上書きできるように調整
		/*if (!empty($this->theme)) {
			$count = count($paths);
			for ($i = 0; $i < $count; $i++) {
			
				$themePaths[] = $paths[$i] . 'themed'. DS . $this->theme . DS;
			}
			$paths = array_merge($themePaths, $adminThemePaths, $paths);
		}*/
		// ---
		$basePaths = $paths;
		$count = count($basePaths);
		if (!empty($this->adminTheme)) {
			$adminThemePaths = array();
			for ($i = 0; $i < $count; $i++) {
				if(strpos($basePaths[$i], 'themed') === false) {
					$adminThemePaths[] = $basePaths[$i] . 'themed'. DS . $this->adminTheme . DS;
				}
			}
			$paths = array_merge($adminThemePaths, $paths);
		}
		if (!empty($this->theme)) {
			for ($i = 0; $i < $count; $i++) {
				if(strpos($basePaths[$i], 'themed') === false) {
					$themePaths[] = $basePaths[$i] . 'themed'. DS . $this->theme . DS;
				}
			}
			$paths = array_merge($themePaths, $paths);
		}
		// <<<

		if (empty($this->__paths)) {
			$this->__paths = $paths;
		}

		return $paths;
	}
/**
 * Return all possible paths to find view files in order
 * 
 * ※ _paths より直接呼び出されるようにする為だけに、Viewクラスより中身をコピー
 * 
 * @param string $plugin
 * @return array paths
 * @access protected
 */
	function __paths($plugin = null, $cached = true) {
		if ($plugin === null && $cached === true && !empty($this->__paths)) {
			return $this->__paths;
		}
		$paths = array();
		$viewPaths = Configure::read('viewPaths');
		$corePaths = array_flip(Configure::corePaths('view'));

		if (!empty($plugin)) {
			$count = count($viewPaths);
			for ($i = 0; $i < $count; $i++) {
				if (!isset($corePaths[$viewPaths[$i]])) {
					$paths[] = $viewPaths[$i] . 'plugins' . DS . $plugin . DS;
				}
			}
			$pluginPaths = Configure::read('pluginPaths');
			$count = count($pluginPaths);

			for ($i = 0; $i < $count; $i++) {
				$paths[] = $pluginPaths[$i] . $plugin . DS . 'views' . DS;
			}
		}
		$paths = array_merge($paths, $viewPaths);

		if (empty($this->__paths)) {
			$this->__paths = $paths;
		}
		return $paths;
	}
/**
 * フック処理を実行する
 *
 * @param	string	$out
 * @return	mixed
 */
	function executeHook($hook, $out) {

		return $this->loaded['pluginHook']->{$hook}($out);

	}
/**
 * Renders a piece of PHP with provided parameters and returns HTML, XML, or any other string.
 *
 * This realizes the concept of Elements, (or "partial layouts")
 * and the $params array is used to send data to be used in the
 * Element.  Elements can be cached through use of the cache key.
 *
 * @param string $name Name of template file in the/app/views/elements/ folder
 * @param array $params Array of data to be made available to the for rendered
 *                      view (i.e. the Element)
 *    Special params:
 *		cache - enable caching for this element accepts boolean or strtotime compatible string.
 *      Can also be an array
 *				if an array,'time' is used to specify duration of cache.  'key' can be used to
 *              create unique cache files.
 *
 * @return string Rendered Element
 * @access public
 */
	function element($name, $params = array(), $loadHelpers = false) {
		$file = $plugin = $key = null;

		if (isset($params['plugin'])) {
			$plugin = $params['plugin'];
		}

		if (isset($this->plugin) && !$plugin) {
			$plugin = $this->plugin;
		}

		if (isset($params['cache'])) {
			$expires = '+1 day';

			if (is_array($params['cache'])) {
				$expires = $params['cache']['time'];
				$key = Inflector::slug($params['cache']['key']);
			} elseif ($params['cache'] !== true) {
				$expires = $params['cache'];
				$key = implode('_', array_keys($params));
			}

			if ($expires) {
				$cacheFile = 'element_' . $key . '_' . $plugin . Inflector::slug($name);
				$cache = cache('views' . DS . $cacheFile, null, $expires);

				if (is_string($cache)) {
					return $cache;
				}
			}
		}
		$paths = $this->_paths($plugin);

		// CUSTOMIZE MODIFY 2012/04/11 ryuring
		// 後方互換保持の為、ファイル走査の優先順位に.ctpを追加
		// @deprecated .php への移行を推奨
		// >>>
		/*foreach ($paths as $path) {
			if (file_exists($path . 'elements' . DS . $name . $this->ext)) {
				$file = $path . 'elements' . DS . $name . $this->ext;
				break;
			} elseif (file_exists($path . 'elements' . DS . $name . '.thtml')) {
				$file = $path . 'elements' . DS . $name . '.thtml';
				break;
			}
		}*/
		// ---
		foreach ($paths as $path) {
			if (file_exists($path . 'elements' . DS . $name . $this->ext)) {
				$file = $path . 'elements' . DS . $name . $this->ext;
				break;
			}elseif (file_exists($path . 'elements' . DS . $name . '.ctp')) {
				trigger_error('エレメントテンプレートの拡張子 .ctp は非推奨です。.php を利用してください。<br />'.$path . 'elements' . DS . $name . '.ctp', E_USER_WARNING);
				$file = $path . 'elements' . DS . $name . '.ctp';
				break;
			} elseif (file_exists($path . 'elements' . DS . $name . '.thtml')) {
				$file = $path . 'elements' . DS . $name . '.thtml';
				break;
			}
		}
		// <<<
		
		if (is_file($file)) {
			$params = array_merge_recursive($params, $this->loaded);
			$element = $this->_render($file, array_merge($this->viewVars, $params), $loadHelpers);
			if (isset($params['cache']) && isset($cacheFile) && isset($expires)) {
				cache('views' . DS . $cacheFile, $element, $expires);
			}
			return $element;
		}
		$file = $paths[0] . 'elements' . DS . $name . $this->ext;

		if (Configure::read() > 0) {
			return "Not Found: " . $file;
		}
	}
/**
 * Returns filename of given action's template file (.ctp) as a string.
 * CamelCased action names will be under_scored! This means that you can have
 * LongActionNames that refer to long_action_names.ctp views.
 *
 * @param string $action Controller action to find template filename for
 * @return string Template filename
 * @access protected
 */
	function _getViewFileName($name = null) {
		
		// CUSTOMIZE ADD 2012/04/11 ryuring
		// プレフィックスが設定されている場合は、プレフィックスを除外する
		// >>>
		if(!$name) {
			$prefix = '';
			if(isset($this->params['prefix'])) {
				$prefix = $this->params['prefix'];
			}
			if($prefix && preg_match('/^'.$prefix.'_/', $this->action)) {
				$name = str_replace($prefix.'_','',$this->action);
			} elseif(preg_match('/^admin_/', $this->action)) {
				// プレフィックスをadminとしてすり替え
				$name = str_replace('admin_','',$this->action);
			}
		}
		if($this->name == 'CakeError' && $this->viewPath == 'errors') {
			// CakeErrorの場合はサブフォルダを除外
			$subDir = $this->subDir;
			$this->subDir = '';
			$fileName = parent::_getViewFileName($name);
			$this->subDir = $subDir;
			return $fileName;
		}
		// <<<
		
		$subDir = null;

		if (!is_null($this->subDir)) {
			$subDir = $this->subDir . DS;
		}

		// CUSTOMIZE MODIFY 2012/10/11 ryuring
		// モバイルの固定ページの場合、PCの固定ページと連動する場合は、
		// サブフォルダを空に設定
		$siteConfig = $this->getVar('siteConfig');
		if($this->name == 'Pages' && preg_match('/(.+)_display$/', $this->action, $maches)) {
			$Page = ClassRegistry::getObject('Page');
			$url = '/'.implode('/', $this->params['pass']);
			if($Page->isLinked($maches[1], $url)) {
				$subDir = '';
			}
		}
		
		if ($name === null) {
			$name = $this->action;
		}
		$name = str_replace('/', DS, $name);

		if (strpos($name, DS) === false && $name[0] !== '.') {
			$name = $this->viewPath . DS . $subDir . Inflector::underscore($name);
		} elseif (strpos($name, DS) !== false) {
			if ($name{0} === DS || $name{1} === ':') {
				if (is_file($name)) {
					return $name;
				}
				$name = trim($name, DS);
			} else if ($name[0] === '.') {
				$name = substr($name, 3);
			} else {
				$name = $this->viewPath . DS . $subDir . $name;
			}
		}

		$paths = $this->_paths(Inflector::underscore($this->plugin));
		
		$exts = array($this->ext, '.ctp', '.thtml');
		
		// CUSTOMIZE MODIFY 2012/04/11 ryuring
		// 拡張子優先順位よりもパスの優先順位を優先する仕様に変更
		// @deprecated .php への移行を推奨
		// >>>
		/*foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $name . $ext)) {
					return $path . $name . $ext;
				}
			}
		}*/
		// ---
		foreach ($paths as $path) {
			foreach ($exts as $ext) {
				if (file_exists($path . $name . $ext)) {
					if($ext == '.ctp') {
						trigger_error('ビューテンプレートの拡張子 .ctp は非推奨です。.php を利用してください。<br />'.$path . $name . $ext, E_USER_WARNING);
					}
					return $path . $name . $ext;
				}
			}
		}
		// <<<
		
		$defaultPath = $paths[0];

		if ($this->plugin) {
			$pluginPaths = Configure::read('pluginPaths');
			foreach ($paths as $path) {
				if (strpos($path, $pluginPaths[0]) === 0) {
					$defaultPath = $path;
					break;
				}
			}
		}
		return $this->_missingView($defaultPath . $name . $this->ext, 'missingView');
	}

/**
 * Returns layout filename for this template as a string.
 *
 * @return string Filename for layout file (.ctp).
 * @access protected
 */
	function _getLayoutFileName($name = null) {
		if ($name === null) {
			$name = $this->layout;
		}
		$subDir = null;

		if (!is_null($this->layoutPath)) {
			$subDir = $this->layoutPath . DS;
		}
		$paths = $this->_paths(Inflector::underscore($this->plugin));
		$file = 'layouts' . DS . $subDir . $name;

		$exts = array($this->ext, '.ctp', '.thtml');

		// CUSTOMIZE MODIFY 2012/04/11 ryuring
		// 拡張子優先順位よりもパスの優先順位を優先する仕様に変更
		// @deprecated .php への移行を推奨
		// >>>
		/*foreach ($exts as $ext) {
			foreach ($paths as $path) {
				if (file_exists($path . $file . $ext)) {
					return $path . $file . $ext;
				}
			}
		}*/
		// ---
		foreach ($paths as $path) {
			foreach ($exts as $ext) {
				if (file_exists($path . $file . $ext)) {
					if($ext == '.ctp') {
						trigger_error('レイアウトテンプレートの拡張子 .ctp は非推奨です。.php を利用してください。<br />'.$path . $file . $ext, E_USER_WARNING);
					}
					return $path . $file . $ext;
				}
			}
		}
		// <<<
		
		return $this->_missingView($paths[0] . $file . $this->ext, 'missingLayout');
	}
/**
 * Renders a layout. Returns output from _render(). Returns false on error.
 * Several variables are created for use in layout.
 *	title_for_layout - contains page title
 *	content_for_layout - contains rendered view file
 *	scripts_for_layout - contains scripts added to header
 *  cakeDebug - if debug is on, cake debug information is added.
 *
 * @param string $content_for_layout Content to render in a view, wrapped by the surrounding layout.
 * @return mixed Rendered output, or false on error
 */
	function renderLayout($content_for_layout, $layout = null) {
		$layoutFileName = $this->_getLayoutFileName($layout);
		if (empty($layoutFileName)) {
			return $this->output;
		}

		$debug = '';

		if (isset($this->viewVars['cakeDebug']) && Configure::read() > 2) {
			$params = array('controller' => $this->viewVars['cakeDebug']);
			$debug = View::element('dump', $params, false);
			unset($this->viewVars['cakeDebug']);
		}

		if ($this->pageTitle !== false) {
			$pageTitle = $this->pageTitle;
		} else {
			$pageTitle = Inflector::humanize($this->viewPath);
		}
		$data_for_layout = array_merge($this->viewVars, array(
			'title_for_layout' => $pageTitle,
			'content_for_layout' => $content_for_layout,
			'scripts_for_layout' => implode("\n\t", $this->__scripts),
			'cakeDebug' => $debug
		));

		if (empty($this->loaded) && !empty($this->helpers)) {
			$loadHelpers = true;
		} else {
			$loadHelpers = false;
			$data_for_layout = array_merge($data_for_layout, $this->loaded);
		}

		$this->_triggerHelpers('beforeLayout');

		// CUSTOMIZE MODIFY 2012/09/11 ryuring
		// >>>
		//if (substr($layoutFileName, -3) === 'ctp' || substr($layoutFileName, -5) === 'thtml') {
		// ---
		if (substr($layoutFileName, -3) === 'ctp' || substr($layoutFileName, -5) === 'thtml' || substr($layoutFileName, -3) === 'php') {
		// <<<
		
			$this->output = View::_render($layoutFileName, $data_for_layout, $loadHelpers, true);
		} else {
			$this->output = $this->_render($layoutFileName, $data_for_layout, $loadHelpers);
		}

		if ($this->output === false) {
			$this->output = $this->_render($layoutFileName, $data_for_layout);
			$msg = __("Error in layout %s, got: <blockquote>%s</blockquote>", true);
			trigger_error(sprintf($msg, $layoutFileName, $this->output), E_USER_ERROR);
			return false;
		}

		$this->_triggerHelpers('afterLayout');

		return $this->output;
	}
	
}
?>