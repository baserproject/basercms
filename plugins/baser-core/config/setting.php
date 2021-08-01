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

use Cake\Cache\Engine\FileEngine;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * @checked
 * @unitTest
 */
$baserCorePrefix = '/baser';
$adminPrefix = '/admin';
return [
    'BcEnv' => [
        /**
         * サイトURL
         */
        'siteUrl' => env('SITE_URL', 'https://localhost/'),
        /**
         * SSL URL
         */
        'sslUrl' => env('SSL_URL', 'https://localhost/')
    ],
    'BcApp' => [
        /**
         * baserコアのプレフィックス
         * URLの先頭に付与
         */
        'baserCorePrefix' => $baserCorePrefix,
        /**
         * 管理システムのプレフィックス
         * baserコアのプレフィックスの後に付与
         */
        'adminPrefix' => $adminPrefix,
        /**
         * 特権管理者グループID
         */
        'adminGroup' => ['admins'],
        // 管理者グループID
        'adminGroupId' => 1,
        /**
         * コアパッケージ名
         */
        'core' => ['baser-core', 'bc-admin-third'],
        /**
         * コアプラグイン
         */
        'corePlugins' => ['BcBlog', 'BcMail'],
        /**
         * パスワード再発行URLの有効時間(min) デフォルト24時間
         */
        'passwordRequestAllowTime' => 1440,
        /**
         * baserマーケットRSS
         */
        'marketPluginRss' => 'https://market.basercms.net/plugins.php',
        /**
         * 管理画面のSSL
         */
        'adminSsl' => filter_var(env('ADMIN_SSL'), FILTER_VALIDATE_BOOLEAN),
        /**
         * エディタ
         */
        'editors' => [
            'none' => __d('baser', 'なし'),
            'BcCkeditor' => 'CKEditor'
        ],
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
                    'url' => $baserCorePrefix . $adminPrefix,
                ],
                'Contents' => [
                    'title' => __d('baser', 'コンテンツ管理'),
                    'type' => 'contents',
                    'menus' => [
                        'Contents' => ['title' => __d('baser', 'コンテンツ'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'contents', 'action' => 'index']],
                        'ContentsTrash' => ['title' => __d('baser', 'ゴミ箱'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'contents', 'action' => 'trash_index']],
                    ]
                ],
            ],
            'Systems' => [
                'SiteConfigs' => [
                    'title' => __d('baser', 'システム基本設定'),
                    'type' => 'system',
                    'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'site_configs', 'action' => 'index']
                ],
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
                'Sites' => [
                    'title' => __d('baser', 'サイト管理'),
                    'type' => 'system',
                    'menus' => [
                        'Sites' => [
                            'title' => __d('baser', 'サイト'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'sites', 'action' => 'index'],
                            'currentRegex' => '/\/sites\/.+?/s'
                        ],
                    ]
                ],
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
                //					'SiteConfigsInfo' => ['title' => __d('baser', '環境情報'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'tools', 'action' => 'info']],
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
            // 認証設定名
            'name' => __d('baser', '管理システム'),
            // 認証タイプ
            'type' => 'Form',
            // URLにおけるエイリアス
            'alias' => $adminPrefix,
            // 認証後リダイレクト先
            'loginRedirect' => ['plugin' => 'BaserCore', 'prefix' => 'Admin', 'controller' => 'Dashboard', 'action' => 'index'],
            // ログインページURL
            'loginAction' => ['plugin' => 'BaserCore', 'prefix' => 'Admin', 'controller' => 'Users', 'action' => 'login'],
            // ログアウトページURL
            'logoutAction' => ['plugin' => 'BaserCore', 'prefix' => 'Admin', 'controller' => 'Users', 'action' => 'logout'],
            // ユーザー名フィールド
            'username' => ['email', 'name'],
            // パスワードフィールド
            'password' => 'password',
            // モデル
            'userModel' => 'BaserCore.Users',
            // セッションキー
            'sessionKey' => 'AuthAdmin',
        ],
    ],
    'Jwt' => [
        // kid（鍵の識別子）
        'kid' => 'Xprg7JjhII1HEtGscjyIhf4Y852gSW4qBbiTXUV69R3ewY5QNfiHNqTo6I8iWhpH',
        // 発行者
        'iss' => 'baser',
        // アルゴリズム：RS256 / HS256
        'algorithm' => 'RS256',
        // アクセストークン有効期間（秒：30分間）
        'accessTokenExpire' => 60 * 30,
        // リフレッシュトークン有効期間（秒：14日間）
        'refreshTokenExpire' => 60 * 60 * 24 * 14,
        // 秘密鍵のパス
        'privateKeyPath' => CONFIG . 'jwt.key',
        // 公開鍵のパス
        'publicKeyPath' => CONFIG . 'jwt.pem'
    ],
    'links' => [
        'marketThemeRss' => 'https://market.basercms.net/themes.rss',
        'marketPluginRss' => 'https://market.basercms.net/plugins.rss',
        'specialThanks' => 'https://basercms.net/special_thanks/special_thanks/ajax_users',
        // インストールマニュアル
        'installManual' => 'https://basercms.net/manuals/introductions/4.html',
        // アップデートマニュアル
        'updateManual' => 'https://basercms.net/manuals/introductions/8.html'
    ],

    /**
     * セッション
     */
    'Session' => [
        'defaults' => 'php',
        /**
         * セッションの有効期限（分）
         * デフォルト：2日間
         */
        'timeout' => 60 * 24 * 2
    ],

    /**
     * キャッシュ
     */
    'Cache' => [
        '_bc_env_' => [
            'className' => FileEngine::class,
            'prefix' => 'myapp_bc_env_',
            'path' => CACHE . 'environment' . DS,
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_BCENV_URL', null),
        ],
    ],

    /**
     * エージェント設定
     */
    'BcAgent' => [
        'mobile' => [
            'name' => __d('baser', 'ケータイ'),
            'helper' => 'BcMobile',
            'agents' => [
                'Googlebot-Mobile',
                'Y!J-SRD',
                'Y!J-MBS',
                'DoCoMo',
                'SoftBank',
                'Vodafone',
                'J-PHONE',
                'UP.Browser'
            ],
            'sessionId' => true
        ],
        'smartphone' => [
            'name' => __d('baser', 'スマートフォン'),
            'helper' => 'BcSmartphone',
            'agents' => [
                'iPhone',            // Apple iPhone
                'iPod',                // Apple iPod touch
                'Android',            // 1.5+ Android
                'dream',            // Pre 1.5 Android
                'CUPCAKE',            // 1.5+ Android
                'blackberry9500',    // Storm
                'blackberry9530',    // Storm
                'blackberry9520',    // Storm v2
                'blackberry9550',    // Storm v2
                'blackberry9800',    // Torch
                'webOS',            // Palm Pre Experimental
                'incognito',        // Other iPhone browser
                'webmate'            // Other iPhone browser
            ]
        ]
    ],
    /**
     * 言語設定
     */
    'BcLang' => [
        'english' => [
            'name' => __d('baser', '英語'),
            'langs' => [
                'en'
            ]
        ],
        'chinese' => [
            'name' => __d('baser', '中国語'),
            'langs' => [
                'zh'
            ]
        ],
        'spanish' => [
            'name' => __d('baser', 'スペイン'),
            'langs' => [
                'es'
            ]
        ]
    ],
    /**
     * 文字コード設定
     */
    'BcEncode' => [
        // 文字コードの検出順
        'detectOrder' => 'ASCII,JIS,UTF-8,SJIS-win,EUC-JP',
        'mail' => [
            'UTF-8' => 'UTF-8',
            'ISO-2022-JP' => 'ISO-2022-JP'
        ]
    ],
    /**
     * コンテンツ設定
     */
    'BcContents' => [
        'items' => [
            'Core' => [
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
    ],
];
