<?php
/* SVN FILE: $Id$ */
/**
 * ページコントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
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
 * @package cake
 * @subpackage cake.baser.controllers
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
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure', 'EmailEx');
/**
 * モデル
 *
 * @var array
 * @access	public
 */
	var $uses = array('Page', 'PageCategory');
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {

		parent::beforeFilter();

		// 認証設定
		$this->AuthEx->allow('display','mobile_display');

		$noCache = array();
		if((!isset($this->params['prefix']) || $this->params['prefix'] != 'admin') && !isset($_SESSION['Auth']['User'])) {
			$this->helpers[] = 'Cache';
			$this->cacheAction = Configure::read('Baser.cachetime'); // ページ更新時にキャッシュは削除するのでとりあえず1ヶ月で固定
		}

		if(!empty($this->params['admin'])){
			$this->navis = array('ページ管理'=>'/admin/pages/index');
		}
		
		$user = $this->AuthEx->user();
		$userModel = $this->getUserModel();
		$newCatAddable = $this->PageCategory->checkNewCategoryAddable(
				$user[$userModel]['user_group_id'], 
				$this->checkRootEditable()
		);
		$this->set('newCatAddable', $newCatAddable);
		
	}
/**
 * [ADMIN] ページリスト
 *
 * @return void
 * @access public
 */
	function admin_index() {

		/* 画面情報設定 */
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num'], 'sortmode' => 0),
							'Page' => array('page_category_id' => '', 'page_type' => 1));
		$this->setViewConditions('Page', array('default' => $default));
		if($this->Session->check('PagesAdminIndex.named.sortmode')) {
			$sortmode = $this->Session->read('PagesAdminIndex.named.sortmode');
		}else {
			$sortmode = 0;
		}

		// 並び替えモードの場合は、強制的にsortフィールドで並び替える
		if($sortmode) {
			$this->passedArgs['sort'] = 'sort';
			$this->passedArgs['direction'] = 'asc';
		}

		// 検索条件
		$conditions = $this->_createAdminIndexConditions($this->data);

		$this->paginate = array(
				'conditions' => $conditions,
				'fields' => array(),
				'order' =>'Page.sort',
				'limit' => $this->passedArgs['num']
		);

		/* 表示設定 */
		if(!isset($this->data['Page']['page_type'])) {
			$this->data['Page']['page_type'] = 1;
		}
		$pageCategories = array('' => '指定しない', 'noncat' => 'カテゴリなし');
		$_pageCategories = $this->getCategorySource($this->data['Page']['page_type']);
		if($_pageCategories) {
			$pageCategories += $_pageCategories;
		}
		$this->set('pageCategories', $pageCategories);
		$this->set('sortmode', $sortmode);
		$this->set('dbDatas',$this->paginate('Page'));
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = 'ページ一覧';

	}
/**
 * [ADMIN] ページ情報登録
 *
 * @return void
 * @access public
 */
	function admin_add() {

		if(empty($this->data)) {
			$this->data = $this->Page->getDefaultValue();
			$this->data['Page']['page_type'] = 1;
		}else {

			/* 登録処理 */
			$this->data['Page']['url'] = $this->Page->getPageUrl($this->data);
			$this->Page->create($this->data);
			if($this->data['Page']['page_type'] == 2 && !$this->data['Page']['page_category_id']) {
				$this->data['Page']['page_category_id'] = 1;
			}
			if($this->Page->validates()) {
				
				if($this->Page->save($this->data,false)) {
					
					// キャッシュを削除する
					if($this->Page->allowedPublish($this->data['Page']['status'], $this->data['Page']['publish_begin'], $this->data['Page']['publish_end'])) {
						clearViewCache();
					}
					
					// 完了メッセージ
					$message = 'ページ「'.$this->data['Page']['name'].'」を追加しました。';
					$this->Session->setFlash($message);
					$this->Page->saveDbLog($message);
					
					// afterPageAdd
					$this->executeHook('afterPageAdd');
					
					// 編集画面にリダイレクト
					$id = $this->Page->getInsertID();
					$this->redirect('/admin/pages/edit/'.$id);
					
				}else {
					
					$this->Session->setFlash('保存中にエラーが発生しました。');
					
				}
				
			}else {
				
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
				
			}

		}

		/* 表示設定 */
		switch ($this->data['Page']['page_type']) {
			case 1:
				$categories = $this->getCategorySource(1, array('empty' => '指定しない', 'own' => true));
				break;
			case 2:
				$categories = $this->getCategorySource(2, array('empty' => '指定しない', 'own' => true));
				break;
		}
		$this->set('categories', $categories);
		$this->set('editable', true);
		$this->set('previewId', 'add_'.mt_rand(0, 99999999));
		$this->set('reflectMobile', Configure::read('Baser.mobile'));
		$this->set('users', $this->Page->getControlSource('user_id'));
		$this->set('ckEditorOptions1', array('useDraft' => true, 'draftField' => 'draft', 'disableDraft' => true));
		$this->subMenuElements = array('pages','page_categories');
		$this->set('rootMobileId', $this->PageCategory->getMobileId());
		$this->pageTitle = '新規ページ登録';
		$this->render('form');

	}
