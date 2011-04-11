<?php
/* SVN FILE: $Id$ */
/**
 * メールコンテンツモデル
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.plugins.mail.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * メールコンテンツモデル
 *
 * @package			baser.plugins.mail.models
 *
 */
class MailContent extends MailAppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'MailContent';
/**
 * behaviors
 *
 * @var 	array
 * @access 	public
 */
	var $actsAs = array('PluginContent');
/**
 * hasMany
 *
 * @var		array
 * @access 	public
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
 * @var		array
 * @access	public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('halfText'),
					'message'	=> 'メールフォームアカウント名は半角のみで入力してください。',
					'allowEmpty'=> false),
			array(	'rule'		=> array('notInList', array('mail')),
					'message'	=> 'メールフォームアカウント名に「mail」は利用できません。'),
			array(	'rule'		=> array('isUnique'),
					'on'		=> 'create',
					'message'	=> '入力されたメールフォームアカウント名は既に使用されています。'),
			array(	'rule'		=> array('maxLength', 20),
					'message'	=> 'メールフォームアカウント名は20文字以内で入力してください。')
		),
		'title' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> "メールフォームタイトルを入力してください。"),
			array(	'rule'		=> array('maxLength', 50),
					'message'	=> 'メールフォームタイトルは50文字以内で入力してください。')
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
 * @return	void
 * @access	public
 */
	function beforeValidate() {

		if($this->data['MailContent']['sender_1_']) {
			$this->validate['sender_1'] = array(
				array(	'rule'		=> 'email',
						'message'	=> '送信先メールアドレスの形式が不正です。'));
		}

		return true;
	}
/**
 * SSL用のURLが設定されているかチェックする
 * 
 * @param	string	チェック対象文字列
 * @return	boolean
 * @access	public
 */
	function checkSslUrl($check) {		
		if($check[key($check)]) {
			$sslUrl = Configure::read('Baser.sslUrl');
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
 * @param	string	チェック対象文字列
 * @return	boolean
 * @access	public
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
 * @return  void
 * @access  protected
 */
	function getDefaultValue() {

		$data['MailContent']['subject_user'] = 'お問い合わせ頂きありがとうございます';
		$data['MailContent']['subject_admin'] = 'お問い合わせを頂きました';
		$data['MailContent']['layout_template'] = 'default';
		$data['MailContent']['form_template'] = 'default';
		$data['MailContent']['mail_template'] = 'mail_default';
		$data['MailContent']['use_description'] = 1;
		$data['MailContent']['auth_captcha'] = 0;
		
		return $data;

	}
}
?>