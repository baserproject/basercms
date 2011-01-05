<?php
/* SVN FILE: $Id$ */
/**
 * ユーザーコントローラー
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
 * Include files
 */
App::import('Helper','HtmlEx',true,BASER_HELPERS);
App::import('Helper','FormEx',true,BASER_HELPERS);
/**
 * ユーザーコントローラー
 *
 * ユーザーを管理するコントローラー。ログイン処理を担当する。
 *
 * @package			baser.controllers
 */
class UsersController extends AppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'Users';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('User','GlobalMenu','UserGroup');
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('HtmlEx','time','FormEx');
/**
 * コンポーネント
 *
 * @var 	array
 * @access 	public
 */
	var $components = array('Auth','Cookie','AuthConfigure', 'EmailEx');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('ユーザー管理'=>'/admin/users/index');
/**
 * beforeFilter
 *
 * @return	void
 * @access 	public
 */
	function beforeFilter() {

		/* 認証設定 */
		$this->Auth->allow('admin_login','admin_login_exec', 'admin_reset_password');
		parent::beforeFilter();
		if($this->params['prefix']=='admin'){
			$this->set('usePermission',$this->UserGroup->checkOtherAdmins());
		}

	}
/**
 * ログイン処理を行う
 * ・リダイレクトは行わない
 * ・requestActionから呼び出す
 * @return boolean
 */
	function admin_login_exec() {

		if(!$this->data) {
			return false;
		}
		if($this->Auth->login($this->data)) {
			return true;
		}
		return false;

	}
/**
 * [ADMIN] 管理者ログイン画面
 *
 * @return	void
 * @access 	public
 */
	function admin_login() {

		/* 他の画面からの遷移の処理 */
		if (empty($this->data)) {
			// セッションが残っている場合は自動ログイン
			if (isset($_SESSION['Auth']['User']['name'])) {
				$this->redirect($this->Auth->loginRedirect);
			}
			// クッキーがある場合には自動ログイン
			$cookie = $this->Cookie->read('Auth.User');
			if (!is_null($cookie)) {
				if ($this->Auth->login($cookie)) {
					$this->Session->del('Message.auth');
					$this->redirect($this->Auth->redirect());
				}
			}
		}

		/* ログインフォームからの処理 */
		$user = $this->Auth->user();
		if ($user) {
			if (!empty($this->data['User']['saved'])) {
				$cookie = array();
				$cookie['name'] = $this->data['User']['name'];
				$cookie['password'] = $this->data['User']['password'];				// ハッシュ化されている
				$this->Cookie->write('Auth.User', $cookie, true, '+2 weeks');	// 3つめの'true'で暗号化
				unset($this->data['User']['save']);
			}else {
				$this->Cookie->destroy();
			}
			$this->Session->setFlash("ようこそ、".$user['User']['real_name_1']." ".$user['User']['real_name_2']."　さん。");
			$this->redirect($this->Auth->redirect());
		}

		/* 表示設定 */
		$this->navis = array();
		$this->subMenuElements = '';
		$this->pageTitle = '管理者ログイン';

	}
/**
 * [ADMIN] 管理者ログアウト
 *
 * @return	void
 * @access 	public
 */
	function admin_logout() {

		$this->Auth->logout();
		$this->Cookie->del('Auth.User');
		$this->Session->setFlash('ログアウトしました');
		$this->redirect(array('action'=>'login'));

	}
/**
 * [ADMIN] ユーザーリスト
 *
 * @return	void
 * @access 	public
 */
	function admin_index() {

		/* データ取得 */
		$this->paginate = array('conditions'=>array(),
				'fields'=>array(),
				'order'=>'User.user_group_id,User.id',
				'limit'=>10
		);
		$dbDatas = $this->paginate('User');

		/* 表示設定 */
		if($dbDatas) {
			$this->set('users',$dbDatas);
		}
		$this->subMenuElements = array('users', 'user_groups', 'permissions');
		$this->pageTitle = 'ユーザー一覧';

	}
/**
 * [ADMIN] ユーザー情報登録
 *
 * @return	void
 * @access 	public
 */
	function admin_add() {

		if(empty($this->data)) {
			$this->data = $this->User->getDefaultValue();
		}else {

			/* 登録処理 */
			$this->User->create();
			$this->User->set($this->data);

			if($this->User->validates()) {
				$this->data['User']['password'] = $this->Auth->password($this->data['User']['password_1']);
				$this->User->save($this->data,false);
				$this->Session->setFlash('ユーザー「'.$this->data['User']['name'].'」を追加しました。');
				$this->User->saveDbLog('ユーザー「'.$this->data['User']['name'].'」を追加しました。');
				$this->redirect('/admin/users/index');
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('users', 'user_groups', 'permissions');
		$this->pageTitle = '新規ユーザー登録';
		$this->render('form');

	}
/**
 * [ADMIN] ユーザー情報編集
 *
 * @param	int		user_id
 * @return	void
 * @access 	public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		if(empty($this->data)) {
			$this->data = $this->User->read(null, $id);
		}else {

			/* 更新処理 */
			// パスワードがない場合は更新しない
			if(!$this->data['User']['password_1'] && !$this->data['User']['password_2']) {
				unset($this->data['User']['password_1']);
				unset($this->data['User']['password_2']);
			}else {
				$this->data['User']['password'] = $this->Auth->password($this->data['User']['password_1']);
			}

			$this->User->set($this->data);

			if($this->User->validates()) {
				$this->User->save($this->data,false);
				$this->Session->setFlash('ユーザー「'.$this->data['User']['name'].'」を更新しました。');
				$this->User->saveDbLog('ユーザー「'.$this->data['User']['name'].'」を更新しました。');
				$this->redirect(array('action'=>'admin_index'));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('users', 'user_groups', 'permissions');
		$this->pageTitle = 'ユーザー情報編集';
		$this->render('form');

	}
/**
 * [ADMIN] ユーザー情報削除
 *
 * @param	int		user_id
 * @return	void
 * @access 	public
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		// 最後のユーザーの場合は削除はできない
		if($this->User->field('user_group_id',array('User.id'=>$id)) == 1 &&
				$this->User->find('count',array('conditions'=>array('User.user_group_id'=>1))) == 1) {
			$this->Session->setFlash('最後の管理者ユーザーは削除する事はできません。');
			$this->redirect(array('action'=>'admin_index'));
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

		$this->redirect(array('action'=>'admin_index'));

	}
/**
 * ログインパスワードをリセットする
 *
 * 新しいパスワードを生成し、指定したメールアドレス宛に送信する
 * 
 * @return	void
 * @access	public
 */
	function admin_reset_password () {
		
		$this->layout = 'popup';
		$this->pageTitle = 'パスワードのリセット';
		
		if($this->data) {
			
			if(empty($this->data['User']['email'])) {
				$this->Session->setFlash('メールアドレスを入力してください。');
				return;
			}
			$email = $this->data['User']['email'];
			$user = $this->User->findByEmail($email);
			if(!$user) {
				$this->Session->setFlash('送信されたメールアドレスは登録されていません。');
				return;
			}
			$password = $this->generatePassword();
			$user['User']['password'] = $this->Auth->password($password);
			$this->User->set($user);
			if(!$this->User->save()) {
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