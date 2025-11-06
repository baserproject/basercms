<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.2.0
 * @license       https://basercms.net/license/index.html MIT License
 */

return [
    'BcSeo' => [
        // 項目
        'fields' => [
           // '項目キー' => [
           //     'title'        => '',   // 項目名
           //     'type'         => '',   // 項目種別 text/textarea/file
           //     'rel'          => '',   // HTMLのrel属性
           //     'property'     => '',   // HTMLのproperty属性
           //     'url'          => true, // 出力時に絶対URLへの変換を行う
           //     'ignoreTypes'  => [],   // 指定したタイプのフォームの編集画面には項目を表示しない
           //     'ignoreParent' => true, // 親要素の設定を引き継がない
           // ],
           'description' => [
               'title' => 'ディスクリプション',
               'type' => 'textarea',
               'name' => 'description',
           ],
           'keywords' => [
               'title' => 'キーワード',
               'type' => 'text',
               'name' => 'keywords',
           ],
           'canonical_url' => [
               'title' => 'カノニカル URL',
               'type' => 'text',
               'rel' => 'canonical',
               'url' => true,
               'ignoreTypes' => [
                   'site',
                   'blogContent',
                   'blogCategory',
                   'customContent',
               ],
               'ignoreParent' => true,
           ],
           'og_title' => [
               'title' => 'OG タイトル',
               'type' => 'text',
               'property' => 'og:title',
               'ignoreTypes' => [
                   'site',
               ],
               'ignoreParent' => true,
           ],
           'og_description' => [
               'title' => 'OG ディスクリプション',
               'type' => 'textarea',
               'property' => 'og:description',
           ],
           'og_type' => [
               'title' => 'OG タイプ',
               'type' => 'text',
               'property' => 'og:type',
           ],
           'og_image' => [
               'title' => 'OG イメージ',
               'type' => 'file',
               'property' => 'og:image',
           ],
           'og_url' => [
               'title' => 'OG URL',
               'type' => 'text',
               'property' => 'og:url',
               'url' => true,
               'ignoreTypes' => [
                   'site',
                   'blogContent',
                   'blogCategory',
                   'customContent',
               ],
               'ignoreParent' => true,
           ],
        ],
        // SEO設定欄を追加するフォーム
        'seoForms' => [
            'site' => [
                'eventIds' => [
                    'SiteAdminAddForm',
                    'SiteAdminEditForm',
                ],
            ],
            'blogContent' => [
                'eventIds' => [
                    'BlogContentAdminEditForm',
                ],
            ],
            'mailContent' => [
                'eventIds' => [
                    'MailContentAdminEditForm',
                ],
            ],
            'page' => [
                'eventIds' => [
                    'PageAdminEditForm',
                ],
            ],
            'contentFolder' => [
                'eventIds' => [
                    'ContentFolderAdminEditForm',
                ],
            ],
            'blogCategory' => [
                'eventIds' => [
                    'BlogCategoryAdminAddForm',
                    'BlogCategoryAdminEditForm',
                ],
            ],
            'blogPost' => [
                'eventIds' => [
                    'BlogPostForm',
                ],
            ],
            'customContent' => [
                'eventIds' => [
                    'CustomContentAdminEditForm',
                ],
            ],
            'customEntry' => [
                'eventIds' => [
                    'CustomEntriesForm',
                ],
            ],
        ],
        // テーブル関連付け
        'associations' => [
            [
                'tablePlugin' => 'BaserCore',
                'table' => 'Sites',
            ], [
                'tablePlugin' => 'BaserCore',
                'table' => 'Contents',
            ], [
                'tablePlugin' => 'BcBlog',
                'table' => 'BlogCategories',
            ], [
                'tablePlugin' => 'BcBlog',
                'table' => 'BlogPosts',
            ], [
                'tablePlugin' => 'BcCustomContent',
                'table' => 'CustomEntries',
            ],
        ],
        // 設定画面のコントローラー
        'controllers' => [
            [
                'plugin' => 'BaserCore',
                'controller' => 'Sites',
            ], [
                'plugin' => 'BaserCore',
                'controller' => 'ContentFolders',
            ], [
                'plugin' => 'BaserCore',
                'controller' => 'Pages',
            ], [
                'plugin' => 'BcBlog',
                'controller' => 'BlogContents',
            ], [
                'plugin' => 'BcBlog',
                'controller' => 'BlogCategories',
            ], [
                'plugin' => 'BcBlog',
                'controller' => 'BlogPosts',
            ], [
                'plugin' => 'BcMail',
                'controller' => 'MailContents',
            ], [
                'plugin' => 'BaserCore',
                'controller' => 'Contents',
            ], [
                'plugin' => 'BcCustomContent',
                'controller' => 'CustomContents',
            ], [
                'plugin' => 'BcCustomContent',
                'controller' => 'CustomEntries',
            ],
        ],
    ],
];
