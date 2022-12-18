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

namespace BcMail\View\Helper;

use BcMail\Model\Entity\MailField;
use Cake\View\Helper;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * メールフィールドヘルパー
 *
 * @package Mail.View.Helper
 *
 */
class MailfieldHelper extends Helper
{

    /**
     * htmlの属性を取得する
     *
     * @param array $data メールフィールドデータ
     * @return array HTML属性
     */
    public function getAttributes($data)
    {
        if (isset($data['MailField'])) {
            $data = $data['MailField'];
        }

        $attributes['size'] = $data['size'];
        $attributes['rows'] = $data['text_rows'];
        $attributes['maxlength'] = $data['maxlength'];
        $attributes['class'] = $data['class'];
        if ($data['type'] === 'multi_check') {
            $attributes['multiple'] = true;
        } elseif ($data['type'] === 'tel') {
            $attributes['type'] = 'tel';
        }
        if (!empty($data['options'])) {
            $options = preg_split('/(?<!\\\)\|/', $data['options']);
            /**
             * 引数のペアから連想配列を構築する
             *
             * Example:
             * `aa('a','b')`
             *
             * Would return:
             * `array('a'=>'b')`
             *
             * @return array Associative array
             */
            $options = call_user_func_array(function () {
                $args = func_get_args();
                $argc = count($args);
                for($i = 0; $i < $argc; $i++) {
                    if ($i + 1 < $argc) {
                        $a[$args[$i]] = $args[$i + 1];
                    } else {
                        $a[$args[$i]] = null;
                    }
                    $i++;
                }
                return $a;
            }, $options);
            $attributes = array_merge($attributes, $options);
        }
        return $attributes;
    }

    /**
     * コントロールのソースを取得する
     *
     * @param MailField $data メールフィールドデータ
     * @return array コントロールソース
     */
    public function getOptions($data)
    {
        if (!empty($data->source)) {
            if ($data->type !== "check") {
                $values = explode("\n", str_replace('|', "\n", $data->source));
                $source = [];
                foreach($values as $value) {
                    $source[$value] = $value;
                }
                return $source;
            }
        }
        return [];
    }

}
