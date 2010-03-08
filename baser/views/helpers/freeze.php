<?php
/* SVN FILE: $Id$ */
/**
 * FreezeHelper
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
App::import('Helper', 'FormEx', 'Upload');
/**
 * @package			baser.view.helpers
 */
class FreezeHelper extends FormExHelper {
/**
 * 凍結状態
 * @var 	boolean
 * @access	public
 */
	var $freezed = false;
/**
 * ヘルパー
 * @var     array
 * @access  public
 */
    var $helpers = array('Html','FormEx','Upload','TextEx','TimeEx','Javascript');
/**
 * フォームを凍結させる
 * @return	void
 * @access	public
 */
	function freeze(){
		$this->freezed = true;
	}
/**
 * テキストボックスを表示する
 * @param	string	フィールド文字列
 * @param	array	html属性
 * @return	string	htmlタグ
 * @access	public
 */
	function text($fieldName,$attributes = array()){
		
		if($this->freezed){
			list($model,$field) = explode('.',$fieldName);
			if(isset($attributes)){
				$attributes = $attributes + array('type'=>'hidden');
			}else{
				$attributes = array('type'=>'hidden');
			}
			if(isset($attributes["value"])){
				$value = $attributes["value"];
			}else{
				$value = $this->data[$model][$field];
			}
			return parent::text($fieldName, $attributes) . $value;
		}else{
			return parent::text($fieldName, $attributes);
		}
		
	}
/**
 * select プルダウンメニューを表示
 * @param	string	フィールド文字列
 * @param	array	コントロールソース
 * @param	mixed	$selected
 * @param	array	html属性
 * @param	array	空データの表示有無
 * @return	string	htmlタグ
 * @access	public
 */
	function select($fieldName, $options, $selected = null, $attributes = array(), $showEmpty = true){

		if($this->freezed){
			return $this->freezeControll($fieldName, $options, $attributes);
		}else{
			// 横幅を設定する
			// 指定した文字数より足りない文字数分スペースを埋める処理としている為、
			// 等幅フォントを設定しないとちゃんとした横幅にはならない
			if(!empty($attributes['cols'])){
				foreach($options as $key => $option){
					if($attributes['cols'] > mb_strlen($option)){
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
 * @param	string	フィールド文字列
 * @param	string	日付フォーマット
 * @param	string	時間フォーマット
 * @param	mixed	$selected
 * @param	array	html属性
 * @param	array	空データの表示有無
 * @return	string	htmlタグ
 * @access	public
 */
	function dateTime($fieldName, $dateFormat = 'DMY', $timeFormat = '12', $selected = null, $attributes = array(), $showEmpty = true){

		if($this->freezed){

			$nengo = $wyear = $year = $month = $day = null;
			$nengoOptions = $yearOptions = $monthOptions = $dayOptions = array();
			$sepaletor = " ";

            // TODO 和暦がうまく動作しない
			/*if($dateFormat == 'WYMD'){
				
				list($model,$field) = explode('.',$fieldName);
				
				// 西暦年がない場合は取得
				if(empty($this->data[$model][$field]['year'])){
					$seireki = $this->TimeEx->convertSeireki($this->data[$model][$field]);
					if($seireki)
						$this->data[$model][$field]['year'] = date('Y',strtotime($seireki));
				}

				if(!$this->data[$model][$field]['nengo']||
						!$this->data[$model][$field]['wyear']||
						!$this->data[$model][$field]['year']||
						!$this->data[$model][$field]['month']||
						!$this->data[$model][$field]['day']){
					$this->data[$model][$field]['nengo']="";
					$this->data[$model][$field]['wyear']="";
					$this->data[$model][$field]['year']="";
					$this->data[$model][$field]['month']="";
					$this->data[$model][$field]['day']="";
				}
				$sepaletor = " ";
				$nengoOptions = $this->__generateOptions("nengo");
				$nengo = $this->freezeControll($fieldName.".nengo", $nengoOptions,$attributes).$sepaletor;
				$wyear = $this->freezeControll($fieldName.".wyear", $yearOptions,$attributes)."年".$sepaletor;
				$year = $this->hidden($fieldName.".year", $yearOptions,$attributes);
			}else{*/
				$year = $this->freezeControll($fieldName.".year", $yearOptions,$attributes)."年".$sepaletor;
			//}
			
			// TODO 値の出力はTextExにまとめた方がよいかも
			// メール本文への出力も同じ処理を利用する。（改行の処理などはどうするか。。）
			$month = $this->freezeControll($fieldName.".month", $monthOptions ,$attributes)."月".$sepaletor;
			$day = $this->freezeControll($fieldName.".day", $dayOptions ,$attributes)."日";
			return $nengo.$wyear.$year.$month.$day;
			
		}else{
			
			return parent::dateTime($fieldName, $dateFormat, $timeFormat, $selected, $attributes, $showEmpty);
		
		}

	}
/**
 * チェックボックスを表示する
 * @param	string	フィールド文字列
 * @param	title	タイトル
 * @param	array	html属性
 * @return	string	htmlタグ
 * @access	public
 */
	function checkbox($fieldName,$title,$attributes = array()){
		
		if($this->freezed){
			$options = array(0=>'',1=>$title);
			return $this->freezeControll($fieldName, $options,$attributes);
		}else{
			return parent::checkbox($fieldName,$attributes).parent::label($fieldName, $title);
		}
	}
/**
 * テキストエリアを表示する
 * @param	string	フィールド文字列
 * @param	array	html属性
 * @return	string	htmlタグ
 * @access	public
 */
	function textarea($fieldName,$attributes = array()){

		if($this->freezed){
			list($model,$field) = explode('.',$fieldName);
			$attributes = $attributes + array('type'=>'hidden');
			if(isset($attributes["value"])){
				$value = $attributes["value"];
			}else{
				$value = $this->data[$model][$field];
			}
			if($value){
				return parent::text($fieldName, $attributes) . nl2br($value);
			}else{
				return "&nbsp;";
			}
		}else{
			return parent::textarea($fieldName, $attributes);
		}
		
	}
/**
 * ラジオボタンを表示する
 * @param	string	フィールド文字列
 * @param	array	コントロールソース
 * @param	array	html属性
 * @return	string	htmlタグ
 * @access	public
 */
	function radio($fieldName,$options, $attributes = array()){
		
		if($this->freezed){
			return $this->freezeControll($fieldName, $options,$attributes);
		}else{
			return parent::radio($fieldName,$options, $attributes);
		}
		
	}
/**
 * ファイルタグを出力
 * @param string $fieldName
 * @param array $options
 * @return string
 */
    function file($fieldName, $options = array()){
        if($this->freezed){
            $value = $this->value($fieldName);
            if(is_array($value) && isset($value['session_key'])){
                $value = $value['session_key'];
                return parent::hidden($fieldName."_tmp",array('value'=>$value)).$this->Upload->fileLink($fieldName,$options);
            }else{
				if(!is_array($value)){
					$delValue = $this->value($fieldName.'_delete');
					if($delValue){
						return parent::hidden($fieldName,array('value'=>$value)).parent::hidden($fieldName.'_delete',array('value'=>true)).$this->Upload->fileLink($fieldName,$options).'<br />削除する';
					}else{
						return parent::hidden($fieldName,array('value'=>$value)).$this->Upload->fileLink($fieldName,$options);
					}
				}
            }
            
        }else{
            return $this->Upload->file($fieldName,$options);
        }
    }
/**
 * ファイルコントロール（画像）を表示する
 * TODO 確認画面には未チェック
 * @param	string	フィールド文字列
 * @param	array	html属性
 * @param	array	画像属性
 * @return	string	htmlタグ
 * @access	public
 */
	function image($fieldName, $attributes = array(), $imageAttributes = array()){
		if(!$attributes)$attributes = array();
		$output = "";
		$imageAttributes = array_merge(array('ext' => 'jpg','alt' => '','dir' => '', 'id' => ''), $imageAttributes);
		if(!empty($imageAttributes['subdir']))$imageAttributes['subdir'].=DS;
		
		list($model,$field) = explode('.',$fieldName);
			
		if($this->freezed){

			$attributes = array_merge($attributes,array('type' => 'hidden'));
			
			// 確認画面
			if(!empty($this->data[$model][$field]['name'])){
				$path = "tmp".DS.Inflector::underscore($model).DS."img".DS.$field.$imageAttributes['ext']."?".rand();
				unset($imageAttributes['ext']);
				$output = parent::text($fieldName."_exists", $attributes);
				$output .= sprintf($this->Html->tags['image'], $path, $this->Html->_parseAttributes($imageAttributes));
				return $output;
			// 通常表示
			}else{
				if(!empty($this->data[$model][$field.'_exists'])){
					$path = DS.$imageAttributes['dir'].DS.Inflector::tableize($model).DS.$imageAttributes['id'].DS.$field.".".$imageAttributes['ext']."?".rand();
					unset($imageAttributes['ext']);
					return sprintf($this->Html->tags['image'], $path, $this->Html->_parseAttributes($imageAttributes));
				}else{
					return "&nbsp;";
				}
			}
		}else{
			if(!empty($this->data[$model][$field.'_exists'])){
				$path = DS.$imageAttributes['dir'].DS.Inflector::tableize($model).DS.$imageAttributes['id'].DS.$field.".".$imageAttributes['ext']."?".rand();
				unset($imageAttributes['ext']);
				$output = sprintf($this->Html->tags['image'], $path, $this->Html->_parseAttributes($imageAttributes));
				$output .= "<br />".$this->checkbox($fieldName."_delete","削除する");
			}
			return parent::file($fieldName, $attributes)."<br />".$output;
		}
		
	}
/**
 * JsonList
 * TODO 確認画面用の実装は全くしてない
 * @param	string	フィールド文字列
 * @param	array	html属性
 * @return	string	htmlタグ
 * @access	public
 */
	function jsonList($fieldName,$attributes){

		if($this->freezed){

			$out='';
			if(!empty($this->data[$fieldName])){
				$out = '<div id="JsonTagView"><ul class="freezed">';
				foreach($this->data[$fieldName] as $tag){
					$out.='<li>'.$tag['name'].'</li>';
				}
				$out.='</ul></div>';
			}
			
			return $out;
		}else{
			return parent::jsonList($fieldName,$attributes);
		}
		
	}
/**
 * カレンダーコントロール付きのテキストフィールド
 * jquery-ui-1.7.2 必須
 * @param 	string	フィールド文字列
 * @param	array	HTML属性
 * @return	string	html
 * @access 	public
 */
	function datepicker($fieldName, $attributes = array()){

		if($this->freezed){
			list($model,$field) = explode('.',$fieldName);
			if(isset($attributes)){
				$attributes = am($attributes,array('type'=>'hidden'));
			}else{
				$attributes = array('type'=>'hidden');
			}
			if(!empty($this->data[$model][$field])){
				$value = date('Y/m/d',strtotime($this->data[$model][$field]));
			}else{
				$value = "";
			}
			return parent::text($fieldName, $attributes) . $value;
		}else{
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
	function freezeControll($fieldName,$options,$attributes=array()){

		$attributes = array_merge(array('class' => ''), $attributes);

		if(preg_match_all("/\./",$fieldName,$regs)==2){
			list($model,$field,$detail) = explode('.',$fieldName);
		}else{
			list($model,$field) = explode('.',$fieldName);
		}
		
		// 値を取得
		if(isset($attributes["value"])){
			$value = $attributes["value"];
		}else{
			// HABTAM
			if(!empty($attributes["multiple"]) && $attributes["multiple"] !== 'checkbox'){

				$value = $this->data[$model];
			}else{
                if(!empty($this->data[$model][$field])){
                    $value = $this->data[$model][$field];
                }else{
                    $value = null;
                }
			}
		}

		// optionsによるソース有 「0」は通す
		if($options && $value!=='' && !is_null($value)){
			
			// HABTAM
			if(!empty($attributes["multiple"]) && $attributes["multiple"] !== 'checkbox'){
				$_value = "";
				foreach($value as $data){
					if(isset($options[$data['id']])){
						$_value .= sprintf($this->Html->tags['li'], null, $options[$data['id']]);
					}
				}
				$value = sprintf($this->Html->tags['ul'], " ".$this->_parseAttributes($attributes, null, '', ' '),$_value);
				
				$out = parent::hidden($fieldName, $attributes) . $value;
				
			// マルチチェック
			}elseif(!empty($attributes["multiple"]) && $attributes["multiple"] === 'checkbox'){
			
				$_value = "";
				foreach($value as $key => $data){
					if(isset($options[$data])){
						$_value .= sprintf($this->tags['hiddenmultiple'], $model, $field, null, $data).sprintf($this->Html->tags['li'], null, $options[$data]);
					}
				}
				$out = sprintf($this->Html->tags['ul'], " ".$this->_parseAttributes($attributes, null, '', ' '),$_value);
				
			// 一般
			}elseif(empty($detail)){

				if(isset($options[$value])){
					$value = $options[$value];
				}else{
					$value = '';
				}

				$out = parent::hidden($fieldName, $attributes) . $value;
				
			// datetime
			}else{
				if($value[$detail]){
					$value = $options[$value[$detail]];
				}else{
					$value = "";
				}
				$out = parent::hidden($fieldName, $attributes) . $value;
			}
	
		// 値なし
		}else{
			if($options){
				if(!$value && !empty($options[0])){
					$value = $options[0];
				}else{
					$value = "";
				}
			}elseif(empty($detail)){
				if(!empty($value)){
					$value = $value;
				}else{
					$value = null;
				}
			}else{
				$value = $value[$detail];
			}
			$out = parent::hidden($fieldName, $attributes) . $value;
		}

		return $out;
		
	}
	
}
?>