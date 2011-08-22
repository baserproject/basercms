<?php
/* SVN FILE: $Id$ */
/**
 * ページカテゴリーモデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページカテゴリーモデル
 *
 * @package baser.models
 */
class PageCategory extends AppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'PageCategory';
/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'baser';
/**
 * actsAs
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('Tree', 'Cache');
/**
 * hasMany
 * 
 * @var array
 * @access @public
 */
	var $hasMany = array('Page' => array('className'=>'Page',
							'conditions'=>'',
							'order'=>'Page.sort',
							'limit'=>'',
							'foreignKey'=>'page_category_id',
							'dependent'=>false,
							'exclusive'=>false,
							'finderQuery'=>''));
/**
 * ページカテゴリフォルダのパスリスト
 * キーはカテゴリID
 * キャッシュ用
 * 
 * @var mixed
 * @access protected
 */
	var $_pageCategoryPathes = -1;
/**
 * モバイルカテゴリのID
 * 
 * @var mixed -1 / false / int
 * @access	protected
 */
	var $_mobileId = -1;
/**
 * 保存時に関連ページを更新するかどうか
 * 
 * @var boolean
 * @access public
 */
	var $updateRelatedPage = true;
/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('minLength', 1),
					'message'	=> 'ページカテゴリ名を入力してください。',
					'required'	=> true),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'ページカテゴリ名は50文字以内で入力してください。'),
			array(  'rule'		=> array('duplicatePageCategory'),
					'message'	=> '入力されたページカテゴリー名は、同一階層に既に登録されています。')
		),
		'title' => array(
			array(	'rule'		=> array('minLength', 1),
					'message'	=> 'ページカテゴリタイトルを入力してください。',
					'required'	=> true),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'ページカテゴリタイトルは255文字以内で入力してください。')
		)
	);
