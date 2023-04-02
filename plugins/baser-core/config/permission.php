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

/**
 * アクセスルール初期値
 */
return [
    'permission' => [

        /**
         * 管理画面
         */
        'ContentFoldersAdmin' => [
            'title' => __d('baser_core', 'コンテンツフォルダ管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/baser-core/content_folders/edit/*', 'method' => 'POST', 'auth' => false]
            ]
        ],

        'ContentsAdmin' => [
            'title' => __d('baser_core', 'コンテンツ管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/baser-core/contents/index', 'method' => '*', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', 'ゴミ箱'), 'url' => '/baser/admin/baser-core/contents/trash_index', 'method' => 'GET', 'auth' => true],
                'EditAlias' => ['title' => __d('baser_core', 'エイリアス編集'), 'url' => '/baser/admin/baser-core/contents/edit_alias/*', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', 'ゴミ箱から戻す'), 'method' => 'POST', 'url' => '/baser/admin/baser-core/contents/trash_return/*', 'auth' => true],
            ]
        ],

        'PagesAdmin' => [
            'title' => __d('baser_core', '固定ページ管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/baser-core/pages/edit/*', 'method' => 'POST', 'auth' => true]
            ]
        ],

        'PermissionGroupsAdmin' => [
            'title' => __d('baser_core', 'アクセスルールグループ管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/baser-core/permission_groups/index/*', 'method' => '*', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/baser-core/permission_groups/edit/*', 'method' => 'POST', 'auth' => false],
                'RebuildByUserGroup' => ['title' => __d('baser_core', 'ルール再構築'), 'url' => '/baser/admin/baser-core/permission_groups/rebuild_by_user_group/*', 'method' => 'POST', 'auth' => false]
            ]
        ],

        'PermissionsAdmin' => [
            'title' => __d('baser_core', 'アクセスルール管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/baser-core/permissions/index/*', 'method' => '*', 'auth' => false],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/admin/baser-core/permissions/add/*', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/baser-core/permissions/edit/*', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/baser-core/permissions/delete/*', 'method' => 'POST', 'auth' => false],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/admin/baser-core/permissions/copy/*', 'method' => 'POST', 'auth' => false],
                'Publish' => ['title' => __d('baser_core', '公開設定'), 'url' => '/baser/admin/baser-core/permissions/publish/*', 'method' => 'POST', 'auth' => false],
                'Unpublish' => ['title' => __d('baser_core', '非公開設定'), 'url' => '/baser/admin/baser-core/permissions/unpublish/*', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'PluginsAdmin' => [
            'title' => __d('baser_core', 'プラグイン管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/baser-core/plugins/index/*', 'method' => '*', 'auth' => false],
                'Add' => ['title' => __d('baser_core', '登録'), 'url' => '/baser/admin/baser-core/plugins/add', 'method' => 'POST', 'auth' => false],
                'GetMarketPlugins' => ['title' => __d('baser_core', 'マーケットのプラグインを取得'), 'url' => '/baser/admin/baser-core/plugins/get_market_plugins', 'method' => 'GET', 'auth' => false],
                'Detach' => ['title' => __d('baser_core', '無効化'), 'url' => '/baser/admin/baser-core/plugins/detach/*', 'method' => 'POST', 'auth' => false],
                'Install' => ['title' => __d('baser_core', 'インストール'), 'url' => '/baser/admin/baser-core/plugins/install/*', 'method' => 'POST', 'auth' => false],
                'ResetDb' => ['title' => __d('baser_core', 'データベース初期化'), 'url' => '/baser/admin/baser-core/plugins/reset_db', 'method' => 'POST', 'auth' => false],
                'Uninstall' => ['title' => __d('baser_core', 'アンインストール'), 'url' => '/baser/admin/baser-core/plugins/uninstall/*', 'method' => 'POST', 'auth' => false],
                'Update' => ['title' => __d('baser_core', 'アップデート'), 'url' => '/baser/admin/baser-core/plugins/update/*', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'SiteConfigsAdmin' => [
            'title' => __d('baser_core', 'システム基本設定'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/baser-core/site_configs/index', 'method' => 'POST', 'auth' => false]
            ]
        ],

        'SitesAdmin' => [
            'title' => __d('baser_core', 'サイト管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/baser-core/sites/index', 'method' => '*', 'auth' => false],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/admin/baser-core/sites/add', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/baser-core/sites/edit/*', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/baser-core/sites/delete/*', 'method' => 'POST', 'auth' => false],
                'Publish' => ['title' => __d('baser_core', '公開設定'), 'url' => '/baser/admin/baser-core/sites/publish/*', 'method' => 'POST', 'auth' => false],
                'Unpublish' => ['title' => __d('baser_core', '非公開設定'), 'url' => '/baser/admin/baser-core/sites/unpublish/*', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'ThemesAdmin' => [
            'title' => __d('baser_core', 'テーマ管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/baser-core/themes/index', 'method' => '*', 'auth' => false],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/admin/baser-core/themes/add', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/baser-core/themes/delete/*', 'method' => 'POST', 'auth' => false],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/admin/baser-core/themes/copy/*', 'method' => 'POST', 'auth' => false],
                'Apply' => ['title' => __d('baser_core', '適用'), 'url' => '/baser/admin/baser-core/themes/apply/*', 'method' => 'POST', 'auth' => false],
                'download' => ['title' => __d('baser_core', 'ダウンロード'), 'url' => '/baser/admin/baser-core/themes/download/*', 'method' => 'GET', 'auth' => false],
                'download_default_data_pattern' => ['title' => __d('baser_core', '初期データダウンロード'), 'url' => '/baser/admin/baser-core/themes/download_default_data_pattern', 'method' => 'GET', 'auth' => false],
                'get_market_themes' => ['title' => __d('baser_core', 'マーケットのテーマ取得'), 'url' => '/baser/admin/baser-core/themes/get_market_themes', 'method' => 'GET', 'auth' => false],
                'load_default_data_pattern' => ['title' => __d('baser_core', '初期データ読み込み'), 'url' => '/baser/admin/baser-core/themes/load_default_data_pattern', 'method' => 'POST', 'auth' => false],
                'screenshot' => ['title' => __d('baser_core', 'スクリーンショット'), 'url' => '/baser/admin/baser-core/themes/screenshot/*', 'method' => 'GET', 'auth' => false],
            ]
        ],

        'UserGroupsAdmin' => [
            'title' => __d('baser_core', 'ユーザーグループ管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/baser-core/user_groups/index', 'method' => '*', 'auth' => false],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/admin/baser-core/user_groups/add', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/baser-core/user_groups/edit/*', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/baser-core/user_groups/delete/*', 'method' => 'POST', 'auth' => false],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/admin/baser-core/user_groups/copy/*', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'UsersAdmin' => [
            'title' => __d('baser_core', 'ユーザー管理'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/baser-core/users/index', 'method' => '*', 'auth' => false],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/admin/baser-core/users/add', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/baser-core/users/edit/*', 'method' => 'POST', 'auth' => false],
                'EditSelf' => ['title' => __d('baser_core', '自身の編集'), 'url' => '/baser/admin/baser-core/users/edit/{loginUserId}', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/baser-core/users/delete/*', 'method' => 'POST', 'auth' => false],
                'LoginAgent' => ['title' => __d('baser_core', '代理ログイン'), 'method' => 'POST', 'url' => '/baser/admin/baser-core/users/login_agent/*', 'auth' => false],
            ]
        ],

        'UtilitiesAdmin' => [
            'title' => __d('baser_core', 'ユーティリティ'),
            'plugin' => 'BaserCore',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', 'トップ'), 'url' => '/baser/admin/baser-core/utilities/index', 'method' => 'GET', 'auth' => false],
                'ClearCache' => ['title' => __d('baser_core', 'キャッシュクリア'), 'url' => '/baser/admin/baser-core/utilities/clear_cache', 'method' => 'GET', 'auth' => true],
                'Credit' => ['title' => __d('baser_core', 'クレジット'), 'url' => '/baser/admin/baser-core/utilities/credit', 'method' => 'GET', 'auth' => true],
                'Info' => ['title' => __d('baser_core', '環境情報'), 'url' => '/baser/admin/baser-core/utilities/info', 'method' => 'GET', 'auth' => false],
                'LogMaintenance' => ['title' => __d('baser_core', 'ログメンテナンス'), 'url' => '/baser/admin/baser-core/utilities/log_maintenance/*', 'method' => 'POST', 'auth' => false],
                'Maintenance' => ['title' => __d('baser_core', 'データメンテナンス'), 'url' => '/baser/admin/baser-core/utilities/maintenance/*', 'method' => 'POST', 'auth' => false],
                'Phpinfo' => ['title' => __d('baser_core', 'PHPインフォ'), 'url' => '/baser/admin/baser-core/utilities/phpinfo', 'method' => 'GET', 'auth' => true],
                'ResetContentsTree' => ['title' => __d('baser_core', 'ツリー構造リセット', 'トップ'), 'url' => '/baser/admin/baser-core/utilities/reset_contents_tree', 'method' => 'POST', 'auth' => false],
                'ResetData' => ['title' => __d('baser_core', 'コア初期データ読み込み'), 'url' => '/baser/admin/baser-core/utilities/reset_data', 'method' => 'POST', 'auth' => false],
                'VerityContents_tree' => ['title' => __d('baser_core', 'ツリー構造チェック'), 'url' => '/baser/admin/baser-core/utilities/verity_contents_tree', 'method' => 'POST', 'auth' => false],
            ]
        ],

        /**
         * Web API
         */
        'ContentFoldersApi' => [
            'title' => __d('baser_core', 'コンテンツフォルダーAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/content_folders/index.json', 'method' => '*', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/baser-core/content_folders/view/*.json', 'method' => '*', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/content_folders/add.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/baser-core/content_folders/edit/*.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/baser-core/content_folders/delete/*.json', 'method' => 'POST', 'auth' => false]
            ]
        ],

        'ContentsApi' => [
            'title' => __d('baser_core', 'コンテンツAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/contents/index.json', 'method' => '*', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/baser-core/contents/view/*.json', 'method' => '*', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/contents/add.json', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/baser-core/contents/edit/*.json', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/baser-core/contents/delete.json', 'method' => 'POST', 'auth' => true],
                'AddAlias' => ['title' => __d('baser_core', 'エイリアス登録'), 'url' => '/baser/api/baser-core/contents/add_alias.json', 'method' => 'POST', 'auth' => true],
                'Batch' => ['title' => __d('baser_core', 'バッチ処理'), 'url' => '/baser/api/baser-core/contents/batch.json', 'method' => 'POST', 'auth' => true],
                'ChangeStatus' => ['title' => __d('baser_core', '公開状態変更'), 'url' => '/baser/api/baser-core/contents/change_status.json', 'method' => 'POST', 'auth' => true],
                'Exists' => ['title' => __d('baser_core', '存在確認'), 'url' => '/baser/api/baser-core/contents/exists/*.json', 'method' => 'GET', 'auth' => true],
                'GetContentFolder_list' => ['title' => __d('baser_core', 'フォルダ一覧取得'), 'url' => '/baser/api/baser-core/contents/get_content_folder_list/*.json', 'method' => 'GET', 'auth' => true],
                'GetFullUrl' => ['title' => __d('baser_core', 'フルURL取得'), 'url' => '/baser/api/baser-core/contents/get_full_url/*.json', 'method' => 'GET', 'auth' => true],
                'IsUniqueContent' => ['title' => __d('baser_core', '一意性チェック'), 'url' => '/baser/api/baser-core/contents/is_unique_content.json', 'method' => 'POST', 'auth' => true],
                'Move' => ['title' => __d('baser_core', '移動'), 'url' => '/baser/api/baser-core/contents/move.json', 'method' => 'POST', 'auth' => true],
                'Rename' => ['title' => __d('baser_core', 'リネーム'), 'url' => '/baser/api/baser-core/contents/rename.json', 'method' => 'POST', 'auth' => true],
                'TrashEmpty' => ['title' => __d('baser_core', 'ゴミ箱を空にする'), 'url' => '/baser/api/baser-core/contents/trash_empty.json', 'method' => 'POST', 'auth' => true],
                'TrashReturn' => ['title' => __d('baser_core', 'ゴミ箱から戻す'), 'url' => '/baser/api/baser-core/contents/trash_return/*.json', 'method' => 'GET', 'auth' => true],
                'ViewTrash' => ['title' => __d('baser_core', 'ゴミ箱内一覧'), 'url' => '/baser/api/baser-core/contents/view_trash/*.json', 'method' => '*', 'auth' => true],
            ]
        ],

        'DblogsApi' => [
            'title' => __d('baser_core', '操作ログAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/dblogs/index.json', 'method' => '*', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/dblogs/add.json', 'method' => 'POST', 'auth' => true],
                'DeleteAll' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/baser-core/dblogs/delete_all.json', 'method' => 'POST', 'auth' => false]
            ]
        ],

        'PagesApi' => [
            'title' => __d('baser_core', '固定ページAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/pages/index.json', 'method' => '*', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/baser-core/pages/view/*.json', 'method' => '*', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/pages/add.json', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/baser-core/pages/edit/*.json', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/baser-core/pages/delete/*.json', 'method' => 'POST', 'auth' => true],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/api/baser-core/pages/copy.json', 'method' => 'POST', 'auth' => true]
            ]
        ],

        'PermissionsApi' => [
            'title' => __d('baser_core', 'アクセスルールAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/permissions/index/*.json', 'method' => '*', 'auth' => false],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/baser-core/permissions/view/*.json', 'method' => '*', 'auth' => false],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/permissions/add.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/baser-core/permissions/edit/*.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/baser-core/permissions/delete/*.json', 'method' => 'POST', 'auth' => false],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/api/baser-core/permissions/copy/*.json', 'method' => 'POST', 'auth' => false],
                'Batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/baser-core/permissions/batch.json', 'method' => 'POST', 'auth' => false],
                'UpdateSort' => ['title' => __d('baser_core', '並び順変更'), 'url' => '/baser/api/baser-core/permissions/update_sort/*.json', 'method' => 'POST', 'auth' => false]
            ]
        ],

        'PluginsApi' => [
            'title' => __d('baser_core', 'プラグインAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/plugins/add.json', 'method' => 'POST', 'auth' => false],
                'Attach' => ['title' => __d('baser_core', '有効化'), 'url' => '/baser/api/baser-core/plugins/attach/*.json', 'method' => 'POST', 'auth' => false],
                'Batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/baser-core/plugins/batch.json', 'method' => 'POST', 'auth' => false],
                'Detach' => ['title' => __d('baser_core', '無効化'), 'url' => '/baser/api/baser-core/plugins/detach/*.json', 'method' => 'POST', 'auth' => false],
                'GetMarketPlugins' => ['title' => __d('baser_core', 'マーケットのプラグインを取得'), 'url' => '/baser/api/baser-core/plugins/get_market_plugins.json', 'method' => 'GET', 'auth' => false],
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/plugins/index.json', 'method' => 'GET', 'auth' => false],
                'Install' => ['title' => __d('baser_core', 'インストール'), 'url' => '/baser/api/baser-core/plugins/install/*.json', 'method' => 'POST', 'auth' => false],
                'ResetDb' => ['title' => __d('baser_core', 'データベース初期化'), 'url' => '/baser/api/baser-core/plugins/reset_db/*.json', 'method' => 'POST', 'auth' => false],
                'Uninstall' => ['title' => __d('baser_core', 'アンインストール'), 'url' => '/baser/api/baser-core/plugins/uninstall/*.json', 'method' => 'POST', 'auth' => false],
                'UpdateSort' => ['title' => __d('baser_core', '並び順変更'), 'url' => '/baser/api/baser-core/plugins/update_sort.json', 'method' => 'POST', 'auth' => false],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/baser-core/plugins/view/*.json', 'method' => 'GET', 'auth' => false]
            ]
        ],

        'SiteConfigsApi' => [
            'title' => __d('baser_core', 'システム基本設定API'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'CheckSendmail' => ['title' => __d('baser_core', 'メール送信テスト'), 'url' => '/baser/api/baser-core/site_configs/check_sendmail.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/baser-core/site_configs/edit.json', 'method' => 'POST', 'auth' => false],
                'View' => ['title' => __d('baser_core', '設定取得'), 'url' => '/baser/api/baser-core/site_configs/view.json', 'method' => 'GET', 'auth' => true],
            ]
        ],

        'SitesApi' => [
            'title' => __d('baser_core', 'サイトAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/sites/add.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/baser-core/sites/delete/*.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/baser-core/sites/edit/*.json', 'method' => 'POST', 'auth' => false],
                'GetSelectableDevicesAndLang' => ['title' => __d('baser_core', '選択可能な言語とデバイス取得'), 'url' => '/baser/api/baser-core/sites/get_selectable_devices_and_lang/*.json', 'method' => 'GET', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/sites/index.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/baser-core/sites/view/*.json', 'method' => 'GET', 'auth' => true],
            ]
        ],

        'ThemesApi' => [
            'title' => __d('baser_core', 'テーマAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/themes/add.json', 'method' => 'POST', 'auth' => false],
                'Apply' => ['title' => __d('baser_core', '適用'), 'url' => '/baser/api/baser-core/themes/apply/*.json', 'method' => 'POST', 'auth' => false],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/api/baser-core/themes/copy/*.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/baser-core/themes/delete/*.json', 'method' => 'POST', 'auth' => false],
                'GetMarketThemes' => ['title' => __d('baser_core', 'マーケットのテーマを取得'), 'url' => '/baser/api/baser-core/themes/get_market_themes.json', 'method' => 'GET', 'auth' => false],
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/themes/index.json', 'method' => 'GET', 'auth' => false],
                'LoadDefaultData' => ['title' => __d('baser_core', '初期データ読み込み'), 'url' => '/baser/api/baser-core/themes/load_default_data/*.json', 'method' => 'POST', 'auth' => false],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/baser-core/themes/view/*.json', 'method' => 'GET', 'auth' => false],
            ]
        ],

        'UserGroupsApi' => [
            'title' => __d('baser_core', 'ユーザーグループAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/user_groups/add.json', 'method' => 'POST', 'auth' => false],
                'copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/api/baser-core/user_groups/copy/*.json', 'method' => 'POST', 'auth' => false],
                'delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/baser-core/user_groups/delete/*.json', 'method' => 'POST', 'auth' => false],
                'edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/baser-core/user_groups/edit/*.json', 'method' => 'POST', 'auth' => false],
                'index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/user_groups/index.json', 'method' => 'GET', 'auth' => false],
                'list' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/baser-core/user_groups/list.json', 'method' => 'GET', 'auth' => false],
                'view' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/baser-core/user_groups/view/*.json', 'method' => 'GET', 'auth' => false],
            ]
        ],

        'UsersApi' => [
            'title' => __d('baser_core', 'ユーザーAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/baser-core/users/index.json', 'method' => 'GET', 'auth' => false],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/baser-core/users/view/*.json', 'method' => 'GET', 'auth' => false],
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/baser-core/users/add.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/baser-core/users/edit/*.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'method' => 'POST', 'url' => '/baser/api/baser-core/users/delete/*.json', 'auth' => false]
            ]
        ],

        'UtilitiesApi' => [
            'title' => __d('baser_core', 'ユーティリティAPI'),
            'plugin' => 'BaserCore',
            'type' => 'Api/Admin',
            'items' => [
                'clear_cache' => ['title' => __d('baser_core', 'キャッシュクリア'), 'url' => '/baser/api/baser-core/utilities/clear_cache.json', 'method' => 'GET', 'auth' => true],
                'delete_log' => ['title' => __d('baser_core', 'ログ削除'), 'url' => '/baser/api/baser-core/utilities/delete_log.json', 'method' => 'POST', 'auth' => false],
                'download_backup' => ['title' => __d('baser_core', 'DBバックアップダウンロード'), 'url' => '/baser/api/baser-core/utilities/download_backup.json', 'method' => 'GET', 'auth' => false],
                'download_log' => ['title' => __d('baser_core', 'ログダウンロード'), 'url' => '/baser/api/baser-core/utilities/download_log.json', 'method' => 'GET', 'auth' => false],
                'reset_contents_tree' => ['title' => __d('baser_core', 'ツリー構造リセット'), 'url' => '/baser/api/baser-core/utilities/reset_contents_tree.json', 'method' => 'POST', 'auth' => false],
                'restore_db' => ['title' => __d('baser_core', 'DBバックアップ復元'), 'url' => '/baser/api/baser-core/utilities/restore_db.json', 'method' => 'POST', 'auth' => false],
                'save_search_opened' => ['title' => __d('baser_core', '検索ボックス開閉状態保存'), 'url' => '/baser/api/baser-core/utilities/save_search_opened/*.json', 'method' => 'POST', 'auth' => true],
                'verity_contents_tree' => ['title' => __d('baser_core', 'ツリー構造チェック'), 'url' => '/baser/api/baser-core/utilities/verity_contents_tree.json', 'method' => 'POST', 'auth' => false],
            ]
        ],

    ]
];
