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
App::uses('Helper', 'View');
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
	protected $_View = null;
/**
 * html tags used by this helper.
 *
 * @var 	array
 * @access	public
 */
	public $tags = array(
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
 * @access	public
 */
	public function __construct(View $View, $settings = array()) {

		parent::__construct($View, $settings);

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
	public function webroot($file) {
		
		// TODO basercamp
		// みなおし要
		
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
					if (strpos($this->request->webroot, $dir) === false) {
						$this->request->webroot .= $dir . '/' ;
					}
					if (strpos($this->request->webroot, $webroot) === false) {
						$this->request->webroot .= $webroot . '/';
					}
					break;
				case 2:
					$baseDir = str_replace('index.php', '', $baseUrl);
					$this->request->webroot = $baseDir;
					break;
			}
		}
		//<<<
		
		$asset = explode('?', $file);
		$asset[1] = isset($asset[1]) ? '?' . $asset[1] : null;
		
		// CUSTOMIZE MODIFY 2009/10/6 ryuring
		// Rewriteモジュールが利用できない場合、$this->Html->css / $javascript->link では、
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
		//$webPath = "{$this->request->webroot}" . $asset[0];
		// ---
		if($this->request->webroot && $this->request->webroot != '/') {
			$filePath = preg_replace('/'.preg_quote($this->request->webroot, '/').'/', '', $asset[0]);
		} else {
			$filePath = $asset[0];
		}
		$filePath = str_replace('/', DS, $filePath);
		
		$docRoot = docRoot();
		if(file_exists(WWW_ROOT . $filePath)) {
			$webPath = $this->request->webroot . $asset[0];
		} elseif(file_exists($docRoot . DS . $filePath) && strpos($docRoot . DS . $filePath, ROOT . DS) !== false) {
			// ※ ファイルのパスが ROOT 配下にある事が前提
			$webPath = $asset[0];
		} else {
			$webPath = Router::url('/' . $asset[0]);
		}
		// <<<
		
		$file = $asset[0];

		if (!empty($this->theme)) {
			$file = trim($file, '/');
			$theme = $this->theme . '/';

			if (DS === '\\') {
				$file = str_replace('/', '\\', $file);
			}

			if (file_exists(Configure::read('App.www_root') . 'theme' . DS . $this->theme . DS . $file)) {
				$webPath = "{$this->request->webroot}theme/" . $theme . $asset[0];
			} else {
				$themePath = App::themePath($this->theme);
				$path = $themePath . 'webroot' . DS . $file;
				if (file_exists($path)) {
				// CUSTOMIZE 2013/6/18 ryuring
				// >>>
				//	$webPath = Configure::read('App.baseUrl')."{$this->request->webroot}theme/" . $theme . $asset[0];
				//}
				// ---
					if($baseUrl) {
						// スマートURLオフ
						$webPath = Configure::read('App.baseUrl') . "/theme/" . $theme . $asset[0];
					} else {
						// スマートURLオン
						$webPath = "{$this->request->webroot}theme/" . $theme . $asset[0];
					}
				} else {
					// フロントのWebページを表示する際に、管理システム用のアセットファイルを参照する為のURLを生成する
					$adminTheme = $this->_View->adminTheme . '/';
					$themePath = App::themePath($adminTheme);
					$path = $themePath . 'webroot' . DS . $file;
					if (file_exists($path)) {
						if($baseUrl) {
							// スマートURLオフ
							$webPath = Configure::read('App.baseUrl') . "/theme/" . $adminTheme . $asset[0];
						} else {
							// スマートURLオン
							$webPath = "{$this->request->webroot}theme/" . $adminTheme . $asset[0];
						}
					}
				}
				// <<<
			}
		}
		if (strpos($webPath, '//') !== false) {
			return str_replace('//', '/', $webPath . $asset[1]);
		}
		
		// >>> CUSTOMIZE ADD 2013/06/18 ryuring
		if (strpos($webPath, '\\') !== false) {
			$webPath = str_replace("\\",'/',$webPath);
		}
		// <<<
		
		return $webPath . $asset[1];
		
		
		
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
	public function url($url = null, $full = false, $sessionId = true) {
		
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
		
		if (is_array($url) && !isset($url['admin']) && !empty($this->request->params['admin'])) {
			$url = array_merge($url, array('admin' => true));
		}
			
		if(!is_array($url) && preg_match('/\/(img|css|js|files)/', $url)) {
			return $this->webroot($url);
		} elseif(!is_array($url) && preg_match('/^javascript:/', $url)) {
			return $url;
		} else {
			return parent::url($url, $full);
		}
		
	}
/**
 * イベントを発火
 * 
 * @param string $name
 * @param array $params
 * @return mixed
 */
	public function dispatchEvent($name, $params = array()) {
		
		App::uses('BcEventDispatcher', 'Event');
		return BcEventDispatcher::dispatch('Helper', $name, $this->_View, $params);
		
	}
	
}
