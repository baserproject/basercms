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
namespace BaserCore\Model\Validation;

use Cake\Validation\Validation;

/**
 * Class BcValidation
 * @package BaserCore\Model\Validation
 */
class BcValidation extends Validation {

    /**
     * 英数チェックプラス
     *
     * ハイフンアンダースコアを許容
     *
     * @param array $check チェック対象文字列
     * @param array $options 他に許容する文字列
     * @return boolean
     */
	public static function alphaNumericPlus($value, $context = []) {
		if (!$value) {
			return true;
		}
		if($context) {
            if (is_array($context)) {
                if(array_key_exists('data', $context)) {
                    $context = [];
                }
            } else {
                $context = [$context];
            }
            $context = preg_quote(implode('', $context), '/');
        }
		if (preg_match("/^[a-zA-Z0-9\-_" . $context . "]+$/", $value)) {
			return true;
		} else {
			return false;
		}
	}

    /**
     * ２つのフィールド値を確認する
     *
     * @param	array	$check 対象となる値
     * @param	mixed	$fields フィールド名
     * @return	boolean
     */
	public static function confirm($value, $fields, $context) {
		$value1 = $value2 = '';
		if (is_array($fields) && count($fields) > 1) {
			if (isset($context['data'][$fields[0]]) &&
				isset($context['data'][$fields[1]])) {
				$value1 = $context['data'][$fields[0]];
				$value2 = $context['data'][$fields[1]];
			}
		} elseif ($fields) {
			if (isset($value) && isset($context['data'][$fields])) {
				$value1 = $value;
				$value2 = $context['data'][$fields];
			}
		} else {
			return false;
		}
		if ($value1 != $value2) {
			return false;
		}
		return true;
	}

    /**
     * HABTM 用マルチチェックボックスの未選択チェック
     * @param mixed $value
     * @param array $context
     * @return bool
     */
    public static function notEmptyMultiple($value, $context) {
        if(isset($value['_ids'])) {
            $value = $value['_ids'];
        }
        if(!is_array($value)) {
            return false;
        }
        foreach($value as $v) {
            if($v) {
                return true;
            }
        }
        return false;
    }

}
