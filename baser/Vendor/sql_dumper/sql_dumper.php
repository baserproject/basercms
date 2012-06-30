<?php
/**
 * SQL Dumper for CakePHP
 *
 * for CakePHP 1.2+
 * PHP version 5
 *
 * Copyright 2010, nojimage (http://php-tips.com/)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @version    1.0.1
 * @author     nojimage <nojimage at gmail.com>
 * @copyright  2010 nojimage (http://php-tips.com/)
 * @license    http://www.opensource.org/licenses/mit-license.php The MIT License
 * @package    sql_dumper
 * @subpackage sql_dumper.vendors
 * @link     　http://php-tips.com/
 * @since   　 File available since Release 1.0.0
 *
 */
class SqlDumper extends Object {

	var $description = '';

	var $message = 'generator: SqlDumper for CakePHP ver 1.0.1';

	/**
	 * output filename prefix
	 *
	 * allow using strftime format
	 *
	 * @var string
	 */
	var $file_prefix = 'sql_dump-';

	/**
	 * output filename suffix
	 *
	 * allow using strftime format
	 *
	 * @var string
	 */
	var $file_suffix = '-%Y%m%d_%H%M%S.sql';

	/**
	 *
	 * @var DboSource
	 */
	var $DataSource;

	/**
	 *
	 * @var CakeSchema
	 */
	var $Schema;

	/**
	 *
	 * @var File
	 */
	var $File;

	/**
	 *
	 * @var array
	 */
	var $_tables;

	/**
	 *
	 */
	function __construct() {

		App::import('Model', array('CakeSchema', 'AppModel'));

		if (!class_exists('CakeSchema')) {
			// for CakePHP 1.2
			App::import('Model', 'Schema');
		}

		$this->Schema =& ClassRegistry::init('CakeSchema');

	}

	/**
	 * Process Dump Datasouces
	 *
	 * @param string $datasource required.
	 * @param string $tablename  optional.
	 * @param string $save       optional.
	 * @param boolean $with_create optional. default: true
	 * @param boolean $with_drop   optional. default: true
	 * @param boolean $with_insert optional. default: true
	 * @param boolean $exclude_missing_tables optional. default: false
	 * @return string
	 */
	function process($datasource, $tablename = null, $save = null,
	$exclude_missing_tables = false,
	$with_create = true,  $with_drop = true, $with_insert = true) {

		if ( !$this->_setupDataSource($datasource) ) {
			return false;
		}

		$this->_setupOutput($datasource, $save);

		$processTables = $this->_getProcessTables($tablename, $exclude_missing_tables);

		$sql = '';

		$sql .= $this->_createSqlDumpHeader($datasource);

		foreach ($processTables as $_table => $data) {

			if ($with_drop) {
				$sql .= $this->getDropSql($datasource, $_table);
			}

			if ($with_create) {
				$sql .= $this->getCreateSql($datasource, $_table);
			}

			if ($with_insert) {
				$this->getInsertSql($datasource, $_table);
			}

			$sql .= $this->out($this->hr());
		}

		$sql .= $this->_createSqlDumpFooter($datasource);

		$this->_closeOutput();

		return $sql;

	}

	/**
	 * create all datasouces sql dump file
	 *
	 * @param string $save path to save folder.
	 */
	function processAll($save) {

		$datasources = ConnectionManager::sourceList();

		foreach ($datasources as $datasource) {

			$this->process($datasource, null, $save);

		}

	}

	/**
	 * sql create statement
	 *
	 * @param $datasource
	 * @param $tablename
	 * @param $exclude_missing_tables
	 * @return string
	 */
	function getCreateSql($datasource, $tablename = null, $exclude_missing_tables = false) {

		if (!$this->_checkCurrentDatasource($datasource)) {
			$this->_setupDataSource();
		}

		$this->Schema->tables = $this->_getProcessTables($tablename, $exclude_missing_tables);

		$sql = $this->DataSource->createSchema($this->Schema);

		return $this->out($sql);
	}

