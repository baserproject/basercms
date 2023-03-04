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
     *
     * 各フィールドの設定値についての説明は、BcCcText プラグインの setting.php を参考にする
     */
    'BcCustomContent' => [
        'fieldTypes' => [
            /**
             * BcCcEmail
             *
             * E-mail用テキストボックスを表示するフィールドタイプ
             */
            'BcCcEmail' => [
                'category' => __d('baser_core', '基本'),
                'label' => __d('baser_core', 'Eメール'),
                'columnType' => 'string',
                'controlType' => 'text',
                'preview' => true,
                'useSize' => true,
                'useMaxLength' => true,
                'useAutoConvert' => true,
                'usePlaceholder' => true,
                'useCheckEmail' => true,
                'useCheckEmailConfirm' => true,
                'loop' => true
            ]
        ]
    ]
];
