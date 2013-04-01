<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * ユーザーコントローラー
 *
 * ユーザーを管理するコントローラー。ログイン処理を担当する。
 *
 * @property BcAuthComponent BcAuth
 * @property BcAuthConfigureComponent BcAuthConfigure
 * @property BcReplacePrefixComponent BcReplacePrefix
 * @property RequestHandlerComponent RequestHandler
 * @property CookieComponent Cookie
 * @property SessionComponent Session
 * @property UserGroup UserGrou
 * @property User User
 * @package baser.controllers
 */
class UsersController extends AppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Users';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('User','GlobalMenu','UserGroup');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_HTML_HELPER, BC_TIME_HELPER, BC_FORM_HELPER);
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcReplacePrefix', 'BcAuth','Cookie','BcAuthConfigure', 'BcEmail');
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
	var $crumbs = array(
		array('name' => 'システム設定', 'url' => array('controller' => 'site_configs', 'action' => 'form')),
		array('name' => 'ユーザー管理', 'url' => array('controller' => 'users', 'action' => 'index'))
	);
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {

		/* 認証設定 */
		// parent::beforeFilterの前に記述する必要あり
		$this->BcAuth->allow(
				'admin_login', 
				'admin_logout', 
				'admin_login_exec', 
				'admin_reset_password',
				'admin_ajax_login'
		);
		$this->set('usePermission',$this->UserGroup->checkOtherAdmins());
		
		// =====================================================================
		// Ajaxによるログインの場合、loginAction が、表示URLと違う為、
		// BcAuthコンポーネントよりコントローラーのisAuthorized を呼びだせない。
		// 正常な動作となるように書き換える。
		// =====================================================================
		if(!empty($this->params['prefix'])) {
			$prefix = $this->params['prefix'];
			if($this->RequestHandler->isAjax() && $this->action == $prefix.'_ajax_login') {
				Configure::write('BcAuthPrefix.'.$prefix.'.loginAction', '/'.$prefix.'/' . $this->params['controller'] . '/ajax_login');
			}
		} else {
			if($this->RequestHandler->isAjax() && $this->action == 'ajax_login') {
				Configure::write('BcAuthPrefix.front.loginAction', '/' . $this->params['controller'] . '/ajax_login');
			}
		}
		
		parent::beforeFilter();

		$this->BcReplacePrefix->allow('login', 'logout', 'login_exec', 'reset_password', 'ajax_login');

	}
/**
 * ログイン処理を行う
 * ・リダイレクトは行わない
 * ・requestActionから呼び出す
 * 
 * @return boolean
 * @access public
 */
	function admin_login_exec() {

		if(!$this->data) {
			return false;
		}
		if($this->BcAuth->login($this->data)) {
			$this->BcAuthConfigure->setSessionAuthPrefix();
			return true;
		}
		return false;

	}
