<?php
/* SVN FILE: $Id$ */
/**
 * PostgreSQL DBO拡張
 * 
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2009, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2009, Catchup, Inc.
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
        return $this->execute("ALTER TABLE ".$model->tablePrefix.$model->table." ADD ".$this->columnSql($addFieldName, $column));
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
        return $this->execute("ALTER TABLE ".$model->tablePrefix.$model->table." RENAME ".$oldFieldName." TO ".$newFieldName,$column);
    }
/**
 * カラムを削除する
 * @param model $model
 * @param string $delFieldName
 * @param array $column
 * @return boolean
 * @access public
 */
    function deleteColumn(&$model,$delFieldName){
        return $this->execute("ALTER TABLE ".$model->tablePrefix.$model->table." DROP ".$delFieldName);
    }
/**
 * カラム用のSQLを生成する
 * @param model $filedName
 * @param array $column
 * @return string $sql
 * @access public
 */
    function columnSql($filedName,$column){

        $sql = $filedName;
        if(!empty($column['type'])){
            $sql .= " ".$column['type'];
        }
        if(!empty($column['length'])){
            $sql .= " (".$column['length'].")";
        }
        if(isset($column['null']) && $column['null']){
            $sql .= ' NOT NULL';
        }
        if(!empty($column['default'])){
            $sql .= " DEFAULT ".$column['default'];
        }
        // TODO 確認要
        /*if(!empty($column['key']) || $column['key'] == 'primary'){
            $sql .= " auto_increment";
        }*/
        return $sql;
        
    }
    
}
?>