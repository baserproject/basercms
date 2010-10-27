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
}
?>