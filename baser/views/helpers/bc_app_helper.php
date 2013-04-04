<?php
/* SVN FILE: $Id: baser_app_helper.php 143 2011-08-26 06:11:39Z ryuring $ */
/**
 * Helper 拡張クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.view.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision: 143 $
 * @modifiedby		$LastChangedBy: ryuring $
 * @lastmodified	$Date: 2011-08-26 15:11:39 +0900 (金, 26 8 2011) $
 * @license			http://basercms.net/license/index.html
 */
/**
 * Helper 拡張クラス
 *
 * @package			baser.view.helpers
 */
class BcAppHelper extends Helper {
/**
 * view
 * キャッシュ用
 * @var View
 */
	var $_view = null;
/**
 * html tags used by this helper.
 *
 * @var 	array
 * @access	public
 */
	var $tags = array(
			'meta' => '<meta%s/>',
			'metalink' => '<link href="%s"%s/>',
			'link' => '<a href="%s"%s>%s</a>',
			'mailto' => '<a href="mailto:%s" %s>%s</a>',
			'form' => '<form %s>',
			'formend' => '</form>',
			'input' => '<input name="%s" %s/>',
			'textarea' => '<textarea name="%s" %s>%s</textarea>',
			'hidden' => '<input type="hidden" name="%s" %s/>',
			'checkbox' => '<input type="checkbox" name="%s" %s/>',
			'checkboxmultiple' => '<input type="checkbox" name="%s[]"%s />',
			'radio' => '<input type="radio" name="%s" id="%s" %s />%s',
			'selectstart' => '<select name="%s"%s>',
			'selectmultiplestart' => '<select name="%s[]"%s>',
			'selectempty' => '<option value=""%s>&nbsp;</option>',
			'selectoption' => '<option value="%s"%s>%s</option>',
			'selectend' => '</select>',
			'optiongroup' => '<optgroup label="%s"%s>',
			'optiongroupend' => '</optgroup>',
			'checkboxmultiplestart' => '',
			'checkboxmultipleend' => '',
			'password' => '<input type="password" name="%s" %s/>',
			'file' => '<input type="file" name="%s" %s/>',
			'file_no_model' => '<input type="file" name="%s" %s/>',
			'submit' => '<input type="submit" %s/>',
			'submitimage' => '<input type="image" src="%s" %s/>',
			'button' => '<input type="%s" %s/>',
			'image' => '<img src="%s" %s/>',
			'tableheader' => '<th%s>%s</th>',
			'tableheaderrow' => '<tr%s>%s</tr>',
			'tablecell' => '<td%s>%s</td>',
			'tablerow' => '<tr%s>%s</tr>',
			'block' => '<div%s>%s</div>',
			'blockstart' => '<div%s>',
			'blockend' => '</div>',
			'tag' => '<%s%s>%s</%s>',
			'tagstart' => '<%s%s>',
			'tagend' => '</%s>',
			'para' => '<p%s>%s</p>',
			'parastart' => '<p%s>',
			'label' => '<label for="%s"%s>%s</label>',
			'fieldset' => '<fieldset%s>%s</fieldset>',
			'fieldsetstart' => '<fieldset><legend>%s</legend>',
			'fieldsetend' => '</fieldset>',
			'legend' => '<legend>%s</legend>',
			'css' => '<link rel="%s" type="text/css" href="%s" %s/>',
			'style' => '<style type="text/css"%s>%s</style>',
			'charset' => '<meta http-equiv="Content-Type" content="text/html; charset=%s" />',
			'ul' => '<ul%s>%s</ul>',
			'ol' => '<ol%s>%s</ol>',
			'li' => '<li%s>%s</li>',
			'error' => '<div%s>%s</div>'
	);
/**
 * Constructor.
 *
 * @return	void
 * @access	private
 */
	function __construct() {

		parent::__construct();

		$this->tags['checkboxmultiple'] = '<input type="checkbox" name="%s[]"%s />&nbsp;';
		$this->tags['hiddenmultiple'] = '<input type="hidden" name="%s[]" %s />';

	}
/**
 * Checks if a file exists when theme is used, if no file is found default location is returned
 *
 * PENDING Core Hack
 *
 * @param  string  $file
 * @return string  $webPath web path to file.
 */
	function webroot($file) {

		// CUSTOMIZE ADD 2010/05/19 ryuring
		// CakePHP1.2.6以降、Rewriteモジュールを利用せず、App.baseUrlを利用した場合、
		// Dispatcherでwebrootが正常に取得できなくなってしまったので、ここで再設定する
		// CUSTOMIZE MODIFY 2011/03/17 ryuring
		// BC_DEPLOY_PATTERN 2 について対応
		// >>>
		$dir = Configure::read('App.dir');
		$webroot = Configure::read('App.webroot');
		$baseUrl = Configure::read('App.baseUrl');
		if($baseUrl) {
			switch (BC_DEPLOY_PATTERN) {
				case 1:
					if (strpos($this->webroot, $dir) === false) {
						$this->webroot .= $dir . '/' ;
					}
					if (strpos($this->webroot, $webroot) === false) {
						$this->webroot .= $webroot . '/';
					}
					break;
				case 2:
					$baseDir = str_replace('index.php', '', $baseUrl);
					$this->webroot = $baseDir;
					break;
			}
		}
		//<<<

		// CUSTOMIZE MODIFY 2009/10/6 ryuring
		// Rewriteモジュールが利用できない場合、$html->css / $javascript->link では、
		// app/webroot/を付加してURLを生成してしまう為、vendors 内のパス解決ができない。
		// URLの取得方法をRouterに変更
		// Dispatcherクラスのハックが必須
		//
		// CUSTOMIZE MODIFY 2010/02/12 ryuring
		// ファイルの存在チェックを行い存在しない場合のみRouterを利用するように変更した。
		//
		// CUSTOMIZE MODIFY 2011/04/11 ryuring
		// Rewriteモジュールが利用できない場合、画像等で出力されるURL形式（/app/webroot/img/...）が
		// $file に設定された場合でもパス解決ができるようにした。
		//
		// >>>
		// $webPath = "{$this->webroot}" . $file;
		// ---
		if($this->webroot && $this->webroot != '/') {
			$file = preg_replace('/'.preg_quote($this->webroot, '/').'/', '', $file);
		}
		$filePath = str_replace('/', DS, $file);
		
		if(strpos($filePath, '?') !== false) {
			list($filePath) = explode('?', $filePath);
		}
		
		$docRoot = docRoot();
		if(file_exists(WWW_ROOT . $filePath)) {
			$webPath = $this->webroot.$file;
		} elseif(file_exists($docRoot.DS.$filePath) && strpos($docRoot.DS.$filePath, ROOT.DS) !== false) {
			// ※ ファイルのパスが ROOT 配下にある事が前提
			$webPath = $file;
		} else {
			$webPath = Router::url('/'.$file);
		}
		// <<<

		if (!empty($this->themeWeb)) {
			$os = env('OS');
			if (!empty($os) && strpos($os, 'Windows') !== false) {
				if (strpos(WWW_ROOT . $this->themeWeb  . $filePath, '\\') !== false) {
					$path = str_replace('/', '\\', WWW_ROOT . $this->themeWeb  . $filePath);
				}
			} else {
				$path = WWW_ROOT . $this->themeWeb  . $filePath;
			}
			if (file_exists($path)) {
				$webPath = "{$this->webroot}" . $this->themeWeb . $file;
			}
		}

		if (strpos($webPath, '//') !== false) {
			return str_replace('//', '/', $webPath);
		}
		// >>> CUSTOMIZE ADD 2010/02/12 ryuring
		if (strpos($webPath, '\\') !== false) {
			$webPath = str_replace("\\",'/',$webPath);
		}
		// <<<
		
		return $webPath;
	}
/**
 * フック処理を実行する
 * 
 * @param	string	$hook
 * @return	mixed
 */
	function executeHook($hook) {
		
		$args = func_get_args();
		return call_user_func_array(array($this, 'dispatchPluginHook'), $args);
		
	}
/**
 * プラグインフックのイベントを発火させる
 * 
 * @param string $hook
 * @return mixed 
 */
	function dispatchPluginHook($hook) {
		
		if(!$this->_view){
			$this->_view =& ClassRegistry::getObject('View');
		}

		$args = func_get_args();
		$args[0] =& $this;

		return call_user_func_array(array($this->_view->loaded['bcPluginHook'], $hook), $args);
		
	}
/**
 * プラグインフックのハンドラを実行する
 * @param type $hook
 * @param type $return
 * @return type 
 */
	function executePluginHook($hook, $return) {
		
		$args = func_get_args();
		$View = ClassRegistry::getObject('View');
		$BcPluginHook = $View->loaded['bcPluginHook'];
		return call_user_func_array(array($BcPluginHook, 'executeHook'), $args);
		
	}
/**
 * Finds URL for specified action.
 *
 * Returns an URL pointing to a combination of controller and action. Param
 * $url can be:
 *	+ Empty - the method will find adress to actuall controller/action.
 *	+ '/' - the method will find base URL of application.
 *	+ A combination of controller/action - the method will find url for it.
 *
 * @param  mixed  $url    Cake-relative URL, like "/products/edit/92" or "/presidents/elect/4"
 *                        or an array specifying any of the following: 'controller', 'action',
 *                        and/or 'plugin', in addition to named arguments (keyed array elements),
 *                        and standard URL arguments (indexed array elements)
 * @param boolean $full   If true, the full base URL will be prepended to the result
 * @return string  Full translated URL with base path.
 */
	function url($url = null, $full = false, $sessionId = true) {
		
		if($sessionId) {
			$url = addSessionId($url);
		}
		
		//======================================================================
		// FormHelper::createで id をキーとして使うので、ルーターでマッチしない場合がある。
		// id というキー名を除外する事で対応。
		//======================================================================
		if(is_array($url) && isset($url['id'])) {
			array_push($url, $url['id']);
			unset($url['id']);
		}

		if (is_array($url) && !isset($url['admin']) && !empty($this->params['admin'])) {
			$url['admin'] = true;
		}
		
		if(!is_array($url) && preg_match('/\/(img|css|js|files)/', $url)) {
			return $this->webroot($url);
		} elseif(!is_array($url) && preg_match('/^javascript:/', $url)) {
			return $url;
		} else {
			return parent::url($url, $full);
		}
		
	}
}