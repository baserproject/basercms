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
         * メールコンテンツ系のメニューは BcMailViewEventListener::beforeRender() にて実装
         */
        'adminNavigation' => [
            'Plugins' => [
                'menus' => [
                    'MailConfigs' => [
                        'title' => __d('baser', 'メール基本設定'),
                        'url' => [
                            'Admin' => true,
                            'plugin' => 'BcMail',
                            'controller' => 'MailConfigs',
                            'action' => 'index'
                        ]],
                ]
            ]
        ]
    ],
    /**
     * コンテンツ管理設定
     */
    'BcContents' => [
        'items' => [
            'BcMail' => [
                'MailContent' => [
                    'title' => __d('baser', 'メールフォーム'),
                    'multiple' => true,
                    'preview' => true,
                    'icon' => 'bca-icon--mail',
                    'routes' => [
                        'manage' => [
                            'admin' => true,
                            'plugin' => 'mail',
                            'controller' => 'mail_fields',
                            'action' => 'index'
                        ],
                        'add' => [
                            'admin' => true,
                            'plugin' => 'mail',
                            'controller' => 'mail_contents',
                            'action' => 'ajax_add'
                        ],
                        'edit' => [
                            'admin' => true,
                            'plugin' => 'mail',
                            'controller' => 'mail_contents',
                            'action' => 'edit'
                        ],
                        'delete' => [
                            'admin' => true,
                            'plugin' => 'mail',
                            'controller' => 'mail_contents',
                            'action' => 'delete'
                        ],
                        'view' => [
                            'plugin' => 'mail',
                            'controller' => 'mail',
                            'action' => 'index'
                        ],
                        'copy' => [
                            'admin' => true,
                            'plugin' => 'mail',
                            'controller' => 'mail_contents',
                            'action' => 'ajax_copy'
                        ]
                    ]
                ]
            ]
        ]
    ],
    /**
     * ショートコード
     */
    'BcShortCode' => [
        'BcMail' => [
            'BcMail.getForm'
        ]
    ]
];