/**
 * [ADMIN] 管理者ログイン画面
 *
 * @return void
 * @access public
 */
	function admin_login() {
		
		if($this->BcAuth->loginAction != ('/'.$this->params['url']['url'])) {
			$this->notFound();
		}
		
		$user = $this->BcAuth->user();
		$userModel = $this->BcAuth->userModel;
		
		if($this->data) {
			if ($user) {
				$this->BcAuthConfigure->setSessionAuthPrefix();
				if (!empty($this->data[$userModel]['saved'])) {
					if(Configure::read('BcRequest.agentAlias') != 'mobile') {
						$this->setAuthCookie($this->data);
					} else {
						$this->BcAuth->saveSerial();
					}
					unset($this->data[$userModel]['save']);
				}else {
					$this->Cookie->destroy();
				}
				App::import('Helper', 'BcBaser');
				$BcBaser = new BcBaserHelper();
				$this->setMessage("ようこそ、" . $BcBaser->getUserName($user) . "　さん。");
			}
		}

		if ($user) {
			$this->redirect($this->BcAuth->redirect());
		} else {
			if($this->data) {
				$this->redirect($this->referer());
			}
		}

		$pageTitle = 'ログイン';
		if(!empty($this->params['prefix'])) {
			$prefixAuth = Configure::read('BcAuthPrefix.'.$this->params['prefix']);
		} else {
			$prefixAuth = Configure::read('BcAuthPrefix.front');
		}
		if($prefixAuth && isset($prefixAuth['loginTitle'])) {
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
 * @access public
 */
	function admin_ajax_agent_login($id) {
		
		if(!$this->Session->check('AuthAgent')) {
			$user = $this->BcAuth->user();
			$this->Session->write('AuthAgent', $user);
		}
		$this->data = $this->User->find('first', array('conditions' => array('User.id' => $id), 'recursive' => 0));
		Configure::write('debug', 0);
		$configs = Configure::read('BcAuthPrefix');
		$config = $configs[$this->data['UserGroup']['auth_prefix']];
		$config['auth_prefix'] = $this->data['UserGroup']['auth_prefix'];
		$this->BcAuthConfigure->setting($config);
		$this->setAction('admin_ajax_login');
		exit();
		
	}
/**
 * 代理ログインをしている場合、元のユーザーに戻る
 * 
 * @return void
 * @access public 
 */
	function back_agent() {
		
		$configs = Configure::read('BcAuthPrefix');
		if($this->Session->check('AuthAgent')) {
			$data = $this->Session->read('AuthAgent.'.$this->BcAuth->userModel);
			$this->Session->write($this->BcAuth->sessionKey, $data);
			$this->Session->delete('AuthAgent');
			$this->setMessage('元のユーザーに戻りました。');
			$authPrefix = $data['authPrefix'];
		} else {
			$this->setMessage('不正な操作です。', true);
			if(!empty($this->params['prefix'])) {
				$authPrefix = $this->params['prefix'];
			} else {
				$authPrefix = 'front';
			}
		}

		if(!empty($configs[$authPrefix])) {
			$redirect = $configs[$authPrefix]['loginRedirect'];
		} else {
			$redirect = '/';
		}
		
		$this->redirect($redirect);
		
	}
/**
 * [ADMIN] 管理者ログイン画面（Ajax）
 *
 * @return void
 * @access public
 */
	function admin_ajax_login() {
		
		if(!$this->BcAuth->login($this->data)) {
			$this->ajaxError(500, 'アカウント名、パスワードが間違っています。');
		}
		
		$this->BcAuthConfigure->setSessionAuthPrefix();
		$user = $this->BcAuth->user();
		$userModel = $this->BcAuth->userModel;
		
		if($this->data) {
			if ($user) {
				if (!empty($this->data[$userModel]['saved'])) {
					if(Configure::read('BcRequest.agentAlias') != 'mobile') {
						$this->setAuthCookie($this->data);
					} else {
						$this->BcAuth->saveSerial();
					}
					unset($this->data[$userModel]['save']);
				}else {
					$this->Cookie->destroy();
				}
				App::import('Helper', 'BcBaser');
				$BcBaser = new BcBaserHelper();
				$this->setMessage("ようこそ、" . $BcBaser->getUserName($user) . "　さん。");
			}
		}

		Configure::write('debug', 0);
		
		if ($user) {
			exit(Router::url($this->BcAuth->redirect()));
		}
		
		exit();
		
	}
/**
 * 認証クッキーをセットする
 *
 * @param array $data
 * @return void
 * @access public
 */
	function setAuthCookie($data) {
		
		$userModel = $this->BcAuth->userModel;
		$cookie = array();
		$cookie['name'] = $data[$userModel]['name'];
		$cookie['password'] = $data[$userModel]['password'];				// ハッシュ化されている
		$this->Cookie->write($this->BcAuth->sessionKey, $cookie, true, '+2 weeks');	// 3つめの'true'で暗号化

	}
/**
 * [ADMIN] 管理者ログアウト
 *
 * @return void
 * @access public
 */
	function admin_logout() {

		$this->BcAuth->logout();
		$this->Cookie->del($this->BcAuth->sessionKey);
		$this->setMessage('ログアウトしました');
		if(empty($this->params['prefix'])) {
			$this->redirect(array('action' => 'login'));
		} else {
			$this->redirect(array($this->params['prefix'] => true, 'action' => 'login'));
		}

	}
/**
 * [ADMIN] ユーザーリスト
 *
 * @return void
 * @access public
 */
	function admin_index() {

		/* データ取得 */
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('User', array('default' => $default));
		$conditions = $this->_createAdminIndexConditions($this->data);
		$this->paginate = array(
			'conditions'=>$conditions,
				'fields'=>array(),
				'order'=>'User.user_group_id,User.id',
				'limit'=>$this->passedArgs['num']
		);
		$dbDatas = $this->paginate();

		if($dbDatas) {
			$this->set('users',$dbDatas);
		}
		
		if($this->RequestHandler->isAjax() || !empty($this->params['url']['ajax'])) {
			$this->render('ajax_index');
			return;
		}
		
		$this->subMenuElements = array('site_configs', 'users', 'user_groups');
		$this->pageTitle = 'ユーザー一覧';
		$this->search = 'users_index';
		$this->help = 'users_index';
		
	}
/**
 * ページ一覧用の検索条件を生成する
 *
 * @param array $data
 * @return array $conditions
 * @access protected
 */
	function _createAdminIndexConditions($data) {

		unset($data['_Token']);
		if(isset($data['User']['user_group_id']) && $data['User']['user_group_id'] === '') {
			unset($data['User']['user_group_id']);
		}
		$conditions = $this->postConditions($data);
		if($conditions) {
			return $conditions;
		} else {
			return array();
		}

	}
/**
 * [ADMIN] ユーザー情報登録
 *
 * @return void
 * @access public
 */
	function admin_add() {

		if(empty($this->data)) {
			$this->data = $this->User->getDefaultValue();
		}else {

			/* 登録処理 */
			$this->data['User']['password'] = $this->data['User']['password_1'];
			
			$this->User->create($this->data);
			
			if($this->User->validates()) {
				unset($this->data['User']['password_1']);
				unset($this->data['User']['password_2']);
				if(isset($this->data['User']['password'])) {
					$this->data['User']['password'] = $this->BcAuth->password($this->data['User']['password']);
				}
				$this->User->save($this->data,false);
				$this->setMessage('ユーザー「'.$this->data['User']['name'].'」を追加しました。', false, true);
				$this->redirect(array('action' => 'edit', $this->User->getInsertID()));
			}else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}

		}

		/* 表示設定 */
		$userGroups = $this->User->getControlSource('user_group_id');
		$editable = true;
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		if($user[$userModel]['user_group_id'] != Configure::read('BcApp.adminGroupId')) {
			unset($userGroups[1]);
		}
		
		$this->set('userGroups', $userGroups);
		$this->set('editable', true);
		$this->subMenuElements = array('site_configs', 'users', 'user_groups');
		$this->pageTitle = '新規ユーザー登録';
		$this->help = 'users_form';
		$this->render('form');

	}
/**
 * [ADMIN] ユーザー情報編集
 *
 * @param int $id
 * @return void
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		$selfUpdate = false;
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		
		if(empty($this->data)) {
			$this->data = $this->User->read(null, $id);
			if($user[$userModel]['id'] == $this->data['User']['id']) {
				$selfUpdate = true;
			}
		}else {
			if($user[$userModel]['id'] == $this->data['User']['id']) {
				$selfUpdate = true;
			}
			/* 更新処理 */
			// パスワードがない場合は更新しない
			if($this->data['User']['password_1'] || $this->data['User']['password_2']) {
				$this->data['User']['password'] = $this->data['User']['password_1'];
			}

			// 自身のアカウントは変更出来ないようにチェック
			if ($selfUpdate && $user[$userModel]['user_group_id'] != $this->data[$userModel]['user_group_id']) {
				$this->setMessage('自分のアカウントのグループは変更できません。', true);
			} else {
				$this->User->set($this->data);

				if($this->User->validates()) {
					unset($this->data['User']['password_1']);
					unset($this->data['User']['password_2']);
					if(isset($this->data['User']['password'])) {
						$this->data['User']['password'] = $this->BcAuth->password($this->data['User']['password']);
					}
					$this->User->save($this->data,false);
					
					if($selfUpdate) {
						$this->admin_logout();
					}

					$this->setMessage('ユーザー「'.$this->data['User']['name'].'」を更新しました。', false, true);
					$this->redirect(array('action' => 'edit', $id));
				}else {
					// よく使う項目のデータを再セット
					$this->data = array_merge($this->User->read(null, $id), $this->data);
					$this->setMessage('入力エラーです。内容を修正してください。', true);
				}
			}
		}

		/* 表示設定 */
		$userGroups = $this->User->getControlSource('user_group_id');
		$editable = true;
		
		if($user[$userModel]['user_group_id'] != Configure::read('BcApp.adminGroupId') && Configure::read('debug') !== -1) {
			$editable = false;
		} else if ($selfUpdate && $user[$userModel]['user_group_id'] == Configure::read('BcApp.adminGroupId')) {
			$editable = false;
		}
		$this->set(compact('userGroups', 'editable', 'selfUpdate'));
		$this->subMenuElements = array('site_configs', 'users', 'user_groups');
		$this->pageTitle = 'ユーザー情報編集';
		$this->help = 'users_form';
		$this->render('form');

	}
