<?php
/* SVN FILE: $Id$ */
/**
 * Helper 拡張クラス
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
 * @package			baser.view.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * Helper 拡張クラス
 *
 * @package			baser.view.helpers
 */
class AppHelper extends Helper {
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
		$this->tags['hiddenmultiple'] = '<input type="hidden" name="%s" %s />';

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

		// 2010/05/19 ADD ryuring
		// CakePHP1.2.6以降、Rewriteモジュールを利用せず、App.baseUrlを利用した場合、
		// Dispatcherでwebrootが正常に取得できなくなってしまったので、ここで再設定する
		// >>>
		$dir = Configure::read('App.dir');
		$webroot = Configure::read('App.webroot');
		if(Configure::read('App.baseUrl')) {
			if (strpos($this->webroot, $dir) === false) {
				$this->webroot .= $dir . '/' ;
			}
			if (strpos($this->webroot, $webroot) === false) {
				$this->webroot .= $webroot . '/';
			}
		}
		//<<<

		// 2009/10/6 MODIFY ryuring
		// Rewriteモジュールが利用できない場合、$html->css / $javascript->link では、
		// app/webroot/を付加してURLを生成してしまう為、vendors 内のパス解決ができない。
		// URLの取得方法をRouterに変更
		// Dispatcherクラスのハックが必須
		//
		// 2010/02/12 MODIFY egashira
		// ファイルの存在チェックを行い存在しない場合のみRouterを利用するように変更した。

		// >>>
		// $webPath = "{$this->webroot}" . $file;
		// ---
		if(file_exists(WWW_ROOT . $file)) {
			$webPath = $this->webroot.$file;
		}else {
			$webPath = Router::url('/'.$file);
		}
		// <<<

		if (!empty($this->themeWeb)) {
			$os = env('OS');
			if (!empty($os) && strpos($os, 'Windows') !== false) {
				if (strpos(WWW_ROOT . $this->themeWeb  . $file, '\\') !== false) {
					$path = str_replace('/', '\\', WWW_ROOT . $this->themeWeb  . $file);
				}
			} else {
				$path = WWW_ROOT . $this->themeWeb  . $file;
			}
			if (file_exists($path)) {
				$webPath = "{$this->webroot}" . $this->themeWeb . $file;
			}
		}

		if (strpos($webPath, '//') !== false) {
			return str_replace('//', '/', $webPath);
		}
		// >>> ADD
		if (strpos($webPath, '\\') !== false) {
			$webPath = str_replace("\\",'/',$webPath);
		}
		// <<<
		return $webPath;
	}
/**
 * プラグインフックを実行する
 * @param	string	$out
 * @return	mixed
 */
	function pluginHook($out,$hook) {
		if(!$this->_view){
			$this->_view =& ClassRegistry::getObject('View');
		}
		return $this->_view->loaded['pluginHook']->{$hook}($out);
	}
}
?>