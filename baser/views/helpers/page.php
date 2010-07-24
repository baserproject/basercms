<?php
/* SVN FILE: $Id$ */
/**
 * ページヘルパー
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
 * ページヘルパー
 *
 * @package			baser.views.helpers
 */
class PageHelper extends Helper {
/**
 * ページモデル
 * @var		Page
 * @access	public
 */
	var $Page = null;
/**
 * data
 * @var		array
 * @access	public
 */
	var $data = array();
/**
 * construct
 */
	function  __construct() {
		if(ClassRegistry::isKeySet('Page')) {
			$this->Page = ClassRegistry::getObject('Page');
		}else {
			$this->Page =& ClassRegistry::init('Page','Model');
		}
	}
/**
 * beforeRender
 */
	function beforeRender() {
		if(isset($this->params['pass'][0])) {
			// TODO ページ機能が.html拡張子なしに統合できたらコメントアウトされたものに切り替える
			//$this->data = $this->Page->findByUrl('/'.impload('/',$this->params['pass'][0]));
			$this->data = $this->Page->findByUrl('/'.Configure::read('Baser.urlParam'));
		}
	}
/**
 * ページ機能用URLを取得する
 * @param array $page
 * @return string
 */
	function url($page) {
		return $this->Page->getPageUrl($page);
	}
/**
 * 現在のページが所属するカテゴリデータを取得する
 * @return array
 */
	function getCategory() {
		if(isset($this->data['PageCategory'])) {
			return $this->data['PageCategory'];
		}else {
			return false;
		}
	}
}
?>