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

namespace BcThemeFile\Utility;

/**
 * BcThemeFileUtil
 */
class BcThemeFileUtil
{

    /**
     * テンプレートタイプの名称を取得する
     *
     * @param string $type
     * @return false|mixed
     */
    public static function getTemplateTypeName(string $type)
    {
        $templateTypes = [
            'layout' => __d('baser', 'レイアウトテンプレート'),
            'element' => __d('baser', 'エレメントテンプレート'),
            'email' => __d('baser', 'Eメールテンプレート'),
            'etc' => __d('baser', 'コンテンツテンプレート'),
            'css' => __d('baser', 'スタイルシート'),
            'js' => 'Javascript',
            'img' => __d('baser', 'イメージ')
        ];
        if (isset($templateTypes[$type])) {
            return $templateTypes[$type];
        } else {
            return false;
        }
    }

}
