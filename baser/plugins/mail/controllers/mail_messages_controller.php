<?php
/* SVN FILE: $Id$ */
/**
 * 受信メールコントローラー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								1-19-4 ikinomatsubara, fukuoka-shi
 *								fukuoka, Japan 819-0055
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.controllers
 * @since			Baser v 0.1.0
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
	var $helpers = array('Mail.maildata', 'Mail.mailfield', 'TextEx', 'Array');
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
		$this->navis = array(
			'メールフォーム管理'									=> array('plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'index'),
			$this->mailContent['MailContent']['title'].'管理'	=> array('plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $this->params['pass'][0])
		);
		
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
			$this->Session->setFlash('無効な処理です。');
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
		$this->navis = am(
			$this->navis, array('受信メール一覧' => array('controller' => 'mail_messages', 'action' => 'index', $this->params['pass'][0]))
		);
		$this->set(compact('mailFields'));
		$this->set(compact('message'));
		$this->pageTitle = '受信メール詳細';

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
			$this->Session->setFlash('無効な処理です。');
			$this->notFound();
		}
		if($this->Message->del($messageId)) {
			$message = $this->mailContent['MailContent']['title'].'への受信データ NO「'.$messageId.'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->Message->saveDbLog($message);
		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}
		$this->redirect(array('action' => 'index', $mailContentId));

	}

}
?>