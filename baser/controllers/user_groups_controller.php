<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーグループコントローラー
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
 * ユーザーグループコントローラー
 *
 * @package baser.controllers
 */
class UserGroupsController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'UserGroups';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('UserGroup');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * ヘルパ
 *
 * @var array
 * @access public
 */
	var $helpers = array('Time','BcForm');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array('users', 'user_groups');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $crumbs = array(
		array('name' => 'ユーザー管理', 'url' => array('controller' => 'users', 'action' => 'index')),
		array('name' => 'ユーザーグループ管理', 'url' => array('controller' => 'user_groups', 'action' => 'index'))
	);
/**
 * beforeFilter
 * @return void
 * @access public
 */
	function beforeFilter () {
		
		parent::beforeFilter();
		if($this->params['prefix']=='admin'){
			$this->set('usePermission',$this->UserGroup->checkOtherAdmins());
		}
		
	}
/**
 * ユーザーグループの一覧を表示する
 *
 * @return void
 * @access public
 */
	function admin_index() {

		/* データ取得 */
		$this->paginate = array('conditions'=>array(),
				'fields'=>array(),
				'order'=>'UserGroup.id',
				'limit'=>10
		);
		$datas = $this->paginate('UserGroup');

		/* 表示設定 */
		$this->set('datas',$datas);
		$this->pageTitle = 'ユーザーグループ一覧';
		$this->help = 'user_groups_index';
		
	}
/**
 * [ADMIN] 登録処理
 *
 * @return void
 * @access public
 */
	function admin_add() {

		if($this->data) {

			/* 登録処理 */
			if(empty($this->data['UserGroup']['auth_prefix'])) {
				$this->data['UserGroup']['auth_prefix'] = 'admin';
			}
			$this->UserGroup->create($this->data);
			if($this->UserGroup->save()) {
				$message = '新規ユーザーグループ「'.$this->data['UserGroup']['title'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->UserGroup->saveDbLog($message);
				$this->redirect(array('action' => 'index'));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->pageTitle = '新規ユーザーグループ登録';
		$this->help = 'user_groups_form';
		$this->render('form');

	}
/**
 * [ADMIN] 編集処理
 *
 * @param int ID
 * @return void
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'index'));
		}

		if(empty($this->data)) {
			$this->data = $this->UserGroup->read(null, $id);
		}else {

			/* 更新処理 */
			if($this->UserGroup->save($this->data)) {
				$message = 'ユーザーグループ「'.$this->data['UserGroup']['name'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->UserGroup->saveDbLog($message);
				$this->redirect(array('action' => 'index', $id));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->pageTitle = 'ユーザーグループ編集：'.$this->data['UserGroup']['title'];
		$this->help = 'user_groups_form';
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理 (ajax)
 *
 * @param int ID
 * @return void
 * @access public
 */
	function admin_ajax_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			exit();
		}

		// メッセージ用にデータを取得
		$post = $this->UserGroup->read(null, $id);

		/* 削除処理 */
		if($this->UserGroup->del($id)) {
			$message = 'ユーザーグループ「'.$post['UserGroup']['title'].'」 を削除しました。';
			$this->UserGroup->saveDbLog($message);
			exit(true);
		}
		exit();

	}
/**
 * [ADMIN] 削除処理
 *
 * @param int ID
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
		$post = $this->UserGroup->read(null, $id);

		/* 削除処理 */
		if($this->UserGroup->del($id)) {
			$message = 'ユーザーグループ「'.$post['UserGroup']['title'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->UserGroup->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action' => 'index'));

	}
/**
 * [ADMIN] データコピー（AJAX）
 * 
 * @param int $id 
 * @return void
 * @access public
 */
	function admin_ajax_copy($id) {
		
		if(!$id) {
			exit();
		}
		
		$result = $this->UserGroup->copy($id);
		if($result) {
			$this->set('data', $result);
		} else {
			exit();
		}
		
	}

}
?>