<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

return [
    'BcApp' => [
        'adminNavigation' => [
            'Systems' => [
                'Utilities' => [
                    'menus' => [
                        'EditorTemplates' => [
                            'title' => __d('baser_core', 'エディタテンプレート'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BcEditorTemplate', 'controller' => 'EditorTemplates', 'action' => 'index'],
                            'currentRegex' => '/\/editor_templates\/[^\/]+?/s'
                        ],
                    ]
                ]
            ]
        ]
    ]
];
