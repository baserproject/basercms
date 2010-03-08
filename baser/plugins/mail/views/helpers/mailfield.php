<?php
/* SVN FILE: $Id$ */
/**
 * メールフィールドヘルパー
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
/**
 * メールフィールドヘルパー
 *
 * @package			baser.plugins.mail.views.helpers
 *
 */
class MailfieldHelper extends AppHelper {
/**
 * htmlの属性を取得する
 *
 * @param	array	メールフィールド
 * @return	array	html属性
 * @access	public
 */
	function getAttributes($data){

        if(isset($data['MailField'])){
            $data = $data['MailField'];
        }
        
		$attributes['size']=$data['size'];
		$attributes['rows']=$data['rows'];
		$attributes['maxlength']=$data['maxlength'];
		$attributes['separator']=$data['separator'];
		$attributes['class']=$data['class'];
		
		if(!empty($data['options'])){
			
			$options = explode("|",$data['options']);
			$options = call_user_func_array('aa', $options);
			$attributes = am($attributes,$options);

		}	
		return $attributes;
		
	}
/**
 * コントロールのソースを取得する
 *
 * @param	array	メールフィールド
 * @return	array	コントロールソース
 * @access	public
 */
	function getOptions($data){

        if(isset($data['MailField'])){
            $data = $data['MailField'];
        }

		$attributes = $this->getAttributes($data);
		
		// コントロールソースを変換
		if(!empty($data['source'])){
			
			if($data['type']!="check"){
				$values = split("\|",$data['source']);
				$i = 0;
				foreach($values as $value){	
					$i++;
					$source[$i] = $value;
				}
				
				return $source;
				
			}
					
		}
	
	}
	
}

?>