/**
 * [ADMIN] ページ情報編集
 *
 * @param int $id (page_id)
 * @return void
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		if(empty($this->data)) {
			$this->data = $this->Page->read(null, $id);
			$this->data['Page']['contents_tmp'] = $this->data['Page']['contents'];
			$mobileIds = $this->PageCategory->getMobileCategoryIds();
			if(!in_array($this->data['Page']['page_category_id'], $mobileIds)) {
				$this->data['Page']['page_type'] = 1;
			} else {
				$this->data['Page']['page_type'] = 2;
			}
		}else {

			$before = $this->Page->read(null, $id);
			
			/* 更新処理 */
			if($this->data['Page']['page_type'] == 2 && !$this->data['Page']['page_category_id']) {
				$this->data['Page']['page_category_id'] = $this->PageCategory->getMobileId();
			}
			$this->data['Page']['url'] = $this->Page->getPageUrl($this->data);
			$this->Page->set($this->data);

			if($this->Page->validates()) {

				if($this->Page->save($this->data,false)) {
					
					// タイトル、URL、公開状態が更新された場合、全てビューキャッシュを削除する
					$beforeStatus = $this->Page->allowedPublish($before['Page']['status'], $before['Page']['publish_begin'], $before['Page']['publish_end']);
					$afterStatus = $this->Page->allowedPublish($this->data['Page']['status'], $this->data['Page']['publish_begin'], $this->data['Page']['publish_end']);
					if($beforeStatus != $afterStatus || $before['Page']['title'] != $this->data['Page']['title'] || $before['Page']['url'] != $this->data['Page']['url']) {
						clearViewCache();
					} else {
						clearViewCache($this->data['Page']['url']);
					}
					
					// 完了メッセージ
					$message = 'ページ「'.$this->data['Page']['name'].'」を更新しました。';
					$this->Session->setFlash($message);
					$this->Page->saveDbLog($message);
					
					// afterPageEdit
					$this->executeHook('afterPageEdit');
					
					// 同ページへリダイレクト
					$this->redirect('/admin/pages/edit/'.$id);
					
				}else {
					
					$this->Session->setFlash('保存中にエラーが発生しました。');
					
				}

			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$currentOwnerId = '';
		$currentPageCategoryId = '';
		if(!empty($this->data['PageCategory']['id'])) {
			$currentPageCategoryId = $this->data['PageCategory']['id'];
		}
		switch ($this->data['Page']['page_type']) {
			case 1:
				$categories = $this->getCategorySource(1, array(
					'currentOwnerId'		=> $currentOwnerId,
					'currentPageCategoryId'	=> $currentPageCategoryId,
					'own'			=> true,
					'empty'			=> '指定しない'
				));
				break;
			case 2:
				$categories = $this->getCategorySource(2, array(
					'currentOwnerId'		=> $currentOwnerId,
					'currentPageCategoryId'	=> $currentPageCategoryId,
					'own'			=> true,
					'empty'			=> '指定しない'
				));
				break;
		}

		$this->set('categories', $categories);
		$this->set('editable', $this->checkCurrentEditable($currentPageCategoryId, $currentOwnerId));
		$this->set('previewId', $this->data['Page']['id']);
		$this->set('reflectMobile', Configure::read('Baser.mobile'));
		$this->set('users', $this->Page->getControlSource('user_id'));
		$this->set('ckEditorOptions1', array('useDraft' => true, 'draftField' => 'draft', 'disableDraft' => false));
		$this->set('url',preg_replace('/^\/mobile\//is', '/m/', preg_replace('/index$/', '', $this->data['Page']['url'])));
		$this->set('mobileExists',$this->Page->mobileExists($this->data));
		$this->set('rootMobileId', $this->PageCategory->getMobileId());
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = 'ページ情報編集';
		$this->render('form');

	}
