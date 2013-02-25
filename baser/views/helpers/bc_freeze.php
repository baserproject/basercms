<?php
/* SVN FILE: $Id$ */
/**
 * FreezeHelper
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
App::import('Helper', BC_FORM_HELPER, BC_UPLOAD_HELPER);
/**
 * @package baser.view.helpers
 */
class BcFreezeHelper extends BcFormHelper {
/**
 * 凍結状態
 * 
 * @var boolean
 * @access public
 */
	var $freezed = false;
/**
 * ヘルパー
 * 
 * @var array
 * @access public
 */
	var $helpers = array('Html', BC_FORM_HELPER, BC_UPLOAD_HELPER, BC_TEXT_HELPER, BC_TIME_HELPER, 'Javascript');
/**
 * フォームを凍結させる
 * 
 * @return void
 * @access public
 */
	function freeze() {
		
		$this->freezed = true;
	
	}
/**
 * テキストボックスを表示する
 * 
 * @param string $fieldName フィールド文字列
 * @param array $attributes html属性
 * @return	string	htmlタグ
 * @access	public
 */
	function text($fieldName,$attributes = array()) {

		if($this->freezed) {
			list($model,$field) = explode('.',$fieldName);
			if(isset($attributes)) {
				$attributes = $attributes + array('type'=>'hidden');
			}else {
				$attributes = array('type'=>'hidden');
			}
			if(isset($attributes["value"])) {
				$value = $attributes["value"];
			}else {
				$value = $this->data[$model][$field];
			}
			return parent::text($fieldName, $attributes) . $value;
		}else {
			return parent::text($fieldName, $attributes);
		}

	}
/**
 * select プルダウンメニューを表示
 * 
 * @param	string $fieldName フィールド文字列
 * @param	array $options コントロールソース
 * @param	mixed $selected
 * @param	array $attributes html属性
 * @param	array	空データの表示有無
 * @return	string $showEmpty htmlタグ
 * @access	public
 */
	function select($fieldName, $options, $selected = null, $attributes = array(), $showEmpty = true) {

		if($this->freezed) {
			return $this->freezeControll($fieldName, $options, $attributes);
		}else {
			// 横幅を設定する
			// 指定した文字数より足りない文字数分スペースを埋める処理としている為、
			// 等幅フォントを設定しないとちゃんとした横幅にはならない
			if(!empty($attributes['cols'])) {
				foreach($options as $key => $option) {
					if($attributes['cols'] > mb_strlen($option)) {
						$pad = str_repeat( '　', $attributes['cols'] - mb_strlen($option));
						$options[$key] = $option.$pad;
					}
				}
			}
			return parent::select($fieldName, $options, $selected, $attributes, $showEmpty);
		}

	}
/**
 * 日付タグを表示
 * 
 * @param	string $fieldName フィールド文字列
 * @param	string $dateFormat 日付フォーマット
 * @param	string $timeFormat 時間フォーマット
 * @param	mixed	$selected
 * @param	array	$attributes html属性
 * @param	array $showEmpty 空データの表示有無
 * @return string htmlタグ
 * @access public
 */
	function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $selected = null, $attributes = array(), $showEmpty = true) {

		if($this->freezed) {

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
			$defaults = array(
				'minYear' => null, 'maxYear' => null, 'separator' => '&nbsp;'
			);
			$attributes = array_merge($defaults, (array) $attributes);
			$minYear = $attributes['minYear'];
			$maxYear = $attributes['maxYear'];
			$separator = $attributes['separator'];
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
			$selects = array();
			if(preg_match('/^W/',$dateFormat)){
				$selects[] = $this->wyear($fieldName, $minYear, $maxYear, $year, $selectYearAttr, $showEmpty)."年";
			}else{
				$selectYearAttr['value'] = $year;
				$selects[] = $this->freezeControll($fieldName.".year", array(),$selectYearAttr)."年";
			}

			// TODO 値の出力はBcTextにまとめた方がよいかも
			// メール本文への出力も同じ処理を利用する。（改行の処理などはどうするか。。）
			$selectMonthAttr['value'] = $month;
			$selectDayAttr['value'] = $day;
			$selects[] = $this->freezeControll($fieldName.".month", array() ,$selectMonthAttr)."月";
			$selects[] = $this->freezeControll($fieldName.".day", array() ,$selectDayAttr)."日";
			return implode($separator, $selects);;

		}else {

			return parent::dateTime($fieldName, $dateFormat, $timeFormat, $selected, $attributes, $showEmpty);

		}

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
 * @access public
 */
	function wyear($fieldName, $minYear = null, $maxYear = null, $selected = null, $attributes = array(), $showEmpty = true) {

		if($this->freezed) {
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
				$w = $this->BcTime->wareki($wareki);
				$wyear = $this->BcTime->wyear($wareki);
				$selected = $w.'-'.$wyear;
				$freezeText = $this->BcTime->nengo($w) . ' ' . $wyear;
			} elseif ($selected === false) {
				$selected = null;
				$freezeText = '';
			} elseif(strpos($selected, '-')===false) {
				$wareki = $this->BcTime->convertToWareki($this->value($fieldName));
				if($wareki) {
					$w = $this->BcTime->wareki($wareki);
					$wyear = $this->BcTime->wyear($wareki);
					$selected = $w.'-'.$wyear;
					$freezeText = $this->BcTime->nengo($w) . ' ' . $wyear;
				} else {
					$selected = null;
					$freezeText = '';
				}
			}
			return $freezeText.$this->hidden($fieldName.".wareki", array('value'=>true)).$this->hidden($fieldName.".year", array('value'=>$selected));
		} else {
			return parent::wyear($fieldName, $minYear, $maxYear, $selected, $attributes, $showEmpty);
		}
	}
