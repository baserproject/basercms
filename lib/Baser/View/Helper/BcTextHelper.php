<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.View.Helper
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('TextHelper', 'View/Helper');
App::uses('BcTimeHelper', 'View/Helper');

/**
 * Textヘルパー拡張
 *
 * @package Baser.View.Helper
 */
class BcTextHelper extends TextHelper
{

	/**
	 * helpers
	 *
	 * @var array
	 */
	// CUSTOMIZE MODIFY 2014/07/03 ryuring
	// >>>
	//public $helpers = array('Html');
	// ---
	public $helpers = ['BcTime', 'BcForm', 'Html'];
	// <<<

// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>
	/**
	 * boolean型を ○ または ― マークで出力
	 *
	 * @param boolean $value
	 * @return string ○ または ―
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
	 */
	public function booleanMarkList()
	{
		return [0 => "―", 1 => "○"];
	}

	/**
	 * boolean型用のリストを「有」「無」で出力
	 *
	 * @return array 「有」「無」リスト
	 */
	public function booleanExistsList()
	{
		return [0 => __d('baser', '無'), 1 => __d('baser', '有')];
	}

	/**
	 * boolean型用のリストを可、不可で出力
	 *
	 * @return array 可/不可リスト
	 */
	public function booleanAllowList()
	{
		return [0 => __d('baser', '不可'), 1 => __d('baser', '可')];
	}

	/**
	 * boolean型用のリストを[〜する/〜しない]形式で出力する
	 *
	 * @param string $doText Do文字列
	 * @return array [〜する/〜しない]形式のリスト
	 */
	public function booleanDoList($doText = null)
	{
		return [
			0 => sprintf('%s しない', $doText),
			1 => sprintf('%s する', $doText)
		];
	}

	/**
	 * boolean型のデータを [〜する / 〜しない] 形式で出力する
	 *
	 * @param boolean $value 値
	 * @param string $doText Do文字列
	 * @return string
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
	 */
	public function prefList($empty = '')
	{
		if ($empty) {
			$pref = ["" => $empty];
		} elseif ($empty === false) {
			$pref = ["" => ""];
		} else {
			$pref = ["" => __d('default', '都道府県')];
		}

		// ※ メールフォームなどフロントエンド側でも利用するので言語リソースをbaser.poではなくdefault.poに記載
		$pref = $pref + [
				1 => __d('default', '北海道'), 2 => __d('default', '青森県'), 3 => __d('default', '岩手県'), 4 => __d('default', '宮城県'), 5 => __d('default', '秋田県'), 6 => __d('default', '山形県'), 7 => __d('default', '福島県'),
				8 => __d('default', '茨城県'), 9 => __d('default', '栃木県'), 10 => __d('default', '群馬県'), 11 => __d('default', '埼玉県'), 12 => __d('default', '千葉県'), 13 => __d('default', '東京都'), 14 => __d('default', '神奈川県'),
				15 => __d('default', '新潟県'), 16 => __d('default', '富山県'), 17 => __d('default', '石川県'), 18 => __d('default', '福井県'), 19 => __d('default', '山梨県'), 20 => __d('default', '長野県'), 21 => __d('default', '岐阜県'),
				22 => __d('default', '静岡県'), 23 => __d('default', '愛知県'), 24 => __d('default', '三重県'), 25 => __d('default', '滋賀県'), 26 => __d('default', '京都府'), 27 => __d('default', '大阪府'), 28 => __d('default', '兵庫県'),
				29 => __d('default', '奈良県'), 30 => __d('default', '和歌山県'), 31 => __d('default', '鳥取県'), 32 => __d('default', '島根県'), 33 => __d('default', '岡山県'), 34 => __d('default', '広島県'), 35 => __d('default', '山口県'),
				36 => __d('default', '徳島県'), 37 => __d('default', '香川県'), 38 => __d('default', '愛媛県'), 39 => __d('default', '高知県'), 40 => __d('default', '福岡県'), 41 => __d('default', '佐賀県'), 42 => __d('default', '長崎県'),
				43 => __d('default', '熊本県'), 44 => __d('default', '大分県'), 45 => __d('default', '宮崎県'), 46 => __d('default', '鹿児島県'), 47 => __d('default', '沖縄県')
			];
		return $pref;
	}

	/**
	 * 性別を出力
	 *
	 * @param array $value
	 * @return string
	 */
	public function sex($value = 1)
	{
		if (preg_match('/[1|2]/', $value)) {
			$sexes = [1 => __d('baser', '男'), 2 => __d('baser', '女')];
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
	 * @access    public
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
	 * @access    public
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
	 * @access    public
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
	 */
	public function booleanExists($value)
	{
		$list = $this->booleanExistsList();
		return $list[(int)$value];
	}

	/**
	 * 配列形式の和暦データを文字列データに変換する
	 *
	 * FormHelper::dateTime() で取得した配列データを
	 * BcTimeHelper::convertToWarekiArray() で配列形式の和暦データに変換したものを利用する
	 *
	 * @param array $arrDate
	 *    − `wareki`:和暦に変換する場合は、trueを設定、設定しない場合何も返さない
	 *    - `year` :和暦のキーを付与した年。
	 *        r: 令和 / h: 平成 / s: 昭和 / t: 大正 / m: 明治
	 *        （例）h-27
	 *    - `month` : 月
	 *    - `day` : 日
	 * @return string 和暦（例）平成 27年 8月 11日
	 */
	public function dateTimeWareki($arrDate)
	{
		if (!is_array($arrDate)) {
			return;
		}
		if (!$arrDate['wareki'] || !$arrDate['year'] || !$arrDate['month'] || !$arrDate['day']) {
			return;
		}
		list($w, $year) = explode('-', $arrDate['year']);
		$wareki = $this->BcTime->nengo($w);
		return $wareki . " " . $year . "年 " . $arrDate['month'] . "月 " . $arrDate['day'] . '日';
	}

	/**
	 * 通貨表示
	 *
	 * @param int $value 通貨となる数値
	 * @param string $prefix '¥'
	 * @return string
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
	 */
	public function dateTime($arrDate)
	{
		if (!isset($arrDate['year']) || !isset($arrDate['month']) || !isset($arrDate['day'])) {
			return;
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
	 * @access    public
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
	 */
	public function listValue($field, $value)
	{
		$list = $this->BcForm->getControlSource($field);
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
	 */
	public function age($birthday, $suffix = '歳', $noValue = '不明')
	{
		if (!$birthday) {
			return $noValue;
		}
		$byear = date('Y', strtotime($birthday));
		$bmonth = date('m', strtotime($birthday));
		$bday = date('d', strtotime($birthday));
		$tyear = date('Y');
		$tmonth = date('m');
		$tday = date('d');
		$age = $tyear - $byear;
		if ($tmonth * 100 + $tday < $bmonth * 100 + $bday) {
			$age--;
		}

		return $age . $suffix;
	}

	/**
	 * boolean型用のリストを有効、無効で出力
	 *
	 * @return array 可/不可リスト
	 */
	public function booleanStatusList()
	{
		return [0 => __d('baser', '無効'), 1 => __d('baser', '有効')];
	}

	/**
	 * boolean型用を無効・有効で出力
	 *
	 * @param boolean
	 * @return string 無効/有効
	 */
	public function booleanStatus($value)
	{
		$list = $this->booleanStatusList();
		return $list[(int)$value];
	}
// <<<
}
