<?php

/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Blog.Config
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */


return [
    'BcApp' => [
        /**
         * システムナビ
         * ブログコンテンツ系のメニューは BcBlogViewEventListener::beforeRender() にて実装
         */
        'adminNavigation' => [
            'Plugins' => [
                'menus' => [
                    'BlogTags' => [
                        'title' => __d('baser_core', 'ブログタグ設定'),
                        'url' => [
                            'Admin' => true,
                            'plugin' => 'BcBlog',
                            'controller' => 'blog_tags',
                            'action' => 'index'
                    ]],
                ]
            ]
        ]
    ],
    'BcContents' => [
        'items' => [
            'BcBlog' => [
                'BlogContent' => [
                    'title' => __d('baser_core', 'ブログ'),
                    'multiple' => true,
                    'preview' => true,
                    'icon' => 'bca-icon--blog',
                    'routes' => [
                        'manage' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogPosts',
                            'action' => 'index'
                        ],
                        'add' => [
                            'prefix' => 'Api',
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogContents',
                            'action' => 'add'
                        ],
                        'edit' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogContents',
                            'action' => 'edit'
                        ],
                        'delete' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogContents',
                            'action' => 'delete'
                        ],
                        'view' => [
                            'plugin' => 'BcBlog',
                            'controller' => 'Blog',
                            'action' => 'index'
                        ],
                        'copy' => [
                            'prefix' => 'Api',
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogContents',
                            'action' => 'copy'
                        ],
                        'dblclick' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogPosts',
                            'action' => 'index'
                        ],
                    ]
                ]
            ]
        ]
    ],
    'BcBlog' => [
        // ブログアイキャッチサイズの初期値
        'eye_catch_size_thumb_width' => 600,
        'eye_catch_size_thumb_height' => 600,
        'eye_catch_size_mobile_thumb_width' => 150,
        'eye_catch_size_mobile_thumb_height' => 150,
    ]
];
