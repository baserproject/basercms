<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Baser.Model.Datasource.Database
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

App::uses('Postgres', 'Model/Datasource/Database');

/**
 * Class BcPostgres
 *
 * PostgreSQL DBO拡張
 *
 * @package Baser.Model.Datasource.Database
 */
class BcPostgres extends Postgres
{

	/**
	 * Returns an array of the fields in given table name.
	 *
	 * @param Model|string $model Name of database table to inspect
	 * @return array Fields in table. Keys are name and type
	 */
	public function describe($model)
	{
		$table = $this->fullTableName($model, false, false);
		// CUSTOMIZE MODIFY 2014/03/28 ryuring
		// 関連シーケンスを正常に取得できない仕様対策
		// >>>
		//$fields = parent::describe($table);
		//$this->_sequenceMap[$table] = array();
		// ---
		$fields = $this->__describe($table);
		// <<<
		$cols = null;
		$hasPrimary = false;

		if ($fields === null) {
			// CUSTOMIZE MODIFY 2013/08/16 ryuring
			// udt_name フィールドを追加
			// >>>
			/*$cols = $this->_execute(
				'SELECT DISTINCT table_schema AS schema,
					column_name AS name,
					data_type AS type,
					is_nullable AS null,
					column_default AS default,
					ordinal_position AS position,
					character_maximum_length AS char_length,
					character_octet_length AS oct_length,
					pg_get_serial_sequence(attr.attrelid::regclass::text, attr.attname) IS NOT NULL AS has_serial
				FROM information_schema.columns c
				INNER JOIN pg_catalog.pg_namespace ns ON (ns.nspname = table_schema)
				INNER JOIN pg_catalog.pg_class cl ON (cl.relnamespace = ns.oid AND cl.relname = table_name)
				LEFT JOIN pg_catalog.pg_attribute attr ON (cl.oid = attr.attrelid AND column_name = attr.attname)
				WHERE table_name = ? AND table_schema = ? AND table_catalog = ?
				ORDER BY ordinal_position',
				array($table, $this->config['schema'], $this->config['database'])
			);*/
			// ---
			$cols = $this->_execute(
				"SELECT DISTINCT table_schema AS schema, column_name AS name, data_type AS type, udt_name AS udt, is_nullable AS null," .
				"column_default AS default, ordinal_position AS position, character_maximum_length AS char_length," .
				"character_octet_length AS oct_length FROM information_schema.columns " .
				"WHERE table_name = ? AND table_schema = ?  ORDER BY position",
				[$table, $this->config['schema']]
			);
			// <<<

			// @codingStandardsIgnoreStart
			// Postgres columns don't match the coding standards.
			foreach($cols as $c) {
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
						$type = 'uuid';
						$length = 36;
					} else {
						$length = (int)$c->oct_length;
					}
				} elseif (!empty($c->char_length)) {
					$length = (int)$c->char_length;
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
				$fields[$c->name] = [
					'type' => $this->column($type),
					'null' => ($c->null === 'NO'? false : true),
					'default' => preg_replace(
						"/^'(.*)'$/",
						"$1",
						preg_replace('/::[\w\s]+/', '', $c->default)
					),
					'length' => $length,
				];
				// CUSTOMIZE ADD 2013/08/16 ryuring
				// >>>
				if (!$fields[$c->name]['length'] && $fields[$c->name]['type'] == 'integer') {
					$fields[$c->name]['length'] = 8;
				}
				// <<<
				// Serial columns are primary integer keys
				if ($c->has_serial) {
					$fields[$c->name]['key'] = 'primary';
					$fields[$c->name]['length'] = 11;
					$hasPrimary = true;
				}
				if ($hasPrimary === false &&
					$model instanceof Model &&
					$c->name === $model->primaryKey
				) {
					$fields[$c->name]['key'] = 'primary';
					if (
						$fields[$c->name]['type'] !== 'string' &&
						$fields[$c->name]['type'] !== 'uuid'
					) {
						$fields[$c->name]['length'] = 11;
					}
				}
				if (
					$fields[$c->name]['default'] === 'NULL' ||
					$c->default === null ||
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
			// CUSTOMIZE ADD 2014/03/28 ryuring
			// 関連シーケンスを正常に取得できない仕様対策
			// >>>
			$fields['sequence'] = $this->_sequenceMap;
			// <<<
			$this->_cacheDescription($table, $fields);
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
	 * Gets the length of a database-native column description, or null if no length
	 *
	 * @param string $real Real database-layer column type (i.e. "varchar(255)")
	 * @return integer An integer representing the length of the column
	 */
	public function length($real)
	{
		// >>> CUSTOMIZE ADD 2012/04/23 ryuring
		if (preg_match('/^int([0-9]+)$/', $real, $maches)) {
			return intval($maches[1]);
		}
		// <<<
		$col = $real;
		if (strpos($real, '(') !== false) {
			list($col, $limit) = explode('(', $real);
		}
		if ($col === 'uuid') {
			return 36;
		}
		return parent::length($real);
	}

	/**
	 * {@inheritDoc}
	 */
	public function value($data, $column = null, $null = true)
	{
		$value = parent::value($data, $column, $null);
		if ($column === 'uuid' && is_scalar($data) && $data === '') {
			return 'NULL';
		}
		// CUSTOMIZE ADD 2014/07/02 ryuring
		// >>>
		switch($column) {
			case 'date':
			case 'datetime':
			case 'timestamp':
			case 'time':
				// postgresql の場合、0000-00-00 00:00:00 を指定すると範囲外エラーとなる為
				if ($data === '0000-00-00 00:00:00') {
					return "'" . date('Y-m-d H:i:s', 0) . "'";
				}
			case 'integer':
				// TreeBehavior::getPath() にて、引数 $id に、null、または、空文字を指定した場合に、
				// Model::id の初期値 false に上書きされてしまう仕様の為、SQLエラーが発生してしまう。
				if ($data === false) {
					return 'NULL';
				}
		}
		// <<<
		return $value;
	}

// CUSTOMIZE ADD 2014/07/02 ryuirng
// >>>
	/**
	 * テーブル名のリネームステートメントを生成
	 *
	 * @param string $sourceName
	 * @param string $targetName
	 * @return string
	 */
	public function buildRenameTable($sourceName, $targetName)
	{
		return "ALTER TABLE " . $sourceName . " RENAME TO " . $targetName;
	}

	/**
	 * カラム名を変更する
	 *
	 * @param array $options [ table / new / old  ]
	 * @return boolean
	 */
	public function renameColumn($options)
	{
		extract($options);

		if (!isset($table) || !isset($new) || !isset($old)) {
			return false;
		}

		$table = $this->config['prefix'] . $table;
		$sql = 'ALTER TABLE "' . $table . '" RENAME "' . $old . '" TO "' . $new . '"';
		return $this->execute($sql);
	}
// <<<
// CUSTOMIZE ADD 2014/07/02 ryuring
// >>>
	/**
	 * DboPostgresのdescribeメソッドを呼び出さずにキャッシュを読み込む為に利用
	 * Datasource::describe と同じ（一部ハック）
	 *
	 * @param Model|string $model
	 * @return array Array of Metadata for the $model
	 */
	private function __describe($model)
	{
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
			if (!empty($cache['sequence'][$table])) {
				$this->_sequenceMap[$table] = $cache['sequence'][$table];
			}
			unset($cache['sequence']);
			// <<<
			$this->_descriptions[$table] =& $cache;
			return $cache;
		}
		return null;
	}

	/**
	 * シーケンスを更新する
	 */
	public function updateSequence()
	{
		$tables = $this->listSources();
		$result = true;
		foreach($tables as $table) {
			$sql = 'select setval(\'' . $this->getSequence($table) . '\', (select max(id) from ' . $table . '));';
			if (!$this->execute($sql)) {
				$result = false;
			}
		}
		return $result;
	}
// <<<
}
