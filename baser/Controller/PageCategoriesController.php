<?php
/* SVN FILE: $Id$ */
/**
 * 固定ページカテゴリーコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * 固定ページカテゴリーコントローラー
 *
 * @package cake
 * @subpackage cake.baser.controllers
 */
class PageCategoriesController extends AppController {
/**
 * Controller name
 *
 * @var string
 * @access public
 */
	var $name = 'PageCategories';
/**
 * Default helper
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_TEXT_HELPER, BC_FORM_HELPER, BC_ARRAY_HELPER);
/**
 * This controller does not use a model
 *
 * @var array
 * @access public
 */
	var $uses = array('PageCategory');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure');
/**
 * パンくず
 * @var array
 * @access	public
 */
	var $crumbs = array(array('name' => '固定ページ管理', 'url' => array('controller' => 'pages', 'action' => 'index')));
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {
		
		parent::beforeFilter();
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		$newCatAddable = $this->PageCategory->checkNewCategoryAddable(
				$user[$userModel]['user_group_id'], 
				$this->checkRootEditable()
		);
		$this->set('newCatAddable', $newCatAddable);
		
	}
/**
 * [ADMIN] 固定ページカテゴリーリスト
 *
 * @return void
 * @access public
 */
	function admin_index() {

		if(!Configure::read('BcApp.mobile')) {
			$this->request->data['PageCategory']['type'] = 'pc';
		}
			
		$default = array('PageCategory' => array('type'=>'pc'));
		$this->setViewConditions('PageCategory', array('default' => $default));

		$mobileId = $this->PageCategory->getAgentId('mobile');
		$smartphoneId = $this->PageCategory->getAgentId('smartphone');
		
		$ids = array();
		$conditions = array();
		if($this->request->data['PageCategory']['type'] == 'pc' || empty($this->request->data['PageCategory']['type'])) {
			$children = am($this->PageCategory->children($mobileId, false, array('PageCategory.id')), $this->PageCategory->children($smartphoneId, false, array('PageCategory.id')));
			if($children) {
				$ids = am($ids, Set::extract('/PageCategory/id', $children));
			}
			$ids = am(array($mobileId, $smartphoneId), $ids);
			$conditions = array('NOT' => array('PageCategory.id' => $ids));
		} elseif($this->request->data['PageCategory']['type'] == 'mobile') {
			$children = am($this->PageCategory->children($mobileId, false, array('PageCategory.id')));
			if($children) {
				$ids = am($ids, Set::extract('/PageCategory/id', $children));
			}
			if($ids) {
				$conditions = array(array('PageCategory.id' => $ids));
			}
		} elseif($this->request->data['PageCategory']['type'] == 'smartphone') {
			$children = am($this->PageCategory->children($smartphoneId, false, array('PageCategory.id')));
			if($children) {
				$ids = am($ids, Set::extract('/PageCategory/id', $children));
			}
			if($ids) {
				$conditions = array(array('PageCategory.id' => $ids));
			}
		}
		
		$datas = array();
		if($conditions) {
			$_dbDatas = $this->PageCategory->generatetreelist($conditions);
			$datas = array();
			foreach($_dbDatas as $key => $dbData) {
				$category = $this->PageCategory->find('first', array('conditions' => array('PageCategory.id'=>$key), 'recursive' => -1));
				if(preg_match("/^([_]+)/i",$dbData,$matches)) {
					$prefix = str_replace('_','&nbsp&nbsp&nbsp',$matches[1]);
					$category['PageCategory']['title'] = $prefix.'└'.$category['PageCategory']['title'];
					$category['PageCategory']['depth'] = strlen($matches[1]);
				} else {
					$category['PageCategory']['depth'] = 0;
				}
				$datas[] = $category;
			}
		}
		
		$this->_setAdminIndexViewData();
		$this->set('datas', $datas);
		
		if($this->RequestHandler->isAjax() || !empty($this->request->params['url']['ajax'])) {
			$this->render('ajax_index');
			return;
		}
		
		$pageType = array();
		if(Configure::read('BcApp.mobile') && (!isset($this->siteConfigs['linked_pages_mobile']) || $this->siteConfigs['linked_pages_mobile'])=='0') {
			$linkedPagesMobile = true;
		} else {
			$linkedPagesMobile = false;
		}
		if(Configure::read('BcApp.smartphone') && (!isset($this->siteConfigs['linked_pages_smartphone']) || $this->siteConfigs['linked_pages_smartphone'])=='0') {
			$linkedPagesSmartPhone = true;
		} else {
			$linkedPagesSmartPhone = false;
		}
		if($linkedPagesMobile || $linkedPagesSmartPhone) {
			$pageType = array('pc' => 'PC');	
		}
		if($linkedPagesMobile) {
			$pageType['mobile'] = 'モバイル';
		}
		if($linkedPagesSmartPhone) {
			$pageType['smartphone'] = 'スマートフォン';
		}
		if($pageType) {
			$this->search = 'page_categories_index';
		}
		$this->help = 'page_categories_index';
		$this->set('pageType', $pageType);

		/* 表示設定 */
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = '固定ページカテゴリー一覧';
		$this->help = 'page_categories_index';

	}
/**
 * [ADMIN] 固定ページカテゴリー情報登録
 *
 * @return void
 * @access public
 */
	function admin_add() {

		if(empty($this->request->data)) {
			$this->request->data = array('PageCategory' => array('contents_navi' => false, 'page_category_type' => 1));
		} else {

			if(!$this->request->data['PageCategory']['parent_id']) {
				switch ($this->request->data['PageCategory']['page_category_type']) {
					case 1:
						$this->request->data['PageCategory']['parent_id'] = '';
						break;
					case 2:
						$this->request->data['PageCategory']['parent_id'] = $this->PageCategory->getAgentId('mobile');
						break;
					case 3:
						$this->request->data['PageCategory']['parent_id'] = $this->PageCategory->getAgentId('smartphone');
						break;
				}
			}
			
			unset($this->request->data['PageCategory']['page_category_type']);
			
			/* 登録処理 */
			$this->PageCategory->create($this->request->data);

			if($this->PageCategory->validates()) {
				if($this->PageCategory->save($this->request->data,false)) {
					$message = '固定ページカテゴリー「'.$this->request->data['PageCategory']['name'].'」を追加しました。';
					if(ini_get('safe_mode')) {
						$message .= '<br />機能制限のセーフモードで動作しているので、手動で次のフォルダ内に追加したカテゴリと同階層のフォルダを作成し、書込権限を与える必要があります。<br />'.
									WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.'pages'.DS;
					}
					$this->Session->setFlash($message);
					$this->PageCategory->saveDbLog('固定ページカテゴリー「'.$this->request->data['PageCategory']['name'].'」を追加しました。');
					$this->redirect(array('controller' => 'page_categories', 'action' => 'index'));
				}else {
					$this->Session->setFlash('保存中にエラーが発生しました。');
				}
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		$parents = $this->PageCategory->getControlSource('parent_id', array(
			'ownerId' => $user[$userModel]['user_group_id']
		));
		if($this->checkRootEditable()) {
			if($parents) {
				$parents = array('' => '指定しない') + $parents;
			} else {
				$parents = array('' => '指定しない');
			}
		} else {
			$mobileId = $this->PageCategory->getAgentId('mobile');
			if(isset($parents[$mobileId])) {
				unset($parents[$mobileId]);
			}
			$smartphoneId = $this->PageCategory->getAgentId('smartphone');
			if(isset($parents[$smartphoneId])) {
				unset($parents[$smartphoneId]);
			}
		}
		
		if(Configure::read('BcApp.mobile') && (!isset($this->siteConfigs['linked_pages_mobile']) || !$this->siteConfigs['linked_pages_mobile'])) {
			$reflectMobile = true;
		} else {
			$reflectMobile = false;
		}
		if(Configure::read('BcApp.smartphone') && (!isset($this->siteConfigs['linked_pages_smartphone']) || $this->siteConfigs['linked_pages_smartphone'])=='0') {
			$reflectSmartphone = true;
		} else {
			$reflectSmartphone = false;
		}
		
		$this->set('reflectMobile', $reflectMobile);
		$this->set('reflectSmartphone', $reflectSmartphone);
		$this->set('parents', $parents);
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = '新規固定ページカテゴリー登録';
		$this->help = 'page_categories_form';
		$this->render('form');

	}
/**
 * [ADMIN] 固定ページカテゴリー情報編集
 *
 * @param int page_id
 * @return void
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->request->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'index'));
		}

		if(empty($this->request->data)) {
			$this->request->data = $this->PageCategory->read(null, $id);
			$this->request->data['PageCategory']['page_category_type'] = $this->PageCategory->getType($this->request->data['PageCategory']['id']);
		}else {

			if(!$this->request->data['PageCategory']['parent_id']) {
				switch ($this->request->data['PageCategory']['page_category_type']) {
					case 1:
						$this->request->data['PageCategory']['parent_id'] = '';
						break;
					case 2:
						$this->request->data['PageCategory']['parent_id'] = $this->PageCategory->getAgentId('mobile');
						break;
					case 3:
						$this->request->data['PageCategory']['parent_id'] = $this->PageCategory->getAgentId('smartphone');
						break;
				}
			}
			
			unset($this->request->data['PageCategory']['page_category_type']);
			
			/* 更新処理 */
			$this->PageCategory->set($this->request->data);

			if($this->PageCategory->validates()) {
				if($this->PageCategory->save($this->request->data,false)) {
					$this->Session->setFlash('固定ページカテゴリー「'.$this->request->data['PageCategory']['name'].'」を更新しました。');
					$this->PageCategory->saveDbLog('固定ページカテゴリー「'.$this->request->data['PageCategory']['name'].'」を更新しました。');
					$this->redirect(array('action' => 'index'));
				}else {
					$this->Session->setFlash('保存中にエラーが発生しました。');
				}
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		$indexPage = array();
		if(isset($this->request->data['Page'])){
			foreach($this->request->data['Page'] as $page){
				if($page['name']=='index'){
					$indexPage['url'] = preg_replace('/^\/mobile\//is', '/m/', $page['url']);
					$indexPage['status'] = $page['status'];
					break;
				}
			}
		}
		$this->set('indexPage',$indexPage);

		/* 表示設定 */
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		$mobileId = $this->PageCategory->getAgentId();
		$parents = $this->PageCategory->getControlSource('parent_id', array(
			'excludeParentId' => $this->request->data['PageCategory']['id'],
			'ownerId' => $user[$userModel]['user_group_id']
		));
		if($this->checkRootEditable()) {
			if($parents) {
				$parents = array('' => '指定しない') + $parents;
			} else {
				$parents = array('' => '指定しない');
			}
		} elseif(isset($parents[$mobileId])) {
			unset($parents[$mobileId]);
		}
		
		if(Configure::read('BcApp.mobile') && (!isset($this->siteConfigs['linked_pages_mobile']) || !$this->siteConfigs['linked_pages_mobile'])) {
			$reflectMobile = true;
		} else {
			$reflectMobile = false;
		}
		if(Configure::read('BcApp.smartphone') && (!isset($this->siteConfigs['linked_pages_smartphone']) || $this->siteConfigs['linked_pages_smartphone'])=='0') {
			$reflectSmartphone = true;
		} else {
			$reflectSmartphone = false;
		}
		
		$this->set('reflectMobile', $reflectMobile);
		$this->set('reflectSmartphone', $reflectSmartphone);
		$this->set('parents', $parents);
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = '固定ページカテゴリー情報編集：'.$this->request->data['PageCategory']['title'];
		$this->help = 'page_categories_form';
		$this->render('form');

	}
/**
 * [ADMIN] 固定ページカテゴリー情報削除
 *
 * @param int page_id
 * @return void
 * @access public
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$page = $this->PageCategory->read(null, $id);

		/* 削除処理 */
		if($this->PageCategory->del($id)) {
			$this->Session->setFlash('固定ページカテゴリー: '.$page['PageCategory']['name'].' を削除しました。');
			$this->PageCategory->saveDbLog('固定ページカテゴリー「'.$page['PageCategory']['name'].'」を削除しました。');
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action' => 'index'));

	}
/**
 * カテゴリの並び替え順を上げる
 * 
 * @param type $id
 * @return void 
 * @access public
 * @deprecated admin_ajax_up に移行
 */
	function admin_up($id) {
		
		$this->PageCategory->moveup($id);
		$this->redirect(array('controller' => 'page_categories', 'action' => 'index', "#" => 'Row'.$id));
		
	}
/**
 * カテゴリの並び替え順を下げる
 * 
 * @param type $id 
 * @return void
 * @access public
 * @deprecated admin_ajax_down に移行
 */
	function admin_down($id) {
		
		$this->PageCategory->movedown($id);
		$this->redirect(array('controller' => 'page_categories', 'action' => 'index', "#" => 'Row'.$id));
		
	}
/**
 * [ADMIN] 固定ページカテゴリー削除
 *
 * @param int $id 固定ページカテゴリーID
 * @return void
 * @access public
 */
	function admin_ajax_delete($id = null) {

		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		
		$data = $this->PageCategory->read(null, $id);

		if($this->PageCategory->del($id)) {			
			$this->PageCategory->saveDbLog('固定ページ: '.$data['PageCategory']['name'].' を削除しました。');
			echo true;
		}
		exit();

	}
/**
 * [ADMIN] 固定ページカテゴリーコピー
 * 
 * @param int $id 固定ページカテゴリID
 * @return void
 * @access public
 */
	function admin_ajax_copy($id = null) {
		
		$result = $this->PageCategory->copy($id);
		if($result) {
			$result['PageCategory']['id'] = $this->PageCategory->getInsertID();
			$this->_setAdminIndexViewData();
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, $this->PageCategory->validationErrors);
		}
		
	}
/**
 * 一覧の表示用データをセットする
 * 
 * @return void
 * @access protected
 */
	function _setAdminIndexViewData() {
		
		$allowOwners = array();
		if(isset($user['user_group_id'])) {
			$allowOwners = array('', $user['user_group_id']);
		}
		$this->set('allowOwners', $allowOwners);
		$this->set('owners', $this->PageCategory->getControlSource('owner_id'));
		
	}
/**
 * 固定ページカテゴリ一括削除
 * 
 * @param array $ids
 * @return boolean
 * @access protected
 */
	function _batch_del($ids) {
		
		if($ids) {
			foreach($ids as $id) {
				$data = $this->PageCategory->read(null, $id);
				if($this->PageCategory->del($id)) {
					$this->PageCategory->saveDbLog('固定ページカテゴリー: '.$data['PageCategory']['name'].' を削除しました。');
				}
			}
		}
		return true;
		
	}
/**
 * [ADMIN] カテゴリの並び替え順を上げる
 * 
 * @param int $id
 * @return void 
 * @access public
 */
	function admin_ajax_up($id) {
		
		if($this->PageCategory->moveup($id)) {
			echo true;
		} else {
			$this->ajaxError(500, '一度リロードしてから再実行してみてください。');
		}
		exit();
		
	}
/**
 * [ADMIN] カテゴリの並び替え順を下げる
 * 
 * @param int $id 
 * @return void
 * @access public
 * @deprecated
 */
	function admin_ajax_down($id) {
		
		if($this->PageCategory->movedown($id)) {
			echo true;
		} else {
			$this->ajaxError(500, '一度リロードしてから再実行してみてください。');
		}
		exit();
		
	}
	
}
