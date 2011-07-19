<?php
/* SVN FILE: $Id$ */
/**
 * アクセス制限設定コントローラー
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
 * アクセス制限設定コントローラー
 *
 * @package			baser.controllers
 */
class PermissionsController extends AppController {
/**
 * クラス名
 *
 * @var     string
 * @access  public
 */
	var $name = 'Permissions';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('Permission');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * ヘルパ
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Time','Freeze');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array('users','user_groups','permissions');
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('ユーザー管理'=>'/admin/users/index',
			'ユーザーグループ管理'=>'/admin/user_group/index',
			'アクセス制限設定管理'=>'/admin/permissions/index');
/**
 * beforeFilter
 * @return	void
 * @access	public
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
 * @return  void
 * @access  public
 */
	function admin_index($userGroupId=null) {

		/* セッション処理 */
		if($userGroupId) {
			$this->Session->write('Filter.Permission.user_group_id',$userGroupId);
			$this->data['Permission']['user_group_id'] = $userGroupId;
		}elseif(!empty($this->params['url']['user_group_id'])) {
			$this->Session->write('Filter.Permission.user_group_id',$this->params['url']['user_group_id']);
			$this->data['Permission']['user_group_id'] = $this->params['url']['user_group_id'];
		}
		if(isset($this->params['named']['sortmode'])){
			$this->Session->write('SortMode.Permission', $this->params['named']['sortmode']);
		}

		$this->data = am($this->data,$this->_checkSession());
		
		/* 並び替えモード */
		if(!$this->Session->check('SortMode.Permission')){
			$this->set('sortmode', 0);
		}else{
			$this->set('sortmode', $this->Session->read('SortMode.Permission'));
		}

		$conditions = $this->_createAdminIndexConditions($this->data);


		/* データ取得 */
		$listDatas = $this->Permission->find('all',array('conditions'=>$conditions, 'order'=>'Permission.sort'));

		/* 表示設定 */
		$this->set('listDatas',$listDatas);
		$this->pageTitle = 'アクセス制限設定一覧';

	}
/**
 * [ADMIN] 登録処理
 *
 * @return  void
 * @access  public
 */
	function admin_add() {

		if(!$this->data) {
			$this->data = $this->Permission->getDefaultValue();
			$userGroupId = $this->Session->read('Filter.Permission.user_group_id');
			if(!$userGroupId) {
				$userGroup = $this->Permission->UserGroup->find('first',array('conditions'=>array('UserGroup.id <>'=>'1'),
																	'fields' => array('id'),
																	'order'=>'UserGroup.id ASC','recursive'=>-1));
				if($userGroup){
					$userGroupId = $userGroup['UserGroup']['id'];
				}
			}
			$this->data['Permission']['user_group_id'] = $userGroupId;
			$authPrefix = $this->Permission->UserGroup->getAuthPrefix($userGroupId);
		}else {
			/* 登録処理 */
			if(isset($this->data['Permission']['user_group_id'])){
				$userGroupId = $this->data['Permission']['user_group_id'];
			}else{
				$userGroupId = null;
			}
			$authPrefix = $this->Permission->UserGroup->getAuthPrefix($userGroupId);
			$this->data['Permission']['url'] = '/'.$authPrefix.'/'.$this->data['Permission']['url'];
			$this->data['Permission']['no'] = $this->Permission->getMax('no',array('user_group_id'=>$userGroupId))+1;
			$this->data['Permission']['sort'] = $this->Permission->getMax('sort',array('user_group_id'=>$userGroupId))+1;
			$this->Permission->create($this->data);
			if($this->Permission->save()) {
				$message = '新規アクセス制限設定「'.$this->data['Permission']['name'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->Permission->saveDbLog($message);
				$this->redirect(array('action'=>'index'));
			}else {
				$this->data['Permission']['url'] = preg_replace('/^\/'.$authPrefix.'\//', '', $this->data['Permission']['url']);
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->pageTitle = '新規アクセス制限設定登録';
		$this->set('authPrefix', $authPrefix);
		$this->render('form');

	}
/**
 * [ADMIN] 編集処理
 *
 @ @param	int		ID
 * @return	void
 * @access 	public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		$authPrefix = $this->Permission->getAuthPrefix($id);
		if(empty($this->data)) {
			$this->data = $this->Permission->read(null, $id);
			$this->data['Permission']['url'] = preg_replace('/^\/'.$authPrefix.'\//', '', $this->data['Permission']['url']);
		}else {

			/* 更新処理 */
			$this->data['Permission']['url'] = '/'.$authPrefix.'/'.$this->data['Permission']['url'];
			if($this->Permission->save($this->data)) {
				$message = 'アクセス制限設定「'.$this->data['Permission']['name'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->Permission->saveDbLog($message);
				$this->redirect(array('action'=>'index'));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->pageTitle = 'アクセス制限設定編集：'.$this->data['Permission']['name'];
		$this->set('authPrefix', $authPrefix);
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理
 *
 @ @param	int		ID
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
		$post = $this->Permission->read(null, $id);

		/* 削除処理 */
		if($this->Permission->del($id)) {
			$message = 'アクセス制限設定「'.$post['Permission']['name'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->Permission->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}
/**
 * 並び替えを更新する [AJAX]
 *
 * @access	public
 * @return	boolean
 */
	function admin_update_sort () {

		if($this->data){
			$this->data = am($this->data,$this->_checkSession());
			$conditions = $this->_createAdminIndexConditions($this->data);
			if($this->Permission->changeSort($this->data['Sort']['id'],$this->data['Sort']['offset'],$conditions)){
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
 * @return	array()
 * @access	protected
 */
	function _checkSession(){
		$data = array();
		if($this->Session->check('Filter.Permission.user_group_id')) {
			$data['user_group_id'] = $this->Session->read('Filter.Permission.user_group_id');
		}else {
			$this->Session->del('Filter.Permission.user_group_id');
			$data['user_group_id'] = $this->Permission->getMax('user_group_id',array('id <>'=>1));
		}
		return array('Permission'=>$data);
	}
/**
 * 管理画面ページ一覧の検索条件を取得する
 *
 * @param	array		$data
 * @return	string
 * @access	protected
 */
	function _createAdminIndexConditions($data){

		if(isset($data['Permission'])){
			$data = $data['Permission'];
		}

		/* 条件を生成 */
		$conditions = array();
		if(!empty($data['user_group_id'])) {
			$conditions['Permission.user_group_id'] = $data['user_group_id'];
		}
		
		return $conditions;

	}
	
}
?>