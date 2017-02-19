<?php

/**
 * お問い合わせメールフォーム用コントローラー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * お問い合わせメールフォーム用コントローラー
 *
 * @package Mail.Controller
 */
class MailController extends MailAppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Mail';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('Mail.Message', 'Mail.MailContent', 'Mail.MailField', 'Mail.MailConfig');

/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	public $helpers = array(
		'BcFreeze', 'BcArray', 'BcTime', 'Mail.Mailform', 'Mail.Maildata', 'Mail.Mailfield', 'Mail.Mail', 'Js'
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
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure', 'Email', 'BcEmail', 'BcCaptcha', 'Security');

/**
 * CSS
 *
 * @var array
 * @access public
 */
	public $css = array('mail/form');

/**
 * ページタイトル
 *
 * @var string
 * @access public
 */
	public $pageTitle = 'お問い合わせ';

/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array();

/**
 * データベースデータ
 *
 * @var array
 * @access public
 */
	public $dbDatas = null;

/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array();

/**
 * beforeFilter.
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		/* 認証設定 */
		$this->BcAuth->allow(
			'index', 'mobile_index', 'smartphone_index', 'confirm', 'mobile_confirm', 'smartphone_confirm', 'submit', 'mobile_submit', 'smartphone_submit', 'captcha', 'smartphone_captcha', 'ajax_get_token', 'smartphone_ajax_get_token'
		);

		parent::beforeFilter();

		// バリデーション自動生成用にメールフォームIDを設定
		if (!empty($this->contentId)) {
			$id = $this->contentId;
		} elseif (!empty($this->params['pass'][0]) && is_numeric($this->params['pass'][0])) {
			$id = $this->params['pass'][0];
		} else {
			$id = 1;
		}

		$this->Message->setup($id);
		$this->dbDatas['mailContent'] = $this->Message->mailContent;
		$this->dbDatas['mailFields'] = $this->Message->mailFields;
		$this->dbDatas['mailConfig'] = $this->MailConfig->find();
		
		// ページタイトルをセット
		$this->pageTitle = $this->dbDatas['mailContent']['MailContent']['title'];
		// レイアウトをセット
		$this->layout = $this->dbDatas['mailContent']['MailContent']['layout_template'];

		if (empty($this->contentId)) {
			// 配列のインデックスが無いためエラーとなるため修正
			$this->contentId = isset($this->request->params['pass'][0]) ? $this->request->params['pass'][0] : null;
		}

		$this->subMenuElements = array('default');

		// 2013/03/14 ryuring
		// baserCMS２系より必須要件をPHP5以上とした為、SecurityComponent を標準で設定する方針に変更
		if (Configure::read('debug') > 0) {
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;
		} else {
			// PHP4でセキュリティコンポーネントがうまくいかなかったので利用停止
			// 詳細はコンポーネント設定のコメントを参照
			$disabledFields = array('Message.mode', 'x', 'y', 'MAX_FILE_SIZE');
			// type="file" を除外
			foreach($this->Message->mailFields as $field) {
				if (isset($field['MailField']['type']) && $field['MailField']['type'] == 'file') {
					$disabledFields[] = $field['MailField']['field_name'];
				}
			}
			$this->Security->requireAuth('confirm', 'submit');
			$this->Security->disabledFields = array_merge($this->Security->disabledFields, $disabledFields);

			// SSL設定
			if ($this->dbDatas['mailContent']['MailContent']['ssl_on']) {
				$this->Security->blackHoleCallback = 'sslFail';
				$this->Security->requireSecure = am($this->Security->requireSecure, array('index', 'confirm', 'submit'));
			}
		}

	}

/**
 * beforeRender
 *
 * @return void
 * @access public
 */
	public function beforeRender() {
		parent::beforeRender();
		if ($this->dbDatas['mailContent']['MailContent']['widget_area']) {
			$this->set('widgetArea', $this->dbDatas['mailContent']['MailContent']['widget_area']);
		}
	}

