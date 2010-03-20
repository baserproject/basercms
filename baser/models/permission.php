<?php
/* SVN FILE: $Id$ */
/**
 * パーミッションモデル
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
 * @package			baser.models
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
 * パーミッションモデル
 *
 * @package			baser.models
 */
class Permission extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
   	var $name = 'Permission';
/**
 * データベース接続
 *
 * @var     string
 * @access  public
 */
    var $useDbConfig = 'baser';
/**
 * beforeValidate
 *
 * @return	boolean
 * @access	public
 */
	function beforeValidate(){

		$this->validate['name'] = array(array('rule' => VALID_NOT_EMPTY,
											'message' => ">> 設定名を入力して下さい"));
		return true;

	}
/**
 * afterFind
 * @param mixed $results
 * @param boolean $primary
 */
	function afterFind($results, $primary = false){
		if($results){
			if(isset($results[$this->alias])){
				$results[$key] = $this->construct($result);
			}else{
				foreach($results as $key => $result){
					$results[$key][$this->alias]['setting'] = $this->deconstructSetting($result);
				}
			}
		}
		return $results;
	}
/**
 * 設定フィールドを結合する
 * @param array $data
 * @return string
 */
	function deconstructSetting($data){
		if (!is_array($data)) {
			return $data;
		}
		if(isset($data[$this->alias])){
			$data = $data[$this->alias];
		}
		$setting = '';
		if($data['prefix']!='-'){
			$setting = '/'.$data['prefix'];
		}
		if($data['plugin']!='-'){
			$setting .= '/'.$data['plugin'];
		}
		if($data['controller']!='-'){
			$setting .= '/'.$data['controller'];
		}
		if($data['action']!='-'){
			$setting .= '/'.$data['action'];
		}
		if($data['pass1']!='-'){
			$setting .= '/'.$data['pass1'];
		}
		if($data['pass2']!='-'){
			$setting .= '/'.$data['pass2'];
		}
		return $setting;

	}
}
?>