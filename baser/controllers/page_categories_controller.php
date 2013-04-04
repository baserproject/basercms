<?php
/* SVN FILE: $Id$ */
/**
 * 固定ページカテゴリーコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
	var $helpers = array(BC_TEXT_HELPER, BC_FORM_HELPER, BC_ARRAY_HELPER, 'BcPage');
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
			
		$default = array('PageCategory' => array('type'=>'1'));
		$this->setViewConditions('PageCategory', array('default' => $default));

		$mobileId = $this->PageCategory->getAgentId('mobile');
		$smartphoneId = $this->PageCategory->getAgentId('smartphone');
		
		$ids = array();
		$conditions = array();
		if($this->data['PageCategory']['type'] == '1') {
			$children = am($this->PageCategory->children($mobileId, false, array('PageCategory.id')), $this->PageCategory->children($smartphoneId, false, array('PageCategory.id')));
			if($children) {
				$ids = am($ids, Set::extract('/PageCategory/id', $children));
			}
			$ids = am(array($mobileId, $smartphoneId), $ids);
			$conditions = array('NOT' => array('PageCategory.id' => $ids));
		} elseif($this->data['PageCategory']['type'] == '2') {
			$children = am($this->PageCategory->children($mobileId, false, array('PageCategory.id')));
			if($children) {
				$ids = am($ids, Set::extract('/PageCategory/id', $children));
			}
			if($ids) {
				$conditions = array(array('PageCategory.id' => $ids));
			}
		} elseif($this->data['PageCategory']['type'] == '3') {
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
		
		if($this->RequestHandler->isAjax() || !empty($this->params['url']['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		/* 表示設定 */
		$this->help = 'page_categories_index';
		$this->search = 'page_categories_index';
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = '固定ページカテゴリー一覧';
		
	}
/**
 * [ADMIN] 固定ページカテゴリー情報登録
 *
 * @return void
 * @access public
 */
	function admin_add() {

		if(empty($this->data)) {
			$user = $this->BcAuth->user();
			$this->data = array('PageCategory' => array(
				'contents_navi'		=> false, 
				'page_category_type'=> 1,
				'owner_id'			=> $user['User']['user_group_id'],
				'layout_template'	=> 'default',
				'content_template'	=> 'default'
			));
		} else {

			$data = $this->data;
			
			if(!$data['PageCategory']['parent_id']) {
				switch ($data['PageCategory']['page_category_type']) {
					case 1:
						$data['PageCategory']['parent_id'] = '';
						break;
					case 2:
						$data['PageCategory']['parent_id'] = $this->PageCategory->getAgentId('mobile');
						break;
					case 3:
						$data['PageCategory']['parent_id'] = $this->PageCategory->getAgentId('smartphone');
						break;
				}
			}
			
			unset($data['PageCategory']['page_category_type']);
			
			/* 登録処理 */
			$this->PageCategory->create($data);

			if($this->PageCategory->validates()) {
				if($this->PageCategory->save($data,false)) {
					$message = '固定ページカテゴリー「'.$data['PageCategory']['name'].'」を追加しました。';
					if(ini_get('safe_mode')) {
						$message .= '<br />機能制限のセーフモードで動作しているので、手動で次のフォルダ内に追加したカテゴリと同階層のフォルダを作成し、書込権限を与える必要があります。<br />'.
									WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.'pages'.DS;
					}
					$this->setMessage($message, false, true);
					$this->redirect(array('controller' => 'page_categories', 'action' => 'index'));
				}else {
					$this->setMessage('保存中にエラーが発生しました。', true);
				}
			}else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
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

		if(Configure::read('BcApp.smartphone') && (!isset($this->siteConfigs['linked_pages_smartphone']) || !$this->siteConfigs['linked_pages_smartphone'])) {
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
		if(!$id && empty($this->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if(empty($this->data)) {
			$this->data = $this->PageCategory->read(null, $id);
			$this->data['PageCategory']['page_category_type'] = $this->PageCategory->getType($this->data['PageCategory']['id']);
		}else {

			if(!$this->data['PageCategory']['parent_id']) {
				switch ($this->data['PageCategory']['page_category_type']) {
					case 1:
						$this->data['PageCategory']['parent_id'] = '';
						break;
					case 2:
						$this->data['PageCategory']['parent_id'] = $this->PageCategory->getAgentId('mobile');
						break;
					case 3:
						$this->data['PageCategory']['parent_id'] = $this->PageCategory->getAgentId('smartphone');
						break;
				}
			}
			
			unset($this->data['PageCategory']['page_category_type']);
			
			/* 更新処理 */
			$this->PageCategory->set($this->data);

			if($this->PageCategory->validates()) {
				if($this->PageCategory->save($this->data,false)) {
					$this->setMessage('固定ページカテゴリー「'.$this->data['PageCategory']['name'].'」を更新しました。', false, true);
					$this->redirect(array('action' => 'index'));
				}else {
					$this->setMessage('保存中にエラーが発生しました。', true);
				}
			}else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}

		}

		$indexPage = array();
		if(isset($this->data['Page'])){
			foreach($this->data['Page'] as $page){
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
			'excludeParentId' => $this->data['PageCategory']['id'],
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
		$this->pageTitle = '固定ページカテゴリー情報編集：'.$this->data['PageCategory']['title'];
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
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$page = $this->PageCategory->read(null, $id);

		/* 削除処理 */
		if($this->PageCategory->del($id)) {
			$this->setMessage('固定ページカテゴリー: '.$page['PageCategory']['name'].' を削除しました。', false, true);
		}else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
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
		
		$user = $this->BcAuth->user();
		$allowOwners = array();
		if(isset($user['User']['user_group_id'])) {
			$allowOwners = array('', $user['User']['user_group_id']);
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
/**
 * カテゴリ編集フォームのコントロール用データをJSONで出力
 * 
 * $this->params['url']['type'] としてGETパラメーター受付
 */
	function admin_ajax_control_sources() {
		
		$type = $agent = '';
		if(!empty($this->params['url']['type'])) {
			$type = $this->params['url']['type'];
		}
		
		switch($type) {
			case '1':
				$agent = '';
				break;
			case '2':
				$agent = 'mobile';
				break;
			case '3':
				$agent = 'smartphone';
				break;
			default:
				exit();
		}
		
		App::import('Helper', 'BcPage');
		$BcPage = new BcPageHelper();
		$result = array(
			'layout'	=> $BcPage->getTemplates('layout', $agent),
			'content'	=> $BcPage->getTemplates('content', $agent),
		);
		$this->RequestHandler->setContent('json');
		$this->RequestHandler->respondAs('application/json; charset=UTF-8');
		echo json_encode($result);
		exit();
		
	}
	
}
