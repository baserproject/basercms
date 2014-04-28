<?php

/**
 * Timeヘルパー拡張
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
App::uses('TimeHelper', 'View/Helper');

/**
 * Timeヘルパー拡張
 *
 * @package Baser.View.Helper
 */
class BcTimeHelper extends TimeHelper {

/**
 * 年号リスト
 *
 * @var array
 * @access public
 */
	public $nengos = array("m" => "明治", "t" => "大正", "s" => "昭和", "h" => "平成");

/**
 * 和暦文字列の正規表現
 *
 * @var string
 */
	public $warekiRegex = '!^(?<nengo>[mtsh])-(?<year>[0-9]{2})([/\-])(?<month>0?[0-9]|1[0-2])([/\-])(?<day>[0-2][0-9]|3[01])$!';

/**
 * 年号を取得
 *
 * @param string $w 年号のローマ字表記の頭文字 m (明治） / t（大正) / s（昭和） / h（平成）
 * @return string 年号をあらわすアルファベット
 * @access public
 */
	public function nengo($w) {
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
 * @return mixid string / false
 * @access public
 */
	public function wareki($date) {
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
 * @access public
 */
	public function wyear($date) {
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
 * @access public
 */
	public function convertToWarekiYear($year) {
		if ($year >= 1868 && $year <= 1911) {
			return array('m-' . ($year - 1867));
		} elseif ($year == 1912) {
			return array('m-' . ($year - 1867), 't-' . ($year - 1911));
		} elseif ($year >= 1913 && $year <= 1925) {
			return array('t-' . ($year - 1911));
		} elseif ($year == 1926) {
			return array('t-' . ($year - 1911), 's-' . ($year - 1925));
		} elseif ($year >= 1927 && $year <= 1988) {
			return array('s-' . ($year - 1925));
		} elseif ($year == 1989) {
			return array('s-' . ($year - 1925), 'h-' . ($year - 1988));
		} elseif ($year >= 1990) {
			return array('h-' . ($year - 1988));
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
 * @access public
 */
	public function convertToSeirekiYear($year) {
		if (strpos($year, '-') === false) {
			return false;
		}
		list($w, $year) = explode('-', $year);
		switch ($w) {
			case 'm':
				return $year + 1867;
			case 't':
				return $year + 1911;
			case 's':
				return $year + 1925;
			case 'h':
				return $year + 1988;
			default:
				return false;
		}
	}

/**
 * 和暦変換(配列で返す)
 *
 * @param string 日付
 * @return array 和暦データ
 * @access public
 */
	public function convertToWarekiArray($date) {
		if (!$date) {
			return '';
		}
		if (is_array($date)) {
			if (empty($date['year']) || empty($date['month']) || empty($date['day'])) {
				return '';
			}
			$date = $this->convertToSeirekiYear($date['year']) . '-' . $date['month'] . '-' . $date['day'];
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
		} elseif ($ymd >= "19890108") {
			$w = "h";
			$y = $y - 1988;
		}

		$dataWareki = array(
			'wareki' => true,
			'year' => $w . '-' . $y,
			'month' => $m,
			'day' => $d
		);

		return $dataWareki;
	}

/**
 * 和暦変換
 *
 * @param string $date 日付
 * @return string 和暦データ
 * @access public
 */
	public function convertToWareki($date) {

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
 * @access	public
 */
	public function minutes($strDate) {
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
 * @access public
 */
	public function format($format = 'Y-m-d', $date = null, $invalid = false, $userOffset = null) {
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
 * @access public
 */
	public function pastDays($date, $days, $now = null) {
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

}
