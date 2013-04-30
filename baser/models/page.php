<?php
/* SVN FILE: $Id$ */
/**
 * ページモデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページモデル
 * 
 * @package baser.models
 */
class Page extends AppModel {
/**
 * クラス名
 * @var string
 * @access public
 */
	var $name = 'Page';
/**
 * データベース接続
 * 
 * @var string
 * @access public
 */
	var $useDbConfig = 'baser';
/**
 * belongsTo
 * 
 * @var array
 * @access public
 */
	var $belongsTo = array(
			'PageCategory' =>   array(  'className'=>'PageCategory',
							'foreignKey'=>'page_category_id'),
			'User' => array('className'=> 'User',
							'foreignKey'=>'author_id'));
/**
 * ビヘイビア
 *
 * @var array
 * @access public
 */
	var $actsAs = array('BcContentsManager', 'BcCache');
/**
 * 更新前のページファイルのパス
 * 
 * @var string
 * @access public
 */
	var $oldPath = '';
/**
 * ファイル保存可否
 * true の場合、ページデータ保存の際、ページテンプレートファイルにも内容を保存する
 * テンプレート読み込み時などはfalseにして保存しないようにする
 * 
 * @var boolean
 * @access public
 */
	var $fileSave = true;
/**
 * 検索テーブルへの保存可否
 *
 * @var boolean
 * @access public
 */
	var $contentSaving = true;
/**
 * 公開WebページURLリスト
 * キャッシュ用
 * 
 * @var mixed
 * @access protected
 */
	var $_publishes = -1;
/**
 * WebページURLリスト
 * キャッシュ用
 * 
 * @var mixed
 * @access protected
 */
	var $_pages = -1;
/**
 * 最終登録ID
 * モバイルページへのコピー処理でスーパークラスの最終登録IDが上書きされ、
 * コントローラーからは正常なIDが取得できないのでモバイルページへのコピー以外の場合だけ保存する
 *
 * @var int
 * @access private
 */
	var $__pageInsertID = null;
/**
 * バリデーション
 *
 * @var array
 * @access	public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'ページ名を入力してください。',
					'required'	=> true),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'ページ名は50文字以内で入力してください。'),
			array(	'rule'		=> array('pageExists'),
					'message'	=> '指定したページは既に存在します。ファイル名、またはカテゴリを変更してください。')
		),
		'page_category_id' => array(
			array(	'rule'		=> array('pageExists'),
					'message'	=> '指定したページは既に存在します。ファイル名、またはカテゴリを変更してください。')
		),
		'title' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'ページタイトルは255文字以内で入力してください。')
		),
		'description' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '説明文は255文字以内で入力してください。')
		)
	);
/**
 * フォームの初期値を設定する
 * 
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue() {

		if(!empty($_SESSION['Auth']['User'])) {
			$data[$this->name]['author_id'] = $_SESSION['Auth']['User']['id'];
		}
		$data[$this->name]['sort'] = $this->getMax('sort')+1;
		$data[$this->name]['status'] = false;
		return $data;

	}
/**
 * beforeSave
 * 
 * @return boolean
 * @access public
 */
	function beforeSave() {

		if(!$this->fileSave) {
			return true;
		}

		// 保存前のページファイルのパスを取得
		if($this->exists()) {
			$this->oldPath = $this->_getPageFilePath(
					$this->find('first', array(
						'conditions' => array('Page.id' => $this->data['Page']['id']),
						'recursive' => -1)
					)
			);
		}else {
			$this->oldPath = '';
		}

		// 新しいページファイルのパスが開けるかチェックする
		$result = true;
		if(!$this->checkOpenPageFile($this->data)){
			$result = false;
		}
		
		if(isset($this->data['Page'])){
			$data = $this->data['Page'];
		} else {
			$data = $this->data;
		}
		
		if(!empty($data['reflect_mobile'])){
			$data['url'] = '/'.Configure::read('BcAgent.mobile.prefix').$this->removeAgentPrefixFromUrl($data['url']);
			if(!$this->checkOpenPageFile($data)){
				$result = false;
			}
		}
		if(!empty($data['reflect_smartphone'])){
			$data['url'] = '/'.Configure::read('BcAgent.smartphone.prefix').$this->removeAgentPrefixFromUrl($data['url']);
			if(!$this->checkOpenPageFile($data)){
				$result = false;
			}
		}
		return $result;

	}
/**
 * プレフィックスを取り除く
 * 
 * @param type $url
 * @return type 
 */
	function removeAgentPrefixFromUrl($url) {
		if(preg_match('/^\/'.Configure::read('BcAgent.mobile.prefix').'\//', $url)) {
			$url = preg_replace('/^\/'.Configure::read('BcAgent.mobile.prefix').'\//', '/', $url);
		} elseif(preg_match('/^\/'.Configure::read('BcAgent.smartphone.prefix').'\//', $url)) {
			$url = preg_replace('/^\/'.Configure::read('BcAgent.smartphone.prefix').'\//', '/', $url);
		}
		return $url;
	}
/**
 * 最終登録IDを取得する
 *
 * @return	int
 * @access	public
 */
	function getInsertID(){
		
		if(!$this->__pageInsertID){
			$this->__pageInsertID = parent::getInsertID();
		}
		return $this->__pageInsertID;
		
	}
/**
 * ページテンプレートファイルが開けるかチェックする
 * 
 * @param	array	$data	ページデータ
 * @return	boolean
 * @access	public
 */
	function checkOpenPageFile($data){
		
		$path = $this->_getPageFilePath($data);
		$File = new File($path);
		if($File->open('w')) {
			$File->close();
			$File = null;
			return true;
		}else {
			return false;
		}
		
	}
/**
 * afterSave
 * 
 * @param array $created
 * @return boolean
 * @access public
 */
	function afterSave($created) {

		if(isset($this->data['Page'])){
			$data = $this->data['Page'];
		} else {
			$data = $this->data;
		}
		// タイトルタグと説明文を追加
		if(empty($data['id'])) {
			$data['id'] = $this->id;
		}
		
		if($this->fileSave) {
			$this->createPageTemplate($data);
		}

		// 検索用テーブルに登録
		if($this->contentSaving) {
			if(empty($data['exclude_search'])) {
				$this->saveContent($this->createContent($data));
			} else {
				$this->deleteContent($data['id']);
			}
		}

		// モバイルデータの生成
		if(!empty($data['reflect_mobile'])) {
			$this->refrect('mobile', $data);
		}
		if(!empty($data['reflect_smartphone'])){
			$this->refrect('smartphone', $data);
		}
			

	}
/**
 * 関連ページに反映する
 * 
 * @param string $type
 * @param array $data
 * @return boolean
 */
	function refrect($type, $data) {
		
		if(isset($this->data['Page'])){
			$data = $this->data['Page'];
		}
		
		// モバイルページへのコピーでスーパークラスのIDを上書きしてしまうので退避させておく
		$this->__pageInsertID = parent::getInsertID();

		$agentId = $this->PageCategory->getAgentId($type);
		if(!$agentId){
			// カテゴリがない場合は trueを返して終了
			return true;
		}

		$data['url'] = '/'.Configure::read('BcAgent.'.$type.'.prefix').$this->removeAgentPrefixFromUrl($data['url']);
		
		$agentPage = $this->find('first',array('conditions'=>array('Page.url'=>$data['url']),'recursive'=>-1));

		unset($data['id']);
		unset($data['sort']);
		unset($data['status']);

		if($agentPage){
			$agentPage['Page']['name'] = $data['name'];
			$agentPage['Page']['title'] = $data['title'];
			$agentPage['Page']['description'] = $data['description'];
			$agentPage['Page']['draft'] = $data['draft'];
			$agentPage['Page']['modified'] = $data['modified'];
			$agentPage['Page']['contents'] = $data['contents'];
			$agentPage['Page']['reflect_mobile'] = false;
			$agentPage['Page']['reflect_smartphone'] = false;
			$this->set($agentPage);
		}else{
			if($data['page_category_id']){
				$fields = array('parent_id','name','title');
				$pageCategoryTree = $this->PageCategory->getTreeList($fields,$data['page_category_id']);
				$path = getViewPath().'pages'.DS.Configure::read('BcAgent.'.$type.'.prefix');
				$parentId = $agentId;
				foreach($pageCategoryTree as $pageCategory) {
					$path .= '/'.$pageCategory['PageCategory']['name'];
					$categoryId = $this->PageCategory->getIdByPath($path);
					if(!$categoryId){
						$pageCategory['PageCategory']['parent_id'] = $parentId;
						$this->PageCategory->create($pageCategory);
						$ret = $this->PageCategory->save();
						$parentId = $categoryId = $this->PageCategory->getInsertID();
					}else{
						$parentId = $categoryId;
					}
				}
				$data['page_category_id'] = $categoryId;
			}else{
				$data['page_category_id'] = $agentId;
			}
			$data['author_id'] = $_SESSION['Auth']['User']['id'];
			$data['sort'] = $this->getMax('sort')+1;
			$data['status'] = false;	// 新規ページの場合は非公開とする
			unset($data['publish_begin']);
			unset($data['publish_end']);
			unset($data['created']);
			unset($data['modified']);
			$data['reflect_mobile'] = false;
			$data['reflect_smartphone'] = false;
			$this->create($data);

		}
		return $this->save();

	}
/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 * @access public
 */
	function createContent($data) {

		if(isset($data['Page'])) {
			$data = $data['Page'];
		}
		if(!isset($data['publish_begin'])) {
			$data['publish_begin'] = '';
		}
		if(!isset($data['publish_end'])) {
			$data['publish_end'] = '';
		}

		if(!$data['title']) {
			$data['title'] = Inflector::camelize($data['name']);
		}
		
		// モバイル未対応
		$PageCategory = ClassRegistry::init('PageCategory');
		$excludeIds = am($PageCategory->getAgentCategoryIds('mobile'), $PageCategory->getAgentCategoryIds('smartphone'));
		
		// インストール時取得できないのでハードコーディング
		if(!$excludeIds) {
			$excludeIds = array(1, 2);
		}
		
		if(in_array($data['page_category_id'], $excludeIds)) {
			return array();
		}

		$_data = array();
		$_data['Content']['type'] = 'ページ';
		// $this->idに値が入ってない場合もあるので
		if(!empty($data['id'])) {
			$_data['Content']['model_id'] = $data['id'];
		} else {
			$_data['Content']['model_id'] = $this->id;
		}
		$_data['Content']['category'] = '';
		if(!empty($data['page_category_id'])) {
			$categoryPath = $PageCategory->getPath($data['page_category_id'], array('title'));
			if($categoryPath) {
				$_data['Content']['category'] = $categoryPath[0]['PageCategory']['title'];
			}
		}
		$_data['Content']['title'] = $data['title'];
		$parameters = explode('/', preg_replace("/^\//", '', $data['url']));
		
		// Viewオブジェクトを一旦削除しないと、Helper内で、View::getVar()を利用しても、
		// 最初に生成したViewの値を使いまわしてしまうので、一旦退避させた上で削除する
		$View = ClassRegistry::getObject('View');
		if($View) {
			ClassRegistry::removeObject('View');
		}
		$detail = $this->requestAction(array('controller' => 'pages', 'action' => 'display'), array('pass' => $parameters, 'return') );
		if($View) {
			ClassRegistry::addObject('View', $View);
		}
		
		$detail = preg_replace('/<!-- BaserPageTagBegin -->.*?<!-- BaserPageTagEnd -->/is', '', $detail);
		$_data['Content']['detail'] = $data['description'].' '.$detail;
		$_data['Content']['url'] = $data['url'];
		$_data['Content']['status'] = $this->allowedPublish($data['status'], $data['publish_begin'], $data['publish_end']);

		return $_data;

	}
/**
 * beforeDelete
 * 
 * @return boolean
 * @access public
 */
	function beforeDelete() {
		
		return $this->deleteContent($this->id);
		
	}
/**
 * データが公開済みかどうかチェックする
 * 同様のメソッド checkPublish があり DB接続前提でURLでチェックする仕組みだが
 * こちらは、実データで直接チェックする
 * TODO メソッド名のリファクタリング要
 *
 * @param boolean $status
 * @param boolean $publishBegin
 * @param boolean $publishEnd
 * @return	array
 * @access public
 */
	function allowedPublish($status, $publishBegin, $publishEnd) {

		if(!$status) {
			return false;
		}

		if($publishBegin && $publishBegin != '0000-00-00 00:00:00') {
			if($publishBegin < date('Y-m-d H:i:s')) {
				return false;
			}
		}

		if($publishEnd && $publishEnd != '0000-00-00 00:00:00') {
			if($publishEnd > date('Y-m-d H:i:s')) {
				return false;
			}
		}

		return true;

	}
/**
 * DBデータを元にページテンプレートを全て生成する
 * 
 * @return boolean
 * @access public
 */
	function createAllPageTemplate(){
		
		$pages = $this->find('all', array('recursive' => -1));
		$result = true;
		foreach($pages as $page){
			if(!$this->createPageTemplate($page)){
				$result = false;
			}
		}
		return $result;
		
	}
/**
 * ページテンプレートを生成する
 * 
 * @param array $data ページデータ
 * @return boolean
 * @access public
 */
	function createPageTemplate($data){

		if(isset($data['Page'])){
			$data = $data['Page'];
		}
		$contents = $this->addBaserPageTag($data['id'], $data['contents'], $data['title'],$data['description'], $data['code']);

		// 新しいページファイルのパスを取得する
		$newPath = $this->_getPageFilePath($data);

		// ファイルに保存
		$newFile = new File($newPath, true);
		if($newFile->open('w')) {
			if($newFile->append($contents)) {
				// テーマやファイル名が変更された場合は元ファイルを削除する
				if($this->oldPath && ($newPath != $this->oldPath)) {
					$oldFile = new File($this->oldPath);
					$oldFile->delete();
					unset($oldFile);
				}
			}
			$newFile->close();
			unset($newFile);
			@chmod($newPath, 0666);
			return true;
		}else {
			return false;
		}

	}
/**
 * ページファイルのディレクトリを取得する
 * 
 * @param array $data
 * @return string
 * @access protected
 */
	function _getPageFilePath($data) {

		if(isset($data['Page'])){
			$data = $data['Page'];
		}

		$file = $data['name'];
		$categoryId = $data['page_category_id'];
		$SiteConfig = ClassRegistry::getObject('SiteConfig');
		if(!$SiteConfig){
			$SiteConfig = ClassRegistry::init('SiteConfig');
		}
		$SiteConfig->cacheQueries = false;
		$siteConfig = $SiteConfig->findExpanded();
		$theme = $siteConfig['theme'];

		// pagesディレクトリのパスを取得
		if($theme) {
			$path = BASER_THEMES.$theme.DS.'pages'.DS;
		}else {
			$path = VIEWS.'pages'.DS;

		}

		if(!is_dir($path)) {
			mkdir($path);
			chmod($path,0777);
		}

		if($categoryId) {
			$this->PageCategory->cacheQueries = false;
			$categoryPath = $this->PageCategory->getPath($categoryId, null, null, -1);
			if(!$categoryPath) {
				// インストール時データの取得ができないので暫定対応
				if($categoryId == 1) {
					$categoryPath = array(0 => array('PageCategory' => array('name' => 'mobile')));
				} elseif($categoryId == 2) {
					$categoryPath = array(0 => array('PageCategory' => array('name' => 'smartphone')));
				}
			}
			if($categoryPath) {
				foreach($categoryPath as $category) {
					$path .= $category['PageCategory']['name'].DS;
					if(!is_dir($path)) {
						mkdir($path,0777);
						chmod($path,0777);
					}
				}
			}
		}
		return $path.$file.Configure::read('BcApp.templateExt');

	}
/**
 * ページファイルを削除する
 * 
 * @param array $data
 */
	function delFile($data) {
		
		$path = $this->_getPageFilePath($data);
		if($path) {
			return unlink($path);
		}
		return true;
		
	}
/**
 * ページのURLを取得する
 * 
 * @param array $data
 * @return string
 * @access public
 */
	function getPageUrl($data) {

		if(isset($data['Page'])) {
			$data = $data['Page'];
		}
		$categoryId = $data['page_category_id'];
		$url = '/';
		if($categoryId) {
			$this->PageCategory->cacheQueries = false;
			$categoryPath = $this->PageCategory->getPath($categoryId);
			if($categoryPath) {
				foreach($categoryPath as $key => $category) {
					if($key == 0 && $category['PageCategory']['name'] == Configure::read('BcAgent.mobile.prefix')) {
						$url .= Configure::read('BcAgent.mobile.prefix').'/';
					} else {
						$url .= $category['PageCategory']['name'].'/';
					}
				}
			}
		}
		return $url.$data['name'];
		
	}
/**
 * Baserが管理するタグを追加する
 * 
 * @param string $id
 * @param string $contents
 * @param string $title
 * @param string $description
 * @return string
 * @access public
 */
	function addBaserPageTag($id, $contents, $title, $description, $code) {
		
		$tag = array();
		$tag[] = '<!-- BaserPageTagBegin -->';
		$tag[] = '<?php $bcBaser->setTitle(\''.$title.'\') ?>';
		$tag[] = '<?php $bcBaser->setDescription(\''.$description.'\') ?>';
		if($id) {
			$tag[] = '<?php $bcBaser->setPageEditLink('.$id.') ?>';
		}
		if($code) {
			$tag[] = trim($code);
		}
		$tag []= '<!-- BaserPageTagEnd -->';
		return implode("\n", $tag) . "\n\n" . $contents;
		
	}
/**
 * ページ存在チェック
 *
 * @param string チェック対象文字列
 * @return boolean
 * @access public
 */
	function pageExists($check) {
		
		$conditions['Page.name'] = $this->data['Page']['name'];
		if($this->exists()) {
			$conditions['Page.id <>'] = $this->data['Page']['id'];
		}
		if(empty($this->data['Page']['page_category_id'])) {
			if(isset($this->data['Page']['page_type']) && $this->data['Page']['page_type'] == 2) {
				$conditions['Page.page_category_id'] = $this->PageCategory->getAgentId('mobile');
			} elseif(isset($this->data['Page']['page_type']) && $this->data['Page']['page_type'] == 3) {
				$conditions['Page.page_category_id'] = $this->PageCategory->getAgentId('smartphone');
			} else {
				$conditions['Page.page_category_id'] = NULL;
			}
		}else {
			$conditions['Page.page_category_id'] = $this->data['Page']['page_category_id'];
		}
		if(!$this->find('first', array('conditions' => $conditions, 'recursive' => -1))) {
			return true;
		}else {
			return !file_exists($this->_getPageFilePath($this->data));
		}
		
	}
/**
 * コントロールソースを取得する
 *
 * @param string $field フィールド名
 * @param array $options
 * @return mixed $controlSource コントロールソース
 * @access public
 */
	function getControlSource($field, $options = array()) {

		switch ($field) {
			
			case 'page_category_id':
								
				$catOption = array();
				$isSuperAdmin = false;
				$agentRoot = true;
				
				extract($options);

				if(!empty($userGroupId)) {
					
					if(!isset($pageCategoryId)) {
						$pageCategoryId = '';
					}

					if($userGroupId == 1) {
						$isSuperAdmin = true;
					}

					// 現在のページが編集不可の場合、現在表示しているカテゴリも取得する
					if(!$pageEditable && $pageCategoryId) {
						$catOption = array('conditions' => array('OR' => array('PageCategory.id' => $pageCategoryId)));
					}

					// super admin でない場合は、管理許可のあるカテゴリのみ取得
					if(!$isSuperAdmin) {
						$catOption['ownerId'] = $userGroupId;
					}
				
					if($pageEditable && !$rootEditable && !$isSuperAdmin) {
						unset($empty);
						$agentRoot = false;
					}
				
				}
				
				$options = am($options, $catOption);
				$categories = $this->PageCategory->getControlSource('parent_id', $options);
				
				// 「指定しない」追加
				if(isset($empty)) {
					if($categories) {
						$categories = array('' => $empty) + $categories;
					} else {
						$categories = array('' => $empty);
					}
				}
				if(!$agentRoot) {
					// TODO 整理
					$agentId = $this->PageCategory->getAgentId('mobile');
					if(isset($categories[$agentId])) {
						unset($categories[$agentId]);
					}
					$agentId = $this->PageCategory->getAgentId('smartphone');
					if(isset($categories[$agentId])) {
						unset($categories[$agentId]);
					}
				}
				
				$controlSources['page_category_id'] = $categories;
				
				break;
				
			case 'user_id':
			case 'author_id':
				$controlSources[$field] = $this->User->getUserList($options);
				break;
			
		}
		
		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * キャッシュ時間を取得する
 * 
 * @param string $url
 * @return mixed int or false
 */
	function getCacheTime($url) {
		
		if(preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		$url = preg_replace('/^\/'.Configure::read('BcRequest.agentAlias').'\//', '/'.Configure::read('BcRequest.agentPrefix').'/', $url);
		$page = $this->find('first', array('conditions' => array('Page.url' => $url), 'recursive' => -1));
		if(!$page) {
			return false;
		}
		if($page['Page']['status'] && $page['Page']['publish_end'] && $page['Page']['publish_end'] != '0000-00-00 00:00:00') {
			return strtotime($page['Page']['publish_end']) - time();
		} else {
			return Configure::read('BcCache.defaultCachetime');
		}
		
	}
/**
 * 公開チェックを行う
 * 
 * @param string $url
 * @return boolean
 * @access public
 */
	function checkPublish($url) {

		if(preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		$url = preg_replace('/^\/'.Configure::read('BcRequest.agentAlias').'\//', '/'.Configure::read('BcRequest.agentPrefix').'/', $url);
		
		if($this->_publishes == -1) {
			$conditions = $this->getConditionAllowPublish();
			// 毎秒抽出条件が違うのでキャッシュしない
			$pages = $this->find('all', array(
				'fields'	=> 'url',
				'conditions'=> $conditions,
				'recursive'	=> -1,
				'cache'		=> false
			));
			if(!$pages) {
				$this->_publishes = array();
				return false;
			}
			$this->_publishes = Set::extract('/Page/url', $pages);
		}
		return in_array($url,$this->_publishes);

	}
/**
 * 公開済の conditions を取得
 *
 * @return array
 * @access public
 */
	function getConditionAllowPublish() {

		$conditions[$this->alias.'.status'] = true;
		$conditions[] = array('or'=> array(array($this->alias.'.publish_begin <=' => date('Y-m-d H:i:s')),
										array($this->alias.'.publish_begin' => NULL),
										array($this->alias.'.publish_begin' => '0000-00-00 00:00:00')));
		$conditions[] = array('or'=> array(array($this->alias.'.publish_end >=' => date('Y-m-d H:i:s')),
										array($this->alias.'.publish_end' => NULL),
										array($this->alias.'.publish_end' => '0000-00-00 00:00:00')));
		return $conditions;

	}
/**
 * ページファイルを登録する
 * ※ 再帰処理
 *
 * @param string $pagePath
 * @param string $parentCategoryId
 * @return array 処理結果 all / success
 * @access protected
 */
	function entryPageFiles($targetPath,$parentCategoryId = '') {
		
		if($this->Behaviors->attached('BcCache')) {
			$this->Behaviors->detach('BcCache');
		}
		if($this->PageCategory->Behaviors->attached('BcCache')) {
			$this->PageCategory->Behaviors->detach('BcCache');
		}
		
		$this->fileSave = false;
		$Folder = new Folder($targetPath);
		$files = $Folder->read(true,true,true);
		$Folder = null;
		$insert = 0;
		$update = 0;
		$all = 0;

		// カテゴリの取得・登録
		$categoryName = basename($targetPath);
		
		$specialCategoryIds = array(
			'',
			$this->PageCategory->getAgentId('mobile'),
			$this->PageCategory->getAgentId('smartphone')
		);
		
		if(in_array($parentCategoryId, $specialCategoryIds) && $categoryName == 'templates') {
			return array('all' => 0, 'insert' => 0, 'update' => 0);
		}
		
		$pageCategoryId = '';
		$this->PageCategory->updateRelatedPage = false;
		if($categoryName != 'pages') {
			
			// カテゴリ名の取得
			// 標準では設定されてないので、利用する場合は、あらかじめ bootstrap 等で宣言しておく
			$categoryTitles = Configure::read('Baser.pageCategoryTitles');
			$categoryTitle = -1;
			if($categoryTitles) {
				$categoryNames = explode('/', str_replace(getViewPath().'pages'.DS, '', $targetPath));
				foreach($categoryNames as $key => $value) {
					if(isset($categoryTitles[$value])) {
						if(count($categoryNames) == ($key + 1)) {
							$categoryTitle = $categoryTitles[$value]['title'];
						}elseif(isset($categoryTitles[$value]['children'])) {
							$categoryTitles = $categoryTitles[$value]['children'];
						}
					}
				}
			}
			
			$categoryId = $this->PageCategory->getIdByPath($targetPath);
			if($categoryId) {
				$pageCategoryId = $categoryId;
				if($categoryTitle != -1) { 
					$pageCategory = $this->PageCategory->find('first', array('conditions' => array('PageCategory.id' => $pageCategoryId), 'recursive' => -1));
					$pageCategory['PageCategory']['title'] = $categoryTitle;
					$this->PageCategory->set($pageCategory);
					$this->PageCategory->save();
				}
			}else {
				$pageCategory['PageCategory']['parent_id'] = $parentCategoryId;
				$pageCategory['PageCategory']['name'] = $categoryName;
				if($categoryTitle == -1) { 
					$pageCategory['PageCategory']['title'] = $categoryName;
				} else {
					$pageCategory['PageCategory']['title'] = $categoryTitle;
				}
				$pageCategory['PageCategory']['sort'] = $this->PageCategory->getMax('sort')+1;
				$this->PageCategory->cacheQueries = false;
				$this->PageCategory->create($pageCategory);
				if($this->PageCategory->save()) {
					$pageCategoryId = $this->PageCategory->getInsertID();
				}
			}
		}else {
			$categoryName = '';
		}

		// ファイル読み込み・ページ登録
		if(!$files[1]) $files[1] = array();
		foreach($files[1] as $path) {

			if(preg_match('/'.preg_quote(Configure::read('BcApp.templateExt')).'$/is',$path) == false) {
				continue;
			}

			$pageName = basename($path, Configure::read('BcApp.templateExt'));
			$file = new File($path);
			$contents = $file->read();
			$file->close();
			$file = null;

			// タイトル取得・置換
			$titleReg = '/<\?php\s+?\$bcBaser->setTitle\(\'(.*?)\'\)\s+?\?>/is';
			if(preg_match($titleReg,$contents,$matches)) {
				$title = trim($matches[1]);
				$contents = preg_replace($titleReg,'',$contents);
			}else {
				$title = Inflector::camelize($pageName);
			}

			// 説明文取得・置換
			$descriptionReg = '/<\?php\s+?\$bcBaser->setDescription\(\'(.*?)\'\)\s+?\?>/is';
			if(preg_match($descriptionReg,$contents,$matches)) {
				$description = trim($matches[1]);
				$contents = preg_replace($descriptionReg,'',$contents);
			}else {
				$description = '';
			}

			// PageTagコメントの削除
			$pageTagReg = '/<\!\-\- BaserPageTagBegin \-\->.*?<\!\-\- BaserPageTagEnd \-\->/is';
			$contents = preg_replace($pageTagReg,'',$contents);

			$conditions['Page.name'] = $pageName;
			if($pageCategoryId) {
				$conditions['Page.page_category_id'] = $pageCategoryId;
			}else{
				$conditions['Page.page_category_id'] = null;
			}
			$page = $this->find('first', array('conditions' => $conditions, 'recursive' => -1));
			if($page) {
				$chage = false;
				if($title != $page['Page']['title']) {
					$chage = true;
				}
				if($description != $page['Page']['description']) {
					$chage = true;
				}
				if(trim($contents) != trim($page['Page']['contents'])) {
					$chage = true;
				}
				if($chage) {
					$page['Page']['title'] = $title;
					$page['Page']['description'] = $description;
					$page['Page']['contents'] = $contents;
					$this->set($page);
					if($this->save()) {
						$update++;
					}
				}
			}else {
				$page = $this->getDefaultValue();
				$page['Page']['name'] = $pageName;
				$page['Page']['title'] = $title;
				$page['Page']['description'] = $description;
				$page['Page']['contents'] = $contents;
				$page['Page']['page_category_id'] = $pageCategoryId;
				$page['Page']['url'] = $this->getPageUrl($page);
				$this->create($page);
				if($this->save()) {
					$insert++;
				}
			}
			$contents = $page = $pageName = $title = $description = $conditions = $descriptionReg = $titleReg = $pageTagReg = null;
			$all++;
		}

		// フォルダー内の登録
		if(!$files[0]) $files[0] = array();
		foreach($files[0] as $file) {
			$folderName = basename($file);
			if($folderName != '_notes' && $folderName != 'admin') {
				$result = $this->entryPageFiles($file,$pageCategoryId);
				$insert += $result['insert'];
				$update += $result['update'];
				$all += $result['all'];
			}
		}

		return array('all'=>$all,'insert'=>$insert,'update'=>$update);

	}
/**
 * 関連ページの存在チェック
 * 存在する場合は、ページIDを返す
 * 
 * @param array $data ページデータ
 * @return mixed ページID / false
 * @access public
 */
	function agentExists ($type, $data) {
		
		if(isset($data['Page'])){
			$data = $data['Page'];
		}
		$url = $this->removeAgentPrefixFromUrl($data['url']);
		if(preg_match('/^\/'.Configure::read('BcAgent.'.$type.'.prefix').'\//is', $url)){
			// 対象ページがモバイルページの場合はfalseを返す
			return false;
		}
		return $this->field('id',array('Page.url'=>'/'.Configure::read('BcAgent.'.$type.'.prefix').$url));
		
	}
/**
 * ページで管理されているURLかチェックする
 * 
 * @param string $url
 * @return boolean
 * @access public
 */
	function isPageUrl($url) {
		
		if(preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		$url = preg_replace('/^\/'.Configure::read('BcRequest.agentAlias').'\//', '/'.Configure::read('BcRequest.agentPrefix').'/', $url);
		
		if($this->_pages == -1) {
			$pages = $this->find('all', array(
				'fields'	=> 'url',
				'recursive'	=> -1
			));
			if(!$pages) {
				$this->_pages = array();
				return false;
			}
			$this->_pages = Set::extract('/Page/url', $pages);
		}
		return in_array($url,$this->_pages);
		
	}
/**
 * Removes record for given ID. If no ID is given, the current ID is used. Returns true on success.
 *
 * @param mixed $id ID of record to delete
 * @param boolean $cascade Set to true to delete records that depend on this record
 * @return boolean True on success
 * @access public
 * @link http://book.cakephp.org/view/690/del
 */
	function del($id = null, $cascade = true) {
		
		// メッセージ用にデータを取得
		$page = $this->read(null, $id);
		
		/* 削除処理 */
		if(parent::del($id, $cascade)) {
			
			// ページテンプレートを削除
			$this->delFile($page);
			
			// 公開状態だった場合、サイトマップのキャッシュを削除
			// 公開期間のチェックは行わず確実に削除
			if($page['Page']['status']) {
				clearViewCache();
			}
			return true;
			
		} else {
			
			return false;
			
		}
		
	}
/**
 * ページデータをコピーする
 * 
 * @param int $id
 * @param array $data
 * @return mixed page Or false
 */
	function copy($id = null, $data = array()) {
		
		if($id) {
			$data = $this->find('first', array('conditions' => array('Page.id' => $id), 'recursive' => -1));
		}
		$data['Page']['name'] .= '_copy';
		$data['Page']['title'] .= '_copy';
		if(!empty($_SESSION['Auth']['User'])) {
			$data['Page']['author_id'] = $_SESSION['Auth']['User']['id'];
		}
		$data['Page']['sort'] = $this->getMax('sort')+1;
		$data['Page']['status'] = false;
		$data['Page']['url'] = $this->getPageUrl($data);
		unset($data['Page']['id']);
		unset($data['Page']['created']);
		unset($data['Page']['modified']);
		
		$this->create($data);
		$result = $this->save();
		if($result) {
			return $result;
		} else {
			if(isset($this->validationErrors['name']) && $this->validationErrors['name'] != 'ページ名は50文字以内で入力してください。') {
				return $this->copy(null, $data);
			} else {
				return false;
			}
		}
		
	}
/**
 * 連携チェック
 * 
 * @param string $agentPrefix
 * @param string $url
 * @return boolean 
 */
	function isLinked($agentPrefix, $url) {
		
		if(!$agentPrefix) {
			return false;
		}
		
		if(!Configure::read('BcApp.'.$agentPrefix)) {
			return false;
		}
		
		$siteConfig = Configure::read('BcSite');
		$linked = false;
		
		if(isset($siteConfig['linked_pages_'.$agentPrefix])) {
			$linked = $siteConfig['linked_pages_'.$agentPrefix];
		}
		
		if($linked) {
			return false;
		}
			
		if(preg_match('/\/$/', $url)) {
			$url .= 'index';
		}
		
		if($this->field('unlinked_' . $agentPrefix, array('Page.url' => $url))) {
			$linked = false;
		}
		
		return $linked;
				
	}
}