<?php
/* SVN FILE: $Id$ */
/**
 * グローバルメニューコントローラー
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
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
 * グローバルメニューコントローラー
 *
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
	var $components = array('AuthEx','Cookie','AuthConfigure','RequestHandler');
/**
 * ヘルパ
 *
 * @var array
 * @access public
 */
	var $helpers = array('Time','FormEx');
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
	var $navis = array('システム設定'=>'/admin/site_configs/form',
			'グローバルメニュー管理'=>'/admin/global_menus/index');
/**
 * グローバルメニューの一覧を表示する
 *
 * @return void
 * @access public
 */
	function admin_index() {

		/* セッション処理 */
		if($this->data) {
			$this->Session->write('Filter.GlobalMenu.menu_type',$this->data['GlobalMenu']['menu_type']);
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
		$listDatas = $this->GlobalMenu->findAll($conditions,null,array('sort'));

		$this->set('listDatas',$listDatas);

		// 表示設定
		$this->subMenuElements = array('site_configs','global_menus');
		$this->pageTitle = 'グローバルメニュー一覧';

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
			$menuType = $this->Session->read('Filter.GlobalMenu.menu_type');
			if($menuType){
				$this->data['GlobalMenu']['menu_type'] = $menuType;
			}else{
				$this->data['GlobalMenu']['menu_type'] = 'default';
			}
		}else {

			/* 登録処理 */
			if(!preg_match('/^http/is', $this->data['GlobalMenu']['link']) && !preg_match('/^\//is', $this->data['GlobalMenu']['link'])){
				$this->data['GlobalMenu']['link'] = '/'.$this->data['GlobalMenu']['link'];
			}
			$this->data['GlobalMenu']['no'] = $this->GlobalMenu->getMax('no',array('menu_type'=>$this->data['GlobalMenu']['menu_type']))+1;
			$this->data['GlobalMenu']['sort'] = $this->GlobalMenu->getMax('sort',array('menu_type'=>$this->data['GlobalMenu']['menu_type']))+1;
			$this->GlobalMenu->create($this->data);

			// データを保存
			if($this->GlobalMenu->save()) {
				clearViewCache();
				$message = '新規グローバルメニュー「'.$this->data['GlobalMenu']['name'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->GlobalMenu->saveDbLog($message);
				$this->redirect(array('action'=>'index'));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('site_configs','global_menus');
		$this->pageTitle = '新規グローバルメニュー登録';
		$this->render('form');

	}
/**
 * [ADMIN] 編集処理
 *
 * @param	int ID
 * @return void
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
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
				$message = 'グローバルメニュー「'.$this->data['GlobalMenu']['name'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->GlobalMenu->saveDbLog($message);
				$this->redirect(array('action'=>'index',$id));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('site_configs','global_menus');
		$this->pageTitle = 'グローバルメニュー編集：'.$this->data['GlobalMenu']['name'];
		$this->render('form');

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
			$this->redirect(array('action'=>'admin_index'));
		}

		// メッセージ用にデータを取得
		$post = $this->GlobalMenu->read(null, $id);

		/* 削除処理 */
		if($this->GlobalMenu->del($id)) {
			clearViewCache();
			$message = 'グローバルメニュー「'.$post['GlobalMenu']['name'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->GlobalMenu->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}
/**
 * 並び替えを更新する [AJAX]
 *
 * @access	public
 * @return boolean
 */
	function admin_update_sort () {

		if($this->data){
			$this->data = am($this->data,$this->_checkSession());
			$conditions = $this->_createAdminIndexConditions($this->data);
			if($this->GlobalMenu->changeSort($this->data['Sort']['id'],$this->data['Sort']['offset'],$conditions)){
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
			$this->Session->del('Filter.GlobalMenu.menu_type');
			$data['menu_type'] = 'default';
		}
		if($this->Session->check('Filter.GlobalMenu.status')) {
			$data['status'] = $this->Session->read('Filter.GlobalMenu.status');
		}else {
			$this->Session->del('Filter.GlobalMenu.status');
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
		if(!empty($data['menu_type'])) {
			$conditions['GlobalMenu.menu_type'] = $data['menu_type'];
		}
		if(isset($data['status']) && $data['status'] !== '') {
			$conditions['GlobalMenu.status'] = $data['status'];
		}

		return $conditions;
		
	}
	
}
?>