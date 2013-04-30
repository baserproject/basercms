<?php
/* SVN FILE: $Id$ */
/**
 * お問い合わせメールフォーム用コントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.controller
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
/**
 * お問い合わせメールフォーム用コントローラー
 *
 * @package baser.plugins.mail.controller
 */
class MailController extends MailAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Mail';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Mail.Message','Mail.MailContent','Mail.MailField','Mail.MailConfig');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array(
		BC_FREEZE_HELPER, 'Mail.Mailform', 'Javascript', 
		BC_ARRAY_HELPER, BC_TIME_HELPER, 'Mail.Maildata', 'Mail.Mailfield', 'Mail.Mail'
	);
/**
 * Array of components a controller will use
 *
 * @var array
 * @access public
 */
	// PHP4の場合、メールフォームの部品が別エレメントになった場合、利用するヘルパが別インスタンスとなってしまう様子。
	// そのためSecurityコンポーネントが利用できない
	// 同じエレメント内で全てのフォーム部品を完結できればよいがその場合デザインの自由度が失われてしまう。
	// var $components = array('Email','BcEmail','Security','BcCaptcha');
	// 
	// 2013/03/14 ryuring
	// baserCMS２系より必須要件をPHP5以上とした為、SecurityComponent を標準で設定する方針に変更
	var $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'Email', 'BcEmail', 'BcCaptcha', 'Security');
/**
 * CSS
 *
 * @var array
 * @access public
 */
	var $css = array('mail/form');
/**
 * ページタイトル
 *
 * @var string
 * @access public
 */
	var $pageTitle = 'お問い合わせ';
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * データベースデータ
 *
 * @var array
 * @access public
 */
	var $dbDatas = null;
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $crumbs = array();
/**
 * beforeFilter.
 *
 * @return void
 * @access public
 */
	function beforeFilter() {

		/* 認証設定 */
		$this->BcAuth->allow(
				'index', 'mobile_index', 'smartphone_index',
				'confirm', 'mobile_confirm', 'smartphone_confirm',
				'submit', 'mobile_submit', 'smartphone_submit', 
				'captcha', 'smartphone_captcha'
		);

		parent::beforeFilter();

		// バリデーション自動生成用にメールフォームIDを設定
		if(!empty($this->contentId)) {
			$id = $this->contentId;
		}elseif(!empty($this->params['pass'][0]) && is_numeric($this->params['pass'][0])) {
			$id = $this->params['pass'][0];
		}else {
			$id = 1;
		}


		$this->dbDatas['mailContent'] = $this->MailContent->find(array("id"=>$id));
		$this->dbDatas['mailConfig'] = $this->MailConfig->find();
		$this->Message->mailFields = $this->dbDatas['mailFields'] = $this->MailField->find('all', array('conditions' => array("mail_content_id"=>$id), 'order' => 'MailField.sort'));

		// ページタイトルをセット
		$this->pageTitle = $this->dbDatas['mailContent']['MailContent']['title'];
		// レイアウトをセット
		$this->layout = $this->dbDatas['mailContent']['MailContent']['layout_template'];

		if(empty($this->contentId)) {
			$this->contentId = $this->params['pass'][0];
		}

		$this->subMenuElements = array('default');

		// 2013/03/14 ryuring
		// baserCMS２系より必須要件をPHP5以上とした為、SecurityComponent を標準で設定する方針に変更
		$this->Security->enabled = true;
		$this->Security->requireAuth('confirm', 'submit');
		$this->Security->validatePost = true;
		$this->Security->disabledFields[] = 'Message.mode';
		
		// SSL設定
		if($this->dbDatas['mailContent']['MailContent']['ssl_on']) {
			$this->Security->blackHoleCallback = '_sslFail';
			$this->Security->requireSecure = am($this->Security->requireSecure, array('index', 'confirm', 'submit'));
		}

		// 複数のメールフォームに対応する為、プレフィックス付のCSVファイルに保存。
		// ※ nameフィールドの名称を[message]以外にする
		if($this->dbDatas['mailContent']['MailContent']['name'] != 'message') {
			$prefix = $this->dbDatas['mailContent']['MailContent']['name']."_";
			$this->Message = new Message(false,null,null,$prefix);
			$this->Message->mailFields = $this->dbDatas['mailFields'];
		}

	}
/**
 * beforeRender
 *
 * @return void
 * @access public
 */
	function beforeRender() {

		parent::beforeRender();
		if($this->dbDatas['mailContent']['MailContent']['widget_area']){
			$this->set('widgetArea',$this->dbDatas['mailContent']['MailContent']['widget_area']);
		}

	}
