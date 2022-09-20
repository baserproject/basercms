<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * アプリケーション基本設定
 */
$config['BcApp'] = [
    // デフォルトタイトル設定（インストールの際のエラー時等DB接続まえのエラーで利用）
    'title' => __d('baser', 'baserCMS'),
    // 初期テーマ
    'defaultTheme' => 'bc_sample',
    // 管理システムテーマ（キャメルケース）
    'adminTheme' => '',
    // テンプレートの基本となる拡張子（.php 推奨）
    'templateExt' => '.php',
    // システムナビ
    //
    // 初期状態で表示するメニューは、`Contents` キー配下に定義し、「設定」内に格納する場合は、`Systems` キー配下に定義する
    //
    // ■ メインメニュー
    // `title` : 表示名称
    // `type` : `system` または、コンテンツを特定する任意の文字列を指定。「設定」内に格納する場合は、`system` を指定
    // `url` : リンク先URL
    // `menus` : サブメニューが存在する場合に配列で指定
    // `disable` : 非表示にする場合に `true` を指定
    //
    // ■ サブメニュー
    // `title` : 表示名称
    // `url` : リンク先URL
    // `disable` : 非表示にする場合に `true` を指定
    'adminNavigation' => [
        'Contents' => [
            'Dashboard' => [
                'title' => __d('baser', 'ダッシュボード'),
                'type' => 'dashboard',
                'url' => '/' . Configure::read('Routing.prefixes.0'),
            ],
            'Contents' => [
                'title' => __d('baser', 'コンテンツ管理'),
                'type' => 'contents',
                'menus' => [
                    'Contents' => ['title' => __d('baser', 'コンテンツ'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'contents', 'action' => 'index']],
                    'ContentsTrash' => ['title' => __d('baser', 'ゴミ箱'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'contents', 'action' => 'trash_index']],
                ]
            ],
        ],
        'Systems' => [
            'SiteConfigs' => [
                'title' => __d('baser', 'サイト基本設定'),
                'type' => 'system',
                'url' => ['admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'form'],
            ],
            'Users' => [
                'title' => __d('baser', 'ユーザー管理'),
                'type' => 'system',
                'menus' => [
                    'Users' => [
                        'title' => __d('baser', 'ユーザー'),
                        'url' => ['admin' => true, 'plugin' => null, 'controller' => 'users', 'action' => 'index'],
                        'currentRegex' => '/\/users\/[^\/]+?/s'
                    ],
                    'UserGroups' => [
                        'title' => __d('baser', 'ユーザーグループ'),
                        'url' => ['admin' => true, 'plugin' => null, 'controller' => 'user_groups', 'action' => 'index'],
                        'currentRegex' => '/\/user_groups\/[^\/]+?/s'
                    ],
                ]
            ],
            'Sites' => [
                'title' => __d('baser', 'サブサイト管理'),
                'type' => 'system',
                'menus' => [
                    'Sites' => [
                        'title' => __d('baser', 'サブサイト'),
                        'url' => ['admin' => true, 'plugin' => null, 'controller' => 'sites', 'action' => 'index'],
                        'currentRegex' => '/\/sites\/.+?/s'
                    ],
                ]
            ],
            'Theme' => [
                'title' => __d('baser', 'テーマ管理'),
                'type' => 'system',
                'menus' => [
                    'Themes' => [
                        'title' => __d('baser', 'テーマ'),
                        'url' => ['admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'index'],
                        'currentRegex' => '/\/themes\/[^\/]+?/s'
                    ],
                    'ThemeConfigs' => ['title' => __d('baser', '設定'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'theme_configs', 'action' => 'form']],
                    'ThemeAdd' => ['title' => __d('baser', '新規追加'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'add']],
                    'ThemesDownload' => ['title' => __d('baser', '利用中テーマダウンロード'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'download']],
                    'ThemesDownloadDefaultDataPattern' => ['title' => __d('baser', 'テーマ用初期データダウンロード'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'themes', 'action' => 'download_default_data_pattern']],
                ]
            ],
            'Plugin' => [
                'title' => __d('baser', 'プラグイン管理'),
                'type' => 'system',
                'menus' => [
                    'Plugins' => [
                        'title' => __d('baser', 'プラグイン'),
                        'url' => ['admin' => true, 'plugin' => null, 'controller' => 'plugins', 'action' => 'index'],
                        'currentRegex' => '/\/plugins\/[^\/]+?/s'
                    ],
                ]
            ],
            'Tools' => [
                'title' => __d('baser', 'ユーティリティ'),
                'type' => 'system',
                'menus' => [
                    'Tools' => ['title' => __d('baser', 'ユーティリティトップ'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'index']],
                    'EditorTemplates' => [
                        'title' => __d('baser', 'エディタテンプレート'),
                        'url' => ['admin' => true, 'plugin' => null, 'controller' => 'editor_templates', 'action' => 'index'],
                        'currentRegex' => '/\/editor_templates\/[^\/]+?/s'
                    ],
                    'WidgetAreas' => [
                        'title' => __d('baser', 'ウィジェットエリア'),
                        'url' => ['admin' => true, 'plugin' => null, 'controller' => 'widget_areas', 'action' => 'index'],
                        'currentRegex' => '/\/widget_areas\/[^\/]+?\/[0-9]+/s'
                    ],
                    'SearchIndices' => ['title' => __d('baser', '検索インデックス'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'search_indices', 'action' => 'index']],
                    'SiteConfigsInfo' => ['title' => __d('baser', '環境情報'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'site_configs', 'action' => 'info']],
                    'ThemeFiles' => [
                        'title' => __d('baser', 'コアテンプレート確認'),
                        'url' => ['admin' => true, 'plugin' => null, 'controller' => 'theme_files', 'action' => 'index', 'core'],
                        'currentRegex' => '/\/theme_files\/[^\/]+?/s'
                    ],
                    'ToolsMaintenance' => ['title' => __d('baser', 'データメンテナンス'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'maintenance']],
                    'ToolsLog' => ['title' => __d('baser', 'ログメンテナンス'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'log']],
                    'ToolsWriteSchema' => ['title' => __d('baser', 'スキーマファイル生成'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'write_schema']],
                    'ToolsLoadSchema' => ['title' => __d('baser', 'スキーマファイル読込'), 'url' => ['admin' => true, 'plugin' => null, 'controller' => 'tools', 'action' => 'load_schema']],
                ]
            ]
        ]
    ],
    // コアプラグイン
    'corePlugins' => ['BcBlog', 'BcFeed', 'BcMail', 'BcUploader'],
    // 管理者グループID
    'adminGroupId' => 1,
    'testTheme' => 'bc_sample',
    // 固定ページでシンタックスエラーチェックを行うかどうか
    // お名前ドットコムの場合、CLI版PHPの存在確認の段階で固まってしまう
    'validSyntaxWithPage' => true,
    // 管理者以外のPHPコードを許可するかどうか
    'allowedPhpOtherThanAdmins' => true,
    // 管理システムのデフォルトテーマ
    'defaultAdminTheme' => 'admin-third',
    // コンテンツの作成日を自動で更新する
    'autoUpdateContentCreatedDate' => true,
    // オートプレフィックス除外設定（絶対URL）
    // 「すべてのリンクをサブサイト用に変換する」指定時、全てのリンクに対してプレフィックスを備える箇所に除外指定できる
    // 指定した絶対URLを記載しているリンクは変換しない
    // 例: 'https://basercms.net/'と記載 → https://basercms.net/s/ は s が付かなくなる
    'excludeAbsoluteUrlAddPrefix' => [],
    // オートプレフィックス除外設定（ディレクトリ）
    // 指定したディレクトリURLを記載しているリンクは変換しない
    // 例: 'test/' と記載 → https://basercms.net/s/test/ は s が付かなくなる
    'excludeListAddPrefix' => [],
    // generator のメタタグを出力するかどうか
    'outputMetaGenerator' => true,
    // システムメッセージの言語につてサイト設定を利用する
    // 	- false：ブラウザ
    // 	- true：サイト設定
    'systemMessageLangFromSiteSetting' => false
];

/**
 * システム要件
 */
$config['BcRequire'] = [
    'phpVersion' => "5.4.0",
    'phpMemory' => "128",
    'MySQLVersion' => "5.0.0",
    'winSQLiteVersion' => "3.7.16",
    'PostgreSQLVersion' => "8.4.0"
];

/**
 * 認証プレフィックス設定
 * ※ CSVは非対応
 */
$prefixes = Configure::read('Routing.prefixes');
$adminPrefix = $prefixes[0];
$config['BcPrefixAuth'] = [
    // 管理画面
    'admin' => [
        // 認証タイプ
        'type' => 'Form',
        // 認証設定名
        'name' => __d('baser', '管理システム'),
        // URLにおけるエイリアス
        'alias' => $adminPrefix,
        // 認証後リダイレクト先
        'loginRedirect' => '/' . $adminPrefix,
        // ログイン画面タイトル
        'loginTitle' => __d('baser', '管理システムログイン'),
        // ログインページURL
        'loginAction' => '/' . $adminPrefix . '/users/login',
        // ログアウトページURL
        'logoutAction' => '/' . $adminPrefix . '/users/logout',
        // ツールバー利用
        'toolbar' => true,
        // モデル
        'userModel' => 'User',
        // セッションキー
        'sessionKey' => 'Admin',
        // preview及びforce指定時に管理画面へログインしていない状況下での挙動判別
        // true：ログイン画面へリダイレクト
        // false：ログイン画面へリダイレクトしない
        // @see /lib/Baser/Routing/Route/BcContentsRoute.php
        'previewRedirect' => true

    ]
    // フロント（例）
    /* 'front' => array(
          'name'			=> __d('baser', 'フロント'),
          'loginRedirect'	=> '/',
          'userModel'		=> 'User',
          'loginAction'	=> '/users/login',
          'logoutAction'=> '/users/logout',
          'toolbar'		=> true,
          'sessionKey'	=> 'User'
        ), */
    // マイページ（例）
    /* 'mypage' => array(
          'name'			=> __d('baser', 'マイページ'),
          'alias'			=> 'mypage',
          'loginRedirect'	=> '/mypage/members/index',
          'loginTitle'	=> __d('baser', 'マイページログイン'),
          'userModel'		=> 'Member',
          'loginAction'	=> '/mypage/members/login',
          'logoutAction'=> '/mypage/members/logout',
          'toolbar'		=> false,
          'sessionKey'	=> 'User'
        ) */
];

/**
 * Eメール設定
 */
$config['BcEmail'] = [
    // 改行コード
    'lfcode' => "\n"
];

/**
 * コンテンツ設定
 */
$config['BcContents'] = [
    'items' => [
        'BaserCore' => [
            'Default' => [
                'title' => __d('baser', '無所属コンテンツ'),
                'omitViewAction' => true,
                'routes' => [
                    'add' => [
                        'admin' => true,
                        'controller' => 'contents',
                        'action' => 'add'
                    ],
                    'edit' => [
                        'admin' => true,
                        'controller' => 'contents',
                        'action' => 'edit'
                    ],
                    'delete' => [
                        'admin' => true,
                        'controller' => 'contents',
                        'action' => 'empty'
                    ],
                    'view' => [
                        'controller' => 'contents',
                        'action' => 'view'
                    ]
                ],
                'icon' => 'bca-icon--file',
            ],
            'ContentFolder' => [
                'multiple' => true,
                'preview' => true,
                'title' => __d('baser', 'フォルダー'),
                'routes' => [
                    'add' => [
                        'admin' => true,
                        'controller' => 'content_folders',
                        'action' => 'add'
                    ],
                    'edit' => [
                        'admin' => true,
                        'controller' => 'content_folders',
                        'action' => 'edit'
                    ],
                    'delete' => [
                        'admin' => true,
                        'controller' => 'content_folders',
                        'action' => 'delete'
                    ],
                    'view' => [
                        'controller' => 'content_folders',
                        'action' => 'view'
                    ]
                ],
                'icon' => 'bca-icon--folder',
            ],
            'ContentAlias' => [
                'multiple' => true,
                'title' => __d('baser', 'エイリアス'),
                'icon' => 'bca-icon--alias',
                'routes' => [
                    'add' => [
                        'admin' => true,
                        'controller' => 'contents',
                        'action' => 'add',
                        1
                    ],
                    'edit' => [
                        'admin' => true,
                        'controller' => 'contents',
                        'action' => 'edit_alias'
                    ]
                ],
            ],
            'ContentLink' => [
                'multiple' => true,
                'title' => __d('baser', 'リンク'),
                'omitViewAction' => true,
                'routes' => [
                    'add' => [
                        'admin' => true,
                        'controller' => 'content_links',
                        'action' => 'add'
                    ],
                    'edit' => [
                        'admin' => true,
                        'controller' => 'content_links',
                        'action' => 'edit'
                    ],
                    'delete' => [
                        'admin' => true,
                        'controller' => 'content_links',
                        'action' => 'delete'
                    ],
                    'view' => [
                        'controller' => 'content_links',
                        'action' => 'view'
                    ]
                ],
                'icon' => 'bca-icon--link',
            ],
            'Page' => [
                'title' => __d('baser', '固定ページ'),
                'multiple' => true,
                'preview' => true,
                'icon' => 'bca-icon--file',
                'omitViewAction' => true,
                'routes' => [
                    'add' => [
                        'admin' => true,
                        'controller' => 'pages',
                        'action' => 'ajax_add'
                    ],
                    'edit' => [
                        'admin' => true,
                        'controller' => 'pages',
                        'action' => 'edit'
                    ],
                    'delete' => [
                        'admin' => true,
                        'controller' => 'pages',
                        'action' => 'delete'
                    ],
                    'view' => [
                        'controller' => 'pages',
                        'action' => 'display'
                    ],
                    'copy' => [
                        'admin' => true,
                        'controller' => 'pages',
                        'action' => 'ajax_copy'
                    ]
                ]
            ]
        ]
    ]
];

/**
 * ショートコード設定
 */
$config['BcShortCode']['BaserCore'] = [
    'BcBaser.getSitemap',
    'BcBaser.getRelatedSiteLinks',
    'BcBaser.getWidgetArea',
    'BcBaser.getGoogleMaps',
    'BcBaser.getSiteSearchForm',
    'BcBaser.getUpdateInfo'
];

/**
 * ウィジェット
 */
$config['BcWidget'] = [
    // フロントにウィジェットエリアの編集リンクを表示するかどうか
    'editLinkAtFront' => false
];
