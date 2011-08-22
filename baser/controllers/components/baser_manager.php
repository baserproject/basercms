<?php
/* SVN FILE: $Id$ */
/**
 * BaserManagerコンポーネント
 *
 * PHP versions 4 and 5
 *
 * BaserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2011, Catchup, Inc.
 *								9-5 nagao 3-chome, fukuoka-shi
 *								fukuoka, Japan 814-0123
 *
 * @copyright		Copyright 2008 - 2011, Catchup, Inc.
 * @link			http://basercms.net BaserCMS Project
 * @package			baser.components
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
class BaserManagerComponent extends Object {
/**
 * データベースを初期化する
 * 
 * @param type $reset
 * @param type $dbConfig
 * @param type $nonDemoData
 * @return type
 * @access public 
 */
	function initDb($reset = true, $dbConfig = null, $nonDemoData = false) {
		
		if($reset) {
			$this->deleteTables();
			$this->deleteTables('plugin');
		}
		
		return $this->constructionDb($dbConfig, $nonDemoData);
		
	}
/**
 * データベースを構築する
 * 
 * @param type $dbConfig
 * @param type $nonDemoData
 * @return type
 * @access public
 */
	function constructionDb($dbConfig = null, $nonDemoData = false) {

		if(!$this->constructionTable(BASER_CONFIGS.'sql', 'baser', $dbConfig, $nonDemoData)) {
			return false;
		}
		if(!$this->constructionTable(BASER_PLUGINS.'blog'.DS.'config'.DS.'sql', 'plugin', $dbConfig, $nonDemoData)) {
			return false;
		}
		if(!$this->constructionTable(BASER_PLUGINS.'feed'.DS.'config'.DS.'sql', 'plugin', $dbConfig, $nonDemoData)) {
			return false;
		}
		if(!$this->constructionTable(BASER_PLUGINS.'mail'.DS.'config'.DS.'sql', 'plugin', $dbConfig, $nonDemoData)) {
			return false;
		}
		return true;

	}
/**
 * テーブルを構築する
 *
 * @param string	$path
 * @param string	$dbConfigKeyName
 * @param string	$dbConfig
 * @param string	$nonDemoData
 * @return boolean
 * @access public
 */
	function constructionTable($path, $dbConfigKeyName = 'baser', $dbConfig = null, $nonDemoData = false) {

		$db =& $this->_getDataSource($dbConfigKeyName, $dbConfig);
		$driver = str_replace('_ex', '', $db->config['driver']);
		
		if (!$db->connected && $driver != 'csv') {
			return false;
		} elseif($driver == 'csv') {
			// CSVの場合はフォルダを作成する
			$folder = new Folder($db->config['database'], true, 0777);
		} elseif($driver == 'sqlite3') {
			$db->connect();
			chmod($db->config['database'], 0666);
		}

		$folder = new Folder($path);
		$files = $folder->read(true, true, true);

		if(isset($files[1])) {

			// DB構築
			foreach($files[1] as $file) {

				if(!preg_match('/\.php$/',$file)) {
					continue;
				}
				if(!$db->createTableBySchema(array('path'=>$file))){
					return false;
				}
				
			}

			if($nonDemoData && $configKeyName == 'baser') {
				$nonDemoData = false;
				$folder = new Folder($path.DS.'non_demo');
				$files = $folder->read(true, true, true);
			}
			if(!$nonDemoData) {

				// CSVの場合ロックを解除しないとデータの投入に失敗する
				if($driver == 'csv') {
					$db->reconnect();
				}

				// 初期データ投入
				foreach($files[1] as $file) {
					if(!preg_match('/\.csv$/',$file)) {
						continue;
					}
					if(!$db->loadCsv(array('path'=>$file, 'encoding'=>'SJIS'))){
						return false;
					}
				}
			}
		}
		
		return true;

	}
/**
 * 全てのテーブルを削除する
 * 
 * @param type $dbConfig 
 * @return void
 * @access public
 */
	function deleteAllTables($dbConfig = null) {
		
		$this->deleteTables('baser', $dbConfig);
		$this->deleteTables('plugin', $dbConfig);
		
	}
/**
 * テーブルを削除する
 * 
 * @param string $dbConfigKeyName
 * @param array $dbConfig
 * @return boolean
 * @access public
 * TODO 処理を DboSource に移動する
 */
	function deleteTables($dbConfigKeyName = 'baser', $dbConfig = null) {

		$db =& $this->_getDataSource($dbConfigKeyName, $dbConfig);
		$dbConfig = $db->config;
		
		/* 削除実行 */
		// TODO schemaを有効活用すればここはスッキリしそうだが見送り
		$dbType = str_replace('_ex','',$dbConfig['driver']);
		switch ($dbType) {
			case 'mysql':
				$sources = $db->listSources();
				foreach($sources as $source) {
					if(preg_match("/^".$dbConfig['prefix']."([^_].+)$/", $source)) {
						$sql = 'DROP TABLE '.$source;
						$db->execute($sql);
					}
				}
				break;

			case 'postgres':
				$sources = $db->listSources();
				foreach($sources as $source) {
					if(preg_match("/^".$dbConfig['prefix']."([^_].+)$/", $source)) {
						$sql = 'DROP TABLE '.$source;
						$db->execute($sql);
					}
				}
				// シーケンスも削除
				$sql = "SELECT sequence_name FROM INFORMATION_SCHEMA.sequences WHERE sequence_schema = '{$dbConfig['schema']}';";
				$sequences = $db->query($sql);
				$sequences = Set::extract('/0/sequence_name',$sequences);
				foreach($sequences as $sequence) {
					if(preg_match("/^".$dbConfig['prefix']."([^_].+)$/", $sequence)) {
						$sql = 'DROP SEQUENCE '.$sequence;
						$db->execute($sql);
					}
				}
				break;

			case 'sqlite':
			case 'sqlite3':
				@unlink($dbConfig['database']);
				break;

			case 'csv':
				$folder = new Folder($dbConfig['database']);
				$files = $folder->read(true,true,true);
				foreach($files[1] as $file) {
					if(basename($file) != 'empty') {
						@unlink($file);
					}
				}
				break;

		}
		return true;

	}
/**
 * データソースを取得する
 * 
 * @param string $configKeyName
 * @param array $dbConfig
 * @return DataSource
 * @access public
 */
	function &_getDataSource($dbConfigKeyName = 'baser', $dbConfig = null) {

		$sources = ConnectionManager::sourceList();
		if($dbConfig) {
			if(in_array($dbConfigKeyName, $sources)) {
				$db =& ConnectionManager::getDataSource($dbConfigKeyName);
			}else {
				$db =& ConnectionManager::create($dbConfigKeyName, $dbConfig);
			}
		} else {
			$db =& ConnectionManager::getDataSource($dbConfigKeyName);
		}
		return $db;
		
	}
/**
 * テーマを配置する
 *
 * @param string $theme
 * @return boolean
 * @access public
 */
	function deployTheme($theme = 'demo') {

		$targetPath = WWW_ROOT.'themed'.DS.$theme;
		$sourcePath = BASER_CONFIGS.'theme'.DS.$theme;
		$folder = new Folder();
		$folder->delete($targetPath);
		if($folder->copy(array('to'=>$targetPath,'from'=>$sourcePath,'mode'=>0777,'skip'=>array('_notes')))) {
			if($folder->create($targetPath.DS.'pages',0777)) {
				return true;
			} else {
				return false;
			}
		} else {
			return false;
		}

	}
	
}
?>