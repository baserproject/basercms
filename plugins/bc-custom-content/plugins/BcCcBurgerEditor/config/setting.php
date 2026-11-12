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
    /**
     * カスタムコンテンツ設定
     */
    'BcCustomContent' => [
        'fieldTypes' => [
            'BcCcBurgerEditor' => [
                'category' => __d('baser_core', 'コンテンツ'),
                'label' => __d('baser_core', 'BurgerEditor'),
                'columnType' => 'text',
                'controlType' => 'text',
                'showHeading' => false,
                'onlyOneOnTable' => true,
                'preview' => false
            ]
        ]
    ]
];
