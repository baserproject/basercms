<?php
/* SVN FILE: $Id$ */
/**
 * メールフィールドヘルパー
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
/**
 * メールフィールドヘルパー
 *
 * @package baser.plugins.mail.views.helpers
 *
 */
class MailfieldHelper extends AppHelper {
/**
 * htmlの属性を取得する
 *
 * @param array メールフィールド
 * @return array html属性
 * @access public
 */
	function getAttributes($data) {

		if(isset($data['MailField'])) {
			$data = $data['MailField'];
		}

		$attributes['size']=$data['size'];
		$attributes['rows']=$data['rows'];
		$attributes['maxlength']=$data['maxlength'];
		$attributes['separator']=$data['separator'];
		$attributes['class']=$data['class'];

		if(!empty($data['options'])) {

			$options = explode("|",$data['options']);
			$options = call_user_func_array('aa', $options);
			$attributes = am($attributes,$options);

		}
		return $attributes;

	}
/**
 * コントロールのソースを取得する
 *
 * @param array メールフィールド
 * @return array コントロールソース
 * @access public
 */
	function getOptions($data) {

		if(isset($data['MailField'])) {
			$data = $data['MailField'];
		}

		$attributes = $this->getAttributes($data);

		// コントロールソースを変換
		if(!empty($data['source'])) {

			if($data['type']!="check") {
				$values = explode("|",$data['source']);
				$i = 0;
				foreach($values as $value) {
					$i++;
					$source[$i] = $value;
				}

				return $source;

			}

		}

	}
	
}

