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
 * 受信メールコントローラー
 *
 * @package Mail.Controller
 */
class MailMessagesController extends MailAppController
{

	/**
	 * クラス名
	 *
	 * @var string
	 */
	public $name = 'MailMessages';

	/**
	 * モデル
	 *
	 * @var array
	 */
	public $uses = ['Mail.MailContent', 'Mail.MailField', 'Mail.MailMessage'];

	/**
	 * ヘルパー
	 *
	 * @var array
	 */
	public $helpers = ['Mail.Maildata', 'Mail.Mailfield', 'BcText', 'BcArray'];

	/**
	 * コンポーネント
	 *
	 * @var array
	 */
	public $components = ['BcAuth', 'Cookie', 'BcAuthConfigure', 'BcContents'];

	/**
	 * メールコンテンツデータ
	 *
	 * @var array
	 */
	public $mailContent;

	/**
	 * サブメニュー
	 *
	 * @var array
	 */
	public $subMenuElements = ['mail_fields'];

	/**
	 * beforeFilter
	 *
	 * @return void
	 */
	public function beforeFilter()
	{
		parent::beforeFilter();
		$content = $this->BcContents->getContent($this->request->param('pass.0'));
		if (!$content) {
			$this->notFound();
		}
		$this->mailContent = $this->MailContent->read(null, $this->request->param('pass.0'));
		App::uses('MailMessage', 'Mail.Model');
		$this->MailMessage = new MailMessage();
		$this->MailMessage->setup($this->mailContent['MailContent']['id']);
		$mailContentId = $this->request->param('pass.0');
		$content = $this->BcContents->getContent($mailContentId);
		$this->request->param('Content', $content['Content']);
		$this->crumbs[] = [
			'name' => sprintf(
				__d('baser', '%s 管理'),
				$this->request->param('Content.title')
			),
			'url' => [
				'plugin' => 'mail',
				'controller' => 'mail_fields',
				'action' => 'index',
				$this->request->param('pass.0')
			]
		];
	}

	/**
	 * beforeRender
	 *
	 * @return void
	 */
	public function beforeRender()
	{
		parent::beforeRender();
		$this->set('mailContent', $this->mailContent);
	}

	/**
	 * [ADMIN] 受信メール一覧
	 *
	 * @param int $mailContentId
	 * @return void
	 */
	public function admin_index($mailContentId)
	{
		$default = ['named' => ['num' => $this->siteConfigs['admin_list_num']]];
		$this->setViewConditions('MailMessage', ['default' => $default]);
		$this->paginate = [
			'fields' => [],
			'order' => 'MailMessage.created DESC',
			'limit' => $this->passedArgs['num']
		];
		$messages = $this->paginate('MailMessage');
		$mailFields = $this->MailMessage->mailFields;

		$this->set(compact('messages', 'mailFields'));

		if ($this->RequestHandler->isAjax() || !empty($this->request->query['ajax'])) {
			$this->render('ajax_index');
			return;
		}

		$this->pageTitle = sprintf(
			__d('baser', '%s｜受信メール一覧'),
			$this->request->param('Content.title')
		);
		$this->help = 'mail_messages_index';
	}

	/**
	 * [ADMIN] 受信メール詳細
	 *
	 * @param int $mailContentId
	 * @param int $messageId
	 * @return void
	 */
	public function admin_view($mailContentId, $messageId)
	{
		if (!$mailContentId || !$messageId) {
			$this->BcMessage->setError(__d('baser', '無効な処理です。'));
			$this->notFound();
		}
		$message = $this->MailMessage->find('first', [
			'conditions' => ['MailMessage.id' => $messageId],
			'order' => 'created DESC'
		]);
		$mailFields = $this->MailMessage->mailFields;

		$this->crumbs[] = [
			'name' => __d('baser', '受信メール一覧'),
			'url' => [
				'controller' => 'mail_messages',
				'action' => 'index',
				$this->params['pass'][0]
			]
		];
		$this->set(compact('message', 'mailFields'));
		$this->pageTitle = sprintf(
			__d('baser', '%s｜受信メール詳細'),
			$this->request->param('Content.title')
		);
	}

	/**
	 * [ADMIN] 受信メール一括削除
	 *
	 * @param int $mailContentId
	 * @param int $messageId
	 * @return bool
	 */
	protected function _batch_del($ids)
	{
		if ($ids) {
			foreach($ids as $id) {
				$this->_del($id);
			}
		}
		return true;
	}

	/**
	 * [ADMIN] 受信メール削除　(ajax)
	 *
	 * @param int $mailContentId
	 * @param int $messageId
	 * @return void
	 */
	public function admin_ajax_delete($mailContentId, $messageId)
	{
		$this->_checkSubmitToken();
		if (!$messageId) {
			$this->ajaxError(500, __d('baser', '無効な処理です。'));
		}
		if (!$this->_del($messageId)) {
			exit;
		}

		exit(true);
	}

	/**
	 * 受信メール削除　
	 *
	 * @param int $mailContentId
	 * @param int $messageId
	 * @return bool
	 */
	protected function _del($id = null)
	{
		if (!$this->MailMessage->delete($id)) {
			return false;
		}

		$message = sprintf(__d('baser', '受信データ NO「%s」 を削除しました。'), $id);
		$this->MailMessage->saveDbLog($message);
		return true;
	}

	/**
	 * [ADMIN] 受信メール削除
	 *
	 * @param int $mailContentId
	 * @param int $messageId
	 * @return void
	 */
	public function admin_delete($mailContentId, $messageId)
	{
		$this->_checkSubmitToken();
		if (!$mailContentId || !$messageId) {
			$this->BcMessage->setError(__d('baser', '無効な処理です。'));
			$this->notFound();
		}
		if (!$this->MailMessage->delete($messageId)) {
			$this->BcMessage->setError(
				__d('baser', 'データベース処理中にエラーが発生しました。')
			);
		} else {
			$this->BcMessage->setSuccess(
				sprintf(
					__d('baser', '%s への受信データ NO「%s」 を削除しました。'),
					$this->mailContent['Content']['title'],
					$messageId
				)
			);
		}
		$this->redirect(['action' => 'index', $mailContentId]);
	}

	/**
	 * メールフォームに添付したファイルを開く
	 */
	public function admin_attachment()
	{
		$args = func_get_args();
		unset($args[0]);
		$file = implode('/', $args);
		$settings = $this->MailMessage->Behaviors->BcUpload->BcFileUploader['MailMessage']->settings;
		$filePath = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . $file;
		$ext = decodeContent(null, $file);
		$mineType = 'application/octet-stream';
		if ($ext !== 'gif' && $ext !== 'jpg' && $ext !== 'png') {
			Header("Content-disposition: attachment; filename=" . $file);
		} else {
			$mineType = 'image/' . $ext;
		}
		Header(sprintf('Content-type: %s; name=%s', $mineType, $file));
		echo file_get_contents($filePath);
		exit();
	}

}
