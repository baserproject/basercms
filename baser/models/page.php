<?php
/* SVN FILE: $Id$ */
/**
 * ページモデル
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
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページモデル
 * @package			baser.models
 */
class Page extends AppModel {
/**
 * クラス名
 * @var		string
 * @access 	public
 */
	var $name = 'Page';
/**
 * データベース接続
 * @var     string
 * @access  public
 */
	var $useDbConfig = 'baser';
/**
 * belongsTo
 * @var 	array
 * @access	public
 */
	var $belongsTo = array(
			'PageCategory' =>   array(  'className'=>'PageCategory',
							'foreignKey'=>'page_category_id'));
/**
 * 更新前のページファイルのパス
 * @var	string
 * @access public
 */
	var $oldPath = '';
/**
 * ファイル保存可否
 * true の場合、ページデータ保存の際、ページテンプレートファイルにも内容を保存する
 * テンプレート読み込み時などはfalseにして保存しないようにする
 * @var		boolean
 * @access	public
 */
	var $fileSave = true;
/**
 * 非公開URLリスト
 * キャッシュ用
 * @var mixed;
 */
	var $_unpublishes = -1;
/**
 * beforeValidate
 * @return	boolean
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(array('rule' => array('minLength',1),
						'message' => ">> ページ名を入力して下さい。",
						'required' => true),
				array('rule' => 'pageExists',
						'message' => ">> 指定したページは既に存在します。ファイル名、またはカテゴリを変更して下さい。"));
		$this->validate['page_category_id'] = array(array('rule' => 'pageExists',
						'message' => ">> 指定したページは既に存在します。ファイル名、またはカテゴリを変更して下さい。",
						'required' => false));
		return true;

	}
/**
 * フォームの初期値を設定する
 * @return	array	初期値データ
 * @access	public
 */
	function getDefaultValue() {
		
		$data[$this->name]['sort'] = $this->getMax('sort')+1;
		$data[$this->name]['status'] = false;
		return $data;

	}
/**
 * beforeSave
 * @return boolean
 */
	function beforeSave() {

		if(!$this->fileSave) {
			return true;
		}

		// 保存前のページファイルのパスを取得
		if($this->exists()) {
			$this->oldPath = $this->_getPageFilePath($this->find(array('Page.id'=>$this->data['Page']['id'])));
		}else {
			$this->oldPath = '';
		}

		// 新しいページファイルのパスが開けるかチェックする
		$newPath = $this->_getPageFilePath($this->data);
		$newFile = new File($newPath);
		if($newFile->open('w')) {
			$newFile->close();
			$newFile = null;
			return true;
		}else {
			return false;
		}

	}
/**
 * afterSave
 * @return boolean
 */
	function afterSave() {

		if(!$this->fileSave) {
			return true;
		}
		
		$data = $this->data['Page'];
		// タイトルタグと説明文を追加
		if(empty($data['id'])) {
			$data['id'] = $this->getInsertID();
		}

		return $this->createPageTemplate($data);

	}
/**
 * DBデータを元にページテンプレートを全て生成する
 * @return	boolean
 * @access	public
 */
	function createAllPageTemplate(){
		$pages = $this->find('all');
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
 * @param	array	$data ページデータ
 * @return	boolean
 * @access	public
 */
	function createPageTemplate($data){

		if(isset($data['Page'])){
			$data = $data['Page'];
		}
		$contents = $this->addBaserPageTag($data['id'], $data['contents'], $data['title'],$data['description']);

		// 新しいページファイルのパスを取得する
		$newPath = $this->_getPageFilePath($data);

		// ファイルに保存
		$newFile = new File($newPath);
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
 * @param array $data
 * @return string
 */
	function _getPageFilePath($data) {

		if(isset($data['Page'])){
			$data = $data['Page'];
		}

		$file = $data['name'];
		$categoryId = $data['page_category_id'];
		$SiteConfig = ClassRegistry::getObject('SiteConfig');
		$SiteConfig->cacheQueries = false;
		$siteConfig = $SiteConfig->findExpanded();
		$theme = $siteConfig['theme'];

		// pagesディレクトリのパスを取得
		if($theme) {
			$path = WWW_ROOT.'themed'.DS.$theme.DS.'pages'.DS;
		}else {
			$path = VIEWS.'pages'.DS;

		}

		if(!is_dir($path)) {
			mkdir($path);
			chmod($path,0777);
		}

		if($categoryId) {
			$this->PageCategory->cacheQueries = false;
			$categoryPath = $this->PageCategory->getPath($categoryId);
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
		return $path.$file.'.ctp';

	}
/**
 * ページファイルを削除する
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
 * @param array $data
 * @return string
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
				foreach($categoryPath as $category) {
					$url .= $category['PageCategory']['name'].'/';
				}
			}
		}
		return $url.$data['name'];
	}
/**
 * Baserが管理するタグを追加する
 * @param string $contents
 * @param string $title
 * @return string
 */
	function addBaserPageTag($id,$contents,$title,$description) {
		$tag = '<!-- BaserPageTagBegin -->'."\n";
		$tag .= '<?php $baser->setTitle(\''.$title.'\') ?>'."\n";
		$tag .= '<?php $baser->setDescription(\''.$description.'\') ?>'."\n";
		if($id) {
			$tag .= '<?php $baser->editPage('.$id.') ?>'."\n";
		}
		$tag .= '<!-- BaserPageTagEnd -->'."\n";
		return $tag . $contents;
	}
/**
 * ページ存在チェック
 *
 * @param	string	チェック対象文字列
 * @return	boolean
 * @access	public
 */
	function pageExists($check) {
		if($this->exists()) {
			return true;
		}else {
			$conditions['Page.name'] = $this->data['Page']['name'];
			if(empty($this->data['Page']['page_category_id'])) {
				$conditions['Page.page_category_id'] = NULL;
			}else {
				$conditions['Page.page_category_id'] = $this->data['Page']['page_category_id'];
			}
			if(!$this->find($conditions)) {
				return true;
			}else {
				return !file_exists($this->_getPageFilePath($this->data));
			}
		}
	}
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource($field = null) {

		if(ClassRegistry::isKeySet('SiteConfig')) {
			$SiteConfig = ClassRegistry::getObject('SiteConfig');
		}
		$controlSources['page_category_id'] = $this->PageCategory->getControlSource('parent_id');

		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}

	}
/**
 * 公開チェックを行う
 * @param	string	$url
 * @return	boolean
 * @access	public
 */
	function checkPublish($url) {

		if($this->_unpublishes == -1) {
			$conditions = array('Page.status' => false);
			$pages = $this->find('all',array('fields'=>'url','conditions'=>$conditions,'recursive'=>-1));
			if(!$pages) {
				$this->_unpublishes = array();
				return true;
			}
			$this->_unpublishes = Set::extract('/Page/url', $pages);
		}

		return !in_array($url,$this->_unpublishes);

	}
/**
 * ページファイルを登録する
 * ※ 再帰処理
 *
 * @param	string	$pagePath
 * @param	string	$parentCategoryId
 * @return	array	処理結果 all / success
 * @access	protected
 */
	function entryPageFiles($targetPath,$parentCategoryId = '') {

		$this->Page->saveFile = false;
		$folder = new Folder($targetPath);
		$files = $folder->read(true,true,true);
		$insert = 0;
		$update = 0;
		$all = 0;

		// カテゴリの取得・登録
		$categoryName = basename($targetPath);
		$pageCategoryId = '';
		if($categoryName != 'pages') {
			$categoryId = $this->PageCategory->getIdByPath($targetPath);
			if($categoryId) {
				$pageCategoryId = $categoryId;
			}else {
				$pageCategory['PageCategory']['parent_id'] = $parentCategoryId;
				$pageCategory['PageCategory']['name'] = $categoryName;
				$pageCategory['PageCategory']['title'] = $categoryName;
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
		foreach($files[1] as $file) {

			if(preg_match('/\.ctp$/is',$file) === false) {
				continue;
			}

			$pageName = basename($file, '.ctp');
			$file = new File($file);
			$contents = $file->read();
			$file->close();
			$file = null;

			// タイトル取得・置換
			$titleReg = '/<\?php\s+?\$baser->setTitle\(\'(.*?)\'\)\s+?\?>/is';
			if(preg_match($titleReg,$contents,$matches)) {
				$title = trim($matches[1]);
				$contents = preg_replace($titleReg,'',$contents);
			}else {
				$title = Inflector::camelize($pageName);
			}

			// 説明文取得・置換
			$descriptionReg = '/<\?php\s+?\$baser->setDescription\(\'(.*?)\'\)\s+?\?>/is';
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
				$conditions['Page.page_category_id'] = '';
			}
			$page = $this->find($conditions);
			if($page) {
				$page['Page']['title'] = $title;
				$page['Page']['description'] = $description;
				$page['Page']['contents'] = $contents;
				$this->set($page);
				if($this->save()) {
					$update++;
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
}
?>