<?php
/* SVN FILE: $Id$ */
/**
 * FormHelper 拡張クラス
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
App::import('Helper',array('Html', 'Form', BC_TIME_HELPER, BC_TEXT_HELPER, BC_CKEDITOR_HELPER));
/**
 * FormHelper 拡張クラス
 *
 * @package baser.views.helpers
 */
class BcFormHelper extends FormHelper {
/**
 * ヘルパー
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html', BC_TIME_HELPER, BC_TEXT_HELPER, 'Javascript', BC_CKEDITOR_HELPER);
/**
 * sizeCounter用の関数読み込み可否
 * 
 * @var boolean
 * @access public
 */
	var $sizeCounterFunctionLoaded = false;
/**
 * フォームID
 * 
 * @var string
 * @access private
 */
	var $__id = null;
/**
 * 都道府県用のSELECTタグを表示する
 *
 * @param string $fieldName Name attribute of the SELECT
 * @param mixed $selected Selected option
 * @param array $attributes Array of HTML options for the opening SELECT element
 * @return string 都道府県用のSELECTタグ
 * @access public
 */
	function prefTag($fieldName, $selected = null, $attributes = array()) {

		$pref = $this->BcText->prefList();
		return $this->select($fieldName, $pref, $selected, $attributes, false);

	}
/**
 * dateTime 拡張
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param string $dateFormat DMY, MDY, YMD or NONE.
 * @param string $timeFormat 12, 24, NONE
 * @param string $selected Option which is selected.
 * @param string $attributes array of Attributes
 *						'monthNames' If set and false numbers will be used for month select instead of text.
 *						'minYear' The lowest year to use in the year select
 *						'maxYear' The maximum year to use in the year select
 *						'interval' The interval for the minutes select. Defaults to 1
 *						'separator' The contents of the string between select elements. Defaults to '-'
 * @param boolean $showEmpty Whether or not to show an empty default value.
 * @return string The HTML formatted OPTION element
 * @access public
 */
	function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $selected = null, $attributes = array(), $showEmpty = true) {

		$year = $month = $day = $hour = $min = $meridian = null;

		if (empty($selected)) {
			$selected = $this->value($fieldName);
		}

		if ($selected === null && $showEmpty != true) {
			$selected = time();
		}

		if (!empty($selected)) {
			if (is_array($selected)) {
				extract($selected);
			} else {
				if (is_numeric($selected)) {
					$selected = strftime('%Y-%m-%d %H:%M:%S', $selected);
				}
				$meridian = 'am';
				$pos = strpos($selected, '-');
				if ($pos !== false) {
					$date = explode('-', $selected);
					$days = explode(' ', $date[2]);
					$day = $days[0];
					$month = $date[1];
					$year = $date[0];
				} else {
					$days[1] = $selected;
				}

				if ($timeFormat != 'NONE' && !empty($timeFormat)) {
					$time = explode(':', $days[1]);
					$check = str_replace(':', '', $days[1]);

					if (($check > 115959) && $timeFormat == '12') {
						$time[0] = $time[0] - 12;
						$meridian = 'pm';
					} elseif ($time[0] == '00' && $timeFormat == '12') {
						$time[0] = 12;
					} elseif ($time[0] > 12) {
						$meridian = 'pm';
					}
					if ($time[0] == 0 && $timeFormat == '12') {
						$time[0] = 12;
					}
					$hour = $time[0];
					$min = $time[1];
				}
			}
		}

		$elements = array('Day','Month','Year','Hour','Minute','Meridian');
		// >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	日本対応
		/*$defaults = array(
			'minYear' => null, 'maxYear' => null, 'separator' => '-',
			'interval' => 1, 'monthNames' => true
		);*/
		// ---
		$defaults = array(
			'minYear' => null, 'maxYear' => null, 'separator' => ' ',
			'interval' => 1, 'monthNames' => ''
		);
		// <<<
		$attributes = array_merge($defaults, (array) $attributes);
		if (isset($attributes['minuteInterval'])) {
			$attributes['interval'] = $attributes['minuteInterval'];
			unset($attributes['minuteInterval']);
		}
		$minYear = $attributes['minYear'];
		$maxYear = $attributes['maxYear'];
		$separator = $attributes['separator'];
		$interval = $attributes['interval'];
		$monthNames = $attributes['monthNames'];
		$attributes = array_diff_key($attributes, $defaults);

		if (isset($attributes['id'])) {
			if (is_string($attributes['id'])) {
				// build out an array version
				foreach ($elements as $element) {
					$selectAttrName = 'select' . $element . 'Attr';
					${$selectAttrName} = $attributes;
					${$selectAttrName}['id'] = $attributes['id'] . $element;
				}
			} elseif (is_array($attributes['id'])) {
				// check for missing ones and build selectAttr for each element
				foreach ($elements as $element) {
					$selectAttrName = 'select' . $element . 'Attr';
					${$selectAttrName} = $attributes;
					${$selectAttrName}['id'] = $attributes['id'][strtolower($element)];
				}
			}
		} else {
			// build the selectAttrName with empty id's to pass
			foreach ($elements as $element) {
				$selectAttrName = 'select' . $element . 'Attr';
				${$selectAttrName} = $attributes;
			}
		}

		$opt = '';

		if ($dateFormat != 'NONE') {
			$selects = array();
			foreach (preg_split('//', $dateFormat, -1, PREG_SPLIT_NO_EMPTY) as $char) {
				switch ($char) {
					// >>> CUSTOMIZE ADD 2011/01/11 ryuring	和暦対応
					case 'W':
						$selects[] = $this->wyear($fieldName, $minYear, $maxYear, $year, $selectYearAttr, $showEmpty)."年";
						break;
					// <<<
					case 'Y':
						// >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	日本対応
						/*$selects[] = $this->year(
							$fieldName, $minYear, $maxYear, $year, $selectYearAttr, $showEmpty
						);*/
						// ---
						$suffix = (preg_match('/^W/', $dateFormat)) ? '年' : '';
						$selects[] = $this->year(
							$fieldName, $minYear, $maxYear, $year, $selectYearAttr, $showEmpty
						).$suffix;
						// <<<
					break;
					case 'M':
						$selectMonthAttr['monthNames'] = $monthNames;
						// >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	日本対応
						//$selects[] = $this->month($fieldName, $month, $selectMonthAttr, $showEmpty);
						// ---
						$suffix = (preg_match('/^W/', $dateFormat)) ? '月' : '';
						$selects[] = $this->month($fieldName, $month, $selectMonthAttr, $showEmpty).$suffix;
						// <<<
					break;
					case 'D':
						// >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	日本対応
						//$selects[] = $this->day($fieldName, $day, $selectDayAttr, $showEmpty);
						// ---
						$suffix = (preg_match('/^W/', $dateFormat)) ? '日' : '';
						$selects[] = $this->day($fieldName, $day, $selectDayAttr, $showEmpty).$suffix;
						// <<<
					break;
				}
			}
			$opt = implode($separator, $selects);
		}
		if (!empty($interval) && $interval > 1 && !empty($min)) {
			$min = round($min * (1 / $interval)) * $interval;
		}
		$selectMinuteAttr['interval'] = $interval;
		switch ($timeFormat) {
			case '24':
				$opt .= $this->hour($fieldName, true, $hour, $selectHourAttr, $showEmpty) . ':' .
				$this->minute($fieldName, $min, $selectMinuteAttr, $showEmpty);
			break;
			case '12':
				$opt .= $this->hour($fieldName, false, $hour, $selectHourAttr, $showEmpty) . ':' .
				$this->minute($fieldName, $min, $selectMinuteAttr, $showEmpty) . ' ' .
				$this->meridian($fieldName, $meridian, $selectMeridianAttr, $showEmpty);
			break;
			case 'NONE':
			default:
				$opt .= '';
			break;
		}
		return $opt;
		
	}
