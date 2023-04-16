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

namespace BcMail\Model\Validation;

use Cake\Validation\Validation;
use BaserCore\Annotation\UnitTest;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\Checked;

/**
 * Class MailMessageValidation
 */
class MailMessageValidation extends Validation
{

    /**
     * 文字列日付チェック
     *
     * @return bool
     * @unitTest
     * @noTodo
     */
    public static function dateString(string $value): bool
    {
        // カレンダー入力利用時は yyyy/mm/dd で入ってくる
        // yyyy/mm/dd 以外の文字列入力も可能であり、そうした際は日付データとして 1970-01-01 となるため認めない
        $value = date('Y-m-d', strtotime($value));
        if ($value === '1970-01-01') return false;
        if (!self::checkDate($value)) return false;
        return true;
    }

    /**
     * 配列日付チェック
     *
     * @param $value
     * @return bool
     * @unitTest
     * @noTodo
     */
    public static function dateArray($value)
    {
        if (
            empty($value['year']) ||
            empty($value['month']) ||
            empty($value['day'])
        ) {
            return false;
        }
        return self::dateString($value['year'] . '-' . $value['month'] . '-' . $value['day']);
    }

    /**
     * 日付チェック
     *
     * @param $value
     * @return bool
     * @unitTest
     * @noTodo
     */
    public static function checkdate($value){
		if (!$value) return true;
		$time = '';
		if (strpos($value, ' ') !== false) {
			[$date, $time] = explode(' ', $value);
		} else {
			$date = $value;
		}
		if (DS != '\\') {
			if ($time) {
				if (!strptime($value, '%Y-%m-%d %H:%M')) return false;
			} else {
				if (!strptime($value, '%Y-%m-%d')) return false;
			}
		}
		[$Y, $m, $d] = explode('-', $date);
		if (checkdate($m, $d, $Y) !== true) return false;
		if ($time) {
			if (strpos($value, ':') !== false) {
				[$H, $i] = explode(':', $time);
				if (checktime($H, $i) !== true) return false;
			} else {
				return false;
			}
		}
		if (date('Y-m-d H:i:s', strtotime($value)) == '1970-01-01 09:00:00') return false;
		return true;
    }

    /**
     * 指定したターゲットのデータと同じかチェックする
     *
     * @param string $value
     * @param string $target
     * @param array $context
     * @return bool
     */
    public static function checkSame(string $value, string $target, array $context)
    {
        if(!isset($context['data'][$target])) return false;
        if($value !== $context['data'][$target]) return false;
        return true;
    }

}
