<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Controller
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('MailAppController', 'Mail.Controller');

/**
 * お問い合わせメールフォーム用コントローラー
 *
 * @package Mail.Controller
 * @property MailMessage $MailMessage
 * @property MailContent $MailContent
 * @property MailField $MailField
 * @property MailConfig $MailConfig
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcEmailComponent $BcEmail
 * @property BcCaptchaComponent $BcCaptcha
 * @property SecurityComponent $Security
 * @property BcContentsComponent $BcContents
 */
class MailController extends MailAppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'Mail';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['Mail.MailMessage', 'Mail.MailContent', 'Mail.MailField', 'Mail.MailConfig', 'Content'];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = [
		'BcFreeze', 'BcArray', 'BcTime', 'Mail.Mailform', 'Mail.Maildata', 'Mail.Mailfield', 'Mail.Mail', 'Js'
	];

	/**
	 * Array of components a controller will use
	 *
	 * @var array
	 */
	// PHP4の場合、メールフォームの部品が別エレメントになった場合、利用するヘルパが別インスタンスとなってしまう様子。
	// そのためSecurityコンポーネントが利用できない
	// 同じエレメント内で全てのフォーム部品を完結できればよいがその場合デザインの自由度が失われてしまう。
	// var $components = array('Email','BcEmail','Security','BcCaptcha');
	//
	// 2013/03/14 ryuring
	// baserCMS２系より必須要件をPHP5以上とした為、SecurityComponent を標準で設定する方針に変更
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'Email', 'BcEmail', 'BcCaptcha', 'Security', 'BcContents'];

	/**
	 * CSS
	 *
	 * @var array
	 */
	public $css = ['mail/form'];

	/**
	 * サブメニューエレメント
	 *
	 * @var array
	 */
	public $subMenuElements = [];

	/**
	 * データベースデータ
	 *
	 * @var array
	 */
	public $dbDatas = null;

	/**
	 * ぱんくずナビ
	 *
	 * @var array
	 */
	public $crumbs = [];

	/**
	 * MailController constructor.
	 *
	 * @param \CakeRequest $request
	 * @param \CakeResponse $response
	 */
	public function __construct($request = null, $response = null)
	{
		parent::__construct($request, $response);
		$this->pageTitle = __('お問い合わせ');
	}

	/**
	 * beforeFilter.
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		/* 認証設定 */
		// @deprecated 5.0.0 since 4.0.0 ajax_get_token は、BcFormController に移行した為、次のバージョンで削除
		$this->BcAuth->allow(
			'index', 'mobile_index', 'smartphone_index', 'confirm', 'mobile_confirm', 'smartphone_confirm', 'submit', 'mobile_submit', 'smartphone_submit', 'thanks', 'captcha', 'smartphone_captcha', 'ajax_get_token', 'smartphone_ajax_get_token'
		);

		parent::beforeFilter();

		if (!$this->request->param('entityId')) {
			$this->notFound();
		}
		$this->MailMessage->setup($this->request->param('entityId'));
		$this->dbDatas['mailContent'] = $this->MailMessage->mailContent;
		$this->dbDatas['mailFields'] = $this->MailMessage->mailFields;
		$this->dbDatas['mailConfig'] = $this->MailConfig->find();

		// ページタイトルをセット
		$this->pageTitle = $this->request->param('Content.title');

		if (empty($this->contentId)) {
			// 配列のインデックスが無いためエラーとなるため修正
			$this->contentId = $this->request->param('entityId');
		}

		$this->subMenuElements = ['default'];

		// 2013/03/14 ryuring
		// baserCMS２系より必須要件をPHP5以上とした為、SecurityComponent を標準で設定する方針に変更
		if (Configure::read('debug') > 0) {
			$this->Security->validatePost = false;
			$this->Security->csrfCheck = false;
		} else {
			// PHP4でセキュリティコンポーネントがうまくいかなかったので利用停止
			// 詳細はコンポーネント設定のコメントを参照
			$disabledFields = ['MailMessage.mode'];
			// type="file" を除外
			foreach($this->MailMessage->mailFields as $field) {
				if (Hash::get($field, 'MailField.type') === 'file') {
					$disabledFields[] = $field['MailField']['field_name'];
				}
			}
			$this->Security->requireAuth('confirm', 'submit');
			$this->set('unlockedFields', array_merge($this->Security->unlockedFields, $disabledFields));

			// SSL設定
			if ($this->dbDatas['mailContent']['MailContent']['ssl_on']) {
				$this->Security->blackHoleCallback = 'sslFail';
				$this->Security->requireSecure = am($this->Security->requireSecure, ['index', 'confirm', 'submit']);
			}
		}

	}

	/**
	 * beforeRender
	 *
	 * @return void
	 */
	public function beforeRender()
	{
		parent::beforeRender();
		if ($this->dbDatas['mailContent']['MailContent']['widget_area']) {
			$this->set('widgetArea', $this->dbDatas['mailContent']['MailContent']['widget_area']);
		}

		// キャッシュ対策
		if (!isConsole() && !$this->request->param('requested')) {
			header("Cache-Control: no-cache, no-store, must-revalidate");
			header("Pragma: no-cache");
			header("Expires: " . date(DATE_RFC1123, strtotime("-1 day")));
		}
	}

	/**
	 * [PUBIC] フォームを表示する
	 *
	 * @return void
	 */
	public function index()
	{
		if (empty($this->dbDatas['mailContent'])) {
			$this->notFound();
		}
		if (!$this->MailContent->isAccepting($this->dbDatas['mailContent']['MailContent']['publish_begin'], $this->dbDatas['mailContent']['MailContent']['publish_end'])) {
			$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'unpublish');
			return;
		}

		if ($this->BcContents->preview === 'default' && $this->request->data && !$this->request->param('requested')) {
			$this->dbDatas['mailContent']['MailContent'] = $this->request->data['MailContent'];
			$this->request->data = $this->Content->saveTmpFiles($this->request->data, mt_rand(0, 99999999));
			$this->request->param('Content.eyecatch', $this->request->data['Content']['eyecatch']);
		}

		$this->Session->write('Mail.valid', true);

		// 初期値を取得
		if (!isset($this->request->data['MailMessage'])) {
			if ($this->request->param('named')) {
				foreach($this->request->params['named'] as $key => $value) {
					$this->request->params['named'][$key] = base64UrlsafeDecode($value);
				}
			}
			$this->request->data = $this->MailMessage->getDefaultValue($this->request->param('named'));
		}

		$this->set('freezed', false);

		if ($this->dbDatas['mailFields']) {
			$this->set('mailFields', $this->dbDatas['mailFields']);
		}

		$user = BcUtil::loginUser('admin');
		if (!empty($user)) {
			$this->set('editLink', ['admin' => true, 'plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'edit', $this->dbDatas['mailContent']['MailContent']['id']]);
		}
		$this->set('mailContent', $this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'index');
	}

	/**
	 * [PUBIC] データの確認画面を表示
	 *
	 * @param mixed    mail_content_id
	 * @return void
	 */
	public function confirm($id = null)
	{

		if ($this->request->is('post')) {
			if ($_SERVER['CONTENT_LENGTH'] > (8 * 1024 * 1024)) {
				$this->BcMessage->setError(__('ファイルのアップロードサイズが上限を超えています。'));
			}
		}

		if (!$this->MailContent->isAccepting($this->dbDatas['mailContent']['MailContent']['publish_begin'], $this->dbDatas['mailContent']['MailContent']['publish_end'])) {
			$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'unpublish');
			return;
		}
		if (!$this->Session->read('Mail.valid')) {
			$this->BcMessage->setError('エラーが発生しました。もう一度操作してください。');
			$this->redirect($this->request->param('Content.url') . '/index');
		}

		if (!$this->request->data) {
			$this->redirect($this->request->param('Content.url') . '/index');
		} else {
			// 入力データを整形し、モデルに引き渡す
			$this->request->data = $this->MailMessage->create($this->MailMessage->autoConvert($this->request->data));

			// fileタイプへの送信データ検証
			if (!$this->_checkDirectoryRraversal()) {
				$this->redirect($this->request->param('Content.url') . '/index');
			}

			// 画像認証を行う
			if ($this->request->param('Site.name') !== 'mobile' && Hash::get($this->dbDatas, 'mailContent.MailContent.auth_captcha')) {
				$captchaResult = $this->BcCaptcha->check(
					Hash::get($this->request->data, 'MailMessage.auth_captcha'),
					Hash::get($this->request->data, 'MailMessage.captcha_id')
				);
				if (!$captchaResult) {
					$this->MailMessage->invalidate('auth_captcha');
				}
			}

			// データの入力チェックを行う
			if ($this->MailMessage->validates()) {
				$this->request->data = $this->MailMessage->saveTmpFiles($this->data, mt_rand(0, 99999999));
				$this->set('freezed', true);
			} else {
				$this->set('freezed', false);
				$this->set('error', true);
				$this->request->data['MailMessage']['auth_captcha'] = null;
				$this->request->data['MailMessage']['captcha_id'] = null;
				$this->BcMessage->setError(__('エラー : 入力内容を確認して再度送信してください。'));
			}
		}

		if ($this->dbDatas['mailFields']) {
			$this->set('mailFields', $this->dbDatas['mailFields']);
		}
		$user = BcUtil::loginUser('admin');
		if (!empty($user)) {
			$this->set(
				'editLink',
				[
					'admin' => true,
					'plugin' => 'mail',
					'controller' => 'mail_contents',
					'action' => 'edit',
					$this->dbDatas['mailContent']['MailContent']['id']
				]
			);
		}
		$this->set('mailContent', $this->dbDatas['mailContent']);
		$this->render(Hash::get($this->dbDatas, 'mailContent.MailContent.form_template') . '/confirm');
	}

	/**
	 * [PUBIC] データ送信
	 *
	 * @param mixed mail_content_id
	 * @return void
	 */
	public function submit($id = null)
	{
		$isAccepting = $this->MailContent->isAccepting(
			$this->dbDatas['mailContent']['MailContent']['publish_begin'],
			$this->dbDatas['mailContent']['MailContent']['publish_end']
		);
		if (!$isAccepting) {
			$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'unpublish');
			return;
		}
		if (!$this->Session->read('Mail.valid')) {
			$this->BcMessage->setError('エラーが発生しました。もう一度操作してください。');
			$this->redirect($this->request->param('Content.url') . '/index');
		}

		if (!$this->request->data) {
			$this->redirect($this->request->param('Content.url') . '/index');
			return;
		}

		if (Hash::get($this->request->data, 'MailMessage.mode') === 'Back') {
			$this->_back($id);
		} else {
			// 画像認証を行う
			$auth_captcha = Hash::get($this->dbDatas, 'mailContent.MailContent.auth_captcha');
			if ($this->request->param('Site.name') !== 'mobile' && $auth_captcha) {
				$captchaResult = $this->BcCaptcha->check(
					$this->request->data['MailMessage']['auth_captcha'],
					@$this->request->data['MailMessage']['captcha_id']
				);
				if (!$captchaResult) {
					$this->redirect($this->request->param('Content.url') . '/index');
					return;
				}
				unset($this->request->data['MailMessage']['auth_captcha']);
			}

			// fileタイプへの送信データ検証
			if (!$this->_checkDirectoryRraversal()) {
				$this->redirect($this->request->param('Content.url') . '/index');
			}

			$this->MailMessage->create($this->request->data);

			// データの入力チェックを行う
			if ($this->MailMessage->validates()) {

				// 送信データを保存するか確認
				if ($this->dbDatas['mailContent']['MailContent']['save_info']) {
					// validation OK
					$result = $this->MailMessage->save(null, false);
				} else {
					$result = $this->MailMessage->data;
				}

				if ($result) {

					// メール送信用にハッシュ化前のパスワードをマスクして保持
					$sendEmailPasswords = [];
					foreach($this->dbDatas['mailFields'] as $key => $field) {
						if ($field['MailField']['type'] === 'password') {
							$sendEmailPasswords[$field['MailField']['field_name']] = preg_replace(
								'/./',
								'*',
								$this->request->data['MailMessage'][$field['MailField']['field_name']]
							);
						}
					}

					$this->request->data = $result;

					/*** Mail.beforeSendEmail ***/
					$event = $this->dispatchEvent('beforeSendEmail', [
						'data' => $this->request->data
					]);
					$sendEmailOptions = [];
					if ($event !== false) {
						$this->request->data = $event->result === true ? $event->data['data'] : $event->result;
						if (!empty($event->data['sendEmailOptions'])) {
							$sendEmailOptions = $event->data['sendEmailOptions'];
						}
					}
					if (!empty($sendEmailPasswords)) {
						$sendEmailOptions['maskedPasswords'] = $sendEmailPasswords;
					}

					// メール送信
					if ($this->_sendEmail($sendEmailOptions)) {
						if (!$this->dbDatas['mailContent']['MailContent']['save_info']) {
							$fileRecords = [];
							foreach($this->dbDatas['mailFields'] as $key => $field) {
								if ($field['MailField']['type'] !== 'file') {
									continue;
								}
								// 削除フラグをセット
								$field_name = $field['MailField']['field_name'];
								$fileRecords['MailMessage'] = [
									$field_name => $this->request->data['MailMessage'][$field_name],
									$field_name . '_delete' => true,
								];
								// BcUploadBehavior::deleteFiles() はデータベースのデータを削除する前提となっているため、
								// Model->data['MailMessage']['field_name'] に、配列ではなく、文字列がセットされている状態を想定しているので状態を模倣する
								$this->MailMessage->data['MailMessage'][$field_name] = $this->request->data['MailMessage'][$field_name];
							}
							$this->MailMessage->deleteFiles($fileRecords);
						}

						$this->Session->delete('Mail.valid');

						/*** Mail.afterSendEmail ***/
						$this->dispatchEvent('afterSendEmail', [
							'data' => $this->request->data
						]);
					} else {
						$this->BcMessage->setError(
							__('エラー : 送信中にエラーが発生しました。しばらくたってから再度送信お願いします。')
						);
						$this->redirect($this->request->param('Content.url'));
					}
				} else {
					$this->BcMessage->setError(
						__('エラー : 送信中にエラーが発生しました。しばらくたってから再度送信お願いします。')
					);
					$this->redirect($this->request->param('Content.url'));
				}

				$this->Session->write('Mail.MailContent', $this->dbDatas['mailContent']);
				$this->redirect($this->request->param('Content.url') . '/thanks');

				// 入力検証エラー
			} else {
				$this->set('freezed', false);
				$this->set('error', true);

				$this->BcMessage->setError('Error : Confirm your entries and send again.');
				$this->request->data['MailMessage']['auth_captcha'] = null;
				$this->request->data['MailMessage']['captcha_id'] = null;
				$this->action = 'index'; //viewのボタンの表示の切り替えに必要なため変更
				if ($this->dbDatas['mailFields']) {
					$this->set('mailFields', $this->dbDatas['mailFields']);
				}

				$this->set('mailContent', $this->dbDatas['mailContent']);
				$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'index');
			}
		}
		$user = BcUtil::loginUser('admin');
		if (!empty($user)) {
			$this->set(
				'editLink',
				[
					'admin' => true,
					'plugin' => 'mail',
					'controller' => 'mail_contents',
					'action' => 'edit',
					$this->dbDatas['mailContent']['MailContent']['id']
				]
			);
		}
	}

	/**
	 * [PUBIC] メール送信完了
	 *
	 * @return void
	 */
	public function thanks()
	{
		$isAccepting = $this->MailContent->isAccepting(
			$this->dbDatas['mailContent']['MailContent']['publish_begin'],
			$this->dbDatas['mailContent']['MailContent']['publish_end']
		);
		if (!$isAccepting) {
			$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'unpublish');
			return;
		}

		$mailContent = $this->Session->consume('Mail.MailContent');
		if (!$mailContent) {
			$this->notFound();
		}

		$this->set('mailContent', $mailContent);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'submit');
	}

	/**
	 * [private] 確認画面から戻る
	 *
	 * @param mixed mail_content_id
	 * @return void
	 */
	public function _back($id)
	{
		$this->set('freezed', false);
		$this->set('error', false);
		$this->request->data['MailMessage']['auth_captcha'] = null;
		$this->request->data['MailMessage']['captcha_id'] = null;
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
		$user = BcUtil::loginUser('admin');
		if (!empty($user)) {
			$this->set(
				'editLink',
				[
					'admin' => true,
					'plugin' => 'mail',
					'controller' => 'mail_contents',
					'action' => 'edit',
					$this->dbDatas['mailContent']['MailContent']['id']
				]
			);
		}
		$this->set('mailContent', $this->dbDatas['mailContent']);
		$this->render($this->dbDatas['mailContent']['MailContent']['form_template'] . DS . 'index');
	}

	/**
	 * メール送信する
	 *
	 * @return false|void
	 */
	protected function _sendEmail($options)
	{
		$options = array_merge(
			[
				'toUser' => [],
				'toAdmin' => [],
			],
			$options
		);

		$mailConfig = $this->dbDatas['mailConfig']['MailConfig'];
		$mailContent = $this->dbDatas['mailContent']['MailContent'];
		$userMail = '';

		// データを整形
		$data = $this->MailMessage->convertToDb($this->request->data);

		$data['message'] = $data['MailMessage'];
		unset($data['MailMessage']);
		$data['content'] = $this->request->param('Content');
		$data['mailFields'] = $this->dbDatas['mailFields'];
		$data['mailContents'] = $this->dbDatas['mailContent']['MailContent'];
		$data['mailConfig'] = $this->dbDatas['mailConfig']['MailConfig'];
		$data['other']['date'] = date('Y/m/d H:i');
		$data = $this->MailMessage->convertDatasToMail($data);

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

		// 送信先名を取得
		if ($mailContent['sender_name']) {
			$fromName = $mailContent['sender_name'];
		} else {
			$fromName = $this->siteConfigs['name'];
		}

		$attachments = [];
		$settings = $this->MailMessage->Behaviors->BcUpload->BcFileUploader['MailMessage']->settings;
		foreach($this->dbDatas['mailFields'] as $mailField) {
			$field = $mailField['MailField']['field_name'];
			if (!isset($data['message'][$field])) {
				continue;
			}
			$value = $data['message'][$field];
			// ユーザーメールを取得
			if ($mailField['MailField']['type'] === 'email' && $value) {
				$userMail = $value;
			}
			// 件名にフィールドの値を埋め込む
			// 和暦など配列の場合は無視
			if (!is_array($value)) {
				$mailContent['subject_user'] = str_replace(
					'{$' . $field . '}',
					$value,
					$mailContent['subject_user']
				);
				$mailContent['subject_admin'] = str_replace(
					'{$' . $field . '}',
					$value,
					$mailContent['subject_admin']
				);
			}
			if ($mailField['MailField']['type'] === 'file' && $value) {
				$attachments[] = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . $value;
			}
			// パスワードは入力値をマスクした値を表示
			if ($mailField['MailField']['type'] === 'password' && $value && !empty($options['maskedPasswords'][$field])) {
				$data['message'][$field] = $options['maskedPasswords'][$field];
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

		// 管理者に送信
		if (!empty($adminMail)) {
			$data['other']['mode'] = 'admin';
			$sendResult = $this->sendMail(
				$adminMail,
				$mailContent['subject_admin'],
				$data,
				array_merge(
					[
						'fromName' => $fromName,
						// カンマ区切りで複数設定されていた場合先頭のアドレスをreplayToに利用
						'replyTo' => strpos($userMail, ',') === false? $userMail : strstr($userMail, ',', true),
						'from' => $fromAdmin,
						'template' => 'Mail.' . $mailContent['mail_template'],
						'bcc' => $mailContent['sender_2'],
						'agentTemplate' => false,
						'attachments' => $attachments,
						// 'additionalParameters' => '-f ' . $fromAdmin,
					],
					$options['toAdmin']
				)
			);
			if (!$sendResult) {
				return false;
			}
		}

		// ユーザーに送信
		if (!empty($userMail)) {
			$site = BcSite::findCurrent();
			$data['other']['mode'] = 'user';
			$sendResult = $this->sendMail(
				$userMail,
				$mailContent['subject_user'],
				$data,
				array_merge(
					[
						'fromName' => $mailContent['sender_name'],
						'from' => $fromAdmin,
						'template' => 'Mail.' . $mailContent['mail_template'],
						'replyTo' => $fromAdmin,
						'agentTemplate' => ($site && $site->device)? true : false,
						// 'additionalParameters' => '-f ' . $fromAdmin,
					],
					$options['toUser']
				)
			);
			if (!$sendResult) {
				return false;
			}
		}

		return true;
	}

	/**
	 * ファイルフィールドのデータがアップロードされたファイルパスであることを検証する
	 *
	 * @return boolean
	 */
	private function _checkDirectoryRraversal()
	{
		if (!isset($this->dbDatas['mailFields'])
			|| !is_array($this->dbDatas['mailFields'])
			|| empty($this->MailMessage->Behaviors->BcUpload->BcFileUploader['MailMessage']->settings)) {
			return false;
		}

		foreach($this->dbDatas['mailFields'] as $mailField) {
			if ($mailField['MailField']['type'] !== 'file') {
				continue;
			}
			$tmp_name = Hash::get(
				$this->request->data,
				sprintf(
					'MailMessage.%s.tmp_name',
					Hash::get($mailField, 'MailField.field_name')
				)
			);
			if ($tmp_name && !is_uploaded_file($tmp_name)) {
				return false;
			}
		}
		return true;
	}

	/**
	 * 認証用のキャプチャ画像を表示する
	 *
	 * @return void
	 */
	public function captcha($token = null)
	{
		$this->BcCaptcha->render($token);
		exit();
	}

}
