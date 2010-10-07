<?php
/* SVN FILE: $Id$ */
/**
 * SQLite3 DBO拡張
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
 * @package			baser.models.datasources.dbo
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::import('Core','DboSqlite3',array('file'=>BASER_MODELS.'datasources'.DS.'dbo'.DS.'dbo_sqlite3.php'));
/**
 * SQLite3 DBO拡張
 *
 * @package			baser.models.datasources.dbo
 */
class DboSqlite3Ex extends DboSqlite3 {
/**
 * カラムを追加するSQLを生成
 *
 * @param string $tableName
 * @param array $column
 * @return string
 * @access public
 */
	function buildAddColumn($tableName, $column) {
		if($column['type'] == 'integer' && !empty($column['length'])){
			unset($column['length']);
		}
		return "ALTER TABLE ".$tableName." ADD ".$this->buildColumn($column);
	}
/**
 * カラムを変更するSQLを生成
 * 未サポート
 * @param string $oldFieldName
 * @param string $newFieldName
 * @param array $column
 * @return string
 * @access public
 */
	function buildEditColumn($tableName, $oldFieldName, $column) {
		return '';
	}
/**
 * カラムを削除する
 * 未サポート
 * @param string $delFieldName
 * @param array $column
 * @return string
 * @access public
 */
	function buildDelColumn($tableName, $delFieldName) {
		return '';
	}
/**
 * テーブル名のリネームステートメントを生成
 *
 * @param	string	$sourceName
 * @param	string	$targetName
 * @return	string
 * @access	public
 */
	function buildRenameTable($sourceName, $targetName) {
		return "ALTER TABLE ".$sourceName." RENAME TO ".$targetName;
	}
/**
 * カラムを変更する
 * @param model $model
 * @param string $oldFieldName
 * @param string $newFieldName
 * @param array $column
 * @return boolean
 * @access public
 */
	function renameColumn(&$model, $oldFieldName, $newfieldName) {

		$db =& ConnectionManager::getDataSource($model->useDbConfig);
		$schema = $db->describe($model, $oldFieldName);
		$tableName = $this->fullTableName($model, false);

		$this->execute('BEGIN TRANSACTION;');

		// リネームして一時テーブル作成
		if(!$this->renameTable($tableName, $tableName.'_temp')) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// スキーマのキーを変更（並び順を変えないように）
		$newSchema = array();
		foreach($schema as $key => $field) {
			if($key == $oldFieldName) {
				$key = $newfieldName;
			}
			$newSchema[$key] = $field;
		}

		// フィールドを変更した新しいテーブルを作成
		if(!$this->createTable($tableName, $newSchema)) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// データの移動
		$sql = 'INSERT INTO '.$tableName.' SELECT '.$this->_convertCsvFieldsFromSchema($schema).' FROM '.$tableName.'_temp';
		$sql = str_replace($oldFieldName,$oldFieldName.' AS '.$newfieldName,$sql);
		if(!$this->execute($sql)) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// 一時テーブルを削除
		if(!$this->dropTable($tableName.'_temp')) {
			$this->execute('ROLLBACK;');
			return false;
		}

		$this->execute('COMMIT;');
		return true;

	}
/**
 * カラムを削除する
 * 
 * @param model $model
 * @param string $delFieldName
 * @param array $column
 * @return boolean
 * @access public
 */
	function delColumn(&$model,$delFieldName) {

		$tableName = $this->fullTableName($model, false);
		$db =& ConnectionManager::getDataSource($model->useDbConfig);
		$schema = $db->describe($model, $delFieldName);

		$this->execute('BEGIN TRANSACTION;');

		// リネームして一時テーブル作成
		if(!$this->renameTable($tableName,$tableName.'_temp')) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// フィールドを削除した新しいテーブルを作成
		unset($schema[$delFieldName]);
		if(!$this->createTable($tableName,$schema)) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// データの移動
		if(!$this->_moveData($tableName.'_temp',$tableName,$schema)) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// 一時テーブルを削除
		if(!$this->dropTable($tableName.'_temp')) {
			$this->execute('ROLLBACK;');
			return false;
		}

		$this->execute('COMMIT;');
		return true;

	}
/**
 * テーブルからテーブルへデータを移動する
 * @param	string	$sourceTableName
 * @param	string	$targetTableName
 * @param	array	$schema
 * @return	booelan
 * @access	protected
 */
	function _moveData($sourceTableName,$targetTableName,$schema) {
		$sql = 'INSERT INTO '.$targetTableName.' SELECT '.$this->_convertCsvFieldsFromSchema($schema).' FROM '.$sourceTableName;
		return $this->execute($sql);
	}
/**
 * スキーマ情報よりCSV形式のフィールドリストを取得する
 * @param	array	$schema
 * @return	string
 * @access	protected
 */
	function _convertCsvFieldsFromSchema($schema) {
		$fields = '';
		foreach($schema as $key => $field) {
			$fields .= "'".$key."',";
		}
		return substr($fields,0,strlen($fields)-1);
	}
}
?>