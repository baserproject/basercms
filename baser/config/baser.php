<?php
/* SVN FILE: $Id$ */
/**
 * baserCMS設定ファイル
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * アプリケーション基本設定
 */
	$config['BcApp'] = array(
		// デフォルトタイトル設定（インストールの際のエラー時等DB接続まえのエラーで利用）
		'title'				=> 'コーポレートサイトにちょうどいいCMS - baserCMS - ',
		// 管理システムテーマ
		'adminTheme'		=> 'baseradmin'
	);
/**
 * 環境設定 
 */
	$config['BcEnv'] = array(
		// プラグインDBプレフィックス
		'pluginDbPrefix'	=> 'pg_',
	);
/**
 * 文字コード設定
 */
	$config['BcEncode'] = array(
		// 文字コードの検出順
		'detectOrder'	=> 'ASCII,JIS,UTF-8,SJIS-win,EUC-JP',
	);
/**
 * キャッシュ設定 
 */
	$config['BcCache'] = array(
		// 標準キャッシュ時間
		'defaultCachetime'	=> '1 month',
		// モデルデータキャッシュ時間
		'dataCachetime'		=> '1 month'
	);
/**
 * 認証プレフィックス設定
 */
	$adminPrefix = Configure::read('Routing.admin');
	$config['BcAuthPrefix'] = array(
		// 管理画面
		'admin' => array(
			'prefix'		=> 'admin',
			'alias'			=> $adminPrefix,
			// 認証後リダイレクト先
			'loginRedirect'	=> '/'.$adminPrefix,
			// ログイン画面タイトル
			'loginTitle'	=> '管理システムログイン',
			'loginAction'	=> '/'.$adminPrefix.'/users/login'
		)/*,
		'mypage' => array(
			'alias'			=> 'mypage',
			'prefix'		=> 'mypage',
			'loginRedirect'=>'/mypage/dashboard/index',
			'loginTitle'=>'マイページログイン',
			'userModel'		=> 'User',
			'loginAction'	=> '/mypage/users/login'
		)*/
	);
/**
 * Eメール設定
 */
	$config['BcEmail'] = array(
		// 改行コード
		'lfcode' => "\n"
	);
/**
 * エージェント設定
 */
	$config['BcAgent'] = array(
		'mobile'	=> array(
			'alias'	=> 'm',
			'prefix'=> 'mobile',
			'autoRedirect'	=> true,
			'agents'	=> array(
				'Googlebot-Mobile',
				'Y!J-SRD',
				'Y!J-MBS',
				'DoCoMo',
				'SoftBank',
				'Vodafone',
				'J-PHONE',
				'UP.Browser'
			)
		),
		'smartphone'	=> array(
			'alias'		=> 's',
			'prefix'	=> 'smartphone',
			'autoRedirect'	=> true,
			'agents'	=> array(
				'iPhone',         // Apple iPhone
				'iPod',           // Apple iPod touch
				'Android',        // 1.5+ Android
				'dream',          // Pre 1.5 Android
				'CUPCAKE',        // 1.5+ Android
				'blackberry9500', // Storm
				'blackberry9530', // Storm
				'blackberry9520', // Storm v2
				'blackberry9550', // Storm v2
				'blackberry9800', // Torch
				'webOS',          // Palm Pre Experimental
				'incognito',      // Other iPhone browser
				'webmate'         // Other iPhone browser
			)
		)
	);
?>