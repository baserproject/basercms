<?php
/* SVN FILE: $Id$ */
/**
 * baserCMS設定ファイル
 *
 * PHP versions 4 and 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
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
		// 初期テーマ
		'defaultTheme'		=> 'nada-icons',
		// 管理システムテーマ
		'adminTheme'		=> 'baseradmin',
		// テンプレートの基本となる拡張子（.php 推奨）
		'templateExt'		=> '.php',
		// システムナビ
		'adminNavi'		=> array('core' => array(
			'name'		=> 'baserCMSコア',
			'contents'	=> array(
				array('name' => '固定ページ一覧',			'url' => array('admin' => true, 'plugin' => null, 'controller' => 'pages', 'action' => 'index')),
				array('name' => 'ウィジェット管理',		'url' => array('admin' => true, 'plugin' => null, 'controller' => 'widget_areas', 'action' => 'index')),
				array('name' => 'テーマ管理',				'url' => array('admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'index')),
				array('name' => 'プラグイン管理',			'url' => array('admin' => true, 'plugin' => null, 'controller' => 'plugins', 'action' => 'index')),
				array('name' => 'システム設定',			'url' => array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'form')),
				array('name' => 'ユーザー一覧',			'url' => array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'index')),
				array('name' => 'ユーザー登録',			'url' => array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'add')),
				array('name' => 'ユーザーグループ一覧',		'url' => array('admin' => true, 'plugin' => null, 'controller' => 'user_groups', 'action' => 'index')),
				array('name' => 'ユーザーグループ登録',		'url' => array('admin' => true, 'plugin' => null, 'controller' => 'user_groups', 'action' => 'add')),
				array('name' => '検索インデックス管理',		'url' => array('admin' => true, 'plugin' => null, 'controller' => 'contents', 'action' => 'index')),
				array('name' => 'メニュー一覧',			'url' => array('admin' => true, 'plugin' => null, 'controller' => 'global_menus', 'action' => 'index')),
				array('name' => 'メニュー登録',			'url' => array('admin' => true, 'plugin' => null, 'controller' => 'global_menus', 'action' => 'add')),
				array('name' => 'エディタテンプレート一覧',	'url' => array('admin' => true, 'plugin' => null, 'controller' => 'editor_templates', 'action' => 'index')),
				array('name' => 'エディタテンプレート登録',	'url' => array('admin' => true, 'plugin' => null, 'controller' => 'editor_templates', 'action' => 'add')),				
				array('name' => 'サーバーキャッシュ削除',	'url' => array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'del_cache')),
				array('name' => 'データメンテナンス',		'url' => array('admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'maintenance')),
				array('name' => '環境情報',				'url' => array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'info')),
				array('name' => 'クレジット',				'url' => 'javascript:credit()')
		))),
		// コアプラグイン
		'corePlugins'	=> array('blog', 'feed', 'mail'),
		// アップデートキー
		'updateKey'		=> 'update',
		// 管理者グループID
		'adminGroupId'	=> 1
	);
/**
 * システム要件 
 */
	$config['BcRequire'] = array(
		'phpVersion'	=> "5.2.0",
		'phpMemory'=> "32"
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
		'mail'			=> array(
			'UTF-8'			=> 'UTF-8',
			'ISO-2022-JP'	=> 'ISO-2022-JP'
		)
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
 * ※ CSVは非対応
 */
	$adminPrefix = Configure::read('Routing.admin');
	$config['BcAuthPrefix'] = array(
		// 管理画面
		'admin' => array(
			// 認証設定名
			'name'			=> '管理システム',
			// URLにおけるエイリアス
			'alias'			=> $adminPrefix,
			// 認証後リダイレクト先
			'loginRedirect'	=> '/'.$adminPrefix,
			// ログイン画面タイトル
			'loginTitle'	=> '管理システムログイン',
			// ログインページURL
			'loginAction'	=> '/'.$adminPrefix.'/users/login',
			'toolbar'		=> true
		),
		// フロント（例）
		/*'front' => array(
			'name'			=> 'フロント',
			'loginRedirect'	=> '/',
			'userModel'		=> 'User',
			'loginAction'	=> '/users/login',
			'toolbar'		=> true
		),*/
		// マイページ（例）
		/*'mypage' => array(
			'name'			=> 'マイページ',
			'alias'			=> 'mypage',
			'loginRedirect'	=> '/mypage/members/edit',
			'loginTitle'	=> 'マイページログイン',
			'userModel'		=> 'Member',
			'loginAction'	=> '/mypage/members/login',
			'toolbar'		=> false
		),*/
		// モバイルマイページ（例）
		/*'mobile_mypage' => array(
			'name'			=> 'ケータイマイページ',
			'alias'			=> 'mobile_mypage',
			'loginRedirect'	=> '/m/',
			'loginTitle'	=> 'マイページログイン',
			'userModel'		=> 'User',
			'loginAction'	=> '/m/mypage/users/login',
			'toolbar'		=> false,
			'userScope'		=> array()
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
			'autoLink'		=> true,
			'agents'	=> array(
				'Googlebot-Mobile',
				'Y!J-SRD',
				'Y!J-MBS',
				'DoCoMo',
				'SoftBank',
				'Vodafone',
				'J-PHONE',
				'UP.Browser'
			),
			'sessionId'	=> true
		),
		'smartphone'	=> array(
			'alias'		=> 's',
			'prefix'	=> 'smartphone',
			'autoRedirect'	=> true,
			'autoLink'	=> true,
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
/**
 * ヘルパ設定 
 */
	define('BC_BASER_HELPER'		, 'BcBaser');
	define('BC_BASER_ADMIN_HELPER'	, 'BcAdmin');
	define('BC_ARRAY_HELPER'		, 'BcArray');
	define('BC_CKEDITOR_HELPER'		, 'BcCkeditor');
	define('BC_CSV_HELPER'			, 'BcCsv');
	define('BC_FORM_HELPER'			, 'BcForm');
	define('BC_FREEZE_HELPER'		, 'BcFreeze');
	define('BC_GOOGLEMAPS_HELPER'	, 'BcGooglemaps');
	define('BC_HTML_HELPER'			, 'BcHtml');
	define('BC_MOBILE_HELPER'		, 'BcMobile');
	define('BC_SMARTPHONE_HELPER'	, 'BcSmartphone');
	define('BC_PAGE_HELPER'			, 'BcPage');
	define('BC_TEXT_HELPER'			, 'BcText');
	define('BC_TIME_HELPER'			, 'BcTime');
	define('BC_UPLOAD_HELPER'		, 'BcUpload');
	define('BC_XML_HELPER'			, 'BcXml');
	
