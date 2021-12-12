<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\Note;

/**
 * @checked
 */
return [
    /**
     * コンテンツ設定
     */
    'BcContents' => [
        'items' => [
            'BcPage' => [
                'Page' => [
                    'title' => __d('baser', '固定ページ'),
                    'multiple' => true,
                    'preview' => true,
                    'icon' => 'bca-icon--file',
                    'omitViewAction' => true,
                    'routes' => [
                        'add' => [
                            'admin' => true,
                            'controller' => 'Pages',
                            'action' => 'ajax_add'
                        ],
                        'edit' => [
                            'admin' => true,
                            'controller' => 'Pages',
                            'action' => 'edit'
                        ],
                        'delete' => [
                            'admin' => true,
                            'controller' => 'Pages',
                            'action' => 'delete'
                        ],
                        'view' => [
                            'controller' => 'Pages',
                            'action' => 'display'
                        ],
                        'copy' => [
                            'admin' => true,
                            'controller' => 'Pages',
                            'action' => 'ajax_copy'
                        ]
                    ]
                ]
            ]
        ]
    ]
];
