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

App::uses('TimeHelper', 'View/Helper');

/**
 * Timeヘルパー拡張
 *
 * @package Baser.View.Helper
 */
class BcTimeHelper extends TimeHelper
{

// CUSTOMIZE ADD 2014/07/03 ryuring
// >>>
	/**
	 * 年号リスト
	 *
	 * @var array
	 */
	public $nengos = ["m" => "明治", "t" => "大正", "s" => "昭和", "h" => "平成", "r" => "令和"];

	/**
	 * 日本語曜日リスト
	 *
	 * @var array
	 */
	public $jpWeekList = [0 => '日', 1 => '月', 2 => '火', 3 => '水', 4 => '木', 5 => '金', 6 => '土', 7 => '日'];

	/**
	 * 和暦文字列の正規表現
	 *
	 * @var string
	 */
	public $warekiRegex = '!^(?<nengo>[mtshr])-(?<year>[0-9]{1,2})([/\-])(?<month>0?[0-9]|1[0-2])([/\-])(?<day>[0-2][0-9]|3[01])$!';

	/**
	 * 年号を取得
	 *
	 * @param string $w 年号のローマ字表記の頭文字 m (明治） / t（大正) / s（昭和） / h（平成） / r（令和）
	 * @return string 年号をあらわすアルファベット
	 */
	public function nengo($w)
	{
		if (isset($this->nengos[$w])) {
			return $this->nengos[$w];
		} else {
			return false;
		}
	}

	/**
	 * 和暦を取得（アルファベット）
	 *
	 * @param string $date 和暦を表す日付文字列（s-48/5/10）
	 * @return mixid 和暦　or false
	 */
	public function wareki($date)
	{
		if (!preg_match($this->warekiRegex, $date, $matches)) {
			return false;
		}
		return $matches['nengo'];
	}

	/**
	 * 和暦の年を取得
	 *
	 * @param string $date 和暦を表す日付文字列（s-48/5/10）
	 * @return mixid int / false
	 */
	public function wyear($date)
	{
		if (!preg_match($this->warekiRegex, $date, $matches)) {
			return false;
		}
		return $matches['year'];
	}

	/**
	 * 西暦を和暦の年に変換する
	 * 西暦をまたがる場合があるので配列で返す
	 *
	 * @param int $year
	 * @return array
	 */
	public function convertToWarekiYear($year)
	{
		if ($year >= 1868 && $year <= 1911) {
			return ['m-' . ($year - 1867)];
		} elseif ($year == 1912) {
			return ['m-' . ($year - 1867), 't-' . ($year - 1911)];
		} elseif ($year >= 1913 && $year <= 1925) {
			return ['t-' . ($year - 1911)];
		} elseif ($year == 1926) {
			return ['t-' . ($year - 1911), 's-' . ($year - 1925)];
		} elseif ($year >= 1927 && $year <= 1988) {
			return ['s-' . ($year - 1925)];
		} elseif ($year == 1989) {
			return ['s-' . ($year - 1925), 'h-' . ($year - 1988)];
		} elseif ($year >= 1990 && $year <= 2018) {
			return ['h-' . ($year - 1988)];
		} elseif ($year == 2019) {
			return ['h-' . ($year - 1988), 'r-' . ($year - 2018)];
		} elseif ($year >= 2020) {
			return ['r-' . ($year - 2018)];
		} else {
			return false;
		}
	}

	/**
	 * 和暦の年を西暦に変換する
	 * 和暦のフォーマット例：s-48
	 *
	 * @param string $year
	 * @return int
	 */
	public function convertToSeirekiYear($year)
	{
		if (strpos($year, '-') === false) {
			return false;
		}
		list($w, $year) = explode('-', $year);
		switch($w) {
			case 'm':
				return $year + 1867;
			case 't':
				return $year + 1911;
			case 's':
				return $year + 1925;
			case 'h':
				return $year + 1988;
			case 'r':
				return $year + 2018;
			default:
				return false;
		}
	}

