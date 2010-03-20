<?php
/* SVN FILE: $Id$ */
/**
 * メールフォーム設定コントローラー
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
 * @package			baser.plugins.mail.controllers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Controller', 'Auth');
/**
 * メールフォーム設定コントローラー
 *
 * @package			baser.plugins.mail.controllers
 */
class MailConfigsController extends MailAppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'MailConfigs';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array('Mail.MailConfig');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
    var $components = array('Auth','Cookie','AuthConfigure');
/**
 * サブメニューエレメント
 *
 * @var 	array
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * ぱんくずナビ
 *
 * @var		string
 * @access 	public
 */
	var $navis = array('システム設定'=>'/admin/site_configs/form',
                        'プラグイン設定'=>'/admin/plugins/index',
                        'メールフォーム管理'=>'/admin/mail/mail_contents/index');
/**
 * [ADMIN] メールフォーム設定
 * 
 * @return	void
 * @access	public
 */
	function admin_form(){
				
		if(empty($this->data)){
			$this->data = $this->MailConfig->read(null, 1);
		}else{
					
			/* 更新処理 */
			if($this->MailConfig->save($this->data)){
				$this->Session->setFlash('メールフォーム設定を保存しました。');
				$this->redirect(array('action'=>'form'));
			}else{
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}
			
		}
		
		/* 表示設定 */
        $this->subMenuElements = array('mail_common');
		$this->pageTitle = 'メールプラグイン基本設定';
		
	}

}
?>