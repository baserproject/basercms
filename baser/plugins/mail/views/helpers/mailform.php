<?php
/* SVN FILE: $Id$ */
/**
 * メールフォームヘルパー
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
 * @package			baser.plugins.mail.views.helpers
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Helper', 'Freeze');
/**
 * メールフォームヘルパー
 *
 * @package			baser.plugins.mail.views.helpers
 *
 */
class MailformHelper extends FreezeHelper {
/**
 * メールフィールドのデータよりコントロールを生成する
 *
 * @param	string	コントロールタイプ
 * @param	string	フィールド文字列
 * @param	array	コントロールソース
 * @param	array	html属性
 * @return	string	htmlタグ
 * @access	public
 */
	function control($type,$fieldName,$options, $attributes = array()) {

		$attributes['escape'] = false;

		switch($type) {

			case 'text':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$out = $this->text($fieldName,$attributes);
				break;

			case 'radio':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$attributes['legend'] = false;
				$attributes['div'] = true;
				if(!empty($attributes['separator'])) {
					$attributes['separator'] = $attributes['separator'];
				}else {
					$attributes['separator'] = "&nbsp;&nbsp;";
				}
				$out = $this->radio($fieldName, $options, $attributes);
				break;

			case 'select':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				if(isset($attributes['empty'])) {
					$showEmpty = $attributes['empty'];
				}else {
					$showEmpty = true;
				}
				$out = $this->select($fieldName, $options, null, $attributes, $showEmpty);
				break;

			case 'pref':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				unset($attributes['empty']);
				$out = $this->prefTag($fieldName, null, $attributes);
				break;

			case 'autozip':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$address1 = $this->__name(array(),$options[1]);
				$address2 = $this->__name(array(),$options[2]);
				$attributes['onKeyUp'] = "AjaxZip3.zip2addr(this,'','{$address1['name']}','{$address2['name']}')";
				$out = $this->Javascript->link('ajaxzip3.js').$this->text($fieldName,$attributes);
				break;

			case 'check':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				unset($attributes['empty']);
				$out = $this->checkbox($fieldName, $attributes);
				break;

			case 'multi_check':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				if($this->freezed) {
					unset($attributes['separator']);
				}
				$attributes['multiple'] = 'checkbox';
				$out = $this->select($fieldName,$options,null,$attributes,false);
				break;

			case 'date_time_reserve':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$attributes['minYear'] = intval(date('Y'));
				$attributes['maxYear'] = $attributes['minYear']+1;
				$attributes['monthNames'] = false;
				$attributes['separator'] = ' ';
				$out = $this->dateTime($fieldName,'YMD',null,null,$attributes);
				break;

			case 'date_time_birthday_wareki':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$attributes['monthNames'] = false;
				$attributes['separator'] = ' ';
				$out = $this->dateTime($fieldName,'WYMD',null,null,$attributes);
				break;

			case 'textarea':
				$attributes['cols'] = $attributes['size'];
				unset($attributes['separator']);
				unset($attributes['empty']);
				unset($attributes['size']);
				$out = $this->textarea($fieldName, $attributes);
				break;
			case 'hidden':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$out = $this->hidden($fieldName, $attributes);
		}
		return $out;

	}
}
?>