<?php
return [
    /**
     * コンテンツ設定
     */
    'BcContents' => [
        'items' => [
            'BcContentLink' => [
                'ContentLink' => [
                    'multiple' => true,
                    'title' => __d('baser', 'リンク'),
                    'omitViewAction' => true,
                    'routes' => [
                        'add' => [
                            'prefix' => 'Api',
                            'plugin' => 'BcContentLink',
                            'controller' => 'ContentLinks',
                            'action' => 'add'
                        ],
                        'edit' => [
                            'prefix' => 'Admin',
                            'plugin' => 'BcContentLink',
                            'controller' => 'ContentLinks',
                            'action' => 'edit'
                        ],
                        'view' => [
                            'plugin' => 'BcContentLink',
                            'controller' => 'ContentLinks',
                            'action' => 'view'
                        ]
                    ],
                    'icon' => 'bca-icon--link',
                ]
            ]
        ]
    ]
];
