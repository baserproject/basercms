<?php

/* SVN FILE: $Id$ */
/**
 * ユーザーグループコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */

/**
 * ユーザーグループコントローラー
 *
 * @package Baser.Controller
 */
class UserGroupsController extends AppController {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'UserGroups';

/**
 * モデル
 *
 * @var array
 */
	public $uses = array('UserGroup');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * ヘルパ
 *
 * @var array
 */
	public $helpers = array('BcTime', 'BcForm');

/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = array('site_configs', 'users', 'user_groups');

/**
 * ぱんくずナビ
 *
 * @var array
 */
	public $crumbs = array(
		array('name' => 'ユーザー管理', 'url' => array('controller' => 'users', 'action' => 'index')),
		array('name' => 'ユーザーグループ管理', 'url' => array('controller' => 'user_groups', 'action' => 'index'))
	);

/**
 * beforeFilter
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if ($this->request->params['prefix'] == 'admin') {
			$this->set('usePermission', $this->UserGroup->checkOtherAdmins());
		}
	}

/**
 * ユーザーグループの一覧を表示する
 *
 * @return void
 */
	public function admin_index() {
		/* データ取得 */
		$this->paginate = array('conditions' => array(),
			'fields' => array(),
			'order' => 'UserGroup.id',
			'limit' => 10
		);
		$datas = $this->paginate('UserGroup');

		/* 表示設定 */
		$this->set('datas', $datas);
		$this->pageTitle = 'ユーザーグループ一覧';
		$this->help = 'user_groups_index';
	}

/**
 * [ADMIN] 登録処理
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->data) {

			/* 登録処理 */
			if (empty($this->request->data['UserGroup']['auth_prefix'])) {
				$this->request->data['UserGroup']['auth_prefix'] = 'admin';
			}
			$this->UserGroup->create($this->request->data);
			if ($this->UserGroup->save()) {
				$this->setMessage('新規ユーザーグループ「' . $this->request->data['UserGroup']['title'] . '」を追加しました。', false, true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
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
 */
	public function admin_edit($id) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->UserGroup->read(null, $id);
		} else {

			/* 更新処理 */
			if ($this->UserGroup->save($this->request->data)) {
				$this->setMessage('ユーザーグループ「' . $this->request->data['UserGroup']['name'] . '」を更新しました。', false, true);
				$this->redirect(array('action' => 'index', $id));
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		$this->pageTitle = 'ユーザーグループ編集：' . $this->request->data['UserGroup']['title'];
		$this->help = 'user_groups_form';
		$this->render('form');
	}

/**
 * [ADMIN] 削除処理 (ajax)
 *
 * @param int ID
 * @return void
 */
	public function admin_ajax_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// メッセージ用にデータを取得
		$post = $this->UserGroup->read(null, $id);

		/* 削除処理 */
		if ($this->UserGroup->delete($id)) {
			$message = 'ユーザーグループ「' . $post['UserGroup']['title'] . '」 を削除しました。';
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
 */
	public function admin_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$post = $this->UserGroup->read(null, $id);

		/* 削除処理 */
		if ($this->UserGroup->delete($id)) {
			$this->setMessage('ユーザーグループ「' . $post['UserGroup']['title'] . '」 を削除しました。', false, true);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * [ADMIN] データコピー（AJAX）
 * 
 * @param int $id 
 * @return void
 */
	public function admin_ajax_copy($id) {
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$result = $this->UserGroup->copy($id);
		if ($result) {
			$this->set('data', $result);
		} else {
			$this->ajaxError(500, $this->UserGroup->validationErrors);
		}
	}

/**
 * ユーザーグループのよく使う項目の初期値を登録する
 * 
 * @return boolean 
 */
	public function admin_set_default_favorites($id) {
		if (!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$user = $this->BcAuth->user();
		$this->UserGroup->id = $id;
		$this->UserGroup->recursive = -1;
		$data = $this->UserGroup->read();
		$data['UserGroup']['default_favorites'] = BcUtil::serialize($this->request->data);
		$this->UserGroup->set($data);
		if ($this->UserGroup->save()) {
			echo true;
		}
		exit();
	}

}
