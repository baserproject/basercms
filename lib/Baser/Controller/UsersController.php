<?php

/**
 * ユーザーコントローラー
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
 * ユーザーコントローラー
 *
 * ユーザーを管理するコントローラー。ログイン処理を担当する。
 *
 * @package Baser.Controller
 */
class UsersController extends AppController {

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
	public $uses = array('User', 'Menu', 'UserGroup');

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcHtml', 'BcTime', 'BcForm');

/**
 * コンポーネント
 *
 * @var array
 */
	public $components = array('BcReplacePrefix', 'BcAuth', 'Cookie', 'BcAuthConfigure', 'BcEmail');

/**
 * サブメニューエレメント
 *
 * @var array
 */
	public $subMenuElements = array();

/**
 * ぱんくずナビ
 *
 * @var array
 */
	public $crumbs = array(
		array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')),
		array('name' => 'ユーザー管理', 'url' => array('controller' => 'users', 'action' => 'index'))
	);

/**
 * beforeFilter
 *
 * @return void
 */
	public function beforeFilter() {
		if (BC_INSTALLED) {
			/* 認証設定 */
			// parent::beforeFilterの前に記述する必要あり
			$this->BcAuth->allow(
				'admin_login', 'admin_logout', 'admin_login_exec', 'admin_reset_password', 'back_agent'
			);
			if(isset($this->UserGroup)) {
				$this->set('usePermission', $this->UserGroup->checkOtherAdmins());
			}
		}

		parent::beforeFilter();

		$this->BcReplacePrefix->allow('login', 'logout', 'login_exec', 'reset_password', 'back_agent');
	}

/**
 * ログイン処理を行う
 * ・リダイレクトは行わない
 * ・requestActionから呼び出す
 * 
 * @return boolean
 */
	public function admin_login_exec() {
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
	public function admin_login() {
		if ($this->BcAuth->loginAction != ('/' . $this->request->url)) {
			$this->notFound();
		}

		if ($this->request->data) {
			$this->BcAuth->login();
			$user = $this->BcAuth->user();
			$userModel = $this->BcAuth->authenticate['Form']['userModel'];
			if ($user && $this->isAuthorized($user)) {
				if (!empty($this->request->data[$userModel]['saved'])) {
					if (Configure::read('BcRequest.agentAlias') != 'mobile') {
						$this->setAuthCookie($this->request->data);
					} else {
						$this->BcAuth->saveSerial();
					}
					unset($this->request->data[$userModel]['save']);
				} else {
					$this->Cookie->destroy();
				}
				App::uses('BcBaserHelper', 'View/Helper');
				$BcBaser = new BcBaserHelper(new View());
				$this->setMessage("ようこそ、" . $BcBaser->getUserName($user) . "　さん。");
				$this->redirect($this->BcAuth->redirect());
			} else {
                $this->setMessage('アカウント名、パスワードが間違っています。', true);
            }
		}
		
		$pageTitle = 'ログイン';
		$prefixAuth = Configure::read('BcAuthPrefix.' . $this->request->params['prefix']);
		if ($prefixAuth && isset($prefixAuth['loginTitle'])) {
			$pageTitle = $prefixAuth['loginTitle'];
		}

		/* 表示設定 */
		$this->crumbs = array();
		$this->subMenuElements = '';
		$this->pageTitle = $pageTitle;
	}

/**
 * [ADMIN] 代理ログイン
 * 
 * @param int $id 
 * @return ダッシュボードへのURL
 */
	public function admin_ajax_agent_login($id) {
		if (!$this->Session->check('AuthAgent')) {
			$user = $this->BcAuth->user();
			$this->Session->write('AuthAgent', $user);
		}

		$result = $this->User->find('first', array('conditions' => array('User.id' => $id), 'recursive' => 0));
		$user = $result['User'];
		unset($user['password']);
		unset($result['User']);
		$user = array_merge($user, $result);
		Configure::write('debug', 0);
		if ($user) {
			$this->Session->renew();
			$this->Session->write(BcAuthComponent::$sessionKey, $user);
			exit(Router::url($this->BcAuth->redirect()));
		}
	}

/**
 * 代理ログインをしている場合、元のユーザーに戻る
 * 
 * @return void
 */
	public function back_agent() {
		$configs = Configure::read('BcAuthPrefix');
		if ($this->Session->check('AuthAgent')) {
			$data = $this->Session->read('AuthAgent');
			$authPrefix = explode(',', $data['UserGroup']['auth_prefix']);
			$authPrefix = $authPrefix[0];
			$this->Session->write('Auth.' . $configs[$authPrefix]['sessionKey'], $data);
			$this->Session->delete('AuthAgent');
			$this->setMessage('元のユーザーに戻りました。');
		} else {
			$this->setMessage('不正な操作です。', true);
			if (!empty($this->request->params['prefix'])) {
				$authPrefix = $this->request->params['prefix'];
			} else {
				$authPrefix = 'front';
			}
		}
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
	public function setAuthCookie($data) {
		$userModel = $this->BcAuth->authenticate['Form']['userModel'];
		$cookie = array();
		foreach($data[$userModel] as $key => $val) {
			// savedは除外
			if ($key !== "saved") {
				$cookie[$key] = $val;
			}
		}
		$this->Cookie->write(Inflector::camelize(str_replace('.', '', BcAuthComponent::$sessionKey)), $cookie, true, '+2 weeks');	// 3つめの'true'で暗号化
	}

/**
 * [ADMIN] 管理者ログアウト
 *
 * @return void
 */
	public function admin_logout() {
		$logoutRedirect = $this->BcAuth->logout();
		$this->Cookie->delete(Inflector::camelize(str_replace('.', '', BcAuthComponent::$sessionKey)));
		$this->setMessage('ログアウトしました');
		$this->redirect($logoutRedirect);
	}

/**
 * [ADMIN] ユーザーリスト
 *
 * @return void
 */
	public function admin_index() {
		/* データ取得 */
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('User', array('default' => $default));
		$conditions = $this->_createAdminIndexConditions($this->request->data);
		$this->paginate = array(
			'conditions' => $conditions,
			'fields' => array(),
			'order' => 'User.user_group_id,User.id',
			'limit' => $this->passedArgs['num']
		);
		$dbDatas = $this->paginate();

		if ($dbDatas) {
			$this->set('users', $dbDatas);
		}

		if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		$this->subMenuElements = array('users', 'user_groups');
		$this->pageTitle = 'ユーザー一覧';
		$this->search = 'users_index';
		$this->help = 'users_index';
	}

/**
 * ページ一覧用の検索条件を生成する
 *
 * @param array $data
 * @return array $conditions
 */
	protected function _createAdminIndexConditions($data) {
		unset($data['_Token']);
		if (isset($data['User']['user_group_id']) && $data['User']['user_group_id'] === '') {
			unset($data['User']['user_group_id']);
		}
		$conditions = $this->postConditions($data);
		if ($conditions) {
			return $conditions;
		} else {
			return array();
		}
	}

/**
 * [ADMIN] ユーザー情報登録
 *
 * @return void
 */
	public function admin_add() {
		if (empty($this->request->data)) {
			$this->request->data = $this->User->getDefaultValue();
		} else {
			/* 登録処理 */
			$this->request->data['User']['password'] = $this->request->data['User']['password_1'];
			$this->User->create($this->request->data);

			if ($this->User->save()) {

				$this->request->data['User']['id'] = $this->User->id;
				$this->getEventManager()->dispatch(new CakeEvent('Controller.Users.afterAdd', $this, array(
					'user' => $this->request->data
				)));

				$this->setMessage('ユーザー「' . $this->request->data['User']['name'] . '」を追加しました。', false, true);
				$this->redirect(array('action' => 'edit', $this->User->getInsertID()));
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
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
		$this->subMenuElements = array('users', 'user_groups');
		$this->pageTitle = '新規ユーザー登録';
		$this->help = 'users_form';
		$this->render('form');
	}

/**
 * [ADMIN] ユーザー情報編集
 *
 * @param int user_id
 * @return void
 */
	public function admin_edit($id) {
		/* 除外処理 */
		if (!$id && empty($this->request->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
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
				$this->setMessage('指定されたページへのアクセスは許可されていません。', true);

			// 自身のアカウントは変更出来ないようにチェック
			} elseif ($selfUpdate && $user['user_group_id'] != $this->request->data['User']['user_group_id']) {
				$this->setMessage('自分のアカウントのグループは変更できません。', true);

			} else {

				$this->User->set($this->request->data);

				if ($this->User->save()) {

					$this->getEventManager()->dispatch(new CakeEvent('Controller.Users.afterEdit', $this, array(
						'user' => $this->request->data
					)));

					if ($selfUpdate) {
						$this->admin_logout();
					}
					$this->setMessage('ユーザー「' . $this->request->data['User']['name'] . '」を更新しました。', false, true);
					$this->redirect(array('action' => 'edit', $id));
				} else {

					// よく使う項目のデータを再セット
					$user = $this->User->find('first', array('conditions' => array('User.id' => $id)));
					unset($user['User']);
					$this->request->data = array_merge($user, $this->request->data);
					$this->setMessage('入力エラーです。内容を修正してください。', true);
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
		$this->subMenuElements = array('users', 'user_groups');
		$this->pageTitle = 'ユーザー情報編集';
		$this->help = 'users_form';
		$this->render('form');
	}

/**
 * [ADMIN] ユーザー情報削除　(ajax)
 *
 * @param int user_id
 * @return void
 */
	public function admin_ajax_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// 最後のユーザーの場合は削除はできない
		if ($this->User->field('user_group_id', array('User.id' => $id)) == Configure::read('BcApp.adminGroupId') &&
			$this->User->find('count', array('conditions' => array('User.user_group_id' => Configure::read('BcApp.adminGroupId')))) == 1) {
			$this->ajaxError(500, 'このユーザーは削除できません。');
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
	public function admin_delete($id = null) {
		/* 除外処理 */
		if (!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// 最後のユーザーの場合は削除はできない
		if ($this->User->field('user_group_id', array('User.id' => $id)) == Configure::read('BcApp.adminGroupId') &&
			$this->User->find('count', array('conditions' => array('User.user_group_id' => Configure::read('BcApp.adminGroupId')))) == 1) {
			$this->setMessage('最後の管理者ユーザーは削除する事はできません。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$user = $this->User->read(null, $id);

		/* 削除処理 */
		if ($this->User->delete($id)) {
			$this->setMessage('ユーザー: ' . $user['User']['name'] . ' を削除しました。', true, false);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));
	}

/**
 * ログインパスワードをリセットする
 * 新しいパスワードを生成し、指定したメールアドレス宛に送信する
 *
 * @return void
 */
	public function admin_reset_password() {
		if ((empty($this->params['prefix']) && !Configure::read('BcAuthPrefix.front'))) {
			$this->notFound();
		}
		if($this->BcAuth->user()) {
			$this->redirect(array('controller' => 'dashboard', 'action' => 'index'));
		}
		$this->pageTitle = 'パスワードのリセット';
		$userModel = $this->BcAuth->authenticate['Form']['userModel'];
		if ($this->request->data) {

			if (empty($this->request->data[$userModel]['email'])) {
				$this->Session->setFlash('メールアドレスを入力してください。');
				return;
			}
			$email = trim($this->request->data[$userModel]['email']);
			$user = $this->{$userModel}->findByEmail($email);
			if (!$user) {
				$this->Session->setFlash('送信されたメールアドレスは登録されていません。');
				return;
			}
			$password = $this->generatePassword();
			$user[$userModel]['password'] = $password;
			$this->{$userModel}->set($user);
			if (!$this->{$userModel}->save()) {
				$this->Session->setFlash('新しいパスワードをデータベースに保存できませんでした。');
				return;
			}
			$body = $email . ' の新しいパスワードは、 ' . $password . ' です。';
			if (!$this->sendMail($email, 'パスワードを変更しました', $body)) {
				$this->Session->setFlash('メール送信時にエラーが発生しました。');
				return;
			}
			$this->Session->setFlash($email . ' 宛に新しいパスワードを送信しました。');
			$this->request->data = array();
		}
	}

}
