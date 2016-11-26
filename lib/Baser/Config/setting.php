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
$config['BcApp'] = [
	// デフォルトタイトル設定（インストールの際のエラー時等DB接続まえのエラーで利用）
	'title' => 'コーポレートサイトにちょうどいいCMS - baserCMS - ',
	// 初期テーマ
	'defaultTheme' => 'bc_sample',
	// 管理システムテーマ（キャメルケース）
	'adminTheme' => '',
	// テンプレートの基本となる拡張子（.php 推奨）
	'templateExt' => '.php',
	// システムナビ
	'adminNavi' => [
		'Contents' => [
			'Default' => [
				'title' => 'コンテンツ管理',
				'type' => 'contents',
				'menus' => [
					'Contents' => ['title' => 'コンテンツ一覧', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'contents', 'action' => 'index']],
					'ContentsTrash' => ['title' => 'ゴミ箱', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'contents', 'action' => 'trash_index']],
				]
			],
		],
		'Theme' => [
			'title' => 'テーマ管理',
			'type' => 'theme',
			'menus' => [
				['name' => 'Themes', 'title' => 'テーマ管理', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'index']],
				['name' => 'ThemesAdd', 'title' => 'テーマ新規追加', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'add']],
				['name' => 'ThemeConfigs', 'title' => 'テーマ設定', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'theme_configs', 'action' => 'form']],
				['name' => 'ThemeFilesCore', 'title' => 'コアテンプレート確認', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'theme_files', 'action' => 'index', 'core']],
				['name' => 'ThemesDownloadDefaultDataPattern', 'title' => 'テーマ用初期データダウンロード', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'download_default_data_pattern']],
				['name' => 'ThemesResetData', 'title' => 'データリセット', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'reset_data']],
			]
		],
		'Plugin' => [
			'title' => 'プラグイン設定',
			'type' => 'plugin',
			'menus' => [
				['name' => 'Plugins', 'title' => 'プラグイン管理', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'plugins', 'action' => 'index']],
			]
		],
		'System' => [
			'title' => 'システム設定',
			'type' => 'system',
			'menus' => [
				'SiteConfigs' => ['title' => 'サイト基本設定', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'form']],
				'WidgetAreas' => ['title' => 'ウィジェット管理', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'widget_areas', 'action' => 'index']],
				'Users' => ['title' => 'ユーザー管理', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'index']],
				'UserGroups' => ['title' => 'ユーザーグループ管理', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'user_groups', 'action' => 'index']],
				'SearchIndices' => ['title' => '検索インデックス管理', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'search_indices', 'action' => 'index']],
				'EditorTemplates' => ['title' => 'エディタテンプレート管理', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'editor_templates', 'action' => 'index']],
				'Sites' => ['title' => 'サブサイト管理', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'sites', 'action' => 'index']],
				'Tools' => ['title' => 'ユーティリティ', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'index']],
				'SiteConfigsDelCache' => ['title' => 'サーバーキャッシュ削除', 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'del_cache'], 'options' => ['confirm' => 'サーバーキャッシュを削除します。いいですか？']]
			]
		]
	],
	// コアプラグイン
	'corePlugins' => ['Blog', 'Feed', 'Mail', 'Uploader'],
	// アップデートキー
	'updateKey' => 'update',
	// 管理者グループID
	'adminGroupId' => 1,
	// エディター
	'editors' => [
		'none' => 'なし',
		'BcCkeditor' => 'CKEditor'
	],
	'testTheme' => 'nada-icons',
	'marketThemeRss' => 'https://market.basercms.net/themes.rss',
	'marketPluginRss' => 'https://market.basercms.net/plugins.rss',
	'specialThanks'	=> 'http://basercms.net/special_thanks/special_thanks/ajax_users'
];

/**
 * システム要件 
 */
$config['BcRequire'] = array(
	'phpVersion' => "5.4.0",
	'phpMemory' => "128"
);

/**
 * 環境設定 
 */
$config['BcEnv'] = array(
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

