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
 * カラムを追加する
 * @param model $model
 * @param string $addFieldName
 * @param array $column
 * @return boolean
 * @access public
 */
    function addColumn(&$model,$addFieldName,$column){
		if(is_object($model)){
			$tableName = $model->tablePrefix.$model->table;
		}else{
			$tableName = $this->config['prefix'].Inflector::tableize($model);
		}
		if(empty($column['name'])){
			$column['name'] = $addFieldName;
		}
        return $this->execute("ALTER TABLE ".$tableName." ADD ".$this->buildColumn($column));
    }
/**
 * カラムを変更する
 * @param model $model
 * @param string $oldFieldName
 * @param string $newFieldName
 * @param array $column
 * @return boolean
 * @access public
 */
    function editColumn(&$model,$oldFieldName,$newFieldName,$column=null){
		if(is_object($model)){
			$tableName = $model->tablePrefix.$model->table;
		}else{
			$tableName = $this->config['prefix'].Inflector::tableize($model);
		}
        return $this->execute("ALTER TABLE ".$tableName." RENAME ".$oldFieldName." TO ".$newFieldName);
    }
/**
 * カラムを削除する
 * @param model $model
 * @param string $delFieldName
 * @param array $column
 * @return boolean
 * @access public
 */
    function deleteColumn($model,$delFieldName){
		if(is_object($model)){
			$tableName = $model->tablePrefix.$model->table;
		}else{
			$tableName = $this->config['prefix'].Inflector::tableize($model);
		}
        return $this->execute("ALTER TABLE ".$tableName." DROP ".$delFieldName);
    }
    
}
?>