/**
 * チェックボックスを表示する
 * 
 * @param string $fieldName フィールド文字列
 * @param title $title タイトル
 * @param array $attributes html属性
 * @return string htmlタグ
 * @access public
 */
	function checkbox($fieldName, $attributes = array()) {
		
		if($this->freezed) {
			$label = '';
			if(isset($attributes['lable'])) {
				$label = $attributes['label'];
			}
			$options = array(0 => '', 1 => $label);
			return $this->freezeControll($fieldName, $options,$attributes);
		}else {
			return parent::checkbox($fieldName,$attributes);
		}
		
	}
/**
 * テキストエリアを表示する
 * 
 * @param string フィールド文字列
 * @param array html属性
 * @return string htmlタグ
 * @access public
 */
	function textarea($fieldName,$attributes = array()) {

		if($this->freezed) {
			list($model,$field) = explode('.',$fieldName);
			$attributes = $attributes + array('type'=>'hidden');
			if(isset($attributes["value"])) {
				$value = $attributes["value"];
			}else {
				$value = $this->data[$model][$field];
			}
			if($value) {
				return parent::text($fieldName, $attributes) . nl2br($value);
			}else {
				return "&nbsp;";
			}
		}else {
			return parent::textarea($fieldName, $attributes);
		}

	}
/**
 * ラジオボタンを表示する
 * 
 * @param string $fieldName フィールド文字列
 * @param array $options コントロールソース
 * @param array $attributes html属性
 * @return string htmlタグ
 * @access public
 */
	function radio($fieldName,$options, $attributes = array()) {

		if($this->freezed) {
			return $this->freezeControll($fieldName, $options,$attributes);
		}else {
			return parent::radio($fieldName,$options, $attributes);
		}

	}
