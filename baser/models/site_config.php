<?php
/* SVN FILE: $Id$ */
/**
 * システム設定モデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models
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
 * システム設定モデル
 *
 * @package baser.models
 */
class SiteConfig extends AppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'SiteConfig';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('BcCache');
/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'baser';
/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'formal_name' => array(
			'rule'		=> array('notEmpty'),
			'message'	=> 'Webサイト名を入力してください。',
			'required'	=> true
		),
		'name' => array(
			'rule'		=> array('notEmpty'),
			'message'	=> 'Webサイトタイトルを入力してください。',
			'required'	=> true
		),
		'email' => array(
			array(	'rule'		=> array('emails'),
					'message'	=> '管理者メールアドレスの形式が不正です。'),
			array(	'rule'		=> array('notEmpty'),
					'message'	=> '管理者メールアドレスを入力してください。')
		),
		'mail_encode' => array(
			'rule'		=> array('notEmpty'),
			'message'	=> "メール送信文字コードを入力してください。初期値は「ISO-2022-JP」です。",
			'required'	=> true
		),
		'site_url' => array(
			'rule'		=> array('notEmpty'),
			'message'	=> "WebサイトURLを入力してください。",
			'required'	=> true
		),
		'admin_ssl' => array(
			'rule'		=> array('sslUrlExists'),
			'message'	=> "管理画面をSSLで利用するには、SSL用のWebサイトURLを入力してください。"
		)
	);
/**
 * テーマの一覧を取得する
 *
 * @return array
 * @access public
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
 * 
 * @param string $field
 * @return mixed array | false
 * @access public
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
