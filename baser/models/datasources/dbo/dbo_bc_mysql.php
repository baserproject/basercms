<?php
/* SVN FILE: $Id$ */
/**
 * MySQL DBO拡張
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models.datasources.dbo
 * @since			baserCMS v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
App::import('Core','DboMysql');
class DboBcMysql extends DboMysql {
/**
 * テーブル名のリネームステートメントを生成
 *
 * @param string $sourceName
 * @param string $targetName
 * @return string
 * @access public
 */
	function buildRenameTable($sourceName, $targetName) {
		
		return "ALTER TABLE `".$sourceName."` RENAME `".$targetName."`";
	
	}
/**
 * Returns a quoted and escaped string of $data for use in an SQL statement.
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @param string $column The column into which this data will be inserted
 * @param boolean $safe Whether or not numeric data should be handled automagically if no column data is provided
 * @return string Quoted and escaped data
 */
	function value($data, $column = null, $safe = false) {
		$parent = parent::value($data, $column, $safe);

		// CUSTOMIZE MODIFY 2012/09/03 ryuring
		// >>>
		/*if ($parent != null) {
			return $parent;
		}*/
		// ---
		if ($column != 'datetime' && $parent != null) {
			return $parent;
		}
		// <<<

		if ($data === null || (is_array($data) && empty($data))) {
			return 'NULL';
		}
		
		// CUSTOMIZE MODIFY 2012/08/31 ryuring
		// datetimeを条件に追加
		// >>>
		/*if ($data === '' && $column !== 'integer' && $column !== 'float' && $column !== 'boolean') {
			return  "''";
		}*/
		// ---
		if ($data === '' && $column !== 'integer' && $column !== 'float' && $column !== 'boolean' && $column !== 'datetime') {
			return  "''";
		}
		// <<<
		
		if (empty($column)) {
			$column = $this->introspectType($data);
		}

		switch ($column) {
			case 'boolean':
				return $this->boolean((bool)$data);
			break;
			case 'integer':
			case 'float':
				if ($data === '') {
					return 'NULL';
				}
				if ((is_int($data) || is_float($data) || $data === '0') || (
					is_numeric($data) && strpos($data, ',') === false &&
					$data[0] != '0' && strpos($data, 'e') === false)) {
						return $data;
					}
		// CUSTOMIZE ADD ryuring 2012/08/31
		// datetime を追加
		// >>>
			case 'datetime':
				if ($data === '') {
					return 'NULL';
				}
		// <<<
			default:
				$data = "'" . mysql_real_escape_string($data, $this->connection) . "'";
			break;
		}
		return $data;
	}
}
