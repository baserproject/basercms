<?php
/* SVN FILE: $Id: page_category.php 309 2010-06-13 11:57:06Z ryuring $ */
/**
 * ページカテゴリーモデル
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
 * @package			baser.models
 * @since			Baser v 0.1.0
 * @version			$Revision: 309 $
 * @modifiedby		$LastChangedBy: ryuring $
 * @lastmodified	$Date: 2010-06-13 20:57:06 +0900 (日, 13 6 2010) $
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページカテゴリーモデル
 *
 * @package			baser.models
 */
class PageCategory extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'PageCategory';
/**
 * バリデーション設定
 * @var array
 */
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
	var $useDbConfig = 'baser';
/**
 * actsAs
 * @var array
 */
	var $actsAs = array('Tree');
/**
 * hasMany
 * @var array
 */
	var $hasMany = array('Page' => array('className'=>'Page',
							'conditions'=>'',
							'order'=>'Page.id',
							'limit'=>'',
							'foreignKey'=>'page_category_id',
							'dependent'=>false,
							'exclusive'=>false,
							'finderQuery'=>''));
/**
 * ページカテゴリフォルダのパスリスト
 * キーはカテゴリID
 * キャッシュ用
 * @var		mixed
 * @access	protected
 */
	var $_pageCategoryPathes = -1;
