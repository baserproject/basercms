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
		$controlSources['tables'] = $this->getListModels();
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
	function getListModels(){

		$db =& ConnectionManager::getDataSource('baser');
		$listSources = $db->listSources();
		if(!$listSources){
			return array();
		}
		$sources = array();
		foreach($listSources as $source) {
			$sources[] = $source;
		}
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
		if(!$data['tables']){
			return false;
		}
		$result = true;
		if(!$this->_writeSchema('tables', $data['tables'], $path)){
			$result = false;
			break;
		}
		return $result;

	}
/**
 * スキーマファイルを読み込む
 * 
 * @param array $data
 * @param string $tmpPath
 * @return boolean
 */
	function loadSchema($data, $tmpPath) {
		
		$path = $tmpPath . $data['Tool']['schema_file']['name'];
		if(move_uploaded_file($data['Tool']['schema_file']['tmp_name'], $path)) {
			App::import('Model','Schema');
			include $path;
			$schemaName = basename(Inflector::classify(basename($path)),'.php').'Schema';
			$Schema = new $schemaName();
			$db =& ConnectionManager::getDataSource($Schema->connection);
			if($db->loadSchema(array('type'=>$data['Tool']['schema_type'],'path' => $tmpPath, 'file'=> $data['Tool']['schema_file']['name']))) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}
		
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

		$db =& ConnectionManager::getDataSource('baser');
		$prefix = $db->config['prefix'];
		$tableList = $this->getControlSource($field);
		$modelList = array();
		foreach ($tableList as $key => $table) {
			if(preg_match('/^'.$prefix.'([a-z][a-z_]*?)$/is', $table,$maches)) {
				$model = Inflector::camelize(Inflector::singularize($maches[1]));
				$modelList[$key] = $model;
			}
		}

		$result = true;
		foreach ($values as $value){
			if(!$db->writeSchema(array('model' => $modelList[$value], 'path' => $path))){
				$result = false;
			}/*else{
				$filename = $path.Inflector::tableize($modelList[$value]).'.php';
				$File = new File($filename);
				$content = file_get_contents($filename);
				$content = str_replace($path, BASER_CONFIGS.'sql'.DS, $content);
				$File->write($content, 'w+');
				$File->close();
			}*/
		}
		return $result;
		
	}
	
}
?>