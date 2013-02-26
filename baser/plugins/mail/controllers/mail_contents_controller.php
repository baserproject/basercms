<?php
/* SVN FILE: $Id$ */
/**
 * メールコンテンツコントローラー
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
 * Include files
 */
/**
 * メールコンテンツコントローラー
 *
 * @package baser.plugins.mail.controllers
 */
class MailContentsController extends MailAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'MailContents';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array("Mail.MailContent",'Mail.Message');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_HTML_HELPER, BC_TIME_HELPER, BC_FORM_HELPER, BC_TEXT_HELPER, 'Mail.Mail');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('BcAuth','Cookie','BcAuthConfigure');
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
 * サブメニューエレメント
 *
 * @var string
 * @access public
 */
	var $subMenuElements = array();
/**
 * [ADMIN] メールフォーム一覧
 *
 * @return void
 * @access public
 */
	function admin_index() {

		$listDatas = $this->MailContent->find('all');
		$this->set('listDatas',$listDatas);
		$this->subMenuElements = array('mail_common');
		$this->pageTitle = 'メールフォーム一覧';
		$this->help = 'mail_contents_index';

	}
/**
 * [ADMIN] メールフォーム追加
 *
 * @return void
 * @access public
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
			if($this->MailContent->validates()) {
				if ($this->Message->createTable($this->data['MailContent']['name'])) {
					/* データを保存 */
					if ($this->MailContent->save(null,false)) {
						$this->setMessage('新規メールフォーム「'.$this->data['MailContent']['title'].'」を追加しました。', false, true);
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
 * @access public
 */
	function admin_edit($id) {

		/* 除外処理 */
		if(!$id && empty($this->data)) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		if(empty($this->data)) {
			$this->data = $this->MailContent->read(null, $id);
		}else {
			$old = $this->MailContent->read(null,$id);
			if(!$this->data['MailContent']['sender_1_']) {
				$this->data['MailContent']['sender_1'] = '';
			}
			$this->MailContent->set($this->data);
			if($this->MailContent->validates()) {
				$ret = true;
				// メッセージテーブルの名前を変更
				if($old['MailContent']['name'] != $this->data['MailContent']['name']) {
					$ret = $this->Message->renameTable($old['MailContent']['name'],$this->data['MailContent']['name']);
				}
				/* 更新処理 */
				if($ret) {
					if($this->MailContent->save(null, false)) {

						$this->setMessage('メールフォーム「'.$this->data['MailContent']['title'].'」を更新しました。', false, true);

						if($this->data['MailContent']['edit_layout']){
							$this->redirectEditLayout($this->data['MailContent']['layout_template']);
						}elseif ($this->data['MailContent']['edit_mail_form']) {
							$this->redirectEditForm($this->data['MailContent']['form_template']);
						}elseif ($this->data['MailContent']['edit_mail']) {
							$this->redirectEditMail($this->data['MailContent']['mail_template']);
						}else{
							$this->redirect(array('action' => 'edit', $this->data['MailContent']['id']));
						}

					}else {
						$this->setMessage('データベース処理中にエラーが発生しました。', true);
					}
				} else {
					$this->setMessage('データベースに問題があります。メール受信データ保存用テーブルのリネームに失敗しました。', true);
				}
			} else {
				$this->setMessage('入力エラーです。内容を修正してください。', true);
			}
		}

		/* 表示設定 */
		$this->set('mailContent',$this->data);
		$this->subMenuElements = array('mail_fields','mail_common');
		$this->pageTitle = 'メールフォーム設定編集：'.$this->data['MailContent']['title'];
		$this->help = 'mail_contents_form';
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理　(ajax);
 *
 * @param int ID
 * @return void
 * @access public
 */
	function admin_ajax_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		// メッセージ用にデータを取得
		$mailContent = $this->MailContent->read(null, $id);

		/* 削除処理 */
		if ($this->Message->dropTable($mailContent['MailContent']['name'])) {
			if($this->MailContent->del($id)) {
				$message = 'メールフォーム「'.$mailContent['MailContent']['title'].'」 を削除しました。';
				$this->MailContent->saveDbLog($message);
				exit(true);
			}
		} 
		exit();

	}
/**
 * [ADMIN] 削除処理
 *
 * @param int ID
 * @return void
 * @access public
 */
	function admin_delete($id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->setMessage('無効なIDです。', true);
			$this->redirect(array('action' => 'index'));
		}

		// メッセージ用にデータを取得
		$mailContent = $this->MailContent->read(null, $id);

		/* 削除処理 */
		if ($this->Message->dropTable($mailContent['MailContent']['name'])) {
			if($this->MailContent->del($id)) {
				$this->setMessage('メールフォーム「'.$mailContent['MailContent']['title'].'」 を削除しました。', false, true);
			}else {
				$this->setMessage('データベース処理中にエラーが発生しました。', true);
			}
		} else {
			$this->setMessage('データベースに問題があります。メール受信データ保存用テーブルの削除に失敗しました。', true);
		}
		$this->redirect(array('action' => 'index'));

	}

