<?php
/**
 * PostgreSQL DBO拡張
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model.Datasource.Database
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('Postgres', 'Model/Datasource/Database');

class BcPostgres extends Postgres {
// CUSTOMIZE ADD 2014/07/02 ryuirng
// >>>
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
// <<<

/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @param string $column The column datatype into which this data will be inserted.
 * @return string Quoted and escaped data
 */
	public function value($data, $column = null) {
		if (is_array($data) && !empty($data)) {
			return array_map(
				array(&$this, 'value'),
				$data, array_fill(0, count($data), $column)
			);
		} elseif (is_object($data) && isset($data->type, $data->value)) {
			if ($data->type === 'identifier') {
				return $this->name($data->value);
			} elseif ($data->type === 'expression') {
				return $data->value;
			}
		} elseif (in_array($data, array('{$__cakeID__$}', '{$__cakeForeignKey__$}'), true)) {
			return $data;
		}

		if ($data === null || (is_array($data) && empty($data))) {
			return 'NULL';
		}

		if (empty($column)) {
			$column = $this->introspectType($data);
		}

		switch ($column) {
			case 'binary':
				return $this->_connection->quote($data, PDO::PARAM_LOB);
			case 'boolean':
				return $this->_connection->quote($this->boolean($data, true), PDO::PARAM_BOOL);
			case 'string':
			case 'text':
				return $this->_connection->quote($data, PDO::PARAM_STR);
			// CUSTOMIZE ADD 2014/07/02 ryuring
			// >>>
			case 'date':
			case 'datetime':
			case 'timestamp':
			case 'time':
				// postgresql の場合、0000-00-00 00:00:00 を指定すると範囲外エラーとなる為
				if ($data === '0000-00-00 00:00:00') {
					return "'" . date('Y-m-d H:i:s', 0) . "'";
				}
				// no break
			case 'integer':
				// TreeBehavior::getPath() にて、引数 $id に、null、または、空文字を指定した場合に、
				// Model::id の初期値 false に上書きされてしまう仕様の為、SQLエラーが発生してしまう。
				if ($data === false) {
					// <<<
					return 'NULL';
				}
				// no break
			// <<<
			default:
				if ($data === '') {
					return 'NULL';
				}
				if (is_float($data)) {
					return str_replace(',', '.', strval($data));
				}
				if ((is_int($data) || $data === '0') || (
					is_numeric($data) && strpos($data, ',') === false &&
					$data[0] != '0' && strpos($data, 'e') === false)
				) {
					return $data;
				}
				return $this->_connection->quote($data);
		}
	}

/**
 * Gets the length of a database-native column description, or null if no length
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return integer An integer representing the length of the column
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
		if ($col === 'uuid') {
			return 36;
		}
		if ($limit) {
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
		// CUSTOMIZE MODIFY 2014/03/28 ryuring
		// 関連シーケンスを正常に取得できない仕様対策
		// >>>
		//$fields = parent::describe($table);
		//$this->_sequenceMap[$table] = array();
		// ---
		$fields = $this->__describe($table);
		// <<<
		$cols = null;

		if ($fields === null) {

			// CUSTOMIZE MODIFY 2013/08/16 ryuring
			// udt_name フィールドを追加
			// >>>
			/* 	$cols = $this->_execute(
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
				WHERE table_name = ? AND table_schema = ?  ORDER BY position",
				array($table, $this->config['schema'])
			);
			// <<<

			// @codingStandardsIgnoreStart
			// Postgres columns don't match the coding standards.
			foreach ($cols as $c) {
				$type = $c->type;
				if (!empty($c->oct_length) && $c->char_length === null) {
					if ($c->type === 'character varying') {
						$length = null;
						$type = 'text';
					// CUSTOMIZE ADD 2013/08/16 ryuring
					// >>>
					} elseif ($c->type == 'text') {
						$length = null;
					// <<<
					} elseif ($c->type === 'uuid') {
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
					'null' => ($c->null === 'NO' ? false : true),
					'default' => preg_replace(
						"/^'(.*)'$/",
						"$1",
						preg_replace('/::.*/', '', $c->default)
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
					if ($c->name === $model->primaryKey) {
						$fields[$c->name]['key'] = 'primary';
						if ($fields[$c->name]['type'] !== 'string') {
							$fields[$c->name]['length'] = 11;
							
						}
					}
				}
				if (
					$fields[$c->name]['default'] === 'NULL' ||
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
				
				if ($fields[$c->name]['type'] === 'timestamp' && $fields[$c->name]['default'] === '') {
					$fields[$c->name]['default'] = null;
				}
				if ($fields[$c->name]['type'] === 'boolean' && !empty($fields[$c->name]['default'])) {
					$fields[$c->name]['default'] = constant($fields[$c->name]['default']);
				}
			}
			
			// CUSTOMIZE MODIFY 2014/03/28 ryuring
			// 関連シーケンスを正常に取得できない仕様対策
			// >>>
			//$this->_cacheDescription($table, $fields);
			// ---
			$fields['sequence'] = $this->_sequenceMap;
			$this->_cacheDescription($table, $fields);
			// <<<
		}
		// @codingStandardsIgnoreEnd

		// CUSTOMIZE ADD 2014/07/3 ryuring
		// >>>
		unset($fields['sequence']);
		// <<<
		
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
 * Datasource::describe と同じ（一部ハック）
 * 
 * @param Model|string $model
 * @return array Array of Metadata for the $model
 */
	private function __describe($model) {
		if ($this->cacheSources === false) {
			return null;
		}
		if (is_string($model)) {
			$table = $model;
		} else {
			$table = $model->tablePrefix . $model->table;
		}

		if (isset($this->_descriptions[$table])) {
			return $this->_descriptions[$table];
		}
		$cache = $this->_cacheDescription($table);

		if ($cache !== null) {
			
			// CUSTOMIZE ADD 2014/03/28 ryuring
			// 関連シーケンスを正常に取得できない仕様対策
			// >>>
			if(!empty($cache['sequence'][$table])) {
				$this->_sequenceMap[$table] = $cache['sequence'][$table];
			}
			unset($cache['sequence']);
			// <<<
			$this->_descriptions[$table] =& $cache;
			return $cache;
		}
		return null;
	}
	
// CUSTOMIZE ADD 2014/07/02 ryuring
// >>>
/**
 * シーケンスを更新する
 */
	public function updateSequence() {
		$tables = $this->listSources();
		$result = true;
		foreach($tables as $table) {
			$sql = 'select setval(\'' . $this->getSequence($table) . '\', (select max(id) from ' . $table . '));';
			if(!$this->execute($sql)) {
				$result = false;
			}
		}
		return $result;
	}
// <<<
}