/**
 * [PUBIC] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	function index($id = null) {

		if(!$this->dbDatas['mailContent']['MailContent']['status']) {
			$this->notFound();
		}
		
		// 初期値を取得
		if(!isset($this->data['Message'])) {
			$this->data = $this->Message->getDefaultValue();
		}else {
			$this->data['Message'] = $this->Message->sanitizeData($this->data['Message']);
		}

		$this->set('freezed',false);

		if($this->dbDatas['mailFields'])
			$this->set('mailFields',$this->dbDatas['mailFields']);

		$user = $this->BcAuth->user();
		if(!empty($user) && !Configure::read('BcRequest.agent')) {
			$this->set('editLink', array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'edit', $this->dbDatas['mailContent']['MailContent']['id']));
		}
		
		$this->set('mailContent',$this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'].DS.'index');

	}
/**
 * [MOBILE] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	function mobile_index($id=null) {

		$this->setAction('index',$id);

	}
/**
 * [SMARTPHONE] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	function smartphone_index($id=null) {

		$this->setAction('index',$id);

	}
/**
 * [PUBIC] データの確認画面を表示
 *
 * @param mixed	mail_content_id
 * @return void
 * @access public
 */
	function confirm($id = null) {

		if(!$this->dbDatas['mailContent']['MailContent']['status']) {
			$this->notFound();
		}
		
		if(!$this->data) {
			$this->redirect(array('action' => 'index', $id));
		}else {
			// 入力データを整形し、モデルに引き渡す
			$this->data = $this->Message->create($this->Message->autoConvert($this->data));

			// 画像認証を行う
			if(Configure::read('BcRequest.agent') != 'mobile' && $this->dbDatas['mailContent']['MailContent']['auth_captcha']){
				$captchaResult = $this->BcCaptcha->check($this->data['Message']['auth_captcha']);
				if(!$captchaResult){
					$this->Message->invalidate('auth_captcha');
				}
			}

			// データの入力チェックを行う
			if($this->Message->validates()) {
				$this->set('freezed',true);
			}else {
				$this->set('freezed',false);
				$this->set('error',true);

				$this->setMessage('【入力エラーです】<br />入力内容を確認して再度送信してください。', true);
			}

			$this->data['Message'] = $this->Message->sanitizeData($this->data['Message']);

		}

		if($this->dbDatas['mailFields'])
			$this->set('mailFields',$this->dbDatas['mailFields']);

		$this->set('mailContent',$this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'].DS.'confirm');

	}
/**
 * [MOBILE] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	function mobile_confirm($id=null) {

		$this->setAction('confirm',$id);

	}
/**
 * [SMARTPHONE] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	function smartphone_confirm($id=null) {

		$this->setAction('confirm',$id);

	}
/**
 * [PUBIC] データ送信
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	function submit($id = null) {

		if(!$this->dbDatas['mailContent']['MailContent']['status']) {
			$this->notFound();
		}
		

		if(!$this->data) {
			$this->redirect(array('action' => 'index', $id));
		} elseif( isset($this->data['Message']['mode']) && $this->data['Message']['mode'] == 'Back' ) {
            $this->_back($id);
		} else {
			// 複数のメールフォームに対応する為、プレフィックス付のCSVファイルに保存。
			// ※ nameフィールドの名称を[message]以外にする
			if($this->dbDatas['mailContent']['MailContent']['name'] != 'message') {
				$prefix = $this->dbDatas['mailContent']['MailContent']['name']."_";
			}else {
				$prefix = "";
			}

			// 画像認証を行う
			if(Configure::read('BcRequest.agent') != 'mobile' && $this->dbDatas['mailContent']['MailContent']['auth_captcha']){
				$captchaResult = $this->BcCaptcha->check($this->data['Message']['auth_captcha']);
				if(!$captchaResult){
					$this->redirect(array('action' => 'index', $id));
				} else {
					unset($this->data['Message']['auth_captcha']);
				}
			}
			
			$this->Message->create($this->data);

			if($this->Message->save(null,false)) {

				// メール送信
				$this->_sendEmail();
				// ビューを一旦初期化しないと携帯の場合に送信完了ページが文字化けしてしまう
				ClassRegistry::removeObject('view');

			}else {

				$this->setMessage('【送信エラーです】<br />送信中にエラーが発生しました。しばらくたってから再度送信お願いします。', true);
				$this->set('sendError',true);

			}

    		$this->set('mailContent',$this->dbDatas['mailContent']);
    		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'].DS.'submit');
		}
	}


    /**
     * [private] 確認画面から戻る
     *
     * @param mixed mail_content_id
     * @return void
     * @access public
     */
    function _back($id)
    {
        $this->set('freezed',false);
        $this->set('error',false);

        if($this->dbDatas['mailFields']){
            $this->set('mailFields',$this->dbDatas['mailFields']);
        }

        //mailの重複チェックがある場合は、チェック用のデータを復帰
        $sendVal = array();
        $noSendVal = array();
        foreach($this->dbDatas['mailContent']['MailField'] as $val){
            if($val['valid_ex'] == 'VALID_EMAIL_CONFIRM'){
                if(! $val['no_send'] ){
                    $sendVal[$val['group_valid']] = $val['field_name'];
                } else {
                    $noSendVal[$val['group_valid']][] = $val['field_name'] ;
                }
            }
        }
        if(! empty($noSendVal) ){
            foreach( $noSendVal as $key => $val){
                foreach( $val as $v){
                    if( isset($this->data['Message'][$sendVal[$key]]) ){
                        $this->data['Message'][$v] = $this->data['Message'][$sendVal[$key]];
                    }
                }
            }
        }

        $this->action = 'index'; //viewのボタンの表示の切り替えに必要なため変更

		$this->set('mailContent',$this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'].DS.'index');
    }


/**
 * [MOBILE] 送信完了ページ
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	function mobile_submit($id=null) {

		$this->setAction('submit',$id);

	}
/**
 * [SMARTPHONE] 送信完了ページ
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	function smartphone_submit($id=null) {

		$this->setAction('submit',$id);

	}
/**
 * メール送信する
 * 
 * @return void
 * @access protected
 */
	function _sendEmail() {

		$mailConfig = $this->dbDatas['mailConfig']['MailConfig'];
		$mailContent = $this->dbDatas['mailContent']['MailContent'];
		$userMail = '';

		// データを整形
		$data = $this->Message->restoreData($this->Message->convertToDb($this->data));
		$data['message'] = $data['Message'];
		$data['mailFields'] = $this->dbDatas['mailFields'];
		$data['mailContents'] = $this->dbDatas['mailContent']['MailContent'];
		$data['mailConfig'] = $this->dbDatas['mailConfig']['MailConfig'];
		$data['other']['date'] = date('Y/m/d H:i');
		$data = $this->Message->convertDatasToMail($data);

		// 管理者メールを取得
		if($mailContent['sender_1']) {
			$adminMail = $mailContent['sender_1'];
		}else {
			$adminMail = $this->siteConfigs['email'];
		}

		foreach($this->dbDatas['mailFields'] as $mailField) {
			$field = $mailField['MailField']['field_name'];
			if(!isset($data['Message'][$field])) {
				continue;
			}
			$value = $data['Message'][$field];
			// ユーザーメールを取得
			if($mailField['MailField']['type'] == 'email' && $value) {
				$userMail = $value;
			}
			// 件名にフィールドの値を埋め込む
			// 和暦など配列の場合は無視
			if(!is_array($value)) {
				$mailContent['subject_user'] = str_replace('{$'.$field.'}', $value, $mailContent['subject_user']);
				$mailContent['subject_admin'] = str_replace('{$'.$field.'}', $value, $mailContent['subject_admin']);
			}
		}
		
		// 前バージョンとの互換性の為 type が email じゃない場合にも取得できるようにしておく
		if(!$userMail) {
			if(!empty($data['Message']['email'])) {
				$userMail = $data['Message']['email'];
			}elseif(!empty($data['Message']['email_1'])) {
				$userMail = $data['Message']['email_1'];
			}
		}

		// ユーザーに送信
		if(!empty($userMail)) {
			$data['other']['mode'] = 'user';
			$options = array(
				'fromName'	=> $mailContent['sender_name'],
				'reply'		=> $adminMail,
				'template'	=> $mailContent['mail_template'],
				'from'		=> $adminMail
			);
			$this->sendMail($userMail, $mailContent['subject_user'], $data, $options);
		}

		// 管理者に送信
		if(!empty($adminMail)) {
			$data['other']['mode'] = 'admin';
			$options = array(
				'fromName'		=> $mailContent['sender_name'],
				'reply'			=> $userMail,
				'from'			=> $adminMail,
				'template'		=> $mailContent['mail_template'],
				'bcc'			=> $mailContent['sender_2'],
				'agentTemplate'	=> false
			);
			$this->sendMail($adminMail,$mailContent['subject_admin'], $data, $options);
		}

	}
/**
 * 認証用のキャプチャ画像を表示する
 * 
 * @return void
 * @access public
 */
    function captcha()
    {
		
        $this->BcCaptcha->render();
		
    }
/**
 * [SMARTPHONE] 認証用のキャプチャ画像を表示する
 * 
 * @return void
 * @access public
 */
    function smartphone_captcha()
    {
		
        $this->BcCaptcha->render();
		
    }
}
