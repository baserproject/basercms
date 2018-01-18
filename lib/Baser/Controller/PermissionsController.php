<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

/**
 * アクセス制限設定コントローラー
 *
 * @package Baser.Controller
 */
class PermissionsController extends AppController {

/**
 * クラス名
 *
 * @var string
 */
	public $name = 'Permissions';

/**
 * モデル
 *
 * @var array
 */
	public $uses = ['Permission'];

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure'];

/**
 * ヘルパ
 *
 * @var array
 */
	public $helpers = ['BcTime', 'BcFreeze'];

/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = ['site_configs', 'users', 'permissions'];

/**
 * ぱんくずナビ
 *
 * @var array
 */
	public $crumbs = [
		['name' => 'ユーザー管理', 'url' => ['controller' => 'users', 'action' => 'index']],
		['name' => 'ユーザーグループ管理', 'url' => ['controller' => 'user_groups', 'action' => 'index']]
	];

/**
 * beforeFilter
 *
 * @return oid
 */
	public function beforeFilter() {
		parent::beforeFilter();
		if ($this->request->params['prefix'] == 'admin') {
			$this->set('usePermission', true);
		}
		$this->crumbs[] = ['name' => 'アクセス制限設定管理', 'url' => ['controller' => 'permissions', 'action' => 'index', $this->request->params['pass'][0]]];
	}

/**
 * アクセス制限設定の一覧を表示する
 *
 * @return void
 */
	public function admin_index($userGroupId = null) {
		/* セッション処理 */
		if (!$userGroupId) {
			$this->setMessage('無効な処理です。', true);
			$this->redirect(['controller' => 'user_groups', 'action' => 'index']);
		}

		$default = ['named' => ['sortmode' => 0]];
		$this->setViewConditions('Permission', ['default' => $default]);
		$conditions = $this->_createAdminIndexConditions($userGroupId);
		$datas = $this->Permission->find('all', ['conditions' => $conditions, 'order' => 'Permission.sort']);
		if ($datas) {
			foreach ($datas as $key => $data) {
				$datas[$key]['Permission']['url'] = preg_replace('/^\/admin\//', '/' . Configure::read('Routing.prefixes.0') . '/', $data['Permission']['url']);
			}
		}
		$this->set('datas', $datas);

		$this->_setAdminIndexViewData();

		if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		$userGroupName = $this->Permission->UserGroup->field('title', ['UserGroup.id' => $userGroupId]);
		$this->pageTitle = '[' . $userGroupName . '] アクセス制限設定一覧';
		$this->help = 'permissions_index';
	}

/**
 * 一覧の表示用データをセットする
 *
 * @return void
 */
	protected function _setAdminIndexViewData() {
		$this->set('sortmode', $this->passedArgs['sortmode']);
	}

/**
 * [ADMIN] 登録処理
 *
 * @return void
 */
	public function admin_add($userGroupId) {
		$userGroup = $this->Permission->UserGroup->find('first', ['conditions' => ['UserGroup.id' => $userGroupId],
			'fields' => ['id', 'title'],
			'order' => 'UserGroup.id ASC', 'recursive' => -1]);
		if (!$this->request->data) {
			$this->request->data = $this->Permission->getDefaultValue();
			$this->request->data['Permission']['user_group_id'] = $userGroupId;
			// TODO 現在 admin 固定、今後、mypage 等にも対応する
			$permissionAuthPrefix = 'admin';
		} else {
			/* 登録処理 */
			if (isset($this->request->data['Permission']['user_group_id'])) {
				$userGroupId = $this->request->data['Permission']['user_group_id'];
			} else {
				$userGroupId = null;
			}
			// TODO 現在 admin 固定、今後、mypage 等にも対応する
			$permissionAuthPrefix = 'admin';
			$this->request->data['Permission']['url'] = '/' . $permissionAuthPrefix . '/' . $this->request->data['Permission']['url'];
			$this->request->data['Permission']['no'] = $this->Permission->getMax('no', ['user_group_id' => $userGroupId]) + 1;
			$this->request->data['Permission']['sort'] = $this->Permission->getMax('sort', ['user_group_id' => $userGroupId]) + 1;
			$this->Permission->create($this->request->data);
			if ($this->Permission->save()) {
				$this->setMessage('新規アクセス制限設定「' . $this->request->data['Permission']['name'] . '」を追加しました。', false, true);
				$this->redirect(['action' => 'index', $userGroupId]);
			} else {
				$this->request->data['Permission']['url'] = preg_replace('/^(\/' . $permissionAuthPrefix . '\/|\/)/', '', $this->request->data['Permission']['url']);
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		if ($permissionAuthPrefix == 'admin') {
			$permissionAuthPrefix = Configure::read('Routing.prefixes.0');
		}
		$this->pageTitle = '[' . $userGroup['UserGroup']['title'] . '] 新規アクセス制限設定登録';
		$this->set('permissionAuthPrefix', $permissionAuthPrefix);
		$this->help = 'permissions_form';
		$this->render('form');
	}

/**
 * [ADMIN] 登録処理
 *
 * @return void
 */
	public function admin_ajax_add() {
		if ($this->request->data) {
			// TODO 現在 admin 固定、今後、mypage 等にも対応する
			$authPrefix = 'admin';
			$this->request->data['Permission']['url'] = '/' . $authPrefix . '/' . $this->request->data['Permission']['url'];
			$this->request->data['Permission']['no'] = $this->Permission->getMax('no', ['user_group_id' => $this->request->data['Permission']['user_group_id']]) + 1;
			$this->request->data['Permission']['sort'] = $this->Permission->getMax('sort', ['user_group_id' => $this->request->data['Permission']['user_group_id']]) + 1;
			$this->request->data['Permission']['status'] = true;
			$this->Permission->create($this->request->data);
			if ($this->Permission->save()) {
				$this->Permission->saveDbLog('新規アクセス制限設定「' . $this->request->data['Permission']['name'] . '」を追加しました。');
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
 */
	public function admin_edit($userGroupId, $id) {
		/* 除外処理 */
		if (!$userGroupId || !$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(['action' => 'index']);
		}

		$userGroup = $this->Permission->UserGroup->find('first', ['conditions' => ['UserGroup.id' => $userGroupId],
			'fields' => ['id', 'title'],
			'order' => 'UserGroup.id ASC', 'recursive' => -1]);

		// TODO 現在 admin 固定、今後、mypage 等にも対応する
		$authPrefix = 'admin';
		if (empty($this->request->data)) {

			$this->request->data = $this->Permission->read(null, $id);
			$this->request->data['Permission']['url'] = preg_replace('/^(\/' . $authPrefix . '\/|\/)/', '', $this->request->data['Permission']['url']);
		} else {

			/* 更新処理 */
			$this->request->data['Permission']['url'] = '/' . $authPrefix . '/' . $this->request->data['Permission']['url'];

			if ($this->Permission->save($this->request->data)) {
				$this->setMessage('アクセス制限設定「' . $this->request->data['Permission']['name'] . '」を更新しました。', false, true);
				$this->redirect(['action' => 'index', $userGroupId]);
			} else {
				$this->request->data['Permission']['url'] = preg_replace('/^(\/' . $authPrefix . '\/|\/)/', '', $this->request->data['Permission']['url']);
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		$this->pageTitle = '[' . $userGroup['UserGroup']['title'] . '] アクセス制限設定編集：' . $this->request->data['Permission']['name'];
		$this->set('permissionAuthPrefix', Configure::read('Routing.prefixes.0'));
		$this->help = 'permissions_form';
		$this->render('form');
	}

/**
 * [ADMIN] 削除処理　(ajax)
 *
 * @param int $id
 * @return void
 */
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {

				// メッセージ用にデータを取得
				$post = $this->Permission->read(null, $id);
				/* 削除処理 */
				if ($this->Permission->delete($id)) {
					$message = 'アクセス制限設定「' . $post['Permission']['name'] . '」 を削除しました。';
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
 */
	public function admin_ajax_delete($id = null) {
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// メッセージ用にデータを取得
		$post = $this->Permission->read(null, $id);

		/* 削除処理 */
		if ($this->Permission->delete($id)) {
			$message = 'アクセス制限設定「' . $post['Permission']['name'] . '」 を削除しました。';
			exit(true);
		}
		exit();
	}

/**
 * [ADMIN] 削除処理
 *
 * @param int $id
 * @return void
 */
	public function admin_delete($userGroupId, $id = null) {
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(['action' => 'index']);
		}

		// メッセージ用にデータを取得
		$post = $this->Permission->read(null, $id);

		/* 削除処理 */
		if ($this->Permission->delete($id)) {
			$this->setMessage('アクセス制限設定「' . $post['Permission']['name'] . '」 を削除しました。', false, true);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true, false);
		}

		$this->redirect(['action' => 'index', $userGroupId]);
	}

/**
 * 並び替えを更新する [AJAX]
 *
 * @return boolean
 * @access	public
 */
	public function admin_ajax_update_sort($userGroupId) {
		if ($this->request->data) {
			$conditions = $this->_createAdminIndexConditions($userGroupId);
			if ($this->Permission->changeSort($this->request->data['Sort']['id'], $this->request->data['Sort']['offset'], $conditions)) {
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
 */
	protected function _createAdminIndexConditions($userGroupId) {
		/* 条件を生成 */
		$conditions = [];
		if ($userGroupId) {
			$conditions['Permission.user_group_id'] = $userGroupId;
		}

		return $conditions;
	}

/**
 * [ADMIN] データコピー（AJAX）
 *
 * @param int $id
 * @return void
 */
	public function admin_ajax_copy($userGroupId, $id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$result = $this->Permission->copy($id);
		if ($result) {
			$this->setViewConditions('Permission', ['action' => 'admin_index']);
			$result['Permission']['url'] = preg_replace('/^\/admin\//', '/' . Configure::read('Routing.prefixes.0') . '/', $result['Permission']['url']);
			$sortmode = false;
			if (isset($this->passedArgs['sortmode'])) {
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
 */
	public function admin_ajax_unpublish($id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, false)) {
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
 */
	public function admin_ajax_publish($id) {
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_changeStatus($id, true)) {
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
 */
	protected function _batch_publish($ids) {
		if ($ids) {
			foreach ($ids as $id) {
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
 */
	protected function _batch_unpublish($ids) {
		if ($ids) {
			foreach ($ids as $id) {
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
	protected function _changeStatus($id, $status) {
		$statusTexts = [0 => '無効', 1 => '有効'];
		$data = $this->Permission->find('first', ['conditions' => ['Permission.id' => $id], 'recursive' => -1]);
		$data['Permission']['status'] = $status;
		$this->Permission->set($data);

		if ($this->Permission->save()) {
			$statusText = $statusTexts[$status];
			$this->Permission->saveDbLog('アクセス制限設定「' . $data['Permission']['name'] . '」 を' . $statusText . '化しました。');
			return true;
		} else {
			return false;
		}
	}

}