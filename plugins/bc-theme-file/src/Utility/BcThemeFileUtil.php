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

use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

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
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function getTemplateTypeName(string $type)
    {
        $templateTypes = [
            'layout' => __d('baser_core', 'レイアウトテンプレート'),
            'element' => __d('baser_core', 'エレメントテンプレート'),
            'email' => __d('baser_core', 'Eメールテンプレート'),
            'etc' => __d('baser_core', 'コンテンツテンプレート'),
            'css' => __d('baser_core', 'スタイルシート'),
            'js' => 'Javascript',
            'img' => __d('baser_core', 'イメージ')
        ];
        if (isset($templateTypes[$type])) {
            return $templateTypes[$type];
        } else {
            return false;
        }
    }

}