/**
 * [PUBIC] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	public function index($id = null) {
		if (!$this->MailContent->isPublish($this->dbDatas['mailContent']['MailContent']['status'], $this->dbDatas['mailContent']['MailContent']['publish_begin'], $this->dbDatas['mailContent']['MailContent']['publish_end'])) {
			$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'unpublish');
			return;
		}

		if (!$this->dbDatas['mailContent']['MailContent']['status']) {
			$this->notFound();
		}

		$this->Session->write('Mail.valid', true);

		// 初期値を取得
		if (!isset($this->request->data['Message'])) {
			if(!empty($this->request->params['named'])) {
				foreach($this->request->params['named'] as $key => $value) {
					$this->request->params['named'][$key] = base64UrlsafeDecode($value);
				}
			}
			$this->request->data = $this->Message->getDefaultValue($this->request->params['named']);
		} else {
			$this->request->data['Message'] = $this->Message->sanitizeData($this->request->data['Message']);
		}

		$this->set('freezed', false);

		if ($this->dbDatas['mailFields']) {
			$this->set('mailFields', $this->dbDatas['mailFields']);
		}

		$user = $this->BcAuth->user();
		if (!empty($user) && !Configure::read('BcRequest.agent')) {
			$this->set('editLink', array('admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'edit', $this->dbDatas['mailContent']['MailContent']['id']));
		}

		$this->set('mailContent', $this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'index');
	}

/**
 * [MOBILE] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	public function mobile_index($id = null) {
		$this->setAction('index', $id);
	}

/**
 * [SMARTPHONE] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	public function smartphone_index($id = null) {
		$this->setAction('index', $id);
	}

/**
 * [PUBIC] データの確認画面を表示
 *
 * @param mixed	mail_content_id
 * @return void
 * @access public
 */
	public function confirm($id = null) {

		if ($this->request->is('post')) {
			if ($_SERVER['CONTENT_LENGTH'] > (8*1024*1024)) {
				$this->Session->setFlash('ファイルのアップロードサイズが上限を超えています。');
			}
		}
		
		if (!$this->MailContent->isPublish($this->dbDatas['mailContent']['MailContent']['status'], $this->dbDatas['mailContent']['MailContent']['publish_begin'], $this->dbDatas['mailContent']['MailContent']['publish_end'])) {
			$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'unpublish');
			return;
		}
		if (!$this->dbDatas['mailContent']['MailContent']['status']) {
			$this->notFound();
		}
		if (!$this->Session->read('Mail.valid')) {
			$this->notFound();
		}

		if (!$this->request->data) {
			$this->redirect(array('action' => 'index', $id));
		} else {
			// 入力データを整形し、モデルに引き渡す
			$this->request->data = $this->Message->create($this->Message->autoConvert($this->request->data));

			// 画像認証を行う
			if (Configure::read('BcRequest.agent') != 'mobile' && $this->dbDatas['mailContent']['MailContent']['auth_captcha']) {
				$captchaResult = $this->BcCaptcha->check($this->request->data['Message']['auth_captcha']);
				if (!$captchaResult) {
					$this->Message->invalidate('auth_captcha');
				}
			}

			// データの入力チェックを行う
			if ($this->Message->validates()) {
				$this->request->data = $this->Message->saveTmpFiles($this->data, mt_rand(0, 99999999));
				$this->set('freezed', true);
			} else {
				$this->set('freezed', false);
				$this->set('error', true);

				$this->setMessage('【入力エラーです】<br />入力内容を確認して再度送信してください。', true);
			}
			$this->request->data['Message'] = $this->Message->sanitizeData($this->request->data['Message']);
		}

		if ($this->dbDatas['mailFields']) {
			$this->set('mailFields', $this->dbDatas['mailFields']);
		}
		$this->set('mailContent', $this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'confirm');
	}

