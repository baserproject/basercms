<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーグループコントローラー
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
 * ユーザーグループコントローラー
 *
 * @package			baser.controllers
 */
class UserGroupsController extends AppController {
/**
 * クラス名
 *
 * @var     string
 * @access  public
 */
	var $name = 'UserGroups';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('UserGroup');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('Auth','Cookie','AuthConfigure');
/**
 * ヘルパ
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Time','FormEx');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array('users', 'user_groups');
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('ユーザー管理'=>'/admin/users/index',
			'ユーザーグループ管理'=>'/admin/user_groups/index');
/**
 * beforeFilter
 * @return	void
 * @access	public
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
 * @return  void
 * @access  public
 */
	function admin_index() {

		/* データ取得 */
		$this->paginate = array('conditions'=>array(),
				'fields'=>array(),
				'order'=>'UserGroup.id',
				'limit'=>10
		);
		$listDatas = $this->paginate('UserGroup');

		/* 表示設定 */
		$this->set('listDatas',$listDatas);
		$this->pageTitle = 'ユーザーグループ一覧';

	}
/**
 * [ADMIN] 登録処理
 *
 * @return  void
 * @access  public
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
				$this->redirect(array('action'=>'index'));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->pageTitle = '新規ユーザーグループ登録';
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

		if(empty($this->data)) {
			$this->data = $this->UserGroup->read(null, $id);
		}else {

			/* 更新処理 */
			if($this->UserGroup->save($this->data)) {
				$message = 'ユーザーグループ「'.$this->data['UserGroup']['name'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->UserGroup->saveDbLog($message);
				$this->redirect(array('action'=>'index',$id));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->pageTitle = 'ユーザーグループ編集：'.$this->data['UserGroup']['title'];
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
		$post = $this->UserGroup->read(null, $id);

		/* 削除処理 */
		if($this->UserGroup->del($id)) {
			$message = 'ユーザーグループ「'.$post['UserGroup']['title'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->UserGroup->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}

}
?>