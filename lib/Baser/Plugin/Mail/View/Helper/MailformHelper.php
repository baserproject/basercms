<?php

/**
 * メールフォームヘルパー
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.View.Helper
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('BcHtmlHelper', 'View/Helper');
App::uses('BcFreezeHelper', 'View/Helper');

/**
 * メールフォームヘルパー
 *
 * @package Mail.View.Helper
 *
 */
class MailformHelper extends BcFreezeHelper {

/**
 * メールフィールドのデータよりコントロールを生成する
 *
 * @param string $type コントロールタイプ
 * @param string $fieldName フィールド文字列
 * @param array $options コントロールソース
 * @param array $attributes HTML属性
 * @return string フォームコントロールのHTMLタグ
 */
	public function control($type, $fieldName, $options, $attributes = array()) {
		$attributes['escape'] = false;
		$out = '';
		switch ($type) {

			case 'text':
			case 'email':
				unset($attributes['separator']);
				unset($attributes['rows']);
				unset($attributes['empty']);
				$out = $this->text($fieldName, $attributes);
				break;

			case 'radio':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$attributes['legend'] = false;
				$attributes['div'] = true;
				if (!empty($attributes['separator'])) {
					$attributes['separator'] = $attributes['separator'];
				} else {
					$attributes['separator'] = "&nbsp;&nbsp;";
				}
				$out = $this->radio($fieldName, $options, $attributes);
				break;

			case 'select':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				if (isset($attributes['empty'])) {
					if (strtolower($attributes['empty']) == 'false' || 
						strtolower($attributes['empty']) == 'null') {
						$showEmpty = false;
					} else {
						$showEmpty = $attributes['empty'];
					}
				} else {
					$showEmpty = true;
				}
				$attributes['value'] = null;
				$attributes['empty'] = $showEmpty;
				$out = $this->select($fieldName, $options, $attributes);
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
				$address1 = $this->_name(array(), $options[1]);
				$address2 = $this->_name(array(), $options[2]);
				$attributes['onKeyUp'] = "AjaxZip3.zip2addr(this,'','{$address1['name']}','{$address2['name']}')";
				$out = $this->Html->script('admin/ajaxzip3.js') . $this->text($fieldName, $attributes);
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
				if ($this->freezed) {
					unset($attributes['separator']);
				}
				$attributes['multiple'] = 'checkbox';
				$attributes['value'] = null;
				$attributes['empty'] = false;
				$out = $this->select($fieldName, $options, $attributes);
				break;
			case 'file':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['separator']);
				unset($attributes['escape']);
				if(empty($attributes['width'])) {
					$attributes['width'] = 400;
				}
				$attributes['delCheck'] = false;
				if(!empty($attributes['maxFileSize'])) {
					$out = '<input type="hidden" name="MAX_FILE_SIZE" value="' . $attributes['maxFileSize'] * 1000 * 1000 . '" />';
				}
				unset($attributes['maxFileSize']);
				unset($attributes['fileExt']);
				$out .= $this->file($fieldName, $attributes);

				break;
			
			case 'date_time_calender':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$out = $this->datepicker($fieldName, $attributes);
				break;

			case 'date_time_wareki':
				unset($attributes['size']);
				unset($attributes['rows']);
				unset($attributes['maxlength']);
				unset($attributes['empty']);
				$attributes['monthNames'] = false;
				$attributes['separator'] = '&nbsp;';
				if (isset($attributes['minYear']) && $attributes['minYear'] == 'today') {
					$attributes['minYear'] = intval(date('Y'));
				}
				if (isset($attributes['maxYear']) && $attributes['maxYear'] == 'today') {
					$attributes['maxYear'] = intval(date('Y'));
				}
				$out = $this->dateTime($fieldName, 'WMD', null, $attributes);
				break;

			case 'textarea':
				$attributes['cols'] = $attributes['size'];
				unset($attributes['separator']);
				unset($attributes['empty']);
				unset($attributes['size']);
				if ($attributes['maxlength'] === null) {
					unset($attributes['maxlength']);
				}
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


/**
 * create
 * ファイル添付の対応のためにデフォルト値を変更
 *
 * @param array $model
 * @param array $options
 * @return string
 */
	public function create($model = null, $options = array()) {
		if (!isset($options['type'])) {
			$options['type'] = 'file';
		}

		return parent::create($model, $options);
	}

}
