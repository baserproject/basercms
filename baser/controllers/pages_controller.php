<?php
/* SVN FILE: $Id$ */
/**
 * ページコントローラー
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
 * @package			baser.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページコントローラー
 *
 * @package       cake
 * @subpackage    cake.baser.controllers
 */
class PagesController extends AppController {
/**
 * コントローラー名
 *
 * @var string
 * @access public
 */
	var $name = 'Pages';
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html','Googlemaps', 'XmlEx', 'TextEx', 'Freeze', 'Ckeditor', 'Page');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('Auth','Cookie','AuthConfigure');
/**
 * モデル
 *
 * @var		array
 * @access	public
 */
	var $uses = array('Page', 'PageCategory');
/**
 * beforeFilter
 *
 * @return	void
 * @access 	public
 */
	function beforeFilter() {

		parent::beforeFilter();

		// 認証設定
		$this->Auth->allow('display','mobile_display');

		// モバイルの場合は、モバイルヘルパーでxhtml+xmlで
		// コンテンツヘッダを出力する必要がある為、キャッシュは利用しない
		$noCache = array('mobile');
		if((empty($this->params['prefix']) || !in_array($this->params['prefix'],$noCache)) && !isset($_SESSION['Auth']['User'])) {
			$this->helpers[] = 'Cache';
			$this->cacheAction = '1 month'; // ページ更新時にキャッシュは削除するのでとりあえず1ヶ月で固定
		}

		// バリデーション用の値をセット
		if(isset($this->siteConfigs['theme'])) {
			$this->Page->PageCategory->validationParams['theme'] = $this->siteConfigs['theme'];
		}

	}
/**
 * [ADMIN] ページリスト
 *
 * @return	void
 * @access 	public
 */
	function admin_index() {

		/* セッション処理 */
		if($this->data) {
			$this->Session->write('Filter.Page.page_category_id',$this->data['Page']['page_category_id']);
			$this->Session->write('Filter.Page.status',$this->data['Page']['status']);
		}else {
			if($this->Session->check('Filter.Page.page_category_id')) {
				$this->data['Page']['page_category_id'] = $this->Session->read('Filter.Page.page_category_id');
			}else {
				$this->Session->del('Filter.Page.page_category_id');
			}
			if($this->Session->check('Filter.Page.status')) {
				$this->data['Page']['status'] = $this->Session->read('Filter.Page.status');
			}else {
				$this->Session->del('Filter.Page.status');
			}
		}

		/* 条件を生成 */
		$conditions = array();
		// テーマ
		$conditions['Page.theme'] = $this->siteConfigs['theme'];
		// ページカテゴリ
		// 子カテゴリも検索条件に入れる
		$pageCategoryIds = array($this->data['Page']['page_category_id']);
		if(!empty($this->data['Page']['page_category_id'])) {
			$children = $this->PageCategory->children($this->data['Page']['page_category_id']);
			if($children) {
				foreach($children as $child) {
					$pageCategoryIds[] = $child['PageCategory']['id'];
				}
			}
			$conditions['Page.page_category_id'] = $pageCategoryIds;
		}
		// ステータス
		if(isset($this->data['Page']['status']) && $this->data['Page']['status'] !== '') {
			$conditions['Page.status'] = $this->data['Page']['status'];
		}

		$this->paginate = array('conditions'=>$conditions,
				'fields'=>array(),
				'order'=>'Page.id',
				'limit'=>10
		);
		$this->set('dbDatas',$this->paginate('Page'));

		/* 表示設定 */
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = 'ページ一覧';

	}
/**
 * [ADMIN] ページ情報登録
 *
 * @return	void
 * @access 	public
 */
	function admin_add() {

		if(empty($this->data)) {
			$this->data = $this->Page->getDefaultValue($this->siteConfigs['theme']);
		}else {

			/* 登録処理 */
			$this->data['Page']['url'] = $this->Page->getPageUrl($this->data);
			$this->Page->create($this->data);

			if($this->Page->validates()) {
				if($this->Page->save($this->data,false)) {
					$id = $this->Page->getLastInsertId();
					$this->Session->setFlash('ページ「'.$this->data['Page']['name'].'」を追加しました。');
					$this->Page->saveDbLog('ページ「'.$this->data['Page']['name'].'」を追加しました。');
					// 編集画面にリダイレクト
					$this->redirect('/admin/pages/edit/'.$id);
				}else {
					$this->Session->setFlash('保存中にエラーが発生しました。');
				}
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = '新規ページ登録';
		$this->render('form');

	}
/**
 * [ADMIN] ページ情報編集
 *
 * @param	int		$id (page_id)
 * @return	void
 * @access 	public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		if(empty($this->data)) {
			$this->data = $this->Page->read(null, $id);
		}else {

			/* 更新処理 */
			$this->data['Page']['url'] = $this->Page->getPageUrl($this->data);
			$this->Page->set($this->data);

			if($this->Page->validates()) {
				if($this->Page->save($this->data,false)) {
					clearViewCache($this->data['Page']['url']);
					$this->Session->setFlash('ページ「'.$this->data['Page']['name'].'」を更新しました。');
					$this->Page->saveDbLog('ページ「'.$this->data['Page']['name'].'」を更新しました。');
					// 一覧にリダイレクトすると記事の再編集時に検索する必要があるので一旦コメントアウト
					//$this->redirect(array('action'=>'admin_index'));
				}else {
					$this->Session->setFlash('保存中にエラーが発生しました。');
				}
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = 'ページ情報編集';
		$this->render('form');

	}
/**
 * [ADMIN] ページ情報削除
 *
 * @param	int		$id (page_id)
 * @return	void
 * @access 	public
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		// メッセージ用にデータを取得
		$page = $this->Page->read(null, $id);

		/* 削除処理 */
		if($this->Page->del($id)) {
			$this->Page->delFile($page);
			$this->Session->setFlash('ページ: '.$page['Page']['name'].' を削除しました。');
			$this->Page->saveDbLog('ページ「'.$page['Page']['name'].'」を削除しました。');
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'admin_index'));

	}
/**
 * [ADMIN] ページファイルを登録する
 * 
 * @return	void
 * @access	public
 */
	function admin_entry_page_files() {

		// 現在のテーマのページファイルのパスを取得
		if($this->siteConfigs['theme']) {
			$pagesPath = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.'pages';
		}else {
			if(is_dir(VIEWS.'pages')) {
				$pagesPath = VIEWS.'pages';
			}else {
				$pagesPath = BASER_VIEWS.'pages';
			}
		}
		$result = $this->_entryPageFiles($pagesPath);

		$message = $result['all'].' ページ中 '.$result['insert'].' ページの新規登録、 '. $result['update'].' ページの更新に成功しました。';
		$this->Session->setFlash($message);
		$this->redirect(array('action'=>'admin_index'));

	}

/**
 * ビューを表示する
 *
 * @param	mixed
 * @return	void
 * @access	public
 */
	function display() {

		$path = func_get_args();
		$url = str_replace('pages','',$path[0]);
		if($url == 'index.html') {
			$url = '/index.html';
		}

		// .htmlの拡張子がついている場合、$pathが正常に取得できないので取得しなおす
		$ext = '';
		if(strpos($path[0], '.html') !== false) {
			$_path = $path[0];
			$params = Router::parse(str_replace('.html','',$_path));
			$path = $params['pass'];
			$ext = '.html';
		}

		$count = count($path);
		if (!$count) {
			$this->redirect('/');
		}
		$page = $subpage = $title = null;

		if (!empty($path[0])) {
			$page = $path[0];
		}
		if (!empty($path[1])) {
			$subpage = $path[1];
		}
		if (!empty($path[$count - 1])) {
			$title = Inflector::humanize($path[$count - 1]);
		}

		// 公開制限を確認
		// TODO モバイルはページ機能を未実装の為制限をかけない→実装する
		if((!Configure::read('Mobile.on') && $ext)) {
			$conditions = array('Page.status'=>true,'Page.url'=>$url);
			if(isset($this->siteConfigs['theme'])) {
				$conditions['Page.theme'] = $this->siteConfigs['theme'];
			}
			if(!$this->Page->find($conditions, array('Page.id'), null, -1)) {
				$this->notFound();
			}
		}

		// ナビゲーションを取得
		$categories = array();
		$conditions = array();
		for($i=0;$i<count($path)-1;$i++){
			$categories[$path[$i]] = '';
			$conditions['or'][] = array('PageCategory.name'=>$path[$i]);
		}
		if($conditions){
			$this->PageCategory->hasMany['Page']['conditions'] = array('Page.status'=>true);
			$pageCategories = $this->PageCategory->find('all',array('fields'=>array('name','title'),'conditions'=>$conditions));
			foreach($pageCategories as $pageCategory){
				if(!empty($pageCategory['Page'])){
					$categoryPageUrl = '';
					foreach($pageCategory['Page'] as $page){
						if($page['name'] == 'index'){
							$categoryPageUrl = $page['url'];
						}
					}
				}
				if(!$categoryPageUrl){
					$categories[$pageCategory['PageCategory']['name']] = array('title'=>$pageCategory['PageCategory']['title']);
				}else{
					$categories[$pageCategory['PageCategory']['name']] = array('title'=>$pageCategory['PageCategory']['title'],
																				'url'=>$categoryPageUrl);
				}
			}
			foreach ($categories as $category){
				if($category['url']){
					$this->navis[$category['title']] = $category['url'];
				}else{
					$this->navis[$category['title']] = '';
				}
			}
		}
		
		$path[count($path)-1] .= $ext;
		$this->subMenuElements = array('default');
		$this->set(compact('page', 'subpage', 'title'));

		$this->render(join('/', $path));

	}
/**
 * [MOBILE] ビューを表示する
 *
 * @param	mixed
 * @return	void
 * @access	public
 */
	function mobile_display() {
		$path = func_get_args();
		call_user_func_array( array( &$this, 'display' ), $path );
	}
/**
 * [ADMIN] WEBページをプレビュー
 *
 * @param	mixed	$id (blog_post_id)
 * @return	void
 * @access 	public
 */
	function admin_preview($id = null) {

		if($id){
			$conditions = array('Page.id' => $id);
			$page = $this->Page->find($conditions);
		}elseif(isset($this->data['Page'])){
			$page = $this->data;
			$page['Page']['url'] = $this->Page->getPageUrl($page);
		}
		
		if(!$page) {
			$this->notFound();
		}

		// 一時ファイルとしてビューを保存
		// タグ中にPHPタグが入る為、ファイルに保存する必要がある
		$contents = $this->Page->addBaserPageTag(null, $page['Page']['contents'], $page['Page']['title'],$page['Page']['description']);
		$path = TMP.'pages_preview.ctp';
		$file = new File($path);
		$file->open('w');
		$file->append($contents);
		$file->close();
		unset($file);
		@chmod($path, 0666);

		$this->layoutPath = '';
		$this->subDir = '';
		$this->params['prefix'] = '';
		$this->params['admin'] = '';
		$this->params['url']['url'] = preg_replace('/^\//i','',$page['Page']['url']);
		$this->theme = $this->siteConfigs['theme'];
		$this->render('display',null,TMP.'pages_preview.ctp');

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
	function _entryPageFiles($pagesPath,$parentCategoryId = '') {

		$pageFolder = new Folder($pagesPath);
		$files = $pageFolder->read(true,true,true);
		$insert = 0;
		$update = 0;
		$all = 0;

		// カテゴリの取得・登録
		$categoryName = basename($pagesPath);
		$pageCategoryId = '';
		if($categoryName != 'pages') {
			$pageCategory = $this->PageCategory->find(array('PageCategory.name'=>$categoryName,
					'PageCategory.theme'=>$this->siteConfigs['theme']));
			if($pageCategory) {
				$pageCategoryId = $pageCategory['PageCategory']['id'];
			}else {
				$pageCategory['PageCategory']['no'] = $this->PageCategory->getMax('no',array('theme'=>$this->siteConfigs['theme']))+1;
				$pageCategory['PageCategory']['parent_id'] = $parentCategoryId;
				$pageCategory['PageCategory']['name'] = $categoryName;
				$pageCategory['PageCategory']['title'] = $categoryName;
				$pageCategory['PageCategory']['sort'] = $this->PageCategory->getMax('sort',array('theme'=>$this->siteConfigs['theme']))+1;
				$pageCategory['PageCategory']['theme'] = $this->siteConfigs['theme'];
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

			if(strpos($file,'.html.ctp') === false) {
				continue;
			}

			$pageName = basename($file, '.html.ctp');
			$file = new File($file);
			$contents = $file->read();
			$file->close();

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
			$conditions['Page.theme'] = $this->siteConfigs['theme'];
			if($pageCategoryId) {
				$conditions['Page.page_category_id'] = $pageCategoryId;
			}

			$page = $this->Page->find($conditions);
			if($page) {
				$page['Page']['title'] = $title;
				$page['Page']['description'] = $description;
				$page['Page']['contents'] = $contents;
				$this->Page->set($page);
				if($this->Page->save()) {
					$update++;
				}
			}else {
				$page = $this->Page->getDefaultValue($this->siteConfigs['theme']);
				$page['Page']['name'] = $pageName;
				$page['Page']['title'] = $title;
				$page['Page']['description'] = $description;
				$page['Page']['contents'] = $contents;
				$page['Page']['page_category_id'] = $pageCategoryId;
				$page['Page']['url'] = $this->Page->getPageUrl($page);
				$this->Page->create($page);
				if($this->Page->save()) {
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
				$result = $this->_entryPageFiles($file,$pageCategoryId);
				$insert += $result['insert'];
				$update += $result['update'];
				$all += $result['all'];
			}
		}

		return array('all'=>$all,'insert'=>$insert,'update'=>$update);

	}
}
?>