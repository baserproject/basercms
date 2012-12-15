<?php
/* SVN FILE: $Id$ */
/**
 * SQLite3 layer for DBO
 *
 * Long description for file
 *
 * PHP versions 5
 *
 * CakePHP(tm) :  Rapid Development Framework <http://www.cakephp.org/>
 * Copyright 2005-2008, Cake Software Foundation, Inc.
 *								1785 E. Sahara Avenue, Suite 490-204
 *								Las Vegas, Nevada 89104
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright		Copyright 2005-2008, Cake Software Foundation, Inc.
 * @link				http://www.cakefoundation.org/projects/info/cakephp CakePHP(tm) Project
 * @package			cake
 * @subpackage		cake.cake.libs.model.datasources.dbo
 * @since			CakePHP(tm) v 0.9.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://www.opensource.org/licenses/mit-license.php The MIT License
 */
/**
 * DBO implementation for the SQLite3 DBMS.
 *
 * Long description for class
 *
 * @package cake
 * @subpackage cake.cake.libs.model.datasources.dbo
 */
class DboSqlite3 extends DboSource {

/**
 * Enter description here...
 *
 * @var string
 * @access public
 */
	var $description = "SQLite3 DBO Driver";
/**
 * Enter description here...
 *
 * @var string
 * @access public
 */
	var $startQuote = '"';
/**
 * Enter description here...
 *
 * @var string
 * @access public
 */
	var $endQuote = '"';
/**
 * Base configuration settings for SQLite3 driver
 *
 * @var array
 * @access protected
 */
	var $_baseConfig = array(
		'persistent' => false,
		'database' => null,
		'connect' => 'sqlite' //sqlite3 in pdo_sqlite is sqlite. sqlite2 is sqlite2
	);
/**
 * SQLite3 column definition
 *
 * @var array
 * @access public
 */
	var $columns = array(
		'primary_key' => array('name' => 'integer primary key autoincrement'),
		'string' => array('name' => 'varchar', 'limit' => '255'),
		'text' => array('name' => 'text'),
		'integer' => array('name' => 'integer', 'limit' => null, 'formatter' => 'intval'),
		'float' => array('name' => 'float', 'formatter' => 'floatval'),
		'datetime' => array('name' => 'datetime', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
		'timestamp' => array('name' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
		'time' => array('name' => 'time', 'format' => 'H:i:s', 'formatter' => 'date'),
		'date' => array('name' => 'date', 'format' => 'Y-m-d', 'formatter' => 'date'),
		'binary' => array('name' => 'blob'),
		'boolean' => array('name' => 'boolean')
	);
	var $last_error = NULL;
	var $pdo_statement = NULL;
	var $rows = NULL;
	var $row_count = NULL;

/**
 * Connects to the database using config['database'] as a filename.
 *
 * @param array $config Configuration array for connecting
 * @return mixed
 * @access public
 */
	function connect() {
		
		//echo "runs connect\n";
		$this->last_error = null;
		$config = $this->config;
		//$this->connection = $config['connect']($config['database']);
		try {
			$this->connection = new PDO($config['connect'].':'.$config['database']);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			//$this->connected = is_resource($this->connection);
			$this->connected = is_object($this->connection);
		}
		catch(PDOException $e) {
			$this->last_error = array('Error connecting to database.',$e->getMessage());
		}
		return $this->connected;
		
	}
/**
 * Disconnects from database.
 *
 * @return boolean True if the database could be disconnected, else false
 */
	function disconnect() {
		
		//echo "runs disconnect\n";
		//@sqlite3_close($this->connection);
		$this->connection = NULL;
		$this->connected = false;
		return $this->connected;
		
	}
/**
 * Executes given SQL statement.
 *
 * @param string $sql SQL statement
 * @return resource Result resource identifier
 * @access protected
 */
	function _execute($sql) {
		
		//echo "runs execute\n";
		//return sqlite3_query($this->connection, $sql);

		for ($i = 0; $i < 2; $i++) {
			try {
				$this->last_error = NULL;
				$this->pdo_statement = $this->connection->query($sql);
				if (is_object($this->pdo_statement)) {
					$this->rows = $this->pdo_statement->fetchAll(PDO::FETCH_NUM);
					$this->row_count = count($this->rows);
					return $this->pdo_statement;
				}
			}
			catch(PDOException $e) {
				// Schema change; re-run query
				if ($e->errorInfo[1] === 17) continue;
				$this->last_error = $e->getMessage();
			}
		}
		return false;
		
	}
/**
 * Returns an array of tables in the database. If there are no tables, an error is raised and the application exits.
 *
 * @return array Array of tablenames in the database
 * @access public
 */
	function listSources() {
		
		//echo "runs listSources\n";
		$db = $this->config['database'];
		$this->config['database'] = basename($this->config['database']);

		$cache = parent::listSources();
		if ($cache != null) {
			// >>> ADD 2010/03/19 egashira
			// 接続をフルパスに戻す
			$this->config['database'] = $db;
			// <<<
			return $cache;
		}

		//echo "listsources:beforeresult ";
		// >>> CUSTOMIZE MODIFY 2010/12/26 ryuring
		//$result = $this->fetchAll("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;", false);
		// ---
		$result = $this->fetchAll("SELECT name FROM sqlite_master WHERE type='table' AND name<>'sqlite_sequence' ORDER BY name;", false);
		// <<<
		//echo "listsources:result ";
		//pr($result);

		if (!$result || empty($result)) {
			// >>> ADD 2010/03/19 egashira
			// 接続をフルパスに戻す
			$this->config['database'] = $db;
			// <<<
			return array();
		} else {
			$tables = array();
			foreach ($result as $table) {
				$tables[] = $table[0]['name'];
			}
			parent::listSources($tables);

			$this->config['database'] = $db;
			return $tables;
		}
		$this->config['database'] = $db;
		return array();
		
	}
/**
 * Returns an array of the fields in given table name.
 *
 * @param string $tableName Name of database table to inspect
 * @return array Fields in table. Keys are name and type
 * @access public
 */
	function describe(&$model) {
		
		$cache = parent::describe($model);
		if ($cache != null) {
			return $cache;
		}
		$fields = array();
		$result = $this->fetchAll('PRAGMA table_info(' . $model->tablePrefix . $model->table . ')');

		foreach ($result as $column) {
			$fields[$column[0]['name']] = array(
				'type'		=> $this->column($column[0]['type']),
				'null'		=> !$column[0]['notnull'],
				'default'	=> $column[0]['dflt_value'],
				'length'	=> $this->length($column[0]['type'])
			);
			if($column[0]['pk'] == 1) {
				$fields[$column[0]['name']] = array(
					'type'		=> $fields[$column[0]['name']]['type'],
					'null'		=> false,
					'default'	=> $column[0]['dflt_value'],
					'key'		=> $this->index['PRI'],
					'length'	=> 11
				);
			}
		}

		$this->__cacheDescription($model->tablePrefix . $model->table, $fields);
		return $fields;
		
	}
/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @param string $column
 * @param int $safe
 * @return string Quoted and escaped
 * @access public
 */
	function value ($data, $column = null, $safe = false) {
		
		$parent = parent::value($data, $column, $safe);

		if ($parent != null) {
			return $parent;
		}

		if ($data === null) {
			return 'NULL';
		}

		switch ($column) {
			case 'boolean':
				if ($data === '') {
					return 0;
				}
				$data = $this->boolean((bool)$data);
				break;
			case 'integer';
				if ($data === '') {
					return 'NULL';
				}
				break;
			case 'datetime':
				if($data) {
					$data = trim(str_replace('/', '-', $data));
				}
				if ($data === '' || $data == '0000-00-00 00:00:00') {
					return "''";
				}
				break;
			default:
				if ($data === '') {
					return "''";
				}
				$data = $this->connection->quote($data);
				return $data;
				break;
		}
		return "'" . $data . "'";
		
	}
/**
 * Generates and executes an SQL UPDATE statement for given model, fields, and values.
 *
 * @param Model $model
 * @param array $fields
 * @param array $values
 * @param mixed $conditions
 * @return array
 * @access public
 */
	function update(&$model, $fields = array(), $values = null, $conditions = null) {
		
		if (empty($values) && !empty($fields)) {
			foreach ($fields as $field => $value) {
				if (strpos($field, $model->alias . '.') !== false) {
					unset($fields[$field]);
					$field = str_replace($model->alias . '.', "", $field);
					$field = str_replace($model->alias . '.', "", $field);
					$fields[$field] = $value;
				}
			}
		}
		return parent::update($model, $fields, $values, $conditions);
		
	}
/**
 * Begin a transaction
 * TODO データベースがロックされてしまい正常に処理が実行されないのでとりあえず未実装とする
 * ロックに関する原因については未解析
 * 
 * @param string $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions).
 * @access public
 */
	function begin (&$model) {
		
		return null;
		/*if (parent::begin($model)) {
			if ($this->connection->beginTransaction()) {
				$this->_transactionStarted = true;
				return true;
			}
		}
		return false;*/
		
	}
/**
 * Commit a transaction
 * TODO データベースがロックされてしまい正常に処理が実行されないのでとりあえず未実装とする
 * ロックに関する原因については未解析
 * 
 * @param unknown_type $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 * @access public
 */
	function commit (&$model) {
		
		return null;
		/*if (parent::commit($model)) {
			$this->_transactionStarted = false;
			return $this->connection->commit();
		}
		return false;*/
		
	}
/**
 * Rollback a transaction
 * TODO データベースがロックされてしまい正常に処理が実行されないのでとりあえず未実装とする
 * ロックに関する原因については未解析
 * 
 * @param unknown_type $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 * @access public
 */
	function rollback (&$model) {
		
		return null;
		/*if (parent::rollback($model)) {
			return $this->connection->rollBack();
		}
		return false;*/
		
	}
/**
 * Deletes all the records in a table and resets the count of the auto-incrementing
 * primary key, where applicable.
 *
 * @param mixed $table A string or model class representing the table to be truncated
 * @return boolean SQL TRUNCATE TABLE statement, false if not applicable.
 * @access public
 */
	function truncate($table) {
		
		return $this->execute('DELETE From ' . $this->fullTableName($table));
		
	}
/**
 * Returns a formatted error message from previous database operation.
 *
 * @return string Error message
 * @access public
 */
	function lastError() {
		
		return $this->last_error;
		
	}
/**
 * Returns number of affected rows in previous database operation. If no previous operation exists, this returns false.
 *
 * @return integer Number of affected rows
 * @access public
 */
	function lastAffected() {
		
		if ($this->_result) {
			return $this->pdo_statement->rowCount();
		}
		return false;
		
	}
/**
 * Returns number of rows in previous resultset. If no previous resultset exists,
 * this returns false.
 *
 * @return integer Number of rows in resultset
 * @access public
 */
	function lastNumRows() { 
		
		if ($this->pdo_statement) {
			// pdo_statement->rowCount() doesn't work for this case
			return $this->row_count;
		}
		return false;
		
	}
/**
 * Returns the ID generated from the previous INSERT operation.
 *
 * @return int
 * @access public
 */
	function lastInsertId() {
		
		//return sqlite3_last_insert_rowid($this->connection);
		return $this->connection->lastInsertId();
		
	}
/**
 * Converts database-layer column types to basic types
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return string Abstract column type (i.e. "string")
 * @access public
 */
	function column($real) {
		
		if (is_array($real)) {
			$col = $real['name'];
			if (isset($real['limit'])) {
				$col .= '('.$real['limit'].')';
			}
			return $col;
		}

		$col = strtolower(str_replace(')', '', $real));
		$limit = null;
		@list($col, $limit) = explode('(', $col);

		if (in_array($col, array('text', 'integer', 'float', 'boolean', 'timestamp', 'date', 'datetime', 'time'))) {
			return $col;
		}
		if (strpos($col, 'varchar') !== false || strpos($col, 'char') !== false) {
			return 'string';
		}
		if (in_array($col, array('blob', 'clob'))) {
			return 'binary';
		}
		if (strpos($col, 'numeric') !== false) {
			return 'float';
		}
		return 'text';
		
	}
/**
 * Enter description here...
 *
 * @param unknown_type $results
 * @return string
 * @access public
 */
	function resultSet(&$results) {
		
		$this->results =& $results;
		//echo "resultSet:results ";
		//pr($results);
		$this->map = array();
		$num_fields = $results->columnCount();
		$index = 0;
		$j = 0;

		//PDO::getColumnMeta is experimental and does not work with sqlite3,
		//so try to figure it out based on the querystring
		$querystring = $results->queryString;
		if (strpos($querystring,"SELECT") === 0)
		{
			$last = strpos($querystring,"FROM");
			if ($last !== false)
			{
				$selectpart = substr($querystring,7,$last-8);
				$selects = explode(",",$selectpart);
			}
		}
		elseif (strpos($querystring,"PRAGMA table_info") === 0)
		{
			$selects = array("cid","name","type","notnull","dflt_value","pk");
		}

		while ($j < $num_fields) {
			//echo "resultSet:columnmeta ";
			//$columnName = str_replace('"', '', sqlite3_field_name($results, $j));

			if(preg_match('/.*AS "(.*)".*/i', $selects[$j], $matches)){
				$columnName = $matches[1];
			}else{
				$columnName = trim(str_replace('"', '', $selects[$j]));
			}

			if (strpos($columnName, '.')) {
				$parts = explode('.', $columnName);
				$this->map[$index++] = array(trim($parts[0]), trim($parts[1]));
			} else {
				$this->map[$index++] = array(0, $columnName);
			}

			$j++;
		}
		
	}

/**
 * Fetches the next row from the current result set
 *
 * @return unknown
 * @access public
 */
	function fetchResult() {
		
		//if ($row = sqlite3_fetch_array($this->results, SQLITE3_ASSOC)) {
		if (count($this->rows)) {
			$row = array_shift($this->rows);
			//echo "fetchResult:nextrow ";
			//pr($row);
			$resultRow = array();
			$i = 0;

			foreach ($row as $index => $field) {
				//pr($index);
				if (isset($this->map[$index]) and $this->map[$index] != "") {
					//echo "asdf: ".$this->map[$index];
					list($table, $column) = $this->map[$index];
					$resultRow[$table][$column] = $row[$index];
				} else {
					$resultRow[0][str_replace('"', '', $index)] = $row[$index];
				}
				$i++;
			}
			//pr($resultRow);
			return $resultRow;
		} else {
			return false;
		}
		
	}
/**
 * Returns a limit statement in the correct format for the particular database.
 *
 * @param integer $limit Limit of results returned
 * @param integer $offset Offset from which to start results
 * @return string SQL limit/offset statement
 * @access public
 */
	function limit ($limit, $offset = null) {
		
		if ($limit) {
			$rt = '';
			if (!strpos(strtolower($limit), 'limit') || strpos(strtolower($limit), 'limit') === 0) {
				$rt = ' LIMIT';
			}
			$rt .= ' ' . $limit;
			if ($offset) {
				$rt .= ' OFFSET ' . $offset;
			}
			return $rt;
		}
		return null;
		
	}
/**
 * Generate a database-native column schema string
 *
 * @param array $column An array structured like the following: array('name'=>'value', 'type'=>'value'[, options]),
 * where options can be 'default', 'length', or 'key'.
 * @return string
 * @access public
 */
	function buildColumn($column) {
		
		$name = $type = null;
		$column = array_merge(array('null' => true), $column);
		extract($column);

		if (empty($name) || empty($type)) {
			trigger_error('Column name or type not defined in schema', E_USER_WARNING);
			return null;
		}

		if (!isset($this->columns[$type])) {
			trigger_error("Column type {$type} does not exist", E_USER_WARNING);
			return null;
		}

		$real = $this->columns[$type];
		if (isset($column['key']) && $column['key'] == 'primary') {
			$out = $this->name($name) . ' ' . $this->columns['primary_key']['name'];
		} else {
			$out = $this->name($name) . ' ' . $real['name'];

			if (isset($real['limit']) || isset($real['length']) || isset($column['limit']) || isset($column['length'])) {
				if (isset($column['length'])) {
					$length = $column['length'];
				} elseif (isset($column['limit'])) {
					$length = $column['limit'];
				} elseif (isset($real['length'])) {
					$length = $real['length'];
				} else {
					$length = $real['limit'];
				}
				$out .= '(' . $length . ')';
			}
			if (isset($column['key']) && $column['key'] == 'primary') {
				$out .= ' NOT NULL';
			} elseif (isset($column['default']) && isset($column['null']) && $column['null'] == false) {
				$out .= ' DEFAULT ' . $this->value($column['default'], $type) . ' NOT NULL';
			} elseif (isset($column['default'])) {
				$out .= ' DEFAULT ' . $this->value($column['default'], $type);
			} elseif (isset($column['null']) && $column['null'] == true) {
				$out .= ' DEFAULT NULL';
			} elseif (isset($column['null']) && $column['null'] == false) {
				$out .= ' NOT NULL';
			}
		}
		return $out;
		
	}

/**
 * Removes redundant primary key indexes, as they are handled in the column def of the key.
 *
 * @param array $indexes
 * @param string $table
 * @return string
 */
	function buildIndex($indexes, $table = null) {
		
		$join = array();

		foreach ($indexes as $name => $value) {
			if ($name == 'PRIMARY') {
				continue;
			} else {
				$out = 'CREATE ';
				if (!empty($value['unique'])) {
					$out .= 'UNIQUE ';
				}
				if (is_array($value['column'])) {
					$value['column'] = join(', ', array_map(array(&$this, 'name'), $value['column']));
				} else {
					$value['column'] = $this->name($value['column']);
				}
				$out .= "INDEX {$name} ON {$table}({$value['column']});";
			}
			$join[] = $out;
		}
		return $join;
		
	}

/**
 * Overrides DboSource::renderStatement to handle schema generation with SQLite3-style indexes
 *
 * @param string $type
 * @param array $data
 * @return string
 */
	function renderStatement($type, $data) {
		
		switch (strtolower($type)) {
			case 'schema':
				extract($data);

				foreach (array('columns', 'indexes') as $var) {
					if (is_array(${$var})) {
						${$var} = "\t" . join(",\n\t", array_filter(${$var}));
					}
				}

				return "CREATE TABLE {$table} (\n{$columns});\n{$indexes}";
			break;
			default:
				return parent::renderStatement($type, $data);
			break;
		}
		
	}

	/**
	 * PDO deals in objects, not resources, so overload accordingly.
	 */
	function hasResult() {
		
		return is_object($this->_result);
		
	}

}
