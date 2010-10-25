<?php
/* SVN FILE: $Id$ */
/**
 * ツールモデル
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
 * ツールモデル
 * @package			baser.models
 */
class Tool extends AppModel {
/**
 * クラス名
 *
 * @var		string
 * @access 	public
 */
	var $name = 'Tool';
/**
 * テーブル
 * 
 * @var		string
 * @access	public
 */
	var $useTable = false;
/**
 * コントロールソースを取得する
 *
 * @param	string	フィールド名
 * @return	array	コントロールソース
 * @access	public
 */
	function getControlSource ($field) {

		// スキーマ用モデルリスト
		$controlSources['baser_models'] = $this->getListModels('baser');
		$controlSources['plugin_models'] = $this->getListModels('plugin');

		if(isset($controlSources[$field])) {
			return $controlSources[$field];
		}else {
			return false;
		}
		
	}
/**
 * データソースを指定してモデルリストを取得する
 * @param string $ds
 * @return array
 */
	function getListModels($ds){
		
		$db =& ConnectionManager::getDataSource($ds);
		$prefix = $db->config['prefix'];
		$listSources = $db->listSources();
		if(!$listSources){
			return array();
		}
		$sources = array();
		foreach($listSources as $source) {
			if(preg_match('/^'.$prefix.'([a-z][a-z_]*?)$/is', $source,$maches)) {
				$model = Inflector::camelize(Inflector::singularize(str_replace('.php', '', $maches[1])));
				$sources[] = $model;
			}
		}
		sort($sources);
		return $sources;
		
	}
/**
 * スキーマを書き出す
 * 
 * @param	string	$path
 * @param	array	$data
 * @return	boolean
 * @access	public
 */
	function writeSchema($data, $path){
		
		if(isset($data['Tool'])){
			$data = $data['Tool'];
		}
		if(!$data['baser_models'] && !$data['plugin_models']){
			return false;
		}
		$result = true;
		$types = array('baser_models','plugin_models');
		foreach($types as $type) {
			if($data[$type]){
				if(!$this->_writeSchema($type, $data[$type], $path)){
					$result = false;
					break;
				}
			}
		}
		return $result;

	}
/**
 * スキーマを書き出す
 *
 * @param	string	$field
 * @param	array	$values
 * @param	string	$path
 * @return	boolean
 * @access	protected
 */
	function _writeSchema($field, $values, $path) {

		$modelList = $this->getControlSource($field);
		$ds = str_replace('_models', '', $field);
		$db =& ConnectionManager::getDataSource($ds);
		$result = true;
		foreach ($values as $value){
			if(!$db->writeSchema(array('model' => $modelList[$value], 'path' => $path))){
				$result = false;
			}else{
				$filename = $path.Inflector::tableize($modelList[$value]).'.php';
				$File = new File($filename);
				$content = file_get_contents($filename);
				$content = str_replace($path, BASER_CONFIGS.'sql'.DS, $content);
				$File->write($content, 'w+');
				$File->close();
			}
		}
		return $result;
		
	}
	
}
?>