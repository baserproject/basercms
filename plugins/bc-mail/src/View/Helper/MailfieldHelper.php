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

use Cake\View\Helper;

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
        $attributes['rows'] = $data['rows'];
        $attributes['maxlength'] = $data['maxlength'];
        $attributes['separator'] = $data['separator'];
        $attributes['class'] = $data['class'];
        if ($data['type'] === 'multi_check') {
            $attributes['multiple'] = true;
        } elseif ($data['type'] === 'tel') {
            $attributes['type'] = 'tel';
        }
        if (!empty($data['options'])) {
            $options = preg_split('/(?<!\\\)\|/', $data['options']);
            $options = call_user_func_array('aa', $options);
            $attributes = am($attributes, $options);
        }
        return $attributes;
    }

    /**
     * コントロールのソースを取得する
     *
     * @param array $data メールフィールドデータ
     * @return array コントロールソース
     */
    public function getOptions($data)
    {
        if (isset($data['MailField'])) {
            $data = $data['MailField'];
        }
        if (!empty($data['source'])) {
            if ($data['type'] !== "check") {
                $values = explode("\n", str_replace('|', "\n", $data['source']));
                $source = [];
                foreach ($values as $value) {
                    $source[$value] = $value;
                }
                return $source;
            }
        }
        return [];
    }
}
