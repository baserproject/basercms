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

namespace BaserCore\View\Helper;

use BaserCore\Event\BcEventDispatcherTrait;
use Cake\View\Helper\HtmlHelper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Htmlヘルパーの拡張クラス
 *
 */
class BcHtmlHelper extends HtmlHelper
{

// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

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
     *  - `block` : ビューブロックを指定（初期値 : false）
     *  - `declaration` : var 宣言を行うかどうか（初期値 : true）
     * @checked
     * @noTodo
     * @unitTest
     */
    public function setScript($variable, $value, $options = []): string
    {
        if (isset($options['inline'])) {
            trigger_error('オプション inline は利用できなくなりました。 block を利用してください。', E_USER_WARNING);
        }
        $options = array_merge([
            'declaration' => true,
            'block' => true
        ], $options);
        $code = '';
        if ($options['declaration']) {
            $code = 'var ';
        }
        $code .= h($variable) . ' = ' . json_encode($value, JSON_UNESCAPED_UNICODE) . ";";
        unset($options['declaration'], $options['escape']);
        $result = $this->scriptBlock($code, $options);
        if (!$options['block']) {
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
        return $this->setScript('bcI18n', [], ['block' => false]);
    }

    /**
     * JavaScript に、翻訳データを引き渡す
     * `bcI18n.キー名` で参照可能
     * （例）bcI18n.alertMessage
     *
     * @param array $value 値（連想配列）
     *  - `inline` : インラインに出力するかどうか。（初期値 : false）
     */
    public function i18nScript($data, $options = [])
    {
        $options = array_merge([
            'block' => true
        ], $options);
        $result = '';
        if (is_array($data)) {
            $options['declaration'] = false;
            foreach($data as $key => $value) {
                $result .= $this->setScript('bcI18n.' . $key, $value, $options) . "\n";
            }
            if (!$options['block']) {
                return $result;
            }
        }
        return '';
    }
// <<<
}
