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
        'ThemeConfigsAdmin' => [
            'title' => __d('baser_core', 'テーマ設定'),
            'plugin' => 'BcThemeConfig',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-theme-config/theme_configs/index', 'method' => 'GET', 'auth' => false],
            ]
        ],

        /**
         * Web API
         */
        'ThemeConfigsApi' => [
            'title' => __d('baser_core', 'テーマ設定API'),
            'plugin' => 'BcThemeConfig',
            'type' => 'Api/Admin',
            'items' => [
                'View' => ['title' => __d('baser_core', '設定取得'), 'url' => '/baser/api/bc-theme-config/theme_configs/view.json', 'method' => 'GET', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/bc-theme-config/theme_configs/edit.json', 'method' => 'POST', 'auth' => false],
            ]
        ],
    ]
];

