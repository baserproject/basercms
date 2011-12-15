<?php
/* SVN FILE: $Id$ */
/**
 * メールフォーム設定コントローラー
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.controllers
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
 * メールフォーム設定コントローラー
 *
 * @package baser.plugins.mail.controllers
 */
class MailConfigsController extends MailAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'MailConfigs';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Mail.MailConfig');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * サブメニューエレメント
 *
 * @var array
 * @access public
 */
	var $subMenuElements = array();
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $navis = array('メールフォーム管理' => array('plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'index'));
/**
 * [ADMIN] メールフォーム設定
 *
 * @return void
 * @access public
 */
	function admin_form() {

		if(empty($this->data)) {
			$this->data = $this->MailConfig->read(null, 1);
		}else {

			/* 更新処理 */
			if($this->MailConfig->save($this->data)) {
				$this->Session->setFlash('メールフォーム設定を保存しました。');
				$this->redirect(array('action' => 'form'));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('mail_common');
		$this->pageTitle = 'メールプラグイン基本設定';

	}
	
}
?>