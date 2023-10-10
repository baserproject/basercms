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
use Cake\View\Helper\TextHelper;
use BaserCore\Annotation\Checked;
use BaserCore\Annotation\NoTodo;
use BaserCore\Annotation\UnitTest;

/**
 * Class BcTextHelper
 */
class BcTextHelper extends TextHelper
{
// CUSTOMIZE ADD 2021/04/24 ryuring
// >>>

    /**
     * Trait
     */
    use BcEventDispatcherTrait;

// <<<

    /**
     * helpers
     *
     * @var array
     */
// CUSTOMIZE MODIFY 2014/07/03 ryuring
// >>>
//protected $helpers = ['Html'];
// ---
    protected $helpers = ['BaserCore.BcTime', 'BaserCore.BcForm', 'Html', 'BaserCore.BcAdminForm'];
// <<<

// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>
    /**
     * boolean型を ○ または ― マークで出力
     *
     * @param boolean $value
     * @return string ○ または ―
     * @checked
     * @noTodo
     */
    public function booleanMark($value)
    {
        if ($value) {
            return "○";
        } else {
            return "―";
        }
    }

    /**
     * boolean型用のリストを ○ ― マークで出力
     *
     * @return array マークリスト（ - ○ ）
     * @checked
     * @noTodo
     */
    public function booleanMarkList()
    {
        return [0 => "―", 1 => "○"];
    }

    /**
     * boolean型用のリストを「有」「無」で出力
     *
     * @return array 「有」「無」リスト
     * @checked
     * @noTodo
     */
    public function booleanExistsList()
    {
        return [0 => __d('baser_core', '無'), 1 => __d('baser_core', '有')];
    }

    /**
     * boolean型用のリストを可、不可で出力
     *
     * @return array 可/不可リスト
     * @checked
     * @noTodo
     */
    public function booleanAllowList()
    {
        return [0 => __d('baser_core', '不可'), 1 => __d('baser_core', '可')];
    }

    /**
     * boolean型用のリストを[〜する/〜しない]形式で出力する
     *
     * @param string $doText Do文字列
     * @return array [〜する/〜しない]形式のリスト
     * @checked
     * @noTodo
     */
    public function booleanDoList($doText = null)
    {
        return [
            0 => sprintf(__d('baser_core', '%s しない'), $doText),
            1 => sprintf(__d('baser_core', '%s する'), $doText)
        ];
    }

    /**
     * boolean型のデータを [〜する / 〜しない] 形式で出力する
     *
     * @param boolean $value 値
     * @param string $doText Do文字列
     * @return string
     * @checked
     * @noTodo
     */
    public function booleanDo($value, $doText = null)
    {
        $booleanDoList = $this->booleanDoList($doText);
        return $booleanDoList[$value];
    }