/**
 * [ADMIN] ユーザー情報削除　(ajax)
 *
 * @param int user_id
 * @return void
 * @access public
 */
	function admin_ajax_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// 最後のユーザーの場合は削除はできない
		if($this->User->field('user_group_id', array('User.id'=>$id)) == Configure::read('BcApp.adminGroupId') &&
				$this->User->find('count', array('conditions' => array('User.user_group_id' => Configure::read('BcApp.adminGroupId')))) == 1) {
			$this->ajaxError(500, 'このユーザーは削除できません。');
		}

		// メッセージ用にデータを取得
		$user = $this->User->read(null, $id);

		/* 削除処理 */
		if($this->User->del($id)) {
			$this->User->saveDbLog('ユーザー「'.$user['User']['name'].'」を削除しました。');
			exit(true);
		}

		exit();

	}
	/**
 * [ADMIN] ユーザー情報削除
 *
 * @param int user_id
 * @return void
 * @access public
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// 最後のユーザーの場合は削除はできない
		if($this->User->field('user_group_id', array('User.id' => $id)) == Configure::read('BcApp.adminGroupId') &&
				$this->User->find('count', array('conditions' => array('User.user_group_id' => Configure::read('BcApp.adminGroupId')))) == 1) {
			$this->setMessage('最後の管理者ユーザーは削除する事はできません。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$user = $this->User->read(null, $id);

		/* 削除処理 */
		if($this->User->del($id)) {
			$this->setMessage('ユーザー: '.$user['User']['name'].' を削除しました。', true, false);
		}else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}

		$this->redirect(array('action' => 'index'));

	}
