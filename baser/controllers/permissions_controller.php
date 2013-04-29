<?php
/* SVN FILE: $Id$ */
/**
 * アクセス制限設定コントローラー
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
 * アクセス制限設定コントローラー
 *
 * @package baser.controllers
 */
class PermissionsController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Permissions';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Permission');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure');
/**
 * ヘルパ
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_TIME_HELPER, BC_FREEZE_HELPER);
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array('site_configs', 'users','user_groups','permissions');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $crumbs = array(
		array('name' => 'ユーザー管理', 'url' => array('controller' => 'users', 'action' => 'index')),
		array('name' => 'ユーザーグループ管理', 'url' => array('controller' => 'user_groups', 'action' => 'index')),
		array('name' => 'アクセス制限設定管理', 'url' => array('controller' => 'permissions', 'action' => 'index'))
	);
/**
 * beforeFilter
 * 
 * @return oid
 * @access public
 */
	function beforeFilter () {
		
		parent::beforeFilter();
		if($this->params['prefix']=='admin'){
			$this->set('usePermission',true);
		}
		
	}
/**
 * アクセス制限設定の一覧を表示する
 *
 * @return void
 * @access public
 */
	function admin_index($userGroupId=null) {

		/* セッション処理 */
		if(!$userGroupId) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(array('controller' => 'user_groups', 'action' => 'index'));
		}
		
		$default = array('named' => array('sortmode' => 0));
		$this->setViewConditions('Permission', array('default' => $default));
		$conditions = $this->_createAdminIndexConditions($userGroupId);
		$datas = $this->Permission->find('all', array('conditions' => $conditions, 'order'=>'Permission.sort'));
		if($datas) {
			foreach($datas as $key => $data) {
				$datas[$key]['Permission']['url'] = preg_replace('/^\/admin\//', '/'.Configure::read('Routing.admin').'/', $data['Permission']['url']);
			}
		}
		$this->set('datas',$datas);
		
		$this->_setAdminIndexViewData();
		
		if($this->RequestHandler->isAjax() || !empty($this->params['url']['ajax'])) {
			$this->render('ajax_index');
			return;
		}
		
		$userGroupName = $this->Permission->UserGroup->field('title', array('UserGroup.id' => $userGroupId));
		$this->pageTitle = '['.$userGroupName.'] アクセス制限設定一覧';
		$this->help = 'permissions_index';
		
	}
/**
 * 一覧の表示用データをセットする
 * 
 * @return void
 * @access protected
 */
	function _setAdminIndexViewData() {
		
		$this->set('sortmode', $this->passedArgs['sortmode']);
		
	}
/**
 * [ADMIN] 登録処理
 *
 * @return void
 * @access public
 */
	function admin_add($userGroupId) {

		$userGroup = $this->Permission->UserGroup->find('first',array('conditions'=>array('UserGroup.id' => $userGroupId),
															'fields' => array('id', 'title'),
															'order'=>'UserGroup.id ASC','recursive'=>-1));
		if(!$this->data) {
			$this->data = $this->Permission->getDefaultValue();
			$this->data['Permission']['user_group_id'] = $userGroupId;
			$permissionAuthPrefix = $this->Permission->UserGroup->getAuthPrefix($userGroupId);
		}else {
			/* 登録処理 */
			if(isset($this->data['Permission']['user_group_id'])){
				$userGroupId = $this->data['Permission']['user_group_id'];
			}else{
				$userGroupId = null;
			}
			$permissionAuthPrefix = $this->Permission->UserGroup->getAuthPrefix($userGroupId);
			$this->data['Permission']['url'] = '/' . $permissionAuthPrefix . '/' . $this->data['Permission']['url'];
			$this->data['Permission']['no'] = $this->Permission->getMax('no',array('user_group_id'=>$userGroupId)) + 1;
			$this->data['Permission']['sort'] = $this->Permission->getMax('sort',array('user_group_id'=>$userGroupId)) + 1;
			$this->Permission->create($this->data);
			if($this->Permission->save()) {
				$this->setMessage('新規アクセス制限設定「'.$this->data['Permission']['name'].'」を追加しました。', false, true);
				$this->redirect(array('action' => 'index', $userGroupId));
			}else {
				$this->data['Permission']['url'] = preg_replace('/^(\/' . $permissionAuthPrefix . '\/|\/)/', '', $this->data['Permission']['url']);
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}

		}

		/* 表示設定 */
		if($permissionAuthPrefix == 'admin') {
			$permissionAuthPrefix = Configure::read('Routing.admin');
		}
		$this->pageTitle = '['.$userGroup['UserGroup']['title'].'] 新規アクセス制限設定登録';
		$this->set('permissionAuthPrefix', $permissionAuthPrefix);
		$this->help = 'permissions_form';
		$this->render('form');

	}