	/**
	 * 日付を配列に分解した形で和暦変換する
	 *
	 * @param string|array $date 文字列形式の日付 (例: '2018/05/28')、または配列形式の和暦データ
	 * @return array|string 配列形式の和暦データ、または日付フォーマットが正しくない場合は空文字
	 */
	public function convertToWarekiArray($date)
	{
		if (!$date) {
			return '';
		}
		if (is_array($date)) {
			if (empty($date['year']) || empty($date['month']) || empty($date['day'])) {
				return '';
			}
			if (strpos($date['year'], '-') === false) {
				$date = $date['year'] . '-' . $date['month'] . '-' . $date['day'];
			} else {
				$date = $this->convertToSeirekiYear($date['year']) . '-' . $date['month'] . '-' . $date['day'];
			}
		}

		$time = strtotime($date);
		if ($time === false) {
			return '';
		}

		$ymd = date('Ymd', $time);
		$y = date('Y', $time);
		$m = date('m', $time);
		$d = date('d', $time);

		if ($ymd <= "19120729") {
			$w = "m";
			$y = $y - 1867;
		} elseif ($ymd >= "19120730" && $ymd <= "19261224") {
			$w = "t";
			$y = $y - 1911;
		} elseif ($ymd >= "19261225" && $ymd <= "19890107") {
			$w = "s";
			$y = $y - 1925;
		} elseif ($ymd >= "19890108" && $ymd <= "20190430") {
			$w = "h";
			$y = $y - 1988;
		} elseif ($ymd >= "20190501") {
			$w = "r";
			$y = $y - 2018;
		}

		$dataWareki = [
			'wareki' => true,
			'year' => $w . '-' . $y,
			'month' => $m,
			'day' => $d
		];

		return $dataWareki;
	}

	/**
	 * 和暦変換
	 *
	 * @param string $date 日付
	 * @return string 和暦データ
	 */
	public function convertToWareki($date)
	{

		// add start yuse@gmail.com
		// 配列形式の場合は、YMDが揃っていない場合も変換を走らせる為、
		// Yがある場合、MDが空でもセットする。
		if (is_array($date)) {
			if (!empty($date['year'])) {
				if (empty($date['month'])) {
					$date['month'] = "01";
				}
				if (empty($date['day'])) {
					$date['day'] = "01";
				}
			}
		}
		// add end
		$dateArray = $this->convertToWarekiArray($date);
		if (is_array($dateArray) && !empty($dateArray)) {
			return $dateArray['year'] . '/' . $dateArray['month'] . '/' . $dateArray['day'];
		} else {
			return '';
		}
	}

	/**
	 * 文字列から時間（分）を取得
	 *
	 * @param string $strDate 日時
	 * @return mixed 分/null
	 */
	public function minutes($strDate)
	{
		$time = strtotime($strDate, 0);
		$minutes = $time / 60;
		if ($minutes) {
			return $minutes . '分';
		} else {
			return null;
		}
	}

	/**
	 * format 拡張
	 *
	 * @param array $format
	 * @param string $date String Datetime string
	 * @param boolean $invalid flag to ignore results of fromString == false
	 * @param int $userOffset User's offset from GMT (in hours)
	 * @return string Formatted date string
	 */
	public function format($format = 'Y-m-d', $date = null, $invalid = false, $userOffset = null)
	{
		if ($date !== "00:00:00" && (!$date || $date == '0000-00-00 00:00:00')) {
			return "";
		}
		return parent::format($format, $date, $invalid, $userOffset);
	}

	/**
	 * 指定した日数が経過しているか確認する
	 * 経過していない場合はtrueを返す
	 * 日付が確認できなかった場合もtrueを返す
	 *
	 * @param string $date 日付
	 * @param int $days 経過日数
	 * @return boolean 経過有無
	 */
	public function pastDays($date, $days, $now = null)
	{
		if (is_null($now)) {
			$now = time();
		}
		if (!$date) {
			return true;
		}
		$pastDateTime = strtotime($date);
		if ($pastDateTime === false) {
			return true;
		}
		if ($now > strtotime($days . 'days', $pastDateTime)) {
			return true;
		}
		return false;
	}

	/**
	 * 日本の曜日名を1文字 + $suffixの形式で取得する
	 * - 引数により、指定しない場合は本日の曜日
	 * - 文字列で、strtotime関数で解析可能な場合は解析された日付の曜日
	 *
	 * @param string $dataStr (null|string) 日付文字列 "+1 day" / "YYYY/MM/DD"など
	 * @param type $suffix 接尾語(曜日 など)
	 * @return string 曜日 | 空白
	 */
	public function getJpWeek($dateStr = null, $suffix = '')
	{
		// nullの場合本日の曜日を取得
		if ($dateStr === null) {
			return $this->jpWeekList[date('w')] . $suffix;
		}

		// 日付として解析出来る場合
		if (strtotime($dateStr)) {
			return $this->jpWeekList[date('w', strtotime($dateStr))] . $suffix;
		}

		// 解析できなかった場合
		return '';
	}

	/**
	 * 曜日情報を出力する
	 * - 曜日情報が正しく取得できない場合は接尾辞も表示しない
	 * - ex) <?php $this->BcTime->jpWeek($post['posts_date'], '曜日'); ?>
	 *
	 * @param string $dateStr getJpWeek参照
	 * @param string $suffix getJpWeek参照
	 */
	public function jpWeek($dateStr = null, $suffix = '')
	{
		echo $this->getJpWeek($dateStr, $suffix);
	}


// <<<
}
