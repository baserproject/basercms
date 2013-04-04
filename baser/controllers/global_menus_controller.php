<?php
/* SVN FILE: $Id$ */
/**
 * メニューコントローラー
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
 * メニューコントローラー
 *
 * @property GlobalMenu GlobalMenu
 * @property SessionComponent Session
 * @property RequestHandlerComponent RequestHandler
 * @package baser.controllers
 */
class GlobalMenusController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'GlobalMenus';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('GlobalMenu');
/**
 * コンポーネント
 *
 * @var array
 * @accesspublic
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure','RequestHandler');
/**
 * ヘルパ
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_TIME_HELPER, BC_FORM_HELPER);
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $crumbs = array(
		array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')),
		array('name' => 'メニュー管理', 'url' => array('controller' => 'global_menus', 'action' => 'index'))
	);
/**
 * メニューの一覧を表示する
 *
 * @return void
 * @access public
 */
	function admin_index() {

		/* セッション処理 */
		if($this->data) {
			$this->Session->write('Filter.GlobalMenu.status',$this->data['GlobalMenu']['status']);
		}
		if(isset($this->params['named']['sortmode'])){
			$this->Session->write('SortMode.GlobalMenu', $this->params['named']['sortmode']);
		}

		$this->data = am($this->data,$this->_checkSession());
		
		/* 並び替えモード */
		if(!$this->Session->check('SortMode.GlobalMenu')){
			$this->set('sortmode', 0);
		}else{
			$this->set('sortmode', $this->Session->read('SortMode.GlobalMenu'));
		}
		
		$conditions = $this->_createAdminIndexConditions($this->data);
		
		// TODO CSVドライバーが複数の並び替えフィールドを指定できないがtypeを指定したい
		$listDatas = $this->GlobalMenu->find( 'all', array('conditions' => $conditions, 'order' => 'GlobalMenu.sort'));
		
		$this->set('listDatas',$listDatas);

		if($this->RequestHandler->isAjax() || !empty($this->params['url']['ajax'])) {
			$this->render('ajax_index');
			return;
		}
		
		// 表示設定
		$this->subMenuElements = array('site_configs','global_menus');
		$this->pageTitle = 'メニュー一覧';
		$this->search = 'global_menus_index';
		$this->help = 'global_menus_index';
		
	}
/**
 * [ADMIN] 登録処理
 *
 * @return void
 * @access public
 */
	function admin_add() {

		if(!$this->data) {
			$this->data['GlobalMenu']['status'] = 0;
		}else {

			/* 登録処理 */
			if(!preg_match('/^http/is', $this->data['GlobalMenu']['link']) && !preg_match('/^\//is', $this->data['GlobalMenu']['link'])){
				$this->data['GlobalMenu']['link'] = '/'.$this->data['GlobalMenu']['link'];
			}
			$this->data['GlobalMenu']['no'] = $this->GlobalMenu->getMax('no')+1;
			$this->data['GlobalMenu']['sort'] = $this->GlobalMenu->getMax('sort')+1;
			$this->GlobalMenu->create($this->data);

			// データを保存
			if($this->GlobalMenu->save()) {
				clearViewCache();
				$this->setMessage('新規メニュー「'.$this->data['GlobalMenu']['name'].'」を追加しました。', false, true);
				$this->redirect(array('action' => 'index'));
			}else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('site_configs','global_menus');
		$this->pageTitle = '新規メニュー登録';
		$this->help = 'global_menus_form';
		$this->render('form');

	}
/**
 * [ADMIN] 編集処理
 *
 * @param int $id
 * @return void
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if(empty($this->data)) {
			$this->data = $this->GlobalMenu->read(null, $id);
		}else {

			/* 更新処理 */
			if(!preg_match('/^http/is', $this->data['GlobalMenu']['link']) && !preg_match('/^\//is', $this->data['GlobalMenu']['link'])){
				$this->data['GlobalMenu']['link'] = '/'.$this->data['GlobalMenu']['link'];
			}
			$this->GlobalMenu->set($this->data);
			if($this->GlobalMenu->save()) {
				clearViewCache();
				$this->setMessage('メニュー「'.$this->data['GlobalMenu']['name'].'」を更新しました。', false, true);
				$this->redirect(array('action' => 'index', $id));
			}else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('site_configs','global_menus');
		$this->pageTitle = 'メニュー編集：'.$this->data['GlobalMenu']['name'];
		$this->help = 'global_menus_form';
		$this->render('form');

	}
/**
 * [ADMIN] 一括削除
 *
 * @param array $ids
 * @return boolean
 * @access public
 */
	function _batch_del($ids) {
		if($ids) {
			foreach($ids as $id) {
				// メッセージ用にデータを取得
				$post = $this->GlobalMenu->read(null, $id);

				/* 削除処理 */
				if($this->GlobalMenu->del($id)) {
					clearViewCache();
					$message = 'メニュー「'.$post['GlobalMenu']['name'].'」 を削除しました。';
					$this->GlobalMenu->saveDbLog($message);
				}
			}
		}
		return true;
	}
/**
 * [ADMIN] 削除処理 (ajax)
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
		$post = $this->GlobalMenu->read(null, $id);

		/* 削除処理 */
		if($this->GlobalMenu->del($id)) {
			clearViewCache();
			$message = 'メニュー「'.$post['GlobalMenu']['name'].'」 を削除しました。';
			$this->GlobalMenu->saveDbLog($message);
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
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$post = $this->GlobalMenu->read(null, $id);

		/* 削除処理 */
		if($this->GlobalMenu->del($id)) {
			clearViewCache();
			$this->setMessage('メニュー「'.$post['GlobalMenu']['name'].'」 を削除しました。', false, true);
		}else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));

	}
/**
 * 並び替えを更新する [AJAX]
 *
 * @access	public
 * @return boolean
 */
	function admin_ajax_update_sort () {

		if($this->data){
			$this->data = am($this->data,$this->_checkSession());
			$conditions = $this->_createAdminIndexConditions($this->data);
			if($this->GlobalMenu->changeSort($this->data['Sort']['id'],$this->data['Sort']['offset'],$conditions)){
				echo true;
			}else{
				$this->ajaxError(500, '一度リロードしてから再実行してみてください。');
			}
		}else{
			$this->ajaxError(500, '無効な処理です。');
		}
		exit();

	}
/**
 * セッションをチェックする
 *
 * @return array()
 * @access	protected
 */
	function _checkSession(){
		
		$data = array();
		if($this->Session->check('Filter.GlobalMenu.menu_type')) {
			$data['menu_type'] = $this->Session->read('Filter.GlobalMenu.menu_type');
		}else {
			$this->Session->delete('Filter.GlobalMenu.menu_type');
			$data['menu_type'] = 'default';
		}
		if($this->Session->check('Filter.GlobalMenu.status')) {
			$data['status'] = $this->Session->read('Filter.GlobalMenu.status');
		}else {
			$this->Session->delete('Filter.GlobalMenu.status');
		}
		return array('GlobalMenu'=>$data);
		
	}
/**
 * 管理画面ページ一覧の検索条件を取得する
 *
 * @param array $data
 * @return string
 * @access protected
 */
	function _createAdminIndexConditions($data){

		if(isset($data['GlobalMenu'])){
			$data = $data['GlobalMenu'];
		}

		/* 条件を生成 */
		$conditions = array();
		if(isset($data['status']) && $data['status'] !== '') {
			$conditions['GlobalMenu.status'] = $data['status'];
		}

		return $conditions;
		
	}
	
}