/**
 * [MOBILE] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	public function mobile_confirm($id = null) {
		$this->setAction('confirm', $id);
	}

/**
 * [SMARTPHONE] フォームを表示する
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	public function smartphone_confirm($id = null) {
		$this->setAction('confirm', $id);
	}

/**
 * [PUBIC] データ送信
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	public function submit($id = null) {
		if (!$this->MailContent->isPublish($this->dbDatas['mailContent']['MailContent']['status'], $this->dbDatas['mailContent']['MailContent']['publish_begin'], $this->dbDatas['mailContent']['MailContent']['publish_end'])) {
			$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'unpublish');
			return;
		}
		if (!$this->dbDatas['mailContent']['MailContent']['status']) {
			$this->notFound();
		}
		if (!$this->Session->read('Mail.valid')) {
			$this->redirect(array('action' => 'index', $id));
		}

		if (!$this->request->data) {
			$this->redirect(array('action' => 'index', $id));
		} elseif (isset($this->request->data['Message']['mode']) && $this->request->data['Message']['mode'] == 'Back') {
			$this->_back($id);
		} else {
			// 複数のメールフォームに対応する為、プレフィックス付のCSVファイルに保存。
			// ※ nameフィールドの名称を[message]以外にする
			if ($this->dbDatas['mailContent']['MailContent']['name'] != 'message') {
				$prefix = $this->dbDatas['mailContent']['MailContent']['name'] . "_";
			} else {
				$prefix = "";
			}

			// 画像認証を行う
			if (Configure::read('BcRequest.agent') != 'mobile' && $this->dbDatas['mailContent']['MailContent']['auth_captcha']) {
				$captchaResult = $this->BcCaptcha->check($this->request->data['Message']['auth_captcha']);
				if (!$captchaResult) {
					$this->redirect(array('action' => 'index', $id));
				} else {
					unset($this->request->data['Message']['auth_captcha']);
				}
			}

			$this->Message->create($this->request->data);

			// データの入力チェックを行う
			if ($this->Message->validates()) {

				// 送信データを保存するか確認
				if ($this->dbDatas['mailContent']['MailContent']['save_info']) {
					// validation OK
					$result = $this->Message->save(null, false);
				} else {
					$result = $this->request->data;
				}

				if ($result) {
					
					$this->request->data = $result;
					
					/*** Mail.beforeSendEmail ***/
					$event = $this->dispatchEvent('beforeSendEmail', array(
						'data' => $this->request->data
					));
					if ($event !== false) {
						$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
					}

					// メール送信
					$this->_sendEmail();

					$this->Session->delete('Mail.valid');

					/*** Mail.afterSendEmail ***/
					$this->dispatchEvent('afterSendEmail', array(
						'data' => $this->request->data
					));
				} else {

					$this->setMessage('【送信エラーです】<br />送信中にエラーが発生しました。しばらくたってから再度送信お願いします。', true);
					$this->set('sendError', true);
				}

				$this->set('mailContent', $this->dbDatas['mailContent']);
				$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'submit');

				// 入力検証エラー
			} else {
				$this->set('freezed', false);
				$this->set('error', true);

				$this->setMessage('【入力エラーです】<br />入力内容を確認して再度送信してください。', true);
				$this->request->data['Message'] = $this->Message->sanitizeData($this->request->data['Message']);
				$this->action = 'index'; //viewのボタンの表示の切り替えに必要なため変更
				if ($this->dbDatas['mailFields']) {
					$this->set('mailFields', $this->dbDatas['mailFields']);
				}

				$this->set('mailContent', $this->dbDatas['mailContent']);
				$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'index');
			}
		}
	}

/**
 * [private] 確認画面から戻る
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	public function _back($id) {
		$this->set('freezed', false);
		$this->set('error', false);

		if ($this->dbDatas['mailFields']) {
			$this->set('mailFields', $this->dbDatas['mailFields']);
		}

		//mailの重複チェックがある場合は、チェック用のデータを復帰
		// ↓
		// 2013/11/08 - gondoh mailヘッダインジェクション対策時に
		// 確認画面にもhiddenタグ出力するよう変更したため削除

		// >>> DELETE 2015/11/25 - gondoh view側で吸収するように変更
		// $this->action = 'index'; //viewのボタンの表示の切り替えに必要なため変更
		// <<<

		$this->set('mailContent', $this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'index');
	}

/**
 * [MOBILE] 送信完了ページ
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	public function mobile_submit($id = null) {
		$this->setAction('submit', $id);
	}

/**
 * [SMARTPHONE] 送信完了ページ
 *
 * @param mixed mail_content_id
 * @return void
 * @access public
 */
	public function smartphone_submit($id = null) {
		$this->setAction('submit', $id);
	}

