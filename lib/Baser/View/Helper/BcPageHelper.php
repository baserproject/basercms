<?php
/* SVN FILE: $Id$ */
/**
 * ページヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.view.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページヘルパー
 *
 * @packa	ge baser.views.helpers
 */
class BcPageHelper extends Helper {
/**
 * ページモデル
 * 
 * @var Page
 * @access public
 */
	public $Page = null;
/**
 * data
 * @var array
 * @access public
 */
	public $data = array();
/**
 * ヘルパー
 * 
 * @var array
 * @access public
 */
	public $helpers = array(BC_BASER_HELPER);
/**
 * construct
 * 
 * @return void
 * @access public
 */
	public function  __construct() {
		
		if(ClassRegistry::isKeySet('Page')) {
			$this->Page = ClassRegistry::getObject('Page');
		}else {
			$this->Page =& ClassRegistry::init('Page','Model');
		}
		
	}
/**
 * beforeRender
 * 
 * @return void
 * @access public
 */
	public function beforeRender($viewFile) {
		
		if(isset($this->request->params['pass'][0])) {
			// TODO ページ機能が.html拡張子なしに統合できたらコメントアウトされたものに切り替える
			//$this->request->data = $this->Page->findByUrl('/'.impload('/',$this->request->params['pass'][0]));
			$param = Configure::read('BcRequest.pureUrl');
			if($param && preg_match('/\/$/is',$param)){
				$param .= 'index';
			}
			if(Configure::read('BcRequest.agent')) {
				$param = Configure::read('BcRequest.agentPrefix').'/'.$param;
			}
			$this->request->data = $this->Page->findByUrl('/'.$param);
		}
		
	}
/**
 * ページ機能用URLを取得する
 * 
 * @param array $page
 * @return string
 */
	public function url($page) {
		
		return $this->Page->getPageUrl($page);
		
	}
/**
 * 現在のページが所属するカテゴリデータを取得する
 * 
 * @return array
 * @access public
 */
	public function getCategory() {
		
		if(!empty($this->request->data['PageCategory']['id'])) {
			return $this->request->data['PageCategory'];
		}else {
			return false;
		}
		
	}
/**
 * 現在のページが所属する親のカテゴリを取得する
 *
 * @param boolean $top
 * @return array
 * @access public
 */
	public function getParentCategory($top = false) {
		
		$category = $this->getCategory();
		if(empty($category['id'])) {
			return false;
		}
		if($top) {
			$path = $this->Page->PageCategory->getpath($category['id']);
			if($path) {
				$parent = $path[0];
			} else {
				return false;
			}
		} else {
			$parent = $this->Page->PageCategory->getparentnode($category['id']);
		}
		return $parent;
		
	}
/**
 * ページリストを取得する
 * 
 * @param int $pageCategoryId
 * @param int $recursive
 * @return array
 * @access public
 */
	public function getPageList($pageCategoryId, $recursive = null) {
		
		return $this->requestAction('/contents/get_page_list_recursive', array('pass' => array($pageCategoryId, $recursive)));
		
	}
/**
 * カテゴリ名を取得する
 * 
 * @return mixed string / false
 * @access public
 */
	public function getCategoryName(){
		
		$category = $this->getCategory();
		if($category['name']) {
			return $category['name'];
		} else {
			return false;
		}
		
	}
/**
 * 公開状態を取得する
 *
 * @param array データリスト
 * @return boolean 公開状態
 * @access public
 */
	public function allowPublish($data){

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
/**
 * ページカテゴリ間の次の記事へのリンクを取得する
 *
 * @param array $post
 * @param string $title
 * @param array $attributes
 */
	public function nextLink($title='', $attributes = array()) {

		if(!$this->contensNaviAvailable()) {
			return '';
		}

		if(ClassRegistry::isKeySet('Page')) {
			$PageClass =& ClassRegistry::getObject('Page');
		} else {
			$PageClass =& ClassRegistry::init('Page');
		}

		$_attributes = array('class'=>'next-link','arrow'=>' ≫');
		$attributes = am($_attributes,$attributes);
		
		$arrow = $attributes['arrow'];
		unset($attributes['arrow']);

		$conditions = am(array(
			'Page.sort >' => $this->request->data['Page']['sort'],
			'Page.page_category_id' => $this->request->data['Page']['page_category_id']
		), $PageClass->getConditionAllowPublish());
		$nextPost = $PageClass->find('first', array(
			'conditions'=> $conditions,
			'fields'	=> array('title', 'url'),
			'order'		=> 'sort',
			'recursive'	=> -1,
			'cache'		=> false
		));
		if($nextPost) {
			if(!$title) {
				$title = $nextPost['Page']['title'].$arrow;
			}
			$this->BcBaser->link($title, preg_replace('/^\/mobile/', '/m', $nextPost['Page']['url']), $attributes);
		}

	}
/**
 * ページカテゴリ間の前の記事へのリンクを取得する
 *
 * @param array $post
 * @param string $title
 * @param array $attributes
 * @return void
 * @access public
 */
	public function prevLink($title='', $attributes = array()) {

		if(!$this->contensNaviAvailable()) {
			return '';
		}

		if(ClassRegistry::isKeySet('Page')) {
			$PageClass =& ClassRegistry::getObject('Page');
		} else {
			$PageClass =& ClassRegistry::init('Page');
		}

		$_attributes = array('class'=>'prev-link','arrow'=>'≪ ');
		$attributes = am($_attributes,$attributes);

		$arrow = $attributes['arrow'];
		unset($attributes['arrow']);

		$conditions = am(array(
			'Page.sort <' => $this->request->data['Page']['sort'],
			'Page.page_category_id' => $this->request->data['Page']['page_category_id']
		), $PageClass->getConditionAllowPublish());
		$nextPost = $PageClass->find('first', array(
			'conditions'=> $conditions,
			'fields'	=> array('title', 'url'),
			'order'		=> 'sort DESC',
			'recursive'	=> -1,
			'cache'		=> false
		));
		if($nextPost) {
			if(!$title) {
				$title = $arrow.$nextPost['Page']['title'];
			}
			$this->BcBaser->link($title, preg_replace('/^\/mobile/', '/m', $nextPost['Page']['url']), $attributes);
		}

	}
/**
 * コンテンツナビ有効チェック
 *
 * @return	boolean
 * @access	public
 */
	public function contensNaviAvailable() {
		
		if(empty($this->request->data['Page']['page_category_id']) || empty($this->request->data['PageCategory']['contents_navi'])) {
			return false;
		} else {
			return true;
		}
		
	}
	
}
?>