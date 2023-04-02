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
        'BlogCategoriesAdmin' => [
            'title' => __d('baser_core', 'ブログカテゴリ管理'),
            'plugin' => 'BcBlog',
            'type' => 'Admin',
            'items' => [
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-blog/blog_categories/index/*', 'method' => 'GET', 'auth' => true],
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/admin/bc-blog/blog_categories/add/*', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-blog/blog_categories/edit/*', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-blog/blog_categories/delete/*', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'BlogCommentsAdmin' => [
            'title' => __d('baser_core', 'ブログコメント管理'),
            'plugin' => 'BcBlog',
            'type' => 'Admin',
            'items' => [
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-blog/blog_comments/delete/*', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-blog/blog_comments/index/*', 'method' => 'GET', 'auth' => true],
                'Publish' => ['title' => __d('baser_core', '公開する'), 'url' => '/baser/admin/bc-blog/blog_comments/publish/*', 'method' => 'POST', 'auth' => true],
                'Unpublish' => ['title' => __d('baser_core', '非公開にする'), 'url' => '/baser/admin/bc-blog/blog_comments/unpublish/*', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'BlogContentsAdmin' => [
            'title' => __d('baser_core', 'ブログコンテンツ管理'),
            'plugin' => 'BcBlog',
            'type' => 'Admin',
            'items' => [
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-blog/blog_contents/edit/*', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'BlogPostsAdmin' => [
            'title' => __d('baser_core', 'ブログ記事管理'),
            'plugin' => 'BcBlog',
            'type' => 'Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/admin/bc-blog/blog_posts/add/*', 'method' => 'POST', 'auth' => true],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/admin/bc-blog/blog_posts/copy/*', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-blog/blog_posts/delete/*', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-blog/blog_posts/edit/*', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-blog/blog_posts/index/*', 'method' => 'GET', 'auth' => true],
                'Publish' => ['title' => __d('baser_core', '公開する'), 'url' => '/baser/admin/bc-blog/blog_posts/publish/*', 'method' => 'POST', 'auth' => true],
                'Unpublish' => ['title' => __d('baser_core', '非公開にする'), 'url' => '/baser/admin/bc-blog/blog_posts/unpublish/*', 'method' => 'POST', 'auth' => true],
            ]
        ],

        'BlogTagsAdmin' => [
            'title' => __d('baser_core', 'ブログタグ管理'),
            'plugin' => 'BcBlog',
            'type' => 'Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規登録'), 'url' => '/baser/admin/bc-blog/blog_tags/add', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/admin/bc-blog/blog_tags/delete/*', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/admin/bc-blog/blog_tags/edit/*', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧'), 'url' => '/baser/admin/bc-blog/blog_tags/index/*', 'method' => 'GET', 'auth' => true],
            ]
        ],

        /**
         * Web API
         */
        'BlogCategoriesApi' => [
            'title' => __d('baser_core', 'ブログカテゴリAPI'),
            'plugin' => 'BcBlog',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/bc-blog/blog_categories/add/*.json', 'method' => 'POST', 'auth' => true],
                'Batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/bc-blog/blog_categories/batch.json', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/bc-blog/blog_categories/delete/*.json', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/bc-blog/blog_categories/edit/*.json', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/bc-blog/blog_categories/index/*.json', 'method' => 'GET', 'auth' => true],
                'List' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/bc-blog/blog_categories/list/*.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/bc-blog/blog_categories/view/*.json', 'method' => 'GET', 'auth' => true],
            ]
        ],

        'BlogCommentsApi' => [
            'title' => __d('baser_core', 'ブログコメントAPI'),
            'plugin' => 'BcBlog',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/bc-blog/blog_comments/add/*.json', 'method' => 'POST', 'auth' => true],
                'Batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/bc-blog/blog_comments/batch.json', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/bc-blog/blog_comments/delete/*.json', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/bc-blog/blog_comments/index.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/bc-blog/blog_comments/view/*.json', 'method' => 'GET', 'auth' => true],
            ]
        ],

        'BlogContentsApi' => [
            'title' => __d('baser_core', 'ブログコンテンツAPI'),
            'plugin' => 'BcBlog',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/bc-blog/blog_contents/add.json', 'method' => 'POST', 'auth' => true],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/api/bc-blog/blog_contents/copy.json', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/bc-blog/blog_contents/delete/*.json', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/bc-blog/blog_contents/edit/*.json', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/bc-blog/blog_contents/index.json', 'method' => 'GET', 'auth' => true],
                'List' => ['title' => __d('baser_core', 'リスト取得'), 'url' => '/baser/api/bc-blog/blog_contents/list/*.json', 'method' => 'GET', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/bc-blog/blog_contents/view/*.json', 'method' => 'GET', 'auth' => true],
            ]
        ],

        'BlogPostsApi' => [
            'title' => __d('baser_core', 'ブログ記事API'),
            'plugin' => 'BcBlog',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/bc-blog/blog_posts/add.json', 'method' => 'POST', 'auth' => true],
                'Batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/bc-blog/blog_posts/batch.json', 'method' => 'POST', 'auth' => true],
                'Copy' => ['title' => __d('baser_core', 'コピー'), 'url' => '/baser/api/bc-blog/blog_posts/copy/*.json', 'method' => 'POST', 'auth' => true],
                'Delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/bc-blog/blog_posts/delete/*.json', 'method' => 'POST', 'auth' => true],
                'Edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/bc-blog/blog_posts/edit/*.json', 'method' => 'POST', 'auth' => true],
                'Index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/bc-blog/blog_posts/index.json', 'method' => 'GET', 'auth' => true],
                'Publish' => ['title' => __d('baser_core', '公開する'), 'url' => '/baser/api/bc-blog/blog_posts/publish/*.json', 'method' => 'POST', 'auth' => true],
                'Unpublish' => ['title' => __d('baser_core', '非公開にする'), 'url' => '/baser/api/bc-blog/blog_posts/unpublish/*.json', 'method' => 'POST', 'auth' => true],
                'View' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/bc-blog/blog_posts/view/*.json', 'method' => 'GET', 'auth' => true],
            ]
        ],

        'BlogTagsApi' => [
            'title' => __d('baser_core', 'ブログタグAPI'),
            'plugin' => 'BcBlog',
            'type' => 'Api/Admin',
            'items' => [
                'Add' => ['title' => __d('baser_core', '新規追加'), 'url' => '/baser/api/bc-blog/blog_tags/add.json', 'method' => 'POST', 'auth' => true],
                'batch' => ['title' => __d('baser_core', '一括処理'), 'url' => '/baser/api/bc-blog/blog_tags/batch.json', 'method' => 'POST', 'auth' => true],
                'delete' => ['title' => __d('baser_core', '削除'), 'url' => '/baser/api/bc-blog/blog_tags/delete/*.json', 'method' => 'POST', 'auth' => true],
                'edit' => ['title' => __d('baser_core', '編集'), 'url' => '/baser/api/bc-blog/blog_tags/edit/*.json', 'method' => 'POST', 'auth' => true],
                'index' => ['title' => __d('baser_core', '一覧取得'), 'url' => '/baser/api/bc-blog/blog_tags/index.json', 'method' => 'GET', 'auth' => true],
                'view' => ['title' => __d('baser_core', '単一取得'), 'url' => '/baser/api/bc-blog/blog_tags/view/*.json', 'method' => 'GET', 'auth' => true],
            ]
        ],

    ]
];

