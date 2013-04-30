<?php
/* SVN FILE: $Id$ */
/**
 * Textヘルパー拡張
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
/**
 * Include files
 */
App::import("Helper",array("Text","BcTime"));
/**
 * Textヘルパー拡張
 *
 * @package baser.views.helpers
 */
class BcTextHelper extends TextHelper {
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array(BC_TIME_HELPER, BC_FORM_HELPER);
/**
 * 文字数カット（日本語対応）
 * Cuts a string to the length of $length and replaces the last characters
 * with the ending if the text is longer than length.
 *
 * @param string $text String to truncate.
 * @param integer $length Length of returned string, including ellipsis.
 * @param mixed $ending If string, will be used as Ending and appended to the trimmed string.
 *						Can also be an associative array that can contain the last three params of this method.
 * @param boolean $exact If false, $text will not be cut mid-word
 * @param boolean $considerHtml If true, HTML tags would be handled correctly
 * @return string Trimmed string.
 * @access public
 */
	function mbTruncate($text, $length = 100, $ending = '...', $exact = true, $considerHtml = false) {
		
		if (is_array($ending)) {
			extract($ending);
		}
		if ($considerHtml) {
			if (mb_strlen(preg_replace('/<.*?>/', '', $text)) <= $length) {
				return $text;
			}

			preg_match_all('/(<.+?>)?([^<>]*)/s', $text, $lines, PREG_SET_ORDER);
			$total_length = mb_strlen($ending);
			$open_tags = array();
			$truncate = '';

			foreach ($lines as $line_matchings) {
				if (!empty($line_matchings[1])) {
					if (preg_match('/^<(\s*.+?\/\s*|\s*(img|br|input|hr|area|base|basefont|col|frame|isindex|link|meta|param)(\s.+?)?)>$/is', $line_matchings[1])) {
					} elseif (preg_match('/^<\s*\/([^\s]+?)\s*>$/s', $line_matchings[1], $tag_matchings)) {
						$pos = array_search($tag_matchings[1], $open_tags);
						if ($pos !== false) {
							unset($open_tags[$pos]);
						}
					} elseif (preg_match('/^<\s*([^\s>!]+).*?>$/s', $line_matchings[1], $tag_matchings)) {
						array_unshift($open_tags, strtolower($tag_matchings[1]));
					}
					$truncate .= $line_matchings[1];
				}

				$content_length = mb_strlen(preg_replace('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', ' ', $line_matchings[2]));
				if ($total_length+$content_length > $length) {
					$left = $length - $total_length;
					$entities_length = 0;
					if (preg_match_all('/&[0-9a-z]{2,8};|&#[0-9]{1,7};|&#x[0-9a-f]{1,6};/i', $line_matchings[2], $entities, PREG_OFFSET_CAPTURE)) {
						foreach ($entities[0] as $entity) {
							if ($entity[1]+1-$entities_length <= $left) {
								$left--;
								$entities_length += mb_strlen($entity[0]);
							} else {
								break;
							}
						}
					}
					$truncate .= mb_substr($line_matchings[2], 0, $left+$entities_length,mb_internal_encoding());
					break;
				} else {
					$truncate .= $line_matchings[2];
					$total_length += $content_length;
				}

				if ($total_length >= $length) {
					break;
				}
			}
		} else {
			if (mb_strlen($text) <= $length) {
				return $text;
			} else {
				$truncate = mb_substr($text, 0, $length - mb_strlen($ending),mb_internal_encoding());
			}
		}

		if (!$exact) {
			$spacepos = strrpos($truncate, ' ');
			if (isset($spacepos)) {
				$truncate = mb_substr($truncate, 0, $spacepos,mb_internal_encoding());
			}
		}

		$truncate .= $ending;

		if ($considerHtml) {
			foreach ($open_tags as $tag) {
				$truncate .= '</' . $tag . '>';
			}
		}

		return $truncate;
		
	}
/**
 * boolean型を○―マークで出力
 *
 * @param boolean
 * @return string ○―マーク
 * @access public
 */
	function booleanMark($value) {

		if($value) {
			return "○";
		}else {
			return "―";
		}

	}
/**
 * boolean型用のリストを○―マークで出力
 *
 * @return array ○―マークリスト
 * @access public
 */
	function booleanMarkList() {

		return array(0=>"―",1=>"○");

	}
/**
 * boolean型用のリストを有無で出力
 *
 * @return array 有無リスト
 * @access public
 */
	function booleanExistsList() {

		return array(0=>"無",1=>"有");

	}
/**
 * boolean型用のリストを可、不可で出力
 *
 * @return array 可/不可リスト
 * @access public
 */
	function booleanAllowList() {

		return array(0=>"不可",1=>"可");

	}
/**
 * boolean型用のリストを[〜する/〜しない]形式で出力する
 *
 * @param string $doText Do文字列
 * @return array [〜する/〜しない]形式のリスト
 * @access public
 */
	function booleanDoList($doText = null) {

		return array(0=>$doText."しない",1=>$doText."する");

	}
/**
 * boolean型用のリストを[〜る/〜ない]形式で出力する
 * 
 * @param string $requireText Do文字列
 * @return array
 * @access public
 */
	function booleanRequireList($requireText = null) {
		
		return array(0=>$requireText.'ない',1=>$requireText.'る');
		
	}
/**
 * boolean型用のリストを[〜る/〜ない]形式で出力する
 * 
 * @param mixed $value
 * @param string $text
 * @return mixed
 */
	function booleanRequire($value,$text) {
		
		$booleanList = $this->booleanRequireList($text);
		return $booleanList[$value];
		
	}
/**
 * boolean型のデータを [〜する / 〜しない] 形式で出力する
 *
 * @param boolean $value 値
 * @param string $doText Do文字列
 * @return string
 * @access public
 */
	function booleanDo($value,$doText = null) {

		$booleanDoList = $this->booleanDoList($doText);
		return $booleanDoList[$value];

	}
/**
 * 都道府県のリストを出力
 *
 * @return array 都道府県リスト
 * @access public
 */
	function prefList($empty = '都道府県') {

		$pref = array();
		if($empty) {
			$pref = array(""=>$empty);
		} elseif($pref !== false) {
			$pref = array(""=>"");
		}

		$pref = $pref + array(1=>"北海道",2=>"青森県",3=>"岩手県",4=>"宮城県",5=>"秋田県",6=>"山形県",
				7=>"福島県",8=>"茨城県",9=>"栃木県",10=>"群馬県",11=>"埼玉県",12=>"千葉県",13=>"東京都",14=>"神奈川県",
				15=>"新潟県",16=>"富山県",17=>"石川県",18=>"福井県",19=>"山梨県",20=>"長野県",21=>"岐阜県",22=>"静岡県",
				23=>"愛知県",24=>"三重県",25=>"滋賀県",26=>"京都府",27=>"大阪府",28=>"兵庫県",29=>"奈良県",30=>"和歌山県",
				31=>"鳥取県",32=>"島根県",33=>"岡山県",34=>"広島県",35=>"山口県",36=>"徳島県",37=>"香川県",38=>"愛媛県",
				39=>"高知県",40=>"福岡県",41=>"佐賀県",42=>"長崎県",43=>"熊本県",44=>"大分県",45=>"宮崎県",46=>"鹿児島県",
				47=>"沖縄県");
		return $pref;

	}
/**
 * 性別を出力
 * 
 * @param array $value
 * @return string
 * @access public
 */
	function sex($value) {
		$sexes = array(1=>'男',2=>'女');
		return $sexes[$value];
	}
/**
 * 郵便番号にハイフンをつけて出力
 * TODO とりあえずの仕様
 *
 * @param string	$value ハイフンなしの郵便番号
 * @return string	〒マーク、ハイフン付きの郵便番号
 * @access	public
 */
	function zipFormat($value) {
		
		$right = substr($value,0,3);
		$left = substr($value,3,4);
		return "〒 ".$right."-".$left;
		
	}
/**
 * 番号を都道府県に変換して出力
 *
 * @param int $value 都道府県番号
 * @param string $noValue 都道府県名
 * @return string 都道府県名
 * @access	public
 */
	function pref($value, $noValue='') {

		if(!$value) {
			return $noValue;
		}
		$list = $this->prefList();
		return $list[(int)$value];

	}
/**
 * データをチェックして空の場合に指定した値を返す
 *
 * @param mixed $value
 * @param	mixed $noValue
 * @return mixed
 * @access public
 */
	function noValue($value, $noValue) {

		if(!$value) {
			return $noValue;
		}else {
			return $value;
		}

	}
/**
 * boolean型用を可、不可で出力
 *
 * @param boolean $value
 * @return	string	可/不可
 * @access	public
 */
	function booleanAllow($value) {

		$list = $this->booleanAllowList();
		return $list[(int)$value];

	}
/**
 * boolean型用を有無で出力
 *
 * @param boolean $value
 * @return string 有/無
 * @access public
 */
	function booleanExists($value) {

		$list = $this->booleanExistsList();
		return $list[(int)$value];

	}
/**
 * form::dateTimeで取得した和暦データを文字列データに変換する
 *
 * @param array $arrDate
 * @return string 和暦
 * @access public
 */
	function dateTimeWareki($arrDate) {

		if(!is_array($arrDate)) return;
		if(!$arrDate['wareki'] || !$arrDate['year'] || !$arrDate['month'] || !$arrDate['day'])
			return;
		list($w,$year) = explode('-', $arrDate['year']);
		$wareki = $this->BcTime->nengo($w);
		return $wareki." ".$year."年 ".$arrDate['month']."月 ".$arrDate['day'].'日';

	}
/**
 * 通貨表示
 *
 * @param int $value
 * @param string $prefix
 * @return string
 */
	function moneyFormat($value, $prefix='¥') {
		
		if($value) {
			return $prefix.number_format($value);
		} else {
			return '';
		}
		
	}
/**
 * form::dateTimeで取得したデータを文字列データに変換する
 *
 * @param array $arrDate
 * @return string 日付
 * @access public
 */
	function dateTime($arrDate) {

		if(!$arrDate['year'] || !$arrDate['month'] || !$arrDate['day'])
			return;
		return $arrDate['year']."/".$arrDate['month']."/".$arrDate['day'];

	}
/**
 * 文字をフォーマット形式で出力する
 *
 * @param string $format フォーマット
 * @param mixed $value 値
 * @param	mixed $noValue データがなかった場合の初期値
 * @return	string	変換後の文字列
 * @access	public
 */
	function format($format,$value, $noValue = '') {
		
		if($value === '' || is_null($value)) {
			return $noValue;
		} else {
			return sprintf($format,$value);
		}
		
	}
/**
 * モデルのコントロールソースより表示用データを取得する
 *
 * @param string $field フィールド名
 * @param mixed $value 値
 * @return string 表示用データ
 * @access public
 */
	function listValue($field,$value) {

		$list = $this->BcForm->getControlSource($field);
		if($list && isset($list[$value])) {
			return $list[$value];
		}else {
			return false;
		}

	}
/**
 * 区切り文字で区切られたテキストを配列に変換する
 * 
 * @param string $separator
 * @param string $value
 * @return array
 * @access public
 */
	function toArray($separator,$value) {
		
		if($separator != '"') {
			$value = str_replace('"','',$value);
		}
		if($separator != "'") {
			$value = str_replace("'",'',$value);
		}
		if(strpos($value,$separator) === false) {
			if($value) {
				return array($value);
			}else {
				return array();
			}
		}
		$values = explode($separator,$value);
		return $values;
		
	}
/**
 * 配列とキーを指定して値を取得する
 * 
 * @param int $key
 * @param mixed $value
 * @param array $array
 * @param mixed type $noValue
 * @return mixied
 */
	function arrayValue($key, $array, $noValue = '') {
		
		if(is_numeric($key)) {
			$key = (int)$key;
		}
		if(isset($array[$key])) {
			return $array[$key];
		}
		return $noValue;
		
	}
/**
 * 連想配列とキーのリストより値のリストを取得し文字列で返す
 * 文字列に結合する際、指定した区切り文字を指定できる
 *
 * @param string $glue
 * @param array $keys
 * @param array $array
 * @return string
 * @access public
 */
	function arrayValues($glue, $keys, $array) {

		$values = array();
		foreach($keys as $key) {
			if(isset($array[$key])) {
				$values[] = $array[$key];
			}
		}
		if($values) {
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
 * @access public
 */
	function age($birthday, $suffix='歳', $noValue = '不明') {

		if(!$birthday) {
			return $noValue;
		}
		$byear = date('Y', strtotime($birthday));
		$bmonth = date('m', strtotime($birthday));
		$bday = date('d', strtotime($birthday));
		$tyear = date('Y');
		$tmonth = date('m');
		$tday = date('d');
		$age = $tyear - $byear;
		if($tmonth * 100 + $tday < $bmonth * 100 + $bday) $age--;
		return $age.$suffix;

	}
/**
 * boolean型用のリストを有効、無効で出力
 *
 * @return array 可/不可リスト
 * @access public
 */
	function booleanStatusList() {

		return array(0=>"無効",1=>"有効");

	}
/**
 * boolean型用を無効・有効で出力
 *
 * @param boolean
 * @return string 無効/有効
 * @access public
 */
	function booleanStatus($value) {

		$list = $this->booleanStatusList();
		return $list[(int)$value];

	}
	
}