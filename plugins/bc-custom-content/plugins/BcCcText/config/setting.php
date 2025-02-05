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
     * - `fieldTypes`: カスタムフィールドのタイプの設定、プラグインごとに１つ定義でき、キーはプラグイン名と同じにする
     */
    'BcCustomContent' => [
        'fieldTypes' => [
            /**
             * BcCcText
             *
             * テキストボックスを表示するフィールドタイプ
             *
             * `category`: タイプのセレクトボックスにおけるグループ（基本|日付|選択|コンテンツ|その他）
             * `label`: 見出しラベル
             * `columnType`: DBのカラム型
             *      CakePHPの ConnectionManager で認識できるもので指定
             * `controlType`: コントロールのタイプ（検索時の条件生成などに利用）
             *      CakePHPの FormHelper で認識できるもので指定
             * `preview`: プレビューに対応しているかどうか（デフォルト false）
             * `useDefaultValue`: 初期値の利用（デフォルト true）
             * `useSize`: 横幅の利用（デフォルト false）
             * `useMaxLength`: 最大文字数の利用（デフォルト false）
             * `useAutoConvert`: 自動変換の利用（デフォルト false）
             * `useCounter`: カウンターの利用（デフォルト false）
             * `usePlaceholder`: プレースホルダーの利用（デフォルト false）
             * `useCheckEmail`: Eメール形式チェックの利用（デフォルト false）
             * `useCheckEmailConfirm`: Eメール比較チェックの利用（デフォルト false）
             * `useCheckNumber`: 数値チェックの利用（デフォルト false）
             * `useCheckHankaku`: 半角チェックの利用（デフォルト false）
             * `useZenkakuKatakana`: 全角カタカナチェックの利用（デフォルト false）
             * `useZenkakuHiragana`: 全角ひらがなチェックの利用（デフォルト false）
             * `useCheckDatetime`: 日付チェックの利用（デフォルト false）
             * `useCheckRegex`: 正規表現チェックの利用（デフォルト false）
             * `useCheckMaxFileSize`: ファイルアップロードサイズ制限の利用（デフォルト false）
             * `useCheckFileExt`: ファイル拡張子チェックの利用（デフォルト false）
             * `loop`: ループ機能に対応しているかどうか（デフォルト false）
             * `hasArchives` : アーカイブ機能に対応しているかどうか（デフォルト false）
             */
            'BcCcText' => [
                'category' => __d('baser_core', '基本'),
                'label' => __d('baser_core', 'テキスト'),
                'columnType' => 'string',
                'controlType' => 'text',
                'preview' => true,
                'useSize' => true,
                'useMaxLength' => true,
                'useAutoConvert' => true,
                'useCounter' => true,
                'usePlaceholder' => true,
                'useCheckEmail' => true,
                'useCheckEmailConfirm' => true,
                'useCheckNumber' => true,
                'useCheckHankaku' => true,
                'useCheckZenkakuKatakana' => true,
                'useCheckZenkakuHiragana' => true,
                'useCheckDatetime' => true,
                'useCheckRegex' => true,
                'useCheckMaxFileSize' => false,
                'useCheckFileExt' => false,
                'useDefaultValue' => true,
                'loop' => true
            ]
        ]
    ]
];
