<?php
/* SVN FILE: $Id$ */
/**
 * メールコンテンツモデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.models
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * メールコンテンツモデル
 *
 * @package baser.plugins.mail.models
 *
 */
class MailContent extends MailAppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'MailContent';
/**
 * behaviors
 *
 * @var array
 * @access public
 */
	var $actsAs = array('BcContentsManager', 'BcPluginContent', 'BcCache');
/**
 * hasMany
 *
 * @var array
 * @access public
 */
	var $hasMany = array('MailField'=>
			array('className'=>'Mail.MailField',
							'order'=>'id',
							'limit'=>100,
							'foreignKey'=>'mail_content_id',
							'dependent'=>true,
							'exclusive'=>false,
							'finderQuery'=>''));
/**
 * validate
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			'notInList' => array(
				'rule'		=> array('halfText'),
				'message'	=> 'メールフォームアカウント名は半角のみで入力してください。',
				'allowEmpty'=> false),
			'notInList' => array(
				'rule'		=> array('notInList', array('mail')),
				'message'	=> 'メールフォームアカウント名に「mail」は利用できません。'),
			'isUnique' => array(
				'rule'		=> array('isUnique'),
				'message'	=> '入力されたメールフォームアカウント名は既に使用されています。'),
			'maxLength' => array(
				'rule'		=> array('maxLength', 100),
				'message'	=> 'メールフォームアカウント名は100文字以内で入力してください。')
		),
		'title' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "メールフォームタイトルを入力してください。")
		),
		'sender_name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "送信先名を入力してください。"),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> '送信先名は50文字以内で入力してください。')
		),
		'subject_user' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "自動返信メール件名[ユーザー宛]を入力してください。"),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> '自動返信メール件名[ユーザー宛]は50文字以内で入力してください。')
		),
		'subject_admin' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "自動送信メール件名[管理者宛]を入力してください。"),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> '自動返信メール件名[管理者宛]は50文字以内で入力してください。')
		),
		'layout_template' => array(
			array(	'rule'		=> array('halfText'),
					'message'	=> 'レイアウトテンプレート名は半角のみで入力してください。',
					'allowEmpty'=> false),
			array(	'rule'		=> array('maxLength', 20),
					'message'	=> 'レイアウトテンプレート名は20文字以内で入力してください。')
		),
		'form_template' => array(
			array(	'rule'		=> array('halfText'),
					'message'	=> "メールフォームテンプレート名は半角のみで入力してください。",
					'allowEmpty'=> false),
			array(	'rule'		=> array('maxLength', 20),
					'message'	=> 'フォームテンプレート名は20文字以内で入力してください。')
		),
		'mail_template' => array(
			array(	'rule'		=> array('halfText'),
					'message'	=> "送信メールテンプレートは半角のみで入力してください。",
					'allowEmpty'=> false),
			array(	'rule'		=> array('maxLength', 20),
					'message'	=> 'メールテンプレート名は20文字以内で入力してください。')
		),
		'redirect_url' => array(
			array(	'rule'		=> array('url'),
					'message'	=> "リダイレクトURLの形式が不正です。",
					'allowEmpty'=> true),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'リダイレクトURLは255文字以内で入力してください。')
		),
		'sender_1' => array(
			array(	'rule'		=> array('email'),
					'allowEmpty'=> true,
					'message'	=> '送信先メールアドレスの形式が不正です。'),
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> '送信先メールアドレスは255文字以内で入力してください。')
		),
		'sender_2' => array(
			array(	'rule'		=> array('maxLength', 255),
					'message'	=> 'CC用送信先メールアドレスは255文字以内で入力してください。')
		),
		'ssl_on' => array(
			'rule'		=> 'checkSslUrl',
			"message"	=> 'SSL通信を利用するには、システム設定で、事前にSSL通信用のWebサイトURLを指定してください。'
		)
	);
/**
 * beforeValidate
 *
 * @return boolean
 * @access public
 */
	function beforeValidate() {

		if($this->data['MailContent']['sender_1']) {
			$this->validate['sender_1'] = array(
				array(	'rule'		=> 'email',
						'message'	=> '送信先メールアドレスの形式が不正です。'));
		}

		return true;
		
	}
