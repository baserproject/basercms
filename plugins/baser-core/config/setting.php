<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

return [
    'BcApp' => [
    /**
     * システムナビ
     *
     * 初期状態で表示するメニューは、`Contents` キー配下に定義し、「設定」内に格納する場合は、`Systems` キー配下に定義する
     *
     * ■ メインメニュー
     * `title` : 表示名称
     * `type` : `system` または、コンテンツを特定する任意の文字列を指定。「設定」内に格納する場合は、`system` を指定
     * `url` : リンク先URL
     * `menus` : サブメニューが存在する場合に配列で指定
     * `disable` : 非表示にする場合に `true` を指定
     *
     * ■ サブメニュー
     * `title` : 表示名称
     * `url` : リンク先URL
     * `disable` : 非表示にする場合に `true` を指定
     */
	'adminNavigation' => [
		'Contents' => [
			'Dashboard' => [
				'title' => __d('baser', 'ダッシュボード'),
				'type' => 'dashboard',
				'url' => env('BC_BASER_CORE_PATH') . env('BC_ADMIN_PREFIX', '/admin'),
			],
//			'Contents' => [
//				'title' => __d('baser', 'コンテンツ管理'),
//				'type' => 'contents',
//				'menus' => [
//					'Contents' => ['title' => __d('baser', 'コンテンツ'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'contents', 'action' => 'index']],
//					'ContentsTrash' => ['title' => __d('baser', 'ゴミ箱'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'contents', 'action' => 'trash_index']],
//				]
//			],
		],
		'Systems' => [
//			'SiteConfigs' => [
//				'title' => __d('baser', 'サイト基本設定'),
//				'type' => 'system',
//				'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'site_configs', 'action' => 'form'],
//			],
			'Users' => [
				'title' => __d('baser', 'ユーザー管理'),
				'type' => 'system',
				'menus' => [
					'Users' => [
						'title' => __d('baser', 'ユーザー'),
						'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'users', 'action' => 'index'],
						'currentRegex' => '/\/users\/[^\/]+?/s'
					],
					'UserGroups' => [
						'title' => __d('baser', 'ユーザーグループ'),
						'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'user_groups', 'action' => 'index'],
						'currentRegex' => '/\/user_groups\/[^\/]+?/s'
					],
				]
			],
//			'Sites' => [
//				'title' => __d('baser', 'サブサイト管理'),
//				'type' => 'system',
//				'menus' => [
//					'Sites' => [
//						'title' => __d('baser', 'サブサイト'),
//						'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'sites', 'action' => 'index'],
//						'currentRegex' => '/\/sites\/.+?/s'
//					],
//				]
//			],
//			'Theme' => [
//				'title' => __d('baser', 'テーマ管理'),
//				'type' => 'system',
//				'menus' => [
//					'Themes' => [
//						'title' => __d('baser', 'テーマ'),
//						'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'index'],
//						'currentRegex' => '/\/themes\/[^\/]+?/s'
//					],
//					'ThemeConfigs' => ['title' => __d('baser', '設定'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'theme_configs', 'action' => 'form']],
//					'ThemeAdd' => ['title' => __d('baser', '新規追加'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'add']],
//					'ThemesDownload' => ['title' => __d('baser', '利用中テーマダウンロード'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'download']],
//					'ThemesDownloadDefaultDataPattern' => ['title' => __d('baser', 'テーマ用初期データダウンロード'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'download_default_data_pattern']],
//				]
//			],
			'Plugin' => [
				'title' => __d('baser', 'プラグイン管理'),
				'type' => 'system',
				'menus' => [
					'Plugins' => [
						'title' => __d('baser', 'プラグイン'),
						'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'plugins', 'action' => 'index'],
						'currentRegex' => '/\/plugins\/[^\/]+?/s'
					],
				]
			],
//			'Tools' => [
//				'title' => __d('baser', 'ユーティリティ'),
//				'type' => 'system',
//				'menus' => [
//					'Tools' => ['title' => __d('baser', 'ユーティリティトップ'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'tools', 'action' => 'index']],
//					'EditorTemplates' => [
//						'title' => __d('baser', 'エディタテンプレート'),
//						'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'editor_templates', 'action' => 'index'],
//						'currentRegex' => '/\/editor_templates\/[^\/]+?/s'
//					],
//					'WidgetAreas' => [
//						'title' => __d('baser', 'ウィジェットエリア'),
//						'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'widget_areas', 'action' => 'index'],
//						'currentRegex' => '/\/widget_areas\/[^\/]+?\/[0-9]+/s'
//					],
//					'SearchIndices' => ['title' => __d('baser', '検索インデックス'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'search_indices', 'action' => 'index']],
//					'SiteConfigsInfo' => ['title' => __d('baser', '環境情報'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'site_configs', 'action' => 'info']],
//					'ThemeFiles' => [
//						'title' => __d('baser', 'コアテンプレート確認'),
//						'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'theme_files', 'action' => 'index', 'core'],
//						'currentRegex' => '/\/theme_files\/[^\/]+?/s'
//					],
//					'ToolsMaintenance' => ['title' => __d('baser', 'データメンテナンス'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'tools', 'action' => 'maintenance']],
//					'ToolsLog' => ['title' => __d('baser', 'ログメンテナンス'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'tools', 'action' => 'log']],
//					'ToolsWriteSchema' => ['title' => __d('baser', 'スキーマファイル生成'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'tools', 'action' => 'write_schema']],
//					'ToolsLoadSchema' => ['title' => __d('baser', 'スキーマファイル読込'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'tools', 'action' => 'load_schema']],
//				]
//			]
		]
	]],
    /**
     * プレフィックス認証
     */
    'BcPrefixAuth' => [
        // 管理画面
	    'Admin' => [
            // 認証タイプ
            'type' => 'Form',
            // URLにおけるエイリアス
            'alias' => env('BC_ADMIN_PREFIX'),
            // 認証後リダイレクト先
            'loginRedirect' => env('BC_BASER_CORE_PATH') . env('BC_ADMIN_PREFIX', '/admin'),
            // ログインページURL
            'loginAction' => env('BC_BASER_CORE_PATH') . env('BC_ADMIN_PREFIX', '/admin') . '/users/login',
            // ログアウトページURL
            'logoutAction'=> env('BC_BASER_CORE_PATH') . env('BC_ADMIN_PREFIX') . '/users/logout',
            // ユーザー名フィールド
            'username' => 'email',
            // パスワードフィールド
            'password' => 'password',
            // モデル
            'userModel' => 'Users',
            // セッションキー
            'sessionKey' => 'AuthAdmin',
	    ]
    ]
];