/**
 * [ADMIN] 登録処理
 *
 * @return void
 * @access public
 */
	function admin_ajax_add() {

		if($this->data) {
			$authPrefix = $this->Permission->UserGroup->getAuthPrefix($this->data['Permission']['user_group_id']);
			$this->data['Permission']['url'] = '/' . $authPrefix . '/' . $this->data['Permission']['url'];
			$this->data['Permission']['no'] = $this->Permission->getMax('no',array('user_group_id'=>$this->data['Permission']['user_group_id']))+1;
			$this->data['Permission']['sort'] = $this->Permission->getMax('sort',array('user_group_id'=>$this->data['Permission']['user_group_id']))+1;
			$this->data['Permission']['status'] = true;
			$this->Permission->create($this->data);
			if($this->Permission->save()) {
				$this->Permission->saveDbLog('新規アクセス制限設定「'.$this->data['Permission']['name'].'」を追加しました。');
				exit(true);
			} else {
				$this->ajaxError(500, $this->Page->validationErrors);
			}
		} else {
			$this->ajaxError(500, '無効な処理です。');
		}
		exit();

	}
/**
 * [ADMIN] 編集処理
 *
 * @param int $id
 * @return void
 * @access public
 */
	function admin_edit($userGroupId, $id) {

		/* 除外処理 */
		if(!$userGroupId || !$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		$userGroup = $this->Permission->UserGroup->find('first',array('conditions'=>array('UserGroup.id' => $userGroupId),
															'fields' => array('id', 'title'),
															'order'=>'UserGroup.id ASC','recursive'=>-1));
		
		$permissionAuthPrefix = $this->Permission->getAuthPrefix($id);
		
		if(empty($this->data)) {
			$this->data = $this->Permission->read(null, $id);
			$this->data['Permission']['url'] = preg_replace('/^(\/' . $permissionAuthPrefix . '\/|\/)/', '', $this->data['Permission']['url']);
		}else {

			/* 更新処理 */
			$this->data['Permission']['url'] = '/' . $permissionAuthPrefix . '/' . $this->data['Permission']['url'];
			if($this->Permission->save($this->data)) {
				$this->setMessage('アクセス制限設定「'.$this->data['Permission']['name'].'」を更新しました。', false, true);
				$this->redirect(array('action' => 'index', $userGroupId));
			}else {
				$this->data['Permission']['url'] = preg_replace('/^(\/' . $permissionAuthPrefix . '\/|\/)/', '', $this->data['Permission']['url']);
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}

		}

		/* 表示設定 */
		if($permissionAuthPrefix == 'admin') {
			$permissionAuthPrefix = Configure::read('Routing.admin');
		}
		$this->pageTitle = '['.$userGroup['UserGroup']['title'].'] アクセス制限設定編集：'.$this->data['Permission']['name'];
		$this->set('permissionAuthPrefix', $permissionAuthPrefix);
		$this->help = 'permissions_form';
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理　(ajax)
 *
 * @param int $id
 * @return void
 * @access public
 */
	function _batch_del($ids) {
		if($ids) {
			foreach($ids as $id) {
				
				// メッセージ用にデータを取得
				$post = $this->Permission->read(null, $id);
				/* 削除処理 */
				if($this->Permission->del($id)) {
					$message = 'アクセス制限設定「'.$post['Permission']['name'].'」 を削除しました。';
				
				}
			}
		}
		return true;
	}
/**
 * [ADMIN] 削除処理　(ajax)
 *
 * @param int $id
 * @return void
 * @access public
 */
	function admin_ajax_delete($id = null) {
		/* 除外処理 */
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// メッセージ用にデータを取得
		$post = $this->Permission->read(null, $id);

		/* 削除処理 */
		if($this->Permission->del($id)) {
			$message = 'アクセス制限設定「'.$post['Permission']['name'].'」 を削除しました。';
			exit(true);
		}
		exit();
	}
	/**
 * [ADMIN] 削除処理
 *
 * @param int $id
 * @return void
 * @access public
 */
	function admin_delete($userGroupId, $id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$post = $this->Permission->read(null, $id);

		/* 削除処理 */
		if($this->Permission->del($id)) {
			$this->setMessage('アクセス制限設定「'.$post['Permission']['name'].'」 を削除しました。', false, true);
		}else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));

	}