/**
 * ファイルタグを出力
 * 
 * @param string $fieldName
 * @param array $options
 * @return string
 * @access public
 */
	function file($fieldName, $options = array()) {
		
		if($this->freezed) {
			$value = $this->value($fieldName);
			if(is_array($value) && isset($value['session_key'])) {
				$value = $value['session_key'];
				return parent::hidden($fieldName."_tmp",array('value'=>$value)).$this->BcUpload->fileLink($fieldName,$options);
			}else {
				if(!is_array($value)) {
					$delValue = $this->value($fieldName.'_delete');
					if($delValue) {
						return parent::hidden($fieldName,array('value'=>$value)).parent::hidden($fieldName.'_delete',array('value'=>true)).$this->BcUpload->fileLink($fieldName,$options).'<br />削除する';
					}else {
						return parent::hidden($fieldName,array('value'=>$value)).$this->BcUpload->fileLink($fieldName,$options);
					}
				}
			}

		}else {
			return $this->BcUpload->file($fieldName,$options);
		}
		
	}
/**
 * ファイルコントロール（画像）を表示する
 * TODO 確認画面には未チェック
 * 
 * @param string $fieldName フィールド文字列
 * @param	array $attributes html属性
 * @param array $attributes 画像属性
 * @return string htmlタグ
 * @access public
 */
	function image($fieldName, $attributes = array(), $imageAttributes = array()) {
		
		if(!$attributes)$attributes = array();
		$output = "";
		$imageAttributes = array_merge(array('ext' => 'jpg','alt' => '','dir' => '', 'id' => ''), $imageAttributes);
		if(!empty($imageAttributes['subdir']))$imageAttributes['subdir'].=DS;

		list($model,$field) = explode('.',$fieldName);

		if($this->freezed) {

			$attributes = array_merge($attributes,array('type' => 'hidden'));

			// 確認画面
			if(!empty($this->data[$model][$field]['name'])) {
				$path = "tmp".DS.Inflector::underscore($model).DS."img".DS.$field.$imageAttributes['ext']."?".rand();
				unset($imageAttributes['ext']);
				$output = parent::text($fieldName."_exists", $attributes);
				$output .= sprintf($this->Html->tags['image'], $path, $this->Html->_parseAttributes($imageAttributes));
				return $output;
				// 通常表示
			}else {
				if(!empty($this->data[$model][$field.'_exists'])) {
					$path = DS.$imageAttributes['dir'].DS.Inflector::tableize($model).DS.$imageAttributes['id'].DS.$field.".".$imageAttributes['ext']."?".rand();
					unset($imageAttributes['ext']);
					return sprintf($this->Html->tags['image'], $path, $this->Html->_parseAttributes($imageAttributes));
				}else {
					return "&nbsp;";
				}
			}
		}else {
			if(!empty($this->data[$model][$field.'_exists'])) {
				$path = DS.$imageAttributes['dir'].DS.Inflector::tableize($model).DS.$imageAttributes['id'].DS.$field.".".$imageAttributes['ext']."?".rand();
				unset($imageAttributes['ext']);
				$output = sprintf($this->Html->tags['image'], $path, $this->Html->_parseAttributes($imageAttributes));
				$output .= "<br />".$this->checkbox($fieldName."_delete", array('label' => '削除する'));
			}
			return parent::file($fieldName, $attributes)."<br />".$output;
		}

	}
/**
 * JsonList
 * TODO 確認画面用の実装は全くしてない
 * 
 * @param string $fieldName フィールド文字列
 * @param array $attributes html属性
 * @return string	htmlタグ
 * @access public
 */
	function jsonList($fieldName,$attributes) {

		if($this->freezed) {

			$out='';
			if(!empty($this->data[$fieldName])) {
				$out = '<div id="JsonTagView"><ul class="freezed">';
				foreach($this->data[$fieldName] as $tag) {
					$out.='<li>'.$tag['name'].'</li>';
				}
				$out.='</ul></div>';
			}

			return $out;
		}else {
			return parent::jsonList($fieldName,$attributes);
		}

	}
