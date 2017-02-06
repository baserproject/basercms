<?php
/**
 * Helper 拡張クラス
 * 
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('Helper', 'View');

/**
 * Helper 拡張クラス
 *
 * @package			Baser.View.Helper
 */
class BcAppHelper extends Helper {

/**
 * Constructor.
 *
 * @return	void
 * @access	public
 */
	public function __construct(View $View, $settings = array()) {

		parent::__construct($View, $settings);

		if (get_class($this) == 'BcHtmlHelper' || get_class($this) == 'HtmlHelper') {
			$this->_tags['checkboxmultiple'] = '<input type="checkbox" name="%s[]"%s />&nbsp;';
			$this->_tags['hiddenmultiple'] = '<input type="hidden" name="%s[]" %s />';
		}
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

		// CUSTOMIZE ADD 2015/02/15 ryuring
		// >>>
		// フルパスの場合はそのまま返す
		if(preg_match('/^(http|https):\/\//', $file)) {
			return $file;
		}
		// <<<
				
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
		$asset[0] = preg_replace('/^\//', '', $asset[0]);
		if ($this->request->webroot && $this->request->webroot != '/') {
			$filePath = preg_replace('/' . preg_quote($this->request->webroot, '/') . '/', '', $asset[0]);
		} else {
			$filePath = $asset[0];
		}
		$filePath = str_replace('/', DS, $filePath);

		$docRoot = docRoot();
		if (file_exists(WWW_ROOT . $filePath)) {
			$webPath = $this->request->webroot . $asset[0];
		} elseif (file_exists($docRoot . DS . $filePath) && strpos($docRoot . DS . $filePath, ROOT . DS) !== false) {
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
					if ($baseUrl) {
						// スマートURLオフ
						$webPath = Configure::read('App.baseUrl') . "/theme/" . $theme . $asset[0];
					} else {
						// スマートURLオン
						$webPath = "{$this->request->webroot}theme/" . $theme . $asset[0];
					}
				} else {
					
					// フロントのWebページを表示する際に、管理システム用のアセットファイルを参照する為のURLを生成する
					if (property_exists($this->_View, 'adminTheme') && $this->_View->adminTheme) {
						if (file_exists($themePath = Configure::read('App.www_root') . 'theme' . DS . $this->_View->adminTheme . DS . $file)) {
							$adminTheme = $this->_View->adminTheme . '/';
							if ($baseUrl) {
								// スマートURLオフ
								$webPath = Configure::read('App.baseUrl') . "/theme/" . $adminTheme . $asset[0];
							} else {
								// スマートURLオン
								$webPath = "{$this->request->webroot}theme/" . $adminTheme . $asset[0];
							}
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
			$webPath = str_replace("\\", '/', $webPath);
		}
		// <<<

		return $webPath . $asset[1];
	}

/**
 * Finds URL for specified action.
 *
 * Returns an URL pointing to a combination of controller and action. Param
 * $url can be:
 * 	+ Empty - the method will find adress to actuall controller/action.
 * 	+ '/' - the method will find base URL of application.
 * 	+ A combination of controller/action - the method will find url for it.
 *
 * @param  mixed  $url    Cake-relative URL, like "/products/edit/92" or "/presidents/elect/4"
 *                        or an array specifying any of the following: 'controller', 'action',
 *                        and/or 'plugin', in addition to named arguments (keyed array elements),
 *                        and standard URL arguments (indexed array elements)
 * @param boolean $full   If true, the full base URL will be prepended to the result
 * @return string  Full translated URL with base path.
 */
	public function url($url = null, $full = false, $sessionId = true) {
		if ($sessionId) {
			$url = addSessionId($url);
		}

		//======================================================================
		// FormHelper::createで id をキーとして使うので、ルーターでマッチしない場合がある。
		// id というキー名を除外する事で対応。
		//======================================================================
		if (is_array($url) && isset($url['id'])) {
			array_push($url, $url['id']);
			unset($url['id']);
		}

		if (is_array($url) && !isset($url['admin']) && !empty($this->request->params['admin'])) {
			$url = array_merge($url, array('admin' => true));
		}

		if (!is_array($url) && preg_match('/^(javascript|https?|ftp):/', $url)) {
			return $url;
		} elseif (!is_array($url) && preg_match('/\/(img|css|js|files)/', $url)) {
			return $this->webroot($url);
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
	public function dispatchEvent($name, $params = array(), $options = array()) {

		$options = array_merge(array(
			'modParams' => 0,
			'plugin' => $this->plugin,
			'layer' => 'Helper',
			'class' => str_replace('Helper', '', get_class($this))
			), $options);

		App::uses('BcEventDispatcher', 'Event');
		return BcEventDispatcher::dispatch($name, $this->_View, $params, $options);
	}

/**
 * afterLayout
 * 
 * @param type $layoutFile
 */
	public function afterLayout($layoutFile) {
		parent::afterLayout($layoutFile);
		// 出力時にインデント用のタブを除去
		// インデントの調整がちゃんとできてないので取り急ぎ除去するようにした
		// 半角スペースは除去した場合レイアウトが崩れる可能性がある為除去しない
		$this->_View->output = preg_replace("/\n[\t]+?([^\t])/", "\n$1", $this->_View->output);
	}
	
}
