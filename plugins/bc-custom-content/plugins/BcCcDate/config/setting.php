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
             * BcCcDate
             *
             * 日付（年月日）を表示するフィールドタイプ
             */
            'BcCcDate' => [
                'category' => __d('baser_core', '日付'),
                'label' => __d('baser_core', '日付（年月日）'),
                'columnType' => 'string',
                'controlType' => 'date',
                'preview' => true,
                'loop' => true
            ]
        ]
    ]
];
