<?php
/* SVN FILE: $Id$ */
/**
 * データベースリストアクラス
 *
 * PHP versions 4 and 5
 *
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://www.e-catchup.jp
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			MIT Lisense
 */
class DbRestore {
/**
 * データベースタイプ
 * @var string
 * @access private
 */
	var $_dbType = null;
/**
 * データベース名
 * @var string
 * @access private
 */
	var $_dbName = null;
/**
 * ホスト名
 * @var string
 * @access private
 */
	var $_dbHost = null;
/**
 * ユーザー名
 * @var string
 * @access private
 */
	var $_dbUser = null;
/**
 * パスワード
 * @var string
 * @access private
 */
	var $_dbPassword = null;
/**
 * ポート
 * @var string
 */
	var $_dbPort = null;
/**
 * DBリンク
 * @var resource
 * @access private
 */
	var $_dbLink = false;
/**
 * 初期状態のテーブル一覧
 * @var array
 * @access private
 */
	var $_initialTables = array();
/**
 * CREATE TABLE は存在しない場合のみに実行する
 * @var boolean
 */
	var $creatingIfNotExists = true;
/**
 * コンストラクタ
 * @param string $db データベース名
 * @param string $filepath ファイルパス
 */
	function DbRestore ($dbType) {

		$this->_dbType = $dbType;

	}
/**
 * データベースに接続する
 * @param string $dbName
 * @param string $dbHost
 * @param string $dbUser
 * @param string $dbPassword
 * @return boolean
 */
	function connect ($dbName, $dbHost = null, $dbUser = null, $dbPassword = null,$dbPort = null) {

		if ($dbName) $this->_dbName = $dbName;
		if ($dbHost) $this->_dbHost = $dbHost;
		if ($dbUser) $this->_dbUser = $dbUser;
		if ($dbPort) $this->_dbPort = $dbPort;
		if ($dbPassword) $this->_dbPassword = $dbPassword;

		switch($this->_dbType) {
			case 'mysql':
				if($this->_dbPort) {
					$host = $this->_dbHost . ':' . $this->_dbPort;
				}else {
					$host = $this->_dbHost;
				}
				$this->_dbLink = mysql_connect($host, $this->_dbUser, $this->_dbPassword);
				if ($this->_dbLink) {
					$ret = !@mysql_select_db($this->_dbName);
				}
				break;

			case 'postgres':
				$this->_dbLink = pg_connect('host='.$this->_dbHost.' port='.$this->_dbPort.' user='.$this->_dbUser.' dbname='.$this->_dbName.' password='.$this->_dbPassword);
				break;

			case 'sqlite':
				$this->_dbLink = sqlite_open($this->_dbName, 0666, $sqliteerror);
				break;

			case 'sqlite3':
				$this->_dbLink = new PDO('sqlite:'.$dbName,'','');
				break;

		}

		// 初期状態のテーブル一覧をスナップショットとして保持しておく
		$this->_initialTables = $this->_getTables();

		return $this->_dbLink;

	}
/**
 * レストアを実行する
 * @param string $filename ファイル名
 * @return boolean
 */
	function doRestore ($inputFilename = null) {

		if (!$this->_dbLink || !file_exists($inputFilename) ) {
			return false;
		}

		$templine = '';
		// ファイル読み込み
		$lines = file($inputFilename);

		$error = false;
		foreach ($lines as $line) {
			// コメントをスキップ
			if (substr($line, 0, 2) == '--' || $line == '')
				continue;

			// 行を追加
			$templine .= $line;
			// 行末にセミコロンがある場合はクエリの終わり
			if (substr(trim($line), -1, 1) == ';') {
				// クエリ実行
				$this->_query($templine) or $error = true;
				$templine = '';
			}

		}
		return !$error;

	}
/**
 * クエリを実行する
 * @param string $sql
 * @return mixed
 */
	function _query ($sql) {

		/* 既に存在するテーブルへの処理はスルーする */
		if($this->creatingIfNotExists &&
				(preg_match("/CREATE\s+?TABLE\s+?[a-z]*?([`a-z0-9_\s]*?)\(/is",$sql,$matches) ||
						preg_match("/CREATE\s+?TABLE([`a-z0-9_\s]*?)\(/is",$sql,$matches) ||
						preg_match("/INSERT\s+?INTO([`a-z0-9_\s]*?)\(/is",$sql,$matches))) {
			$table = str_replace("`","",trim($matches[1]));
			if(in_array($table,$this->_initialTables)) {
				return true;
			}
		}

		$ret = true;

		switch ($this->_dbType) {
			case 'mysql':
				$ret = mysql_query($sql);
				//if (!$ret) print(mysql_error());
				break;

			case 'postgres':
				$ret = pg_query($this->_dbLink,$sql);
				//if (!$ret) print(pg_last_error());
				break;

			case 'sqlite':
				$ret = sqlite_query($this->_dbLink, $sql, SQLITE_BOTH, $sqliteerror);
				//if (!$ret) print($sqliteerror);
				break;

			case 'sqlite3':

				$ret = $this->_dbLink->query($sql);

		}
		return $ret;

	}
/**
 * データベースの一覧を配列で取得する
 * @return array
 */
	function _getTables() {

		$tables = array();

		switch ($this->_dbType) {
			case 'mysql':
				$rs = mysql_query("SHOW TABLES;", $this->_dbLink);
				while($table=mysql_fetch_row($rs)) {
					$tables[] = $table[0];
				}
				break;
			case 'postgres':
				$tables = $this->pg_list_tables($this->_dbLink);
				break;
			case 'sqlite':  // TODO 未実装
				break;

			case 'sqlite3':
				$sth = $this->_dbLink->query("SELECT name FROM sqlite_master WHERE type='table' ORDER BY name;");
				$result = $sth->fetchAll();

				foreach($result as $table) {
					$tables[] = $table[0];
				}
				break;
		}

		return $tables;

	}
/**
 * PostgreSQLのテーブル一覧を取得する
 * @param link $db
 * @return array
 */
	function pg_list_tables($db) {
		$query = "SELECT    c.relname as \"Name\", ".
				"CASE c.relkind   WHEN 'r' THEN 'table' ".
				"WHEN 'v' THEN 'view' ".
				"WHEN 'i' THEN 'index' ".
				"WHEN 'S' THEN 'sequence' ".
				"WHEN 's' THEN 'spectial' END as \"Type\",".
				"u.usename as \"Owner\" ".
				"FROM     pg_class as c ".
				"LEFT JOIN pg_user as u ".
				"ON       c.relowner = u.usesysid ".
				"WHERE    c.relkind IN ('r','v','S','') AND ".
				"c.relname !~ '^pg_' ORDER BY 1;";
		$ret = pg_query($this->_dbLink,$query);
		$tables = array();
		while($row = pg_fetch_array($ret)) {
			if($row['Name']=='table') {
				$tables[] = $row['Name'];
			}
		}
		return $tables;
	}
}
