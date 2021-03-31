<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS User Community <https://basercms.net/community/>
 *
 * @copyright     Copyright (c) baserCMS User Community
 * @link          https://basercms.net baserCMS Project
 * @since         5.0.0
 * @license       http://basercms.net/license/index.html MIT License
 */

namespace BaserCore\View\Helper;

use Cake\View\Helper\HtmlHelper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Htmlヘルパーの拡張クラス
 *
 * @package Baser.View.Helper
 */
class BcHtmlHelper extends HtmlHelper
{

// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>
    /**
     * Included helpers.
     *
     * @var array
     */
    public $helpers = ['Url'];

    /**
     * タグにラッピングされていないパンくずデータを取得する
     * @return array
     */
    public function getStripCrumbs()
    {
        return $this->_crumbs;
    }

    /**
     * JavaScript に変数を引き渡す
     *
     * @param string $variable 変数名（グローバル変数）
     * @param mixed $value 値
     * @param array $options
     *  - `inline` : インラインに出力するかどうか。（初期値 : false）
     *  - `declaration` : var 宣言を行うかどうか（初期値 : true）
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setScript($variable, $value, $options = [])
    {
        $options = array_merge([
            'inline' => false,
            'declaration' => true
        ], $options);
        $code = '';
        if ($options['declaration']) {
            $code = 'var ';
        }
        $code .= h($variable) . ' = ' . json_encode($value) . ';';
        if (!$options['inline']) {
            $options['block'] = 'script';
            $isInline = false;
        } else {
            $isInline = true;
        }
        unset($options['declaration'], $options['escape'], $options['inline']);
        $result = $this->scriptBlock($code, $options);
        if ($isInline) {
            return $result;
        }
        return '';
    }

    /**
     * i18n 用の変数を宣言する
     * @return string
     * @checked
     * @noTodo
     * @unitTest
     */
    public function declarationI18n()
    {
        return $this->setScript('bcI18n', [], ['inline' => true]);
    }

    /**
     * JavaScript に、翻訳データを引き渡す
     * `bcI18n.キー名` で参照可能
     * （例）bcI18n.alertMessage
     *
     * @param array $value 値（連想配列）
     *  - `inline` : インラインに出力するかどうか。（初期値 : false）
     *  - `declaration` : var 宣言を行うかどうか（初期値 : false）
     *  - `escape` : 値のエスケープを行うかどうか（初期値 : true）
     */
    public function i18nScript($data, $options = [])
    {
        $options = array_merge([
            'inline' => false,
            'declaration' => false,
            'escape' => true
        ], $options);
        if (is_array($data)) {
            $result = '';
            foreach($data as $key => $value) {
                $result .= $this->setScript('bcI18n.' . $key, $value, $options) . "\n";
            }
            if ($options['inline']) {
                return $result;
            }
        }
        return '';
    }
// <<<
}
