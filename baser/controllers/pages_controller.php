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

		$noCache = array();
		if((empty($this->params['prefix']) || !in_array($this->params['prefix'],$noCache)) && !isset($_SESSION['Auth']['User'])) {
			$this->helpers[] = 'Cache';
			$this->cacheAction = '1 month'; // ページ更新時にキャッシュは削除するのでとりあえず1ヶ月で固定
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
				//$this->Session->del('Filter.Page.page_category_id');
				$this->Session->write('Filter.Page.page_category_id','pconly');
				$this->data['Page']['page_category_id'] = 'pconly';
			}
			if($this->Session->check('Filter.Page.status')) {
				$this->data['Page']['status'] = $this->Session->read('Filter.Page.status');
			}else {
				$this->Session->del('Filter.Page.status');
			}
		}

		// 表示件数設定
		if(!empty($this->params['named']['num'])){
			$this->Session->write('Filter.Page.num', $this->params['named']['num']);
		}else{
			if(!$this->Session->check('Filter.Page.num')){
				$this->passedArgs['num'] = 10;
			}else{
				$this->passedArgs['num'] = $this->Session->read('Filter.Page.num');
			}
		}
		
		// 並び替え処理
		if(!empty($this->params['named']['sortup'])) {
			$this->Page->sortup($this->params['named']['sortup'],array('Page.page_category_id'=>$this->data['Page']['page_category_id']));
		}
		if(!empty($this->params['named']['sortdown'])) {
			$this->Page->sortdown($this->params['named']['sortdown'],array('Page.page_category_id'=>$this->data['Page']['page_category_id']));
		}

		/* 条件を生成 */
		$conditions = array();
		// ページカテゴリ
		// 子カテゴリも検索条件に入れる
		$pageCategoryIds = array($this->data['Page']['page_category_id']);
		if(!empty($this->data['Page']['page_category_id'])) {
			if($this->data['Page']['page_category_id'] == 'pconly') {
				$conditions['or'] = array('not'=>array('Page.page_category_id'=>$this->PageCategory->getMobileCategoryIds()),
											array('Page.page_category_id'=>null));
			}elseif($this->data['Page']['page_category_id'] != 'noncat') {
				$children = $this->PageCategory->children($this->data['Page']['page_category_id']);
				if($children) {
					foreach($children as $child) {
						$pageCategoryIds[] = $child['PageCategory']['id'];
					}
				}
				$conditions['Page.page_category_id'] = $pageCategoryIds;
			}else {
				$conditions['Page.page_category_id'] = '';
			}
		}
		// ステータス
		if(isset($this->data['Page']['status']) && $this->data['Page']['status'] !== '') {
			$conditions['Page.status'] = $this->data['Page']['status'];
		}

		$this->paginate = array(
				'conditions' => $conditions,
				'fields' => array(),
				'order' =>'Page.sort',
				'limit' => $this->passedArgs['num']
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
			$this->data = $this->Page->getDefaultValue();
		}else {

			/* 登録処理 */
			$this->data['Page']['url'] = $this->Page->getPageUrl($this->data);
			$this->Page->create($this->data);

			if($this->Page->validates()) {
				if($this->Page->save($this->data,false)) {
					$id = $this->Page->getLastInsertId();
					$this->data['Page']['reflect_mobile'] = false;
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
		$this->set('mobileCategoryIds',$this->PageCategory->getMobileCategoryIds());
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
					$this->data['Page']['reflect_mobile'] = false;
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
		$this->set('url',preg_replace('/^\/mobile\//is', '/m/', $this->data['Page']['url']));
		$this->set('mobileExists',$this->Page->mobileExists($this->data));
		$this->set('mobileCategoryIds',$this->PageCategory->getMobileCategoryIds());
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

		$result = $this->Page->entryPageFiles($pagesPath);

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

		$ext = '';
		if(strpos($path[0], '.html') !== false) {
			// .htmlの拡張子がついている場合、$pathが正常に取得できないので取得しなおす
			$params = Router::parse(str_replace('.html','',$path[0]));
			$path = $params['pass'];
			$this->params['pass'] = $path;
			$ext = '.html';
		}

		// モバイルディレクトリへのアクセスは Not Found
		if($path[0]=='mobile'){
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
			$conditions = array('Page.status'=>true,'Page.url'=>$url);
			if(!$this->Page->find($conditions, array('Page.id'), null, -1)) {
				$this->notFound();
			}
		}

		// ナビゲーションを取得
		$categories = array();
		$conditions = array();
		for($i=0;$i<count($path)-1;$i++) {
			$categories[$path[$i]] = '';
			$conditions['or'][] = array('PageCategory.name'=>$path[$i]);
		}
		if($conditions) {
			$this->PageCategory->hasMany['Page']['conditions'] = array('Page.status'=>true);
			$pageCategories = $this->PageCategory->find('all',array('fields'=>array('name','title'),'conditions'=>$conditions));
			foreach($pageCategories as $pageCategory) {
				if(!empty($pageCategory['Page'])) {
					$categoryPageUrl = '';
					foreach($pageCategory['Page'] as $page) {
						if($page['name'] == 'index') {
							$categoryPageUrl = $page['url'];
						}
					}
				}
				if(!$categoryPageUrl) {
					$categories[$pageCategory['PageCategory']['name']] = array('title'=>$pageCategory['PageCategory']['title']);
				}else {
					$categories[$pageCategory['PageCategory']['name']] = array('title'=>$pageCategory['PageCategory']['title'],
							'url'=>$categoryPageUrl);
				}
			}
			foreach ($categories as $category) {
				if(!empty($category['url'])) {
					$this->navis[$category['title']] = $category['url'];
				}else {
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

		if($id) {
			$conditions = array('Page.id' => $id);
			$page = $this->Page->find($conditions);
		}elseif(isset($this->data['Page'])) {
			$page = $this->data;
			$page['Page']['url'] = $this->Page->getPageUrl($page);
		}

		if(!$page) {
			$this->notFound();
		}

		if(preg_match('/^\/mobile\//is', $page['Page']['url'])){
			Configure::write('Mobile.on',true);
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

		if(!Configure::read('Mobile.on')){
			$this->layoutPath = '';
		}else{
			$this->layoutPath = 'mobile';
			$this->helpers[] = 'mobile';
		}
		$this->subDir = '';
		$this->params['prefix'] = '';
		$this->params['admin'] = '';
		$this->params['url']['url'] = preg_replace('/^\//i','',preg_replace('/^\/mobile\//is','/m/',$page['Page']['url']));
		$this->theme = $this->siteConfigs['theme'];
		$this->render('display',null,TMP.'pages_preview.ctp');

	}

}
?>