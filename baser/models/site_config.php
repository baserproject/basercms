<?php
/* SVN FILE: $Id$ */
/**
 * システム設定モデル
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
 * @package			baser.models
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
 * システム設定モデル
 *
 * @package			baser.models
 */
class SiteConfig extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'SiteConfig';
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
	var $useDbConfig = 'baser';
/**
 * バリデーション
 *
 * @var		array
 * @access	public
 */
	var $validate = array(
		'formal_name' => array(
			'rule'		=> array('minLength',1),
			'message'	=> "Webサイト名を入力して下さい。",
			'required'	=> true
		),
		'name' => array(
			'rule'		=> array('minLength',1),
			'message'	=> "Webサイトタイトルを入力して下さい。",
			'required'	=> true
		),
		'email' => array(
			array(
				'rule'		=> array('email'),
				'message'	=> "管理者メールアドレスの形式が不正です。"
			),
			array(
				'rule'		=> array('minLength',1),
				'message'	=> "管理者メールアドレスを入力してください。"
			)
		),
		'mail_encode' => array(
			'rule'		=> array('minLength',1),
			'message'	=> "メール送信文字コードを入力してください。初期値は「ISO-2022-JP」です。",
			'required'	=> true
		),
		'site_url' => array(
			'rule'		=> array('minLength', 1),
			'message'	=> "WebサイトURLを入力してください。",
			'required'	=> true
		),
		'admin_ssl_on' => array(
			'rule'		=> array('sslUrlExists'),
			'message'	=> "管理画面をSSLで利用するには、SSL用のWebサイトURLを入力してください。"
		)
	);
/**
 * テーマの一覧を取得する
 *
 * @return array
 */
	function getThemes() {

		$themes = array();
		$themedFolder = new Folder(VIEWS.'themed'.DS);
		$_themes = $themedFolder->read(true,true);
		foreach($_themes[0] as $theme) {
			$themes[$theme] = Inflector::camelize($theme);
		}
		$themedFolder = new Folder(WWW_ROOT.'themed'.DS);
		$_themes = array_merge($themes,$themedFolder->read(true,true));
		foreach($_themes[0] as $theme) {
			$themes[$theme] = Inflector::camelize($theme);
		}
		return $themes;

	}
/**
 * コントロールソースを取得する
 * @param string $field
 * @return mixed array | false
 */
	function getControlSource($field=null) {
		$controlSources['mode'] = array(-1=>'インストールモード',0=>'ノーマルモード',1=>'デバッグモード１',2=>'デバッグモード２',3=>'デバッグモード３');
		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}
	}
/**
 * SSL用のURLが設定されているかチェックする
 *
 * @param mixed	$check
 * @return boolean
 * @access public
 */
	function sslUrlExists($check) {
		$sslOn = $check[key($check)];
		if($sslOn && empty($this->data['SiteConfig']['ssl_url'])) {
			return false;
		}
		return true;
	}
	
}
?>