/**
 * ログインパスワードをリセットする
 * 新しいパスワードを生成し、指定したメールアドレス宛に送信する
 *
 * @return void
 * @access public
 */
	function admin_reset_password () {
		
		if(empty($this->params['prefix']) && !Configure::read('BcAuthPrefix.front')) {
			$this->notFound();
		}
		
		$this->pageTitle = 'パスワードのリセット';
		$userModel = $this->BcAuth->userModel;
		if($this->data) {

			if(empty($this->data[$userModel]['email'])) {
				$this->setMessage('メールアドレスを入力してください。', true);
				return;
			}
			$email = $this->data[$userModel]['email'];
			$user = $this->{$userModel}->findByEmail($email);
			if(!$user) {
				$this->setMessage('送信されたメールアドレスは登録されていません。', true);
				return;
			}
			$password = $this->generatePassword();
			$user['User']['password'] = $this->BcAuth->password($password);
			$this->{$userModel}->set($user);
			if(!$this->{$userModel}->save()) {
				$this->setMessage('新しいパスワードをデータベースに保存できませんでした。', true);
				return;
			}
			$body = $email.' の新しいパスワードは、 '.$password.' です。';
			if(!$this->sendMail($email, 'パスワードを変更しました', $body)) {
				$this->setMessage('メール送信時にエラーが発生しました。', true);
				return;
			}
			$this->setMessage($email.' 宛に新しいパスワードを送信しました。');
			$this->data = array();

		}

	}
	
}