/**
 * 和暦年
 *
 * @param string $fieldName Prefix name for the SELECT element
 * @param integer $minYear First year in sequence
 * @param integer $maxYear Last year in sequence
 * @param string $selected Option which is selected.
 * @param array $attributes Attribute array for the select elements.
 * @param boolean $showEmpty Show/hide the empty select option
 * @return string
 */
	function wyear($fieldName, $minYear = null, $maxYear = null, $selected = null, $attributes = array(), $showEmpty = true) {
		
		if ((empty($selected) || $selected === true) && $value = $this->value($fieldName)) {
			if (is_array($value)) {
				extract($value);
				$selected = $year;
			} else {
				if (empty($value)) {
					if (!$showEmpty && !$maxYear) {
						$selected = 'now';

					} elseif (!$showEmpty && $maxYear && !$selected) {
						$selected = $maxYear;
					}
				} else {
					$selected = $value;
				}
			}
		}

		if (strlen($selected) > 4 || $selected === 'now') {
			$wareki = $this->BcTime->convertToWareki(date('Y-m-d', strtotime($selected)));
			$wareki = $this->BcTime->convertToWareki($this->value($fieldName));
			$w = $this->BcTime->wareki($wareki);
			$wyear = $this->BcTime->wyear($wareki);
			$selected = $w.'-'.$wyear;
		} elseif ($selected === false) {
			$selected = null;
		} elseif(strpos($selected, '-')===false) {
			$wareki = $this->BcTime->convertToWareki($this->value($fieldName));
			if($wareki) {
				$w = $this->BcTime->wareki($wareki);
				$wyear = $this->BcTime->wyear($wareki);
				$selected = $w.'-'.$wyear;
			} else {
				$selected = null;
			}
		}
		$yearOptions = array('min' => $minYear, 'max' => $maxYear);

		return $this->hidden($fieldName.".wareki", array('value'=>true)).
		$this->select(
			$fieldName . ".year", $this->__generateOptions('wyear', $yearOptions),
			$selected, $attributes, $showEmpty
		);
		
	}
