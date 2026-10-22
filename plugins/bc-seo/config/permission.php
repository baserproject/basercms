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
 * SEO 欄の入力は各編集画面の保存に含まれ、そちらのアクセスルールで判定される
 */

return [
    'permission' => [
        'SeoAdmin' => [
            'title' => __d('baser_core', 'SEO設定'),
            'plugin' => 'BcSeo',
            'type' => 'Admin',
            'items' => [
                'All' => ['title' => __d('baser_core', 'SEO設定の利用'), 'url' => '/baser/admin/bc-seo/*', 'method' => '*', 'auth' => true],
            ]
        ],
    ]
];
