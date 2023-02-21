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
             * BcCcPassword
             *
             * パスワード用テキストボックスを表示するフィールドタイプ
             */
            'BcCcPassword' => [
                'category' => '基本',
                'label' => 'パスワード',
                'columnType' => 'string',
                'controlType' => 'password',
                'preview' => true,
                'useSize' => true,
                'useMaxLength' => true,
                'useAutoConvert' => true,
                'useCounter' => true,
                'usePlaceholder' => true,
                'useCheckRegex' => true,
                'loop' => true
            ]
        ]
    ]
];
