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

use Cake\Core\Configure;

$defaultFrontTheme = \Cake\Utility\Inflector::camelize(Configure::read('BcApp.defaultFrontTheme'), '-');

return [

    /**
     * テーマテンプレートの編集を許可するかどうか
     */
    'BcThemeFile.allowedThemeEdit' => true,

    /**
     * 管理画面メニュー
     */
    'BcApp' => [
        'adminNavigation' => [
            'Systems' => [
                'Theme' => [
                    'menus' => [
                        'Themes' => [
                            // 通常のテーマ管理、または、デフォルトテーマ以外のテーマフォルダ管理
                            'currentRegex' => '/(\/themes\/[^\/]+?|\/theme_files\/[^\/]+?\/(?!.*' . $defaultFrontTheme . '))/s'
                        ]
                    ]
                ],
                'Utilities' => [
                    'menus' => [
                        'ThemeFiles' => [
                            'title' => __d('baser', 'コアテンプレート確認'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BcThemeFile', 'controller' => 'ThemeFiles', 'action' => 'index', $defaultFrontTheme, 'BaserCore'],
                            'currentRegex' => '/\/theme_files\/[^\/]+?\/' . $defaultFrontTheme . '/s'
                        ]
                    ]
                ]
            ]
        ]
    ]
];
