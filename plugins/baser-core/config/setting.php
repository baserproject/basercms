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

    /**
     * アプリ基本設定
     */
    'App' => [
        /**
         * アップロードファイルをオブジェクトとして取り扱うかどうか
         */
        'uploadedFilesAsObjects' => false,
    ],

    /**
     * データベース接続情報
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
     * エラー構成
     */
    'Error' => [
        'errorLevel' => E_ALL,
        'exceptionRenderer' => BcExceptionRenderer::class,
    ],

    /*
     * キャッシュアダプター
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
     * ロギングオプション
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
     * セッション設定
     */
    'Session' => [
        'defaults' => 'cake',
        'cookie' => 'BASERCMS',
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

    /**
     * 環境設定
     */
    'BcEnv' => [
        /**
         * サイトURL
         */
        'siteUrl' => env('SITE_URL', 'https://localhost/'),
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
        'host' => (isset($_SERVER['HTTP_HOST']))? $_SERVER['HTTP_HOST'] : null,
        /**
         * インストール済かどうか
         *
         * BaserCorePlugin::bootstrap() で設定する
         * bootstrap の方が呼び出し順が早いため、こちらで設定すると再初期化となってしまうため
         * コメントアウトのままとする
         * ここで別途判定を入れた場合ユニットテストがやりにくくなるのでそのままにしておく
         */
        // 'isInstalled' => null,
    ],

    /**
     * baserCMS基本設定
     */
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
        'coreFrontTheme' => 'bc-front',

        /**
         * デフォルト管理画面テーマ
         */
        'coreAdminTheme' => 'bc-admin-third',

        /**
         * デフォルトフロントテーマ
         */
        'defaultFrontTheme' => 'BcThemeSample',

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
            'BcUploader',
        ],

        /**
         * コアのリリース情報を取得するためのURL
         */
        'coreReleaseUrl' => 'https://packagist.org/feeds/package.baserproject/baser-core.rss',

        /**
         * インストール時に composer.json にセットするバージョン
         * @see \BaserCore\Utility\BcComposer::setupComposerForDistribution()
         */
        'setupVersion' => '5.1.*',

        /**
         * リリースパッケージに不要なファイル
         * @see \BaserCore\Command\CreateReleaseCommand::deleteExcludeFiles()
         */
        'excludeReleasePackage' => [
            '.git',
            '.github',
            '__assets',
            'tests',
            '.editorconfig',
            '.gitattributes',
            '.gitignore',
            'monorepo-builder.php',
            'phpdoc.dist.xml'.
            'phpstan.neon',
            'phpunit.xml.dist',
            'phpdoc.dist.xml'
        ],

        /**
         * 開発レポジトリのURL
         * 配布用パッケージ作成に利用する
         * @see \BaserCore\Command\CreateReleaseCommand::clonePackage()
         */
        'repositoryUrl' => 'https://github.com/baserproject/basercms.git',

        /**
         * パスワード再発行URLの有効時間(min) デフォルト24時間
         */
        'passwordRequestAllowTime' => 1440,

        /**
         * 二段階認証コードの有効時間(min)
         */
        'twoFactorAuthenticationCodeAllowTime' => 10,

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
        'reservedWords' => ["accessible", "add", "all", "alter", "analyze", "and", "array", "as", "asc", "asensitive",
            "before", "between", "bigint", "binary", "blob", "both", "by", "call", "cascade", "case", "change", "char",
            "character", "check", "collate", "column", "condition", "constraint", "continue", "convert", "create",
            "cross", "cube", "cume_dist", "current_date", "current_time", "current_timestamp", "current_user",
            "cursor", "database", "databases", "day_hour", "day_microsecond", "day_minute", "day_second",
            "dec", "decimal", "declare", "default", "delayed", "delete", "dense_rank", "desc", "describe",
            "deterministic", "distinct", "distinctrow", "div", "double", "drop", "dual", "each", "else",
            "elseif", "empty", "enclosed", "escaped", "except", "exists", "exit", "explain", "false", "fetch",
            "first_value", "float", "float4", "float8", "for", "force", "foreign", "from", "fulltext", "function",
            "generated", "get", "grant", "group", "grouping", "groups", "having", "high_priority", "hour_microsecond",
            "hour_minute", "hour_second", "if", "ignore", "in", "index", "infilex", "inner", "inout", "insensitive",
            "insert", "int", "int1", "int2", "int3", "int4", "int8", "integer", "interval", "into", "io_after_gtids",
            "io_before_gtids", "is", "iterate", "join", "json_table", "key", "keys", "kill", "lag", "last_value",
            "lateral", "lead", "leading", "leave", "left", "like", "limit", "linear", "lines", "load", "localtime",
            "localtimestamp", "lock", "long", "longblob", "longtext", "loop", "low_priority", "master", "master_bind",
            "master_ssl_verify_server_cert", "match", "maxvalue", "mediumblob", "mediumint", "mediumtext", "member",
            "middleint", "minute_microsecond", "minute_second", "mod", "modifies", "natural", "not",
            "no_write_to_binlog", "nth_value", "ntile", "null", "numeric", "of", "on", "optimize", "optimizer_costs",
            "option", "optionally", "or", "order", "out", "outer", "outfile", "over", "partition", "percent_rank",
            "precision", "primary", "procedure", "purge", "range", "rank", "read", "reads", "read_write", "real",
            "recursive", "references", "regexp", "release", "rename", "repeat", "replace", "require", "resignal",
            "restrict", "return", "revoke", "right", "rlike", "row", "rows", "row_number", "schema", "schemas",
            "second_microsecond", "select", "sensitive", "separator", "set", "show", "signal", "smallint", "spatial",
            "specific", "sql", "sqlexception", "sqlstate", "sqlwarning", "sql_big_result", "sql_calc_found_rows",
            "sql_small_result", "ssl", "starting", "stored", "straight_join", "system", "table", "terminated", "then",
            "tinyblob", "tinyint", "tinytext", "to", "trailing", "trigger", "true", "undo", "union", "unique",
            "unlock", "unsigned", "update", "usage", "use", "using", "utc_date", "utc_time", "utc_timestamp", "values",
            "varbinary", "varchar", "varcharacter", "varying", "virtual", "when", "where", "while", "window", "with",
            "write", "xor", "year_month", "zerofill"
        ],

        /**
         * システムメッセージの言語につてサイト設定を利用する
         *  - false：ブラウザ
         *  - true：サイト設定
         */
        'systemMessageLangFromSiteSetting' => true,

        /**
         * POST送信において CSRF をスキップするURL
         */
        'skipCsrfUrl' => [
            ['plugin' => 'BaserCore', 'controller' => 'Users', 'action' => 'login', '_ext' => 'json'],
            ['plugin' => 'BaserCore', 'controller' => 'Users', 'action' => 'refresh_token', '_ext' => 'json'],
        ],

        /**
         * generator のメタタグを出力するかどうか
         */
        'outputMetaGenerator' => true,

        /**
         * オートプレフィックス除外設定（絶対URL）
         *
         * 「すべてのリンクをサブサイト用に変換する」指定時、全てのリンクに対してプレフィックスを備える箇所に除外指定できる
         * 指定した絶対URLを記載しているリンクは変換しない
         * 例: 'https://basercms.net/'と記載 → https://basercms.net/s/ は s が付かなくなる
         */
        'excludeAbsoluteUrlAddPrefix' => [],

        /**
         * オートプレフィックス除外設定（ディレクトリ）
         *
         * 指定したディレクトリURLを記載しているリンクは変換しない
         * 例: 'test/' と記載 → https://basercms.net/s/test/ は s が付かなくなる
         */
        'excludeListAddPrefix' => [],

        /**
         * /config/routes.php を有効化するかどうか
         */
        'enableRootRoutes' => false,

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
                    ]
                ]
            ]
        ],

        /*
         * パスワードの設定ルール
         */
        'passwordRule' => [
            // 最小文字数
            'minLength' => 12,
            // 入力必須な文字種
            'requiredCharacterTypes' => [
                // 数字
                'numeric',
                // 大文字英字
                'uppercase',
                // 小文字英字
                'lowercase',
                // 記号
                // 'symbol',
            ],
        ],
    ],

    /**
     * アクセスルール
     */
    'BcPermission' => [
        'defaultAllows' => [
            '/baser/admin',
            '/baser/admin/baser-core/users/login',
            '/baser/admin/baser-core/users/login_code',
            '/baser/admin/baser-core/users/logout',
            '/baser/admin/baser-core/password_requests/*',
            '/baser/admin/baser-core/dashboard/*',
            '/baser/admin/baser-core/dblogs/*',
            '/baser/admin/baser-core/users/back_agent',
            '/baser/admin/baser-core/users/edit_password',
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
        'installManual' => 'https://baserproject.github.io/5/introduce/',
        // アップデートマニュアル
        'updateManual' => 'https://baserproject.github.io/5/operation/update'
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
     * ショートコード
     */
    'BcShortCode' => [
        'BaserCore' => [
            'BcBaser.getGoogleMaps',
            'BcBaser.getSitemap',
            'BcBaser.getRelatedSiteLinks',
            'BcBaser.getWidgetArea',
            'BcBaser.getSiteSearchForm',
            'BcBaser.getUpdateInfo'
        ]
    ],

    /**
     * コンテンツ設定
     */
    'BcContents' => [

        /**
         * コンテンツの作成日を自動で更新する
         */
        'autoUpdateContentCreatedDate' => true,

        /**
         * preview及びforce指定時に管理画面へログインしていない状況下での挙動判別
         *
         *  - true：ログイン画面へリダイレクト
         *  - false：ログイン画面へリダイレクトしない
         * @see \BaserCore\Routing\Route\BcContentsRoute
         */
        'previewRedirect' => true,

        /**
         * 利用するコンテンツ
         */
        'items' => [
            'BaserCore' => [
                'Default' => [
                    'title' => __d('baser_core', '無所属コンテンツ'),
                    'omitViewAction' => true,
                    'routes' => [
                        'add' => [
                            'plugin' => 'BaserCore',
                            'prefix' => 'Admin',
                            'controller' => 'Contents',
                            'action' => 'add'
                        ],
                        'edit' => [
                            'plugin' => 'BaserCore',
                            'prefix' => 'Admin',
                            'controller' => 'Contents',
                            'action' => 'edit'
                        ],
                        'view' => [
                            'plugin' => 'BaserCore',
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
                            'plugin' => 'BaserCore',
                            'prefix' => 'Api/Admin',
                            'controller' => 'ContentFolders',
                            'action' => 'add',
                            '_ext' => 'json'
                        ],
                        'edit' => [
                            'plugin' => 'BaserCore',
                            'prefix' => 'Admin',
                            'controller' => 'ContentFolders',
                            'action' => 'edit'
                        ],
                        'view' => [
                            'plugin' => 'BaserCore',
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
                            'plugin' => 'BaserCore',
                            'prefix' => 'Api/Admin',
                            'controller' => 'Pages',
                            'action' => 'add',
                            '_ext' => 'json'
                        ],
                        'edit' => [
                            'plugin' => 'BaserCore',
                            'prefix' => 'Admin',
                            'controller' => 'Pages',
                            'action' => 'edit'
                        ],
                        'view' => [
                            'plugin' => 'BaserCore',
                            'controller' => 'Pages',
                            'action' => 'view'
                        ],
                        'copy' => [
                            'plugin' => 'BaserCore',
                            'prefix' => 'Api/Admin',
                            'controller' => 'Pages',
                            'action' => 'copy',
                            '_ext' => 'json'
                        ]
                    ]
                ],
                'ContentAlias' => [
                    'multiple' => true,
                    'title' => __d('baser_core', 'エイリアス'),
                    'icon' => 'bca-icon--alias',
                    'routes' => [
                        'add' => [
                            'plugin' => 'BaserCore',
                            'prefix' => 'Api/Admin',
                            'controller' => 'Contents',
                            'action' => 'add_alias',
                            '_ext' => 'json'
                        ],
                        'edit' => [
                            'plugin' => 'BaserCore',
                            'prefix' => 'Admin',
                            'controller' => 'Contents',
                            'action' => 'edit_alias'
                        ]
                    ]
                ]
            ]
        ]
    ],
];