    /**
     * 都道府県のリストを出力
     *
     * @return array 都道府県リスト
     * @checked
     * @noTodo
     */
    public function prefList($empty = '')
    {
        if ($empty) {
            $pref = ["" => $empty];
        } elseif ($empty === false) {
            $pref = ["" => ""];
        } else {
            $pref = ["" => __d('baser_core', '都道府県')];
        }

        $pref = $pref + [
                1 => __d('baser_core', '北海道'), 2 => __d('baser_core', '青森県'), 3 => __d('baser_core', '岩手県'), 4 => __d('baser_core', '宮城県'), 5 => __d('baser_core', '秋田県'), 6 => __d('baser_core', '山形県'), 7 => __d('baser_core', '福島県'),
                8 => __d('baser_core', '茨城県'), 9 => __d('baser_core', '栃木県'), 10 => __d('baser_core', '群馬県'), 11 => __d('baser_core', '埼玉県'), 12 => __d('baser_core', '千葉県'), 13 => __d('baser_core', '東京都'), 14 => __d('baser_core', '神奈川県'),
                15 => __d('baser_core', '新潟県'), 16 => __d('baser_core', '富山県'), 17 => __d('baser_core', '石川県'), 18 => __d('baser_core', '福井県'), 19 => __d('baser_core', '山梨県'), 20 => __d('baser_core', '長野県'), 21 => __d('baser_core', '岐阜県'),
                22 => __d('baser_core', '静岡県'), 23 => __d('baser_core', '愛知県'), 24 => __d('baser_core', '三重県'), 25 => __d('baser_core', '滋賀県'), 26 => __d('baser_core', '京都府'), 27 => __d('baser_core', '大阪府'), 28 => __d('baser_core', '兵庫県'),
                29 => __d('baser_core', '奈良県'), 30 => __d('baser_core', '和歌山県'), 31 => __d('baser_core', '鳥取県'), 32 => __d('baser_core', '島根県'), 33 => __d('baser_core', '岡山県'), 34 => __d('baser_core', '広島県'), 35 => __d('baser_core', '山口県'),
                36 => __d('baser_core', '徳島県'), 37 => __d('baser_core', '香川県'), 38 => __d('baser_core', '愛媛県'), 39 => __d('baser_core', '高知県'), 40 => __d('baser_core', '福岡県'), 41 => __d('baser_core', '佐賀県'), 42 => __d('baser_core', '長崎県'),
                43 => __d('baser_core', '熊本県'), 44 => __d('baser_core', '大分県'), 45 => __d('baser_core', '宮崎県'), 46 => __d('baser_core', '鹿児島県'), 47 => __d('baser_core', '沖縄県')
            ];
        return $pref;
    }

    /**
     * 性別を出力
     *
     * @param array $value
     * @return string
     * @checked
     * @noTodo
     */
    public function sex($value = 1)
    {
        if (preg_match('/[1|2]/', $value)) {
            $sexes = [1 => __d('baser_core', '男'), 2 => __d('baser_core', '女')];
            return $sexes[$value];
        }
        return '';
    }

    /**
     * 郵便番号にハイフンをつけて出力
     *
     * @param string $value 郵便番号
     * @param string $prefix '〒'
     * @return string    〒マーク、ハイフン付きの郵便番号
     * @checked
     * @noTodo
     */
    public function zipFormat($value, $prefix = "〒 ")
    {
        if (preg_match('/-/', $value)) {
            return $prefix . $value;
        }
        $right = substr($value, 0, 3);
        $left = substr($value, 3, 4);

        return $prefix . $right . "-" . $left;
    }

    /**
     * 番号を都道府県に変換して出力
     *
     * @param int $value 都道府県番号
     * @param string $noValue 都道府県名
     * @return string 都道府県名
     * @checked
     * @noTodo
     */
    public function pref($value, $noValue = '')
    {
        if (!empty($value) && ($value >= 1 && $value <= 47)) {
            $list = $this->prefList();
            return $list[(int)$value];
        }
        return $noValue;
    }

    /**
     * データをチェックして空の場合に指定した値を返す
     *
     * @param mixed $value
     * @param mixed $noValue データが空の場合に返す値
     * @return mixed そのままのデータ/空の場合のデータ
     * @checked
     * @noTodo
     */
    public function noValue($value, $noValue)
    {
        if (!$value) {
            return $noValue;
        } else {
            return $value;
        }
    }

    /**
     * boolean型のデータを可、不可で出力
     *
     * 0 or 1 の int も許容する
     * 文字列を与えた場合には、不可を出力
     *
     * @param boolean $value
     * @return    string    可/不可
     * @checked
     * @noTodo
     */
    public function booleanAllow($value)
    {
        $list = $this->booleanAllowList();
        return $list[(int)$value];
    }

    /**
     * boolean型用を有無で出力
     *
     * @param boolean $value
     * @return string 有/無
     * @checked
     * @noTodo
     */
    public function booleanExists($value)
    {
        $list = $this->booleanExistsList();
        return $list[(int)$value];
    }

    /**
     * 通貨表示
     *
     * @param int $value 通貨となる数値
     * @param string $prefix '¥'
     * @return string
     * @checked
     * @noTodo
     */
    public function moneyFormat($value, $prefix = '¥')
    {
        if (!is_numeric($value)) {
            return false;
        }
        if ($value) {
            return $prefix . number_format($value);
        } else {
            return '';
        }
    }

