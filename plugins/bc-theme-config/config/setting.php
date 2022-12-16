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
 * テーマ設定機能の設定
 */
return [
    'BcApp' => [
        /**
         * メニュー
         */
        'adminNavigation' => [
            'Systems' => [
                'Theme' => [
                    'menus' => [
                        'ThemeConfigs' => [
                            'title' => __d('baser', '設定'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BcThemeConfig', 'controller' => 'ThemeConfigs', 'action' => 'index']
                        ]
                    ]
                ]
            ]
        ]
    ]
];
