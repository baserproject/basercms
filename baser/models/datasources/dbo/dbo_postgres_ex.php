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
 * カラムを追加するSQLを生成
 *
 * @param string $tableName
 * @param array $column
 * @return string
 * @access public
 */
	function buildAddColumn($tableName, $column) {
		return 'ALTER TABLE "'.$tableName.'" ADD '.$this->buildColumn($column);
	}
/**
 * カラムを変更するSQLを生成
 * TODO 未実装
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
 *
 * @param string $delFieldName
 * @param array $column
 * @return string
 * @access public
 */
	function buildDelColumn($tableName, $delFieldName) {
		return 'ALTER TABLE "'.$tableName.'" DROP "'.$delFieldName.'"';
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
 * カラム名を変更する
 *
 * @param string $oldFieldName
 * @param string $newFieldName
 * @return boolean
 * @access public
 */
	function renameColumn(&$model,$oldFieldName,$newFieldName) {
		$tableName = $model->tablePrefix.$model->table;
		$sql = 'ALTER TABLE "'.$tableName.'" RENAME "'.$oldFieldName.'" TO "'.$newFieldName.'"';
		return $this->execute($sql);
	}
}
?>