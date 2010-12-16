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
			$param = Configure::read('Baser.urlParam');
			if($param && preg_match('/\/$/is',$param)){
				$param .= 'index';
			}
			$this->data = $this->Page->findByUrl('/'.$param);
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
		if(!empty($this->data['PageCategory']['id'])) {
			return $this->data['PageCategory'];
		}else {
			return false;
		}
	}
/**
 * 公開状態を取得する
 *
 * @param	array	データリスト
 * @return	boolean	公開状態
 * @access	public
 */
	function allowPublish($data){

		if(isset($data['Page'])){
			$data = $data['Page'];
		}

		$allowPublish = (int)$data['status'];

		// 期限を設定している場合に条件に該当しない場合は強制的に非公開とする
		if(($data['publish_begin'] != 0 && $data['publish_begin'] >= date('Y-m-d H:i:s')) ||
				($data['publish_end'] != 0 && $data['publish_end'] <= date('Y-m-d H:i:s'))){
			$allowPublish = false;
		}

		return $allowPublish;

	}
}
?>