/**
 * 並び替えを更新する [AJAX]
 *
 * @return boolean
 * @access	public
 */
	function admin_ajax_update_sort ($userGroupId) {

		if($this->data){
			$conditions = $this->_createAdminIndexConditions($userGroupId);
			if($this->Permission->changeSort($this->data['Sort']['id'],$this->data['Sort']['offset'],$conditions)){
				echo true;
			} else {
				$this->ajaxError(500, $this->Permission->validationErrors);
			}
		} else {
			$this->ajaxError(500, '無効な処理です。');
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
	function _createAdminIndexConditions($userGroupId){

		/* 条件を生成 */
		$conditions = array();
		if($userGroupId) {
			$conditions['Permission.user_group_id'] = $userGroupId;
		}
		
		return $conditions;

	}
/**
 * [ADMIN] データコピー（AJAX）
 * 
 * @param int $id 
 * @return void
 * @access public
 */
	function admin_ajax_copy($userGroupId, $id) {
		
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		
		$result = $this->Permission->copy($id);
		if($result) {
			$this->setViewConditions('Permission', array('action' => 'admin_index'));
			$result['Permission']['url'] = preg_replace('/^\/admin\//', '/'.Configure::read('Routing.admin').'/', $result['Permission']['url']);
			$sortmode = false;
			if(isset($this->passedArgs['sortmode'])) {
				$sortmode = $this->passedArgs['sortmode'];
			}
			$this->set('sortmode', $sortmode);
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, $this->Permission->validationErrors);
		}
		
	}
/**
 * [ADMIN] 無効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 * @access public
 */
	function admin_ajax_unpublish($id) {
		
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if($this->_changeStatus($id, false)) {
			exit(true);
		} else {
			$this->ajaxError(500, $this->Permission->validationErrors);
		}
		exit();

	}
/**
 * [ADMIN] 有効状態にする（AJAX）
 * 
 * @param string $blogContentId
 * @param string $blogPostId beforeFilterで利用
 * @param string $blogCommentId
 * @return void
 * @access public
 */
	function admin_ajax_publish($id) {
		
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if($this->_changeStatus($id, true)) {
			exit(true);
		} else {
			$this->ajaxError(500, $this->Permission->validationErrors);
		}
		exit();

	}
/**
 * 一括公開
 * 
 * @param array $ids
 * @return boolean
 * @access protected 
 */
	function _batch_publish($ids) {
		
		if($ids) {
			foreach($ids as $id) {
				$this->_changeStatus($id, true);
			}
		}
		return true;
		
	}
/**
 * 一括非公開
 * 
 * @param array $ids
 * @return boolean
 * @access protected 
 */
	function _batch_unpublish($ids) {
		
		if($ids) {
			foreach($ids as $id) {
				$this->_changeStatus($id, false);
			}
		}
		return true;
		
	}
/**
 * ステータスを変更する
 * 
 * @param int $id
 * @param boolean $status
 * @return boolean 
 */
	function _changeStatus($id, $status) {
		
		$statusTexts = array(0 => '無効', 1 => '有効');
		$data = $this->Permission->find('first', array('conditions' => array('Permission.id' => $id), 'recursive' => -1));
		$data['Permission']['status'] = $status;
		$this->Permission->set($data);
		
		if($this->Permission->save()) {
			$statusText = $statusTexts[$status];
			$this->Permission->saveDbLog('アクセス制限設定「'.$data['Permission']['name'].'」 を'.$statusText.'化しました。');
			return true;
		} else {
			return false;
		}
		
	}
	
}
