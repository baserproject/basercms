<?php
/* SVN FILE: $Id$ */
/**
 * メールフィールドコントローラー
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
 * メールフィールドコントローラー
 *
 * @package baser.plugins.mail.controllers
 */
class MailFieldsController extends MailAppController {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'MailFields';
/**
 * モデル
 *
 * @var array
 * @access public
 */
	var $uses = array('Mail.MailField','Mail.MailContent','Mail.Message');
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html','TimeEx','FormEx','TextEx','Csv');
/**
 * コンポーネント
 *
 * @var array
 * @access public
 */
	var $components = array('AuthEx','Cookie','AuthConfigure');
/**
 * ぱんくずナビ
 *
 * @var array
 * @access public
 */
	var $navis = array('メールフォーム管理' => array('plugin' => 'mail', 'controller' => 'mail_contents', 'action' => 'index'));
/**
 * サブメニューエレメント
 *
 * @var string
 * @access public
 */
	var $subMenuElements = array();
/**
 * beforeFilter
 *
 * @return void
 * @access public
 */
	function beforeFilter() {

		parent::beforeFilter();
		$this->MailContent->recursive = -1;
		$this->mailContent = $this->MailContent->read(null,$this->params['pass'][0]);
		$this->navis = am($this->navis, array($this->mailContent['MailContent']['title'].'管理' => array('plugin' => 'mail', 'controller' => 'mail_fields', 'action' => 'index', $this->params['pass'][0])));

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
 * [ADMIN] メールフィールド一覧
 *
 * @param int $mailContentId
 * @return void
 * @access public
 */
	function admin_index($mailContentId) {

		if(!$mailContentId || !$this->mailContent) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller' => 'mail_contents', 'action' => 'index'));
		}

		/* セッション処理 */
		if(isset($this->params['named']['sortmode'])){
			$this->Session->write('SortMode.MailField', $this->params['named']['sortmode']);
		}

		/* 並び替えモード */
		if(!$this->Session->check('SortMode.MailField')){
			$this->set('sortmode', 0);
		}else{
			$this->set('sortmode', $this->Session->read('SortMode.MailField'));
		}

		$conditions = $this->_createAdminIndexConditions($mailContentId);
		$listDatas = $this->MailField->findAll($conditions, null, 'MailField.sort');
		$this->set('listDatas',$listDatas);
		$this->subMenuElements = array('mail_fields','mail_common');
		$this->pageTitle = '['.$this->mailContent['MailContent']['title'].'] メールフィールド一覧';

	}
/**
 * [ADMIN] メールフィールド追加
 *
 * @param int $mailContentId
 * @return void
 * @access public
 */
	function admin_add($mailContentId) {

		if(!$mailContentId || !$this->mailContent) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller' => 'mail_contents', 'action' => 'index'));
		}

		if(!$this->data) {
			$this->data = $this->_getDefaultValue();
		}else {

			/* 登録処理 */
			$this->data['MailField']['mail_content_id'] = $mailContentId;
			$this->data['MailField']['no'] = $this->MailField->getMax('no',array('MailField.mail_content_id'=>$mailContentId))+1;
			$this->data['MailField']['sort'] = $this->MailField->getMax('sort')+1;
			$this->MailField->create($this->data);
			if($this->MailField->validates()) {
				if($this->Message->addField($this->mailContent['MailContent']['name'],$this->data['MailField']['field_name'])) {
					// データを保存
					if($this->MailField->save(null, false)) {
						$message = '新規メールフィールド「'.$this->data['MailField']['name'].'」を追加しました。';
						$this->Session->setFlash($message);
						$this->MailField->saveDbLog($message);
						$this->redirect(array('controller' => 'mail_fields', 'action' => 'index', $mailContentId));
					}else {
						$this->Session->setFlash('データベース処理中にエラーが発生しました。');
					}
				} else {
					$this->Session->setFlash('データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。');
				}
			} else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}
		}

		$this->subMenuElements = array('mail_fields','mail_common');
		$this->pageTitle = '['.$this->mailContent['MailContent']['title'].'] 新規メールフィールド登録';
		$this->set('controlSource',$this->MailField->getControlSource());
		$this->render('form');

	}
