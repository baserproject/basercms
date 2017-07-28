<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
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
	'defaultTheme' => 'bc_sample',
	// 管理システムテーマ（キャメルケース）
	'adminTheme' => '',
	// テンプレートの基本となる拡張子（.php 推奨）
	'templateExt' => '.php',
	// システムナビ
	'adminNavi' => array('core' => array(
			'name' => 'baserCMSコア',
			'contents' => array(
				array('name' => 'コンテンツ管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'contents', 'action' => 'index')),
				array('name' => 'ウィジェット管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'widget_areas', 'action' => 'index')),
				array('name' => 'テーマ管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'index')),
				array('name' => 'プラグイン管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'plugins', 'action' => 'index')),
				array('name' => 'システム設定', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'form')),
				array('name' => 'ユーザー管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'index')),
				array('name' => 'ユーザーグループ管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'user_groups', 'action' => 'index')),
				array('name' => '検索インデックス管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'search_indices', 'action' => 'index')),
				array('name' => 'エディタテンプレート管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'editor_templates', 'action' => 'index')),
				array('name' => 'サブサイト管理', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'sites', 'action' => 'index')),
				array('name' => 'ユーティリティ', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'index')),
				array('name' => 'サーバーキャッシュ削除', 'url' => array('admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'del_cache'), 'options' => array('confirm' => 'サーバーキャッシュを削除します。いいですか？'))
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
	'testTheme' => 'nada-icons',
	'marketThemeRss' => 'https://market.basercms.net/themes.rss',
	'marketPluginRss' => 'https://market.basercms.net/plugins.rss',
	'specialThanks'	=> 'http://basercms.net/special_thanks/special_thanks/ajax_users'
);

/**
 * システム要件 
 */
$config['BcRequire'] = array(
	'phpVersion' => "5.4.0",
	'phpMemory' => "128",
	'MySQLVersion' => "5.0.0",
	'PostgreSQLVersion' => "8.4.0"
);

/**
 * 環境設定 
 */
$config['BcEnv'] = array(
	// テストDBプレフィックス
	'testDbPrefix' => 'test_',
	// WebサイトURL（インストーラーで install.php 自動設定される、システム設定で変更可）
	'siteUrl' => '',
	// SSLのWebサイトURL（システム設定で変更可）
	'sslUrl' => '',
	// 復数のWebサイトを管理する場合のメインとなるドメイン
	'mainDomain' => '',
	// 現在のリクエストのホスト
	'host' => @$_SERVER['HTTP_HOST']
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
		'sessionKey' => 'Admin'
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
		'name' => 'ケータイ',
		'helper' => 'BcMobile',
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
		'name' => 'スマートフォン',
		'helper' => 'BcSmartphone',
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

//p($_SERVER['HTTP_ACCEPT_LANGUAGE']);
$config['BcLang'] = [
	'english' => [
		'name' => '英語',
		'langs' => [
			'en'
		]	
	]
];

/**
 * コンテンツ設定
 */
$config['BcContents'] = [
	'items' => [
		'Core' => [
			'Default'	=> [
				'title' => '無所属コンテンツ',
				'omitViewAction' => true,
				'routes' => [
					'add'	=> [
						'admin' => true,
						'controller'=> 'contents',
						'action'	=> 'add'
					],
					'edit'	=> [
						'admin' => true,
						'controller'=> 'contents',
						'action'	=> 'edit'
					],
					'delete' => [
						'admin' => true,
						'controller'=> 'contents',
						'action'	=> 'empty'
					],
					'view' => [
						'controller'=> 'contents',
						'action'	=> 'view'
					]
				],
				'icon'	=> 'admin/icon_content.png',
			],
			'ContentFolder'	=> [
				'multiple'	=> true,
				'preview'	=> true,
				'title' => 'フォルダー',
				'routes' => [
					'add'	=> [
						'admin' => true,
						'controller'=> 'content_folders',
						'action'	=> 'add'
					],
					'edit'	=> [
						'admin' => true,
						'controller'=> 'content_folders',
						'action'	=> 'edit'
					],
					'delete' => [
						'admin' => true,
						'controller'=> 'content_folders',
						'action'	=> 'delete'
					],
					'view' => [
						'controller'=> 'content_folders',
						'action'	=> 'view'
					]
				],
				'icon'	=> 'admin/icon_folder.png',
			],
			'ContentAlias'	=> [
				'multiple' => true,
				'title' => 'エイリアス',
				'icon'	=> 'admin/icon_alias.png',
				'routes' => [
					'add'	=> [
						'admin' => true,
						'controller'=> 'contents',
						'action'	=> 'add',
						1
					],
					'edit'	=> [
						'admin' => true,
						'controller'=> 'contents',
						'action'	=> 'edit_alias'
					]
				],
			],
			'ContentLink'	=> [
				'multiple' => true,
				'title' => 'リンク',
				'omitViewAction' => true,
				'routes' => [
					'add'	=> [
						'admin' => true,
						'controller'=> 'content_links',
						'action'	=> 'add'
					],
					'edit'	=> [
						'admin' => true,
						'controller'=> 'content_links',
						'action'	=> 'edit'
					],
					'delete' => [
						'admin' => true,
						'controller'=> 'content_links',
						'action'	=> 'delete'
					],
					'view' => [
						'controller'=> 'content_links',
						'action'	=> 'view'
					]
				],
				'icon'	=> 'admin/icon_link.png',
			],
			'Page'	=> [
				'title' => '固定ページ',
				'multiple'	=> true,
				'preview'	=> true,
				'icon'	=> 'admin/icon_page.png',
				'omitViewAction' => true,
				'routes' => [
					'add'	=> [
						'admin' => true,
						'controller'=> 'pages',
						'action'	=> 'ajax_add'
					],
					'edit'	=> [
						'admin' => true,
						'controller'=> 'pages',
						'action'	=> 'edit'
					],
					'delete' => [
						'admin' => true,
						'controller'=> 'pages',
						'action'	=> 'delete'
					],
					'view' => [
						'controller'=> 'pages',
						'action'	=> 'display'
					],
					'copy'	=> [
						'admin' => true,
						'controller'=> 'pages',
						'action'	=> 'ajax_copy'
					]
				]
			]
		]
	]
];

/**
 * ショートコード設定
 */
$config['BcShortCode']['Core'] = [
	'BcBaser.getSitemap',
	'BcBaser.getRelatedSiteLinks',
	'BcBaser.getWidgetArea',
	'BcBaser.getGoogleMaps',
	'BcBaser.getSiteSearchForm',
	'BcBaser.getUpdateInfo'
];

/**
 * セキュリティ設定
 */
$config['BcSecurity'] = [
	'csrfExpires' => '+4 hours'
];
