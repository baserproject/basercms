<?php
/* SVN FILE: $Id$ */
/**
 * BaserCMS設定ファイル
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
 * @package			baser.config
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * BaserCMS基本設定
 */
	$config['Baser'] = array(
		// デフォルトタイトル設定（インストールの際のエラー時等DB接続まえのエラーで利用）
		'title'				=> 'コーポレートサイトにちょうどいいCMS - BaserCMS - ',
		// 標準キャッシュ時間
		'cachetime'			=> '1 month',
		// プラグインDBプレフィックス
		'pluginDbPrefix'	=> 'pg_',
		// 文字コードの検出順
		'detectOrder'		=> 'ASCII,JIS,UTF-8,SJIS-win,EUC-JP'
	);
/**
 * 認証プレフィックス設定
 */
	$adminPrefix = Configure::read('Routing.admin');
	$config['AuthPrefix'] = array(
		// 管理画面
		$adminPrefix => array(
			// 認証後リダイレクト先
			'loginRedirect'	=> '/'.$adminPrefix,
			// ログイン画面タイトル
			'loginTitle'	=> '管理システムログイン'
		)
	);
/**
 * Eメール設定
 */
	$config['Email'] = array(
		// 改行コード
		'lfcode' => "\n"
	);
/**
 * 携帯設定
 */
	$config['Mobile'] = array(
		// プレフィックス
		'prefix'	=> 'm',
		// ユーザーエージェント
		'agents'	=> array(
			'Googlebot-Mobile','Y!J-SRD','Y!J-MBS','DoCoMo','SoftBank',
			'Vodafone','J-PHONE','UP.Browser'
		)
	);
?>