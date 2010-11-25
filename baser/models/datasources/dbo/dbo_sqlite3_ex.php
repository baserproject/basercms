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
 * システムテーブル
 * 
 * @var		array
 * @access	public
 */
var $systemTables = array('sqlite_sequence');
/**
 * Generate a MySQL Alter Table syntax for the given Schema comparison
 *
 * @param array $compare Result of a CakeSchema::compare()
 * @return array Array of alter statements to make.
 */
	function alterSchema($compare, $table = null) {
		if (!is_array($compare)) {
			return false;
		}
		$out = '';
		$colList = array();
		foreach ($compare as $curTable => $types) {
			$indexes = array();
			if (!$table || $table == $curTable) {
				$out .= 'ALTER TABLE ' . $this->fullTableName($curTable) . " \n";
				foreach ($types as $type => $column) {
					if (isset($column['indexes'])) {
						$indexes[$type] = $column['indexes'];
						unset($column['indexes']);
					}
					switch ($type) {
						case 'add':
							foreach ($column as $field => $col) {
								$col['name'] = $field;
								$alter = 'ADD '.$this->buildColumn($col);
								if (isset($col['after'])) {
									$alter .= ' AFTER '. $this->name($col['after']);
								}
								$colList[] = $alter;
							}
						break;
						case 'drop':
							foreach ($column as $field => $col) {
								$col['name'] = $field;
								$colList[] = 'DROP '.$this->name($field);
							}
						break;
						case 'change':
							foreach ($column as $field => $col) {
								if (!isset($col['name'])) {
									$col['name'] = $field;
								}
								$colList[] = 'CHANGE '. $this->name($field).' '.$this->buildColumn($col);
							}
						break;
					}
				}
				$colList = array_merge($colList, $this->_alterIndexes($curTable, $indexes));
				$out .= "\t" . implode(",\n\t", $colList) . ";\n\n";
			}
		}
		return $out;
	}
/**
 * Overrides DboSource::index to handle SQLite indexe introspection
 * Returns an array of the indexes in given table name.
 *
 * @param string $model Name of model to inspect
 * @return array Fields in table. Keys are column and unique
 */
	function index(&$model) {
		$index = array();
		$table = $this->fullTableName($model, false);
		if ($table) {

			$tableInfo = $this->query('PRAGMA table_info(' . $table . ')');
			$primary = array();
			foreach($tableInfo as $info) {
				if(!empty($info[0]['pk'])){
					$primary = array('PRIMARY' => array('unique' => true, 'column' => $info[0]['name']));
				}
			}

			$indexes = $this->query('PRAGMA index_list(' . $table . ')');
			foreach ($indexes as $i => $info) {
				$key = array_pop($info);
				$keyInfo = $this->query('PRAGMA index_info("' . $key['name'] . '")');
				foreach ($keyInfo as $keyCol) {
					if (!isset($index[$key['name']])) {
						$col = array();
						$index[$key['name']]['column'] = $keyCol[0]['name'];
						$index[$key['name']]['unique'] = intval($key['unique'] == 1);
					} else {
						if (!is_array($index[$key['name']]['column'])) {
							$col[] = $index[$key['name']]['column'];
						}
						$col[] = $keyCol[0]['name'];
						$index[$key['name']]['column'] = $col;
					}
				}
			}
			$index = am($primary, $index);
		}
		return $index;
	}
/**
 * Generate index alteration statements for a table.
 * TODO 未サポート
 * @param string $table Table to alter indexes for
 * @param array $new Indexes to add and drop
 * @return array Index alteration statements
 */
	function _alterIndexes($table, $indexes) {
		return array();
	}
