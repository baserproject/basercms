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
 * Class UserGroupsController
 *
 * ユーザーグループコントローラー
 *
 * @package Baser.Controller
 * @property UserGroup $UserGroup
 */
class UserGroupsController extends AppController
{

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
	public $uses = ['UserGroup'];

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
	public $helpers = ['BcTime', 'BcForm'];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = ['site_configs', 'users'];

	/**
	 * UserGroupsController constructor.
	 *
	 * @param \CakeRequest $request
	 * @param \CakeRequest $response
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		$this->crumbs = [
			['name' => __d('baser', 'システム設定'), 'url' => ['controller' => 'site_configs', 'action' => 'form']],
			['name' => __d('baser', 'ユーザーグループ管理'), 'url' => ['controller' => 'user_groups', 'action' => 'index']]
		];
	}

	/**
	 * beforeFilter
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		if ($this->request->params['prefix'] === 'admin') {
			$this->set('usePermission', $this->UserGroup->checkOtherAdmins());
		}

		$authPrefixes = [];
		foreach(Configure::read('BcAuthPrefix') as $key => $authPrefix) {
			$authPrefixes[$key] = $authPrefix['name'];
		}
		if (count($authPrefixes) <= 1) {
			$this->UserGroup->validator()->remove('auth_prefix');
		}
	}

	/**
	 * ユーザーグループの一覧を表示する
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
		$this->setViewConditions('UserGroup', ['default' => $default]);
		$this->paginate = [
			'order' => 'UserGroup.id',
			'limit' => $this->passedArgs['num']
		];
		/* 表示設定 */
		$this->set('datas', $this->paginate());
		$this->pageTitle = __d('baser', 'ユーザーグループ一覧');
		$this->help = 'user_groups_index';
	}

	/**
	 * [ADMIN] 登録処理
	 *
	 * @return void
	 */
	public function admin_add()
	{
		/* 表示設定 */
		$this->pageTitle = __d('baser', '新規ユーザーグループ登録');
		$this->help = 'user_groups_form';
		if (!$this->request->data) {
			$this->render('form');
			return;
		}

		/* 登録処理 */
		if (empty($this->request->data['UserGroup']['auth_prefix'])) {
			$this->request->data['UserGroup']['auth_prefix'] = 'admin';
		} else {
			$this->request->data['UserGroup']['auth_prefix'] = implode(
				',',
				$this->request->data['UserGroup']['auth_prefix']
			);
		}
		$this->UserGroup->create($this->request->data);
		if (!$this->UserGroup->save()) {
			$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			$this->render('form');
			return;
		}

		$this->BcMessage->setSuccess(
			'新規ユーザーグループ「' . $this->request->data['UserGroup']['title'] . '」を追加しました。'
		);
		$this->redirect(['action' => 'index']);
		$this->render('form');
	}

	/**
	 * [ADMIN] 編集処理
	 *
	 * @param int ID
	 * @return void
	 */
	public function admin_edit($id)
	{
		/* 除外処理 */
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
		}

		/* 表示設定 */
		$this->pageTitle = __d('baser', 'ユーザーグループ編集');
		$this->help = 'user_groups_form';
		if (empty($this->request->data)) {
			$this->request->data = $this->UserGroup->read(null, $id);
			$this->render('form');
			return;
		}

		/* 更新処理 */
		if (empty($this->request->data['UserGroup']['auth_prefix'])) {
			$this->request->data['UserGroup']['auth_prefix'] = 'admin';
		} else {
			$this->request->data['UserGroup']['auth_prefix'] = implode(',', $this->request->data['UserGroup']['auth_prefix']);
		}
		if (!$this->UserGroup->save($this->request->data)) {
			$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			$this->render('form');
			return;
		}
		$this->BcMessage->setSuccess(
			'ユーザーグループ「' . $this->request->data['UserGroup']['name'] . '」を更新しました。'
		);
		$this->BcAuth->relogin();
		$this->redirect(['action' => 'index', $id]);
	}

	/**
	 * [ADMIN] 削除処理 (ajax)
	 *
	 * @param int ID
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
		$post = $this->UserGroup->read(null, $id);

		/* 削除処理 */
		if (!$this->UserGroup->delete($id)) {
			exit;
		}

		$message = 'ユーザーグループ「' . $post['UserGroup']['title'] . '」 を削除しました。';
		$this->UserGroup->saveDbLog($message);
		exit(true);
	}

	/**
	 * [ADMIN] 削除処理
	 *
	 * @param int ID
	 * @return void
	 */
	public function admin_delete($id = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
		}

		// メッセージ用にデータを取得
		$post = $this->UserGroup->read(null, $id);

		/* 削除処理 */
		if (!$this->UserGroup->delete($id)) {
			$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
		} else {
			$this->BcMessage->setSuccess('ユーザーグループ「' . $post['UserGroup']['title'] . '」 を削除しました。');
		}

		$this->redirect(['action' => 'index']);
	}

	/**
	 * [ADMIN] データコピー（AJAX）
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_ajax_copy($id)
	{
		$this->_checkSubmitToken();
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		$result = $this->UserGroup->copy($id);
		if (!$result) {
			$this->ajaxError(500, $this->UserGroup->validationErrors);
		} else {
			$this->set('data', $result);
		}
	}

	/**
	 * ユーザーグループのよく使う項目の初期値を登録する
	 * ユーザー編集画面よりAjaxで呼び出される
	 *
	 * @param $id
	 * @return void
	 * @throws Exception
	 */
	public function admin_set_default_favorites($id)
	{
		if (!$this->request->is(['post', 'put'])) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		$defaultFavorites = null;
		if ($this->request->data) {
			$defaultFavorites = BcUtil::serialize($this->request->data);
		}
		$this->UserGroup->id = $id;
		$this->UserGroup->recursive = -1;
		$data = $this->UserGroup->read();
		$data['UserGroup']['default_favorites'] = $defaultFavorites;
		$this->UserGroup->set($data);
		if ($this->UserGroup->save()) {
			echo true;
		}
		exit();
	}

}
