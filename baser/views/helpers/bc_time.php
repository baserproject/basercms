<?php
/* SVN FILE: $Id$ */
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
 * @package			baser.view.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import("Helper","time");
/**
 * Timeヘルパー拡張
 *
 * @package baser.views.helpers
 */
class BcTimeHelper extends TimeHelper {
/**
 * 年号リスト
 *
 * @var array
 * @access public
 */
	var $nengos = array("m"=>"明治","t"=>"大正","s"=>"昭和","h"=>"平成");
/**
 * 年号を取得
 *
 * @param string $w
 * @return string 年号をあらわすアルファベット
 * @access public
 */
	function nengo($w) {
		
		if(isset($this->nengos[$w])) {
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
	function wareki($date) {

		$_date = explode('/', $date);
		if(!$_date) {
			$_date = explode('-', $date);
		}
		if(count($_date)==3) {
			$wyear = explode('-',$_date[0]);
			if(isset($wyear[0])) {
				return $wyear[0];
			} else {
				return false;
			}
		} elseif(count($_date)==4) {
			return $_date[0];
		} else {
			return false;
		}

	}
/**
 * 和暦の年を取得
 *
 * @param string $date 和暦を表す日付文字列（s-48/5/10）
 * @return mixid int / false
 * @access public
 */
	function wyear($date) {
		
		$_date = explode('/', $date);
		if(!$_date) {
			$_date = explode('-', $date);
		}
		if(count($_date)==3) {
			$wyear = explode('-',$_date[0]);
			if(isset($wyear[1])) {
				return $wyear[1];
			} else {
				return false;
			}
		} elseif(count($_date)==4) {
			return $_date[1];
		} else {
			return false;
		}
		
	}
/**
 * 西暦を和暦の年に変換する
 * 西暦をまたがる場合があるので配列で返す
 * 
 * @param int $year
 * @return array
 * @access public
 */
	function convertToWarekiYear($year) {

		if($year >= 1868 && $year <= 1911) {
			return array('m-'.($year-1867));
		} elseif ($year == 1912) {
			return array('m-'.($year-1867),'t-'.($year-1911));
		} elseif ($year >= 1913 && $year <= 1925) {
			return array('t-'.($year-1911));
		} elseif ($year == 1926) {
			return array('t-'.($year-1911), 's-'.($year-1925));
		} elseif ($year >= 1927 && $year <= 1988) {
			return array('s-'.($year-1925));
		} elseif ($year == 1989) {
			return array('s-'.($year-1925), 'h-'.($year-1988));
		} elseif ($year >= 1990) {
			return array('h-'.($year-1988));
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
	function convertToSeirekiYear($year) {

		if(strpos($year, '-')===false) {
			return false;
		}
		list($w,$year) = explode('-', $year);
		switch ($w) {
			case 'm':
				return $year + 1867;
				break;
			case 't':
				return $year + 1911;
				break;
			case 's':
				return $year + 1925;
				break;
			case 'h':
				return $year + 1988;
				break;
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
	function convertToWarekiArray($date) {

		if(!$date) {
			return '';
		} elseif(is_array($date)) {
			if(!empty($date['year']) && !empty($date['month']) && !empty($date['day'])) {
				$date = $date['year'].'-'.$date['month'].'-'.$date['day'];
			} else {
				return '';
			}
		}

		if(strtotime($date)==-1) {
			return '';
		}

		$ymd = date('Ymd',strtotime($date));
		$y = date('Y',strtotime($date));
		$m = date('m',strtotime($date));
		$d = date('d',strtotime($date));

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
			'wareki'	=>	true,
			'year'		=>	$w.'-'.$y,
			'month'		=>	$m,
			'day'		=>	$d
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
	function convertToWareki($date) {

		$dateArray = $this->convertToWarekiArray($date);
		if(is_array($dateArray) && !empty($dateArray)) {
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
	function minutes($strDate) {

		$time = strtotime($strDate,0);
		$minutes = $time / 60;
		if($minutes) {
			return $minutes . '分';
		}else {
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
	function format($format = 'Y-m-d', $date = null, $invalid = false, $userOffset = null) {

		if($date != "00:00:00" && (!$date||$date === 0||$date=='0000-00-00 00:00:00')) {
			return "";
		}

		return parent::format($format,$date,$invalid,$userOffset);

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
	function pastDays($date,$days) {

		if(!$date) return true;
		$pastDate = strtotime($date);
		if(!$pastDate) return true;
		$_days = $days * 60 * 60 * 24;

		if(time() > ($pastDate + $_days)) {
			return true;
		}else {
			return false;
		}

	}
	
}