/**
 * コントロールソースを取得する
 * Model側でメソッドを用意しておく必要がある
 *
 * @param string $field フィールド名
 * @param array $options
 * @return array コントロールソース
 * @access public
 */
	function getControlSource($field,$options = array()) {
		
		$count = preg_match_all('/\./is',$field,$matches);
		if($count == 1) {
			list($modelName,$field) = explode('.',$field);
		} elseif ($count == 2){
			list($plugin,$modelName,$field) = explode('.',$field);
			$modelName = $plugin.'.'.$modelName;
		}
		if(empty($modelName)) {
			$modelName = $this->model();
		}
		if(ClassRegistry::isKeySet($modelName)){
			$model =& ClassRegistry::getObject($modelName);
		}else{
			$model =& ClassRegistry::init($modelName);
		}
		if($model) {
			return $model->getControlSource($field,$options);
		}else {
			return false;
		}

	}
/**
 * モデルよりリストを生成する
 *
 * @param string $modelName
 * @param mixed $conditions
 * @param mixed $fields
 * @param mixed $order
 * @return mixed リストまたは、false
 * @access public
 */
	function generateList($modelName,$conditions = array(),$fields = array(),$order = array()) {

		$model =& ClassRegistry::getObject($modelName);

		if($fields) {
			list($idField,$displayField) = $fields;
		}else {
			$idField = 'id';
			$displayField = $model->getDisplayField();
			$fields = array($idField,$displayField);
		}

		$list = $model->find( 'all', array('conditions' => $conditions, 'fields' => $fields, 'order' => $order));

		if($list) {
			return Set::combine($list,"{n}.".$modelName.".".$idField,"{n}.".$modelName.".".$displayField);
		}else {
			return null;
		}

	}
/**
 * JsonList
 *
 * @param string $field フィールド文字列
 * @param string $attributes
 * @return array 属性
 * @access public
 */
	function jsonList($field,$attributes) {

		am(array("imgSrc"=>"","ajaxAddAction"=>"","ajaxDelAction"=>""),$attributes);
		// JsonDb用Hiddenタグ
		$out = $this->hidden('Json.'.$field.'.db');
		// 追加テキストボックス
		$out .= $this->text('Json.'.$field.'.name');
		// 追加ボタン
		$out .= $this->button('追加',array('id'=>'btnAdd'.$field));
		// リスト表示用ビュー
		$out .= '<div id="Json'.$field.'View"></div>';

		// javascript
		$out .= '<script type="text/javascript"><!--'."\n".
				'jQuery(function(){'."\n".
				'var json_List = new JsonList({"dbId":"Json'.$field.'Db","viewId":"JsonTagView","addButtonId":"btnAdd'.$field.'",'."\n".
				'"deleteButtonType":"img","deleteButtonSrc":"'.$attributes['imgSrc'].'","deleteButtonRollOver":true,'."\n".
				'"ajaxAddAction":"'.$attributes['ajaxAddAction'].'",'."\n".
				'"ajaxDelAction":"'.$attributes['ajaxDelAction'].'"});'."\n".
				'json_List.loadData();'."\n".
				'});'."\n".
				'//--></script>';

		return $out;

	}
/**
 * カレンダーコントロール付きのテキストフィールド
 * jquery-ui-1.7.2 必須
 *
 * @param string フィールド文字列
 * @param array HTML属性
 * @return string html
 * @access public
 */
	function datepicker($fieldName, $attributes = array()) {

		if (!isset($attributes['value'])) {
			$value = $this->value($fieldName);
		}else {
			$value = $attributes['value'];
		}

		if($value) {
			$value = $this->BcTime->format('Y/m/d',$value);
			if($value) {
				$attributes['value'] = date('Y/m/d',strtotime($value));
			} else {
				$attributes['value'] = '';
			}
		}else {
			unset($attributes['value']);
		}

		$this->setEntity($fieldName);
		$id = $this->domId($fieldName);

		// テキストボックス
		$input = $this->text($fieldName,$attributes);

		// javascript
		$script = <<< DOC_END
<script type="text/javascript">
<!--
jQuery(function($){
	$("#{$id}").datepicker();
});
//-->
</script>
DOC_END;
		
		$out = $input."\n".$script;
		return $out;

	}