	/**
	 * sql drop statement
	 *
	 * @param $datasource
	 * @param $tablename
	 * @param $exclude_missing_tables
	 * @return string
	 */
	function getDropSql($datasource, $tablename = null, $exclude_missing_tables = false) {

		if (!$this->_checkCurrentDatasource($datasource)) {
			$this->_setupDataSource();
		}

		$this->Schema->tables = $this->_getProcessTables($tablename, $exclude_missing_tables);

		$sql = $this->DataSource->dropSchema($this->Schema);

		return $this->out($sql);
	}

	/**
	 * sql insert statement
	 *
	 * @param $datasource
	 * @param $tablename
	 * @param $exclude_missing_tables
	 * @param $return if want return sql string, set true.
	 * @return string
	 */
	function getInsertSql($datasource, $tablename, $exclude_missing_tables = false, $return = false) {

		if (!$this->_checkCurrentDatasource($datasource)) {
			$this->_setupDataSource();
		}

		if (!$return && (empty($this->File) || !$this->File->writable())) {
			return false;
		}

		$tables = $this->_getProcessTables($tablename, $exclude_missing_tables);

		$insert_sql = '';

		foreach ($tables as $table => $fields) {

			/* @var $model AppModel */
			$model = ClassRegistry::init(array('class' => Inflector::classify($table), 'table' => $table));

			$field_names = array_keys($this->DataSource->describe($model));

			$full_tablename = $this->DataSource->fullTableName($model);
			$all_fields = implode(', ', array_map(array($this->DataSource, 'name'), $field_names));

			$count_query = array(
                'table'  => $full_tablename,
                'fields' => 'count(*) ' . $this->DataSource->alias . 'count',
                'alias'  => $this->DataSource->alias . $this->DataSource->name($model->alias),
                'joins'  => '',
                'conditions' => 'WHERE 1=1',
                'group' => '',
                'order' => '',
                'limit' => '',
			);
			$count_sql = $this->DataSource->renderStatement('select', $count_query);

			$total = $this->DataSource->fetchRow($count_sql);
			if (is_array($total)) {
				$total = $total[0]['count'];
			}

			$query = array(
                'table'  => $full_tablename,
                'fields' => implode(', ', $this->DataSource->fields($model)),
                'alias'  => $this->DataSource->alias . $this->DataSource->name($model->alias),
                'joins'  => '',
                'conditions' => '',
                'group' => '',
                'order' => '',
                'limit' => '',
			);

			$limit = 100;
			$record = array();

			for ($offset = 0; $offset < $total; $offset += $limit) {

				$query['limit'] = $this->DataSource->limit($limit, $offset);

				$select_sql = $this->DataSource->renderStatement('select', $query);

				$datas = $this->DataSource->fetchAll($select_sql, false);

				foreach ($datas as $record) {

					$insert_query = array(
                        'table' => $full_tablename,
                        'fields' => $all_fields,
                        'values' => implode(', ', array_map(array($this->DataSource, 'value'), array_values($record[$model->alias])))
					);

					$_sql = $this->out($this->DataSource->renderStatement('create', $insert_query) . ';');

					if ($return) {
						$insert_sql .= $_sql;
					}
				}

			}

			// -- sequence update section for postgres
			// NOTE: only primary key sequence..
			if (method_exists($this->DataSource, 'getSequence')) {

				foreach ($fields as $field => $column) {

					if ($field == 'indexes' || empty($record)) {
						continue;
					}

					if ($column['type'] == 'integer' && isset($column['key']) && $column['key'] == 'primary') {
						// only primary key
						$sequence_name = $this->DataSource->getSequence($this->DataSource->fullTableName($model, false), $field);

						$_sql  = $this->out( sprintf('SELECT setval(%s, %s);', $this->DataSource->value($sequence_name), $record[$model->alias][$field]) );

						if ($return) {
							$insert_sql .= $_sql;
						}
					}

				}

			}
		}

		return $insert_sql;
	}

