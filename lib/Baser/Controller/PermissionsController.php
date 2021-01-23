<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * Class PermissionsController
 *
 * アクセス制限設定コントローラー
 *
 * @package Baser.Controller
 */
class PermissionsController extends AppController
{

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
	 * beforeFilter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$this->crumbs = [
			['name' => __d('baser', 'ユーザー管理'), 'url' => ['controller' => 'users', 'action' => 'index']],
			['name' => __d('baser', 'ユーザーグループ管理'), 'url' => ['controller' => 'user_groups', 'action' => 'index']]
		];
		if ($this->request->params['prefix'] === 'admin') {
			$this->set('usePermission', true);
		}
		if (!$this->request->is('ajax')) {
			$this->crumbs[] = ['name' => __d('baser', 'アクセス制限設定管理'), 'url' => ['controller' => 'permissions', 'action' => 'index', $this->request->params['pass'][0]]];
		}
	}

	/**
	 * アクセス制限設定の一覧を表示する
	 *
	 * @return void
	 */
	public function admin_index($userGroupId = null)
	{
		/* セッション処理 */
		if (!$userGroupId) {
			$this->BcMessage->setError(__d('baser', '無効な処理です。'));
			$this->redirect(['controller' => 'user_groups', 'action' => 'index']);
		}

		$default = ['named' => ['sortmode' => 0]];
		$this->setViewConditions('Permission', ['default' => $default]);
		$conditions = $this->_createAdminIndexConditions($userGroupId);
		$datas = $this->Permission->find('all', ['conditions' => $conditions, 'order' => 'Permission.sort']);
		if ($datas) {
			foreach($datas as $key => $data) {
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
		$this->pageTitle = sprintf(__d('baser', '%s｜アクセス制限設定一覧'), $userGroupName);
		$this->help = 'permissions_index';
	}

	/**
	 * 一覧の表示用データをセットする
	 *
	 * @return void
	 */
	protected function _setAdminIndexViewData()
	{
		$this->set('sortmode', $this->passedArgs['sortmode']);
	}

	/**
	 * [ADMIN] 登録処理
	 *
	 * @return void
	 */
	public function admin_add($userGroupId)
	{
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
				$this->BcMessage->setSuccess(sprintf(__d('baser', '新規アクセス制限設定「%s」を追加しました。'), $this->request->data['Permission']['name']));
				$this->redirect(['action' => 'index', $userGroupId]);
			} else {
				$this->request->data['Permission']['url'] = preg_replace('/^(\/' . $permissionAuthPrefix . '\/|\/)/', '', $this->request->data['Permission']['url']);
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		/* 表示設定 */
		if ($permissionAuthPrefix === 'admin') {
			$permissionAuthPrefix = Configure::read('Routing.prefixes.0');
		}
		$this->pageTitle = sprintf(__d('baser', '%s｜新規アクセス制限設定登録'), $userGroup['UserGroup']['title']);
		$this->set('permissionAuthPrefix', $permissionAuthPrefix);
		$this->help = 'permissions_form';
		$this->render('form');
	}

	/**
	 * [ADMIN] 登録処理
	 *
	 * @return void
	 */
	public function admin_ajax_add()
	{
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
			exit;
		}

// TODO 現在 admin 固定、今後、mypage 等にも対応する
		$authPrefix = 'admin';
		$this->request->data['Permission']['url'] = '/' . $authPrefix . '/' . $this->request->data['Permission']['url'];
		$this->request->data['Permission']['no'] = $this->Permission->getMax('no', ['user_group_id' => $this->request->data['Permission']['user_group_id']]) + 1;
		$this->request->data['Permission']['sort'] = $this->Permission->getMax('sort', ['user_group_id' => $this->request->data['Permission']['user_group_id']]) + 1;
		$this->request->data['Permission']['status'] = true;
		$this->Permission->create($this->request->data);
		if (!$this->Permission->save()) {
			$this->ajaxError(500, $this->Page->validationErrors);
			exit;
		}

		$this->Permission->saveDbLog(
			sprintf(
				__d('baser', '新規アクセス制限設定「%s」を追加しました。'), $this->request->data['Permission']['name']
			)
		);
		exit(true);
	}

	/**
	 * [ADMIN] 編集処理
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_edit($userGroupId, $id)
	{
		/* 除外処理 */
		if (!$userGroupId || !$id) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
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
				$this->BcMessage->setSuccess(sprintf(__d('baser', 'アクセス制限設定「%s」を更新しました。'), $this->request->data['Permission']['name']));
				$this->redirect(['action' => 'index', $userGroupId]);
			} else {
				$this->request->data['Permission']['url'] = preg_replace('/^(\/' . $authPrefix . '\/|\/)/', '', $this->request->data['Permission']['url']);
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		/* 表示設定 */
		$this->pageTitle = sprintf(__d('baser', '%s｜アクセス制限設定編集'), $userGroup['UserGroup']['title']);
		$this->set('permissionAuthPrefix', Configure::read('Routing.prefixes.0'));
		$this->help = 'permissions_form';
		$this->render('form');
	}

	/**
	 * [ADMIN] 削除処理　(ajax)
	 *
	 * @param $ids
	 * @return boolean
	 */
	protected function _batch_del($ids)
	{
		if ($ids) {
			foreach($ids as $id) {
				// メッセージ用にデータを取得
				$post = $this->Permission->read(null, $id);
				/* 削除処理 */
				if ($this->Permission->delete($id)) {
					$message = sprintf(__d('baser', 'アクセス制限設定「%s」 を削除しました。'), $post['Permission']['name']);
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
	public function admin_ajax_delete($id = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		// メッセージ用にデータを取得
		$post = $this->Permission->read(null, $id);

		/* 削除処理 */
		if ($this->Permission->delete($id)) {
			$message = sprintf(__d('baser', 'アクセス制限設定「%s」 を削除しました。'), $post['Permission']['name']);
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
	public function admin_delete($userGroupId, $id = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
		}

		// メッセージ用にデータを取得
		$post = $this->Permission->read(null, $id);

		/* 削除処理 */
		if ($this->Permission->delete($id)) {
			$this->BcMessage->setSuccess(
				sprintf(
					__d('baser', 'アクセス制限設定「%s」 を削除しました。'), $post['Permission']['name']
				)
			);
		} else {
			$this->BcMessage->setError('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(['action' => 'index', $userGroupId]);
	}

	/**
	 * 並び替えを更新する [AJAX]
	 *
	 * @access    public
	 * @param $userGroupId
	 * @return void
	 */
	public function admin_ajax_update_sort($userGroupId)
	{
		$this->autoRender = false;
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
			exit;
		}

		$conditions = $this->_createAdminIndexConditions($userGroupId);
		if (!$this->Permission->changeSort($this->request->data['Sort']['id'], $this->request->data['Sort']['offset'], $conditions)) {
			$this->ajaxError(500, $this->Permission->validationErrors);
			exit;
		}
		echo true;
	}

	/**
	 * 管理画面ページ一覧の検索条件を取得する
	 *
	 * @param $userGroupId
	 * @return array
	 */
	protected function _createAdminIndexConditions($userGroupId)
	{
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
	public function admin_ajax_copy($userGroupId, $id)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
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
	 * @param $id
	 * @return void
	 */
	public function admin_ajax_unpublish($id)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		if (!$this->_changeStatus($id, false)) {
			$this->ajaxError(500, $this->Permission->validationErrors);
			exit;
		}
		exit(true);
	}

	/**
	 * [ADMIN] 有効状態にする（AJAX）
	 *
	 * @param $id
	 * @return void
	 */
	public function admin_ajax_publish($id)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		if (!$this->_changeStatus($id, true)) {
			$this->ajaxError(500, $this->Permission->validationErrors);
			exit;
		}

		exit(true);
	}

	/**
	 * 一括公開
	 *
	 * @param array $ids
	 * @return boolean
	 */
	protected function _batch_publish($ids)
	{
		if ($ids) {
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
	 */
	protected function _batch_unpublish($ids)
	{
		if ($ids) {
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
	protected function _changeStatus($id, $status)
	{
		$statusTexts = [0 => __d('baser', '無効'), 1 => __d('baser', '有効')];
		$data = $this->Permission->find('first', ['conditions' => ['Permission.id' => $id], 'recursive' => -1]);
		$data['Permission']['status'] = $status;
		$this->Permission->set($data);

		if (!$this->Permission->save()) {
			return false;
		}

		$statusText = $statusTexts[$status];
		$this->Permission->saveDbLog(
			sprintf(
				'アクセス制限設定「%s」 を %s に設定しました。',
				$data['Permission']['name'],
				$statusText
			)
		);
		return true;
	}
}
