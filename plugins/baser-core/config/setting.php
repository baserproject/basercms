<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       https://basercms.net/license/index.html MIT License
 */

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * setting
 *
 * カスタマイズを行う場合は、 config/setting.example.php を config/setting.php としてコピーし設定値を定義する。
 * app.php に関するカスタマイズは、こちらには定義せず、 config/app_local.example.php に定義する。
 *
 * @checked
 * @unitTest
 * @note(value="テーマ管理とユーティリティを実装してからメニューを表示する")
 */
$baserCorePrefix = filter_var(env('BASER_CORE_PREFIX', 'baser'));
$adminPrefix = filter_var(env('ADMIN_PREFIX', 'admin'));

return [
    'BcEnv' => [
        /**
         * サイトURL
         */
        'siteUrl' => env('SITE_URL', 'https://localhost/'),
        /**
         * SSL URL
         */
        'sslUrl' => env('SSL_URL', 'https://localhost/'),
        /**
         * CMS URL
         * CMSのURLが別ドメインの場合に設定する
         */
        'cmsUrl' => '',
        /**
         * 復数のWebサイトを管理する場合のメインとなるドメイン
         */
        'mainDomain' => '',
        /**
         * 現在のリクエストのホスト
         */
        'host' => (isset($_SERVER['HTTP_HOST']))? $_SERVER['HTTP_HOST'] : null
    ],
    'BcApp' => [
        // デフォルトタイトル設定（インストールの際のエラー時等DB接続まえのエラーで利用）
        'title' => __d('baser', 'baserCMS'),
        // テンプレートの基本となる拡張子（.php 推奨）
        'templateExt' => '.php',
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
        // お名前ドットコムの場合、CLI版PHPの存在確認の段階で固まってしまう
        'validSyntaxWithPage' => true,
        // 管理者以外のPHPコードを許可するかどうか
        'allowedPhpOtherThanAdmins' => false,
        /**
         * コアパッケージ名
         * プラグイン一覧に表示しないようにする
         */
        'core' => ['BaserCore', 'BcAdminThird', 'BcFront', 'BcInstaller'],
        /**
         * デフォルトフロントテーマ
         */
        'defaultFrontTheme' => 'bc-front',
        /**
         * デフォルト管理画面テーマ
         */
        'defaultAdminTheme' => 'bc-admin-third',
        /**
         * 管理画面をカスタマイズするためのテーマ
         * アッパーキャメルケースで指定する
         */
        'customAdminTheme' => '',
        /**
         * コアプラグイン
         */
        'corePlugins' => [
            'BcSearchIndex',
            'BcBlog',
            'BcMail',
            'BcEditorTemplate',
            'BcThemeConfig',
            'BcThemeFile',
            'BcUploader',
            'BcWidgetArea',
            'BcContentLink',
            'BcFavorite'
        ],
        /**
         * パスワード再発行URLの有効時間(min) デフォルト24時間
         */
        'passwordRequestAllowTime' => 1440,
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
         * アップデートキー
         * アップデート時のURLに使用する
         * 例）https://localhost/update
         */
        'updateKey' => env('UPDATE_KEY', 'update'),
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
                    'url' => '/' . $baserCorePrefix . '/' . $adminPrefix,
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
                'Theme' => [
                    'title' => __d('baser', 'テーマ管理'),
                    'type' => 'system',
                    'menus' => [
                        'Themes' => [
                            'title' => __d('baser', 'テーマ'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'index'],
                            'currentRegex' => '/\/themes\/[^\/]+?/s'
                        ],
                        'ThemeAdd' => [
                            'title' => __d('baser', '新規追加'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'add']
                        ],
                        'ThemesDownload' => [
                            'title' => __d('baser', '利用中テーマダウンロード'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'download']
                        ],
                        'ThemesDownloadDefaultDataPattern' => [
                            'title' => __d('baser', 'テーマ用初期データダウンロード'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'download_default_data_pattern']
                        ],
                    ]
                ],
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
                'Utilities' => [
                    'title' => __d('baser', 'ユーティリティ'),
                    'type' => 'system',
                    'menus' => [
                        'Utilities' => [
                            'title' => __d('baser', 'ユーティリティトップ'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'index']
                        ],
                        'SiteConfigsInfo' => [
                            'title' => __d('baser', '環境情報'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'info']
                        ],
                        'UtilitiesMaintenance' => [
                            'title' => __d('baser', 'データメンテナンス'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'maintenance']
                        ],
                        'UtilitiesLog' => [
                            'title' => __d('baser', 'ログメンテナンス'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'log_maintenance']
                        ],
//                        'UtilitiesWriteSchema' => ['title' => __d('baser', 'スキーマファイル生成'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'write_schema']],
//                        'UtilitiesLoadSchema' => ['title' => __d('baser', 'スキーマファイル読込'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'load_schema']],
                    ]
                ]
            ]
        ]
    ],

    /**
     * リクエスト情報
     */
    'BcRequest' => [
        // アセットファイルかどうか
        'asset' => false,
        // Router がロード済かどうか
        // TODO 不要か確認
        'routerLoaded' => false,
        // アップデーターかどうか
        'isUpdater' => false,
        // メンテナンスかどうか
        'isMaintenance' => false,
    ],

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
            'alias' => '/' . $adminPrefix,
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
    'BcLinks' => [
        'marketThemeRss' => 'https://market.basercms.net/themes.php',
        'marketPluginRss' => 'https://market.basercms.net/plugins.php',
        'specialThanks' => 'https://basercms.net/special_thanks/special_thanks/ajax_users',
        // インストールマニュアル
        // TODO ucmitz リンク先を準備した上で変更要
        'installManual' => 'https://wiki.basercms.net/%E3%82%A4%E3%83%B3%E3%82%B9%E3%83%88%E3%83%BC%E3%83%AB%E3%82%AC%E3%82%A4%E3%83%89',
        // アップデートマニュアル
        // TODO ucmitz リンク先を準備した上で変更要
        'updateManual' => 'https://wiki.basercms.net/%E3%83%90%E3%83%BC%E3%82%B8%E3%83%A7%E3%83%B3%E3%82%A2%E3%83%83%E3%83%97%E3%82%AC%E3%82%A4%E3%83%89'
    ],

    /**
     * エージェント設定
     */
    'BcAgent' => [
        'mobile' => [
            'name' => __d('baser', 'ケータイ'),
            'helper' => 'BaserCore.BcMobile',
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
            'helper' => 'BaserCore.BcSmartphone',
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
            'BaserCore' => [
                'Default' => [
                    'title' => __d('baser', '無所属コンテンツ'),
                    'omitViewAction' => true,
                    'routes' => [
                        'add' => [
                            'prefix' => 'Admin',
                            'controller' => 'Contents',
                            'action' => 'add'
                        ],
                        'edit' => [
                            'prefix' => 'Admin',
                            'controller' => 'Contents',
                            'action' => 'edit'
                        ],
                        'view' => [
                            'controller' => 'Contents',
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
                            'prefix' => 'Api',
                            'controller' => 'ContentFolders',
                            'action' => 'add'
                        ],
                        'edit' => [
                            'prefix' => 'Admin',
                            'controller' => 'ContentFolders',
                            'action' => 'edit'
                        ],
                        'view' => [
                            'controller' => 'ContentFolders',
                            'action' => 'view'
                        ]
                    ],
                    'icon' => 'bca-icon--folder',
                ],
                'Page' => [
                    'title' => __d('baser', '固定ページ'),
                    'multiple' => true,
                    'preview' => true,
                    'icon' => 'bca-icon--file',
                    'omitViewAction' => true,
                    'routes' => [
                        'add' => [
                            'prefix' => 'Api',
                            'controller' => 'Pages',
                            'action' => 'add'
                        ],
                        'edit' => [
                            'prefix' => 'Admin',
                            'controller' => 'Pages',
                            'action' => 'edit'
                        ],
                        'view' => [
                            'controller' => 'Pages',
                            'action' => 'view'
                        ],
                        'copy' => [
                            'prefix' => 'Api',
                            'controller' => 'Pages',
                            'action' => 'copy'
                        ]
                    ]
                ],
                'ContentAlias' => [
                    'multiple' => true,
                    'title' => __d('baser', 'エイリアス'),
                    'icon' => 'bca-icon--alias',
                    'routes' => [
                        'add' => [
                            'prefix' => 'Api',
                            'controller' => 'Contents',
                            'action' => 'add_alias'
                        ],
                        'edit' => [
                            'prefix' => 'Admin',
                            'controller' => 'Contents',
                            'action' => 'edit_alias'
                        ]
                    ]
                ]
            ]
        ]
    ]
];
