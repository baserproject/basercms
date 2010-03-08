<?php
/* SVN FILE: $Id$ */
/**
 * Timeヘルパー拡張
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2010, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi 
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2010, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.view.helpers
 * @since			Baser v 0.1.0
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
 * @package			baser.views.helpers
 */
class TimeExHelper extends TimeHelper {
/**
 * 年号リスト
 *
 * @var		array
 * @access	public
 */
	var $nengos = array("m"=>"明治","t"=>"大正","s"=>"昭和","h"=>"平成");
/**
 * 年号リストを取得
 *
 * @return	array	年号リスト
 * @access	public
 */
	function getNengos(){
		return $this->nengos;
	}
/**
 * 和暦変換
 *
 * @param	string	日付
 * @return	array	和暦データ
 * @access	public
 */
	function convertWareki($date)
	{
		if(strtotime($date)==-1){
			list($y,$m,$d) = explode("-",$date);
			$ymd = $y.$m.$d;
		}else{
			$ymd = date('Ymd',strtotime($date));
			$y = date('Y',strtotime($date));
		}
		
		if ($ymd <= "19120729") {
			$g = $this->nengos['m'];
			$n = "m";
			$y = $y - 1867;
		} elseif ($ymd >= "19120730" && $ymd <= "19261224") {
			$g = $this->nengos['t'];
			$n = "t";
			$y = $y - 1911;
		} elseif ($ymd >= "19261225" && $ymd <= "19890107") {
			$g = $this->nengos['s'];
			$n = "s";
			$y = $y - 1925;
		} elseif ($ymd >= "19890108") {
			$g = $this->nengos['h'];
			$n = "h";
			$y = $y - 1988;
		}
		$wareki = array('G'=>$g,"n"=>$n,'Y'=>$y,'m'=> date('m',strtotime($date)),'d'=> date('d',strtotime($date)));
		return $wareki;
	}
/**
 * 西暦変換
 *
 * @param	array	$aryDate
 * @return	string	西暦
 * @access	public
 */
	function convertSeireki($aryDate){
	
		if(!$aryDate['month'] || !$aryDate['day'] || !$aryDate['wyear']){
			return null;
		}
		
		switch ($aryDate['nengo']){
		
		case "m":
			$aryDate["year"] = $aryDate["wyear"] + 1867;
		break;

		case "t":
			$aryDate["year"] = $aryDate["wyear"] + 1911;
		break;
			
		case "s":
			$aryDate["year"] = $aryDate["wyear"] + 1925;
		break;
		
		case "h":
			$aryDate["year"] = $aryDate["wyear"] + 1988;		
		break;
		
		}
		if(checkdate($aryDate['month'],$aryDate['day'],$aryDate['year'])){
			return $aryDate["year"]."/".$aryDate['month']."/".$aryDate['day'];
		}
		
	}
/**
 * 文字列から時間（分）を取得
 *
 * @param	string	日時
 * @return	mixed	分/null
 * @access	public
 */
	function minutes($strDate){
		
		$time = strtotime($strDate,0);
		$minutes = $time / 60;
		if($minutes){
			return $minutes . '分';
		}else{
			return null;
		}
	
	}
/**
 * format 拡張
 *
 * @param	string 	$dateString Datetime string
 * @param	boolean $invalid flag to ignore results of fromString == false
 * @param	int 	$userOffset User's offset from GMT (in hours)
 * @return	string 	Formatted date string
 * @access	public
 */
	function format($format = 'Y-m-d', $date, $invalid = false, $userOffset = null) {

		if($date != "00:00:00" && (!$date||$date === 0)){
			return "";
		}

		return parent::format($format,$date,$invalid,$userOffset);	

	}
/**
 * 指定した日数が経過しているか確認する
 *
 * 経過していない場合はtrueを返す
 * 日付が確認できなかった場合もtrueを返す
 *
 * @param	string	日付
 * @param	int		経過日数
 * @return	boolean	経過有無
 * @access	public
 */
	function pastDays($date,$days){
		
		if(!$date) return true;
		$pastDate = strtotime($date);
		if(!$pastDate) return true;
		$_days = $days * 60 * 60 * 24;

		if(time() > ($pastDate + $_days)){
			return true;
		}else{
			return false;
		}
		
	}
	
}

?>