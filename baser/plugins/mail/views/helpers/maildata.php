<?php
/* SVN FILE: $Id$ */
/**
 * メールデータヘルパー
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.plugins.mail.views.helpers
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Helper', BC_TEXT_HELPER);
/**
 * メールデータヘルパー
 *
 * @package baser.plugins.mail.views.helpers
 *
 */
class MaildataHelper extends BcTextHelper {

	var $helpers = array(BC_TIME_HELPER);
/**
 * メール表示用のデータを出力する
 * 
 * @param string コントロールタイプ
 * @param mixed 変換前の値
 * @param array コントロールソース
 * @return string メール用データ
 * @access public
 */
	function control($type,$value,$options = "") {

		// コントロールソースの配列変換
		if(!is_array($options)) {
			$options = explode("|",$options);
		}
		$options = am(array(0=>""),$options);

		$out = "";

		switch($type) {

			case 'text':
			case 'email':
				$out = " ".$value;
				break;

			case 'radio':
				if(isset($options[$value])) {
					$out = " ".$options[$value];
				} else {
					$out = " ";
				}
				break;

			case 'select':
				if(isset($options[$value])) {
					$out = " ".$options[$value];
				} else {
					$out = " ";
				}
				break;

			case 'pref':
				$options = $pref = $this->prefList();
				if(isset($options[$value])) {
					$out = " ".$options[$value];
				} else {
					$out = " ";
				}
				break;

			case 'check':
				if($options) {
					if(isset($options[$value])) {
						$out = $options[$value];
					} else {
						$out = " ";
					}
				}else {
					$out = " ".$value;
				}
				break;

			case 'multi_check':
				$out = "";
				if($value) {
					if(!is_array($value)) {
						$value = explode("|", $value);
					}
					foreach($value as $data) {
						if(isset($options[$data])) {
							$out .= "・".$options[$data]."\n";
						}
					}
				}
				break;

			case 'date_time_calender':
				if(is_array($value)) {
					$value = $this->dateTime($value);
				}
				if($value) {
					$out = " ".date('Y年 m月 d日',strtotime($value));
				}
				break;

			case 'date_time_wareki':
				if(!is_array($value)) {
					$value = $this->BcTime->convertToWarekiArray($value);
				}
				$out = " ".$this->dateTimeWareki($value);
				break;

			case 'textarea':
				$out = " ".$value;
				break;

			case 'autozip':
				if(strlen($value)==7) {
					$out = " ".substr($value,0,3).'-'.substr($value,3,7);
				}else {
					$out = " ".$value;
				}
				break;

			case 'hidden':
				$out = " ".$value;
				break;

			default:
				$out = " ".$value;
				break;

		}

		return $out;

	}
	
}
