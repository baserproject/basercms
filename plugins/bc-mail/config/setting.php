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
                        ]
                    ]
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
                            'prefix' => 'Admin',
                            'plugin' => 'BcMail',
                            'controller' => 'MailFields',
                            'action' => 'index'
                        ],
                        'add' => [
                            'prefix' => 'Api',
                            'plugin' => 'BcMail',
                            'controller' => 'MailContents',
                            'action' => 'add'
                        ],
                        'edit' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcMail',
                            'controller' => 'MailContents',
                            'action' => 'edit'
                        ],
                        'view' => [
                            'plugin' => 'BcMail',
                            'controller' => 'Mail',
                            'action' => 'index'
                        ],
                        'copy' => [
                            'prefix' => 'Api',
                            'plugin' => 'BcMail',
                            'controller' => 'MailContents',
                            'action' => 'copy'
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
