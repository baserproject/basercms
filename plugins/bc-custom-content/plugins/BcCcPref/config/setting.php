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
             * BcCcPref
             *
             * 都道府県リストを表示するフィールドタイプ
             */
            'BcCcPref' => [
                'category' => '選択',
                'label' => '都道府県リスト',
                'columnType' => 'string',
                'controlType' => 'select',
                'preview' => true,
                'loop' => true
            ]
        ]
    ]
];
