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
        'UploaderCategoriesAdmin' => [
            'title' => __d('baser_core', 'アップロードカテゴリ管理'),
            'plugin' => 'BcUploader',
            'type' => 'Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/admin/bc-uploader/uploader_categories/add', 'method' => 'POST', 'auth' => true],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/admin/bc-uploader/uploader_categories/copy/*', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-uploader/uploader_categories/delete/*', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-uploader/uploader_categories/edit/*', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-uploader/uploader_categories/index', 'method' => 'GET', 'auth' => true],
            ]
        ],

        'UploaderConfigsAdmin' => [
            'title' => __d('baser_core', 'アップローダー設定'),
            'plugin' => 'BcUploader',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-uploader/uploader_configs/index', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'UploaderFilesAdmin' => [
            'title' => __d('baser_core', 'アップロードファイル管理'),
            'plugin' => 'BcUploader',
            'type' => 'Admin',
            'items' => [
                'AjaxGetSearchBox' => ['title' => __d('baser_core', '検索ボックス取得'), 'url' => '/baser/admin/bc-uploader/uploader_files/ajax_get_search_box*', 'method' => 'GET', 'auth' => true], // 引数がない場合もある
                'AjaxImage' => ['title' => __d('baser_core', '画像取得'), 'url' => '/baser/admin/bc-uploader/uploader_files/ajax_image/*', 'method' => 'GET', 'auth' => true],
                'AjaxIndex' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/admin/bc-uploader/uploader_files/ajax_index*', 'method' => 'GET', 'auth' => true], // 引数がない場合もある
                'AjaxList' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/admin/bc-uploader/uploader_files/ajax_list*', 'method' => 'GET', 'auth' => true], // 引数がない場合もある
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-uploader/uploader_files/delete/*', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-uploader/uploader_files/edit/*', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-uploader/uploader_files/index', 'method' => 'GET', 'auth' => true],
            ]
        ],

        /**
         * Web API
         */
        'UploaderCategoriesApi' => [
            'title' => __d('baser_core', 'アップロードカテゴリAPI'),
            'plugin' => 'BcUploader',
            'type' => 'Api/Admin',
            'items' => [
                'index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-uploader/uploader_categories/index.json', 'method' => 'GET', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/admin/bc-uploader/uploader_categories/add.json', 'method' => 'POST', 'auth' => true],
                'edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-uploader/uploader_categories/edit/*.json', 'method' => 'POST', 'auth' => true],
                'delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-uploader/uploader_categories/delete/*.json', 'method' => 'POST', 'auth' => true],
                'copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/api/admin/bc-uploader/uploader_categories/copy/*.json', 'method' => 'POST', 'auth' => true],
                'batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/admin/bc-uploader/uploader_categories/batch.json', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'UploaderConfigsApi' => [
            'title' => __d('baser_core', 'アップローダー設定API'),
            'plugin' => 'BcUploader',
            'type' => 'Api/Admin',
            'items' => [
                'view' => ['title' => __d('baser_core', '設定取得'), 'url' => '/baser/api/admin/bc-uploader/uploader_categories/view.json', 'method' => 'GET', 'auth' => true],
                'edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-uploader/uploader_categories/edit.json', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'UploaderFilesApi' => [
            'title' => __d('baser_core', 'アップロードファイルAPI'),
            'plugin' => 'BcUploader',
            'type' => 'Api/Admin',
            'items' => [
                'index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-uploader/uploader_files/index.json', 'method' => 'GET', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/admin/bc-uploader/uploader_files/add.json', 'method' => 'POST', 'auth' => true],
                'edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-uploader/uploader_files/edit/*.json', 'method' => 'POST', 'auth' => true],
                'delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-uploader/uploader_files/delete/*.json', 'method' => 'POST', 'auth' => true],
            ]
        ],
    ]
];