/**
 * 日付カレンダーと時間フィールド
 * 
 * @param string $fieldName
 * @param array $attributes
 * @return string
 * @access public
 */
	function dateTimePicker($fieldName, $attributes = array()) {

		$timeAttributes = array('size'=>8,'maxlength'=>8);
		if (!isset($attributes['value'])) {
			$value = $this->value($fieldName);
		}else {
			$value = $attributes['value'];
			unset($attributes['value']);
		}
		if($value && $value != '0000-00-00 00:00:00') {
			$dateValue = date('Y/m/d',strtotime($value));
			$timeValue = date('H:i:s',strtotime($value));
			$attributes['value']=$dateValue;
			$timeAttributes['value']=$timeValue;
		}
		$dateTag = $this->datepicker($fieldName.'_date', $attributes);
		$timeTag = $this->text($fieldName.'_time', $timeAttributes);
		$hiddenTag = $this->hidden($fieldName, array('value'=>$value));
		$domId = $this->domId();
		$_script = <<< DOC_END
$(function(){
   $("#{$domId}Date").change({$domId}ChangeResultHandler);
   $("#{$domId}Time").change({$domId}ChangeResultHandler);
   function {$domId}ChangeResultHandler(){
		var value = $("#{$domId}Date").val().replace(/\//g, '-');
		if($("#{$domId}Time").val()) {
			value += ' '+$("#{$domId}Time").val();
		}
        $("#{$domId}").val(value);
		if(this.id.replace('{$domId}','') == 'Date') {
			if($("#{$domId}Date").val() && !$("#{$domId}Time").val()) {
				$("#{$domId}Time").val('00:00:00');
			}
		}
   }
});
DOC_END;
		$script = $this->Javascript->codeBlock($_script,array('inline'=>false));
		return $dateTag.$timeTag.$hiddenTag;
		
	}
/**
 * Generates option lists for common <select /> menus
 *
 * @param string $name
 * @param array $options
 * @return array option lists
 * @access private
 */
	function __generateOptions($name, $options = array()) {
		
		if (!empty($this->options[$name])) {
			return $this->options[$name];
		}
		$data = array();

		switch ($name) {
			case 'minute':
				if (isset($options['interval'])) {
					$interval = $options['interval'];
				} else {
					$interval = 1;
				}
				$i = 0;
				while ($i < 60) {
					$data[$i] = sprintf('%02d', $i);
					$i += $interval;
				}
			break;
			case 'hour':
				for ($i = 1; $i <= 12; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'hour24':
				for ($i = 0; $i <= 23; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'meridian':
				$data = array('am' => 'am', 'pm' => 'pm');
			break;
			case 'day':
				$min = 1;
				$max = 31;

				if (isset($options['min'])) {
					$min = $options['min'];
				}
				if (isset($options['max'])) {
					$max = $options['max'];
				}

				for ($i = $min; $i <= $max; $i++) {
					$data[sprintf('%02d', $i)] = $i;
				}
			break;
			case 'month':
				if ($options['monthNames']) {
					$data['01'] = __('January', true);
					$data['02'] = __('February', true);
					$data['03'] = __('March', true);
					$data['04'] = __('April', true);
					$data['05'] = __('May', true);
					$data['06'] = __('June', true);
					$data['07'] = __('July', true);
					$data['08'] = __('August', true);
					$data['09'] = __('September', true);
					$data['10'] = __('October', true);
					$data['11'] = __('November', true);
					$data['12'] = __('December', true);
				} else {
					for ($m = 1; $m <= 12; $m++) {
						$data[sprintf("%02s", $m)] = strftime("%m", mktime(1, 1, 1, $m, 1, 1999));
					}
				}
			break;
			case 'year':
				$current = intval(date('Y'));

				if (!isset($options['min'])) {
					$min = $current - 20;
				} else {
					$min = $options['min'];
				}

				if (!isset($options['max'])) {
					$max = $current + 20;
				} else {
					$max = $options['max'];
				}
				if ($min > $max) {
					list($min, $max) = array($max, $min);
				}
				for ($i = $min; $i <= $max; $i++) {
					$data[$i] = $i;
				}
				$data = array_reverse($data, true);
			break;
			// >>> CUSTOMIZE ADD 2011/01/11 ryuring	和暦対応
			case 'wyear':
				$current = intval(date('Y'));

				if (!isset($options['min'])) {
					$min = $current - 20;
				} else {
					$min = $options['min'];
				}

				if (!isset($options['max'])) {
					$max = $current + 20;
				} else {
					$max = $options['max'];
				}
				if ($min > $max) {
					list($min, $max) = array($max, $min);
				}
				for ($i = $min; $i <= $max; $i++) {
					$wyears = $this->BcTime->convertToWarekiYear($i);
					if($wyears) {
						foreach($wyears as $value) {
							list($w,$year) = explode('-', $value);
							$data[$value] = $this->BcTime->nengo($w).' '.$year;
						}
					}
				}
				$data = array_reverse($data, true);
				break;
			// <<<
		}
		$this->__options[$name] = $data;
		return $this->__options[$name];
		
	}
/**
 * Creates a checkbox input widget.
 * MODIFIED 2008/10/24 egashira
 *          hiddenタグを出力しないオプションを追加
 *
 * @param string $fieldNamem Name of a field, like this "Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 *		'value' - the value of the checkbox
 *		'checked' - boolean indicate that this checkbox is checked.
 * @todo Right now, automatically setting the 'checked' value is dependent on whether or not the
 * 		 checkbox is bound to a model.  This should probably be re-evaluated in future versions.
 * @return string An HTML text input element
 * @access public
 */
	function checkbox($fieldName, $options = array()) {
		
		// CUSTOMIZE ADD 2011/05/07 ryuring
		// >>> hiddenをデフォルトオプションに追加
		$options = array_merge(array('hidden' => true), $options);
		$hidden = $options['hidden'];
		unset($options['hidden']);
		// <<<
		
		$options = $this->_initInputField($fieldName, $options);
		$value = current($this->value());

		if (!isset($options['value']) || empty($options['value'])) {
			$options['value'] = 1;
		} elseif (
			(!isset($options['checked']) && !empty($value) && $value === $options['value']) ||
			!empty($options['checked'])
		) {
			$options['checked'] = 'checked';
		}

		// CUSTOMIZE MODIFY 2011/05/07 ryuring
		// >>> hiddenオプションがある場合のみ、hiddenタグを出力
		/*$hiddenOptions = array(
			'id' => $options['id'] . '_', 'name' => $options['name'],
			'value' => '0', 'secure' => false
		);
		if (isset($options['disabled']) && $options['disabled'] == true) {
			$hiddenOptions['disabled'] = 'disabled';
		}
		$output = $this->hidden($fieldName, $hiddenOptions);*/
		// ---
		if($hidden) {
			$hiddenOptions = array(
				'id' => $options['id'] . '_', 'name' => $options['name'],
				'value' => '0', 'secure' => false
			);
			if (isset($options['disabled']) && $options['disabled'] == true) {
				$hiddenOptions['disabled'] = 'disabled';
			}
			$output = $this->hidden($fieldName, $hiddenOptions);
		}else {
			$output='';
		}
		// <<<
		
		// CUSTOMIZE MODIFY 2011/05/07 ryuring
		// >>> label を追加
		/*return $this->output($output . sprintf(
			$this->Html->tags['checkbox'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		));*/
		// ---
		if(!empty($options['label'])) {
			$label = '&nbsp;'.parent::label($fieldName, $options['label']);
		}else {
			$label = '';
		}
		return $this->output($output . sprintf(
			$this->Html->tags['checkbox'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		)).$label;
		// <<<
		
	}
/**
 * Returns an array of formatted OPTION/OPTGROUP elements
 * 
 * @return array
 * @access private
 */
	function __selectOptions($elements = array(), $selected = null, $parents = array(), $showParents = null, $attributes = array()) {
		 
		$select = array();
		$attributes = array_merge(array('escape' => true, 'style' => null), $attributes);
		$selectedIsEmpty = ($selected === '' || $selected === null);
		$selectedIsArray = is_array($selected);

		foreach ($elements as $name => $title) {
			$htmlOptions = array();
			if (is_array($title) && (!isset($title['name']) || !isset($title['value']))) {
				if (!empty($name)) {
					if ($attributes['style'] === 'checkbox') {
						$select[] = $this->Html->tags['fieldsetend'];
					} else {
						$select[] = $this->Html->tags['optiongroupend'];
					}
					$parents[] = $name;
				}
				$select = array_merge($select, $this->__selectOptions(
						$title, $selected, $parents, $showParents, $attributes
				));

				if (!empty($name)) {
					if ($attributes['style'] === 'checkbox') {
						$select[] = sprintf($this->Html->tags['fieldsetstart'], $name);
					} else {
						$select[] = sprintf($this->Html->tags['optiongroup'], $name, '');
					}
				}
				$name = null;
			} elseif (is_array($title)) {
				$htmlOptions = $title;
				$name = $title['value'];
				$title = $title['name'];
				unset($htmlOptions['name'], $htmlOptions['value']);
			}

			if ($name !== null) {
				if ((!$selectedIsEmpty && $selected == $name) || ($selectedIsArray && in_array($name, $selected))) {
					if ($attributes['style'] === 'checkbox') {
						$htmlOptions['checked'] = true;
					} else {
						$htmlOptions['selected'] = 'selected';
					}
				}

				if ($showParents || (!in_array($title, $parents))) {
					$title = ($attributes['escape']) ? h($title) : $title;

					if ($attributes['style'] === 'checkbox') {
						$htmlOptions['value'] = $name;

						$tagName = Inflector::camelize(
								$this->model() . '_' . $this->field().'_'.Inflector::underscore($name)
						);
						$htmlOptions['id'] = $tagName;
						$label = array('for' => $tagName);

						if (isset($htmlOptions['checked']) && $htmlOptions['checked'] === true) {
							$label['class'] = 'selected';
						}

						list($name) = array_values($this->__name());

						if (empty($attributes['class'])) {
							$attributes['class'] = 'checkbox';
						}
						$label = $this->label(null, $title, $label);
						$item = sprintf(
								$this->Html->tags['checkboxmultiple'], $name,
								$this->Html->_parseAttributes($htmlOptions)
						);
						// checkboxのdivを外せるオプションを追加
						if(isset($attributes['div']) && $attributes['div']===false) {
							$select[] = $item.$label;
						}else {
							$select[] = $this->Html->div($attributes['class'], $item . $label);
						}
					} else {
						$select[] = sprintf(
								$this->Html->tags['selectoption'],
								$name, $this->Html->_parseAttributes($htmlOptions), $title
						);
					}
				}
			}
		}

		return array_reverse($select, true);
		
	}
/**
 * Returns a formatted SELECT element.
 * Attributes:
 *
 * - 'showParents' - If included in the array and set to true, an additional option element
 *   will be added for the parent of each option group.
 * - 'multiple' - show a multiple select box.  If set to 'checkbox' multiple checkboxes will be
 *   created instead.
 *
 * @param string $fieldName Name attribute of the SELECT
 * @param array $options Array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the
 *    SELECT element
 * @param mixed $selected The option selected by default.  If null, the default value
 *   from POST data will be used when available.
 * @param array $attributes The HTML attributes of the select element.
 * @param mixed $showEmpty If true, the empty select option is shown.  If a string,
 *   that string is displayed as the empty element.
 * @return string Formatted SELECT element
 * @access public
 */
	function select($fieldName, $options = array(), $selected = null, $attributes = array(), $showEmpty = '') {
		
		$select = array();
		$showParents = false;
		$escapeOptions = true;
		$style = null;
		$tag = null;

		if (isset($attributes['escape'])) {
			$escapeOptions = $attributes['escape'];
			unset($attributes['escape']);
		}
		$attributes = $this->_initInputField($fieldName, array_merge(
			(array)$attributes, array('secure' => false)
		));

		if (is_string($options) && isset($this->__options[$options])) {
			$options = $this->__generateOptions($options);
		} elseif (!is_array($options)) {
			$options = array();
		}
		if (isset($attributes['type'])) {
			unset($attributes['type']);
		}
		if (in_array('showParents', $attributes)) {
			$showParents = true;
			unset($attributes['showParents']);
		}

		if (!isset($selected)) {
			$selected = $attributes['value'];
		}

		if (isset($attributes) && array_key_exists('multiple', $attributes)) {
			$style = ($attributes['multiple'] === 'checkbox') ? 'checkbox' : null;
			$template = ($style) ? 'checkboxmultiplestart' : 'selectmultiplestart';
			$tag = $this->Html->tags[$template];
			// >>> CUSTOMIZE MODIFY 2011/01/21 ryuring
			// multiplecheckboxの場合にhiddenをつけないオプションを追加
			//$select[] = $this->hidden(null, array('value' => '', 'id' => null, 'secure' => false));
			// ---
			if(!isset($attributes['hidden']) || $attributes['hidden']!==false) {
				$select[] = $this->hidden(null, array('value' => '', 'id' => null, 'secure' => false));
			}
			// <<<
		} else {
			$tag = $this->Html->tags['selectstart'];
		}

		if (!empty($tag) || isset($template)) {
			$this->__secure();
			$select[] = sprintf($tag, $attributes['name'], $this->_parseAttributes(
				$attributes, array('name', 'value'))
			);
		}
		$emptyMulti = (
			$showEmpty !== null && $showEmpty !== false && !(
				empty($showEmpty) && (isset($attributes) &&
				array_key_exists('multiple', $attributes))
			)
		);

		if ($emptyMulti) {
			$showEmpty = ($showEmpty === true) ? '' : $showEmpty;
			$options = array_reverse($options, true);
			$options[''] = $showEmpty;
			$options = array_reverse($options, true);
		}

		// divを追加すぐ下の__selectOptionsのみで利用
		if(isset($attributes['div'])) {
			if($attributes['div']=='false') {
				$attributes['div'] = false;
			}
			$div = $attributes['div'];
		}else {
			$div = null;
		}
		$_select = $this->__selectOptions(
			array_reverse($options, true),
			$selected,
			array(),
			$showParents,
			array('escape' => $escapeOptions, 'style' => $style, 'div' => $div)
		);
		if(!empty($attributes['separator'])) {
			$separator = $attributes['separator']."\n";
		}else {
			$separator = "\n";
		}
		$select[] = implode($separator, $_select);

		$template = ($style == 'checkbox') ? 'checkboxmultipleend' : 'selectend';
		$select[] = $this->Html->tags[$template];

		// 解除ボタンを追加（jQuery必須)
		if(isset($attributes['multiple']) && $attributes['multiple'] === true) {
			list($model,$field) = explode(".",$fieldName);
			$tagName = Inflector::camelize($model . '_' . $field);
			$out = '<script type="text/javascript">';
			$out .= "jQuery(document).ready(function() {";
			$out .= "jQuery('#".$tagName."Clear').click(function(){";
			$out .= "jQuery('#".$tagName."').val('');";
			$out .= "});";
			$out .= "});";
			$out .= "</script>";
			$out .= '<input type="button" name="'.$tagName.'Clear" id="'.$tagName.'Clear" value="　解　除　" />';
			return $this->output(implode("\n", $select))."<br />".$out;
		}else {
			return $this->output(implode("\n", $select));
		}
		
	}
/**
 * 文字列保存用複数選択コントロール
 * 
 * @param string $fieldName
 * @param array $options
 * @param mixed $selected
 * @param array $attributes
 * @param mixed $showEmpty
 * @return string
 * @access public
 */
	function selectText($fieldName, $options = array(), $selected = null, $attributes = array(), $showEmpty = '') {

		$_attributes = array('separator'=>'<br />','quotes'=>true);
		$attributes = Set::merge($_attributes,$attributes);

		$quotes = $attributes['quotes'];
		unset($attributes['quotes']);

		$_options = $this->_initInputField($fieldName, $options);
		if(empty($attributes['multiple'])) $attributes['multiple'] = 'checkbox';
		$id = $_options['id'];
		$_id = $_options['id'].'_';
		$name = $_options['name'];
		$out = '<div id="'.$_id.'">'.$this->select($fieldName.'_', $options, $selected, $attributes, $showEmpty).'</div>';
		$out .= $this->hidden($fieldName);
		$script = <<< DOC_END
$(document).ready(function() {
    aryValue = $("#{$id}").val().replace(/\'/g,"").split(",");
    for(key in aryValue){
        var value = aryValue[key];
        $("#"+camelize("{$id}_"+value)).attr('checked',true);
    }
    $("#{$_id} input[type=checkbox]").change(function(){
        var aryValue = [];
        $("#{$_id} input[type=checkbox]").each(function(key,value){
            if($(this).attr('checked')){
                aryValue.push("'"+$(this).val()+"'");
            }
        });
        $("#{$id}").val(aryValue.join(','));
    });
});
DOC_END;
		$out .= $this->Javascript->codeBlock($script);
		return $out;
		
	}
/**
 * Creates a hidden input field.
 *
 * @param string $fieldName Name of a field, in the form"Modelname.fieldname"
 * @param array $options Array of HTML attributes.
 * @return string
 * @access public
 */
	function hidden($fieldName, $options = array()) {
		
		$secure = true;

		if (isset($options['secure'])) {
			$secure = $options['secure'];
			unset($options['secure']);
		}

		// 2010/07/24 ryuring
		// セキュリティコンポーネントのトークン生成の仕様として、
		// ・hiddenタグ以外はフィールド情報のみ
		// ・hiddenタグはフィールド情報と値
		// をキーとして生成するようになっている。
		// その場合、生成の元のなる値は、multipleを想定されておらず、先頭の値のみとなるが
		// multiple な hiddenタグの場合、送信される値は配列で送信されるので値違いで認証がとおらない。
		// という事で、multiple の場合は、あくまでhiddenタグ以外のようにフィールド情報のみを
		// トークンのキーとする事で認証を通すようにする。
		// >>> ADD
		if(!empty($options['multiple'])){
			$secure = false;
			$this->__secure();
		}
		// <<<

		$options = $this->_initInputField($fieldName, array_merge(
			$options, array('secure' => false)
		));
		$model = $this->model();

		if ($fieldName !== '_method' && $model !== '_Token' && $secure) {
			$this->__secure(null, '' . $options['value']);
		}

		// CUSTOMIZE 2010/07/24 ryuring
		// 配列用のhiddenタグを出力できるオプションを追加
		// CUSTOMIZE 2010/08/01 ryuring
		// class属性を指定できるようにした
		// CUSTOMIZE 2011/03/11 ryuring
		// multiple で送信する値が配列の添字となっていたので配列の値に変更した
		// >>> ADD
		$multiple = false;
		$value = '';
		if(!empty($options['multiple'])){
			$multiple = true;
			$tagType = 'hiddenmultiple';
			$options['id'] = null;
			if (!isset($options['value'])) {
				$value = $this->value($fieldName);
			}else {
				$value = $options['value'];
			}
			if(is_array($value) && !$value) {
				unset($options['value']);
			}
			unset($options['multiple']);
		} else {
			$tagType = 'hidden';
		}
		// <<<
		// >>> MODIFY
		/*return $this->output(sprintf(
		$this->Html->tags['hidden'],
			$options['name'],
			$this->_parseAttributes($options, array('name', 'class'), '', ' ')
		));*/
		// ---
		if($multiple && is_array($value)) {
			$out = array();
			foreach($value as $_value) {
				$options['value'] = $_value;
				$out[] = $this->output(sprintf(
					$this->Html->tags[$tagType],
					$options['name'],
					$this->_parseAttributes($options, array('name'), '', ' ')
				));
			}
			return implode("\n", $out);
		} else {
			return $this->output(sprintf(
				$this->Html->tags[$tagType],
				$options['name'],
				$this->_parseAttributes($options, array('name'), '', ' ')
			));
		}
		// <<<
		
	}
/**
 * CKEditorを出力する
 *
 * @param	string	$fieldName
 * @param	array	$options
 * @param	array	$editorOptions
 * @param	array	$styles
 * @return	string
 * @access	public
 */
	function ckeditor($fieldName, $options = array(), $editorOptions = array(), $styles = array()) {

		$_options = array('type'=>'textarea');
		$options = am($_options,$options);
		$method = $options['type'];
		return $this->BcCkeditor->{$method}($fieldName, $options, $editorOptions, $styles, $this);

	}
/**
 * create
 * フック用にラッピング
 * 
 * @param array $model
 * @param array $options
 * @return string
 * @access public
 */
	function create($model = null, $options = array()) {

		$this->__id = $this->_getId($model, $options);
		
		$options = $this->executeHook('beforeFormCreate', $this->__id, $model, $options);
		
		$out = parent::create($model, $options);

		return $this->executeHook('afterFormCreate', $this->__id, $out);
		
	}
/**
 * end
 * フック用にラッピング
 *
 * @param	array	$options
 * @return	string
 * @access	public
 */
	function end($options = null) {

		$id = $this->__id;
		$this->__id = null;
		$options = $this->executeHook('beforeFormEnd', $id, $options);

		$out = parent::end($options);
		
		return $this->executeHook('afterFormEnd', $id, $out);
		
	}
/**
 * Generates a form input element complete with label and wrapper div
 *
 * Options - See each field type method for more information. Any options that are part of
 * $attributes or $options for the different type methods can be included in $options for input().
 *
 * - 'type' - Force the type of widget you want. e.g. ```type => 'select'```
 * - 'label' - control the label
 * - 'div' - control the wrapping div element
 * - 'options' - for widgets that take options e.g. radio, select
 * - 'error' - control the error message that is produced
 *
 * @param string $fieldName This should be "Modelname.fieldname"
 * @param array $options Each type of input takes different options.
 * @return string Completed form widget
 */
	function input($fieldName, $options = array()) {

		$options = $this->executeHook('beforeFormInput', $fieldName, $options);
		
		$type = '';
		if(isset($options['type'])) {
			$type = $options['type'];
		}

		if(!isset($options['div'])) {
			$options['div'] = false;
		}

		if(!isset($options['error'])) {
			$options['error'] = false;
		}

		switch($type) {
			case 'text':
			default :
				if(!isset($options['label'])) {
					$options['label'] = false;
				}
				break;
			case 'radio':
				if(!isset($options['legend'])) {
					$options['legend'] = false;
				}
				if(!isset($options['separator'])) {
					$options['separator'] = '　';
				}
				break;
		}

		$out = parent::input($fieldName, $options);

		/* カウンター */
		if(!empty($options['counter'])) {
			$domId = $this->domId($fieldName, $options);
			$counter = '<span id="'.$domId.'Counter'.'" class="size-counter"></span>';
			$script = '$("#'.$domId.'").keyup(countSize);$("#'.$domId.'").keyup();';
			if(!$this->sizeCounterFunctionLoaded) {
				$script .= <<< DOC_END
function countSize() {
	var len = $(this).val().length;
	var maxlen = $(this).attr('maxlength');
	if(!maxlen || maxlen == -1){
		maxlen = '-';
	}
	$("#"+$(this).attr('id')+'Counter').html(len+'/<small>'+maxlen+'</small>');
}
DOC_END;
				$this->sizeCounterFunctionLoaded = true;
			}
			$out = $out.$counter.$this->Javascript->codeBlock($script);
		}
		
		return $this->executeHook('afterFormInput', $fieldName, $out);
		
	}
/**
 * フォームのIDを取得する
 * BcForm::create より呼出される事が前提
 * 
 * @param string $model
 * @param array $options
 * @return string
 */
	function _getId($model = null, $options = array()) {
		
		if (is_array($model) && empty($options)) {
			$model = null;
		}

		if (empty($model) && $model !== false && !empty($this->params['models'])) {
			$model = $this->params['models'][0];
		} elseif (empty($model) && empty($this->params['models'])) {
			$model = false;
		} elseif (is_string($model) && strpos($model, '.') !== false) {
			$path = explode('.', $model);
			$model = $path[count($path) - 1];
		}
		
		if (ClassRegistry::isKeySet($model)) {
			$object =& ClassRegistry::getObject($model);
		}
		
		if (isset($object)) {
			$key = $object->primaryKey;
			$data = compact('key');
		} else {
			$data = $this->fieldset;
		}
		
		$recordExists = (
			isset($this->data[$model]) &&
			isset($this->data[$model][$data['key']]) &&
			!empty($this->data[$model][$data['key']])
		);
		
		$created = false;
		if ($recordExists) {
			$created = true;
		}
		
		if (empty($options['url']) || is_array($options['url'])) {
			if (empty($options['action'])) {
				$options['action'] = ($created) ? 'edit' : 'add';
			}
		}
		
		if (!empty($options['action']) && !isset($options['id'])) {
			$id = $model . Inflector::camelize($options['action']) . 'Form';
		} elseif(isset($options['id'])) {
			$id = $options['id'];
		} else {
			$id = '';
		}
		return $id;
		
	}
/**
 * 日付タグ
 * 和暦実装
 * TODO 未実装
 */
	/*function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $selected = null, $attributes = array(), $showEmpty = true) {

		if($dateFormat == "WYMD"){
			$this->options['month'] = $this->getWarekiMonthes();
			$this->options['day'] = $this->getWarekiDays();
			$this->options['year'] = $this->getWarekiYears($attributes['minYear'],$attributes['maxYear']);
			$dateFormat = "YMD";

		}
		return parent::dateTime($fieldName, $dateFormat, $timeFormat, $selected, $attributes, $showEmpty);

	}*/
}