    /**
     * 配列形式の日付データを文字列データに変換する
     *
     * 配列形式のデータは、FormHelper::dateTime()で取得できる
     *
     * @param array $arrDate
     *    - `year` : 年
     *    - `month` : 月
     *    - `day` : 日
     * @return string 日付（例）2015/8/11
     * @checked
     * @noTodo
     */
    public function dateTime($arrDate)
    {
        if (!isset($arrDate['year']) || !isset($arrDate['month']) || !isset($arrDate['day'])) {
            return '';
        }
        return $arrDate['year'] . "/" . $arrDate['month'] . "/" . $arrDate['day'];
    }

    /**
     * 文字をフォーマット形式で出力し、値が存在しない場合は初期値を出力する
     *
     * @param string $format フォーマット文字列（sprintfで利用できるもの）
     * @param mixed $value フォーマット対象の値
     * @param mixed $noValue データがなかった場合の初期値
     * @return    string    変換後の文字列
     * @checked
     * @noTodo
     */
    public function format($format, $value, $noValue = '')
    {
        if ($value === '' || is_null($value)) {
            return $noValue;
        } else {
            return sprintf($format, $value);
        }
    }

    /**
     * モデルのコントロールソースより表示用データを取得する
     *
     * @param string $field フィールド名
     * @param mixed $value 値
     * @return string 表示用データ
     * @checked
     * @noTodo
     */
    public function listValue($field, $value)
    {
        $list = $this->BcAdminForm->getControlSource($field);
        if ($list && isset($list[$value])) {
            return $list[$value];
        } else {
            return false;
        }
    }

    /**
     * 配列とキーを指定して値を取得する
     *
     * @param int $key 配列のキー
     * @param array $array 配列
     * @param mixed type $noValue 値がない場合に返す値
     * @return mixed
     * @checked
     * @noTodo
     */
    public function arrayValue($key, $array, $noValue = '')
    {
        if (is_numeric($key)) {
            $key = (int)$key;
        }
        if (isset($array[$key])) {
            return $array[$key];
        }
        return $noValue;
    }

    /**
     * 連想配列とキーのリストより値のリストを取得し文字列で返す
     * 文字列に結合する際、指定した結合文字を指定できる
     *
     * @param string $glue 結合文字
     * @param array $keys 結合対象のキーのリスト
     * @param array $array リスト
     * @return string
     * @checked
     * @noTodo
     */
    public function arrayValues($glue, $keys, $array)
    {
        $values = [];
        foreach($keys as $key) {
            if (isset($array[$key])) {
                $values[] = $array[$key];
            }
        }
        if ($values) {
            return implode($glue, $values);
        } else {
            return '';
        }
    }

    /**
     * 日付より年齢を取得する
     *
     * @param string $birthday
     * @param string $suffix
     * @param mixed $noValue
     * @return mixed
     * @checked
     * @noTodo
     */
    public function age($birthday, $suffix = '歳', $noValue = '不明')
    {
        if (!$birthday) {
            return $noValue;
        }
        $byear = (int) date('Y', strtotime($birthday));
        $bmonth = (int) date('m', strtotime($birthday));
        $bday = (int) date('d', strtotime($birthday));
        $tyear = (int) date('Y');
        $tmonth = (int) date('m');
        $tday = (int) date('d');
        $age = $tyear - $byear;
        if (($tmonth * 100 + $tday) < ($bmonth * 100 + $bday)) {
            $age--;
        }
        return $age . $suffix;
    }

    /**
     * boolean型用のリストを有効、無効で出力
     *
     * @return array 可/不可リスト
     * @checked
     * @noTodo
     */
    public function booleanStatusList()
    {
        return [0 => __d('baser_core', '無効'), 1 => __d('baser_core', '有効')];
    }

    /**
     * boolean型用を無効・有効で出力
     *
     * @param boolean
     * @return string 無効/有効
     * @checked
     * @noTodo
     */
    public function booleanStatus($value)
    {
        $list = $this->booleanStatusList();
        return $list[(int)$value];
    }
// <<<

}
