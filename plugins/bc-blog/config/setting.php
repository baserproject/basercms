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

/**
 * システムナビ
 */
 return [
    'BcApp' => [
        // TODO ucmitz 未移行
        // BcBlogのルーティングが実勢できないとルーティングに失敗するため
        /* >>>
        'adminNavigation' => [
            'Plugins' => [
                'menus' => [
                    'BlogTags' => ['title' => 'ブログタグ設定', 'url' => ['admin' => true, 'plugin' => 'blog', 'controller' => 'blog_tags', 'action' => 'index']],
                ]
            ]
        ]
        <<< */
    ],

/* @var BlogContent $BlogContent */
// TODO ucmitz 未実装のためコメントアウト
/* >>>
$BlogContent = ClassRegistry::init('Blog.BlogContent');
$blogContents = $BlogContent->find('all', [
    'conditions' => [
        $BlogContent->Content->getConditionAllowPublish()
    ],
    'recursive' => 0,
    'order' => $BlogContent->id,
]);
foreach ($blogContents as $blogContent) {
    $blog = $blogContent['BlogContent'];
    $content = $blogContent['Content'];
    $menus = function ($blog) {
        $menus = [];
        $route = [
            'admin' => true, 'plugin' => 'blog', 'action' => 'index', $blog['id']
        ];
        $menus['BlogPosts' . $blog['id']] = [
            'title' => '記事',
            'url' => array_merge($route, ['controller' => 'blog_posts']),
            'currentRegex' => '{/blog/blog_posts/[^/]+?/' . $blog['id'] . '($|/)}s'
        ];
        $menus['BlogCategories' . $blog['id']] = [
            'title' => 'カテゴリ',
            'url' => array_merge($route, ['controller' => 'blog_categories']),
            'currentRegex' => '{/blog/blog_categories/[^/]+?/' . $blog['id'] . '($|/)}s'
        ];
        if ($blog['tag_use']) {
            $menus['BlogTags' . $blog['id']] = [
                'title' => 'タグ',
                'url' => array_merge($route, ['controller' => 'blog_tags']),
                'currentRegex' => '{/blog/blog_tags/[^/]+?/}s'
            ];
        }
        if ($blog['comment_use']) {
            $menus['BlogComments' . $blog['id']] = [
                'title' => 'コメント',
                'url' => array_merge($route, ['controller' => 'blog_comments'])
            ];
        }
        $menus['BlogContentsEdit' . $blog['id']] = [
            'title' => '設定',
            'url' => array_merge($route, ['controller' => 'blog_contents', 'action' => 'edit'])
        ];
        return $menus;
    };
    $config['BcApp.adminNavigation.Contents.' . 'BlogContent' . $blog['id']] = [
        'siteId' => $content['site_id'],
        'title' => $content['title'],
        'type' => 'blog-content',
        'icon' => 'bca-icon--blog',
        'menus' => $menus($blog)
    ];
}
<<< */
    'BcContents' => [
        'items' => [
            'BcBlog' => [
                'BlogContent' => [
                    'title' => __d('baser', 'ブログ'),
                    'multiple' => true,
                    'preview' => true,
                    'icon' => 'bca-icon--blog',
                    'routes' => [
                        'manage' => [
                            'admin' => true,
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogPosts',
                            'action' => 'index'
                        ],
                        'add' => [
                            'admin' => true,
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogContents',
                            'action' => 'ajax_add'
                        ],
                        'edit' => [
                            'admin' => true,
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogContents',
                            'action' => 'edit'
                        ],
                        'delete' => [
                            'admin' => true,
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
                            'admin' => true,
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogContents',
                            'action' => 'ajax_copy'
                        ],
                        'dblclick' => [
                            'admin' => true,
                            'plugin' => 'BcBlog',
                            'controller' => 'BlogPosts',
                            'action' => 'index'
                        ],
                    ]
                ]
            ]
        ]
    ],
    'Blog' => [
        // ブログアイキャッチサイズの初期値
        'eye_catch_size_thumb_width' => 600,
        'eye_catch_size_thumb_height' => 600,
        'eye_catch_size_mobile_thumb_width' => 150,
        'eye_catch_size_mobile_thumb_height' => 150,
    ]
];