/**
 * SSL用のURLが設定されているかチェックする
 * 
 * @param string $check チェック対象文字列
 * @return boolean
 * @access public
 */
	function checkSslUrl($check) {
		
		if($check[key($check)]) {
			$sslUrl = Configure::read('BcEnv.sslUrl');
			if(empty($sslUrl)) {
				return false;
			} else {
				return true;
			}
		} else {
			return true;
		}
		
	}
/**
 * 英数チェック
 *
 * @param string $check チェック対象文字列
 * @return boolean
 * @access public
 */
	function alphaNumeric($check) {

		if(preg_match("/^[a-z0-9]+$/",$check[key($check)])) {
			return true;
		}else {
			return false;
		}

	}
/**
 * フォームの初期値を取得する
 *
 * @return string
 * @access protected
 */
	function getDefaultValue() {

		$data['MailContent']['subject_user'] = 'お問い合わせ頂きありがとうございます';
		$data['MailContent']['subject_admin'] = 'お問い合わせを頂きました';
		$data['MailContent']['layout_template'] = 'default';
		$data['MailContent']['form_template'] = 'default';
		$data['MailContent']['mail_template'] = 'mail_default';
		$data['MailContent']['use_description'] = true;
		$data['MailContent']['auth_captcha'] = false;
		$data['MailContent']['ssl_on'] = false;
		$data['MailContent']['status'] = false;
		
		return $data;

	}
/**
 * afterSave
 *
 * @return boolean
 * @access public
 */
	function afterSave($created) {

		// 検索用テーブルへの登録・削除
		if(!$this->data['MailContent']['exclude_search'] && $this->data['MailContent']['status'] ) {
			$this->saveContent($this->createContent($this->data));
		} else {
			$this->deleteContent($this->data['MailContent']['id']);
		}

	}
/**
 * beforeDelete
 *
 * @return	boolean
 * @access	public
 */
	function beforeDelete() {

		return $this->deleteContent($this->id);

	}
/**
 * 検索用データを生成する
 *
 * @param array $data
 * @return array
 * @access public
 */
	function createContent($data) {

		if(isset($data['MailContent'])) {
			$data = $data['MailContent'];
		}

		$_data = array();
		$_data['Content']['type'] = 'メール';
		$_data['Content']['model_id'] = $this->id;
		$_data['Content']['category'] = '';
		$_data['Content']['title'] = $data['title'];
		$_data['Content']['detail'] = $data['description'];
		$_data['Content']['url'] = '/'.$data['name'].'/index';
		$_data['Content']['status'] = $data['status'];

		return $_data;

	}
/**
 * メールコンテンツデータをコピーする
 * 
 * @param int $id
 * @param array $data
 * @return mixed UserGroup Or false
 */
	function copy($id, $data = array(), $recursive = true) {
		
		if($id) {
			$data = $this->find('first', array('conditions' => array('MailContent.id' => $id), 'recursive' => -1));
		}
		
		$data['MailContent']['name'] .= '_copy';
		$data['MailContent']['title'] .= '_copy';
		unset($data['MailContent']['id']);
		unset($data['MailContent']['created']);
		unset($data['MailContent']['modified']);
		
		$this->create($data);
		$result = $this->save();
		if($result) {
			$result['MailContent']['id'] = $this->getInsertID();
			if($recursive) {
				$mailFields = $this->MailField->find('all', array('conditions' => array('MailField.mail_content_id' => $id), 'order' => 'MailField.sort', 'recursive' => -1));
				foreach($mailFields as $mailField) {
					$mailField['MailField']['mail_content_id'] = $result['MailContent']['id'];
					$this->MailField->copy(null, $mailField, array('sortUpdateOff' => true));
				}
				$Message = ClassRegistry::getObject('Message');
				$Message->createTable($result['MailContent']['name']);
				$Message->construction($result['MailContent']['id']);
			}
			return $result;
		} else {
			if(isset($this->validationErrors['name']) && mb_strlen($data['MailContent']['name']) < 20) {
				return $this->copy(null, $data, $recursive);
			} else {
				return false;
			}
		}
		
	}
	
}
