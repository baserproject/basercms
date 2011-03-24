<?php
/* SVN FILE: $Id$ */
/**
 * PostgreSQL DBO拡張
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
App::import('Core','DboPostgres');
class DboPostgresEx extends DboPostgres {
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
 * カラム名を変更する
 *
 * @param	array	$options [ table / new / old  ]
 * @return boolean
 * @access public
 */
	function renameColumn($options) {

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
 */
	function value($data, $column = null, $read = true) {

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
 */
	function __value($data, $column = null, $read = true) {
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
}
?>