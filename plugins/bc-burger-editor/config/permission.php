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
 *
 * 画像・ファイルの一覧やアップロードはエディタが内部で呼ぶため、まとめて 1 件で許可する。
 * アップロードできる拡張子は Bge.allowedExt / Bge.deniedExtension で制限している
 */

return [
    'permission' => [
        'BurgerEditorAdmin' => [
            'title' => __d('baser_core', 'BurgerEditor'),
            'plugin' => 'BcBurgerEditor',
            'type' => 'Admin',
            'items' => [
                'All' => ['title' => __d('baser_core', 'BurgerEditorの利用'), 'url' => '/baser/admin/bc-burger-editor/*', 'method' => '*', 'auth' => true],
            ]
        ],
    ]
];
