<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) NPO baser foundation <https://baserfoundation.org/>
 *
 * @copyright     Copyright (c) NPO baser foundation
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.6
 * @license       https://basercms.net/license/index.html MIT License
 */

namespace BaserCore\Utility;

use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcText
 */
class BcText
{

    /**
     * 文字列よりスクリプトタグを除去する
     *
     * @param string $value
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public static function stripScriptTag($value)
    {
        $allows = [
            'a', 'abbr', 'address', 'area', 'b', 'blockquote', 'body', 'br', 'button', 'caption', 'cite', 'code',
            'col', 'colgroup', 'dd', 'del', 'dfn', 'div', 'dl', 'dt', 'em', 'fieldset', 'form', 'h1', 'h2', 'h3',
            'h4', 'h5', 'h6', 'hr', 'i', 'iframe', 'img', 'input', 'ins', 'kbd', 'label', 'legend', 'li', 'link',
            'map', 'meta', 'noscript', 'object', 'ol', 'optgroup', 'option', 'p', 'pre', 'q', 'samp', 'select',
            'small', 'span', 'strong', 'sub', 'sup', 'table', 'tbody', 'td', 'textarea', 'tfoot', 'th', 'thead',
            'title', 'tr', 'ul', 'var', 'style'
        ];
        return strip_tags($value, '<' . implode('><', $allows) . '>');
    }

}
