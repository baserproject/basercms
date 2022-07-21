<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright Copyright (c) baserCMS Users Community
 * @link      https://basercms.net baserCMS Project
 * @package   Baser.Controller
 * @since     baserCMS v 0.1.0
 * @license   https://basercms.net/license/index.html
 */

/**
 * Class UsersController
 *
 * ユーザーコントローラー
 *
 * ユーザーを管理するコントローラー。ログイン処理を担当する。
 *
 * @package Baser.Controller
 */
class UsersController extends AppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'Users';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['User', 'UserGroup'];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['BcHtml', 'BcTime', 'BcForm'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcReplacePrefix', 'BcAuth', 'Cookie', 'BcAuthConfigure', 'BcEmail'];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = [];

	/**
	 * UsersController constructor.
	 *
	 * @param \CakeRequest $request
	 * @param \CakeRequest $response
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		$this->crumbs = [
			[
				'name' => __d('baser', 'システム設定'),
				'url'  => [
					'controller' => 'site_configs',
					'action' => 'form'
				]
			],
			[
				'name' => __d('baser', 'ユーザー管理'),
				'url'  => [
					'controller' => 'users',
					'action' => 'index'
				]
			]
		];
	}

	/**
	 * beforeFilter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		if (BC_INSTALLED) {
			/* 認証設定 */
			// parent::beforeFilterの前に記述する必要あり
			$this->BcAuth->allow(
				'admin_login',
				'admin_logout',
				'admin_login_exec',
				'admin_reset_password',
				'admin_send_activate_url'
			);
			if (isset($this->UserGroup)) {
				$this->set('usePermission', $this->UserGroup->checkOtherAdmins());
			}
		}

		parent::beforeFilter();

		$this->BcReplacePrefix->allow(
			'login',
			'logout',
			'login_exec',
			'reset_password',
			'send_activate_url'
		);
	}

	/**
	 * ログイン処理を行う
	 * ・リダイレクトは行わない
	 * ・requestActionから呼び出す
	 *
	 * @return boolean
	 */
	public function admin_login_exec()
	{
		if (!$this->request->data) {
			return false;
		}
		if ($this->BcAuth->login()) {
			return true;
		}
		return false;
	}

	/**
	 * [ADMIN] 管理者ログイン画面
	 *
	 * @return void
	 */
	public function admin_login()
	{
		if ($this->BcAuth->loginAction != ('/' . $this->request->url)) {
			$this->notFound();
		}

		// EVENT Users.beforeLogin
		$event = $this->dispatchEvent('beforeLogin', [
			'user' => $this->request->data
		]);
		if ($event !== false) {
			$this->request->data = $event->result === true ? $event->data['user'] : $event->result;
		}

		if ($this->request->is('post')) {
			$this->BcAuth->login();
		}
		$user = $this->BcAuth->user();

		if ($this->request->is('post')) {
			if($this->_set_auth($user)) {

				// EVENT Users.afterLogin
				$this->dispatchEvent('afterLogin', [
					'user' => $this->BcAuth->user(),
					'loginRedirect' => $this->BcAuth->redirect(),
				]);

				$this->redirect($this->BcAuth->redirectUrl());
				return;
			}
			$this->BcMessage->setError(__d('baser', 'アカウント名、パスワードが間違っています。'));
		} else {
			if ($user && $this->isAuthorized($user)) {
				$this->redirect($this->BcAuth->redirectUrl());
				return;
			}
		}

		/* 表示設定 */
		$this->crumbs = [];
		$this->subMenuElements = '';
		$prefixAuth = Configure::read('BcAuthPrefix.' . $this->request->prefix);
		if (Hash::get($prefixAuth, 'loginTitle')) {
			$this->pageTitle = $prefixAuth['loginTitle'];
		} else {
			$this->pageTitle = __d('baser', 'ログイン');
		}
	}

	private function _set_auth($user) {
		if (!$user || !$this->isAuthorized($user)) {
			return false;
		}
		$userModel = $this->BcAuth->authenticate['Form']['userModel'];
		if ($this->request->data($userModel . '.saved')) {
			if ($this->request->is('mobile')) {
				$this->BcAuth->saveSerial();
			} else {
				$this->setAuthCookie($this->request->data);
			}
			unset($this->request->data[$userModel]['save']);
		} else {
			$this->Cookie->destroy();
		}
		App::uses('BcBaserHelper', 'View/Helper');
		$BcBaser = new BcBaserHelper(new View());
		$this->BcMessage->setInfo(
			sprintf(__d('baser', 'ようこそ、%s さん。'), $BcBaser->getUserName($user))
		);
		return true;
	}

	/**
	 * [ADMIN] 代理ログイン
	 *
	 * @param int $id
	 * @return void
	 */
	public function admin_ajax_agent_login($id)
	{
		$beforeUser = $this->BcAuth->user();
		if (!$this->Session->check('AuthAgent')) {
			$this->Session->write('AuthAgent', $beforeUser);
		}

		$result = $this->User->find(
			'first',
			[
				'conditions' => ['User.id' => $id],
				'recursive' => 0
			]
		);
		$user = $result['User'];
		unset($user['password'], $result['User']);
		$user = array_merge($user, $result);

		// EVENT Users.beforeAgentLogin
		$event = $this->dispatchEvent('beforeAgentLogin', [
			'beforeUser' => $beforeUser,
			'afterUser' => $user,
		]);
		if ($event !== false) {
			$user = $event->result === true ? $event->data['afterUser'] : $event->result;
		}

		Configure::write('debug', 0);
		if ($user) {
			$this->Session->renew();
			$this->Session->write(BcAuthComponent::$sessionKey, $user);

			// EVENT Users.afterAgentLogin
			$this->dispatchEvent('afterAgentLogin', [
				'beforeUser' => $beforeUser,
				'afterUser' => $user,
			]);

			exit(Router::url($this->BcAuth->redirect()));
		}
	}

	/**
	 * 代理ログインをしている場合、元のユーザーに戻る
	 *
	 * @return void
	 */
	public function back_agent()
	{
		$configs = Configure::read('BcAuthPrefix');
		if ($this->Session->check('AuthAgent')) {
			$data = $this->Session->read('AuthAgent');

			// EVENT Users.beforeBackAgent
			$event = $this->dispatchEvent('beforeBackAgent', [
				'beforeUser' => $this->BcAuth->user(),
				'afterUser' => $data,
			]);
			if ($event !== false) {
				$data = $event->result === true ? $event->data['afterUser'] : $event->result;
			}

			$this->Session->write(BcAuthComponent::$sessionKey, $data);
			$this->Session->delete('AuthAgent');
			$this->BcMessage->setInfo(__d('baser', '元のユーザーに戻りました。'));
			$authPrefix = explode(',', $data['UserGroup']['auth_prefix']);
			$authPrefix = $authPrefix[0];
		} else {
			$this->BcMessage->setError(__d('baser', '不正な操作です。'));
			if (!empty($this->request->params['prefix'])) {
				$authPrefix = $this->request->params['prefix'];
			} else {
				$authPrefix = 'front';
			}
		}

		// EVENT Users.afterBackAgent
		$event = $this->dispatchEvent('afterBackAgent', [
			'authPrefix' => $authPrefix,
			'user' => $this->BcAuth->user(),
		]);

		if (!empty($configs[$authPrefix])) {
			$redirect = $configs[$authPrefix]['loginRedirect'];
		} else {
			$redirect = '/';
		}

		$this->redirect($redirect);
	}

	/**
	 * 認証クッキーをセットする
	 *
	 * @param array $data
	 * @return void
	 */
	public function setAuthCookie($data)
	{
		$userModel = $this->BcAuth->authenticate['Form']['userModel'];
		$cookie = [];
		foreach($data[$userModel] as $key => $val) {
			// savedは除外
			if ($key !== 'saved') {
				$cookie[$key] = $val;
			}
		}
		$this->Cookie->httpOnly = true;
		$this->Cookie->write(
			Inflector::camelize(str_replace('.', '', BcAuthComponent::$sessionKey)),
			$cookie,
			true,
			'+2 weeks'
		);	// 3つめの'true'で暗号化
	}

	/**
	 * [ADMIN] 管理者ログアウト
	 *
	 * @return void
	 */
	public function admin_logout()
	{
		// EVENT Users.beforeLogout
		$event = $this->dispatchEvent('beforeLogout', [
			'user' => $this->BcAuth->user(),
		]);

		$logoutRedirect = $this->BcAuth->logout();
		$this->Cookie->delete(
			Inflector::camelize(str_replace('.', '', BcAuthComponent::$sessionKey))
		);
		$this->BcMessage->setInfo(__d('baser', 'ログアウトしました'));

		// EVENT Users.afterLogout
		$event = $this->dispatchEvent('afterLogout', [
			'logoutRedirect' => $logoutRedirect,
		]);
		if ($event !== false) {
			$logoutRedirect = $event->result === true ? $event->data['logoutRedirect'] : $event->result;
		}

		$this->redirect($logoutRedirect);
	}

	/**
	 * [ADMIN] ユーザーリスト
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
		$this->setViewConditions('User', ['default' => $default]);
		$conditions = $this->_createAdminIndexConditions($this->request->data);
		$options = [
			'conditions' => $conditions,
			'fields' => [],
			'order' => 'User.user_group_id,User.id',
			'limit' => $this->passedArgs['num']
		];

		// EVENT Users.searchIndex
		$event = $this->getEventManager()->dispatch(
			new CakeEvent(
				'Controller.Users.searchIndex',
				$this,
				[
					'options' => $options
				]
			)
		);
		if ($event === false) {
			$this->paginate = $options;
		} else {
			if ($event->result === null || $event->result === true) {
				$this->paginate = $event->data['options'];
			} else {
				$this->paginate = $event->result;
			}
		}

		$dbDatas = $this->paginate();

		if ($dbDatas) {
			$this->set('users', $dbDatas);
		}

		if ($this->request->is('ajax') || !empty($this->request->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		$this->subMenuElements = ['site_configs', 'users'];
		$this->pageTitle = __d('baser', 'ユーザー一覧');
		$this->search = 'users_index';
		$this->help = 'users_index';
	}

	/**
	 * ページ一覧用の検索条件を生成する
	 *
	 * @param array $data
	 * @return array $conditions
	 */
	protected function _createAdminIndexConditions($data)
	{
		$conditions = [];
		if (isset($data['User']['user_group_id']) && $data['User']['user_group_id'] !== '') {
			$conditions['User.user_group_id'] = $data['User']['user_group_id'];
		}
		if (!$conditions) {
			return [];
		}

		return $conditions;
	}

	/**
	 * [ADMIN] ユーザー情報登録
	 *
	 * @return void
	 */
	public function admin_add()
	{
		if (empty($this->request->data)) {
			$this->request->data = $this->User->getDefaultValue();
		} else {
			/* 登録処理 */
			$this->request->data['User']['password'] = $this->request->data['User']['password_1'];
			$this->User->create($this->request->data);

			if ($this->User->save()) {

				$this->request->data['User']['id'] = $this->User->id;
				$this->getEventManager()->dispatch(
					new CakeEvent(
						'Controller.Users.afterAdd',
						$this,
						[
							'user' => $this->request->data
						]
					)
				);

				$this->BcMessage->setSuccess(
					sprintf('ユーザー「%s」を追加しました。', $this->request->data['User']['name'])
				);
				$this->redirect(['action' => 'edit', $this->User->getInsertID()]);
			} else {
				$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			}
		}

		/* 表示設定 */
		$userGroups = $this->User->getControlSource('user_group_id');
		$user = $this->BcAuth->user();
		if ($user['user_group_id'] != Configure::read('BcApp.adminGroupId')) {
			unset($userGroups[1]);
		}

		$this->set('userGroups', $userGroups);
		$this->set('editable', true);
		$this->set('selfUpdate', false);
		$this->subMenuElements = ['site_configs', 'users'];
		$this->pageTitle = __d('baser', '新規ユーザー登録');
		$this->help = 'users_form';
		$this->render('form');
	}

	/**
	 * [ADMIN] ユーザー情報編集
	 *
	 * @param int user_id
	 * @return void
	 */
	public function admin_edit($id)
	{
		/* 除外処理 */
		if (!$id && empty($this->request->data)) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(['action' => 'index']);
		}

		$selfUpdate = false;
		$updatable = true;
		$user = $this->BcAuth->user();

		if (empty($this->request->data)) {

			$this->request->data = $this->User->read(null, $id);
			if ($user['id'] == $this->request->data['User']['id']) {
				$selfUpdate = true;
			}
		} else {

			if ($user['id'] == $this->request->data['User']['id']) {
				$selfUpdate = true;
			}

			// パスワードがない場合は更新しない
			if ($this->request->data['User']['password_1'] || $this->request->data['User']['password_2']) {
				$this->request->data['User']['password'] = $this->request->data['User']['password_1'];
			}

			// 非特権ユーザは該当ユーザの編集権限があるか確認
			if ($user['user_group_id'] !== Configure::read('BcApp.adminGroupId')) {
				if (!$this->UserGroup->Permission->check('/admin/users/edit/' . $this->request->data['User']['id'], $user['user_group_id'])) {
					$updatable = false;
				}
			}

			// 権限確認
			if (!$updatable) {
				$this->BcMessage->setError(__d('baser', '指定されたページへのアクセスは許可されていません。'));

			// 自身のアカウントは変更できないようにチェック
			} elseif ($selfUpdate && $user['user_group_id'] != $this->request->data['User']['user_group_id']) {
				$this->BcMessage->setError(__d('baser', '自分のアカウントのグループは変更できません。'));

			} else {

				$this->User->set($this->request->data);

				if ($this->User->save()) {

					$this->getEventManager()->dispatch(
						new CakeEvent(
							'Controller.Users.afterEdit',
							$this,
							[
								'user' => $this->request->data
							]
						)
					);

					if ($selfUpdate) {
						$this->admin_logout();
					}
					$this->BcMessage->setSuccess(
						sprintf('ユーザー「%s」を更新しました。', $this->request->data['User']['name'])
					);
					$this->redirect(['action' => 'edit', $id]);
				} else {

					// よく使う項目のデータを再セット
					$user = $this->User->find('first', ['conditions' => ['User.id' => $id]]);
					unset($user['User']);
					$this->request->data = array_merge($user, $this->request->data);
					$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
				}
			}
		}

		/* 表示設定 */
		$userGroups = $this->User->getControlSource('user_group_id');
		$editable = true;
		$deletable = true;

		if (@$user['user_group_id'] != Configure::read('BcApp.adminGroupId') && Configure::read('debug') !== -1) {
			$editable = false;
		} elseif ($selfUpdate && @$user['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
			$deletable = false;
		}

		$this->set(compact('userGroups', 'editable', 'selfUpdate', 'deletable'));
		$this->subMenuElements = ['site_configs', 'users'];
		$this->pageTitle = __d('baser', 'ユーザー情報編集');
		$this->help = 'users_form';
		$this->render('form');
	}

	/**
	 * [ADMIN] ユーザー情報削除　(ajax)
	 *
	 * @param int user_id
	 * @return void
	 */
	public function admin_ajax_delete($id = null)
	{
		$this->_checkSubmitToken();
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}

		// 最後のユーザーの場合は削除はできない
		if ($this->User->field('user_group_id', ['User.id' => $id]) == Configure::read('BcApp.adminGroupId') &&
			$this->User->find('count', ['conditions' => ['User.user_group_id' => Configure::read('BcApp.adminGroupId')]]) == 1) {
			$this->ajaxError(500, __d('baser', 'このユーザーは削除できません。'));
		}

		// メッセージ用にデータを取得
		$user = $this->User->read(null, $id);

		/* 削除処理 */
		if ($this->User->delete($id)) {
			$this->User->saveDbLog('ユーザー「' . $user['User']['name'] . '」を削除しました。');
			exit(true);
		}
		exit();
	}

	/**
	 * [ADMIN] ユーザー情報削除
	 *
	 * @param int user_id
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

		$admin_user_count = $this->User->find(
			'count',
			[
				'conditions' => [
					'User.user_group_id' => Configure::read('BcApp.adminGroupId')
				]
			]
		);
		// 最後のユーザーの場合は削除はできない
		if ($this->User->field('user_group_id', ['User.id' => $id]) == Configure::read('BcApp.adminGroupId') &&
			$admin_user_count == 1) {
			$this->BcMessage->setError(__d('baser', '最後の管理者ユーザーは削除する事はできません。'));
			$this->redirect(['action' => 'index']);
		}

		// メッセージ用にデータを取得
		$user = $this->User->read(null, $id);

		/* 削除処理 */
		if ($this->User->delete($id)) {
			$this->BcMessage->setSuccess(sprintf('ユーザー: %s を削除しました。', $user['User']['name']));
		} else {
			$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
		}

		$this->redirect(['action' => 'index']);
	}

	/**
	 * ログインパスワードをリセットする
	 * 新しいパスワードを生成し、指定したメールアドレス宛に送信する
	 *
	 * @return void
	 */
	public function admin_reset_password()
	{
		if (!$this->request->param('prefix') && !Configure::read('BcAuthPrefix.front')) {
			$this->notFound();
		}
		$this->BcAuth->logout();

		$this->pageTitle = __d('baser', 'パスワード再発行');
		$userModel = $this->BcAuth->authenticate['Form']['userModel'];
		if (strpos($userModel, '.') !== false) {
			list(, $userModel) = explode('.', $userModel);
		}
		$activate_key = $this->request->query('key');
		if (!$activate_key) {
			$this->BcMessage->setError('無効なリセットURLです。');
			$this->render('send_activate_url');
			return;
		}
		$user = $this->{$userModel}->find(
			'first', [
				'conditions' => [$userModel.'.activate_key' => $activate_key]
			]
		);
		if (!$user) {
			$this->BcMessage->setError('無効なリセットURLです。');
			$this->render('send_activate_url');
			return;
		}
		if(strtotime(Hash::get($user, $userModel.'.activate_expire', 0)) < env('REQUEST_TIME')) {
			$this->BcMessage->setError('有効期限切れのリセットURLです。');
			$this->render('send_activate_url');
			return;
		}
		$new_password = $this->generatePassword();
		$user[$userModel]['password'] = $new_password;
		$user[$userModel]['activate_key'] = null;
		$user[$userModel]['activate_expire'] = null;
		$this->{$userModel}->set($user);
		if (!$this->{$userModel}->save(null, ['validate' => false])) {
			$this->BcMessage->setError('新しいパスワードをデータベースに保存できませんでした。');
			$this->render('send_activate_url');
			return;
		}
		$this->set('new_password', $new_password);
		$this->BcAuth->login($user[$userModel]);
		$this->_set_auth($this->BcAuth->user());
	}
	/**
	 * パスワードリセットのためアクティベーション処理を行なう
	 * アクティベーション画面のURLを指定したメールアドレス宛に送信する
	 *
	 * @return void
	 */
	public function admin_send_activate_url() {
		if (!$this->request->param('prefix') && !Configure::read('BcAuthPrefix.front')) {
			$this->notFound();
		}
		if($this->BcAuth->user()) {
			$this->redirect(['controller' => 'dashboard', 'action' => 'index']);
		}
		$this->pageTitle = __d('baser', 'パスワードのリセット');

		if (!$this->request->is('post')) {
			return;
		}

		$userModel = $this->BcAuth->authenticate['Form']['userModel'];
		if(strpos($userModel, '.') !== false) {
			list(, $userModel) = explode('.', $userModel);
		}

		$email = $this->request->data($userModel . '.email') ?: '';

		if (!$email) {
			$this->BcMessage->setError('メールアドレスを入力してください。');
			return;
		}
		$find = $this->{$userModel}->find(
			'all',
			[
				'conditions' => [
					'email' => $email
				]
			]
		);
		if (!$find) {
			$this->BcMessage->setError('送信されたメールアドレスは登録されていません。');
			return;
		}
		$users = Hash::combine(
			Hash::remove($find, '{n}.User.password'),
			'{n}.User.id',
			'{n}.User'
		);
		$data = [];
		$activate_expire = date('Y-m-d H:i:s', strtotime(Configure::read('BcSecurity.passwordChangeExpire')));
		foreach ($users as $user_id => $user) {
			$activate_key = CakeText::uuid();
			$data[] = [
				'id'              => $user_id,
				'activate_key'    => $activate_key,
				'activate_expire' => $activate_expire
			];
			$users[$user_id]['activate_key'] = $activate_key;
		}
		$result = $this->{$userModel}->saveAll($data);
		if (!$result) {
			$this->BcMessage->setError('アクティベートメールを送信できませんでした。');
			return;
		}
		$result = $this->sendMail(
			$email,
			__d('baser', 'パスワード変更リクエストを受け付けました'),
			[
				'action_url' => Router::url(
					[
						'prefix'     => $this->request->param('prefix'),
						'controller' => 'users',
						'action'     => 'reset_password'
					]
					, true
				),
				'expire' => $activate_expire,
				'email'  => $email,
				'users'  => 1<count($users) ? $users : array_pop($users)
			],
			['template' => 1<count($users) ? 'send_activate_urls' : 'send_activate_url']
		);
		if (!$result) {
			$this->BcMessage->setError('メール送信時にエラーが発生しました。');
			return;
		}

		$this->BcMessage->setSuccess(
			$email . ' 宛てにパスワード再発行手順を送信しました。'
		);
		$this->render('sent_activate_url');
	}

}
