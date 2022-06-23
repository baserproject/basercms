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

/**
 * メールコンテンツコントローラー
 *
 * @package Mail.Controller
 * @property MailMessage $MailMessage
 * @property MailContent $MailContent
 * @property BcAuthComponent $BcAuth
 * @property CookieComponent $Cookie
 * @property BcAuthConfigureComponent $BcAuthConfigure
 * @property BcContentsComponent $BcContents
 * @property Content $Content
 */
class MailContentsController extends MailAppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'MailContents';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ["Mail.MailContent", 'Mail.MailMessage'];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['BcHtml', 'BcTime', 'BcForm', 'BcText', 'Mail.Mail'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents' => ['useForm' => true]];

	/**
	 * サブメニューエレメント
	 *
	 * @var string
	 */
	public $subMenuElements = [];

	/**
	 * [ADMIN] メールフォーム一覧
	 *
	 * @return void
	 */
	public function admin_index()
	{
		$listDatas = $this->MailContent->find('all');
		$this->set('listDatas', $listDatas);
		$this->subMenuElements = ['mail_common'];
		$this->pageTitle = __d('baser', 'メールフォーム一覧');
		$this->help = 'mail_contents_index';
	}

	/**
	 * メールフォーム登録
	 *
	 * @return mixed json|false
	 */
	public function admin_ajax_add()
	{
		$this->autoRender = false;
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		$this->request->data['MailContent'] = $this->MailContent->getDefaultValue()['MailContent'];
		$data = $this->MailContent->save($this->request->data);
		if (!$data) {
			$this->ajaxError(500, $this->MailContent->validationErrors);
			return false;
		}
		$this->MailMessage->createTable($data['MailContent']['id']);
		$this->BcMessage->setSuccess(
			sprintf(
				__d('baser', 'メールフォーム「%s」を追加しました。'),
				$this->request->data['Content']['title']
			),
			true,
			false
		);
		return json_encode($data['Content']);
	}

	/**
	 * [ADMIN] メールフォーム追加
	 *
	 * @return void
	 */
	public function admin_add()
	{
		$this->pageTitle = __d('baser', '新規メールフォーム登録');
		$this->subMenuElements = ['mail_common'];
		$this->help = 'mail_contents_form';

		if (!$this->request->data) {
			$this->request->data = $this->MailContent->getDefaultValue();
			$this->render('form');
			return;
		}

		/* 登録処理 */
		if (!$this->request->data['MailContent']['sender_1_']) {
			$this->request->data['MailContent']['sender_1'] = '';
		}
		$this->MailContent->create($this->request->data);
		if (!$this->MailContent->validates()) {
			$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
			$this->render('form');
			return;
		}

		if (!$this->MailMessage->createTable($this->request->data['MailContent']['id'])) {
			$this->BcMessage->setError(
				__d('baser', 'データベースに問題があります。メール受信データ保存用テーブルの作成に失敗しました。')
			);
			$this->render('form');
			return;
		}

		/* データを保存 */
		if (!$this->MailContent->save(null, false)) {
			$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
			$this->render('form');
			return;
		}

		$this->BcMessage->setSuccess(
			sprintf(__d('baser', '新規メールフォーム「%s」を追加しました。'),
				$this->request->data['MailContent']['title']
			)
		);
		$this->redirect(['action' => 'edit', $this->MailContent->id]);
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

		if (!$id && empty($this->request->data)) {
			$this->BcMessage->setError(__d('baser', '無効なIDです。'));
			$this->redirect(
				['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']
			);
		}

		if (Hash::get($this->request->data, 'MailContent.id')) {
			if (!$this->request->data['MailContent']['sender_1_']) {
				$this->request->data['MailContent']['sender_1'] = '';
			}
			$this->MailContent->set($this->request->data);
			if (!$this->MailContent->save()) {
				if ($this->MailContent->validationErrors || $this->MailContent->Content->validationErrors) {
					$this->BcMessage->setError(__d('baser', '入力エラーです。内容を修正してください。'));
				} else {
					$this->BcMessage->setError(__d('baser', 'データベース処理中にエラーが発生しました。'));
				}
			} else {
				$this->BcMessage->setSuccess(
					sprintf(
						__d(
							'baser',
							'メールフォーム「%s」を更新しました。'),
						$this->request->data['Content']['title']
					)
				);
				if ($this->request->data['MailContent']['edit_mail_form']) {
					$this->redirectEditForm($this->request->data['MailContent']['form_template']);
				} elseif ($this->request->data['MailContent']['edit_mail']) {
					$this->redirectEditMail($this->request->data['MailContent']['mail_template']);
				} else {
					$this->redirect(['action' => 'edit', $this->request->data['MailContent']['id']]);
				}
			}
		} else {
			$this->request->data = $this->MailContent->read(null, $id);
			if ($this->MailContent->isOverPostSize()) {
				$this->BcMessage->setError(
					__d(
						'baser',
						'送信できるデータ量を超えています。合計で %s 以内のデータを送信してください。',
						ini_get('post_max_size')
					)
				);
			}
			if (!$this->request->data) {
				$this->BcMessage->setError(__d('baser', '無効な処理です。'));
				$this->redirect(
					['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']
				);
			}
		}

		$this->request->param('Content', $this->BcContents->getContent($id)['Content']);
		$this->set('publishLink', $this->Content->getPublishUrl($this->request->data['Content']));
		$this->set('mailContent', $this->request->data);
		$this->subMenuElements = ['mail_fields'];
		$this->pageTitle = __d('baser', 'メールフォーム設定編集');
		$this->help = 'mail_contents_form';
		$this->render('form');
	}

	/**
	 * 削除
	 *
	 * Controller::requestAction() で呼び出される
	 *
	 * @return bool
	 */
	public function admin_delete()
	{
		if (empty($this->request->data['entityId'])) {
			return false;
		}
		if ($this->MailContent->delete($this->request->data['entityId'])) {
			$this->MailMessage->dropTable($this->request->data['entityId']);
			return true;
		}
		return false;
	}

	/**
	 * メール編集画面にリダイレクトする
	 *
	 * @param string $template
	 * @return void
	 */
	public function redirectEditMail($template)
	{
		$type = 'Emails';
		$path = 'text' . DS . $template . $this->ext;
		$target = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $type . DS . $path;
		$sorces = [BASER_PLUGINS . 'Mail' . DS . 'View' . DS . $type . DS . $path];
		if ($this->siteConfigs['theme']) {
			if (!file_exists($target)) {
				foreach($sorces as $source) {
					if (!file_exists($source)) {
						continue;
					}
					$folder = new Folder();
					$folder->create(dirname($target), 0777);
					copy($source, $target);
					chmod($target, 0666);
					break;
				}
			}
			$path = str_replace(DS, '/', $path);
			$this->redirect(
				array_merge(
					[
						'plugin' => null,
						'mail' => false,
						'prefix' => false,
						'controller' => 'theme_files',
						'action' => 'edit',
						$this->siteConfigs['theme'],
						$type
					],
					explode('/', $path)
				)
			);
		} else {
			$this->BcMessage->setError(
				__d('baser', '現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。')
			);
			$this->redirect(['action' => 'index']);
		}
	}

	/**
	 * メールフォーム編集画面にリダイレクトする
	 *
	 * @param string $template
	 * @return void
	 */
	public function redirectEditForm($template)
	{
		$path = 'Mail' . DS . $template;
		$target = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $path;
		$sorces = [BASER_PLUGINS . 'Mail' . DS . 'View' . DS . $path];
		if ($this->siteConfigs['theme']) {
			if (!file_exists($target . DS . 'index' . $this->ext)) {
				foreach($sorces as $source) {
					if (!is_dir($source)) {
						continue;
					}
					$folder = new Folder();
					$folder->create(dirname($target), 0777);
					$folder->copy(
						['from' => $source, 'to' => $target, 'chmod' => 0777, 'skip' => ['_notes']]
					);
					break;
				}
			}
			$path = str_replace(DS, '/', $path);
			$this->redirect(
				array_merge(
					[
						'plugin' => null,
						'mail' => false,
						'prefix' => false,
						'controller' => 'theme_files',
						'action' => 'edit',
						$this->siteConfigs['theme'],
						'etc'
					],
					explode('/', $path . '/index' . $this->ext)
				)
			);
		} else {
			$this->BcMessage->setError(
				__d('baser', '現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。')
			);
			$this->redirect(['action' => 'index']);
		}
	}

	/**
	 * コピー
	 *
	 * @return bool
	 */
	public function admin_ajax_copy()
	{
		$this->autoRender = false;
		if (!$this->request->data) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		$user = $this->BcAuth->user();
		$data = $this->MailContent->copy(
			$this->request->data['entityId'],
			$this->request->data['parentId'],
			$this->request->data['title'],
			$user['id'],
			$this->request->data['siteId']
		);
		if (!$data) {
			$this->ajaxError(500, $this->MailContent->validationErrors);
			return false;
		}
		$message = sprintf(
			__d('baser', 'メールフォームのコピー「%s」を追加しました。'), $this->request->data['title']
		);
		$this->BcMessage->setSuccess($message, true, false);
		return json_encode($data['Content']);
	}

}
