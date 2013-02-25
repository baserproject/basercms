<?php
/* SVN FILE: $Id$ */
/**
 * テーマモデル
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models
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
 * テーマモデル
 *
 * @package baser.models
 */
class Theme extends AppModel {
/**
 * クラス名
 *
 * @var string
 * @access public
 */
	var $name = 'Theme';
/**
 * テーブル
 *
 * @var string
 * @access public
 */
	var $useTable = false;
/**
 * バリデーション
 *
 * @var array
 * @access public
 */
	var $validate = array(
		'name' => array(
			array(	'rule'		=> array('notEmpty'),
					'message'	=> 'テーマ名を入力してください。'),
			array(	'rule'		=> 'halfText',
					'message'	=> 'テーマ名は半角英数字のみで入力してください。'),
			array(	'rule'		=> 'duplicate',
					'message'	=> '既に存在するテーマ名です。')
		),
		'url' => array(
			array(	'rule'		=> 'halfText',
					'message'	=> 'URLは半角英数字のみで入力してください。'),
			array(	'rule'		=> 'url',
					'message'	=> 'URLの形式が間違っています。'),
		)
	);
/**
 * 重複チェック
 *
 * @param string
 * @return boolean
 * @access public
 */
	function duplicate($check) {
		
		$value = $check[key($check)];
		if(!$value){
			return true;
		}
		if($value == $this->data['Theme']['old_name']){
			return true;
		}
		if(!is_dir(WWW_ROOT.'themed'.DS.$value)){
			return true;
		}else {
			return false;
		}
		
	}
/**
 * 保存
 *
 * @param string
 * @return boolean
 * @access public
 */
	function save($data = null, $validate = true, $fieldList = array()) {

		if(!$data){
			$data = $this->data;
		}else{
			$this->set($data);
		}

		if($validate){
			if(!$this->validates()){
				return false;
			}
		}

		if(isset($data['Theme'])){
			$data = $data['Theme'];
		}

		$path = WWW_ROOT.'themed'.DS;
		if($path.$data['old_name'] != $path.$data['name']) {
			if(!rename($path.$data['old_name'], $path.$data['name'])){
				return false;
			}
		}

		$keys = array('title','description','author','url');
		foreach($keys as $key){
			if(isset($data[$key])){
				$this->setConfig($data['name'], $key, $data[$key]);
			}
		}

		return true;

	}
/**
 * テーマ設定ファイルに値を設定する
 *
 * @param string $key
 * @param string $value
 * @param string $contents
 * @return string
 * @access public
 */
	function setConfig($theme,$key,$value){

		$path = WWW_ROOT.'themed'.DS;
		$contents = file_get_contents($path.$theme.DS.'config.php');
		$reg = '/\$'.$key.'[\s]*?=[\s]*?\'.*?\';/is';
		if(preg_match($reg, $contents)){
			$contents = preg_replace($reg, '$'.$key.' = \''.$value.'\';', $contents);
		}else{
			$contents = str_replace("?>", "\$".$key." = '".$value."';\n?>", $contents);
		}
		$file = new File($path.$theme.DS.'config.php');
		$file->write($contents, 'w');
		$file->close();

	}
}
