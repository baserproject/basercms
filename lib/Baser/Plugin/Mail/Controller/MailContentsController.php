<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Controller
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
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
class MailContentsController extends MailAppController {

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
	public $uses = array("Mail.MailContent", 'Mail.MailMessage');

/**
 * ヘルパー
 *
 * @var array
 */
	public $helpers = array('BcHtml', 'BcTime', 'BcForm', 'BcText', 'Mail.Mail');

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
	public $subMenuElements = array();

/**
 * [ADMIN] メールフォーム一覧
 *
 * @return void
 */
	public function admin_index() {
		$listDatas = $this->MailContent->find('all');
		$this->set('listDatas', $listDatas);
		$this->subMenuElements = array('mail_common');
		$this->pageTitle = 'メールフォーム一覧';
		$this->help = 'mail_contents_index';
	}

/**
 * メールフォーム登録
 *
 * @return mixed json|false
 */
	public function admin_ajax_add() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$this->request->data['MailContent'] = $this->MailContent->getDefaultValue()['MailContent'];
		$data = $this->MailContent->save($this->request->data);
		if ($data) {
			$this->MailMessage->createTable($data['MailContent']['id']);
			$message = 'メールフォーム「' . $this->request->data['Content']['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode($data['Content']);
		} else {
			$this->ajaxError(500, $this->MailContent->validationErrors);
		}
		return false;
	}
	
/**
 * [ADMIN] メールフォーム追加
 *
 * @return void
 */
	public function admin_add() {
		$this->pageTitle = '新規メールフォーム登録';

		if (!$this->request->data) {
			$this->request->data = $this->MailContent->getDefaultValue();
		} else {

			/* 登録処理 */
			if (!$this->request->data['MailContent']['sender_1_']) {
				$this->request->data['MailContent']['sender_1'] = '';
			}
			$this->MailContent->create($this->request->data);
			if ($this->MailContent->validates()) {
				if ($this->MailMessage->createTable($this->request->data['MailContent']['id'])) {
					/* データを保存 */
					if ($this->MailContent->save(null, false)) {
						$this->setMessage('新規メールフォーム「' . $this->request->data['MailContent']['title'] . '」を追加しました。', false, true);
						$this->redirect(array('action' => 'edit', $this->MailContent->id));
					} else {
						$this->setMessage('データベース処理中にエラーが発生しました。', true);
					}
				} else {
					$this->setMessage('データベースに問題があります。メール受信データ保存用テーブルの作成に失敗しました。', true);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}
		$this->subMenuElements = array('mail_common');
		$this->help = 'mail_contents_form';
		$this->render('form');
	}

/**
 * [ADMIN] 編集処理
 *
 * @param int ID
 * @return void
 */
	public function admin_edit($id) {

		if (!$id && empty($this->request->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
		}

		if (empty($this->request->data['MailContent']['id'])) {
			$this->request->data = $this->MailContent->read(null, $id);
			if(!$this->request->data) {
				$this->setMessage('無効な処理です。', true);
				$this->redirect(['plugin' => false, 'admin' => true, 'controller' => 'contents', 'action' => 'index']);
			}
		} else {
			if (!$this->request->data['MailContent']['sender_1_']) {
				$this->request->data['MailContent']['sender_1'] = '';
			}
			$this->MailContent->set($this->request->data);
			if ($this->MailContent->validates()) {
				if ($this->MailContent->save(null, false)) {
					$this->setMessage('メールフォーム「' . $this->request->data['Content']['title'] . '」を更新しました。', false, true);
					if ($this->request->data['MailContent']['edit_mail_form']) {
						$this->redirectEditForm($this->request->data['MailContent']['form_template']);
					} elseif ($this->request->data['MailContent']['edit_mail']) {
						$this->redirectEditMail($this->request->data['MailContent']['mail_template']);
					} else {
						$this->redirect(array('action' => 'edit', $this->request->data['MailContent']['id']));
					}
				} else {
					$this->setMessage('データベース処理中にエラーが発生しました。', true);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		$this->request->params['Content'] = $this->BcContents->getContent($id)['Content'];
		if($this->request->data['Content']['status']) {
			$this->set('publishLink', $this->request->data['Content']['url']);
		}
		$this->set('mailContent', $this->request->data);
		$this->subMenuElements = ['mail_fields'];
		$this->pageTitle = 'メールフォーム設定編集：' . $this->request->data['Content']['title'];
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
	public function admin_delete() {
		if(empty($this->request->data['entityId'])) {
			return false;
		}
		if($this->MailContent->delete($this->request->data['entityId'])) {
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
	public function redirectEditMail($template) {
		$type = 'Emails';
		$path = 'text' . DS . $template . $this->ext;
		$target = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $type . DS . $path;
		$sorces = array(BASER_PLUGINS . 'Mail' . DS . 'View' . DS . $type . DS . $path);
		if ($this->siteConfigs['theme']) {
			if (!file_exists($target)) {
				foreach ($sorces as $source) {
					if (file_exists($source)) {
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						copy($source, $target);
						chmod($target, 0666);
						break;
					}
				}
			}
			$path = str_replace(DS, '/', $path);
			$this->redirect(array_merge(array('plugin' => null, 'mail' => false, 'prefix' => false, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], $type), explode('/', $path)));
		} else {
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * メールフォーム編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 */
	public function redirectEditForm($template) {
		$path = 'Mail' . DS . $template;
		$target = WWW_ROOT . 'theme' . DS . $this->siteConfigs['theme'] . DS . $path;
		$sorces = array(BASER_PLUGINS . 'Mail' . DS . 'View' . DS . $path);
		if ($this->siteConfigs['theme']) {
			if (!file_exists($target . DS . 'index' . $this->ext)) {
				foreach ($sorces as $source) {
					if (is_dir($source)) {
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						$folder->copy(array('from' => $source, 'to' => $target, 'chmod' => 0777, 'skip' => array('_notes')));
						break;
					}
				}
			}
			$path = str_replace(DS, '/', $path);
			$this->redirect(array_merge(array('plugin' => null, 'mail' => false, 'prefix' => false, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'etc'), explode('/', $path . '/index' . $this->ext)));
		} else {
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(array('action' => 'index'));
		}
	}

/**
 * コピー
 *
 * @return bool
 */
	public function admin_ajax_copy() {
		$this->autoRender = false;
		if(!$this->request->data) {
			$this->ajaxError(500, '無効な処理です。');
		}
		$user = $this->BcAuth->user();
		$data = $this->MailContent->copy($this->request->data['entityId'], $this->request->data['parentId'], $this->request->data['title'], $user['id'], $this->request->data['siteId']);
		if ($data) {
			$message = 'メールフォームのコピー「' . $this->request->data['title'] . '」を追加しました。';
			$this->setMessage($message, false, true, false);
			return json_encode($data['Content']);
		} else {
			$this->ajaxError(500, $this->MailContent->validationErrors);
		}
		return false;
	}

}
