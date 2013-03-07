<?php
/* SVN FILE: $Id$ */
/**
 * 受信メールコントローラー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.controllers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * 受信メールコントローラー
 *
 * @package baser.plugins.mail.controllers
 */
class MailMessagesController extends MailAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'MailMessages';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Mail.MailContent', 'Mail.MailField', 'Mail.Message');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array('Mail.maildata', 'Mail.mailfield', BC_TEXT_HELPER, BC_ARRAY_HELPER);
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure');
/**
 * メールコンテンツデータ
 *
 * @var array
 * @access public
 */
	var $mailContent;
/**
 * サブメニュー
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array('mail_fields','mail_common');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $crumbs = array(
		array('name' => 'プラグイン管理', 'url' => array('plugin' => '', 'controller' => 'plugins', 'action' => 'index')),
		array('name' => 'メールフォーム管理', 'url' => array('plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'index'))
	);
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function  beforeFilter() {
		
		parent::beforeFilter();
		$this->MailContent->recursive = -1;
		$this->mailContent = $this->MailContent->read(null,$this->params['pass'][0]);
		if($this->mailContent['MailContent']['name'] != 'message') {
			$prefix = $this->mailContent['MailContent']['name']."_";
			$this->Message = new Message(false,null,null,$prefix);
		}
		$this->crumbs[] = array('name' => $this->mailContent['MailContent']['title'].'管理', 'url' => array('plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $this->params['pass'][0]));
		
	}
/**
 * beforeRender
 *
 * @return void
 * @access public
 */
	function beforeRender() {

		parent::beforeRender();
		$this->set('mailContent',$this->mailContent);

	}
/**
 * [ADMIN] 受信メール一覧
 *
 * @param int $mailContentId
 * @return void
 * @access public
 */
	function admin_index($mailContentId) {

		$default = array('named' => array('num' => $this->siteConfigs['admin_list_num']));
		$this->setViewConditions('MailMessage', array('default' => $default));
		$this->paginate = array(
			'fields'=>array(),
			'order'=>'Message.created DESC',
			'limit'=>$this->passedArgs['num']
		);
		$messages = $this->paginate('Message');
		$mailFields = $this->MailField->find('all', array(
			'conditions'=> array('MailField.mail_content_id' => $mailContentId),
			'order'		=> 'MailField.sort'
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
	function admin_view($mailContentId, $messageId){

		if(!$mailContentId || !$messageId) {
			$this->setMessage('無効な処理です。', true);
			$this->notFound();
		}
		$message = $this->Message->find('first', array(
			'conditions'=>array('Message.id' => $messageId),
			'order'=>'created DESC'
		));
		$mailFields = $this->MailField->find('all', array(
			'conditions'=> array('MailField.mail_content_id' => $mailContentId),
			'order'		=> 'MailField.sort'
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
	function _batch_del($ids) {
		if($ids) {
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
 * @access public
 */
	function admin_ajax_delete($mailContentId, $messageId) {

		if(!$messageId) {
			$this->ajaxError(500, '無効な処理です。');
		}
		if($this->_del($messageId)){
			exit(true);
		}else{
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
	function _del($id = null) {
		if($this->Message->del($id)) {
			$message = '受信データ NO「'.$id.'」 を削除しました。';
			$this->Message->saveDbLog($message);
			return true;
		}else {
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
	function admin_delete($mailContentId, $messageId) {

		if(!$mailContentId || !$messageId) {
			$this->setMessage('無効な処理です。', true);
			$this->notFound();
		}
		if($this->Message->del($messageId)) {
			$this->setMessage($this->mailContent['MailContent']['title'].'への受信データ NO「'.$messageId.'」 を削除しました。', false, true);
		}else {
			$this->setMessage('データベース処理中にエラーが発生しました。', true);
		}
		$this->redirect(array('action' => 'index', $mailContentId));

	}

}