/**
 * テーブル構造を変更する
 *
 * @param	array	$options [ new / old ]
 * @return	boolean
 * @access	public
 */
	function alterTable($options) {

		extract($options);

		if(!isset($old) || !isset($new)){
			return false;
		}

		$Schema = ClassRegistry::init('CakeSchema');
		$Schema->connection = $this->configKeyName;
		$compare = $Schema->compare($old, $new);

		if(!$compare) {
			return false;
		}

		foreach($compare as $table => $types) {
			if(!$types){
				return false;
			}
			foreach($types as $type => $fields) {
				if(!$fields){
					return false;
				}
				foreach($fields as $fieldName => $column) {
					switch ($type) {
						case 'add':
							if(!$this->addColumn(array('field'=>$fieldName,'table'=>$table, 'column'=>$column))){
								return false;
							}
							break;
						case 'change':
							// TODO 未実装
							// SQLiteでは、SQLで実装できない？ので、フィールドの作り直しとなる可能性が高い
							// その場合、changeColumnメソッドをオーバライドして実装する
							return false;
							/*if(!$this->changeColumn(array('field'=>$fieldName,'table'=>$table, 'column'=>$column))){
								return false;
							}*/
							break;
						case 'drop':
							if(!$this->dropColumn(array('field'=>$fieldName,'table'=>$table))){
								return false;
							}
							break;
					}
				}
			}
		}

		return true;

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
 * 
 * @param	array	$options [ table / new / old ]
 * @return boolean
 * @access public
 */
	function renameColumn($options) {

		extract($options);

		if(!isset($table) || !isset($new) || !isset($old)) {
			return false;
		}

		$prefix = $this->config['prefix'];
		$_table = $table;
		$model = Inflector::classify(Inflector::singularize($table));
		$table = $prefix . $table;

		App::import('Model','Schema');
		$Schema = ClassRegistry::init('CakeSchema');
		$Schema->connection = $this->configKeyName;
		$schema = $Schema->read(array('models'=>array($model)));
		$schema = $schema['tables'][$_table];

		$this->execute('BEGIN TRANSACTION;');

		// リネームして一時テーブル作成
		if(!$this->renameTable(array('old'=>$_table, 'new'=>$_table.'_temp'))) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// スキーマのキーを変更（並び順を変えないように）
		$newSchema = array();
		foreach($schema as $key => $field) {
			if($key == $old) {
				$key = $new;
			}
			$newSchema[$key] = $field;
		}

		// フィールドを変更した新しいテーブルを作成
		if(!$this->createTable(array('schema'=>$newSchema, 'table'=>$_table))) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// データの移動
		unset($schema['indexes']);
		$sql = 'INSERT INTO '.$table.' SELECT '.$this->_convertCsvFieldsFromSchema($schema).' FROM '.$table.'_temp';
		$sql = str_replace($old,$old.' AS '.$new, $sql);
		if(!$this->execute($sql)) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// 一時テーブルを削除
		// dropTableメソッドはモデルありきなので利用できない
		if(!$this->execute('DROP TABLE '.$table.'_temp')) {
			$this->execute('ROLLBACK;');
			return false;
		}

		$this->execute('COMMIT;');
		return true;

	}
/**
 * カラムを削除する
 * 
 * @param	array	$options [ table / field / prefix ]
 * @return boolean
 * @access public
 */
	function dropColumn($options) {

		extract($options);

		if(!isset($table) || !isset($field)) {
			return false;
		}

		if(!isset($prefix)){
			$prefix = $this->config['prefix'];
		}
		$_table = $table;
		$model = Inflector::classify(Inflector::singularize($table));
		$table = $prefix . $table;

		App::import('Model','Schema');
		$Schema = ClassRegistry::init('CakeSchema');
		$Schema->connection = $this->configKeyName;
		$schema = $Schema->read(array('models'=>array($model)));
		$schema = $schema['tables'][$_table];

		$this->execute('BEGIN TRANSACTION;');

		// リネームして一時テーブル作成
		if(!$this->renameTable(array('old'=>$_table, 'new'=>$_table.'_temp'))) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// フィールドを削除した新しいテーブルを作成
		unset($schema[$field]);
		if(!$this->createTable(array('schema'=>$schema, 'table'=>$_table))) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// データの移動
		unset($schema['indexes']);
		if(!$this->_moveData($table.'_temp',$table,$schema)) {
			$this->execute('ROLLBACK;');
			return false;
		}

		// 一時テーブルを削除
		// dropTableメソッドはモデルありきなので利用できない
		if(!$this->execute('DROP TABLE '.$table.'_temp')) {
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