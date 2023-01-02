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
        'WidgetAreasAdmin' => [
            'title' => __d('baser', 'ウィジェットエリア管理'),
            'plugin' => 'BcWidgetArea',
            'type' => 'Admin',
            'items' => [
                'Add' => ['title' => __d('baser', '新規登録'), 'url' => '/baser/admin/bc-widget-area/widget_areas/add', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser', '削除'), 'url' => '/baser/admin/bc-widget-area/widget_areas/delete/*', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser', '編集'), 'url' => '/baser/admin/bc-widget-area/widget_areas/edit/*', 'method' => 'POST', 'auth' => false],
                'Index' => ['title' => __d('baser', '一覧'), 'url' => '/baser/admin/bc-widget-area/widget_areas/index', 'method' => 'GET', 'auth' => false],
            ]
        ],

        /**
         * Web API
         */
        'WidgetAreasApi' => [
            'title' => __d('baser', 'ウィジェットエリアAPI'),
            'plugin' => 'BcWidgetArea',
            'type' => 'Api',
            'items' => [
                'Add' => ['title' => __d('baser', '新規追加'), 'url' => '/baser/api/bc-widget-area/widget_areas/add.json', 'method' => 'POST', 'auth' => false],
                'Batch' => ['title' => __d('baser', '一括処理'), 'url' => '/baser/api/bc-widget-area/widget_areas/batch.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser', '削除'), 'url' => '/baser/api/bc-widget-area/widget_areas/delete/*.json', 'method' => 'POST', 'auth' => false],
                'DeleteWidget' => ['title' => __d('baser', 'ウィジェット削除'), 'url' => '/baser/api/bc-widget-area/widget_areas/delete_widget/*.json', 'method' => 'POST', 'auth' => false],
                'Index' => ['title' => __d('baser', '一覧'), 'url' => '/baser/api/bc-widget-area/widget_areas/index.json', 'method' => 'GET', 'auth' => false],
                'UpdateSort' => ['title' => __d('baser', '並び順更新'), 'url' => '/baser/api/bc-widget-area/widget_areas/update_sort/*.json', 'method' => 'POST', 'auth' => false],
                'UpdateTitle' => ['title' => __d('baser', 'タイトル更新'), 'url' => '/baser/api/bc-widget-area/widget_areas/update_title/*.json', 'method' => 'POST', 'auth' => false],
                'UpdateWidget' => ['title' => __d('baser', '編集'), 'url' => '/baser/api/bc-widget-area/widget_areas/update_widget/*.json', 'method' => 'POST', 'auth' => false],
            ]
        ],
    ]
];
