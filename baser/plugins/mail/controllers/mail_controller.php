<?php
/* SVN FILE: $Id$ */
/**
 * お問い合わせメールフォーム用コントローラー
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
 * @package			baser.plugins.mail.controller
 * @since			Baser v 0.1.0
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
 * @package			baser.plugins.mail.controller
 */
class MailController extends MailAppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'Mail';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('Mail.Message','Mail.MailContent','Mail.MailField','Mail.MailConfig');
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Freeze','Mailform','Javascript','Array','TimeEx','Maildata','Mailfield','Mail');
/**
 * Array of components a controller will use
 *
 * @var 	array
 * @access 	public
 */
	// PHP4の場合、メールフォームの部品が別エレメントになった場合、利用するヘルパが別インスタンスとなってしまう様子。
	// そのためSecurityコンポーネントが利用できない
	// 同じエレメント内で全てのフォーム部品を完結できればよいがその場合デザインの自由度が失われてしまう。
	//var $components = array('Email','EmailEx','Security','Captcha');
	var $components = array('Email','EmailEx','Captcha');
/**
 * CSS
 *
 * @var 	array
 * @access 	public
 */
	var $css = array('mail/form');
/**
 * ページタイトル
 *
 * @var		string
 * @access 	public
 */
	var $pageTitle = 'お問い合わせ';
