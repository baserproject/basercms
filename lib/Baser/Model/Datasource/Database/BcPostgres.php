<?php
/* SVN FILE: $Id$ */
/**
 * PostgreSQL DBO拡張
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2012, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models.datasources.dbo
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */

App::uses('Postgres', 'Model/Datasource/Database');
class BcPostgres extends Postgres {
/**
 * テーブル名のリネームステートメントを生成
 *
 * @param string $sourceName
 * @param string $targetName
 * @return string
 * @access public
 */
	public function buildRenameTable($sourceName, $targetName) {
		
		return "ALTER TABLE ".$sourceName." RENAME TO ".$targetName;
		
	}
/**
 * カラム名を変更する
 *
 * @param array $options [ table / new / old  ]
 * @return boolean
 * @access public
 */
	public function renameColumn($options) {

		extract($options);

		if(!isset($table) || !isset($new) || !isset($old)) {
			return false;
		}

		$table = $this->config['prefix'] . $table;
		$sql = 'ALTER TABLE "'.$table.'" RENAME "'.$old.'" TO "'.$new.'"';
		return $this->execute($sql);
		
	}
/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @param string $column The column into which this data will be inserted
 * @param boolean $read Value to be used in READ or WRITE context
 * @return string Quoted and escaped
 * @todo Add logic that formats/escapes data based on column type
 * @access public
 */
	public function value($data, $column = null, $read = true) {

		// >>> CUSTOMIZE MODIFY 2011/03/23 ryuring
		//$parent = parent::value($data, $column);
		// ---
		$parent = $this->__value($data, $column);
		// <<<
		if ($parent != null) {
			return $parent;
		}

		if ($data === null) {
			return 'NULL';
		}
		if (empty($column)) {
			$column = $this->introspectType($data);
		}

		switch($column) {
			case 'binary':
				$data = pg_escape_bytea($data);
			break;
			case 'boolean':
				if ($data === true || $data === 't' || $data === 'true') {
					return 'TRUE';
				} elseif ($data === false || $data === 'f' || $data === 'false') {
					return 'FALSE';
				}
				return (!empty($data) ? 'TRUE' : 'FALSE');
			break;
			case 'float':
				if (is_float($data)) {
					$data = sprintf('%F', $data);
				}
			case 'inet':
			case 'integer':
				if ($data === '') {
					return $read ? 'NULL' : 'DEFAULT';
				}
				if (!$read && $data == '') {
					return 'NULL';
				}
			case 'date':
			case 'datetime':
			case 'timestamp':
			case 'time':
				// >>> CUSTOMIZE ADD 2010/03/23 ryuring
				// postgresql の場合、0000-00-00 00:00:00 を指定すると範囲外エラーとなる為
				if ($data === '0000-00-00 00:00:00') {
					return "'".date('Y-m-d H:i:s', 0)."'";
				}
				// <<<
				if ($data === '') {
					return $read ? 'NULL' : 'DEFAULT';
				}
			default:
				$data = pg_escape_string($data);
			break;
		}
		return "'" . $data . "'";
		
	}
/**
 * Prepares a value, or an array of values for database queries by quoting and escaping them.
 *
 * @param mixed $data A value or an array of values to prepare.
 * @param string $column The column into which this data will be inserted
 * @param boolean $read Value to be used in READ or WRITE context
 * @return mixed Prepared value or array of values.
 * @access private
 */
	private function __value($data, $column = null, $read = true) {
		
		if (is_array($data) && !empty($data)) {
			return array_map(
				array(&$this, 'value'),
				$data, array_fill(0, count($data), $column), array_fill(0, count($data), $read)
			);
		} elseif (is_object($data) && isset($data->type)) {
			if ($data->type == 'identifier') {
				return $this->name($data->value);
			} elseif ($data->type == 'expression') {
				return $data->value;
			}
		} elseif (in_array($data, array('{$__cakeID__$}', '{$__cakeForeignKey__$}'), true)) {
			return $data;
		} else {
			return null;
		}
		
	}
/**
 * Alter the Schema of a table.
 *
 * @param array $compare Results of CakeSchema::compare()
 * @param string $table name of the table
 * @access public
 * @return array
 */
	public function alterSchema($compare, $table = null) {
		
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
								$alter = 'ADD COLUMN '.$this->buildColumn($col);
								if (isset($col['after'])) {
									$alter .= ' AFTER '. $this->name($col['after']);
								}
								$colList[] = $alter;
							}
						break;
						case 'drop':
							foreach ($column as $field => $col) {
								$col['name'] = $field;
								$colList[] = 'DROP COLUMN '.$this->name($field);
							}
						break;
						case 'change':
							// CUSTOMIZE DEL 2010/05/16 ryuring
							//==================================================
							// PostgreSQLの場合、schemaでDB側の数値型の長さが取得できない為、
							// 変更されてない場合でも変更されてしまうので、chageは無視する
							// 仕様に変更（暫定措置）
							//==================================================
							/*foreach ($column as $field => $col) {
								if (!isset($col['name'])) {
									$col['name'] = $field;
								}
								$fieldName = $this->name($field);
								$colList[] = 'ALTER COLUMN '. $fieldName .' TYPE ' . str_replace($fieldName, '', $this->buildColumn($col));
							}*/
						break;
					}
				}
				if (isset($indexes['drop']['PRIMARY'])) {
					$colList[] = 'DROP CONSTRAINT ' . $curTable . '_pkey';
				}
				if (isset($indexes['add']['PRIMARY'])) {
					$cols = $indexes['add']['PRIMARY']['column'];
					if (is_array($cols)) {
						$cols = implode(', ', $cols);
					}
					$colList[] = 'ADD PRIMARY KEY (' . $cols . ')';
				}
				
				if (!empty($colList)) {
					$out .= "\t" . implode(",\n\t", $colList) . ";\n\n";
				} else {
					$out = '';
				}
				$out .= implode(";\n\t", $this->_alterIndexes($curTable, $indexes)) . ";";
			}
		}
		return $out;
		
	}
/**
 * Gets the length of a database-native column description, or null if no length
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return int An integer representing the length of the column
 */
	public function length($real) {
		
		// >>> CUSTOMIZE ADD 2012/04/23 ryuring
		if(preg_match('/^int([0-9]+)$/', $real, $maches)) {
			return intval($maches[1]);
		}
		// <<<
		
		$col = str_replace(array(')', 'unsigned'), '', $real);
		$limit = null;

		if (strpos($col, '(') !== false) {
			list($col, $limit) = explode('(', $col);
		}
		if ($col == 'uuid') {
			return 36;
		}
		if ($limit != null) {
			return intval($limit);
		}
		return null;
	}
/**
 * Returns a Model description (metadata) or null if none found.
 * DboPostgresのdescribeメソッドを呼び出さずにキャッシュを読み込む為に利用
 * Datasource::describe と同じ
 * 
 * @param Model $model
 * @return mixed
 * @access private
 */
	private function __describe($model) {
		
		if ($this->cacheSources === false) {
			return null;
		}
		$table = $this->fullTableName($model, false);
		if (isset($this->__descriptions[$table])) {
			return $this->__descriptions[$table];
		}
		$cache = $this->_cacheDescription($table);

		if ($cache !== null) {
			$this->__descriptions[$table] =& $cache;
			return $cache;
		}
		return null;
		
	}
}