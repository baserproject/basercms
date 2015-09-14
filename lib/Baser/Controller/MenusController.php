<?php

/**
 * メニューコントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * メニューコントローラー
 *
 * @package Baser.Controller
 */
class MenusController extends AppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Menus';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('Menu');

/**
 * コンポーネント
 *
 * @var array
 * @accesspublic
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'RequestHandler');

/**
 * ヘルパ
 *
 * @var array
 * @access public
 */
	public $helpers = array('BcTime', 'BcForm');

/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array();

/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array(
		array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')),
		array('name' => 'メニュー管理', 'url' => array('controller' => 'menus', 'action' => 'index'))
	);

/**
 * メニューの一覧を表示する
 *
 * @return void
 * @access public
 */
	public function admin_index() {
		/* セッション処理 */
		if ($this->request->data) {
			$this->Session->write('Filter.Menu.status', $this->request->data['Menu']['status']);
		}
		if (isset($this->request->params['named']['sortmode'])) {
			$this->Session->write('SortMode.Menu', $this->request->params['named']['sortmode']);
		}

		$this->request->data = am($this->request->data, $this->_checkSession());

		/* 並び替えモード */
		if (!$this->Session->check('SortMode.Menu')) {
			$this->set('sortmode', 0);
		} else {
			$this->set('sortmode', $this->Session->read('SortMode.Menu'));
		}

		$conditions = $this->_createAdminIndexConditions($this->request->data);

		// TODO CSVドライバーが複数の並び替えフィールドを指定できないがtypeを指定したい
		$listDatas = $this->Menu->find('all', array('conditions' => $conditions, 'order' => 'Menu.sort'));

		$this->set('listDatas', $listDatas);

		if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		// 表示設定
		$this->subMenuElements = array('menus');
		$this->pageTitle = 'メニュー一覧';
		$this->search = 'menus_index';
		$this->help = 'menus_index';
	}

/**
 * [ADMIN] 登録処理
 *
 * @return void
 * @access public
 */
	public function admin_add() {
		if (!$this->request->data) {
			$this->request->data['Menu']['status'] = 0;
		} else {

			/* 登録処理 */
			if (!preg_match('/^http/is', $this->request->data['Menu']['link']) && !preg_match('/^\//is', $this->request->data['Menu']['link'])) {
				$this->request->data['Menu']['link'] = '/' . $this->request->data['Menu']['link'];
			}
			$this->request->data['Menu']['no'] = $this->Menu->getMax('no') + 1;
			$this->request->data['Menu']['sort'] = $this->Menu->getMax('sort') + 1;
			$this->Menu->create($this->request->data);

			// データを保存
			if ($this->Menu->save()) {
				clearViewCache();
				$this->setMessage('新規メニュー「' . $this->request->data['Menu']['name'] . '」を追加しました。', false, true);
				$this->redirect(array('action' => 'index'));
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		$this->subMenuElements = array('menus');
		$this->pageTitle = '新規メニュー登録';
		$this->help = 'menus_form';
		$this->render('form');
	}

/**
 * [ADMIN] 編集処理
 *
 * @param	int ID
 * @return void
 * @access public
 */
	public function admin_edit($id) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->request->data)) {
			$this->request->data = $this->Menu->read(null, $id);
		} else {

			/* 更新処理 */
			if (!preg_match('/^http/is', $this->request->data['Menu']['link']) && !preg_match('/^\//is', $this->request->data['Menu']['link'])) {
				$this->request->data['Menu']['link'] = '/' . $this->request->data['Menu']['link'];
			}
			$this->Menu->set($this->request->data);
			if ($this->Menu->save()) {
				clearViewCache();
				$this->setMessage('メニュー「' . $this->request->data['Menu']['name'] . '」を更新しました。', false, true);
				$this->redirect(array('action' => 'index', $id));
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		$this->subMenuElements = array('menus');
		$this->pageTitle = 'メニュー編集：' . $this->request->data['Menu']['name'];
		$this->help = 'menus_form';
		$this->render('form');
	}

/**
 * [ADMIN] 一括削除
 *
 * @param int ID
 * @return void
 * @access public
 */
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {
				// メッセージ用にデータを取得
				$post = $this->Menu->read(null, $id);

				/* 削除処理 */
				if ($this->Menu->delete($id)) {
					clearViewCache();
					$message = 'メニュー「' . $post['Menu']['name'] . '」 を削除しました。';
					$this->Menu->saveDbLog($message);
				}
			}
		}
		return true;
	}

/**
 * [ADMIN] 削除処理 (ajax)
 *
 * @param int ID
 * @return void
 * @access public
 */
	public function admin_ajax_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// メッセージ用にデータを取得
		$post = $this->Menu->read(null, $id);

		/* 削除処理 */
		if ($this->Menu->delete($id)) {
			clearViewCache();
			$message = 'メニュー「' . $post['Menu']['name'] . '」 を削除しました。';
			$this->Menu->saveDbLog($message);
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
	public function admin_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$post = $this->Menu->read(null, $id);

		/* 削除処理 */
		if ($this->Menu->delete($id)) {
			clearViewCache();
			$this->setMessage('メニュー「' . $post['Menu']['name'] . '」 を削除しました。', false, true);
		} else {
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
	public function admin_ajax_update_sort() {
		if ($this->request->data) {
			$this->request->data = am($this->request->data, $this->_checkSession());
			if ($this->Menu->changeSort($this->request->data['Sort']['id'], $this->request->data['Sort']['offset'])) {
				echo true;
			} else {
				$this->ajaxError(500, '一度リロードしてから再実行してみてください。');
			}
		} else {
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
	protected function _checkSession() {
		$data = array();
		if ($this->Session->check('Filter.Menu.menu_type')) {
			$data['menu_type'] = $this->Session->read('Filter.Menu.menu_type');
		} else {
			$this->Session->delete('Filter.Menu.menu_type');
			$data['menu_type'] = 'default';
		}
		if ($this->Session->check('Filter.Menu.status')) {
			$data['status'] = $this->Session->read('Filter.Menu.status');
		} else {
			$this->Session->delete('Filter.Menu.status');
		}
		return array('Menu' => $data);
	}

/**
 * 管理画面ページ一覧の検索条件を取得する
 *
 * @param array $data
 * @return string
 * @access protected
 */
	protected function _createAdminIndexConditions($data) {
		if (isset($data['Menu'])) {
			$data = $data['Menu'];
		}

		/* 条件を生成 */
		$conditions = array();
		if (isset($data['status']) && $data['status'] !== '') {
			$conditions['Menu.status'] = $data['status'];
		}

		return $conditions;
	}

}