/**
 * カレンダーコントロール付きのテキストフィールド
 * jquery-ui-1.7.2 必須
 * 
 * @param 	string $fieldName フィールド文字列
 * @param array $attributes HTML属性
 * @return string html
 * @access public
 */
	function datepicker($fieldName, $attributes = array()) {

		if($this->freezed) {
			list($model,$field) = explode('.',$fieldName);
			if(isset($attributes)) {
				$attributes = am($attributes,array('type'=>'hidden'));
			}else {
				$attributes = array('type'=>'hidden');
			}
			if(!empty($this->data[$model][$field])) {
				$value = date('Y/m/d',strtotime($this->data[$model][$field]));
			}else {
				$value = "";
			}
			return parent::text($fieldName, $attributes) . $value;
		}else {
			return parent::datepicker($fieldName, $attributes);
		}

	}
/**
 * 凍結時用のコントロールを取得する
 * @param	string	フィールド文字列
 * @param	array	コントロールソース
 * @param	array	html属性
 * @return	string	htmlタグ
 * @access	public
 */
	function freezeControll($fieldName,$options,$attributes=array()) {

		$attributes = array_merge(array('class' => ''), $attributes);
		unset($attributes["separator"]);
		if(preg_match_all("/\./",$fieldName,$regs)==2) {
			list($model,$field,$detail) = explode('.',$fieldName);
		}else {
			list($model,$field) = explode('.',$fieldName);
		}

		// 値を取得
		if(isset($attributes["value"])) {
			$value = $attributes["value"];
		}else {
			// HABTAM
			if(!empty($attributes["multiple"]) && $attributes["multiple"] !== 'checkbox') {

				$value = $this->data[$model];
			}else {
				if(!empty($this->data[$model][$field])) {
					$value = $this->data[$model][$field];
				}else {
					$value = null;
				}
			}
		}

		// optionsによるソース有 「0」は通す
		if($options && $value!=='' && !is_null($value)) {

			// HABTAM
			if(!empty($attributes["multiple"]) && $attributes["multiple"] !== 'checkbox') {
				$_value = "";
				foreach($value as $data) {
					if(isset($options[$data['id']])) {
						$_value .= sprintf($this->Html->tags['li'], null, $options[$data['id']]);
					}
				}
				$value = sprintf($this->Html->tags['ul'], " ".$this->_parseAttributes($attributes, null, '', ' '),$_value);

				$out = parent::hidden($fieldName, $attributes) . $value;

				// マルチチェック
			}elseif(!empty($attributes["multiple"]) && $attributes["multiple"] === 'checkbox') {

				$_value = "";
				foreach($value as $key => $data) {
					if(isset($options[$data])) {
						$_value .= sprintf($this->Html->tags['li'], null, $options[$data]);
					}
				}

				$out = sprintf($this->Html->tags['ul'], " ".$this->_parseAttributes($attributes, null, '', ' '),$_value);
				$out .= $this->hidden($fieldName, array('value'=>$value,'multiple'=>true));
				
				// 一般
			}elseif(empty($detail)) {

				if(isset($options[$value])) {
					$value = $options[$value];
				}else {
					$value = '';
				}

				$out = parent::hidden($fieldName, $attributes) . $value;

				// datetime
			}else {
				if($value[$detail]) {
					$value = $options[$value[$detail]];
				}else {
					$value = "";
				}
				$out = parent::hidden($fieldName, $attributes) . $value;
			}

			// 値なし
		}else {

			if($options) {
				if(!$value && !empty($options[0])) {
					$value = $options[0];
				}else {
					$value = "";
				}
			}elseif(empty($detail)) {
				if(!empty($value)) {
					$value = $value;
				}else {
					$value = null;
				}
			}elseif(is_array ($value) && isset($value[$detail])) {
				$value = $value[$detail];
			}

			$out = parent::hidden($fieldName, $attributes) . $value;

		}

		return $out;

	}
	
}