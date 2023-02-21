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

return [
    'BcApp' => [

        /**
         * 管理画面メニュー
         */
        'adminNavigation' => [
            'Systems' => [
                'CustomTables' => [
                    'title' => __d('baser', 'カスタムコンテンツ'),
                    'type' => 'system',
                    'menus' => [
                        // テーブル
                        'CustomTables' => [
                            'title' => __d('baser', 'テーブル'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BcCustomContent', 'controller' => 'CustomTables', 'action' => 'index'],
                            'currentRegex' => '/(\/custom_tables\/[^\/]+?|\/custom_links\/[^\/]+?\/)/s'
                        ],
                        // フィールド
                        'CustomFields' => [
                            'title' => __d('baser', 'フィールド'),
                            'url' => ['prefix' => 'Admin', 'plugin' => 'BcCustomContent', 'controller' => 'CustomFields', 'action' => 'index'],
                            'currentRegex' => '/\/custom_fields\/[^\/]+?/s'
                        ],
                    ]
                ]
            ]
        ]
    ],

    /**
     * コンテンツツリー設定
     */
    'BcContents' => [
        'items' => [
            'BcCustomContent' => [
                'CustomContent' => [
                    'title' => __d('baser', 'カスタムコンテンツ'),
                    'multiple' => true,
                    'preview' => true,
                    'icon' => 'bca-icon--custom',
                    'routes' => [
                        // 管理機能
                        'manage' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcCustomContent',
                            'controller' => 'CustomContents',
                            'action' => 'index'
                        ],
                        // 新規追加
                        'add' => [
                            'prefix' => 'Api',
                            'plugin' => 'BcCustomContent',
                            'controller' => 'CustomContents',
                            'action' => 'add'
                        ],
                        // 編集
                        'edit' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcCustomContent',
                            'controller' => 'CustomContents',
                            'action' => 'edit'
                        ],
                        // フロントビュー
                        'view' => [
                            'plugin' => 'BcCustomContent',
                            'controller' => 'CustomContent',
                            'action' => 'index'
                        ],
                        // コピー
                        'copy' => [
                            'prefix' => 'Api',
                            'plugin' => 'BcCustomContent',
                            'controller' => 'CustomContents',
                            'action' => 'copy'
                        ],
                        // ダブルクリック時の遷移先
                        // 定義がない場合は編集画面に遷移する
                        'dblclick' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcCustomContent',
                            'controller' => 'CustomContents',
                            'action' => 'index'
                        ],
                    ]
                ]
            ]
        ]
    ],
    'BcCustomContent' => [
        /**
         * フィールドグループ
         */
        'fieldCategories' => [
            '基本',
            '日付',
            '選択',
            'コンテンツ',
            'その他'
        ],
        /**
         * フィールドタイプ
         */
        'fieldTypes' => [
            'group' => [
                'category' => 'その他',
                'label' => 'グループ',
            ]
        ]
    ]
];
