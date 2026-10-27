<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.4.3
 * @license       https://basercms.net/license/index.html MIT License
 */

return [
    /**
     * カスタムコンテンツ設定
     *
     * - `fieldTypes`: カスタムフィールドのタイプの設定、プラグインごとに１つ定義でき、キーはプラグイン名と同じにする
     */
    'BcCustomContent' => [
        'fieldTypes' => [
            /**
             * BcCcGoogleMap
             *
             * Googleマップの座標（または座標を含むURL）を入力するフィールドタイプ
             *
             * 各設定項目の意味は BcCcText/config/setting.php を参照
             */
            'BcCcGoogleMap' => [
                'category' => __d('baser_core', 'その他'),
                'label' => __d('baser_core', 'Googleマップ'),
                'columnType' => 'text',
                'controlType' => 'text',
                'usePlaceholder' => true,
                'useSize' => true,
                'preview' => true,
            ]
        ]
    ]
];