/**
 * [ADMIN] ページ情報削除
 *
 * @param int $id (page_id)
 * @return void
 * @access public
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
			
			// ページテンプレートを削除
			$this->Page->delFile($page);
			
			// 公開状態だった場合、サイトマップのキャッシュを削除
			// 公開期間のチェックは行わず確実に削除
			if($page['Page']['status']) {
				clearViewCache();
			}
			
			// 完了メッセージ
			$message = 'ページ: '.$page['Page']['name'].' を削除しました。';
			$this->Session->setFlash($message);
			$this->Page->saveDbLog($message);
			
		}else {
			
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
			
		}

		$this->redirect(array('action'=>'admin_index'));

	}
/**
 * [ADMIN] ページファイルを登録する
 *
 * @return void
 * @access public
 */
	function admin_entry_page_files() {

		// 現在のテーマのページファイルのパスを取得
		$pagesPath = getViewPath().'pages';
		$result = $this->Page->entryPageFiles($pagesPath);

		$message = $result['all'].' ページ中 '.$result['insert'].' ページの新規登録、 '. $result['update'].' ページの更新に成功しました。';
		$this->Session->setFlash($message);
		$this->redirect(array('action'=>'admin_index'));

	}
/**
 * [ADMIN] ページファイルを登録する
 *
 * @return void
 * @access public
 */
	function admin_write_page_files() {

		if($this->Page->createAllPageTemplate()){
			$this->Session->setFlash('ページテンプレートの書き出しに成功しました。');
		} else {
			$this->Session->setFlash('ページテンプレートの書き出しに失敗しました。<br />表示できないページはページ管理より更新処理を行ってください。');
		}
		clearViewCache();
		$this->redirect(array('action'=>'admin_index'));

	}
