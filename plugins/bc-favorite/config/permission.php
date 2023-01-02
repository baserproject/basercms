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
         * Web API
         */
        'FavoritesApi' => [
            'title' => __d('baser', 'お気に入りAPI'),
            'plugin' => 'BcFavorite',
            'type' => 'Api',
            'items' => [
                // TODO ucmitz 本体側のログインユーザー別制御ができていないため一旦フルアクセスとする
                'Full' => ['title' => __d('baser', 'フルアクセス'), 'url' => '/baser/api/bc-favorite/favorites/*.json', 'method' => '*', 'auth' => true],
            ]
        ],
    ]
];

