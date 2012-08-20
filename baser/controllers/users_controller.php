<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
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
				'admin_ajax_login',
				'admin_login_exec',
				'admin_reset_password'
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
				Configure::write('BcAuthPrefix.'.$prefix.'.loginAction', '/'.$prefix.'/users/ajax_login');
			}
		} else {
			if($this->RequestHandler->isAjax() && $this->action == 'ajax_login') {
				Configure::write('BcAuthPrefix.front.loginAction', '/users/ajax_login');
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
				$this->Session->setFlash("ようこそ、".$user[$userModel]['real_name_1']." ".$user[$userModel]['real_name_2']."　さん。");
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
		$this->data = $this->User->find('first', array('conditions' => array('User.id' => $id), 'recursive' => -1));
		Configure::write('debug', 0);
		$this->setAction('admin_ajax_login');
		exit();
	}
/**
 * 代理ログインをしている場合、元のユーザーに戻る
 * 
 * @return void
 * @access public 
 */
	function admin_back_agent() {
		if($this->Session->check('AuthAgent')) {
			$this->Session->write($this->BcAuth->sessionKey, $this->Session->read('AuthAgent.'.$this->BcAuth->userModel));
			$this->Session->delete('AuthAgent');
			$this->Session->setFlash('元のユーザーに戻りました。');
		} else {
			$this->Session->setFlash('不正な操作です。');
		}
		$this->redirect('/admin');
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
				$this->Session->setFlash("ようこそ、".$user[$userModel]['real_name_1']." ".$user[$userModel]['real_name_2']."　さん。");
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
		$this->Cookie->write('Auth.'.$userModel, $cookie, true, '+2 weeks');	// 3つめの'true'で暗号化

	}
/**
 * [ADMIN] 管理者ログアウト
 *
 * @return void
 * @access public
 */
	function admin_logout() {

		$userModel = $this->BcAuth->userModel;
		$this->BcAuth->logout();
		$this->Cookie->del('Auth.'.$userModel);
		$this->Session->setFlash('ログアウトしました');
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
				$this->Session->setFlash('ユーザー「'.$this->data['User']['name'].'」を追加しました。');
				$this->User->saveDbLog('ユーザー「'.$this->data['User']['name'].'」を追加しました。');
				$this->redirect(array('action' => 'edit', $this->User->getInsertID()));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$userGroups = $this->User->getControlSource('user_group_id');
		$editable = true;
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		if($user[$userModel]['user_group_id'] != 1) {
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
 * @param int user_id
 * @return void
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
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

				$this->Session->setFlash('ユーザー「'.$this->data['User']['name'].'」を更新しました。');
				$this->User->saveDbLog('ユーザー「'.$this->data['User']['name'].'」を更新しました。');
				$this->redirect(array('action' => 'edit', $id));
			}else {
				// よく使う項目のデータを再セット
				$this->data = array_merge($this->User->read(null, $id), $this->data);
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$userGroups = $this->User->getControlSource('user_group_id');
		$editable = true;
		$user = $this->BcAuth->user();
		$userModel = $this->getUserModel();
		if($user[$userModel]['user_group_id'] != 1 && Configure::read('debug') !== -1) {
			if($this->data['User']['user_group_id'] == 1) {
				$editable = false;
			} else {
				unset($userGroups[1]);
			}
		}
		
		$this->set('userGroups', $userGroups);
		$this->set('editable', $editable);
		$this->set('selfUpdate', $selfUpdate);
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
		if($this->User->field('user_group_id',array('User.id'=>$id)) == 1 &&
				$this->User->find('count',array('conditions'=>array('User.user_group_id'=>1))) == 1) {
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
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'index'));
		}

		// 最後のユーザーの場合は削除はできない
		if($this->User->field('user_group_id',array('User.id'=>$id)) == 1 &&
				$this->User->find('count',array('conditions'=>array('User.user_group_id'=>1))) == 1) {
			$this->Session->setFlash('最後の管理者ユーザーは削除する事はできません。');
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$user = $this->User->read(null, $id);

		/* 削除処理 */
		if($this->User->del($id)) {
			$this->Session->setFlash('ユーザー: '.$user['User']['name'].' を削除しました。');
			$this->User->saveDbLog('ユーザー「'.$user['User']['name'].'」を削除しました。');
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
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

		$this->pageTitle = 'パスワードのリセット';
		$userModel = $this->BcAuth->userModel;
		if($this->data) {

			if(empty($this->data[$userModel]['email'])) {
				$this->Session->setFlash('メールアドレスを入力してください。');
				return;
			}
			$email = $this->data[$userModel]['email'];
			$user = $this->{$userModel}->findByEmail($email);
			if(!$user) {
				$this->Session->setFlash('送信されたメールアドレスは登録されていません。');
				return;
			}
			$password = $this->generatePassword();
			$user['User']['password'] = $this->BcAuth->password($password);
			$this->{$userModel}->set($user);
			if(!$this->{$userModel}->save()) {
				$this->Session->setFlash('新しいパスワードをデータベースに保存できませんでした。');
				return;
			}
			$body = $email.' の新しいパスワードは、 '.$password.' です。';
			if(!$this->sendMail($email, 'パスワードを変更しました', $body)) {
				$this->Session->setFlash('メール送信時にエラーが発生しました。');
				return;
			}
			$this->Session->setFlash($email.' 宛に新しいパスワードを送信しました。');
			$this->data = array();

		}

	}
	
}
?>