/**
 * [ADMIN] 編集処理
 *
 * @param int $mailContentId
 * @param int $id
 * @return void
 * @access public
 */
	function admin_edit($mailContentId,$id) {

		if(!$id && empty($this->data)) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'index'));
		}

		if (empty($this->data)) {
			$this->data = $this->MailField->read(null, $id);
		}else {
			$old = $this->MailField->read(null, $id);
			$this->MailField->set($this->data);
			if($this->MailField->validates()) {
				$ret = true;
				if ($old['MailField']['field_name'] != $this->data['MailField']['field_name']) {
					$ret = $this->Message->renameField($this->mailContent['MailContent']['name'], $old['MailField']['field_name'],$this->data['MailField']['field_name']);
				}
				if ($ret) {
					/* 更新処理 */
					if($this->MailField->save(null, false)) {
						$message = 'メールフィールド「'.$this->data['MailField']['name'].'」を更新しました。';
						$this->Session->setFlash($message);
						$this->MailField->saveDbLog($message);
						$this->redirect(array('action' => 'index', $mailContentId));
					}else {
						$this->Session->setFlash('データベース処理中にエラーが発生しました。');
					}
				} else {
					$this->Session->setFlash('データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。');
				}
			} else {
				$this->Session->setFlash('入力エラーです。内容を修正してください。');
			}
		}

		/* 表示設定 */
		$this->subMenuElements = array('mail_fields','mail_common');
		$this->set('controlSource',$this->MailField->getControlSource());
		$this->pageTitle = '['.$this->mailContent['MailContent']['title'].'] メールフィールド編集：　'.$this->data['MailField']['name'];
		$this->render('form');

	}
/**
 * [ADMIN] 削除処理
 *
 * @param int $mailContentId
 * @param int $id
 * @return void
 * @access public
 */
	function admin_delete($mailContentId,$id = null) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'admin_index'));
		}

		// メッセージ用にデータを取得
		$mailField = $this->MailField->read(null, $id);

		/* 削除処理 */
		if ($this->Message->delField($this->mailContent['MailContent']['name'], $mailField['MailField']['field_name'])) {
			if($this->MailField->del($id)) {
				$message = 'メールフィールド「'.$mailField['MailField']['name'].'」 を削除しました。';
				$this->Session->setFlash($message);
				$this->MailField->saveDbLog($message);
			}else {
				$this->Session->setFlash('データベース処理中にエラーが発生しました。');
			}
		} else {
			$this->Session->setFlash('データベースに問題があります。メール受信データ保存用テーブルの更新処理に失敗しました。');
		}
		
		$this->redirect(array('action' => 'index', $mailContentId));

	}
/**
 * フォームの初期値を取得する
 *
 * @return string
 * @access protected
 */
	function _getDefaultValue() {

		$data['MailField']['type'] = 'text';
		$data['MailField']['use_field'] = 1;
		$data['MailField']['no_send'] = 0;
		return $data;

	}
/**
 * フィールドデータをコピーする
 *
 * @param int $mailContentId
 * @param int $Id
 * @return void
 * @access protected
 */
	function admin_copy($mailContentId,$id) {

		/* 除外処理 */
		if(!$id) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'index'));
		}

		/* コピー対象フィールドデータを読み込む */
		$mailField = $this->MailField->read(null,$id);
		if(!$mailField) {
			$this->Session->setFlash('無効なIDです。');
			$this->redirect(array('action' => 'index'));
		}

		// 不要な値をリセットする
		unset($mailField['MailField']['id']);
		unset($mailField['MailField']['modified']);
		unset($mailField['MailField']['created']);

		// メッセージ用
		$oldName = $mailField['MailField']['name'];

		// 項目名とフィールド名は識別用に__n形式のナンバーを付加する
		$mailField['MailField']['field_name'] = $this->__getNewValueOnCopy('field_name',$mailField['MailField']['field_name']);
		$mailField['MailField']['name'] = $this->__getNewValueOnCopy('name',$mailField['MailField']['name']);
		$mailField['MailField']['no'] = $this->MailField->getMax('no',array('MailField.mail_content_id'=>$mailContentId))+1;
		$mailField['MailField']['sort'] = $this->MailField->getMax('sort')+1;

		// データを保存
		$this->MailField->create($mailField);
		if($this->MailField->save()) {
			$message = 'メールフィールド「'.$oldName.'」 をコピーしました。';
			$this->Session->setFlash($message);
			$this->MailField->saveDbLog($message);
			$this->Message->construction($mailContentId);
		}else {
			$message = 'コピー中にエラーが発生しました。';
			$this->Session->setFlash($message);
		}

		$this->redirect(array('action' => 'index', $mailContentId));

	}
