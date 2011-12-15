<?php
/* SVN FILE: $Id$ */
/**
 * baserCMS設定ファイル
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2011, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.config
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * baserCMS基本設定
 */
	$config['Baser'] = array(
		// デフォルトタイトル設定（インストールの際のエラー時等DB接続まえのエラーで利用）
		'title'				=> 'コーポレートサイトにちょうどいいCMS - baserCMS - ',
		// 標準キャッシュ時間
		'cachetime'			=> '1 month',
		// プラグインDBプレフィックス
		'pluginDbPrefix'	=> 'pg_',
		// 文字コードの検出順
		'detectOrder'		=> 'ASCII,JIS,UTF-8,SJIS-win,EUC-JP',
		// モデルデータキャッシュの利用可否
		'dataCachetime'		=> '1 month'
	);
/**
 * 認証プレフィックス設定
 */
	$adminPrefix = Configure::read('Routing.admin');
	$config['AuthPrefix'] = array(
		// 管理画面
		'admin' => array(
			'prefix'		=> 'admin',
			'alias'			=> $adminPrefix,
			// 認証後リダイレクト先
			'loginRedirect'	=> '/'.$adminPrefix,
			// ログイン画面タイトル
			'loginTitle'	=> '管理システムログイン',
			'loginAction'	=> '/'.$adminPrefix.'/users/login'
		),
		'mypage' => array(
			'alias'			=> 'mypage',
			'prefix'		=> 'mypage',
			'loginRedirect'=>'/mypage/dashboard/index',
			'loginTitle'=>'マイページログイン',
			'userModel'		=> 'User',
			'loginAction'	=> '/mypage/users/login'
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
 * エージェント設定
 */
	$config['AgentSettings'] = array(
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