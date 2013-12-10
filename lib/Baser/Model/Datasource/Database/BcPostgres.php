<?php

/* SVN FILE: $Id$ */
/**
 * PostgreSQL DBO拡張
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model.datasources.dbo
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
		return "ALTER TABLE " . $sourceName . " RENAME TO " . $targetName;
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

		if (!isset($table) || !isset($new) || !isset($old)) {
			return false;
		}

		$table = $this->config['prefix'] . $table;
		$sql = 'ALTER TABLE "' . $table . '" RENAME "' . $old . '" TO "' . $new . '"';
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

		switch ($column) {
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
					return "'" . date('Y-m-d H:i:s', 0) . "'";
				}
				// <<<
				// >>> CUSTOMIZE MODIFY 2013/04/12 ryuring
				// TreeBehavior::getPath() にて、引数 $id に、null、または、空文字を指定した場合に、
				// Model::id の初期値 false に上書きされてしまう仕様の為、SQLエラーが発生してしまう。
				//if ($data === '') {
				if ($data === '' || $data === false) {
					// <<<
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
				array(&$this, 'value'), $data, array_fill(0, count($data), $column), array_fill(0, count($data), $read)
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
 * Gets the length of a database-native column description, or null if no length
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return int An integer representing the length of the column
 */
	public function length($real) {
		// >>> CUSTOMIZE ADD 2012/04/23 ryuring
		if (preg_match('/^int([0-9]+)$/', $real, $maches)) {
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
 * Returns an array of the fields in given table name.
 *
 * @param Model|string $model Name of database table to inspect
 * @return array Fields in table. Keys are name and type
 */
	public function describe($model) {
		$table = $this->fullTableName($model, false, false);

		// CUSTOMIZE MODIFY 2013/08/16 ryuring
		// >>>
		//$fields = parent::describe($table);
		// ---
		$fields = $this->__describe($table);
		// <<<

		$this->_sequenceMap[$table] = array();
		$cols = null;

		if ($fields === null) {

			// CUSTOMIZE MODIFY 2013/08/16 ryuring
			// udt_name フィールドを追加
			// >>>
			/* $cols = $this->_execute(
			  "SELECT DISTINCT table_schema AS schema, column_name AS name, data_type AS type, is_nullable AS null,
			  column_default AS default, ordinal_position AS position, character_maximum_length AS char_length,
			  character_octet_length AS oct_length FROM information_schema.columns
			  WHERE table_name = ? AND table_schema = ?  ORDER BY position",
			  array($table, $this->config['schema'])
			  ); */
			// ---
			$cols = $this->_execute(
				"SELECT DISTINCT table_schema AS schema, column_name AS name, data_type AS type, udt_name AS udt, is_nullable AS null,
					column_default AS default, ordinal_position AS position, character_maximum_length AS char_length,
					character_octet_length AS oct_length FROM information_schema.columns
				WHERE table_name = ? AND table_schema = ?  ORDER BY position", array($table, $this->config['schema'])
			);

			// @codingStandardsIgnoreStart
			// Postgres columns don't match the coding standards.
			foreach ($cols as $c) {
				$type = $c->type;
				if (!empty($c->oct_length) && $c->char_length === null) {
					if ($c->type == 'character varying') {
						$length = null;
						$type = 'text';

						// CUSTOMIZE ADD 2013/08/16 ryuring
						// >>>
					} elseif ($c->type == 'text') {
						$length = null;
						// <<<
					} elseif ($c->type == 'uuid') {
						$length = 36;
					} else {
						$length = intval($c->oct_length);
					}
				} elseif (!empty($c->char_length)) {
					$length = intval($c->char_length);
				} else {

					// CUSTOMIZE MODIFY 2013/08/16 ryuring
					// >>>
					//$length = $this->length($c->type);
					// ---
					$length = $this->length($c->udt);
					// <<<
				}
				if (empty($length)) {
					$length = null;
				}
				$fields[$c->name] = array(
					'type' => $this->column($type),
					'null' => ($c->null == 'NO' ? false : true),
					'default' => preg_replace(
						"/^'(.*)'$/", "$1", preg_replace('/::.*/', '', $c->default)
					),
					'length' => $length
				);

				// CUSTOMIZE ADD 2013/08/16 ryuring
				// >>>
				if (!$fields[$c->name]['length'] && $fields[$c->name]['type'] == 'integer') {
					$fields[$c->name]['length'] = 8;
				}
				// <<<

				if ($model instanceof Model) {
					if ($c->name == $model->primaryKey) {
						$fields[$c->name]['key'] = 'primary';
						if ($fields[$c->name]['type'] !== 'string') {

							// CUSTOMIZE MODIFY 2013/08/16 ryuring
							// >>>
							//$fields[$c->name]['length'] = 11;
							// ---
							$fields[$c->name]['length'] = 11;
							// <<<
						}
					}
				}
				if (
					$fields[$c->name]['default'] == 'NULL' ||
					preg_match('/nextval\([\'"]?([\w.]+)/', $c->default, $seq)
				) {
					$fields[$c->name]['default'] = null;
					if (!empty($seq) && isset($seq[1])) {
						if (strpos($seq[1], '.') === false) {
							$sequenceName = $c->schema . '.' . $seq[1];
						} else {
							$sequenceName = $seq[1];
						}
						$this->_sequenceMap[$table][$c->name] = $sequenceName;
					}
				}
				if ($fields[$c->name]['type'] == 'boolean' && !empty($fields[$c->name]['default'])) {
					$fields[$c->name]['default'] = constant($fields[$c->name]['default']);
				}
			}
			$this->_cacheDescription($table, $fields);
		}
		// @codingStandardsIgnoreEnd

		if (isset($model->sequence)) {
			$this->_sequenceMap[$table][$model->primaryKey] = $model->sequence;
		}

		if ($cols) {
			$cols->closeCursor();
		}
		return $fields;
	}

/**
 * DboPostgresのdescribeメソッドを呼び出さずにキャッシュを読み込む為に利用
 * Datasource::describe と同じ
 * 
 * @param Model $model
 * @return mixed
 * @access private
 */
	private function __describe($table) {
		if ($this->cacheSources === false) {
			return null;
		}
		if (isset($this->__descriptions[$table])) {
			return $this->__descriptions[$table];
		}
		$cache = $this->_cacheDescription($table);

		if ($cache !== null) {
			$this->__descriptions[$table] = $cache;
			return $cache;
		}
		return null;
	}

}
