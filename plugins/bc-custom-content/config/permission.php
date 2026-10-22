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
 *
 * 管理者以外のユーザーグループには、エントリーの作成・編集まで許可し、
 * テーブル・フィールドなど構造を変更する操作は許可しない
 */

return [
    'permission' => [

        /**
         * 管理画面
         */
        'CustomEntriesAdmin' => [
            'title' => __d('baser_core', 'カスタムエントリー管理'),
            'plugin' => 'BcCustomContent',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-custom-content/custom_entries/index/*', 'method' => 'GET', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/admin/bc-custom-content/custom_entries/add/*', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-custom-content/custom_entries/edit/*', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-custom-content/custom_entries/delete/*', 'method' => 'POST', 'auth' => true],
                'MoveUp' => ['title' => __d('baser_core', '上へ移動'), 'url' => '/baser/admin/bc-custom-content/custom_entries/move_up/*', 'method' => 'POST', 'auth' => true],
                'MoveDown' => ['title' => __d('baser_core', '下へ移動'), 'url' => '/baser/admin/bc-custom-content/custom_entries/move_down/*', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'CustomContentsAdmin' => [
            'title' => __d('baser_core', 'カスタムコンテンツ管理'),
            'plugin' => 'BcCustomContent',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-custom-content/custom_contents/index/*', 'method' => 'GET', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-custom-content/custom_contents/edit/*', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'CustomTablesAdmin' => [
            'title' => __d('baser_core', 'カスタムテーブル管理'),
            'plugin' => 'BcCustomContent',
            'type' => 'Admin',
            'items' => [
                'All' => ['title' => __d('baser_core', '全て'), 'url' => '/baser/admin/bc-custom-content/custom_tables/*', 'method' => '*', 'auth' => false],
            ]
        ],

        'CustomFieldsAdmin' => [
            'title' => __d('baser_core', 'カスタムフィールド管理'),
            'plugin' => 'BcCustomContent',
            'type' => 'Admin',
            'items' => [
                'All' => ['title' => __d('baser_core', '全て'), 'url' => '/baser/admin/bc-custom-content/custom_fields/*', 'method' => '*', 'auth' => false],
            ]
        ],

        /**
         * Web API
         */
        'CustomEntriesApi' => [
            'title' => __d('baser_core', 'カスタムエントリーAPI'),
            'plugin' => 'BcCustomContent',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_entries/index.json', 'method' => 'GET', 'auth' => true],
                'IndexShort' => ['title' => __d('baser_core', '一覧取得（短縮形）'), 'url' => '/baser/api/admin/bc-custom-content/custom_entries.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_entries/view/*.json', 'method' => 'GET', 'auth' => true],
                'List' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_entries/list/*.json', 'method' => 'GET', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/admin/bc-custom-content/custom_entries/add.json', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-custom-content/custom_entries/edit/*.json', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-custom-content/custom_entries/delete/*.json', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'CustomContentsApi' => [
            'title' => __d('baser_core', 'カスタムコンテンツAPI'),
            'plugin' => 'BcCustomContent',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_contents/index.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_contents/view/*.json', 'method' => 'GET', 'auth' => true],
                'List' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_contents/list/*.json', 'method' => 'GET', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/admin/bc-custom-content/custom_contents/add.json', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-custom-content/custom_contents/edit/*.json', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-custom-content/custom_contents/delete/*.json', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'CustomTablesApi' => [
            'title' => __d('baser_core', 'カスタムテーブルAPI'),
            'plugin' => 'BcCustomContent',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_tables/index.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_tables/view/*.json', 'method' => 'GET', 'auth' => true],
                'List' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_tables/list/*.json', 'method' => 'GET', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/admin/bc-custom-content/custom_tables/add.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-custom-content/custom_tables/edit/*.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-custom-content/custom_tables/delete/*.json', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'CustomFieldsApi' => [
            'title' => __d('baser_core', 'カスタムフィールドAPI'),
            'plugin' => 'BcCustomContent',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_fields/index.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_fields/view/*.json', 'method' => 'GET', 'auth' => true],
                'List' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_fields/list/*.json', 'method' => 'GET', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/admin/bc-custom-content/custom_fields/add.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-custom-content/custom_fields/edit/*.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-custom-content/custom_fields/delete/*.json', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'CustomLinksApi' => [
            'title' => __d('baser_core', 'カスタムリンクAPI'),
            'plugin' => 'BcCustomContent',
            'type' => 'Api/Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_links/index/*.json', 'method' => 'GET', 'auth' => true],
                'IndexShort' => ['title' => __d('baser_core', '一覧取得（短縮形）'), 'url' => '/baser/api/admin/bc-custom-content/custom_links.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_links/view/*.json', 'method' => 'GET', 'auth' => true],
                'List' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_links/list/*.json', 'method' => 'GET', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/admin/bc-custom-content/custom_links/add.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-custom-content/custom_links/edit/*.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-custom-content/custom_links/delete/*.json', 'method' => 'POST', 'auth' => false],
                'GetParentList' => ['title' => __d('baser_core', '親リスト取得'), 'url' => '/baser/api/admin/bc-custom-content/custom_links/get_parent_list/*.json', 'method' => 'GET', 'auth' => false],
            ]
        ],
    ]
];
