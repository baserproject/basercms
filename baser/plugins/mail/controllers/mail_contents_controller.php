<?php
/* SVN FILE: $Id$ */
/**
 * メールコンテンツコントローラー
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
/**
 * メールコンテンツコントローラー
 *
 * @package			baser.plugins.mail.controllers
 */
class MailContentsController extends MailAppController {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'MailContents';
/**
 * モデル
 *
 * @var 	array
 * @access 	public
 */
	var $uses = array("Mail.MailContent",'Mail.Message');
/**
 * ヘルパー
 *
 * @var 	array
 * @access 	public
 */
	var $helpers = array('Html','TimeEx','FormEx','TextEx', 'Mail.Mail');
/**
 * コンポーネント
 *
 * @var     array
 * @access  public
 */
	var $components = array('Auth','Cookie','AuthConfigure');
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
 * サブメニューエレメント
 *
 * @var		string
 * @access 	public
 */
	var $subMenuElements = array();
/**
 * [ADMIN] メールフォーム一覧
 *
 * @return  void
 * @access  public
 */
	function admin_index() {

		$listDatas = $this->MailContent->findAll();
		$this->set('listDatas',$listDatas);
		$this->subMenuElements = array('mail_common');
		$this->pageTitle = 'メールフォーム一覧';

	}
/**
 * [ADMIN] メールフォーム追加
 *
 * @return  void
 * @access  public
 */
	function admin_add() {

		$this->pageTitle = '新規メールフォーム登録';

		if(!$this->data) {
			$this->data = $this->MailContent->getDefaultValue();
		}else {

			/* 登録処理 */
			if(!$this->data['MailContent']['sender_1_']) {
				$this->data['MailContent']['sender_1'] = '';
			}
			$this->MailContent->create($this->data);

			/* データを保存 */
			if($this->MailContent->save()) {

				// 新しいメッセージテーブルを作成
				$this->Message->createTable($this->data['MailContent']['name']);

				$message = '新規メールフォーム「'.$this->data['MailContent']['title'].'」を追加しました。';
				$this->Session->setFlash($message);
				$this->MailContent->saveDbLog($message);
				$this->redirect(array('controller'=>'mail_contents','action'=>'index'));
			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}
		$this->subMenuElements = array('mail_common');
		$this->render('form');

	}
/**
 * [ADMIN] 編集処理
 *
 @ @param	int		ID
 * @return	void
 * @access 	public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		if(empty($this->data)) {
			$this->data = $this->MailContent->read(null, $id);
			$this->set('mailContent',$this->data);
		}else {
			$old = $this->MailContent->read(null,$id);
			if(!$this->data['MailContent']['sender_1_']) {
				$this->data['MailContent']['sender_1'] = '';
			}
			/* 更新処理 */
			if($this->MailContent->save($this->data)) {

				// メッセージテーブルの名前を変更
				if($old['MailContent']['name'] != $this->data['MailContent']['name']) {
					$this->Message->renameTable($old['MailContent']['name'],$this->data['MailContent']['name']);
				}

				$message = 'メールフォーム「'.$this->data['MailContent']['title'].'」を更新しました。';
				$this->Session->setFlash($message);
				$this->MailContent->saveDbLog($message);

				if($this->data['MailContent']['edit_layout']){
					$this->redirectEditLayout($this->data['MailContent']['layout_template']);
				}elseif ($this->data['MailContent']['edit_mail_form']) {
					$this->redirectEditForm($this->data['MailContent']['form_template']);
				}elseif ($this->data['MailContent']['edit_mail']) {
					$this->redirectEditMail($this->data['MailContent']['mail_template']);
				}else{
					$this->redirect(array('action'=>'index'));
				}

			}else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}

		}

		/* 表示設定 */
		$this->subMenuElements = array('mail_fields','mail_common');
		$this->pageTitle = 'メールフォーム設定編集：'.$this->data['MailContent']['title'];
		$this->render('form');

	}
/**
 * レイアウト編集画面にリダイレクトする
 * @param	string	$template
 * @return	void
 * @access	public
 */
	function redirectEditLayout($template){
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.'layouts'.DS.$template.'.ctp';
		$sorces = array(BASER_PLUGINS.'mail'.DS.'views'.DS.'layouts'.DS.$template.'.ctp',
						BASER_VIEWS.'layouts'.DS.$template.'.ctp');
		if($this->siteConfigs['theme']){
			if(!file_exists($target)){
				foreach($sorces as $source){
					if(file_exists($source)){
						copy($source,$target);
						chmod($target,0666);
						break;
					}
				}
			}
			$this->redirect(array('plugin'=>null,'mail'=>false,'prefix'=>false,'controller'=>'theme_files','action'=>'edit',$this->siteConfigs['theme'],'layouts',$template.'.ctp'));
		}else{
			$this->Session->setFlash('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。');
			$this->redirect(array('action'=>'index'));
		}
	}
/**
 * メール編集画面にリダイレクトする
 * @param	string	$template
 * @return	void
 * @access	public
 */
	function redirectEditMail($template){
		$type = 'elements';
		$path = 'email'.DS.'text'.DS.$template.'.ctp';
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$type.DS.$path;
		$sorces = array(BASER_PLUGINS.'mail'.DS.'views'.DS.$type.DS.$path);
		if($this->siteConfigs['theme']){
			if(!file_exists($target)){
				foreach($sorces as $source){
					if(file_exists($source)){
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						copy($source,$target);
						chmod($target,0666);
						break;
					}
				}
			}
			$this->redirect(array('plugin'=>null,'mail'=>false,'prefix'=>false,'controller'=>'theme_files','action'=>'edit',$this->siteConfigs['theme'],$type,$path));
		}else{
			$this->Session->setFlash('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。');
			$this->redirect(array('action'=>'index'));
		}
	}
/**
 * メールフォーム編集画面にリダイレクトする
 * @param	string	$template
 * @return	void
 * @access	public
 */
	function redirectEditForm($template){
		$path = 'mail'.DS.$template;
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$path;
		$sorces = array(BASER_PLUGINS.'mail'.DS.'views'.DS.$path);
		if($this->siteConfigs['theme']){
			if(!file_exists($target.DS.'index.ctp')){
				foreach($sorces as $source){
					if(is_dir($source)){
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						$folder->copy(array('from'=>$source,'to'=>$target,'chmod'=>0777,'skip'=>array('_notes')));
						break;
					}
				}
			}
			$this->redirect(array('plugin'=>null,'mail'=>false,'prefix'=>false,'controller'=>'theme_files','action'=>'edit',$this->siteConfigs['theme'],'etc',$path.DS.'index.ctp'));
		}else{
			$this->Session->setFlash('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。');
			$this->redirect(array('action'=>'index'));
		}
	}
/**
 * [ADMIN] 削除処理
 *
 @ @param	int		ID
 * @return	void
 * @access 	public
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action'=>'admin_index'));
		}

		// メッセージ用にデータを取得
		$mailContent = $this->MailContent->read(null, $id);

		/* 削除処理 */
		if($this->MailContent->del($id)) {

			// メッセージテーブルを削除
			$this->Message->deleteTable($mailContent['MailContent']['name']);

			$message = 'メールフォーム「'.$mailContent['MailContent']['title'].'」 を削除しました。';
			$this->Session->setFlash($message);
			$this->MailContent->saveDbLog($message);

		}else {
			$this->Session->setFlash('データベース処理中にエラーが発生しました。');
		}

		$this->redirect(array('action'=>'index'));

	}
}
?>