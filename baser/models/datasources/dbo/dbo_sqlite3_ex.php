<?php
/* SVN FILE: $Id$ */
/**
 * SQLite3 DBO拡張
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
/**
 * Include files
 */
App::import('Core','DboSqlite3',array('file'=>BASER_MODELS.'datasources'.DS.'dbo'.DS.'dbo_sqlite3.php'));
/**
 * SQLite3 DBO拡張
 *
 * @package			baser.models.datasources.dbo
 */
class DboSqlite3Ex extends DboSqlite3 {
/**
 * カラムを追加する
 * @param model $model
 * @param string $addFieldName
 * @param array $column
 * @return boolean
 * @access public
 */
    function addColumn(&$model,$addFieldName,$column){
        $this->execute("ALTER TABLE ".$model->tablePrefix.$model->table." ADD ".$this->columnSql($addFieldName, $column));
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
    function editColumn(&$model,$oldFieldName,$newfieldName,$column=null){

        $schema = $model->schema();
        $tableName = $model->tablePrefix.$model->table;

        //$this->begin($model);
        $this->execute('BEGIN TRANSACTION;');

        // リネームして一時テーブル作成
        if(!$this->renameTable($tableName,$tableName.'_temp')){
            //$this->rollback($model);
            $this->execute('ROLLBACK;');
            return false;
        }

        // スキーマのキーを変更（並び順を変えないように）
        $newSchema = array();
        foreach($schema as $key => $field){
            if($key == $oldFieldName){
                $key = $newfieldName;
            }
            $newSchema[$key] = $field;
        }
        
        // フィールドを変更した新しいテーブルを作成
        if(!$this->createTable($tableName,$newSchema)){
            //$this->rollback($model);
            $this->execute('ROLLBACK;');
            return false;
        }

        // データの移動
        $sql = 'INSERT INTO '.$tableName.' SELECT '.$this->convertCsvFieldsFromSchema($schema).' FROM '.$tableName.'_temp';
        $sql = str_replace($oldFieldName,$oldFieldName.' AS '.$newfieldName,$sql);
        if(!$this->execute($sql)){
            //$this->rollback($model);
            $this->execute('ROLLBACK;');
            return false;
        }

        // 一時テーブルを削除
        if(!$this->dropTable($tableName.'_temp')){
            //$this->rollback($model);
            $this->execute('ROLLBACK;');
            return false;
        }

        //$this->commit($model);
        $this->execute('COMMIT;');
        return true;
        
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

        $schema = $model->schema();
        $tableName = $model->tablePrefix.$model->table;

        //$this->begin($model);
        $this->execute('BEGIN TRANSACTION;');

        // リネームして一時テーブル作成
        if(!$this->renameTable($tableName,$tableName.'_temp')){
            //$this->rollback($model);
            $this->execute('ROLLBACK;');
            return false;
        }

        // フィールドを削除した新しいテーブルを作成
        unset($schema[$delFieldName]);
        if(!$this->createTable($tableName,$schema)){
            //$this->rollback($model);
            $this->execute('ROLLBACK;');
            return false;
        }

        // データの移動
        if(!$this->moveData($tableName.'_temp',$tableName,$schema)){
            //$this->rollback($model);
            $this->execute('ROLLBACK;');
            return false;
        }

        // 一時テーブルを削除
        if(!$this->dropTable($tableName.'_temp')){
            //$this->rollback($model);
            $this->execute('ROLLBACK;');
            return false;
        }

        //$this->commit($model);
        $this->execute('COMMIT;');
        return true;
        
    }
/**
 * テーブル名を変更する
 * @param string $sourceTableName
 * @param string $targetTableName
 * @return boolean
 */
    function renameTable($sourceTableName,$targetTableName){
        $sql = 'ALTER TABLE '.$sourceTableName.' RENAME TO '.$targetTableName;
        return $this->execute($sql);
    }
/**
 * テーブルからテーブルへデータを移動する
 * @param string $sourceTableName
 * @param string $targetTableName
 * @param array $schema
 * @return booelan
 */
    function moveData($sourceTableName,$targetTableName,$schema){
        $sql = 'INSERT INTO '.$targetTableName.' SELECT '.$this->convertCsvFieldsFromSchema($schema).' FROM '.$sourceTableName;
        return $this->execute($sql);
    }
/**
 * テーブルを削除する
 * @param string $tableName
 * @return boolean
 */
    function dropTable($tableName){
        $sql = 'drop table '.$tableName;
        return $this->execute($sql);
    }
/**
 * テーブルを作成する
 * @param string $tableName
 * @param array $schema
 * @return boolean
 */
    function createTable($tableName,$schema){
        
        $sql = 'CREATE TABLE '.$tableName .'(';
        $fields = '';
        foreach($schema as $key => $field){
            $sql .= $this->columnSql($key,$field).',';
        }
        $sql = substr($sql,0,strlen($sql)-1) . ');';
        return $this->execute($sql);

    }
/**
 * スキーマ情報よりCSV形式のフィールドリストを取得する
 * @param array $schema
 * @return string
 */
    function convertCsvFieldsFromSchema($schema){
        $fields = '';
        foreach($schema as $key => $field){
            $fields .= $key.',';
        }
        return substr($fields,0,strlen($fields)-1);
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
        if(!empty($column['key']) && $column['key'] == 'primary'){
            $sql .= " PRIMARY KEY";
        }
        return $sql;
        
    }
}