/**
 * モバイルカテゴリのID
 * @var		mixed		-1 / false / int
 * @access	protected
 */
	var $_mobileId = -1;
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(array(	'rule' => array('minLength',1),
						'message' => ">> ページカテゴリ名を入力して下さい。",
						'required' => true),
				array(  'rule' => array('duplicatePageCategory'),
						'message' => '>> 入力されたページカテゴリー名は、同一階層に既に登録されています'));
		$this->validate['title'] = array(array(	'rule' => array('minLength',1),
						'message' => ">> ページカテゴリタイトルを入力して下さい。",
						'required' => true));
		return true;

	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null,$options = array()) {

		if(ClassRegistry::isKeySet('SiteConfig')) {
			$SiteConfig = ClassRegistry::getObject('SiteConfig');
		}
		$conditions = array();
		if(!empty($options['excludeParentId'])) {
			$children = $this->children($options['excludeParentId']);
			$excludeIds = array($options['excludeParentId']);
			foreach($children as $child) {
				$excludeIds[] = $child['PageCategory']['id'];
			}
			$conditions['NOT']['PageCategory.id'] = $excludeIds;
		}

		$parents = $this->generatetreelist($conditions);
		$controlSources['parent_id'] = array();
		foreach($parents as $key => $parent) {
			if($parent) {
				if(preg_match("/^([_]+)/i",$parent,$matches)) {
					$parent = preg_replace("/^[_]+/i",'',$parent);
					$prefix = str_replace('_','&nbsp&nbsp&nbsp',$matches[1]);
					$parent = $prefix.'└'.$parent;
				}
				$controlSources['parent_id'][$key] = $parent;
			}
		}

		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * beforeSave
 * @return boolean
 */
	function beforeSave() {

		// 新しいページファイルのパスを取得する
		$newPath = $this->createPageCategoryFolder($this->data);
		if($this->exists()) {
			$oldPath = $this->createPageCategoryFolder($this->find('first',array('conditions'=>array('id'=>$this->id))));
			if($newPath != $oldPath) {
				$dir = new Folder();
				$ret = $dir->move(array('to'=>$newPath,'from'=>$oldPath,'chmod'=>0777));
			}else {
				if(!is_dir($newPath)) {
					$dir = new Folder();
					$ret = $dir->create($newPath, 0777);
				}
				$ret = true;
			}
		}else {
			$dir = new Folder();
			$ret = $dir->create($newPath, 0777);
		}

		return $ret;

	}
/**
 * afterSave
 * @param	boolean	$created
 * @access	public
 */
	function afterSave($created) {
		if(!$created) {
			$this->updateRelatedPageUrl($this->data['PageCategory']['id']);
		}
	}
/**
 * ページカテゴリのフォルダを生成してパスを返す
 * @param	array	$data	ページカテゴリデータ
 * @return	mixid	カテゴリのパス / false
 * @access	public
 */
	function createPageCategoryFolder($data) {
		$path = $this->getPageCategoryFolderPath($data);
		$folder = new Folder();
		if($folder->create($path, 0777)){
			return $path;
		}else{
			return false;
		}
	}
/**
 * カテゴリフォルダのパスを取得する
 * @param	array	$data	ページカテゴリデータ
 * @return	string	$path
 * @access	public
 */
	function getPageCategoryFolderPath($data) {

		if(isset($data['PageCategory'])) {
			$data = $data['PageCategory'];
		}
		
		$SiteConfig = ClassRegistry::getObject('SiteConfig');
		$siteConfig = $SiteConfig->findExpanded();
		$theme = $siteConfig['theme'];
		$categoryName = $data['name'];
		$parentId = $data['parent_id'];

		if($theme) {
			$path = WWW_ROOT.'themed'.DS.$theme.DS.'pages'.DS;
		}else {
			$path = VIEWS.'pages'.DS;
		}

		if($parentId) {
			$categoryPath = $this->getPath($parentId);
			if($categoryPath) {
				foreach($categoryPath as $category) {
					$path .= $category['PageCategory']['name'].DS;
				}
			}
		}
		return $path.$categoryName;
		
	}
/**
 * 同一階層に同じニックネームのカテゴリがないかチェックする
 * 同じテーマが条件
 * @param array $check
 * @return boolean
 */
	function duplicatePageCategory($check) {

		$parentId = $this->data['PageCategory']['parent_id'];
		if($parentId) {
			$conditions['PageCategory.parent_id'] = $parentId;
		}else {
			$conditions['OR'] = array('PageCategory.parent_id'=>'');
			$conditions['OR'] = array('PageCategory.parent_id'=>null);
		}

		$children = $this->find('all',array('conditions'=>$conditions));

		if($children) {
			foreach($children as $child) {
				if($this->exists()) {
					if($this->id == $child['PageCategory']['id']) {
						continue;
					}
				}
				if($child['PageCategory']['name'] == $check[key($check)]) {
					return false;
				}
			}
		}
		return true;

	}
/**
 * 関連するページデータをカテゴリ無所属に変更し保存する
 * @param <type> $cascade
 * @return <type>
 */
	function beforeDelete($cascade = true) {
		parent::beforeDelete($cascade);
		$id = $this->data['PageCategory']['id'];
		if($this->releaseRelatedPagesRecursive($id)){
			$path = $this->createPageCategoryFolder($this->find('first',array('conditions'=>array('id'=>$id))));
			$folder = new Folder();
			$folder->delete($path);
			return true;
		}else {
			return false;
		}
	}
/**
 * 関連するページのカテゴリを解除する（再帰的）
 * @param	int		$categoryId
 * @return	boolean
 * @access	public
 */
	function releaseRelatedPagesRecursive($categoryId) {
		if(!$this->releaseRelatedPages($categoryId)){
			return false;
		}
		$children = $this->children($categoryId);
		$ret = true;
		foreach($children as $child) {
			if(!$this->releaseRelatedPages($child['PageCategory']['id'])) {
				$ret = false;
			}
		}
		return $ret;
	}
/**
 * 関連するページのカテゴリを解除する
 * @param	int		$categoryId
 * @return	boolean
 * @access	public
 */
	function releaseRelatedPages($categoryId) {
		$pages = $this->Page->find('all',array('conditions'=>array('Page.page_category_id'=>$categoryId),'recursive'=>-1));
		$ret = true;
		if($pages) {
			foreach($pages as $page) {
				$page['Page']['page_category_id'] = '';
				$page['Page']['url'] = $this->Page->getPageUrl($page);
				$this->Page->set($page);
				if(!$this->Page->save()) {
					$ret = false;
				}
			}
		}
		return $ret;
	}
/**
 * 関連するページデータのURLを更新する
 * @param	string	$id
 * @return	void
 * @access	public
 */
	function updateRelatedPageUrlRecursive($categoryId) {
		if(!$this->releaseRelatedPages($categoryId)){
			return false;
		}
		$children = $this->children($categoryId);
		$ret = true;
		foreach($children as $child) {
			if(!$this->updateRelatedPageUrl($child['PageCategory']['id'])) {
				$ret = false;
			}
		}
		return $ret;
	}
/**
 * 関連するページデータのURLを更新する
 * @param	string	$id
 * @return	void
 * @access	public
 */
	function updateRelatedPageUrl($id) {
		if(!$id) {
			return;
		}
		$pages = $this->Page->find('first',array('conditions'=>array('Page.page_category_id'=>$id),'recursive'=>-1));
		$result = true;
		// ページデータのURLを更新
		if($pages) {
			$this->Page->saveFile = false;
			foreach($pages as $page) {
				$page['url'] = $this->Page->getPageUrl($page);
				$this->Page->set($page);
				if(!$this->Page->save()){
					$result = false;
				}
			}
		}
		return $result;
	}
/**
 * カテゴリフォルダのパスから対象となるデータが存在するかチェックする
 * 存在する場合は id を返す
 * @param	string	$path
 * @return	mixed
 */
	function getIdByPath($path) {
		if($this->_pageCategoryPathes == -1) {
			$this->_pageCategoryPathes = array();
			$pageCategories = $this->find('all');
			if($pageCategories) {
				foreach($pageCategories as $pageCategory) {
					$this->_pageCategoryPathes[$pageCategory['PageCategory']['id']] = $this->getPageCategoryFolderPath($pageCategory);
				}
			}
		}
		if(in_array($path, $this->_pageCategoryPathes)) {
			return array_search($path,$this->_pageCategoryPathes);
		}else{
			return false;
		}
	}
/**
 * モバイル用のカテゴリIDをリストで取得する
 * @return	array	$ids
 * @access	public
 */
	function getMobileCategoryIds(){

		$mobileId = $this->getMobileId();
		if(!$mobileId){
			return array();
		}
		$ids = array($mobileId);
		$children = $this->children($mobileId,false,array('PageCategory.id'),array('PageCategory.id'));
		if($children){
			$children = Set::extract('/PageCategory/id',$children);
			$ids = am($ids,$children);
		}
		return $ids;
		
	}
/**
 * モバイルカテゴリのIDを取得する
 * @return string
 */
	function getMobileId() {
		if($this->_mobileId == -1){
			$this->_mobileId = $this->field('id',array('PageCategory.name'=>'mobile'));
		}
		return $this->_mobileId;
	}
}
?>