/**
 * サブメニューエレメント
 *
 * @var		string
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * データベースデータ
 *
 * @var 	array
 * @access 	public
 */
	var $dbDatas = null;
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array();
/**
 * beforeFilter.
 *
 * @return void
 * @access 	public
 */
	function beforeFilter() {

		/* 認証設定 */
		$this->Auth->allow('index','mobile_index','confirm','mobile_confirm','submit','mobile_submit','captcha');

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
		$this->Message->mailFields = $this->dbDatas['mailFields'] = $this->MailField->findAll(array("mail_content_id"=>$id),null,'MailField.sort');

		// ページタイトルをセット
		$this->pageTitle = $this->dbDatas['mailContent']['MailContent']['title'];
		// レイアウトをセット
		$this->layout = $this->dbDatas['mailContent']['MailContent']['layout_template'];

		if(empty($this->contentId)) {
			$this->contentId = $this->params['pass'][0];
		}

		$this->subMenuElements = array('default');

		// PHP4でセキュリティコンポーネントがうまくいかなかったので利用停止
		// 詳細はコンポーネント設定のコメントを参照
		//$this->Security->requireAuth('submit');
		$this->Security->validatePost = false;

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
 * @return	void
 * @access 	public
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
 * @param	mixed	mail_content_id
 * @return	void
 * @access	public
 */
	function index($id = null) {

		// 初期値を取得
		if(!$this->data) {
			$this->data = $this->Message->getDefaultValue();
		}

		$this->set('freezed',false);

		if($this->dbDatas['mailFields'])
			$this->set('mailFields',$this->dbDatas['mailFields']);

		$this->set('mailContent',$this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'].DS.'index');

	}
/**
 * [MOBILE] フォームを表示する
 *
 * @param	mixed	mail_content_id
 * @return	void
 * @access	public
 */
	function mobile_index($id=null) {

		$this->setAction('index',$id);

	}
/**
 * [PUBIC] データの確認画面を表示
 *
 * @param	mixed	mail_content_id
 * @return	void
 * @access	public
 */
	function confirm($id = null) {

		if(!$this->data) {
			$this->redirect(array("action"=>"index",$id));
		}else {
			// 入力データを整形し、モデルに引き渡す
			$this->data = $this->Message->create($this->Message->autoConvert($this->data));

			// 画像認証を行う
			if(!Configure::read('Mobile.on') && $this->dbDatas['mailContent']['MailContent']['auth_captcha']){
				$captchaResult = $this->Captcha->check($this->data['Message']['auth_captcha']);
				if(!$captchaResult){
					$this->Message->invalidate('auth_captcha');
				} else {
					unset($this->data['Message']['auth_captcha']);
				}
			}

			// データの入力チェックを行う
			if($this->Message->validates()) {
				$this->set('freezed',true);
			}else {
				$this->set('freezed',false);
				$this->set('error',true);

				$this->Session->setFlash('【入力エラーです】<br />入力内容を確認して再度送信してください。');
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
 * @param	mixed	mail_content_id
 * @return	void
 * @access	public
 */
	function mobile_confirm($id=null) {

		$this->setAction('confirm',$id);

	}
/**
 * [PUBIC] データ送信
 *
 * @param	mixed	mail_content_id
 * @return	void
 * @access	public
 */
	function submit($id = null) {

		if(!$this->data) {
			$this->redirect(array("action"=>"index",$id));
		}else {

			// 複数のメールフォームに対応する為、プレフィックス付のCSVファイルに保存。
			// ※ nameフィールドの名称を[message]以外にする
			if($this->dbDatas['mailContent']['MailContent']['name'] != 'message') {
				$prefix = $this->dbDatas['mailContent']['MailContent']['name']."_";
			}else {
				$prefix = "";
			}

			$this->Message->create($this->data);

			if($this->Message->save(null,false)) {

				// メール送信
				$this->_sendEmail();
				// ビューを一旦初期化しないと携帯の場合に送信完了ページが文字化けしてしまう
				ClassRegistry::removeObject('view');

			}else {

				$this->Session->setFlash('【送信エラーです】<br />送信中にエラーが発生しました。しばらくたってから再度送信お願いします。');
				$this->set('sendError',true);

			}

		}

		$this->set('mailContent',$this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'].DS.'submit');

	}
/**
 * [MOBILE] 送信完了ページ
 *
 * @param	mixed	mail_content_id
 * @return	void
 * @access	public
 */
	function mobile_submit($id=null) {

		$this->setAction('submit',$id);

	}
/**
 * メール送信する
 * @return	void
 * @access	protected
 */
	function _sendEmail() {

		$mailConfig = $this->dbDatas['mailConfig']['MailConfig'];
		$mailContent = $this->dbDatas['mailContent']['MailContent'];
		$userMail = '';
		//$userName = '';

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
			// ユーザーメールを取得
			if($mailField['MailField']['type'] == 'email') {
				$userMail = $data['Message'][$mailField['MailField']['field_name']];
			}
			// 件名にフィールドの値を埋め込む
			$mailContent['subject_user'] = str_replace('{$'.$mailField['MailField']['field_name'].'}',
														$data['Message'][$mailField['MailField']['field_name']],
														$mailContent['subject_user']);
			$mailContent['subject_admin'] = str_replace('{$'.$mailField['MailField']['field_name'].'}',
														$data['Message'][$mailField['MailField']['field_name']],
														$mailContent['subject_admin']);
		}
		// 前バージョンとの互換性の為 type が email じゃない場合にも取得できるようにしておく
		if(!$userMail) {
			if(!empty($data['Message']['email'])) {
				$userMail = $data['Message']['email'];
			}elseif(!empty($data['Message']['email_1'])) {
				$userMail = $data['Message']['email_1'];
			}
		}

		// ユーザー名を取得
		/*if(!empty($data['Message']['name'])){
			$userName = $data['Message']['name'] . '　様';
		}elseif(!empty($data['Message']['name_1']) && !empty($data['Message']['name_2'])){
			$userName = $data['Message']['name_1'] . '　' . $data['Message']['name_2'] . '　様';
		}*/

		// ユーザーに送信
		if(!empty($userMail)) {
			$data['other']['mode'] = 'user';
			$options = array('fromName' => $mailContent['sender_name'],
								'reply' => $adminMail,
								'template' => $mailContent['mail_template'],
								'from' => $adminMail);
			$this->sendMail($userMail, $mailContent['subject_user'], $data, $options);
		}

		// 管理者に送信
		if(!empty($adminMail)) {
			$data['other']['mode'] = 'admin';
			$options = array('fromName' => $mailContent['sender_name'],
								'reply' => $userMail,
								'from' => $adminMail,
								'template' => $mailContent['mail_template'],
								'cc' => $mailContent['sender_2']
			);
			$this->sendMail($adminMail,$mailContent['subject_admin'], $data, $options);
		}

	}
/**
 * 認証用のキャプチャ画像を表示する
 * @return	void
 * @access	public
 */
    function captcha()
    {
        $this->Captcha->render();
    }
}
?>