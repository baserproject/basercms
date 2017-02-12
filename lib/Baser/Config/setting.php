<?php

/**
 * baserCMS設定ファイル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Config
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * アプリケーション基本設定
 */
$config['BcApp'] = array(
	// デフォルトタイトル設定（インストールの際のエラー時等DB接続まえのエラーで利用）
	'title' => 'コーポレートサイトにちょうどいいCMS - baserCMS - ',
	// 初期テーマ
	'defaultTheme' => 'bccolumn',
	// 管理システムテーマ（キャメルケース）
	'adminTheme' => '',
	// テンプレートの基本となる拡張子（.php 推奨）
	'templateExt' => '.php',
	// システムナビ
	'adminNavi' => array('core' => array(
			'name' => 'baserCMSコア',
			'contents' => array(
				array('name' => '固定ページ一覧', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'pages', 'action' => 'index')),
				array('name' => '固定ページカテゴリ一覧', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'page_categories', 'action' => 'index')),
				array('name' => 'ウィジェット管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'widget_areas', 'action' => 'index')),
				array('name' => 'テーマ管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'index')),
				array('name' => 'プラグイン管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'plugins', 'action' => 'index')),
				array('name' => 'システム設定', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'form')),
				array('name' => 'ユーザー一覧', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'index')),
				array('name' => 'ユーザー登録', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'add')),
				array('name' => 'ユーザーグループ一覧', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'user_groups', 'action' => 'index')),
				array('name' => 'ユーザーグループ登録', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'user_groups', 'action' => 'add')),
				array('name' => '検索インデックス管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'contents', 'action' => 'index')),
				array('name' => 'メニュー一覧', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'menus', 'action' => 'index')),
				array('name' => 'メニュー登録', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'menus', 'action' => 'add')),
				array('name' => 'エディタテンプレート一覧', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'editor_templates', 'action' => 'index')),
				array('name' => 'エディタテンプレート登録', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'editor_templates', 'action' => 'add')),
				array('name' => 'サーバーキャッシュ削除', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'del_cache')),
				array('name' => 'データメンテナンス', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'maintenance')),
				array('name' => '環境情報', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'info'))
			))),
	// コアプラグイン
	'corePlugins' => array('Blog', 'Feed', 'Mail', 'Uploader'),
	// アップデートキー
	'updateKey' => 'update',
	// 管理者グループID
	'adminGroupId' => 1,
	// エディター
	'editors' => array(
		'none' => 'なし',
		'BcCkeditor' => 'CKEditor'
	),
	'marketThemeRss' => 'https://market.basercms.net/themes.rss',
	'marketPluginRss' => 'https://market.basercms.net/plugins.rss',
	'specialThanks'	=> 'http://basercms.net/special_thanks/special_thanks/ajax_users'
);
/**
 * システム要件 
 */
$config['BcRequire'] = array(
	'phpVersion' => "5.2.0",
	'phpMemory' => "32"
);
/**
 * 環境設定 
 */
$config['BcEnv'] = array(
	// プラグインDBプレフィックス
	'pluginDbPrefix' => 'pg_',
	// テストDBプレフィックス
	'testDbPrefix' => 'test_',
);
/**
 * 文字コード設定
 */
$config['BcEncode'] = array(
	// 文字コードの検出順
	'detectOrder' => 'ASCII,JIS,UTF-8,SJIS-win,EUC-JP',
	'mail' => array(
		'UTF-8' => 'UTF-8',
		'ISO-2022-JP' => 'ISO-2022-JP'
	)
);

/**
 * 認証プレフィックス設定
 * ※ CSVは非対応
 */
$prefixes = Configure::read('Routing.prefixes');
$adminPrefix = $prefixes[0];
$config['BcAuthPrefix'] = array(
	// 管理画面
	'admin' => array(
		// 認証設定名
		'name' => '管理システム',
		// URLにおけるエイリアス
		'alias' => $adminPrefix,
		// 認証後リダイレクト先
		'loginRedirect' => '/' . $adminPrefix,
		// ログイン画面タイトル
		'loginTitle' => '管理システムログイン',
		// ログインページURL
		'loginAction' => '/' . $adminPrefix . '/users/login',
		// ログアウトページURL
		'logoutAction'=> '/' . $adminPrefix . '/users/logout',
		// ツールバー利用
		'toolbar' => true,
		// モデル
		'userModel' => 'User',
		// セッションキー
		// TODO Adminとした方がわかりやすいが、アップデート時にセッションキーが変わってしまうと
		// 予期せぬ事態が発生しかねない為、メジャーバージョンアップのタイミングで変更する
		'sessionKey' => 'User'
	)
	// フロント（例）
/* 'front' => array(
	  'name'			=> 'フロント',
	  'loginRedirect'	=> '/',
	  'userModel'		=> 'User',
	  'loginAction'	=> '/users/login',
	  'logoutAction'=> '/users/logout',
	  'toolbar'		=> true,
	  'sessionKey'	=> 'User'
	), */
	// マイページ（例）
/* 'mypage' => array(
	  'name'			=> 'マイページ',
	  'alias'			=> 'mypage',
	  'loginRedirect'	=> '/mypage/members/index',
	  'loginTitle'	=> 'マイページログイン',
	  'userModel'		=> 'Member',
	  'loginAction'	=> '/mypage/members/login',
	  'logoutAction'=> '/mypage/members/logout',
	  'toolbar'		=> false,
	  'sessionKey'	=> 'User'
	) */
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
	'mobile' => array(
		'alias' => 'm',
		'prefix' => 'mobile',
		'autoRedirect' => true,
		'autoLink' => true,
		'agents' => array(
			'Googlebot-Mobile',
			'Y!J-SRD',
			'Y!J-MBS',
			'DoCoMo',
			'SoftBank',
			'Vodafone',
			'J-PHONE',
			'UP.Browser'
		),
		'sessionId' => true
	),
	'smartphone' => array(
		'alias' => 's',
		'prefix' => 'smartphone',
		'autoRedirect' => true,
		'autoLink' => true,
		'agents' => array(
			'iPhone',			// Apple iPhone
			'iPod',				// Apple iPod touch
			'Android',			// 1.5+ Android
			'dream',			// Pre 1.5 Android
			'CUPCAKE',			// 1.5+ Android
			'blackberry9500',	// Storm
			'blackberry9530',	// Storm
			'blackberry9520',	// Storm v2
			'blackberry9550',	// Storm v2
			'blackberry9800',	// Torch
			'webOS',			// Palm Pre Experimental
			'incognito',		// Other iPhone browser
			'webmate'			// Other iPhone browser
		)
	)
);

/**
 * セキュリティ設定
 */
$config['BcSecurity'] = array(
	'csrfExpires' => '+4 hours'
);