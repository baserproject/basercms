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
         * 管理画面
         */
        'MailConfigsAdmin' => [
            'title' => __d('baser_core', 'メールフォーム設定'),
            'plugin' => 'BcMail',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-mail/mail_configs/index', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'MailContentsAdmin' => [
            'title' => __d('baser_core', 'メールコンテンツ管理'),
            'plugin' => 'BcMail',
            'type' => 'Admin',
            'items' => [
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-mail/mail_contents/edit/*', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'MailFieldsAdmin' => [
            'title' => __d('baser_core', 'メールフィールド管理'),
            'plugin' => 'BcMail',
            'type' => 'Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/admin/bc-mail/mail_fields/add/*', 'method' => 'POST', 'auth' => true],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/admin/bc-mail/mail_fields/copy/*', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-mail/mail_fields/delete/*', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-mail/mail_fields/edit/*', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-mail/mail_fields/index/*', 'method' => 'GET', 'auth' => true],
                'Publish' => ['title' => __d('baser_core', '公開する'), 'url' => '/baser/admin/bc-mail/mail_fields/publish/*', 'method' => 'POST', 'auth' => true],
                'Unpublish' => ['title' => __d('baser_core', '非公開にする'), 'url' => '/baser/admin/bc-mail/mail_fields/unpublish/*', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'MailMessagesAdmin' => [
            'title' => __d('baser_core', 'メールメッセージ管理'),
            'plugin' => 'BcMail',
            'type' => 'Admin',
            'items' => [
                'Attachment' => ['title' => __d('baser_core', '添付ファイル表示'), 'url' => '/baser/admin/bc-mail/mail_messages/attachment/*', 'method' => 'GET', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-mail/mail_messages/delete/*', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-mail/mail_messages/index/*', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '表示'), 'url' => '/baser/admin/bc-mail/mail_messages/view/*', 'method' => 'GET', 'auth' => true],
                'DownloadCsv' => ['title' => __d('baser_core', 'CSVダウンロード'), 'url' => '/baser/admin/bc-mail/mail_messages/download_csv/*', 'method' => 'GET', 'auth' => true],
            ]
        ],

        /**
         * Web API
         */
        'MailConfigsApi' => [
            'title' => __d('baser_core', 'メールフォーム設定API'),
            'plugin' => 'BcMail',
            'type' => 'Api/Admin',
            'items' => [
                'view' => ['title' => __d('baser_core', '設定取得'), 'url' => '/baser/api/admin/bc-mail/mail_configs/view.json', 'method' => 'GET', 'auth' => true],
                'edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-mail/mail_configs/edit.json', 'method' => 'POST', 'auth' => false],
            ]
        ],

        'MailContentsApi' => [
            'title' => __d('baser_core', 'メールコンテンツAPI'),
            'plugin' => 'BcMail',
            'type' => 'Api/Admin',
            'items' => [
                'index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-mail/mail_contents/index.json', 'method' => 'GET', 'auth' => true],
                'view' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/admin/bc-mail/mail_contents/view/*.json', 'method' => 'GET', 'auth' => true],
                'list' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/admin/bc-mail/mail_contents/list.json', 'method' => 'GET', 'auth' => true],
                'add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/admin/bc-mail/mail_contents/add.json', 'method' => 'POST', 'auth' => true],
                'edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-mail/mail_contents/edit/*.json', 'method' => 'POST', 'auth' => true],
                'delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-mail/mail_contents/delete/*.json', 'method' => 'POST', 'auth' => true],
                'copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/api/admin/bc-mail/mail_contents/copy.json', 'method' => 'POST', 'auth' => true],
            ]
        ],

        // TODO ucmitz API未実装のため、URLの値未検証
        'MailFieldsApi' => [
            'title' => __d('baser_core', 'メールフィールドAPI'),
            'plugin' => 'BcMail',
            'type' => 'Api/Admin',
            'items' => [
                'index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-mail/mail_fields/index/*.json', 'method' => 'GET', 'auth' => true],
                'view' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/admin/bc-mail/mail_fields/view/*.json', 'method' => 'GET', 'auth' => true],
                'list' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/admin/bc-mail/mail_fields/list/*.json', 'method' => 'GET', 'auth' => true],
                'add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/admin/bc-mail/mail_fields/add/*.json', 'method' => 'POST', 'auth' => true],
                'edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-mail/mail_fields/edit/*.json', 'method' => 'POST', 'auth' => true],
                'delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-mail/mail_fields/delete/*.json', 'method' => 'POST', 'auth' => true],
                'copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/api/admin/bc-mail/mail_fields/copy/*.json', 'method' => 'POST', 'auth' => true],
                'batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/admin/bc-mail/mail_fields/batch.json', 'method' => 'POST', 'auth' => true],
                'update_sort' => ['title' => __d('baser_core', '並び順更新'), 'url' => '/baser/api/admin/bc-mail/mail_fields/update_sort/*.json', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'MailMessagesApi' => [
            'title' => __d('baser_core', 'メールメッセージAPI'),
            'plugin' => 'BcMail',
            'type' => 'Api/Admin',
            'items' => [
                'add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/admin/bc-mail/mail_messages/add/*.json', 'method' => 'POST', 'auth' => true],
                'batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/admin/bc-mail/mail_messages/batch/*.json', 'method' => 'POST', 'auth' => true],
                'delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/admin/bc-mail/mail_messages/delete/*.json', 'method' => 'POST', 'auth' => true],
                'download' => ['title' => __d('baser_core', 'ダウンロード'), 'url' => '/baser/api/admin/bc-mail/mail_messages/download/*.json', 'method' => 'GET', 'auth' => true],
                'edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/admin/bc-mail/mail_messages/edit/*.json', 'method' => 'POST', 'auth' => true],
                'index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/admin/bc-mail/mail_messages/index/*.json', 'method' => 'GET', 'auth' => true],
                'view' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/admin/bc-mail/mail_messages/view/*.json', 'method' => 'GET', 'auth' => true],
            ]
        ],
    ]
];
