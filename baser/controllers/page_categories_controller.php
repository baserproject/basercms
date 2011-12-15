<?php
/* SVN FILE: $Id$ */
/**
 * ページカテゴリーコントローラー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ページカテゴリーコントローラー
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
	var $helpers = array('TextEx', 'FormEx', 'Array');
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
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * パンくず
 * @var array
 * @access	public
 */
	var $navis = array('ページ管理' => array('controller' => 'pages', 'action' => 'index'));
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {
		
		parent::beforeFilter();
		$user = $this->AuthEx->user();
		$userModel = $this->getUserModel();
		$newCatAddable = $this->PageCategory->checkNewCategoryAddable(
				$user[$userModel]['user_group_id'], 
				$this->checkRootEditable()
		);
		$this->set('newCatAddable', $newCatAddable);
		
	}
/**
 * [ADMIN] ページカテゴリーリスト
 *
 * @return void
 * @access public
 */
	function admin_index() {

		if(!Configure::read('Baser.mobile')) {
			$this->data['PageCategory']['type'] = 'pc';
		}
			
		$default = array('PageCategory' => array('type'=>'pc'));
		$this->setViewConditions('PageCategory', array('default' => $default));

		$mobileId = $this->PageCategory->getAgentId('mobile');
		$smartphoneId = $this->PageCategory->getAgentId('smartphone');
		
		$ids = array();
		$conditions = array();
		if($this->data['PageCategory']['type'] == 'pc' || empty($this->data['PageCategory']['type'])) {
			$children = am($this->PageCategory->children($mobileId, false, array('PageCategory.id')), $this->PageCategory->children($smartphoneId, false, array('PageCategory.id')));
			if($children) {
				$ids = am($ids, Set::extract('/PageCategory/id', $children));
			}
			$ids = am(array($mobileId, $smartphoneId), $ids);
			$conditions = array('NOT' => array('PageCategory.id' => $ids));
		} elseif($this->data['PageCategory']['type'] == 'mobile') {
			$children = am($this->PageCategory->children($mobileId, false, array('PageCategory.id')));
			if($children) {
				$ids = am($ids, Set::extract('/PageCategory/id', $children));
			}
			if($ids) {
				$conditions = array(array('PageCategory.id' => $ids));
			}
		} elseif($this->data['PageCategory']['type'] == 'smartphone') {
			$children = am($this->PageCategory->children($smartphoneId, false, array('PageCategory.id')));
			if($children) {
				$ids = am($ids, Set::extract('/PageCategory/id', $children));
			}
			if($ids) {
				$conditions = array(array('PageCategory.id' => $ids));
			}
		}
		
		$dbDatas = array();
		if($conditions) {
			$_dbDatas = $this->PageCategory->generatetreelist($conditions);
			$dbDatas = array();
			foreach($_dbDatas as $key => $dbData) {
				$category = $this->PageCategory->find(array('PageCategory.id'=>$key));
				if(preg_match("/^([_]+)/i",$dbData,$matches)) {
					$prefix = str_replace('_','&nbsp&nbsp&nbsp',$matches[1]);
					$category['PageCategory']['title'] = $prefix.'└'.$category['PageCategory']['title'];
				}
				$dbDatas[] = $category;
			}
		}
		
		$this->set('owners', $this->PageCategory->getControlSource('owner_id'));
		$this->set('dbDatas',$dbDatas);
		/* 表示設定 */
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = 'ページカテゴリー一覧';

	}
/**
 * [ADMIN] ページカテゴリー情報登録
 *
 * @return void
 * @access public
 */
	function admin_add() {

		if(empty($this->data)) {
			$this->data = array('PageCategory' => array('contents_navi' => false));
		} else {

			/* 登録処理 */
			$this->PageCategory->create($this->data);

			if($this->PageCategory->validates()) {
				if($this->PageCategory->save($this->data,false)) {
					$message = 'ページカテゴリー「'.$this->data['PageCategory']['name'].'」を追加しました。';
					if(ini_get('safe_mode')) {
						$message .= '<br />機能制限のセーフモードで動作しているので、手動で次のフォルダ内に追加したカテゴリと同階層のフォルダを作成し、書込権限を与える必要があります。<br />'.
									WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.'pages'.DS;
					}
					$this->Session->setFlash($message);
					$this->PageCategory->saveDbLog('ページカテゴリー「'.$this->data['PageCategory']['name'].'」を追加しました。');
					$this->redirect(array('controller' => 'page_categories', 'action' => 'index'));
				}else {
					$this->Session->setFlash('保存中にエラーが発生しました。');
				}
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$user = $this->AuthEx->user();
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
		$this->set('parents', $parents);
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = '新規ページカテゴリー登録';
		$this->render('form');

	}
/**
 * [ADMIN] ページカテゴリー情報編集
 *
 * @param int page_id
 * @return void
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'index'));
		}

		if(empty($this->data)) {
			$this->data = $this->PageCategory->read(null, $id);
		}else {

			/* 更新処理 */
			$this->PageCategory->set($this->data);

			if($this->PageCategory->validates()) {
				if($this->PageCategory->save($this->data,false)) {
					$this->Session->setFlash('ページカテゴリー「'.$this->data['PageCategory']['name'].'」を更新しました。');
					$this->PageCategory->saveDbLog('ページカテゴリー「'.$this->data['PageCategory']['name'].'」を更新しました。');
					$this->redirect(array('action' => 'index'));
				}else {
					$this->Session->setFlash('保存中にエラーが発生しました。');
				}
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
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
		$user = $this->AuthEx->user();
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
		$this->set('parents', $parents);
		$this->subMenuElements = array('pages','page_categories');
		$this->pageTitle = 'ページカテゴリー情報編集';
		$this->render('form');

	}
/**
 * [ADMIN] ページカテゴリー情報削除
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
			$this->Session->setFlash('ページカテゴリー: '.$page['PageCategory']['name'].' を削除しました。');
			$this->PageCategory->saveDbLog('ページカテゴリー「'.$page['PageCategory']['name'].'」を削除しました。');
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
 */
	function admin_down($id) {
		
		$this->PageCategory->movedown($id);
		$this->redirect(array('controller' => 'page_categories', 'action' => 'index', "#" => 'Row'.$id));
		
	}
	
}
?>