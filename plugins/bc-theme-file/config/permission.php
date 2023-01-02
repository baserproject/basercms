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
        'ThemeFilesAdmin' => [
            'title' => __d('baser', 'テーマファイル管理'),
            'plugin' => 'BcThemeFile',
            'type' => 'Admin',
            'items' => [
                'Add' => ['title' => __d('baser', 'ファイル新規登録'), 'url' => '/baser/admin/bc-theme-file/theme_files/add/*', 'method' => 'POST', 'auth' => false],
                'AddFolder' => ['title' => __d('baser', 'フォルダ新規登録'), 'url' => '/baser/admin/bc-theme-file/theme_files/add_folder/*', 'method' => 'POST', 'auth' => false],
                'Copy' => ['title' => __d('baser', 'ファイルコピー'), 'url' => '/baser/admin/bc-theme-file/theme_files/copy/*', 'method' => 'POST', 'auth' => false],
                'CopyFolder' => ['title' => __d('baser', 'フォルダコピー'), 'url' => '/baser/admin/bc-theme-file/theme_files/copy_folder/*', 'method' => 'POST', 'auth' => false],
                'CopyFolderToTheme' => ['title' => __d('baser', 'フォルダをテーマにコピー'), 'url' => '/baser/admin/bc-theme-file/theme_files/copy_folder_to_theme/*', 'method' => 'POST', 'auth' => false],
                'CopyToTheme' => ['title' => __d('baser', 'ファイルをテーマにコピー'), 'url' => '/baser/admin/bc-theme-file/theme_files/copy_to_theme/*', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser', 'ファイル削除'), 'url' => '/baser/admin/bc-theme-file/theme_files/delete/*', 'method' => 'POST', 'auth' => false],
                'DeleteFolder' => ['title' => __d('baser', 'フォルダ削除'), 'url' => '/baser/admin/bc-theme-file/theme_files/delete_folder/*', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser', 'ファイル編集'), 'url' => '/baser/admin/bc-theme-file/theme_files/edit/*', 'method' => 'POST', 'auth' => false],
                'EditFolder' => ['title' => __d('baser', 'フォルダ編集'), 'url' => '/baser/admin/bc-theme-file/theme_files/edit_folder/*', 'method' => 'POST', 'auth' => false],
                'Img' => ['title' => __d('baser', '画像取得'), 'url' => '/baser/admin/bc-theme-file/theme_files/img/*', 'method' => 'GET', 'auth' => false],
                'ImgThumb' => ['title' => __d('baser', 'サムネイル画像取得'), 'url' => '/baser/admin/bc-theme-file/theme_files/img_thumb/*', 'method' => 'GET', 'auth' => false],
                'Index' => ['title' => __d('baser', '一覧'), 'url' => '/baser/admin/bc-theme-file/theme_files/index/*', 'method' => 'GET', 'auth' => false],
                'Upload' => ['title' => __d('baser', 'ファイルアップロード'), 'url' => '/baser/admin/bc-theme-file/theme_files/upload/*', 'method' => 'POST', 'auth' => false],
                'View' => ['title' => __d('baser', 'ファイル表示'), 'url' => '/baser/admin/bc-theme-file/theme_files/view/*', 'method' => 'GET', 'auth' => false],
                'ViewFolder' => ['title' => __d('baser', 'フォルダ表示'), 'url' => '/baser/admin/bc-theme-file/theme_files/view_folder/*', 'method' => 'GET', 'auth' => false],
            ]
        ],

        /**
         * Web API
         */
        // TODO ucmitz API未実装のためURLの設定値について未検証
        'ThemeFilesApi' => [
            'title' => __d('baser', 'テーマファイルAPI'),
            'plugin' => 'BcThemeFile',
            'type' => 'Api',
            'items' => [
                'Add' => ['title' => __d('baser', '新規登録'), 'url' => '/baser/api/bc-theme-file/theme_files/add.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser', '編集'), 'url' => '/baser/api/bc-theme-file/theme_files/edit.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser', '削除'), 'url' => '/baser/api/bc-theme-file/theme_files/delete.json', 'method' => 'POST', 'auth' => false],
                'Copy' => ['title' => __d('baser', 'コピー'), 'url' => '/baser/api/bc-theme-file/theme_files/copy.json', 'method' => 'POST', 'auth' => false],
                'CopyToTheme' => ['title' => __d('baser', 'ファイルをテーマにコピー'), 'url' => '/baser/api/bc-theme-file/theme_files/copy_to_theme.json', 'method' => 'POST', 'auth' => false],
                'View' => ['title' => __d('baser', '単一取得'), 'url' => '/baser/api/bc-theme-file/theme_files/view.json', 'method' => 'GET', 'auth' => false],
                'Img' => ['title' => __d('baser', '画像取得'), 'url' => '/baser/api/bc-theme-file/theme_files/img.json', 'method' => 'GET', 'auth' => false],
                'ImgThumb' => ['title' => __d('baser', 'サムネイル画像取得'), 'url' => '/baser/api/bc-theme-file/theme_files/img_thumb.json', 'method' => 'GET', 'auth' => false],
            ]
        ],

        // TODO ucmitz API未実装のためURLの設定値について未検証
        'ThemeFoldersApi' => [
            'title' => __d('baser', 'テーマフォルダAPI'),
            'plugin' => 'BcThemeFile',
            'type' => 'Api',
            'items' => [
                'Index' => ['title' => __d('baser', '一覧'), 'url' => '/baser/api/bc-theme-file/theme_folders/index.json', 'method' => 'GET', 'auth' => false],
                'Add' => ['title' => __d('baser', '新規登録'), 'url' => '/baser/api/bc-theme-file/theme_folders/add.json', 'method' => 'POST', 'auth' => false],
                'Edit' => ['title' => __d('baser', '編集'), 'url' => '/baser/api/bc-theme-file/theme_folders/edit.json', 'method' => 'POST', 'auth' => false],
                'Delete' => ['title' => __d('baser', '削除'), 'url' => '/baser/api/bc-theme-file/theme_folders/delete.json', 'method' => 'POST', 'auth' => false],
                'Copy' => ['title' => __d('baser', 'コピー'), 'url' => '/baser/api/bc-theme-file/theme_folders/copy.json', 'method' => 'POST', 'auth' => false],
                'Upload' => ['title' => __d('baser', 'アップロード'), 'url' => '/baser/api/bc-theme-file/theme_folders/upload.json', 'method' => 'POST', 'auth' => false],
                'CopyToTheme' => ['title' => __d('baser', 'フォルダをテーマにコピー'), 'url' => '/baser/api/bc-theme-file/theme_folders/copy_to_theme.json', 'method' => 'POST', 'auth' => false],
                'View' => ['title' => __d('baser', '単一取得'), 'url' => '/baser/api/bc-theme-file/theme_folders/view.json', 'method' => 'GET', 'auth' => false],
            ]
        ],
    ]
];

