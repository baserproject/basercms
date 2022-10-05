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

/**
 * setting
 *
 * @checked
 * @unitTest
 */

return [
    'BcApp' => [
        /**
         * システムナビ
         */
        'adminNavigation' => [
            'Systems' => [
                'Utilities' => [
                    'menus' => [
                        'SearchIndices' => [
                            'title' => __d('baser', '検索インデックス'),
                            'url' => [
                                'prefix' => 'Admin',
                                'plugin' => 'BcSearchIndex',
                                'controller' => 'search_indexes',
                                'action' => 'index'
                            ]
                        ]
                    ]
                ]
            ]
        ]
    ]
];
