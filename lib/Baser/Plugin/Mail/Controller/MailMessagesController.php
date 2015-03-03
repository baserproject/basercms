<?php

/**
 * 受信メールコントローラー
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
 * 受信メールコントローラー
 *
 * @package Mail.Controller
 */
class MailMessagesController extends MailAppController {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'MailMessages';

/**
 * モデル
 *
 * @var array
 * @access public
 */
	public $uses = array('Mail.MailContent', 'Mail.MailField', 'Mail.Message');

/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	public $helpers = array('Mail.Maildata', 'Mail.Mailfield', 'BcText', 'BcArray');

/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	public $components = array('BcAuth', 'Cookie', 'BcAuthConfigure');

/**
 * メールコンテンツデータ
 *
 * @var array
 * @access public
 */
	public $mailContent;

/**
 * サブメニュー
 *
 * @var array
 * @access public
 */
	public $subMenuElements = array('mail_fields');

/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	public $crumbs = array(
		array('name' => 'メールフォーム管理', 'url' => array('plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'index'))
	);

/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->MailContent->recursive = -1;
		$this->mailContent = $this->MailContent->read(null, $this->params['pass'][0]);
		if ($this->mailContent['MailContent']['name'] != 'message') {
			App::uses('Message', 'Mail.Model');
			$this->Message = new Message();
			$this->Message->setup($this->mailContent['MailContent']['id']);
		}
		$this->crumbs[] = array('name' => $this->mailContent['MailContent']['title'] . '管理', 'url' => array('plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $this->params['pass'][0]));
	}

/**
 * beforeRender
 *
 * @return void
 * @access public
 */
	public function beforeRender() {
		parent::beforeRender();
		$this->set('mailContent', $this->mailContent);
	}

/**
 * [ADMIN] 受信メール一覧
 *
 * @param int $mailContentId
 * @return void
 * @access public
 */
	public function admin_index($mailContentId) {
		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('MailMessage', array('default' => $default));
		$this->paginate = array(
			'fields' => array(),
			'order' => 'Message.created DESC',
			'limit' => $this->passedArgs['num']
		);
		$messages = $this->paginate('Message');
		$mailFields = $this->MailField->find('all', array(
			'conditions' => array('MailField.mail_content_id' => $mailContentId),
			'order' => 'MailField.sort'
		));
		$this->set(compact('mailFields'));
		$this->set(compact('messages'));
		$this->pageTitle = '受信メール一覧';
		$this->help = 'mail_messages_index';
	}

/**
 * [ADMIN] 受信メール詳細
 *
 * @param int $mailContentId
 * @param int $messageId
 * @return void
 * @access public
 */
	public function admin_view($mailContentId, $messageId) {
		if (!$mailContentId || !$messageId) {
			$this->setMessage('無効な処理です。', true);
			$this->notFound();
		}
		$message = $this->Message->find('first', array(
			'conditions' => array('Message.id' => $messageId),
			'order' => 'created DESC'
		));
		$mailFields = $this->MailField->find('all', array(
			'conditions' => array('MailField.mail_content_id' => $mailContentId),
			'order' => 'MailField.sort'
		));
		$this->crumbs[] = array('name' => '受信メール一覧', 'url' => array('controller' => 'mail_messages', 'action' => 'index', $this->params['pass'][0]));
		$this->set(compact('mailFields'));
		$this->set(compact('message'));
		$this->pageTitle = '受信メール詳細';
	}

/**
 * [ADMIN] 受信メール一括削除
 *
 * @param int $mailContentId
 * @param int $messageId
 * @return void
 * @access public
 */
	protected function _batch_del($ids) {
		if ($ids) {
			foreach ($ids as $id) {
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
 * @access public
 */
	public function admin_ajax_delete($mailContentId, $messageId) {
		if (!$messageId) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if ($this->_del($messageId)) {
			exit(true);
		} else {
			exit();
		}
	}

/**
 * 受信メール削除　
 *
 * @param int $mailContentId
 * @param int $messageId
 * @return void
 * @access public
 */
	protected function _del($id = null) {
		if ($this->Message->delete($id)) {
			$message = '受信データ NO「' . $id . '」 を削除しました。';
			$this->Message->saveDbLog($message);
			return true;
		} else {
			return false;
		}
	}

/**
 * [ADMIN] 受信メール削除
 *
 * @param int $mailContentId
 * @param int $messageId
 * @return void
 * @access public
 */
	public function admin_delete($mailContentId, $messageId) {
		if (!$mailContentId || !$messageId) {
			$this->setMessage('無効な処理です。', true);
			$this->notFound();
		}
		if ($this->Message->delete($messageId)) {
			$this->setMessage($this->mailContent['MailContent']['title'] . 'への受信データ NO「' . $messageId . '」 を削除しました。', false, true);
		} else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}
		$this->redirect(array('action' => 'index', $mailContentId));
	}

/**
 * メールフォームに添付したファイルを開く
 */
	public function admin_attachment() {
		$args = func_get_args();
		unset($args[0]);
		$file = implode('/', $args);
		$settings = $this->Message->Behaviors->BcUpload->settings['Message'];
		$filePath = WWW_ROOT . 'files' . DS . $settings['saveDir'] . DS . $file;
		$ext = decodeContent(null, $file);
		$mineType = 'application/octet-stream';
		if ($ext != 'gif' && $ext != 'jpg' && $ext != 'png') {
			Header("Content-disposition: attachment; filename=" . $file);
		} else {
			$mineType = 'image/' . $ext;
		}
		Header("Content-type: " . $mineType . "; name=" . $file);
		echo file_get_contents($filePath);
		exit();
	}
	
}
