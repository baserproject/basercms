<?php

/**
 * システム設定モデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */

/**
 * システム設定モデル
 *
 * @package Baser.Model
 */
class SiteConfig extends AppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'SiteConfig';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

/**
 * データベース接続
 *
 * @var string
 * @access public
 */
	public $useDbConfig = 'baser';

/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'formal_name' => array(
			'rule' => array('notEmpty'),
			'message' => 'Webサイト名を入力してください。',
			'required' => true
		),
		'name' => array(
			'rule' => array('notEmpty'),
			'message' => 'Webサイトタイトルを入力してください。',
			'required' => true
		),
		'email' => array(
			array('rule' => array('emails'),
				'message' => '管理者メールアドレスの形式が不正です。'),
			array('rule' => array('notEmpty'),
				'message' => '管理者メールアドレスを入力してください。')
		),
		'mail_encode' => array(
			'rule' => array('notEmpty'),
			'message' => "メール送信文字コードを入力してください。初期値は「ISO-2022-JP」です。",
			'required' => true
		),
		'site_url' => array(
			'rule' => array('notEmpty'),
			'message' => "WebサイトURLを入力してください。",
			'required' => true
		),
		'admin_ssl' => array(
			'rule' => array('sslUrlExists'),
			'message' => "管理画面をSSLで利用するには、SSL用のWebサイトURLを入力してください。"
		)
	);

/**
 * テーマの一覧を取得する
 *
 * @return array
 * @access public
 */
	public function getThemes() {
		$themes = array();
		$themeFolder = new Folder(APP . 'View' . DS . 'theme' . DS);
		$_themes = $themeFolder->read(true, true);
		foreach ($_themes[0] as $theme) {
			$themes[$theme] = Inflector::camelize($theme);
		}
		$themeFolder = new Folder(WWW_ROOT . 'theme' . DS);
		$_themes = array_merge($themes, $themeFolder->read(true, true));
		foreach ($_themes[0] as $theme) {
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
	public function getControlSource($field = null) {
		$controlSources['mode'] = array(-1 => 'インストールモード', 0 => 'ノーマルモード', 1 => 'デバッグモード１', 2 => 'デバッグモード２', 3 => 'デバッグモード３');
		if (isset($controlSources[$field])) {
			return $controlSources[$field];
		} else {
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
	public function sslUrlExists($check) {
		$sslOn = $check[key($check)];
		if ($sslOn && empty($this->data['SiteConfig']['ssl_url'])) {
			return false;
		}
		return true;
	}

/**
 * コンテンツ一覧を表示してから、コンテンツの並び順が変更されていないかどうか
 * 
 * @param $listDisplayed
 * @return bool
 */
	public function isChangedContentsSortLastModified($listDisplayed) {
		$siteConfigs = $this->findExpanded();
		$changed = false;
		if(!empty($siteConfigs['contents_sort_last_modified'])) {
			$lastModified = $siteConfigs['contents_sort_last_modified'];
			list($lastModified, $sessionId) = explode('|', $lastModified);
			if(session_id() != $sessionId) {
				// 60秒はブラウザのロード時間を加味したバッファ
				if($lastModified >= ($listDisplayed - 60)) {
					$changed = true;
				}
			}
		}
		return $changed;
	}

/**
 * コンテンツ並び順変更時間を更新する
 */
	public function updateContentsSortLastModified() {
		$siteConfigs = $this->findExpanded();
		$siteConfigs['contents_sort_last_modified'] = date('U') . '|' . session_id();
		$this->saveKeyValue($siteConfigs);
	}

/**
 * コンテンツ並び替え順変更時間をリセットする 
 */
	public function resetContentsSortLastModified() {
		$siteConfigs = $this->findExpanded();
		$siteConfigs['contents_sort_last_modified'] = '';
		$this->saveKeyValue($siteConfigs);
	}

}