/**
 * メール送信する
 * 
 * @return void
 * @access protected
 */
	protected function _sendEmail() {
		$mailConfig = $this->dbDatas['mailConfig']['MailConfig'];
		$mailContent = $this->dbDatas['mailContent']['MailContent'];
		$userMail = '';

		// データを整形
		$data = $this->Message->restoreData($this->Message->convertToDb($this->request->data));
		$data['message'] = $data['Message'];
		unset($data['Message']);
		$data['mailFields'] = $this->dbDatas['mailFields'];
		$data['mailContents'] = $this->dbDatas['mailContent']['MailContent'];
		$data['mailConfig'] = $this->dbDatas['mailConfig']['MailConfig'];
		$data['other']['date'] = date('Y/m/d H:i');
		$data = $this->Message->convertDatasToMail($data);

		// 管理者メールを取得
		if ($mailContent['sender_1']) {
			$adminMail = $mailContent['sender_1'];
		} else {
			$adminMail = $this->siteConfigs['email'];
		}
		if (strpos($adminMail, ',') !== false) {
			list($fromAdmin) = explode(',', $adminMail);
		} else {
			$fromAdmin = $adminMail;
		}

		$attachments = array();
		$settings = $this->Message->Behaviors->BcUpload->settings['Message'];
		foreach ($this->dbDatas['mailFields'] as $mailField) {
			$field = $mailField['MailField']['field_name'];
			if (!isset($data['message'][$field])) {
				continue;
			}
			$value = $data['message'][$field];
			// ユーザーメールを取得
			if ($mailField['MailField']['type'] == 'email' && $value) {
				$userMail = $value;
			}
			// 件名にフィールドの値を埋め込む
			// 和暦など配列の場合は無視
			if (!is_array($value)) {
				if ($mailField['MailField']['type'] == 'radio' || 
					  $mailField['MailField']['type'] == 'select') {
					$source = explode('|', $mailField['MailField']['source']);
					if(!empty($value)){
						$mailContent['subject_user'] = str_replace('{$' . $field . '}', $source[$value-1], $mailContent['subject_user']);
						$mailContent['subject_admin'] = str_replace('{$' . $field . '}', $source[$value-1], $mailContent['subject_admin']);
					}
				} else {
					$mailContent['subject_user'] = str_replace('{$' . $field . '}', $value, $mailContent['subject_user']);
					$mailContent['subject_admin'] = str_replace('{$' . $field . '}', $value, $mailContent['subject_admin']);
				}
			}
			if($mailField['MailField']['type'] == 'file' && $value) {
				$attachments[] = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . $value;
			}
		}

		// 前バージョンとの互換性の為 type が email じゃない場合にも取得できるようにしておく
		if (!$userMail) {
			if (!empty($data['message']['email'])) {
				$userMail = $data['message']['email'];
			} elseif (!empty($data['message']['email_1'])) {
				$userMail = $data['message']['email_1'];
			}
		}

		// ユーザーに送信
		if (!empty($userMail)) {
			$data['other']['mode'] = 'user';
			$options = array(
				'fromName'	=> $mailContent['sender_name'],
				'from'		=> $fromAdmin,
				'template'	=> 'Mail.' . $mailContent['mail_template'],
				'replyTo'		=> $fromAdmin,
				'attachments'	=> $attachments,
				'additionalParameters'	 => '-f ' . $fromAdmin,
			);
			$this->sendMail($userMail, $mailContent['subject_user'], $data, $options);
		}

		// 管理者に送信
		if (!empty($adminMail)) {
			$data['other']['mode'] = 'admin';
			$options = array(
				'fromName' => $mailContent['sender_name'],
				'replyTo' => $userMail,
				'from' => $fromAdmin,
				'template' => 'Mail.' . $mailContent['mail_template'],
				'bcc' => $mailContent['sender_2'],
				'agentTemplate' => false,
				'attachments'	=> $attachments,
				'additionalParameters'	 => '-f ' . $fromAdmin,
			);
			$this->sendMail($adminMail, $mailContent['subject_admin'], $data, $options);
		}
	}

/**
 * 認証用のキャプチャ画像を表示する
 * 
 * @return void
 * @access public
 */
	public function captcha() {
		$this->BcCaptcha->render();
		exit();
	}

/**
 * [SMARTPHONE] 認証用のキャプチャ画像を表示する
 * 
 * @return void
 * @access public
 */
	public function smartphone_captcha() {
		$this->BcCaptcha->render();
		exit();
	}

/**
 * [ajax] Tokenのkeyを取得
 *
 * @return void
 * @access public
 */
	public function ajax_get_token() {
		echo $this->request->params['_Token']['key'];
		exit();
	}

	/**
	 * [ajax] Tokenのkeyを取得
	 *
	 * @return void
	 * @access public
	 */
	public function smartphone_ajax_get_token() {
		$this->setAction('ajax_get_token');
	}

}
