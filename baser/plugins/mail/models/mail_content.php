<?php
/* SVN FILE: $Id$ */
/**
 * メールコンテンツモデル
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
 * beforeValidate
 *
 * @return	void
 * @access	public
 */
	function beforeValidate() {

		$this->validate['name'] = array(array(	'rule' => 'halfText',
						'message' => '>> メールフォームアカウント名は半角のみで入力して下さい。',
						'allowEmpty'=>false),
				array(	'rule' => array('isUnique'),
						'message' => '入力されたメールフォームアカウント名は既に使用されています。'));
		$this->validate['title'] = array(array(	'rule' => VALID_NOT_EMPTY,
						'message' => ">> メールフォームタイトルを入力して下さい。"));
		$this->validate['sender_name'] = array(array(	'rule' => VALID_NOT_EMPTY,
						'message' => ">> 送信先名を入力して下さい"));
		$this->validate['subject_user'] = array(array(	'rule' => VALID_NOT_EMPTY,
						'message' => ">> 自動返信メール件名[ユーザー宛]を入力して下さい。"));
		$this->validate['subject_admin'] = array(array(	'rule' => VALID_NOT_EMPTY,
						'message' => ">> 自動送信メール件名[管理者宛]を入力して下さい。"));
		$this->validate['layout_template'] = array(array(	'rule' => 'halfText',
						'message' => '>> レイアウトテンプレート名は半角のみで入力して下さい。',
						'allowEmpty'=>false));
		$this->validate['form_template'] = array(array(	'rule' => 'halfText',
						'message' => ">> メールフォームテンプレート名は半角のみで入力して下さい。",
						'allowEmpty'=>false));
		$this->validate['mail_template'] = array(array(	'rule' => 'halfText',
						'message' => ">> 送信メールテンプレートは半角のみで入力して下さい。",
						'allowEmpty'=>false));
		$this->validate['sender_1'] = array(array(	'rule' => 'email',
						'allowEmpty' => true,
						'message' => '>> 送信先メールアドレスの形式が不正です。'));

		if($this->data['MailContent']['sender_1_']) {
			$this->validate['sender_1'] = array(array('rule' => 'email',
							'message' => '>> 送信先メールアドレスの形式が不正です。'));
		}

		return true;
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