	/**
	 * Setup DataSource Object and get tables information
	 *
	 * @param string $datasouce
	 * @return bool
	 */
	function _setupDataSource($datasouce) {

		// get datasource
		$this->DataSource =& ConnectionManager::getDataSource($datasouce);

		if (!is_subclass_of($this->DataSource, 'DboSource')) {
			// DataSource is not subclass of DboSource
			return false;
		}

		// get datasouces tables
		$schema = $this->Schema->read(array('connection' => $this->DataSource->configKeyName, 'models' => false));
		$this->_tables = $schema['tables'];

		return true;
	}

	/**
	 *
	 *
	 * @param string  $tablename
	 * @param boolean $exclude_missing_tables
	 * @return array
	 */
	function _getProcessTables($tablename = null, $exclude_missing_tables = false) {

		$tables = $this->_tables;
		unset($tables['missing']);

		if (!$exclude_missing_tables && !empty($this->_tables['missing'])) {
			$tables = am($tables, $this->_tables['missing']);
		}

		if (!empty($tablename)) {

			if (!empty($tables[$tablename])) {
				return array($tablename => $tables[$tablename]);
			}

			return array();

		}

		return $tables;
	}

	/**
	 * Check current DataSouce
	 *
	 * @param string $datasource
	 */
	function _checkCurrentDatasource($datasource) {

		return $this->DataSource->configKeyName == $datasource;

	}

	/**
	 * Setup output source
	 *
	 * @param $datasource datasource name
	 * @param $save path to save folder
	 */
	function _setupOutput($datasource, $save) {

		if (empty($save)) {
			return false;
		}

		$now = time();
		$path = realpath($save) . DS . strftime($this->file_prefix, $now) . $datasource . strftime($this->file_suffix, $now);

		$this->File = new File($path, false, 0666);

		return $this->File->open('w');

	}

	/**
	 * Close file resource
	 */
	function _closeOutput() {
		if (isset($this->File)) {
			$this->File->close();
		}
	}

	/**
	 * get output file path
	 */
	function getOutputPath() {
		return $this->File->pwd();
	}

	/**
	 * SQL file header
	 *
	 * @param  $datasource
	 * @return string
	 */
	function _createSqlDumpHeader($datasource) {

		$sql = array();

		$sql[] = $this->hr(0);
		$sql[] = '-- ' . $this->message;
		$sql[] = '-- generated on: ' . date('Y-m-d H:i:s') . ' : ' . time();
		$sql[] = $this->hr(0);
		$sql[] = '';

		if (preg_match('/^mysql/i', $this->DataSource->config['driver'])) {
			$sql[] = 'use ' . $this->DataSource->name($this->DataSource->config['database']) . ';';
		}

		if (!empty($this->DataSource->config['encoding'])) {
			$sql[] = 'SET NAMES ' . $this->DataSource->value($this->DataSource->config['encoding']) . ';';
		}

		return $this->out($sql);

	}

	/**
	 * SQL file footer
	 *
	 * @param  $datasource
	 * @return string
	 */
	function _createSqlDumpFooter($datasource) {

		$sql = array();
		$sql[] = $this->hr(0);
		$sql[] = '-- END OF SQL DUMP';
		$sql[] = $this->hr(0);

		return $this->out($sql);

	}

	/**
	 * return separator string (SQL comment line)
	 *
	 * @return string
	 */
	function hr($newline = 1) {

		return '-- ' . str_repeat('-', 70) . $this->nl($newline);

	}

	/**
	 * Outputs a single or multiple error messages to stderr. If no parameters
	 * are passed outputs just a newline.
	 *
	 * @param mixed $message A string or a an array of strings to output
	 * @param integer $newlines Number of newlines to append
	 * @access public
	 */
	function out($message = null, $newlines = 1) {
		if (is_array($message)) {
			$message = implode($this->nl(), $message);
		}

		$output = $message . $this->nl($newlines);

		if (isset($this->File) && $this->File->writable()) {
			$this->File->append($output);
		}

		return $output;
	}

	/**
	 * Returns a single or multiple linefeeds sequences.
	 *
	 * @param integer $multiplier Number of times the linefeed sequence should be repeated
	 * @access public
	 * @return string
	 */
	function nl($multiplier = 1) {
		return str_repeat("\n", $multiplier);
	}

}