/**
 * コントロールソースを取得する
 *
 * @param string フィールド名
 * @return array コントロールソース
 * @access public
 */
	function getControlSource($field, $options = array()) {

		switch ($field) {
			
			case 'parent_id':
				
				$conditions = array();
				if(isset($options['conditions'])) {
					$conditions = $options['conditions'];
				}
				
				if(isset($options['excludeParentId'])) {
					if($options['excludeParentId']) {
						$children = $this->children($options['excludeParentId']);
						$excludeIds = array($options['excludeParentId']);
						foreach($children as $child) {
							$excludeIds[] = $child['PageCategory']['id'];
						}
						$conditions['NOT']['PageCategory.id'] = $excludeIds;
					} else {
						// excludeParentId が空文字の場合はPCページ以外を指す
						$mobileCategoryIds = $this->getMobileCategoryIds(false);
						if($mobileCategoryIds) {
							$conditions['PageCategory.id'] = $mobileCategoryIds;
						} else {
							return array();
						}
					}
				}
				
				if(isset($options['ownerId'])) {
					$ownerIdConditions = array(
						array('PageCategory.owner_id' => null),
						array('PageCategory.owner_id' => $options['ownerId']),
					);
					if(isset($conditions['OR'])) {
						$conditions['OR'] = am($conditions['OR'], $ownerIdConditions);
					} else {
						$conditions['OR'] = $ownerIdConditions;
					}
				}

				$parents = $this->generatetreelist($conditions);
				$controlSources['parent_id'] = array();
				if(!Configure::read('Baser.mobile')) {
					$excludeIds = $this->getMobileCategoryIds();
				} else {
					$excludeIds = array();
				}
				foreach($parents as $key => $parent) {
					if($parent && !in_array($key, $excludeIds)) {
						if(preg_match("/^([_]+)/i",$parent,$matches)) {
							$parent = preg_replace("/^[_]+/i",'',$parent);
							$prefix = str_replace('_','&nbsp&nbsp&nbsp',$matches[1]);
							$parent = $prefix.'└'.$parent;
						}
						$controlSources['parent_id'][$key] = $parent;
					}
				}
				break;
			case 'owner_id':
				$UserGroup = ClassRegistry::init('UserGroup');
				$controlSources['owner_id'] = $UserGroup->find('list', array('fields' => array('id', 'title'), 'recursive' => -1));
				break;
		}
		
		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * beforeSave
 * 
 * @return boolean
 * @access public
 */
	function beforeSave() {

		// セーフモードの場合はフォルダの自動生成は行わない
		if(ini_get('safe_mode')) {
			return true;
		}

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
 * 
 * @param boolean $created
 * @return void
 * @access public
 */
	function afterSave($created) {
		
		if(!$created && $this->updateRelatedPage) {
			$this->updateRelatedPageUrlRecursive($this->data['PageCategory']['id']);
		}
		
	}
/**
 * ページカテゴリのフォルダを生成してパスを返す
 * 
 * @param array $data ページカテゴリデータ
 * @return mixid カテゴリのパス / false
 * @access public
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
 * 
 * @param array $data ページカテゴリデータ
 * @return string $path
 * @access public
 */
	function getPageCategoryFolderPath($data) {

		if(isset($data['PageCategory'])) {
			$data = $data['PageCategory'];
		}

		$path = $pagesPath = getViewPath().'pages'.DS;
		$categoryName = $data['name'];
		$parentId = $data['parent_id'];

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
 * 
 * @param array $check
 * @return boolean
 * @access public
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
 * 
 * @param boolean $cascade
 * @return boolean
 * @access public
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
 * 
 * @param int $categoryId
 * @return boolean
 * @access public
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
 * 
 * @param int $categoryId
 * @return boolean
 * @access public
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
 * 
 * @param string $id
 * @return void
 * @access public
 */
	function updateRelatedPageUrlRecursive($categoryId) {
		
		if(!$this->updateRelatedPageUrl($categoryId)){
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
 * 
 * @param string $id
 * @return void
 * @access public
 */
	function updateRelatedPageUrl($id) {
		
		if(!$id) {
			return;
		}
		$pages = $this->Page->find('all',array('conditions'=>array('Page.page_category_id'=>$id),'recursive'=>-1));
		$result = true;
		// ページデータのURLを更新
		if($pages) {
			$this->Page->saveFile = false;
			foreach($pages as $page) {
				$page['Page']['url'] = $this->Page->getPageUrl($page);
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
 * 
 * @param string $path
 * @return mixed
 * @access public
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
 * 
 * @param boolean $top
 * @return array $ids
 * @access public
 */
	function getMobileCategoryIds($top = true){

		$mobileId = $this->getMobileId();
		if(!$mobileId){
			return array();
		}
		$ids = array();
		if($top) {
			$ids[] = $mobileId;
		}
		$children = $this->children($mobileId,false,array('PageCategory.id'),array('PageCategory.id'));
		if($children){
			$children = Set::extract('/PageCategory/id',$children);
			$ids = am($ids,$children);
		}
		return $ids;

	}
/**
 * モバイルカテゴリのIDを取得する
 * 
 * @param int $pcId
 * @return int
 * @access public
 */
	function getMobileId($pcId = null) {
		
		if($pcId){
			$path = $this->getPath($pcId, array('name'), -1);
			$path = Set::extract('/PageCategory/name', $path);
			$path = implode(DS, $path);
			$path = getViewPath().'pages'.DS.'mobile'.DS.$path;
			$mobileId = $this->getIdByPath($path);			
		}else{
			if($this->_mobileId == -1){
				$this->_mobileId = $this->field('id',array('PageCategory.name'=>'mobile'));
			}
			$mobileId = $this->_mobileId;
		}
		return $mobileId;
		
	}
/**
 * ツリーリストを取得する
 * 
 * @param array $fields
 * @param int $id
 * @return array
 * @access public 
 */
	function getTreeList($fields,$id){
		
		$this->recursive = -1;
		$pageCategories = array();
		$pageCategories[] = $pageCategory = $this->read($fields,$id);
		if($pageCategory['PageCategory']['parent_id']){
			$parents = $this->getTreeList($fields,$pageCategory['PageCategory']['parent_id']);
			$pageCategories = am($parents,$pageCategories);
		}
		return $pageCategories;
		
	}
/**
 * 新しいカテゴリが追加できる状態かチェックする
 * 
 * @param int $userGroupId
 * @param boolean $rootEditable
 * @return boolean
 * @access public
 */
	function checkNewCategoryAddable($userGroupId, $rootEditable) {
		
		$newCatAddable = false;
		$ownerCats = $this->find('count', array(
			'conditions' => array(
				'OR' => array(
					array('PageCategory.owner_id' => null),
					array('PageCategory.owner_id' => $userGroupId)
				),
				'PageCategory.id <>' => $this->getMobileId()
		)));

		if($ownerCats || $rootEditable) {
			$newCatAddable = true;
		}
		return $newCatAddable;
		
	}
	
}
?>