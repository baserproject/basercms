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
         * システムナビ
         */
        'adminNavigation' => [
            'Plugins' => [
                'menus' => [
                    'UploaderConfigs' => [
                        'title' => 'アップローダー基本設定',
                        'url' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcUploader',
                            'controller' => 'UploaderConfigs',
                            'action' => 'index'
                        ]
                    ]
                ]
            ],
            'Contents' => [
                'BcUploader' => [
                    'title' => __d('baser', 'アップロード管理'),
                    'type' => 'uploader',
                    'icon' => 'bca-icon--uploader',
                    'menus' => [
                        'UplaoderFiles' => [
                            'title' => __d('baser', 'アップロードファイル'),
                            'url' => [
                                'prefix' => 'Admin',
                                'plugin' => 'BcUploader',
                                'controller' => 'UploaderFiles',
                                'action' => 'index'
                            ]],
                        'UploaderCategories' => [
                            'title' => __d('baser', 'アップロードカテゴリ'),
                            'url' => [
                                'prefix' => 'Admin',
                                'plugin' => 'BcUploader',
                                'controller' => 'UploaderCategories',
                                'action' => 'index'
                            ],
                            'currentRegex' => '/\/bc-uploader\/uploader_categories\/[^\/]+?/s'
                        ],
                    ]
                ],
            ],
        ]
    ],
    /**
     * アップローダー設定
     */
    'BcUploader' => [
        // システム管理者によるアップロードでいかなる拡張子も許可する
        'allowedAdmin' => false,
        // システム管理者グループ以外のユーザーがアップロード可能なファイル（拡張子をカンマ区切りで指定する）
        'allowedExt' => 'gif,jpg,jpeg,png,ico,pdf,zip,doc,docx,xls,xlsx,ppt,pptx,txt',
        // 'allowedExt' => 'mp4,mp3,mpg,mpeg,avi,wmv' // メディア例
        // 'allowedExt' => 'fon,ttf,ttc' // フォント例
    ]
];
