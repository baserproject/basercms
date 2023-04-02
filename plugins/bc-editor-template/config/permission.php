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
        'EditorTemplatesAdmin' => [
            'title' => __d('baser_core', 'エディターテンプレート管理'),
            'plugin' => 'BcEditorTemplate',
            'type' => 'Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/admin/bc-editor-template/editor_templates/add', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-editor-template/editor_templates/delete/*', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-editor-template/editor_templates/edit/*', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-editor-template/editor_templates/index', 'method' => 'GET', 'auth' => true],
                'Js' => ['title' => __d('baser_core', 'JS取得'), 'url' => '/baser/admin/bc-editor-template/editor_templates/js', 'method' => 'GET', 'auth' => true],
            ]
        ],

        /**
         * Web API
         */
        'EditorTemplatesApi' => [
            'title' => __d('baser_core', 'エディターテンプレートAPI'),
            'plugin' => 'BcEditorTemplate',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/api/bc-editor-template/editor_templates/add.json', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/bc-editor-template/editor_templates/delete/*.json', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/bc-editor-template/editor_templates/edit/*.json', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/bc-editor-template/editor_templates/index.json', 'method' => 'GET', 'auth' => true],
                'List' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/bc-editor-template/editor_templates/list.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/bc-editor-template/editor_templates/view/*.json', 'method' => 'GET', 'auth' => true],
            ]
        ],
    ]
];