/**
 * ビューを表示する
 *
 * @param mixed
 * @return void
 * @access public
 */
	function display() {

		$path = func_get_args();

		$ext = '';
		if(preg_match('/^pages/', $path[0])) {
			// .htmlの拡張子がついている場合、$pathが正常に取得できないので取得しなおす
			// 1.5.9 以前との互換性の為残しておく
			$url = str_replace('pages','',$path[0]);
			if($url == 'index.html') {
				$url = '/index.html';
			}
			$params = Router::parse(str_replace('.html','',$path[0]));
			$path = $params['pass'];
			$this->params['pass'] = $path;
			$ext = '.html';
		} else {
			$url = '/'.implode('/', $path);
		}

		// モバイルディレクトリへのアクセスは Not Found
		if(isset($path[0]) && $path[0]=='mobile'){
			$this->notFound();
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
		// 1.5.10 で、拡張子なしを標準に変更
		// 拡張子なしの場合は、route.phpで認証がかかる為、ここでは処理を行わない
		// 1.5.9 以前との互換性の為残しておく
		if(($ext)) {
			if(!$this->Page->checkPublish($url)) {
				return $this->notFound();
			}
		}

		// ナビゲーションを取得
		$this->navis = $this->_getNavi($url);

		$path[count($path)-1] .= $ext;
		$this->subMenuElements = array('default');
		$this->set(compact('page', 'subpage', 'title'));
		$this->render(join('/', $path));

	}
/**
 * パンくずナビ用の配列を取得する
 *
 * @param string	$url
 * @return array
 * @access protected
 */
	function _getNavi($url) {

		if(Configure::read('AgentPrefix.on')) {
			$url = '/'.Configure::read('AgentPrefix.currentPrefix').$url;
		}
		
		// 直属のカテゴリIDを取得
		$pageCategoryId = $this->Page->field('page_category_id', array('Page.url' => $url));
		
		// 関連カテゴリを取得（関連ページも同時に取得）
		$pageCategorires = $this->Page->PageCategory->getPath($pageCategoryId, array('PageCategory.name', 'PageCategory.title'), 1);
		
		$navis = array();
		if($pageCategorires) {
			// index ページの有無によりリンクを判別
			foreach($pageCategorires as $pageCategory) {
				if(!empty($pageCategory['Page'])) {
					$categoryUrl = '';
					foreach($pageCategory['Page'] as $page) {
						if($page['name'] == 'index') {
							$categoryUrl = $page['url'];
							break;
						}
					}
					if($categoryUrl) {
						$navis[$pageCategory['PageCategory']['title']] = $categoryUrl;
					} else {
						$navis[$pageCategory['PageCategory']['title']] = '';
					}
				}
			}
		}

		return $navis;
		
	}
/**
 * [MOBILE] ビューを表示する
 *
 * @param mixed
 * @return void
 * @access public
 */
	function mobile_display() {
		
		$path = func_get_args();
		call_user_func_array( array( &$this, 'display' ), $path );
		
	}
/**
 * [ADMIN] WEBページをプレビュー
 *
 * @param mixed	$id (blog_post_id)
 * @return void
 * @access public
 */
	function admin_create_preview($id) {

		if(isset($this->data['Page'])) {
			$page = $this->data;
			if(empty($page['Page']['page_category_id']) && $page['Page']['page_type'] == 2) {
				$page['Page']['page_category_id'] = $this->Page->PageCategory->getMobileId();
			}
			$page['Page']['url'] = $this->Page->getPageUrl($page);
		} else {
			$conditions = array('Page.id' => $id);
			$page = $this->Page->find($conditions);
		}

		if(!$page) {
			echo false;
			exit();
		}

		Cache::write('page_preview_'.$id, $page);

		if(preg_match('/^\/'.Configure::read('AgentPrefix.mobile.prefix').'\//is', $page['Page']['url'])){
			Configure::write('AgentPrefix.on',true);
			Configure::write('Agent.currentAgent', 'mobile');
			Configure::write('AgentPrefix.currentPrefix', Configure::read('AgentPrefix.mobile.prefix'));
			Configure::write('AgentPrefix.currentAlias', Configure::read('AgentPrefix.mobile.alias'));
		}

		// 一時ファイルとしてビューを保存
		// タグ中にPHPタグが入る為、ファイルに保存する必要がある
		$contents = $this->Page->addBaserPageTag(null, $page['Page']['contents'], $page['Page']['title'],$page['Page']['description']);
		$path = TMP.'pages_preview_'.$id.'.ctp';
		$file = new File($path);
		$file->open('w');
		$file->append($contents);
		$file->close();
		unset($file);
		@chmod($path, 0666);
		echo true;
		exit();

	}
/**
 * プレビューを表示する
 *
 * @return void
 * @access public
 */
	function admin_preview($id){

		$page = Cache::read('page_preview_'.$id);

		if(preg_match('/^\/'.Configure::read('AgentPrefix.mobile.prefix').'\//is', $page['Page']['url'])){
			Configure::write('AgentPrefix.on',true);
			Configure::write('AgentPrefix.currentAgent', 'mobile');
			Configure::write('AgentPrefix.currentPrefix', Configure::read('AgentPrefix.mobile.prefix'));
			Configure::write('AgentPrefix.currentAlias', Configure::read('AgentPrefix.mobile.alias'));
			$this->layoutPath = Configure::read('AgentPrefix.mobile.prefix');
			if(Configure::read('AgentPrefix.currentAgent') == 'mobile') {
				$this->helpers[] = 'Mobile';
			}
		} else {
			$this->layoutPath = '';
		}
		$this->subDir = '';
		$this->params['prefix'] = '';
		$this->params['admin'] = '';
		$this->params['url']['url'] = preg_replace('/^\//i','',preg_replace('/^\/mobile\//is','/m/',$page['Page']['url']));
		$this->navis = $this->_getNavi($this->params['url']['url']);
		$this->theme = $this->siteConfigs['theme'];
		$this->render('display',null,TMP.'pages_preview_'.$id.'.ctp');
		@unlink(TMP.'pages_preview_'.$id.'.ctp');
		Cache::delete('page_preview_'.$id);

	}
/**
 * 並び替えを更新する [AJAX]
 *
 * @access public
 * @return boolean
 */
	function admin_update_sort () {

		if($this->data){
			$this->setViewConditions('Page', array('action' => 'admin_index'));
			$conditions = $this->_createAdminIndexConditions($this->data);
			$this->Page->fileSave = false;
			$this->Page->contentSaving = false;
			if($this->Page->changeSort($this->data['Sort']['id'],$this->data['Sort']['offset'],$conditions)){
				clearViewCache();
				clearDataCache();
				echo true;
			}else{
				echo false;
			}
		}else{
			echo false;
		}
		exit();

	}
/**
 * 管理画面ページ一覧の検索条件を取得する
 *
 * @param array $data
 * @return string
 * @access protected
 */
	function _createAdminIndexConditions($data){

		/* 条件を生成 */
		$conditions = array();
		// ページカテゴリ

		$pageCategoryId = $data['Page']['page_category_id'];
		
		$name = '';
		$pageType = 1;
		if(isset($data['Page']['name'])) {
			$name = $data['Page']['name'];
		}
		if(isset($data['Page']['page_type'])) {
			$pageType = $data['Page']['page_type'];
		}
		
		unset($data['_Token']);
		unset($data['Page']['name']);
		unset($data['Page']['page_category_id']);
		unset($data['Sort']);
		unset($data['Page']['open']);
		unset($data['Page']['page_type']);
		
		if($pageType == 1 && !$pageCategoryId) {
			$pageCategoryId = 'pconly';
		}
		if($pageType == 2 && !$pageCategoryId) {
			$pageCategoryId = $this->PageCategory->getMobileId();
		}

		// 条件指定のないフィールドを解除
		foreach($data['Page'] as $key => $value) {
			if($value === '') {
				unset($data['Page'][$key]);
			}
		}

		if($data['Page']) {
			$conditions = $this->postConditions($data);
		}

		if(isset($data['Page'])){
			$data = $data['Page'];
		}

		// ページカテゴリ
		if(!empty($pageCategoryId)) {

			if($pageCategoryId == 'pconly') {

				// PCのみ
				$mobileCategoryIds = $this->PageCategory->getMobileCategoryIds();
				if($mobileCategoryIds) {
					$conditions['or'] = array('not'=>array('Page.page_category_id' => $mobileCategoryIds),
												array('Page.page_category_id'=>null));
				} else {
					$conditions['or'] = array(array('Page.page_category_id'=>null));
				}

			}elseif($pageCategoryId != 'noncat') {

				// カテゴリ指定
				// 子カテゴリも検索条件に入れる
				$pageCategoryIds = array($pageCategoryId);
				$children = $this->PageCategory->children($pageCategoryId);
				if($children) {
					foreach($children as $child) {
						$pageCategoryIds[] = $child['PageCategory']['id'];
					}
				}
				$conditions['Page.page_category_id'] = $pageCategoryIds;

			}elseif($pageCategoryId == 'noncat') {

				//カテゴリなし
				if($pageType == 1) {
					$conditions['or'] = array(array('Page.page_category_id' => ''),array('Page.page_category_id'=>NULL));
				} elseif($pageType == 2) {
					$conditions['Page.page_category_id'] = $this->PageCategory->getMobileId();
				}

			}

		} else {
			if(!Configure::read('Baser.mobile')) {
				$conditions['or'] = array(
					array('Page.page_category_id' => ''),
					array('Page.page_category_id' => NULL),
					array('Page.page_category_id <>' => $this->PageCategory->getMobileId())
				);
			}
		}

		if($name) {
			$conditions['and']['or'] = array(
				'Page.name LIKE' => '%'.$name.'%',
				'Page.title LIKE' => '%'.$name.'%'
			);
		}
		
		return $conditions;

	}
/**
 * PC用のカテゴリIDを元にモバイルページが作成する権限があるかチェックする
 * 
 * @param int $type
 * @param int $id
 * @return boolean
 * @access public
 */
	function admin_check_mobile_page_addable($type, $id) {
		
		$user = $this->AuthEx->user();
		$userModel = $this->getUserModel();
		$userGroupId = $user[$userModel]['user_group_id'];
		$result = false;
		while(true) {
			$mobileId = $this->PageCategory->getMobileId($id);
			if($mobileId) {
				if($mobileId == 1) {
					$ownerId = $this->siteConfigs['root_owner_id'];
				} else {
					$pageCategory = $this->PageCategory->find('first', array(
						'conditions'=> array('PageCategory.id' => $mobileId),
						'field'		=> array('owner_id')
					));
					$ownerId = $pageCategory['PageCategory']['owner_id'];
				}
				if($ownerId) {
					if($userGroupId == $ownerId) {
						$result = true;
					} else {
						$result = false;
					}
				} else {
					$result = true;
				}
				break;
			}
			$pageCategory = $this->PageCategory->find('first', array(
				'conditions'=> array('PageCategory.id' => $id),
				'field'		=> array('parent_id')
			));
			
			$id = $pageCategory['PageCategory']['parent_id'];
			
		}
		
		if($result) {
			echo 1;
		}
		exit();
		
	}
/**
 * [AJAX] カテゴリリスト用のデータを取得する
 * 
 * @param int $type
 * @param boolean $empty
 * @return array
 * @access public
 */
	function admin_ajax_category_source($type) {
		
		$categorySource = $this->getCategorySource($type, $this->data['Page']);
		$this->set('categorySource', $categorySource);

	}
/**
 * カテゴリリスト用のデータを取得する
 * 
 * @param int $type
 * @param int $options
 * @param boolean $empty
 * @return array
 * @access public
 */
	function getCategorySource($type, $options = array()) {
		
		$editable = true;
		
		if(isset($options['currentPageCategoryId']) && isset($options['currentOwnerId'])) {
			$editable = $this->checkCurrentEditable($options['currentPageCategoryId'], $options['currentOwnerId']);
		}

		switch($type) {
			case '1':
				$excludeParentId = '1';
				break;
			case '2':
				$excludeParentId = '';
				break;
		}

		$_options = array(
			'rootEditable'		=> $this->checkRootEditable(),
			'pageEditable'		=> $editable,
			'mobileRoot'		=> false,
			'excludeParentId'	=> $excludeParentId
		);
		
		if(isset($options['currentPageCategoryId'])) {
			$_options['pageCategoryId'] = $options['currentPageCategoryId'];
		}
		if(isset($options['empty'])) {
			$_options['empty'] = $options['empty'];
		}
		if(!empty($options['own'])) {
			$user = $this->AuthEx->user();
			$userModel = $this->getUserModel();
			$_options['userGroupId'] = $user[$userModel]['user_group_id'];
		}
		
		return $this->Page->getControlSource('page_category_id', $_options);

	}
/**
 * 現在のページが書込可能かチェックする
 * 
 * @param int $pageCategoryId
 * @param int $ownerId
 * @return boolean
 * @access public
 */
	function checkCurrentEditable($pageCategoryId, $ownerId) {
		
		$user = $this->AuthEx->user();
		$userModel = $this->getUserModel();
		$editable = false;

		if(!$pageCategoryId) {
			$currentCatOwner = $this->siteConfigs['root_owner_id'];
		} else {
			$currentCatOwner = $ownerId;
		}
		
		return ($currentCatOwner == $user[$userModel]['user_group_id'] ||
					$user[$userModel]['user_group_id'] == 1 || !$currentCatOwner);

	}
	
}
?>