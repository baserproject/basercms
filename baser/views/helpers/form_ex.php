<?php
/* SVN FILE: $Id$ */
/**
 * FormHelper 拡張クラス
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
App::import("Helper",array("Form","TimeEx","TextEx"));
/**
 * FormHelper 拡張クラス
 *
 * @package			baser.views.helpers
 */
class FormExHelper extends FormHelper {
/**
 * ヘルパー
 *
 * @var		array
 * @access	public
 */
	var $helpers = array('Html','TimeEx',"TextEx","Javascript");	
/**
 * 都道府県用のSELECTタグを表示する
 * 
 * @param 	string	$fieldName Name attribute of the SELECT
 * @param 	mixed 	$selected Selected option
 * @param 	array 	$attributes Array of HTML options for the opening SELECT element
 * @return 	string 	都道府県用のSELECTタグ
 * @access 	public
 */
	function prefTag($fieldName, $selected = null, $attributes = array()){

		$pref = $this->TextEx->prefList();
		return $this->select($fieldName, $pref, $selected, $attributes, false);
		
	}
/**
 * dateTime 拡張
 *
 * @param 	string 	$fieldName Prefix name for the SELECT element
 * @param 	string 	$dateFormat DMY, MDY, YMD or NONE.
 * @param 	string 	$timeFormat 12, 24, NONE
 * @param 	string 	$selected Option which is selected.
 * @param 	string 	$attributes array of Attributes
 *						'monthNames' If set and false numbers will be used for month select instead of text.
 *						'minYear' The lowest year to use in the year select
 *						'maxYear' The maximum year to use in the year select
 *						'interval' The interval for the minutes select. Defaults to 1
 *						'separator' The contents of the string between select elements. Defaults to '-'
 * @param 	bool 	$showEmpty Whether or not to show an empty default value.
 * @return 	string 	The HTML formatted OPTION element
 * @access 	public
 */
	function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $selected = null, $attributes = array(), $showEmpty = true) {
		
		// $nengoを追加
		$year = $month = $day = $hour = $min = $meridian = $nengo = null;

		if (empty($selected)) {
			$selected = $this->value($fieldName);
		}

		if ($selected === null && $showEmpty !== true) {
			$selected = time();
		}

		if (!empty($selected)) {
			if (is_array($selected)) {
				extract($selected);
			} else {
				if (is_int($selected)) {
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
		$defaults = array('minYear' => null, 'maxYear' => null, 'separator' => '-', 'interval' => 1, 'monthNames' => true);
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
		
			if(substr($dateFormat,0,1)=="W"){
				//$this->options['year'] = $this->getWarekiYears($attributes['minYear'],$attributes['maxYear']);
			}
		
			$selects = array();
			foreach (preg_split('//', $dateFormat, -1, PREG_SPLIT_NO_EMPTY) as $char) {
				switch ($char) {
					case 'W':
						$selects[] = $this->nengo($fieldName, $nengo, $selectYearAttr, $showEmpty);
					break;
					case 'Y':
						if(substr($dateFormat,0,1)=="W"){
							$selects[] = $this->wyear($fieldName,$year)."年";
						}else{
							$selects[] = $this->year($fieldName, $minYear, $maxYear, $year, $selectYearAttr, $showEmpty)."年";
						}
					break;
					case 'M':
						$selectMonthAttr['monthNames'] = $monthNames;
						$selects[] = $this->month($fieldName, $month, $selectMonthAttr, $showEmpty)."月";
					break;
					case 'D':
						$selects[] = $this->day($fieldName, $day, $selectDayAttr, $showEmpty)."日";
					break;
				}
			}
			$opt = implode($separator, $selects);
		}

		switch($timeFormat) {
			case '24':
				$opt .= $this->hour($fieldName, true, $hour, $selectHourAttr, $showEmpty) . ':' .
				$this->minute($fieldName, $min, $selectMinuteAttr, $showEmpty);
			break;
			case '12':
				$selectMinuteAttr['interval'] = $interval;
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
 * 年号選択タグ
 *
 * @param	string	$fieldName Prefix name for the SELECT element
 * @param	string	$selected Option which is selected.
 * @param	array	$attributes Attribute array for the select elements.
 * @param	boolean	$showEmpty Show/hide the empty select option
 * @return	string
 * @access	public
 */
	function nengo($fieldName, $selected = null, $attributes = array(), $showEmpty = true) {

		if ((empty($selected) || $selected === true) && $value = $this->value($fieldName)) {
			if (is_array($value)) {
				extract($value);
				$selected = $nengo;
			} else {
				if (empty($value)) {
					if (!$showEmpty) {
						$selected = 'now';

					} elseif (!$showEmpty && !$selected) {
						$selected = "h";
					}
				} else {
					$selected = $value;
				}
			}
		}

		if (strlen($selected) > 1 || $selected === 'now') {
			$wareki = $this->TimeEx->convertWareki($selected);
			$selected = $wareki['n'];
		} elseif ($selected === false) {
			$selected = null;
		}
		return $this->select($fieldName . ".nengo", $this->__generateOptions("nengo"), $selected, $attributes, $showEmpty);

	}
/**
 * 和暦の年（初期値を設定した際に、和暦に変換される）
 *
 * @param	string	$fieldName Prefix name for the SELECT element
 * @param	string	$selected Option which is selected.
 * @return	string
 * @access	public
 */
	function wyear($fieldName,$selected){
			
		if ((empty($selected) || $selected === true) && $value = $this->value($fieldName)) {
			if (is_array($value)) {
				extract($value);
				$selected = $year;
			} else {
				if (empty($value)) {
					$selected = 'now';
				} else {
					$selected = $value;
				}
			}
		}

		if (strlen($selected) > 4 || $selected === 'now') {
			$selectedYear = date('Y', strtotime($selected));
			$wareki = $this->TimeEx->convertWareki($selected);
			$selectedWyear = $wareki['Y'];
		} elseif ($selected === false) {
			$selectedYear = null;
			$selectedWyear = null;
		} else {
			$selectedYear = "";
			$selectedWyear = "";
		}
		
		return $this->text($fieldName.".wyear",array("size"=>4,"value"=>$selectedWyear)).$this->hidden($fieldName.".year",array("size"=>4,"value"=>$selectedYear));
		
	}
/**
 * コントロールソースを取得する
 *
 * Model側でメソッドを用意しておく必要がある
 *
 * @param	string	フィールド名
 * @return 	array	コントロールソース
 * @access	public
 */
	function getControlSource($field,$options = array()){

		if(strpos($field,'.') !== false){
			list($modelName,$field) = explode('.',$field);
		}
		if(!empty($modelName)){
			$model =& ClassRegistry::getObject($modelName);
		}else{
			$model =& ClassRegistry::getObject($this->model());
		}
		if($model){
			return $model->getControlSource($field,$options);
		}else{
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
 * @return mixed    リストまたは、false
 */
    function generateList($modelName,$conditions = array(),$fields = array(),$order = array()){

        $model =& ClassRegistry::getObject($modelName);

        if($fields){
            list($idField,$displayField) = $fields;
        }else{
            $idField = 'id';
            $displayField = $model->getDisplayField();
            $fields = array($idField,$displayField);
        }
        
        $list = $model->findAll($conditions,$fields,$order);

        if($list){
            return Set::combine($list,"{n}.".$modelName.".".$idField,"{n}.".$modelName.".".$displayField);
        }else{
            return null;
        }
        
    }
/**
 * JsonList
 *
 * @param	string	フィールド文字列
 * @return 	array	属性
 * @access	public
 */
	function jsonList($field,$attributes){
	
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
 *
 * jquery-ui-1.7.2 必須
 *
 * @param 	string	フィールド文字列
 * @param	array	HTML属性
 * @return	string	html
 * @access 	public
 */
	function datepicker($fieldName, $attributes = array()){

		if (!isset($attributes['value'])) {
            $value = $this->value($fieldName);
		}else{
            $value = $attributes['value'];
		}

        if($value){
            $attributes['value'] = date('Y/m/d',strtotime($this->TimeEx->format('Y/m/d',$value)));
        }else{
            unset($attributes['value']);
        }

		$this->setEntity($fieldName);
		$id = $this->domId($fieldName);

		// テキストボックス
		$input = $this->text($fieldName,$attributes);

        // javascript
        $script = "<script type=\"text/javascript\">";
        $script .= 'jQuery(function($){';
        $script .= '$("#'.$id.'").datepicker()';
        $script .= '});';
        $script .= "</script>";

		$out = $input."\n".$script;
		return $out;

	}
/**
 * 日付カレンダーと時間フィールド
 * @param string $fieldName
 * @param array $attributes
 * @return string
 */
    function dateTimePicker($fieldName, $attributes = array()){

        $timeAttributes = array('size'=>8,'maxlength'=>8);
		if (!isset($attributes['value'])) {
            $value = $this->value($fieldName);
		}else{
            $value = $attributes['value'];
            unset($attributes['value']);
		}
        if($value){
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
        $("#{$domId}").val($("#{$domId}Date").val().replace(/\//g, '-')+' '+$("#{$domId}Time").val());
   }
});
DOC_END;
        $script = $this->Javascript->codeBlock($_script,array('inline'=>false));
        return $dateTag.$timeTag.$hiddenTag;
    }
/**
 * Generates option lists for common <select /> menus
 *
 * @param	string	$name
 * @param	array	$options
 * @return	array	option lists
 * @access 	private
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
				if (!isset($options['min'])) {
					$min = 1;
				} else {
					$min = $options['min'];
				}

				if (!isset($options['max'])) {
					$max = 31;
				} else {
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
			case 'nengo':
				$data = $this->TimeEx->getNengos();
			break;
		}
		$this->__options[$name] = $data;
		return $this->__options[$name];
	}
/**
 * Creates a checkbox input widget.
 *
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
 */
	function checkbox($fieldName, $options = array()) {

        /* hiddenをデフォルトオプションに追加 */
        $options = array_merge(array('hidden' => true), $options);
        $hidden = $options['hidden'];
        unset($options['hidden']);

		$options = $this->_initInputField($fieldName, $options);
		$value = current($this->value());

		if (!isset($options['value']) || empty($options['value'])) {
			$options['value'] = 1;
		} elseif (!empty($value) && $value === $options['value']) {
			$options['checked'] = 'checked';
		}

        // hiddenオプションがある場合のみ、hiddenタグを出力
        if($hidden){
            $output = $this->hidden($fieldName, array(
                'id' => $options['id'] . '_', 'name' => $options['name'],
                'value' => '0', 'secure' => false
    		));
        }else{
            $output='';
        }
		/* label を追加 */
        if(!empty($options['label'])){
			$label = '&nbsp;'.parent::label($fieldName, $options['label']);
		}else{
			$label = '';
		}
		return $this->output($output . sprintf(
			$this->Html->tags['checkbox'],
			$options['name'],
			$this->_parseAttributes($options, array('name'), null, ' ')
		)).$label;
	}
/**
 * Returns an array of formatted OPTION/OPTGROUP elements
 * @access private
 * @return array
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
                        if(isset($attributes['div']) && $attributes['div']===false){
                            $select[] = $item.$label;
                        }else{
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
 *
 * @param string $fieldName Name attribute of the SELECT
 * @param array $options Array of the OPTION elements (as 'value'=>'Text' pairs) to be used in the
 * 						 SELECT element
 * @param mixed $selected The option selected by default.  If null, the default value
 *						  from POST data will be used when available.
 * @param array $attributes	 The HTML attributes of the select element.
 *		'showParents' - If included in the array and set to true, an additional option element
 *						will be added for the parent of each option group.
 *		'multiple' - show a multiple select box.  If set to 'checkbox' multiple checkboxes will be
 * 					 created instead.
 * @param mixed $showEmpty If true, the empty select option is shown.  If a string,
 *						   that string is displayed as the empty element.
 * @return string Formatted SELECT element
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
			if ($attributes['multiple'] === 'checkbox') {
				$tag = $this->Html->tags['checkboxmultiplestart'];
				$style = 'checkbox';
			} else {
				$tag = $this->Html->tags['selectmultiplestart'];
			}
            // multiplecheckboxの場合にhiddenをつけないオプションを追加
            if(!isset($attributes['hidden']) || $attributes['hidden']!==false){
                $select[] = $this->hidden(null, array('value' => '', 'id' => null));
            }
		} else {
			$tag = $this->Html->tags['selectstart'];
		}

		if (!empty($tag)) {
			$this->__secure();
			$select[] = sprintf($tag, $attributes['name'], $this->_parseAttributes(
				$attributes, array('name', 'value'))
			);
		}
		$emptyMulti = (
			$showEmpty !== null && $showEmpty !== false &&
			!(empty($showEmpty) && (isset($attributes) && array_key_exists('multiple', $attributes)))
		);

		if ($emptyMulti) {
			if ($showEmpty === true) {
				$showEmpty = '';
			}
			$options = array_reverse($options, true);
			$options[''] = $showEmpty;
			$options = array_reverse($options, true);
		}

        // divを追加すぐ下の__selectOptionsのみで利用
        if(isset($attributes['div'])){
						if($attributes['div']=='false'){
							$attributes['div'] = false;
						}
            $div = $attributes['div'];
        }else{
            $div = null;
        }
		$select = array_merge($select, $this->__selectOptions(
			array_reverse($options, true),
			$selected,
			array(),
			$showParents,
			array('escape' => $escapeOptions, 'style' => $style, 'div' => $div)
		));

		if ($style == 'checkbox') {
			$select[] = $this->Html->tags['checkboxmultipleend'];
		} else {
			$select[] = $this->Html->tags['selectend'];
		}

        // 解除ボタンを追加（jQuery必須)
        if(isset($attributes['multiple']) && $attributes['multiple'] === true){
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
		}else{
            if(!empty($attributes['separator'])){
                $separator = $attributes['separator'];
            }else{
                $separator = "\n";
            }
			return $this->output(implode($separator, $select));
		}


		
	}
/**
 * 文字列保存用複数選択コントロール
 * @param string $fieldName
 * @param array $options 
 * @param mixed $selected
 * @param array $attributes
 * @param mixed $showEmpty
 * @return string
 */
    function selectText($fieldName, $options = array(), $selected = null, $attributes = array(), $showEmpty = ''){

        $_attributes = array('separator'=>'<br />','quotes'=>true);
        $attributes = Set::merge($_attributes,$attributes);

        $quotes = $attributes['quotes'];
        unset($attributes['quotes']);

        $_options = $this->_initInputField($fieldName, $options);
        if(empty($attributes['multiple'])) $attributes['multiple'] = 'checkbox';
        $id = $_options['id'];
        $_id = $_options['id'].'_';
        $name = $_options['name'];
        $out = '<span id="'.$_id.'">'.$this->select($fieldName.'_', $options, $selected, $attributes, $showEmpty).'</span>';
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

?>