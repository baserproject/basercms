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
                        'title' => __d('baser_core', 'メール基本設定'),
                        'url' => [
                            'prefix' => 'Admin',
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
                    'title' => __d('baser_core', 'メールフォーム'),
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
                            'prefix' => 'Api/Admin',
                            'plugin' => 'BcMail',
                            'controller' => 'MailContents',
                            'action' => 'add',
                            '_ext' => 'json'
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
                            'prefix' => 'Api/Admin',
                            'plugin' => 'BcMail',
                            'controller' => 'MailContents',
                            'action' => 'copy',
                            '_ext' => 'json'
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
            'Mail.getForm'
        ]
    ],
    'BcMail' => [
        'autoComplete' => [
            ['name' => 'none', 'title' => '指定しない'],
            ['name' => 'off', 'title' => '無効'],
            ['name' => 'name', 'title' => '名前', 'child' => [
                ['name' => 'honorific-prefix', 'title' => '接頭語（Mr.,Mrs.等）'],
                ['name' => 'given-name', 'title' => '名前'],
                ['name' => 'additional-name', 'title' => 'ミドルネーム'],
                ['name' => 'family-name', 'title' => '名字'],
                ['name' => 'honorific-suffix', 'title' => '接尾語（Jr.等）'],
            ]],
            ['name' => 'nickname', 'title' => 'ニックネーム'],
            ['name' => 'organization-title', 'title' => '役職'],
            ['name' => 'username', 'title' => 'ユーザー名'],
            ['name' => 'new-password', 'title' => '新しいパスワード'],
            ['name' => 'current-password', 'title' => '現在のパスワード'],
            ['name' => 'one-time-code', 'title' => 'ワンタイムコード'],
            ['name' => 'organization', 'title' => '企業または団体の名前'],
            ['name' => 'street-address', 'title' => '住所', 'child' => [
                ['name' => 'street-address1', 'title' => '住所（1行目）'],
                ['name' => 'street-address2', 'title' => '住所（2行目）'],
                ['name' => 'street-address3', 'title' => '住所（3行目）'],
            ]],
            ['name' => 'address-level1', 'title' => '住所1（都道府県、州）'],
            ['name' => 'address-level2', 'title' => '住所2（市町村）'],
            ['name' => 'address-level3', 'title' => '住所3（3番目の行政レベル）'],
            ['name' => 'address-level4', 'title' => '住所4（もっとも細かい行政レベル）'],
            ['name' => 'country', 'title' => '国コード'],
            ['name' => 'country-name', 'title' => '国名'],
            ['name' => 'postal-code', 'title' => '郵便番号'],
            ['name' => 'cc-name', 'title' => 'クレジットカード名義', 'child' => [
                ['name' => 'cc-given-name', 'title' => 'クレジットカード名義（名前）'],
                ['name' => 'cc-additional-name', 'title' => 'クレジットカード名義（ミドルネーム）'],
                ['name' => 'cc-family-name', 'title' => 'クレジットカード名義（名字）'],
            ]],
            ['name' => 'cc-number', 'title' => 'クレジットカード番号'],
            ['name' => 'cc-exp', 'title' => 'クレジットカード有効期限', 'child' => [
                ['name' => 'cc-exp-month', 'title' => 'クレジットカード有効期限（月）'],
                ['name' => 'cc-exp-year', 'title' => 'クレジットカード有効期限（年）'],
            ]],
            ['name' => 'cc-csc', 'title' => 'クレジットカードセキュリティコード'],
            ['name' => 'cc-type', 'title' => 'クレジットカード種類'],
            ['name' => 'transaction-currency', 'title' => '決済通貨'],
            ['name' => 'transaction-amount', 'title' => '決済通貨の単位による量'],
            ['name' => 'language', 'title' => '言語'],
            ['name' => 'bday', 'title' => '生年月日', 'child' => [
                ['name' => 'bday-day', 'title' => '生年月日（日）'],
                ['name' => 'bday-month', 'title' => '生年月日（月）'],
                ['name' => 'bday-year', 'title' => '生年月日（年）'],
            ]],
            ['name' => 'sex', 'title' => '性別'],
            ['name' => 'url', 'title' => 'URL'],
            ['name' => 'photo', 'title' => '画像'],
            ['name' => 'tel', 'title' => '電話番号', 'child' => [
                ['name' => 'tel-country-code', 'title' => '国番号'],
                ['name' => 'tel-national', 'title' => '国際電話番号', 'child' => [
                    ['name' => 'tel-area-code', 'title' => '電話番号（市外局番）'],
                    ['name' => 'tel-local', 'title' => '国番号や市外局番を含まない電話番号', 'child' => [
                        ['name' => 'tel-local-prefix', 'title' => '電話番号（市内局番）'],
                        ['name' => 'tel-local-suffix', 'title' => '電話番号（加入者番号）'],
                    ]],
                ]],
            ]],
            ['name' => 'tel-extension', 'title' => '内線番号'],
            ['name' => 'email', 'title' => 'Eメールアドレス'],
            ['name' => 'impp', 'title' => 'インスタントメッセージングプロトコルの端点'],
            ['name' => 'on', 'title' => '自動設定'],
        ]
    ]
];