/**
 * レイアウト編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 * @access public
 */
	function redirectEditLayout($template){
		
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.'layouts'.DS.$template.$this->ext;
		$sorces = array(BASER_PLUGINS.'mail'.DS.'views'.DS.'layouts'.DS.$template.$this->ext,
						BASER_VIEWS.'layouts'.DS.$template.$this->ext);
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
			$this->redirect(array('plugin' => null, 'mail' => false, 'prefix' => false, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'layouts', $template.$this->ext));
		}else{
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(array('action' => 'index'));
		}
		
	}
/**
 * メール編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 * @access public
 */
	function redirectEditMail($template){
		
		$type = 'elements';
		$path = 'email'.DS.'text'.DS.$template.$this->ext;
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
			$path = str_replace(DS, '/', $path);
			$this->redirect(array('plugin' => null, 'mail' => false, 'prefix' => false, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], $type, $path));
		}else{
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(array('action' => 'index'));
		}
		
	}
/**
 * メールフォーム編集画面にリダイレクトする
 * 
 * @param string $template
 * @return void
 * @access public
 */
	function redirectEditForm($template){
		
		$path = 'mail'.DS.$template;
		$target = WWW_ROOT.'themed'.DS.$this->siteConfigs['theme'].DS.$path;
		$sorces = array(BASER_PLUGINS.'mail'.DS.'views'.DS.$path);
		if($this->siteConfigs['theme']){
			if(!file_exists($target.DS.'index'.$this->ext)){
				foreach($sorces as $source){
					if(is_dir($source)){
						$folder = new Folder();
						$folder->create(dirname($target), 0777);
						$folder->copy(array('from'=>$source,'to'=>$target,'chmod'=>0777,'skip'=>array('_notes')));
						break;
					}
				}
			}
			$path = str_replace(DS, '/', $path);
			$this->redirect(array('plugin' => null, 'mail' => false, 'prefix' => false, 'controller' => 'theme_files', 'action' => 'edit', $this->siteConfigs['theme'], 'etc', $path.'/index'.$this->ext));
		}else{
			$this->setMessage('現在、「テーマなし」の場合、管理画面でのテンプレート編集はサポートされていません。', true);
			$this->redirect(array('action' => 'index'));
		}
		
	}
/**
 * データをコピーする
 *
 * @param int $mailContentId
 * @param int $Id
 * @return void
 * @access protected
 */
	function admin_ajax_copy($id) {

		/* 除外処理 */
		if(!$id) {
			$this->ajaxError(500, '無効な処理です。');
		}

		$result = $this->MailContent->copy($id);
		if($result) {
			$this->set('data', $result);
		} else {
			if(isset($this->MailContent->validationErrors['name']) && $this->MailContent->validate['name']['maxLength']['message'] == $this->MailContent->validationErrors['name']) {
				$this->ajaxError(500, 'コピー元のメールコンテンツ名が長い為コピーに失敗しました。<br />コピー後のメールコンテンツ名は20文字以内になる必要があります。');
			} else {
				$this->ajaxError(500, $this->MailContent->validationErrors);
			}
		}

	}
}