/**
 * コピー時用の新しい値を取得する
 * 値の末尾に(n)形式のナンバーを付加する
 *
 * (例) field_name・・・field_name(2) / field_name(3) / field_name(4)
 *
 * @param string $fieldName
 * @param string $oldValue
 * @return string
 * @access private
 */
	function __getNewValueOnCopy($fieldName,$oldValue) {

		// プレフィックスを削除したフィールド名を取得
		$baseValue = preg_replace("/\\[[0-9]+\]+$/s","",$oldValue);
		$baseValue = trim($baseValue);

		// 先頭が同じ名前のリストを取得し、後方プレフィックス付きのフィールド名を取得する
		$conditions = array('MailField.'.$fieldName.' LIKE'=>$baseValue.'%',
				'MailField.mail_content_id'=>$this->mailContent['MailContent']['id']);
		$datas = $this->MailField->findAll($conditions,$fieldName);
		$prefixNo = 1;

		foreach($datas as $data) {

			$lastPrefix = str_replace($baseValue,'',$data['MailField'][$fieldName]);
			if(preg_match("/^\\[([0-9]+)\]$/s",$lastPrefix,$matches)) {
				$no = (int)$matches[1];
				if($no > $prefixNo) $prefixNo = $no;
			}

		}
		return $baseValue.'['.($prefixNo+1).']';

	}
/**
 * メッセージCSVファイルをダウンロードする
 * 
 * @param int $mailContentId
 * @return void
 * @access public
 */
	function admin_download_csv($mailContentId) {

		if(!$mailContentId || !$this->mailContent) {
			$this->Session->setFlash('無効な処理です。');
			$this->redirect(array('controller' => 'mail_contents', 'action' => 'index'));
		}

		$this->Message->alias = Inflector::camelize($this->mailContent['MailContent']['name'].'_message');
		$this->Message->tablePrefix .= $this->mailContent['MailContent']['name'].'_';
		$this->Message->_schema = null;
		$this->Message->cacheSources = false;
		$messages = $this->Message->findAll();

		// フィールドの一覧を取得する
		$mailFields = $this->MailField->find('all', array(
			'conditions' => array('MailField.mail_content_id' => $mailContentId)
		));

		// フィールド名とデータの変換に必要なヘルパーを読み込む
		App::import('Helper', 'Mail.maildata');
		$maildata = new MaildataHelper();
		App::import('Helper', 'Mail.mailfield');
		$mailfield = new MailfieldHelper();

		foreach ($messages as $key => $message) {

			$inData = array();
			// 届いているメッセージの内容を表示状態に変換する
			foreach($mailFields as $mailField) {
				$inData[$mailField['MailField']['field_name']] = $maildata->control(
					$mailField['MailField']['type'],
					$message[$this->Message->alias][$mailField['MailField']['field_name']],
					$mailfield->getOptions($mailField['MailField'])
				);
			}
			$convertData = array_merge($message[$this->Message->alias], $inData);
			$messages[$key][$this->Message->alias] = $convertData;

		}

		$this->set('messages',$messages);
		$this->set('contentName',$this->mailContent['MailContent']['name']);

	}

/**
 * 並び替えを更新する [AJAX]
 *
 * @param int $mailContentId
 * @return boolean
 * @access	public
 */
	function admin_update_sort ($mailContentId) {

		if($this->data){
			$conditions = $this->_createAdminIndexConditions($mailContentId);
			if($this->MailField->changeSort($this->data['Sort']['id'],$this->data['Sort']['offset'],$conditions)){
				echo true;
			}else{
				echo false;
			}
		}else{
			echo false;
		}
		exit();

	}
/**
 * 管理画面ページ一覧の検索条件を取得する
 *
 * @param array $mailContentId
 * @return string
 * @access protected
 */
	function _createAdminIndexConditions($mailContentId){

		$conditions = array('MailField.mail_content_id'=>$mailContentId);
		return $conditions;

	}

}
?>