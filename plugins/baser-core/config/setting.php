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
use BaserCore\Error\BcExceptionRenderer;
use Cake\Cache\Engine\FileEngine;
use Cake\Log\Engine\FileLog;

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

    /*
     * Configure basic information about the application.
     */
    'App' => [
        /**
         * アップロードファイルをオブジェクトとして取り扱うかどうか
         */
        'uploadedFilesAsObjects' => false,
    ],

    /*
     * Connection information used by the ORM to connect
     * to your application's datastores.
     */
    'Datasources' => [
        /*
         * These configurations should contain permanent settings used
         * by all environments.
         */
        'default' => [
            'log' => filter_var(env('SQL_LOG', false), FILTER_VALIDATE_BOOLEAN),
            'timezone' => 'Asia/Tokyo',
        ],
        /*
         * The test connection is used during the test suite.
         */
        'test' => [
            'timezone' => 'Asia/Tokyo',
        ],
    ],

    /*
     * Configure the Error and Exception handlers used by your application.
     */
    'Error' => [
        'errorLevel' => E_ALL & ~E_USER_DEPRECATED,
        'exceptionRenderer' => BcExceptionRenderer::class,
    ],

    /*
     * Configure the cache adapters.
     */
    'Cache' => [
        /**
         * 環境情報に利用
         */
        '_bc_env_' => [
            'className' => FileEngine::class,
            'prefix' => 'myapp_bc_env_',
            'path' => CACHE . 'environment' . DS,
            'serialize' => true,
            'duration' => '+1 years',
            'url' => env('CACHE_BCENV_URL', null),
        ],
        /**
         * Packagist の BaserCore のバージョン、baserマーケットのテーマ、プラグイン情報、baserオフィシャルニュースに利用
         * @see \BaserCore\Service\PluginsService::getAvailableCoreVersion()
         * @see \BaserCore\Service\BcOfficialApiService::getRss()
         */
        '_bc_update_' => [
            'className' => FileEngine::class,
            'prefix' => 'myapp_bc_update_',
            'path' => CACHE . 'environment' . DS,
            'serialize' => true,
            'duration' => '+1 days',
        ],
        /**
         * Google Mapsの ロケーション情報に利用
         * @see \BaserCore\Utility\BcGmaps::getLocation()
         */
        '_bc_gmaps_' => [
            'className' => FileEngine::class,
            'prefix' => 'myapp_bc_gmaps_',
            'path' => CACHE . 'environment' . DS,
            'serialize' => true,
            'duration' => '+1 months',
        ],
    ],

    /*
     * Configures logging options
     */
    'Log' => [
        'update' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'update',
            'scopes' => ['update'],
            'levels' => ['info', 'error']
        ]
    ],

    /*
     * Session configuration.
     *
     * Contains an array of settings to use for session configuration. The
     * `defaults` key is used to define a default preset to use for sessions, any
     * settings declared here will override the settings of the default config.
     *
     * ## Options
     *
     * - `cookie` - The name of the cookie to use. Defaults to value set for `session.name` php.ini config.
     *    Avoid using `.` in cookie names, as PHP will drop sessions from cookies with `.` in the name.
     * - `cookiePath` - The url path for which session cookie is set. Maps to the
     *   `session.cookie_path` php.ini config. Defaults to base path of app.
     * - `timeout` - The time in minutes the session should be valid for.
     *    Pass 0 to disable checking timeout.
     *    Please note that php.ini's session.gc_maxlifetime must be equal to or greater
     *    than the largest Session['timeout'] in all served websites for it to have the
     *    desired effect.
     * - `defaults` - The default configuration set to use as a basis for your session.
     *    There are four built-in options: php, cake, cache, database.
     * - `handler` - Can be used to enable a custom session handler. Expects an
     *    array with at least the `engine` key, being the name of the Session engine
     *    class to use for managing the session. CakePHP bundles the `CacheSession`
     *    and `DatabaseSession` engines.
     * - `ini` - An associative array of additional ini values to set.
     *
     * The built-in `defaults` options are:
     *
     * - 'php' - Uses settings defined in your php.ini.
     * - 'cake' - Saves session files in CakePHP's /tmp directory.
     * - 'database' - Uses CakePHP's database sessions.
     * - 'cache' - Use the Cache class to save sessions.
     *
     * To define a custom session handler, save it at src/Network/Session/<name>.php.
     * Make sure the class implements PHP's `SessionHandlerInterface` and set
     * Session.handler to <name>
     *
     * To use database sessions, load the SQL file located at config/schema/sessions.sql
     */
    'Session' => [
        'defaults' => 'cake',
        'cookie' => 'BASERCMS5',
        /**
         * セッションの有効期限（分）
         * デフォルト：2日間
         */
        'timeout' => 60 * 24 * 2,
        'ini' => [
            'session.serialize_handler' => 'php',
            'session.save_path' => TMP . 'sessions',
            'session.use_cookies' => 1,
            'session.use_trans_sid' => 0,
            'session.gc_divisor' => 100,
            'session.gc_probability' => 1,
            /**
             * クッキーの有効期限（秒）
             * デフォルト：1年間
             */
            'session.cookie_lifetime' => 60 * 60 * 24 * 365
        ]
    ],

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

        /**
         * デフォルトタイトル設定（インストールの際のエラー時等、DB接続前のエラーで利用）
         */
        'title' => __d('baser_core', 'baserCMS'),

        /**
         * テンプレートの基本となる拡張子（.php 推奨）
         */
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
         * Web API のプレフィックス
         * baserコアのプレフィックスの後に付与
         */
        'apiPrefix' => 'api',

        /**
         * 管理者グループID
         */
        'adminGroupId' => 1,

        /**
         * スーパーユーザーID
         */
        'superUserId' => 1,

        /**
         * お名前ドットコムの場合、CLI版PHPの存在確認の段階で固まってしまう
         */
        'validSyntaxWithPage' => true,

        /**
         * 管理者以外のPHPコードを許可するかどうか
         */
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
            'BcBlog',
            'BcContentLink',
            'BcCustomContent',
            'BcEditorTemplate',
            'BcFavorite',
            'BcMail',
            'BcSearchIndex',
            'BcThemeConfig',
            'BcThemeFile',
            'BcUploader',
            'BcWidgetArea',
        ],
        'defaultInstallCorePlugins' => [
            'BcSearchIndex',
            'BcBlog',
            'BcMail',
            'BcThemeConfig',
            'BcWidgetArea',
        ],

        /**
         * コアのリリース情報を取得するためのURL
         */
        'coreReleaseUrl' => 'https://packagist.org/feeds/package.baserproject/baser-core.rss',

        /**
         * インストール時に composer.json にセットするバージョン
         * @see \BaserCore\Command\CreateReleaseCommand::setupComposer()
         */
        'setupVersion' => '5.*.*',

        /**
         * リリースパッケージに不要なファイル
         * @see \BaserCore\Command\CreateReleaseCommand::deleteExcludeFiles()
         */
        'excludeReleasePackage' => [
            '.git',
            '.github',
            '__assets',
            'docker',
            'tests',
            '.editorconfig',
            '.gitattributes',
            '.gitignore',
            'monorepo-builder.php',
            'phpstan.neon',
            'phpunit.xml.dist'
        ],

        /**
         * 開発レポジトリのURL
         * 配布用パッケージ作成に利用する
         * @see \BaserCore\Command\CreateReleaseCommand::clonePackage()
         */
        'repositoryUrl' => 'https://github.com/baserproject/ucmitz.git',

        /**
         * パスワード再発行URLの有効時間(min) デフォルト24時間
         */
        'passwordRequestAllowTime' => 1440,

        /**
         * 管理画面のSSL
         */
        'adminSsl' => filter_var(env('ADMIN_SSL', true), FILTER_VALIDATE_BOOLEAN),

        /**
         * エディタ
         */
        'editors' => [
            'none' => __d('baser_core', 'なし'),
            'BaserCore.BcCkeditor' => 'CKEditor'
        ],

        /**
         * 予約語
         * 主にDBの予約語としてテーブルのフィールドで利用できない名称
         */
        'reservedWords' => ['group', 'rows', 'option'],

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
                    'title' => __d('baser_core', 'ダッシュボード'),
                    'type' => 'dashboard',
                    'url' => '/' . $baserCorePrefix . '/' . $adminPrefix,
                ],
                'Contents' => [
                    'title' => __d('baser_core', 'コンテンツ管理'),
                    'type' => 'contents',
                    'menus' => [
                        'Contents' => ['title' => __d('baser_core', 'コンテンツ'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'contents', 'action' => 'index']],
                        'ContentsTrash' => ['title' => __d('baser_core', 'ゴミ箱'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'contents', 'action' => 'trash_index']],
                    ]
                ],
            ],
            'Systems' => [
                'SiteConfigs' => [
                    'title' => __d('baser_core', 'システム基本設定'),
                    'type' => 'system',
                    'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'site_configs', 'action' => 'index']
                ],
                'Users' => [
                    'title' => __d('baser_core', 'ユーザー管理'),
                    'type' => 'system',
                    'menus' => [
                        'Users' => [
                            'title' => __d('baser_core', 'ユーザー'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'users', 'action' => 'index'],
                            'currentRegex' => '/\/users\/[^\/]+?/s'
                        ],
                        'UserGroups' => [
                            'title' => __d('baser_core', 'ユーザーグループ'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'user_groups', 'action' => 'index'],
                            'currentRegex' => '/\/user_groups\/[^\/]+?/s'
                        ],
                        'PermissionGroups' => [
                            'title' => __d('baser_core', 'アクセスルールグループ'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'permission_groups', 'action' => 'index'],
                            'currentRegex' => '/(\/permission_groups\/[^\/]+?|\/permissions\/[^\/]+?)/s'
                        ],
                    ]
                ],
                'Sites' => [
                    'title' => __d('baser_core', 'サイト管理'),
                    'type' => 'system',
                    'menus' => [
                        'Sites' => [
                            'title' => __d('baser_core', 'サイト'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'sites', 'action' => 'index'],
                            'currentRegex' => '/\/sites\/.+?/s'
                        ],
                    ]
                ],
                'Theme' => [
                    'title' => __d('baser_core', 'テーマ管理'),
                    'type' => 'system',
                    'menus' => [
                        'Themes' => [
                            'title' => __d('baser_core', 'テーマ'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'index'],
                            'currentRegex' => '/\/themes\/[^\/]+?/s'
                        ],
                        'ThemeAdd' => [
                            'title' => __d('baser_core', '新規追加'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'add']
                        ],
                        'ThemesDownload' => [
                            'title' => __d('baser_core', '利用中テーマダウンロード'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'download']
                        ],
                        'ThemesDownloadDefaultDataPattern' => [
                            'title' => __d('baser_core', 'テーマ用初期データダウンロード'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'themes', 'action' => 'download_default_data_pattern']
                        ],
                    ]
                ],
                'Plugin' => [
                    'title' => __d('baser_core', 'プラグイン管理'),
                    'type' => 'system',
                    'menus' => [
                        'Plugins' => [
                            'title' => __d('baser_core', 'プラグイン'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'plugins', 'action' => 'index'],
                            'currentRegex' => '/\/plugins\/[^\/]+?/s'
                        ],
                    ]
                ],
                'Utilities' => [
                    'title' => __d('baser_core', 'ユーティリティ'),
                    'type' => 'system',
                    'menus' => [
                        'Utilities' => [
                            'title' => __d('baser_core', 'ユーティリティトップ'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'index']
                        ],
                        'SiteConfigsInfo' => [
                            'title' => __d('baser_core', '環境情報'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'info']
                        ],
                        'UtilitiesMaintenance' => [
                            'title' => __d('baser_core', 'データメンテナンス'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'maintenance']
                        ],
                        'UtilitiesLog' => [
                            'title' => __d('baser_core', 'ログメンテナンス'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'log_maintenance']
                        ],
//                        'UtilitiesWriteSchema' => ['title' => __d('baser_core', 'スキーマファイル生成'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'write_schema']],
//                        'UtilitiesLoadSchema' => ['title' => __d('baser_core', 'スキーマファイル読込'), 'url' => ['prefix' => 'Admin', 'plugin' => 'BaserCore', 'controller' => 'utilities', 'action' => 'load_schema']],
                    ]
                ]
            ]
        ]
    ],

    /**
     * アクセスルール
     */
    'BcPermission' => [
        'defaultAllows' => [
            '/baser/admin',
            '/baser/admin/baser-core/users/login',
            '/baser/admin/baser-core/users/logout',
            '/baser/admin/baser-core/password_requests/*',
            '/baser/admin/baser-core/dashboard/*',
            '/baser/admin/baser-core/dblogs/*',
            '/baser/admin/baser-core/users/back_agent',
            '/baser/admin/baser-core/preview/*',
            '/baser/admin/baser-core/utilities/credit',
            '/',
            '/baser-core/users/login',
            '/baser-core/users/logout',
            '/baser-core/password_requests/*',
            '/baser/api/admin/baser-core/users/login.json',
            '/baser/api/admin/baser-core/users/refresh_token.json'
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
     *
     * プレフィックスに紐付ける認証設定を定義する
     *
     * - `name`: 認証設定名
     * - `type`: 認証タイプ（ Session | Jwt ）
     *      セッション認証、または、 JWT 認証を提供。
     *      どちらにおいても、テーブルにおける ユーザー名とパスワードで識別する。
     * - `alias`: URLにおけるエイリアス
     * - `loginRedirect`: 認証後のリダイレクト先のURL
     * - `loginAction`: ログインページURL
     * - `logoutAction`: ログアウトページURL
     * - `username`: ユーザー識別用テーブルにおけるユーザー名。配列での複数指定が可能
     * - `password`: ユーザー識別用テーブルにおけるパスワード
     * - `userModel`: ユーザー識別用のテーブル（プラグイン記法）
     * - `sessionKey`: セッションを利用する場合のセッションキー
     * - `permissionType`: アクセスルール設定
     *      - 1.ホワイトリスト: 全て拒否してアクセスルールで許可を設定
     *      - 2.ブラックリスト: 全て許可してアクセスルールで拒否を設定
     * - `disabled`: 設定を無効にする場合は true に設定（キーがない場合は有効とみなす）
     * - `withCorePrefix`: プレフィックスの前に baserのコアプレフィックスを追加するかどうか
     * - `isRestApi`: REST API かどうか
     */
    'BcPrefixAuth' => [
        // 管理画面
        'Admin' => [
            'name' => __d('baser_core', '管理システム'),
            'type' => 'Session',
            'alias' => '/' . $adminPrefix,
            'loginRedirect' => ['plugin' => 'BaserCore', 'prefix' => 'Admin', 'controller' => 'Dashboard', 'action' => 'index'],
            'loginAction' => ['plugin' => 'BaserCore', 'prefix' => 'Admin', 'controller' => 'Users', 'action' => 'login'],
            'logoutAction' => ['plugin' => 'BaserCore', 'prefix' => 'Admin', 'controller' => 'Users', 'action' => 'logout'],
            'username' => ['email', 'name'],
            'password' => 'password',
            'userModel' => 'BaserCore.Users',
            'permissionType' => 1,
            'sessionKey' => 'AuthAdmin',
            'withCorePrefix' => true
        ],
        // Api
        'Api' => [
            'name' => __d('baser_core', 'Web API'),
            'alias' => '/api',
            'withCorePrefix' => true,
            'isRestApi' => true
        ],
        // Api/Admin
        'Api/Admin' => [
            'name' => __d('baser_core', 'Admin Web API'),
            'type' => 'Jwt',
            'alias' => '/api/admin',
            'username' => ['email', 'name'],
            'password' => 'password',
            'userModel' => 'BaserCore.Users',
            'permissionType' => 1,
            'sessionKey' => 'AuthAdmin',
            'withCorePrefix' => true,
            'isRestApi' => true
        ],
        // フロントページ
        'Front' => [
            'name' => 'フロントページ',
            'type' => 'Session',
            'alias' => '/',
            'loginRedirect' => '/',
            'loginAction' => ['plugin' => 'BaserCore', 'controller' => 'Users', 'action' => 'login'],
            'logoutAction' => ['plugin' => 'BaserCore', 'controller' => 'Users', 'action' => 'logout'],
            'username' => ['email', 'name'],
            'password' => 'password',
            'userModel' => 'BaserCore.Users',
            'sessionKey' => 'AuthAdmin',
            'permissionType' => 2,
            'disabled' => true
        ]
    ],

    /**
     * Jwt認証設定
     */
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

    /**
     * パッケージ内の外部リンク
     */
    'BcLinks' => [
        // baserマーケット テーマRSS
        'marketThemeRss' => 'https://market.basercms.net/themes.php',
        // baserマーケット プラグインRSS
        'marketPluginRss' => 'https://market.basercms.net/plugins.php',
        // スペシャルサンクス
        'specialThanks' => 'https://basercms.net/special_thanks/special_thanks/ajax_users',
        // baserCMSオフィシャルニュース
        'baserNewsRss' => 'https://basercms.net/news/index.rss',
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
            'name' => __d('baser_core', 'ケータイ'),
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
            'name' => __d('baser_core', 'スマートフォン'),
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
            'name' => __d('baser_core', '英語'),
            'langs' => [
                'en'
            ]
        ],
        'chinese' => [
            'name' => __d('baser_core', '中国語'),
            'langs' => [
                'zh'
            ]
        ],
        'spanish' => [
            'name' => __d('baser_core', 'スペイン'),
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
        'detectOrder' => ['UTF-8', 'ASCII', 'JIS', 'SJIS-win', 'EUC-JP'],
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
                    'title' => __d('baser_core', '無所属コンテンツ'),
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
                    'title' => __d('baser_core', 'フォルダー'),
                    'routes' => [
                        'add' => [
                            'prefix' => 'Api/Admin',
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
                    'title' => __d('baser_core', '固定ページ'),
                    'multiple' => true,
                    'preview' => true,
                    'icon' => 'bca-icon--file',
                    'omitViewAction' => true,
                    'routes' => [
                        'add' => [
                            'prefix' => 'Api/Admin',
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
                            'prefix' => 'Api/Admin',
                            'controller' => 'Pages',
                            'action' => 'copy'
                        ]
                    ]
                ],
                'ContentAlias' => [
                    'multiple' => true,
                    'title' => __d('baser_core', 'エイリアス'),
                    'icon' => 'bca-icon--alias',
                    'routes' => [
                        'add' => [
                            'prefix' => 'Api/Admin',
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
    ],
    /**
     * ショートコード
     */
    'BcShortCode' => [
        'BaserCore' => [
            'BcBaser.getGoogleMaps'
        ]
    ]
];
