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
        'SearchIndexesAdmin' => [
            'title' => __d('baser_core', '検索インデックス管理'),
            'plugin' => 'BcSearchIndex',
            'type' => 'Admin',
            'items' => [
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-search-index/search_indexes/delete/*', 'method' => 'POST', 'auth' => false],
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-search-index/search_indexes/index', 'method' => 'GET', 'auth' => false],
                'reconstruct' => ['title' => __d('baser_core', '検索インデックス再構築'), 'url' => '/baser/admin/bc-search-index/search_indexes/reconstruct', 'method' => 'POST', 'auth' => false],
            ]
        ],

        /**
         * Web API
         */
        'SearchIndexesApi' => [
            'title' => __d('baser_core', '検索インデックスAPI'),
            'plugin' => 'BcSearchIndex',
            'type' => 'Api/Admin',
            'items' => [
                'Batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/bc-search-index/search_indexes/batch.json', 'method' => 'POST', 'auth' => false],
                'ChangePriority' => ['title' => __d('baser_core', '優先度変更'), 'url' => '/baser/api/bc-search-index/search_indexes/change_priority/*.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/bc-search-index/search_indexes/delete/*.json', 'method' => 'POST', 'auth' => false],
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/bc-search-index/search_indexes/index.json', 'method' => 'GET', 'auth' => false],
                'Reconstruct' => ['title' => __d('baser_core', '検索インデックス再構築'), 'url' => '/baser/api/bc-search-index/search_indexes/reconstruct.json', 'method' => 'POST', 'auth' => false],
            ]
        ],
    ]
];

