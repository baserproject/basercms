<?php

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Uploader.Config
 * @since           baserCMS v 3.0.10
 * @license         https://basercms.net/license/index.html
 */

/**
 * システムナビ
 */
$config['BcApp.adminNavigation'] = [
    'Plugins' => [
        'menus' => [
            'UploaderConfigs' => ['title' => 'アップローダー基本設定', 'url' => ['admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_configs', 'action' => 'index']],
        ]
    ],
    'Contents' => [
        'BcUploader' => [
            'title' => __d('baser', 'アップロード管理'),
            'type' => 'uploader',
            'icon' => 'bca-icon--uploader',
            'menus' => [
                'UplaoderFiles' => ['title' => __d('baser', 'アップロードファイル'), 'url' => ['admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_files', 'action' => 'index']],
                'UploaderCategories' => [
                    'title' => __d('baser', 'カテゴリ'),
                    'url' => ['admin' => true, 'plugin' => 'uploader', 'controller' => 'uploader_categories', 'action' => 'index'],
                    'currentRegex' => '/\/uploader\/uploader_categories\/[^\/]+?/s'
                ],
            ]
        ],
    ],
];

$config['BcUploader'] = [
    // システム管理者によるアップロードでいかなる拡張子も許可する
    'allowedAdmin' => false,
    // システム管理者グループ以外のユーザーがアップロード可能なファイル（拡張子をカンマ区切りで指定する）
    'allowedExt' => 'gif,jpg,jpeg,png,ico,pdf,zip,doc,docx,xls,xlsx,ppt,pptx,txt',
    // 'allowedExt' => 'mp4,mp3,mpg,mpeg,avi,wmv' // メディア例
    // 'allowedExt' => 'fon,ttf,ttc' // フォント例
];
