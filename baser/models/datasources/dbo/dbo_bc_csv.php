<?php
/* SVN FILE: $Id$ */
/**
 * CSV DBO Driver
 *
 * SQLベースでCSVファイルに読み書きをさせる為のドライバー
 *
 * ・dbo_datasourcesによって、一旦SQL文に変換された文字列をqueryDataとして復元した上で処理を行う。
 * ・復元したqueryDataは、CSVファイルを処理しやすいように独自拡張、仕様変更している。
 * ・機能として追加できていないものは空メソッドとして、CakeErrorを発生させる。
 * ・Order By は１フィールドのみ対応
 * ・アソシエイションは未実装
 *
 * [ CSV ファイル 仕様 ]
 * ・カンマは[\,]でエスケープする
 * ・ダブルコーテーションは、[""]でエスケープする（Cassavaは自動でエスケープしてくれる）
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
/**
 * CSV DBO Driver
 *
 * @package baser.models.datasources.dbo
 */
class DboBcCsv extends DboSource {
/**
 * ドライバーの説明文
 *
 * @var string
 * @access public
 */
	var $description = "CSV DBO Driver";
/**
 * 開始クォート
 * TODO 空文字でいいかも
 *
 * @var string
 * @access public
 */
	var $startQuote = "`";
/**
 * 終了クォート
 * TODO 空文字でいいかも
 *
 * @var string
 * @access public
 */
	var $endQuote = "`";
/**
 * DBエンコーディング
 *
 * @var string
 * @access public
 */
	var $dbEncoding = "SJIS";
/**
 * アプリエンコーディング
 *
 * @var string
 * @access public
 */
	var $appEncoding = "UTF-8";
/**
 * コネクション
 *
 * @var array
 * @access public
 */
	var $connection = array();
/**
 * 接続状態
 *
 * @var boolean
 * @access public
 */
	var $connected = false;
/**
 * CSVファイル名（フルパス）
 *
 * @var string
 * @access public
 */
	var $csvName = '';
/**
 * 最後に追加されたID
 *
 * @var string
 * @access protected
 */
	var $_lastInsertId = '';
/**
 * 基本SQLコマンドの一覧
 * 未実装
 *
 * @var array
 * @access protected
 */
	var $_command = array();
	/*var $_commands = array(
		'begin'    => 'START TRANSACTION',
		'commit'   => 'COMMIT',
		'rollback' => 'ROLLBACK'
		);*/
/**
 * resultTable
 *
 * @var string
 * @access	private
 */
	var $__resultModelName = 0;
/**
 * CSVドライバの基本設定
 *
 * @var array
 * @access	protected
 */
	var $_baseConfig = array(
			'database' => 'cake'
	);
/**
 * column definition
 *
 * @var array
 * @access public
 */
	var $columns = array(
			'primary_key' => array('name' => 'NOT NULL AUTO_INCREMENT'),
			'string' => array('name' => 'varchar', 'limit' => '255'),
			'text' => array('name' => 'text'),
			'integer' => array('name' => 'int', 'limit' => '11', 'formatter' => 'intval'),
			'float' => array('name' => 'float', 'formatter' => 'floatval'),
			'datetime' => array('name' => 'datetime', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
			'timestamp' => array('name' => 'timestamp', 'format' => 'Y-m-d H:i:s', 'formatter' => 'date'),
			'time' => array('name' => 'time', 'format' => 'H:i:s', 'formatter' => 'date'),
			'date' => array('name' => 'date', 'format' => 'Y-m-d', 'formatter' => 'date'),
			'binary' => array('name' => 'blob'),
			'boolean' => array('name' => 'tinyint', 'limit' => '1')
	);
/**
 * コンストラクタ
 *
 * @param 	array 接続設定
 * @param boolean 自動接続の有無
 * @return void
 * @access private
 */
	function __construct($config = null, $autoConnect = true) {

		// TODO 現在の仕様として、$connected は、配列にしてしまっているので、
		// 次の処理を行うと処理がうまくいかなくなってしまう。
		// 配列の接続データは別のプロパティに持たせるようにした方が？
		/*if($autoConnect){
			$folder = new Folder();
			$this->connected = $folder->create($config['database']);
		}*/
		parent::__construct($config,false);
		$this->appEncoding = Configure::read('App.encoding');

	}
/**
 * The "R" in CRUD
 * TODO 改修要
 *
 * @param 	Model $model
 * @param 	array $queryData
 * @param 	integer アソシエーションの深さ
 * @return array 結果セット
 * @access public
 */
	function read(&$model, $queryData = array(), $recursive = null) {

		// DB接続
		if(!$this->connect($model,false)) {
			return false;
		}
		$queryData = $this->__scrubQueryData($queryData);
		$null = null;
		$array = array();
		$linkedModels = array();
		$this->__bypass = false;
		$this->__booleans = array();

		if ($recursive === null && isset($queryData['recursive'])) {
			$recursive = $queryData['recursive'];
		}

		if (!is_null($recursive)) {
			$_recursive = $model->recursive;
			$model->recursive = $recursive;
		}

		if (!empty($queryData['fields'])) {
			$this->__bypass = true;
			$queryData['fields'] = $this->fields($model, null, $queryData['fields']);
		} else {
			$queryData['fields'] = $this->fields($model);	// フィールド取得
		}
		
		// 全てのフィールドを取得
		$this->_loadCsvFields($model);

		foreach ($model->__associations as $type) {
			foreach ($model->{$type} as $assoc => $assocData) {
				if ($model->recursive > -1) {
					$linkModel =& $model->{$assoc};
					$external = isset($assocData['external']);
					// ここはいらんかも
					if ($model->useDbConfig == $linkModel->useDbConfig) {
						if (true === $this->generateAssociationQuery($model, $linkModel, $type, $assoc, $assocData, $queryData, $external, $null)) {
							$linkedModels[] = $type . '/' . $assoc;
						}
					}
				}
			}
		}

		$query = $this->generateAssociationQuery($model, $null, null, null, null, $queryData, false, $null); // SQL生成
		$resultSet = $this->fetchAll($query, $model->cacheQueries, $model->alias);	// SQL実行


		if ($resultSet === false) {
			$model->onError();
			$this->disconnect($model->tablePrefix.$model->table);
			return false;
		}

		$filtered = $this->__filterResults($resultSet, $model);

		if ($model->recursive > 0) {
			foreach ($model->__associations as $type) {
				foreach ($model->{$type} as $assoc => $assocData) {

					$linkModel =& $model->{$assoc};

					$db =& ConnectionManager::getDataSource($linkModel->useDbConfig);

					if (isset($db)) {
						$stack = array($assoc);
						$db->queryAssociation($model, $linkModel, $type, $assoc, $assocData, $array, true, $resultSet, $model->recursive - 1, $stack);
						unset($db);
					}
				}
			}
			$this->__filterResults($resultSet, $model, $filtered);
		}

		if (!is_null($recursive)) {
			$model->recursive = $_recursive;
		}

		$this->disconnect($model->tablePrefix.$model->table);
		return $resultSet;
		
	}
/**
 * The "C" in CRUD
 *
 * @param Model $model
 * @param array フィールドリスト
 * @param array 値リスト
 * @return boolean Success
 * @access public
 */
	function create(&$model, $fields = null, $values = null) {

		// DB接続
		if(!$this->connect($model,true)) {
			return false;
		}

		// 全てのフィールドを取得
		$this->_loadCsvFields($model);

		$id = null;

		if ($fields == null) {
			unset($fields, $values);
			$fields = array_keys($model->data);
			$values = array_values($model->data);
		}
		$count = count($fields);

		for ($i = 0; $i < $count; $i++) {
			$valueInsert[] = $this->value($values[$i], $model->getColumnType($fields[$i]));
		}
		for ($i = 0; $i < $count; $i++) {
			$fieldInsert[] = $this->name($fields[$i]);
			if ($fields[$i] == $model->primaryKey) {
				$id = $values[$i];
			}
		}

		if ($this->execute('INSERT INTO ' . $this->fullTableName($model) . ' (' . join(',', $fieldInsert). ') VALUES (' . join(',', $valueInsert) . ')')) {
			if (empty($id)) {
				$id = $this->lastInsertId($this->fullTableName($model, false), $model->primaryKey);
			}
			$model->setInsertID($id);
			$model->id = $id;
			$this->disconnect($model->tablePrefix.$model->table);
			return true;
		} else {
			$model->onError();
			$this->disconnect($model->tablePrefix.$model->table);
			return false;
		}
		
	}
/**
 * The "U" in CRUD
 *
 * @param Model $model
 * @param array フィールドリスト
 * @param array 値リスト
 * @param mixed 条件
 * @return boolean
 * @access public
 */
	function update(&$model, $fields = array(), $values = null, $conditions = null) {

		// DB接続
		if(!$this->connect($model,true)) {
			return false;
		}

		$this->_loadCsvFields($model);

		if ($values == null) {
			$combined = $fields;
		} else {
			$combined = array_combine($fields, $values);
		}

		$fields = $this->_prepareUpdateFields($model, $combined, empty($conditions), !empty($conditions));
		$fields = join(', ', $fields);
		$table = $this->fullTableName($model);
		$alias = $this->name($model->alias);
		$joins = implode(' ', $this->_getJoins($model));

		$conditions = $this->conditions($this->defaultConditions($model, $conditions, $alias), true, true, $model);

		if ($conditions === false) {
			$this->disconnect($model->tablePrefix.$model->table);
			return false;
		}

		if (!$this->execute($this->renderStatement('update', compact('table', 'alias', 'joins', 'fields', 'conditions')))) {
			$model->onError();
			$this->disconnect($model->tablePrefix.$model->table);
			return false;
		}
		$this->disconnect($model->tablePrefix.$model->table);
		return true;
		
	}
/**
 * The "D" in CRUD
 *
 * @param Model $model
 * @param mixed 条件
 * @return boolean Success
 * @access public
 */
	function delete(&$model, $conditions = null) {

		// DB接続
		if(!$this->connect($model,true)) {
			return false;
		}

		$this->_loadCsvFields($model);

		$alias = $this->name($model->alias);
		$table = $this->fullTableName($model);
		$joins = implode(' ', $this->_getJoins($model));

		if (empty($conditions)) {
			$alias = $joins = false;
		}
		$conditions = $this->conditions($this->defaultConditions($model, $conditions, $alias), true, true, $model);

		if ($conditions === false) {
			$this->disconnect($model->tablePrefix.$model->table);
			return false;
		}

		if ($this->execute($this->renderStatement('delete', compact('alias', 'table', 'joins', 'conditions'))) === false) {
			$model->onError();
			$this->disconnect($model->tablePrefix.$model->table);
			return false;
		}

		$this->disconnect($model->tablePrefix.$model->table);
		return true;
	}
/**
 * CSVファイルに接続する
 *
 * @param model モデル
 * @param boolean CSVファイルロック有無
 * @return boolean 接続できた場合は True 、できなかった場合は False
 * @access public
 */
	function connect(&$model,$lock=true) {

		$config = $this->config;
		$tableName = $this->fullTableName($model,false);

		if($this->isConnected($tableName)) {
			return true;
		}

		$this->connected[$tableName] = false;

		if(!$this->_connect($tableName,$lock)) {

			// 接続が見つからない場合はエラー
			//die (__("DboCsv::connect : Can't find Connection : ".$model->tablePrefix.$model->table));
			$this->cakeError('missingConnection', array(array('className' => $model->alias)));
		}else {
			return true;
		}

	}
/**
 * Sets the database encoding
 *
 * @param string $enc Database encoding
 * @return boolean
 * @access public
 */
	function setEncoding($enc) {
		
		$this->dbEncoding = $this->_dbEncToPhp($enc);
		return true;
		
	}
/**
 * Sets the database encoding
 *
 * @param string $enc Database encoding
 * @return mixed
 * @access public
 */
	function getEncoding() {
		
		return $this->_phpEncToDb($this->dbEncoding);
		
	}
/**
 * Reconnects to database server with optional new settings
 * CSVの場合は切断するだけ
 * 
 * @param array $config An array defining the new configuration settings
 * @return boolean True on success, false on failure
 * @access public
 */
	function reconnect($config = array()) {
		
		$this->disconnect();
		$this->setConfig($config);
		$this->_sources = null;
		
	}
/**
 * 接続処理
 *
 * @param string $tableName
 * @param boolean $lock
 * @param boolean $force
 * @return mixed ファイルポインタ / false
 * @access protected
 */
	function _connect($tableName, $lock = true, $force = false) {

		if(!empty($this->connection[$tableName])){
			return $this->connection[$tableName];
		}

		$config = $this->config;
		// CSVファイルのパスを取得
		$this->csvName[$tableName] = $config['database'].DS.$tableName.'.csv';

		if(file_exists($this->csvName[$tableName]) || $force) {
			if($lock) {
				$this->connection[$tableName] = $this->csvConnectByLocked($this->csvName[$tableName]);
			}else {
				$this->connection[$tableName] = $this->csvConnect($this->csvName[$tableName]);
			}
		}
		if($this->connection[$tableName] !== false) {
			$this->connected[$tableName] = true;
		}
		if (!empty($config['encoding'])) {
			$this->setEncoding($config['encoding']);
		}
		return $this->connected[$tableName];
		
	}
/**
 * CSVファイルのファイルリソースを開放する
 * テーブル名の指定がない場合は全て開放する
 * 
 * @param string テーブル名
 * @return boolean 開放できたら True を返す
 * @access public
 */
	function disconnect($tableName = null) {

		if($tableName) {
			if(empty($this->connection[$tableName])) {
				// 接続がない場合は既に切断されているとみなしてtrueを返す
				return true;
			}else {
				if($this->csvCloseByLocked($this->connection[$tableName])) {
					unset($this->connected[$tableName]);
					unset($this->csvName[$tableName]);
					return true;
				}else {
					return false;
				}
			}
		}else {
			if($this->csvCloseByLocked($this->connection)) {
				unset($this->connected);
				unset($this->csvName);
				return true;
			}else {
				return false;
			}
		}

	}
/**
 * データベースに接続できているかチェックする
 *
 * @param string テーブル名
 * @return boolean True if the database is connected, else false
 * @access public
 */
	function isConnected($tableName = null) {

		if(!empty($this->connected)) {
			if($tableName && !empty($this->connected[$tableName])) {
				return $this->connected[$tableName];
			}else if(!empty($this->connected[0])) {
				return $this->connected[0];
			}else {
				return false;
			}
		}else {
			return false;
		}

	}
/**
 * CSVファイルを開く
 *
 * @param string CSVファイルのパス
 * @return stream CSVファイルへのポインタ
 * @access public
 */
	function csvConnect($file) {

		// ファイルを開く
		$fp = fopen($file,'r');
		return $fp;

	}
/**
 * ロック状態でCSVファイルを開く
 * 開く前にバックアップを生成する
 *
 * @param string CSVファイルのパス
 * @return stream CSVファイルへのポインタ
 * @access public
 */
	function csvConnectByLocked($file) {

		/* 念の為バックアップ */
		// TODO すぐに上書きされてしまうので意味がないかも
		// 上書きしないように一意の名称でバックアップをとるとゴミが溜まりすぎる
		if(!is_dir(TMP."csv")) {
			mkdir(TMP.'csv');
			chmod(TMP.'csv', 0777);
		}
		if(file_exists($file)) {
			copy($file, TMP."csv".DS.basename($file).".bak");
			chmod(TMP."csv".DS.basename($file).".bak", 0666);
		}

		// ファイルを開く
		$fp = fopen($file,'ab+');

		if(!$fp) {
			return false;
		}

		//バッファを0に指定（排他制御の保証）
		stream_set_write_buffer($fp,0);
		//ファイルのロック
		flock($fp, LOCK_EX);

		return $fp;

	}
/**
 * ロック状態のCSVファイルを解除した上で開放する（配列対応）
 * @param mixid stream OR array
 * @return void 開放に成功した場合には true を返す
 * @access public
 */
	function csvCloseByLocked(&$fp) {
		
		if(is_array($fp)) {
			$ret = true;
			foreach($fp as $key => $value) {
				if(isset($fp[$key]) && !$this->__csvCloseByLocked($fp[$key])) {
					$ret = false;
				}
			}
			return $ret;
		}else {
			return $this->__csvCloseByLocked($fp);
		}
		
	}
/**
 * ロック状態のCSVファイルを解除した上で開放する
 * @param stream ファイルストリーム
 * @return void 開放に成功した場合には true を返す
 * @access private
 */
	function __csvCloseByLocked(&$fp) {
		
		$ret = false;
		if($fp) {
			//ロックの開放
			flock($fp, LOCK_UN);
			//ファイルのクローズ
			$ret = fclose($fp);
			$fp = null;
		}
		return $ret;
		
	}
/**
 * 与えられたSQLステートメントを実行する
 *
 * @param 	string SQL statement
 * @return mixed 配列の結果セットまたは、true/false
 * @access protected
 */
	function _execute($sql) {

		return $this->csvQuery($sql);

	}
/**
 * CSVデータの操作を行う
 *
 * @param string $sql SQL statement
 * @return mixed 配列の結果セットまたは、true/false
 * @access public
 */
	function csvQuery($sql) {

		// SQL文を解析して、CSV操作用のクエリデータを生成する
		$this->__resultModelName = 0;
		$queryData = $this->parseSql($sql);
		if(isset($queryData['crud'])) {
			switch ($queryData['crud']) {

				case "create":
					$ret = $this->createCsv($queryData);
					break;
				case "read":
					$ret = $this->readCsv($queryData);
					break;
				case "update":
					$ret = $this->updateCsv($queryData);
					break;
				case "delete":
					$ret = $this->deleteCsv($queryData);
					break;
				case "build":
					$ret = $this->buildCsv($queryData);
					break;
				case "drop":
					$ret = $this->dropCsv($queryData);
					break;
				default:
					$ret = false;
			}
		}else {
			$ret = false;
		}
		return $ret;

	}
/**
 * CSVデータを読み込む
 *
 * @param array $queryData
 * @return mixed array / false result
 * @access public
 */
	function readCsv($queryData) {

		$queryData = am($queryData,array('option'=>''));

		if(preg_match("/^COUNT\(\*\)\sAS\scount$/s",trim($queryData['fields'][0]))) {
			/* COUNTフィールドの確認 */
			$queryData['fields'] = null;
			$queryData['option'] = 'count';
		}elseif(preg_match("/^MAX\((.+?)\)\sAS\s(.*?)$/s",trim($queryData['fields'][0]),$matches)) {
			/* MAXフィールドの確認（１フィールドのみ対応） */
			$queryData['fields'] = null;
			$maxField = $matches[1];
			$maxAsField = $matches[2];
			$queryData['option'] = 'max';
		}

		/* CSVファイルを配列として読み込む */
		// TODO ここでは、全データを読み込む仕様となっているので大量のデータを扱う場合、メモリに負荷がかかってしまう。
		// 並び替えを実行した上で、指定件数を取り出すという要件を実現する為、こういう仕様となっている。
		// 何か解決策があれば・・・
		if(empty($queryData['conditions'])) {
			$queryData['conditions'] = null;
		}
		$records = $this->_readCsvFile($queryData['tableName'],$queryData['conditions']);

		/* ソート処理（１フィールドのみ対応） */
		if(!empty($queryData['order'][0])) {
			list($sortField,$direct) = explode(" ",$queryData['order'][0]);
			qsort($records, 0, count($records)-1, $sortField,strtoupper($direct));
		}

		/* ページ指定がある場合は、取得開始件数を計算 */
		if($queryData['page']) {
			$begin = ($queryData['page'] - 1) * $queryData['limit'] + 1;
		}

		/* データのフィルタリング */
		$count=0;
		$matchCount=0;
		$maxValue = 0;
		if($records) {
			foreach($records as $record) {

				$matchCount++;
				if(isset($begin) && $matchCount < $begin) {
					continue;
				}

				if($queryData['option'] == 'max') {
					if($record[$maxField] > $maxValue) {
						$maxValue = $record[$maxField];
					}
					continue;
				}

				// フィールド指定がある場合は指定されたフィールドのみ取得
				if($queryData['fields']) {
					foreach($queryData['fields'] as $field) {
						if(!empty($record[$field])) {
							$result[$field] = $record[$field];
						}else {
							$result[$field] = '';
						}
					}
					$results[] = $result;
				}else {
					$results[] = $record;
				}

				$count++;
				// 件数制限がある場合は、件数を超えた時点で抜ける
				if($queryData['limit'] && $count>=$queryData['limit'])
					break;

			}

		}

		$this->_count = $count;

		// カウントオプションの場合は件数を返す
		if(!empty($queryData['option']) && $queryData['option'] == 'count') {
			return array(0=>array('count'=>$this->_count));
		}

		// MAXオプションの場合は指定フィールドの最大値を返す
		if(!empty($queryData['option']) && $queryData['option'] == 'max') {
			return array(0=>array($maxAsField=>$maxValue));
		}

		if(!empty($queryData["className"])){
			$this->__resultModelName = $queryData["className"];
		}

		if(isset($results)) {
			return $results;
		}else {
			return  array();
		}

	}
/**
 * CSVファイルにレコードを追加する
 *
 * @param array クエリデータ
 * @return mixed
 * @access public
 */
	function createCsv($queryData) {

		if(!$this->_connect($queryData['tableName'])){
			return false;
		}

		// 追加対象のデータを絞り込む
		$_records = $queryData['records'];
		$records = array();
		$id = $this->_getMaxId($queryData['tableName']);
		foreach($_records as $record) {
			if(empty($record['id'])) {
				// 主キーがない場合のauto処理
				$id++;
				$record['id'] = '"'.$id.'"';
				$records[] = $record;
				$this->_lastInsertId = $id;
				continue;
			}else{
				// ID重複チェック
				if(!$this->__checkDuplicateId($queryData['tableName'], $record['id'])) {
					$records[] = $record;
					continue;
				}
			}
		}

		// カラムをテーブル情報どおりに並べる
		$_records = $records;
		$records = array();
		foreach($_records as $record) {
			foreach($this->_csvFields  as $field) {
				if(isset($record[$field])) {
					$_record[$field]=$record[$field];
				}else {
					$_record[$field]=null;
				}
			}
			$records[] = $_record;
		}

		// CSVファイルを全て読み込む
		rewind($this->connection[$queryData['tableName']]);
		$csv = fread($this->connection[$queryData['tableName']],filesize($this->csvName[$queryData['tableName']]));

		// 最後の行に改行がなかったら改行を追加する
		// もっといい方法があれば・・・
		if(!preg_match("/\n$/s",$csv)) {
			$csv .= "\n";
		}

		foreach ($records as $record) {
			$newRecord = implode(",", $record)."\n";
			// 新しいレコードを追加
			if($this->dbEncoding != $this->appEncoding) {
				$newRecord = mb_convert_encoding($newRecord, $this->dbEncoding, $this->appEncoding);
			}
			$csv .= $newRecord;
		}
		
		// ファイルサイズを0に
		ftruncate($this->connection[$queryData['tableName']],0);

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$queryData['tableName']], $csv);

		$this->disconnect($queryData['tableName']);

		return $ret;

	}
/**
 * IDの重複チェックを行う
 * 
 * @param string $table
 * @param int $id
 * @return boolean
 * @access private
 */
	function __checkDuplicateId($table, $id) {
		
		$queryData['crud'] = 'read';
		$queryData['className'] = Inflector::classify(str_replace($this->config['prefix'], '', $table));
		$queryData['fields'] = array('id');
		$queryData['tableName'] = $table;
		$queryData['limit'] = 1;
		$queryData['page'] = 1;
		$queryData['conditions'] = 'if ($record[\'id\']=='.$id.') return true;';
		if($this->readCsv($queryData)) {
			$result = true;
		} else {
			$result = false;
		}

		// フィールド情報を読み込み直す
		$this->_loadCsvFields($queryData['tableName']);
		
		return $result;
		
	}
/**
 * CSVテーブルを生成する
 *
 * @param array $queryData
 * @return mixed boolean / int
 * @access public
 */
	function buildCsv($queryData) {

		if(file_exists($this->config['database'].DS.$queryData['tableName'].'.csv')){
			return false;
		}
		$this->_connect($queryData['tableName'], true, true);
		$head = $this->_getCsvHead($queryData['fields']);
		if($this->appEncoding != $this->dbEncoding) {
			$head = mb_convert_encoding($head, $this->dbEncoding, $this->appEncoding);
		}
		
		$result = fwrite($this->connection[$queryData['tableName']], $head);
		if($result) {
			chmod($this->csvName[$queryData['tableName']], 0666);
			return true;
		}
		return false;

	}
/**
 * CSVテーブルを削除する
 *
 * @param array $queryData
 * @return mixed fals / string
 * @access public
 */
	function dropCsv($queryData) {

		$path = $this->config['database'].DS.$queryData['tableName'].'.csv';
		if(!file_exists($path)){
			return false;
		}
		$this->disconnect($queryData['tableName']);
		return @unlink($path);

	}
/**
 * CSVファイルを更新する
 *
 * @param array クエリデータ
 * @return mixed
 * @access public
 */
	function updateCsv($queryData) {

		$records = $this->_readCsvFile($queryData['tableName']);

		// ヘッダーの生成
		$head = $this->_getCsvHead();

		// データの生成
		$body="";
		if($records) {
			foreach($records as $key => $record) {
				// 更新対象のレコードのみ更新
				if(eval($queryData['conditions'])) {
					$record = $this->_convertRecord($record);
					// 更新対象のフィールドのみ更新
					foreach($queryData['values'] as $key => $field) {
						// TODO TreeBehaviourが演算を使うので対応する為の苦肉の策→他の方法があれば変更する
						if(preg_match('/^"\{'.$key.'\}\s*([\+\-\/\*]+)\s*([\-0-9]+)"$/is', trim($field), $matches)) {
							eval('$field = '.$record[$key].' '.$matches[1].' '.$matches[2].';');
						}
						if(isset($record[$key])){
							$record[$key] = $field;
						}else{
							trigger_error('フィールド： '.$key.' は存在しません。', E_USER_WARNING);
							return false;
						}
					}
				}else {
					// 既存データをCSV用にコンバートする
					$record = $this->_convertRecord($record);
				}
				$body .= implode(",",$record)."\n";
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$queryData['tableName']],0);

		$csvData = $head.$body;
		if($this->dbEncoding != $this->appEncoding) {
			$csvData = mb_convert_encoding($csvData, $this->dbEncoding, $this->appEncoding);
		}

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$queryData['tableName']], $csvData);

		return $ret;

	}
/**
 * CSVファイルよりレコードを削除する
 *
 * @param array クエリデータ
 * @return boolean true/false
 * @access public
 */
	function deleteCsv($queryData) {

		$records = $this->_readCsvFile($queryData['tableName']);

		// ヘッダーの生成
		$head = $this->_getCsvHead();

		// ボディを生成
		$body = '';
		if($records) {
			foreach($records as $key => $record) {
				if(!eval($queryData['conditions'])) {
					$record = $this->_convertRecord($record);
					$body .= implode(",",$record)."\n";
				}
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$queryData['tableName']],0);

		$csvData = $head.$body;
		if($this->dbEncoding != $this->appEncoding) {
			$csvData = mb_convert_encoding($csvData, $this->dbEncoding, $this->appEncoding);
		}

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$queryData['tableName']], $csvData);

		return $ret;

	}
/**
 * CSVファイルを配列として読み込む
 *
 * @param string テーブル名
 * @param string 検索条件
 * @return array 配列の結果セット
 * @access protected
 */
	function _readCsvFile($tableName = null, $conditions = null) {

		if($tableName) {
			$index = $tableName;
		}else {
			$index = 0;
		}

		if(!isset($this->connection[$index])) {
			if(!$this->_connect($tableName)) {
				return false;
			}
		}

		$records = null;
		$count=0;

		// ヘッダ取得
		//setlocale(LC_ALL, 'ja_JP.SJIS'); //日本語文字化け対策
		$this->_loadCsvFields($index);

		while(($_record = fgetcsvReg($this->connection[$index], 10240)) !== false) {
			$record = array();
			// 配列の添え字をフィールド名に変換
			foreach($_record as $key => $value) {
				@$record[$this->_csvFields[$key]] = $value;
			}
			// 文字コードを変換
			if($this->dbEncoding != $this->appEncoding) {
				mb_convert_variables($this->appEncoding,$this->dbEncoding,$record);
			}

			// 条件に合致しない場合は取得せず次へ
			if($conditions && !eval($conditions)) {
				continue;
			}
			$records[] = $record;
		}

		rewind($this->connection[$index]);

		return $records;

	}
/**
 * IDの最大値を取得する
 * 
 * @param string $tableName
 * @return int $id
 * @access protecteds
 */
	function _getMaxId($tableName) {

		if($tableName) {
			$index = $tableName;
		}else {
			$index = 0;
		}

		if(!isset($this->connection[$index])) {
			if(!$this->_connect($tableName)) {
				return false;
			}
		}

		$maxId=0;

		// ヘッダ取得
		$this->_loadCsvFields($index);

		$idNum = '';
		foreach($this->_csvFields as $key => $value) {
			if($value == 'id') {
				$idNum = $key;
				break;
			}
		}

		while(($record = fgetcsvReg($this->connection[$index], 10240)) !== false) {
			if($record[$idNum]>=$maxId) {
				$maxId = $record[$idNum];
			}
		}
		return $maxId;

	}
/**
 * CSV用のヘッダを取得する
 *
 * @param array
 * @return string
 * @access protected
 */
	function _getCsvHead($fields = null) {
		
		if(!$fields) {
			$fields = $this->_csvFields;
		}
		$head = "";
		foreach($fields as $field) {
			$head .= "\"".$field . "\",";
		}
		return substr($head,0,strlen($head)-1) . "\n";
		
	}
/**
 * CSV用のフィールドデータに変換する
 *
 * @param string $value
 * @param boolean $dc （ " を "" に変換するか）
 * @return string
 * @access protected
 */
	function _convertField($value,$dc = true) {
		
		if($dc) {
			$value = str_replace('"','""',$value);
		}
		$value = trim(trim($value),"\'");
		$value = str_replace("\\'","'",$value);
		$value = str_replace('{CM}',',',$value);
		$value = '"'.$value.'"';
		return $value;
		
	}
/**
 * CSV用のレコードデータに変換する
 *
 * @param array $record
 * @return array
 */
	function _convertRecord($record) {
		
		foreach($record as $field => $value) {
			$record[$field] = $this->_convertField($value);
		}
		return $record;
		
	}
/**
 * フィールドを追加する
 *
 * @param array $options [ table / column / prefix ]
 * @return boolean
 * @access protected
 */
	function addColumn($options) {

		extract($options);

		if(!isset($table) || !isset($column)) {
			return false;
		}

		if(!isset($field)) {
			if(isset($column['name'])) {
				$field = $column['name'];
			} else {
				return false;
			}
		}

		if(!isset($prefix)){
			$prefix = $this->config['prefix'];
		}

		$table = $prefix . $table;

		// DB接続
		if(!$this->_connect($table, true, true)) {
			return false;
		}

		$this->_loadCsvFields($table);
		
		if($this->_csvFields) {
			if(in_array($field,$this->_csvFields)) {
				// 既に存在するフィールドの場合は falseを返す
				return false;
			}
		}else {
			$this->_csvFields = array();
		}

		$this->_csvFields[] = $field;
		$head = $this->_getCsvHead();

		// 全てのレコードを取得
		$records = $this->_readCsvFile($table);
		$body="";
		if($records) {
			foreach($records as $key => $record) {
				$_record = $this->_convertRecord($record);
				$_record[] = '""';
				$body .= implode(",",$_record)."\n";
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$table],0);

		$csvData = $head.$body;
		if($this->dbEncoding != $this->appEncoding) {
			$csvData = mb_convert_encoding($csvData, $this->dbEncoding, $this->appEncoding);
		}

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$table], $csvData);

		$this->disconnect($table);
		return $ret;

	}
/**
 * フィールドを編集する
 *
 * @param array $options [ table / column / field / prefix ]
 * @return boolean
 * @access protected
 */
	function changeColumn($options) {

		extract($options);

		if(!isset($table) || !isset($column)) {
			return false;
		}

		if(!isset($field)) {
			if(isset($column['name'])){
				$field = $column['name'];
			} else{
				return false;
			}
		}

		if(!isset($prefix)){
			$prefix = $this->config['prefix'];
		}

		$table = $prefix . $table;
		$old = $field;
		if(isset($column['name'])){
			$new = $column['name'];
		}else{
			// CSVは型やサイズがない為、リネーム以外はtrueを返して終了する
			return true;
		}

		// DB接続
		if(!$this->_connect($table, true, true)) {
			return false;
		}

		// 全てのフィールドを取得
		$this->_loadCsvFields($table);
		
		if($this->_csvFields) {
			if(!in_array($old,$this->_csvFields)) {
				return false;
			}
		}else {
			return false;
		}

		// キーを取得
		while ($field = current($this->_csvFields)) {
			if ($field == $old) {
				$key = key($this->_csvFields);
			}
			next($this->_csvFields);
		}

		// ヘッダーの生成
		$this->_csvFields[$key] = $new;
		$head = $this->_getCsvHead();

		// 全てのレコードを取得
		$records = $this->_readCsvFile($table);
		$body="";
		if($records) {
			foreach($records as $key => $record) {
				$_record = $this->_convertRecord($record);
				$body .= implode(",",$_record)."\n";
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$table],0);

		$csvData = $head.$body;
		if($this->dbEncoding != $this->appEncoding) {
			$csvData = mb_convert_encoding($csvData, $this->dbEncoding, $this->appEncoding);
		}

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$table], $csvData);
		$this->disconnect($table);
		return $ret;

	}
/**
 * フィールドを削除する
 *
 * @param array $options [ table / field / prefix ]
 * @return boolean
 * @access protected
 */
	function dropColumn($options) {

		extract($options);

		if(!isset($table) || !isset($field)) {
			return false;
		}

		if(!isset($prefix)){
			$prefix = $this->config['prefix'];
		}

		$table = $prefix . $table;

		// DB接続
		if(!$this->_connect($table, true, true)) {
			return false;
		}

		// 全てのフィールドを取得
		$this->_loadCsvFields($table);
		
		if($this->_csvFields) {
			if(!in_array($field,$this->_csvFields)) {
				return false;
			}
		}else {
			return false;
		}

		// キーを取得
		while ($_field = current($this->_csvFields)) {
			if ($_field == $field) {
				$key = key($this->_csvFields);
			}
			next($this->_csvFields);
		}

		if(!isset($key)) {
			return false;
		}

		// ヘッダーの生成
		unset($this->_csvFields[$key]);
		$head = $this->_getCsvHead();

		// 全てのレコードを取得
		$records = $this->_readCsvFile($table);
		$body="";
		if($records) {
			foreach($records as $key => $record) {
				$_record = $this->_convertRecord($record);
				unset($_record[$field]);
				$body .= implode(",",$_record)."\n";
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$table],0);

		$csvData = $head.$body;
		if($this->dbEncoding != $this->appEncoding) {
			$csvData = mb_convert_encoding($csvData, $this->dbEncoding, $this->appEncoding);
		}

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$table], $csvData);
		$this->disconnect($table);
		return $ret;

	}
/**
 * SQLデータのCSV処理用の解析を行う
 *
 * @param string SQL statement
 * @return array configs
 * @access	public
 */
	function parseSql($sql) {

		$parseData = array('conditions'=>array(),
				'fields'=>array(),
				'joins'=>array(),
				'limit'=>null,
				'offset'=>null,
				'order'=>array(),
				'page'=>null,
				'group'=>array(),
				'recursive'=>null);
		$sql = preg_replace('/;$/', '', $sql);
		$createPattern = "/INSERT INTO[\s]*([^\s]+)[\s]*\(([^\)]+)\)[\s]*VALUES[\s]*\((.+)\)[\s]*$/si";
		$readPattern = "/SELECT(.+)FROM(.+?)(WHERE.+|ORDER\sBY.+|LIMIT.+|)$/si";
		$updatePattern = "/UPDATE[\s]+(.+?)[\s]+SET[\s]+(.+)[\s]+WHERE[\s]+(.+)/si";
		$deletePattern = "/DELETE.+FROM[\s]+(.+)[\s]+WHERE[\s]+(.+)/si"; // deleteAllの場合は、DELETEとFROMの間にクラス名が入る
		$buildPattern = "/CREATE\sTABLE\s([^\s]+)\s*\((.+)\);/si";
		$dropPattern = "/DROP\sTABLE\s+([^\s]+)/si";

		// CREATE
		if(preg_match($createPattern,$sql,$matches)) {
			$parseData['crud'] = 'create';
			$parseData['tableName'] = $this->_parseSqlTableName($matches[1]);
			$parseData = array_merge($parseData,$this->_parseSqlValuesFromCreate($matches[2],$matches[3]));

		// READ
		}elseif(preg_match($readPattern,$sql,$matches)) {
			$parseData['crud'] = 'read';
			$parseData['className'] = $this->_parseSqlClassName($matches[1]);
			$parseData['fields'] = $this->_parseSqlFields($matches[1],$parseData['className']);
			$parseData['tableName'] = $this->_parseSqlTableName($matches[2]);
			//$parseData['conditions'] = $this->_parseSqlCondition($matches[3],$parseData['fields']);
			if(isset($matches[3])){
				$options = $matches[3];
				if(preg_match("/WHERE(.+?)(ORDER\sBY.+|LIMIT.+|)$/s",$options,$matches)) {
					$parseData['conditions'] = $this->_parseSqlCondition($matches[1],$parseData['fields'], $parseData['tableName']);
				}
				if(preg_match("/ORDER\sBY(.+?)(LIMIT.+|)$/s",$options,$matches)) {
					$parseData['order'] = $this->_parseSqlOrder($matches[1]);
				}
				if(preg_match("/LIMIT(.+)$/s",$options,$matches)) {
					$parseData = array_merge($parseData,$this->_parseSqlLimit($matches[1]));
				}
			}

		// UPDATE
		}elseif(preg_match($updatePattern,$sql,$matches)) {

			$parseData['crud'] = 'update';
			$parseData['tableName'] = $this->_parseSqlTableName($matches[1]);
			$parseData = array_merge($parseData,$this->_parseSqlValuesFromUpdate($matches[2]));
			$parseData['conditions'] = $this->_parseSqlCondition($matches[3],$parseData['fields']);

		// DELETE
		}elseif(preg_match($deletePattern,$sql,$matches)) {

			$parseData['crud'] = 'delete';
			$parseData['tableName'] = $this->_parseSqlTableName($matches[1]);
			$parseData['conditions'] = $this->_parseSqlCondition($matches[2],$parseData['fields']);

		// BUILD (CREATE TABLE)
		}elseif(preg_match($buildPattern,$sql,$matches)) {
			$parseData['crud'] = 'build';
			$parseData['tableName'] = $this->_parseSqlTableName($matches[1]);
			$parseData['fields'] = $this->_parseSqlFieldsFromBuild($matches[2]);

		// DROP
		}elseif(preg_match($dropPattern,$sql,$matches)) {
			$parseData['crud'] = 'drop';
			$parseData['tableName'] = $this->_parseSqlTableName($matches[1]);
		}

		return $parseData;

	}
/**
 * SQL文のフィールド名を配列に変換する
 *
 * @param string SQL statement
 * @return array フィールド名リスト
 * @access protected
 */
	function _parseSqlFields($fields,$modelName) {
		$aryFields = explode(",",$fields);
		foreach($aryFields as $key => $field) {
			if(preg_match('/(max|MAX)\((.*?)\)\sAS\s(.*)/s',$field,$matches)) {
				$field = $matches[2];
				if(strpos($field,".")!==false) {
					list($model,$field) = explode(".",$field);
				}
				$field = 'MAX('.$field.') AS '.str_replace('`','',$matches[3]);
			}elseif(preg_match('/(count|COUNT)\((.*?)\)\sAS\s(.*)/s',$field,$matches)) {
				$field = $matches[2];
				if(strpos($field,".")!==false) {
					list($model,$field) = explode(".",$field);
				}
				$field = 'COUNT('.$field.') AS '.str_replace('`','',$matches[3]);
			}else {
				if(strpos($field,".")!==false) {
					list($model,$field) = explode(".",$field);
				}
			}
			if(isset($model)){
				if(trim(str_replace('`','',$model))==$modelName){
					$aryFields[$key] = trim(str_replace("`","",$field));
				}else{
					unset($aryFields[$key]);
				}
			}else{
				$aryFields[$key] = trim(str_replace("`","",$field));
			}
		}
		return $aryFields;

	}
/**
 * resultSet メソッドで利用する為のクラス名を取得する
 *
 * 本来なら、フィールドごとに保持するべきだが、処理速度向上の為、
 * 一つのフィールドにクラス名があれば全てのフィールドに適用するものとする
 * 結果、複数のフィールドでその中にcountやmaxが入っている場合、
 * 他のDBの取得結果とは違うものとなる可能性がある
 * （countやmaxはクラス名を含まないため）
 * 
 * @param array $fields
 * @return string
 * @access protected
 */
	function _parseSqlClassName($fields) {
		
		$model = '';
		$aryFields = explode(",",$fields);
		foreach($aryFields as $field) {
			if(preg_match('/\((.*?)\)\sAS/is', $field, $matches)){
				if(strpos($matches[1],".")!==false){
					list($model,$field) = explode(".",$matches[1]);
					break;
				}
			}else{
				if(strpos($field,".")!==false){
					list($model,$field) = explode(".",$field);
					break;
				}
			}
		}
		return trim(str_replace('`','',$model));
		
	}
/**
 * CREATE TABLE 文のフィールド名を配列に変換する
 *
 * @param 	string SQL statement
 * @return array フィールド名リスト
 * @access protected
 */
	function _parseSqlFieldsFromBuild($sql) {

		$arySql = explode(",",$sql);
		$fields = array();
		foreach($arySql as $key => $value) {
			if(strpos($value,'PRIMARY KEY')===false) {
				if(preg_match('/`([^`]+)`/is',$value,$matches)) {
					$fields[] = $matches[1];
				}
			}
		}
		return $fields;

	}
/**
 * SQL文のフィールド名と値を配列に変換する（INSERT文用）
 *
 * @param 	string SQL statement
 * @param strig $values
 * @return array フィールドリスト
 * @access protected
 */
	function _parseSqlValuesFromCreate($fields,$values) {

		$values = str_replace('), (', '),(', $values);
		if(strpos($values, '),(') !== false) {
			$values = explode('),(', $values);
		} else {
			$values = array($values);
		}
		
		$fields = str_replace("`","",$fields);
		$arrFields = explode(",",$fields);

		$records = array();
		foreach($values as $value) {
			$value = str_replace('\,','{CM}',$value);
			$value = str_replace("\"",'""',$value);
			$arrValues = explode(",",$value);
			$record = array();
			for($i=0;$i<count($arrFields);$i++) {
				$arrFields[$i] = trim($arrFields[$i]);
				$record[$arrFields[$i]] = $this->_convertField($arrValues[$i], false);
			}
			$records[] = $record;
		}

		$parseData['records'] = $records;
		$parseData['fields'] = $arrFields;
		return $parseData;

	}
/**
 * SQL文のフィールド名と値を配列に変換する（UPDATE文用）
 *
 * @param string SQL statement
 * @return array フィールドリスト
 * @access protected
 */
	function _parseSqlValuesFromUpdate($sql) {

		$fields = array();
		$values = array();
		// エスケープされたカンマを一旦変換
		$sql = str_replace('\,','{CM}',$sql);
		$sql = str_replace("\"",'""',$sql);

		$arrSql = explode(",",$sql);
		foreach($arrSql as $field) {
			list($fieldName,$value) = explode("=",$field,2);
			if(strpos($value,$fieldName) !== false) {
				$value = str_replace($fieldName,'{FIELDNAME}',$value);
			}
			if(strpos($fieldName,'.') !== false) {
				list($modelName,$fieldName) = explode('.',$fieldName);
			}
			$fieldName = trim(str_replace("`","",$fieldName));
			$value = str_replace('{FIELDNAME}','{'.$fieldName.'}',$value);
			$values[$fieldName] = $this->_convertField($value,false);
			$fields[] = $fieldName;
		}
		$parseData['values'] = $values;
		$parseData['fields'] = $fields;

		return $parseData;

	}
/**
 * テーブル名を解析する
 * TODO 現時点では単一のみ実装
 *
 * @param string SQL
 * @return string モデル名
 * @access protected
 */
	function _parseSqlTableName($tables) {

		$tables = str_replace("`","",$tables);

		if(strpos($tables,"AS") !== false) {
			list($tableName,$modelName) = explode("AS",$tables);
		}else {
			$tableName = trim($tables);
		}
		return trim($tableName);

	}
/**
 * 検索条件文字列を解析
 *
 * @param string SQL Conditions
 * @param	array フィールドリスト
 * @return string eval用の検索条件
 * @access protected
 */
	function _parseSqlCondition($conditions, $fields, $tableName = '') {

		if(is_array($conditions)) {
			foreach($conditions as $key => $condition) {
				if(!isset($tmpConditions)) {
					$tmpConditions = $key."=='".$condition."'";
				}else {
					$tmpConditions .= " && ".$key."==".$condition;
				}
			}
			$conditions = $tmpConditions;
		}else{
			$conditions = trim(str_replace('WHERE','',$conditions));
		}

		$conditions = preg_replace("/`[^`]+?`\./s","",$conditions);
		$conditions = str_replace("\"","'",$conditions);
		$conditions = str_replace("%","*",$conditions); // TODO LIKEの後の''に囲まれた%のみ*に変換するようにする
		$conditions = preg_replace("/(\s+)and(\s+)/s","$1AND$2",$conditions);
		$conditions = preg_replace("/(\s+)or(\s+)/s","$1OR$2",$conditions);
		$conditions = preg_replace("/(\s+)like(\s+)/s","$1LIKE$2",$conditions);
		$conditions = preg_replace("/(\s+)AND(\s+)/s","$1&&$2",$conditions);
		$conditions = preg_replace("/(\s+)OR(\s+)/s","$1||$2",$conditions);
		$conditions = str_replace('<>','!=',$conditions);
		$conditions = str_replace('IS NULL',"== ''",$conditions);
		$conditions = str_replace('IS NOT NULL',"!= ''",$conditions);
		$conditions = preg_replace("/YEAR\((.*?)\)/si","date('Y',strtotime($1))",$conditions);
		$conditions = preg_replace("/MONTH\((.*?)\)/si","date('m',strtotime($1))",$conditions);
		$conditions = preg_replace("/DAY\((.*?)\)/si","date('d',strtotime($1))",$conditions);
		$conditions = preg_replace('/([^<>!])=+/s','$1==',$conditions);
		$conditions = preg_replace("/([`a-z0-9_]+)\s+NOT\s+LIKE\s+\'(.*?)\'/s","!preg_match('/^'.str_replace(\"*\",\".*\",\"$2\").'$/s',$1)>=1",$conditions);
		$conditions = preg_replace("/([`a-z0-9_]+)\s+LIKE\s+\'(.*?)\'/s","preg_match('/^'.str_replace(\"*\",\".*\",\"$2\").'$/s',$1)>=1",$conditions);

		// BETWEEN（数字のみ対応）
		$conditions = preg_replace("/([`a-z0-9_]+)\s+BETWEEN\s+([0-9]+?)\s+&&\s+([0-9]+?)/s","$1 >= $2 && $1 <= $3",$conditions);

		// IN句
		if(preg_match("/([`a-z0-9_]+?)\sIN\s\((.*?)\)/s",$conditions,$matches)) {
			$fieldName = $matches[1];
			$values = explode(",",$matches[2]);
			$in_conditions = "";
			foreach($values as $value) {
				if($in_conditions) {
					$in_conditions .= " || ".$fieldName . " == " . $value;
				}else {
					$in_conditions = $fieldName . " == " . $value;
				}
			}
			$conditions = preg_replace("/[`a-z0-9_]+?\sIN\s\(.*?\)/s",'('.$in_conditions.')',$conditions);
		}

		// TODO NOT句（２重カッコに対応できていない）
		$befores = array('&&','||','==','!=','>','>=','<','<=','NOT');
		$afters = array('||','&&','!=','==','<=','<','>=','>','');
		if(preg_match("/(NOT\s*?\(.*?\))/s",$conditions,$matches)) {
			$_conditions = $matches[1];
			foreach($befores as $key => $before) {
				$_conditions = str_replace($before,'{'.$key.'}',$_conditions);
			}
			foreach($afters as $key => $after) {
				$_conditions = str_replace('{'.$key.'}',$after,$_conditions);
			}
			$conditions = preg_replace("/NOT\s*?\(.*?\)/s",$_conditions,$conditions);
		}

		if(empty($this->_csvFields) && $tableName) {
			$this->_loadCsvFields($tableName);
		}
		
		if(isset($this->_csvFields)) {
			foreach($this->_csvFields as $fieldName) {
				$conditions = preg_replace("/(^|[^a-z0-9_])`".$fieldName."`([^a-z0-9_]*)/s","$1\$record['".$fieldName."']$2",$conditions);
			}
		}
		
		//$conditions = str_replace("`","",$conditions);
		$conditions = 'if (' . $conditions . ') return true;';

		return $conditions;

	}
/**
 * LIMIT を解析
 *
 * @param string SQL
 * @returnarray offset/limit/page 格納した配列
 * @access protected
 */
	function _parseSqlLimit($limit) {

		$_config = explode(",",$limit);
		if(!empty($_config[1])) {
			$config['offset'] = trim($_config[0]);
			$config['limit'] = trim($_config[1]);
		}else {
			$config['offset'] = 0;
			$config['limit'] = trim($_config[0]);
		}

		$config['page'] = floor(($config['offset']+1)/$config['limit']);
		if(($config['offset']+1)%$config['limit']>0) {
			$config['page']++;
		}
		return $config;

	}
/**
 * ORDER を解析
 * TOOD 現在、Orderを指定できるフィールドは１フィールドのみ
 *
 * @param string SQL Order
 * @return array 並び替え条件リスト
 * @access protected
 */
	function _parseSqlOrder($strOrder) {

		$strOrder = preg_replace("/`[^`]+?`\./s","",$strOrder);
		$strOrder = str_replace("`","",$strOrder);
		$aryOrders = explode(",",$strOrder);
		foreach($aryOrders as $key =>$order) {
			$_aryOrders[]=trim($order);
		}
		return $_aryOrders;

	}
/**
 * テーブルの全てのリストを取得する
 * 
 * @return array Array of tablenames in the database
 */
	function listSources() {

		$cache = parent::listSources();
		if ($cache != null) {
			return $cache;
		}
		$folder = new Folder($this->config['database']);
		$result = $folder->read(true,true);

		if (empty($result[1])) {
			return array();
		} else {
			$tables = array();
			foreach($result[1] as $csv) {
				if(preg_match('/^'.$this->config['prefix'].'[a-z0-9]/', $csv)){
					$tables[] = str_replace('.csv','',$csv);
				}
			}
			parent::listSources($tables);
			return $tables;
		}

	}
/**
 * フィールド情報を取得する
 *
 * @param Model モデル
 * @return array フィールド情報のリスト
 * @access public
 */
	function describe(&$model) {

		$cache = parent::describe($model);
		if ($cache != null) {
			return $cache;
		}

		if(!file_exists($this->config['database'].DS.$this->config['prefix'].$model->useTable.'.csv')){
			return null;
		}

		$fields = false;

		// 接続されていない場合は、一時的に接続してヘッダーを取得
		// （モデルの初期化時など）
		if(empty($this->connected[$model->tablePrefix.$model->table])) {
			$this->connect($model,false);

			if(empty($this->connection[$model->tablePrefix.$model->table])) {
				die (__("DboCsv::describe : Can't find Connection"));
			}

			$cols = fgetcsv($this->connection[$model->tablePrefix.$model->table],10240);
			$this->disconnect($model->tablePrefix.$model->table);
		}else {
			$cols = fgetcsv($this->connection[$model->tablePrefix.$model->table],10240);
			if(!$cols) {
				// TODO 処理を見直す
				// ファイルリソースがあるにも関わらずデータの取得ができない場合がある。（インストール時に再現）
				// 取り急ぎの対応として一旦接続を切って再接続している。
				// ファイルのロック処理？かもしれない。接続を一旦解除しているので他の部分に影響している可能性もある。
				// 追記：接続したままだとロックがかかりっぱなしになるので再度接続を解除する事にした。
				$this->disconnect($model->tablePrefix.$model->table);
				$this->connect($model,false);
				$cols = fgetcsv($this->connection[$model->tablePrefix.$model->table],10240);
				$this->disconnect($model->tablePrefix.$model->table);
			}
		}

		$cols = str_replace("\"","",$cols); // ダブルコーテーションを削除

		if(!$cols) {
			return null;
		}
		foreach ($cols as $column) {
			if($column) {
				if($column == 'created'|| $column == 'modified' || substr($column, strlen($column)-5,5)=="_date") {
					$fields[$column] = array(
							'type'		=> $this->column("datetime"),
							'null'		=> true,
							'default'	=> "",
							'length'	=> $this->length("datetime"),
					);
				}elseif($column == 'id') {
					// CSVの場合、フィールド名 id は主キーで int(4) 固定とする
					$type = 'int(4)';
					$fields[$column] = array(
							'type'		=> $this->column($type),
							'null'		=> false,
							'default'	=> $this->index['PRI'],
							'length'	=> $this->length($type),
					);
				}else {
					$fields[$column] = array(
							'type'		=> $this->column("text"),
							'null'		=> true,
							'default'	=> "",
							'length'	=> $this->length("text"),
					);
				}
				if($column == 'id' && isset($this->index[$column])) {
					$fields[$column]['key']	= $this->index[$column];
				}
			}
		}
		$this->__cacheDescription($this->fullTableName($model, false), $fields);

		return $fields;

	}
/**
 * SQL用にエスケープ処理を行う
 *
 * @param string $data String to be prepared for use in an SQL statement
 * @param string $column The column into which this data will be inserted
 * @param boolean $safe Whether or not numeric data should be handled automagically if no column data is provided
 * @return string Quoted and escaped data
 * @access public
 */
	function value($data, $column = null, $safe = false) {
		
		$parent = parent::value($data, $column, $safe);

		if ($parent != null) {
			return $parent;
		} elseif ($data === null) {
			return 'NULL';
		} elseif ($data === '') {
			return  "''";
		}
		if (empty($column)) {
			$column = $this->introspectType($data);
		}

		switch ($column) {
			case 'boolean':
				return $this->boolean((bool)$data);
				break;
			case 'integer':
			case 'float':
				if ((is_int($data) || is_float($data)) || (
						is_numeric($data) && strpos($data, ',') === false &&
								$data[0] != '0' && strpos($data, 'e') === false
				)) {
					return $data;
				}
			default:
				$data = "'" . $this->escapeString($data) . "'";
				break;
		}
		return $data;
		
	}
/**
 * 文字列のエスケープ処理を行う
 *
 * @param string エスケープ対象データ
 * @return string エスケープ処理を行ったデータ
 * @access public
 */
	function escapeString($value) {

		$value = str_replace("'","\'",$value);
		$value = str_replace(",","\,",$value);
		return $value;

	}
/**
 * Returns a formatted error message from previous database operation.
 * TODO 未サポート
 *
 * @return string Error message with error number
 * @access public
 */
	function lastError() {
		
		/*if (mysql_errno($this->connection)) {
		 return mysql_errno($this->connection).': '.mysql_error($this->connection);
		 }*/
		return null;
		
	}
/**
 * Returns number of affected rows in previous database operation. If no previous operation exists,
 * this returns false.
 * TODO 未サポート
 *
 * @return integer Number of affected rows
 * @access public
 */
	function lastAffected() {
		
		/*
		 if ($this->_result) {
		 return mysql_affected_rows($this->connection);
		 }
		*/
		return null;
		
	}
/**
 * Returns number of rows in previous resultset. If no previous resultset exists,
 * this returns false.
 * TODO 未検証
 *
 * @return integer Number of rows in resultset
 * @access public
 */
	function lastNumRows() {
		
		if ($this->_result and is_resource($this->_result)) {
			return @mysql_num_rows($this->_result);
		}
		return null;
		
	}
/**
 * 最後に追加されたデータのIDを返す
 *
 * @param mixed	$source
 * @return mixed	最後に追加されたデータのID
 * @access public
 */
	function lastInsertId($source = null) {

		if(!empty($this->_lastInsertId)) {
			return $this->_lastInsertId;
		}else {
			return null;
		}

	}
/**
 * Converts database-layer column types to basic types
 * TODO 未検証
 *
 * @param string $real Real database-layer column type (i.e. "varchar(255)")
 * @return string Abstract column type (i.e. "string")
 * @access public
 */
	function column($real) {
		
		if (is_array($real)) {
			$col = $real['name'];
			if (isset($real['limit'])) {
				$col .= '('.$real['limit'].')';
			}
			return $col;
		}

		$col = str_replace(')', '', $real);
		$limit = $this->length($real);
		if (strpos($col, '(') !== false) {
			list($col, $vals) = explode('(', $col);
		}

		if (in_array($col, array('date', 'time', 'datetime', 'timestamp'))) {
			return $col;
		}
		if ($col == 'tinyint' && $limit == 1) {
			return 'boolean';
		}
		if (strpos($col, 'int') !== false) {
			return 'integer';
		}
		if (strpos($col, 'char') !== false || $col == 'tinytext') {
			return 'string';
		}
		if (strpos($col, 'text') !== false) {
			return 'text';
		}
		if (strpos($col, 'blob') !== false || $col == 'binary') {
			return 'binary';
		}
		if (in_array($col, array('float', 'double', 'decimal'))) {
			return 'float';
		}
		if (strpos($col, 'enum') !== false) {
			return "enum($vals)";
		}
		if ($col == 'boolean') {
			return $col;
		}
		return 'text';
		
	}
/**
 * queryAssociation
 *
 * @param Model $model
 * @param Model $linkModel
 * @param string $type Association type
 * @param array $association
 * @param mixed $assocData
 * @param array $queryData
 * @param boolean $external
 * @param array $resultSet
 * @param integer $recursive Number of levels of association
 * @param array $stack
 * @return void
 * @access public
 */
	function queryAssociation(&$model, &$linkModel, $type, $association, $assocData, &$queryData, $external = false, &$resultSet, $recursive, $stack) {

		// DB接続
		if(!$this->connect($linkModel,false)) {
			return false;
		}
		// 全てのフィールドを取得
		$this->_loadCsvFields($linkModel);

		if ($query = $this->generateAssociationQuery($model, $linkModel, $type, $association, $assocData, $queryData, $external, $resultSet)) {
			if (!isset($resultSet) || !is_array($resultSet)) {
				if (Configure::read() > 0) {
					e('<div style = "font: Verdana bold 12px; color: #FF0000">' . sprintf(__('SQL Error in model %s:', true), $model->alias) . ' ');
					if (isset($this->error) && $this->error != null) {
						e($this->error);
					}
					e('</div>');
				}
				return null;
			}
			$count = count($resultSet);

			if ($type === 'hasMany' && empty($assocData['limit']) && !empty($assocData['foreignKey'])) {
				$ins = $fetch = array();
				for ($i = 0; $i < $count; $i++) {
					if ($in = $this->insertQueryData('{$__cakeID__$}', $resultSet[$i], $association, $assocData, $model, $linkModel, $stack)) {
						$ins[] = $in;
					}
				}

				if (!empty($ins)) {
					$fetch = $this->fetchAssociated($model, $query, $ins);
				}

				if (!empty($fetch) && is_array($fetch)) {
					if ($recursive > 0) {
						foreach ($linkModel->__associations as $type1) {
							foreach ($linkModel->{$type1} as $assoc1 => $assocData1) {
								$deepModel =& $linkModel->{$assoc1};
								$tmpStack = $stack;
								$tmpStack[] = $assoc1;

								if ($linkModel->useDbConfig === $deepModel->useDbConfig) {
									$db =& $this;
								} else {
									$db =& ConnectionManager::getDataSource($deepModel->useDbConfig);
								}
								$db->queryAssociation($linkModel, $deepModel, $type1, $assoc1, $assocData1, $queryData, true, $fetch, $recursive - 1, $tmpStack);
							}
						}
					}
				}
				return $this->__mergeHasMany($resultSet, $fetch, $association, $model, $linkModel, $recursive);
			} elseif ($type === 'hasAndBelongsToMany') {
				$ins = $fetch = array();
				for ($i = 0; $i < $count; $i++) {
					if ($in = $this->insertQueryData('{$__cakeID__$}', $resultSet[$i], $association, $assocData, $model, $linkModel, $stack)) {
						$ins[] = $in;
					}
				}
				if (!empty($ins)) {
					$query = str_replace('{$__cakeID__$}', join(', ', $ins), $query);
					$query = str_replace('= (', 'IN (', $query);
					$query = str_replace('=  (', 'IN (', $query);
				}

				$foreignKey = $model->hasAndBelongsToMany[$association]['foreignKey'];
				$joinKeys = array($foreignKey, $model->hasAndBelongsToMany[$association]['associationForeignKey']);
				list($with, $habtmFields) = $model->joinModel($model->hasAndBelongsToMany[$association]['with'], $joinKeys);
				$habtmFieldsCount = count($habtmFields);
				$q = $this->insertQueryData($query, null, $association, $assocData, $model, $linkModel, $stack);

				$tableName = $this->config['prefix'].$assocData['joinTable'];
				$this->_loadCsvFields($tableName);

				if ($q != false) {
					$fetch = $this->fetchAll($q, $model->cacheQueries, $model->alias);
					if($fetch) {
						$query = $this->generateAssociationQuery($model, $linkModel, 'belongsTo', $association, $assocData, $queryData, $external, $resultSet);
						$ins = Set::extract('/'.$assocData['with'].'/'.$assocData['associationForeignKey'], $fetch);
						if (!empty($ins)) {
							$query = str_replace('{$__cakeForeignKey__$}', '('.join(', ', $ins).')', $query);
							$query = str_replace('= (', 'IN (', $query);
							$query = str_replace('=  (', 'IN (', $query);
						}
						$q = $this->insertQueryData($query, null, $association, $assocData, $model, $linkModel, $stack);

						$this->_loadCsvFields($linkModel);
						$fetch2 = $this->fetchAll($q, $model->cacheQueries, $model->alias);
						
						$uniqueIds = $merge = array();
						/*foreach($fetch2 as $row) {
							foreach($fetch as $j => $data) {
								if (
								(isset($data[$with]) && $data[$with][$joinKeys[1]] === $row[$assocData['className']][$linkModel->primaryKey]) &&
										(!in_array($data[$with][$joinKeys[1]], $uniqueIds))
								) {
									$uniqueIds[] = $data[$with][$joinKeys[1]];

									if ($habtmFieldsCount <= 2) {
										unset($data[$with]);
									}
									$data[$assocData['className']] = $row;
									$merge[] = am($data, $row);
								}
							}
						}*/

						$merge = array();
						foreach($fetch as $row) {
							foreach($fetch2 as $data) {
								if(isset($row[$with]) && $row[$with][$joinKeys[1]] === $data[$assocData['className']][$linkModel->primaryKey]) {
									$merge[] = am($row, $data);
								}
							}
						}
						$fetch = $merge;
					}
				} else {
					$fetch = null;
				}
			}

			for ($i = 0; $i < $count; $i++) {
				$row =& $resultSet[$i];

				if ($type !== 'hasAndBelongsToMany') {
					$q = $this->insertQueryData($query, $resultSet[$i], $association, $assocData, $model, $linkModel, $stack);
					if ($q != false) {
						$fetch = $this->fetchAll($q, $model->cacheQueries, $model->alias);
					} else {
						$fetch = null;
					}
				}
				$selfJoin = false;

				if ($linkModel->name === $model->name) {
					$selfJoin = true;
				}

				if (!empty($fetch) && is_array($fetch)) {
					if ($recursive > 0) {
						foreach ($linkModel->__associations as $type1) {
							foreach ($linkModel->{$type1} as $assoc1 => $assocData1) {
								$deepModel =& $linkModel->{$assoc1};

								if (($type1 === 'belongsTo') || ($deepModel->alias === $model->alias && $type === 'belongsTo') || ($deepModel->alias != $model->alias)) {
									$tmpStack = $stack;
									$tmpStack[] = $assoc1;
									if ($linkModel->useDbConfig == $deepModel->useDbConfig) {
										$db =& $this;
									} else {
										$db =& ConnectionManager::getDataSource($deepModel->useDbConfig);
									}
									$db->queryAssociation($linkModel, $deepModel, $type1, $assoc1, $assocData1, $queryData, true, $fetch, $recursive - 1, $tmpStack);
								}
							}
						}
					}
					if ($type == 'hasAndBelongsToMany') {
						$uniqueIds = $merge = array();

						foreach($fetch as $j => $data) {
							if (
							(isset($data[$with]) && $data[$with][$foreignKey] === $row[$model->alias][$model->primaryKey]) &&
									(!in_array($data[$with][$joinKeys[1]], $uniqueIds))
							) {
								$uniqueIds[] = $data[$with][$joinKeys[1]];

								if ($habtmFieldsCount <= 2) {
									unset($data[$with]);
								}
								$merge[] = $data;
							}
						}
						if (empty($merge) && !isset($row[$association])) {
							$row[$association] = $merge;
						} else {
							$this->__mergeAssociation($resultSet[$i], $merge, $association, $type);
						}
					} else {
						$this->__mergeAssociation($resultSet[$i], $fetch, $association, $type, $selfJoin);
					}
					$resultSet[$i][$association] = $linkModel->afterfind($resultSet[$i][$association]);

				} else {
					$tempArray[0][$association] = false;
					$this->__mergeAssociation($resultSet[$i], $tempArray, $association, $type, $selfJoin);
				}
			}
		}

		// 接続を解除
		$this->disconnect($linkModel->tablePrefix.$linkModel->table);

	}
/**
 * CSVフィールドを読み込む
 *
 * @param mixed $model or $table
 * @return void
 * @access protected
 */
	function _loadCsvFields($model) {
		
		if(is_object($model)) {
			$this->_csvFields = array_keys($model->schema());
		} else {
			if(empty($this->connection[$model])) {
				$this->_connect($model, false);
			}
			rewind($this->connection[$model]);
			$this->_csvFields = fgetcsv($this->connection[$model],10240);
		}
		
	}
/**
 * 全ての結果セットを返す
 *
 * @param string $sql SQL statement
 * @param boolean $cache Enables returning/storing cached query results
 * @return array Array of resultset rows, or false if no rows matched
 * @access public
 */
	function fetchAll($sql, $cache = true, $modelName = null) {
		
		if ($cache && isset($this->_queryCache[$sql])) {
			if (preg_match('/^\s*select/i', $sql)) {
				return $this->_queryCache[$sql];
			}
		}

		if ($this->execute($sql)) {
			$out = array();

			if(is_array($this->_result)) {
				reset($this->_result);
			}

			while ($item = $this->fetchRow()) {
				$out[] = $item;
			}

			if ($cache) {
				if (strpos(trim(strtolower($sql)), 'select') !== false) {
					$this->_queryCache[$sql] = $out;
				}
			}
			return $out;

		} else {
			return array();
		}
		
	}
/**
 * カレントの結果セットを取得する
 *
 * @param string $sql
 * @return array The fetched row as an array
 * @access public
 */
	function fetchRow($sql = null) {
		
		if (!empty($sql) && is_string($sql) && strlen($sql) > 5) {
			if (!$this->execute($sql)) {
				return null;
			}
		}
		if (is_array($this->_result)) {
			$this->resultSet($this->_result);
			$resultRow = $this->fetchResult();
			return $resultRow;
		} else {
			return null;
		}
		
	}
/**
 * 結果をfetchResult用にセットする。
 *
 * テーブル名とフィールド名のマッピングを取得する
 * fetchRowよりレコード分呼び出される
 * 配列ポインタを利用してループを制御
 *
 * @param array $results
 * @return void
 * @access public
 */
	function resultSet(&$results) {
		
		$this->results =& $results;
		$this->map = array();
		$index = 0;

		// 要素がない場合は戻る
		if(!$row = current($results))
			return;

		reset($row);

		while ($_row = each($row)) {
			$this->map[$index++] = array($this->__resultModelName, ($_row['key']));
		}

	}
/**
 * CakePHP固有のモデル配列を生成する
 * 処理ごとに配列ポインタを進める
 *
 * @return array $resultRow
 * @access public
 */
	function fetchResult() {
		
		if ($row = each($this->results)) {
			$resultRow = array();
			$i = 0;
			$index = 0;
			foreach ($row['value'] as $key => $field) {
				list($table, $column) = $this->map[$index++];
				$resultRow[$table][$column] = $row['value'][$key];
				$i++;
			}
			return $resultRow;
		} else {
			return false;
		}
		
	}
/**
 * Inserts multiple values into a table
 *
 * @param string $table
 * @param string $fields
 * @param array $values
 * @return void
 * @access public
 */
	function insertMulti($table, $fields, $values) {
		$table = $this->fullTableName($table);
		if (is_array($fields)) {
			$fields = join(', ', array_map(array(&$this, 'name'), $fields));
		}
		$values = implode(', ', $values);
		$this->query("INSERT INTO {$table} ({$fields}) VALUES {$values}");
	}
/**
 * Returns an array of the indexes in given table name.
 * CSVの場合主キーは id 固定
 * 
 * @param string $model Name of model to inspect
 * @return array Fields in table. Keys are column and unique
 * @access public
 */
	function index($model) {
		
		$index = array('PRIMARY'=>array('unique'=>1,'column'=>'id'));
		return $index;
	
	}
/**
 * Alter Table syntax for the given Schema comparison
 * 未サポート
 * 
 * @param array $compare Result of a CakeSchema::compare()
 * @return array Array of alter statements to make.
 * @access public
 */
	function alterSchema($compare, $table = null) {
		
		return false
		;
	}
/**
 * Generate index alteration statements for a table.
 * 未サポート
 * 
 * @param string $table Table to alter indexes for
 * @param array $new Indexes to add and drop
 * @return array Index alteration statements
 * @access protected
 */
	function _alterIndexes($table, $indexes) {
		
		return array();
		
	}
/**
 * テーブル名を変更する
 *
 * @param	array	$options [ old / new ]
 * @return	mixed
 * @access	public
 */
	function renameTable($options) {

		extract($options);

		if(!isset($new) || !isset($old)) {
			return false;
		}

		$new = $this->config['prefix'].$new;
		$old = $this->config['prefix'].$old;

		if(!$this->disconnect($old)){
			return false;
		}
		$path = $this->config['database'].DS;
		$oldPath = $path.$old.'.csv';
		$newPath = $path.$new.'.csv';
		if(!file_exists($oldPath)){
			return false;
		}

		return rename($oldPath,$newPath);

	}
/**
 * テーブル構造を変更する
 *
 * @param array $options [ new / old ]
 * @return boolean
 * @access public
 */
	function alterTable($options) {

		extract($options);

		if(!isset($old) || !isset($new)){
			return false;
		}

		$Schema = ClassRegistry::init('CakeSchema');
		$Schema->connection = $this->configKeyName;
		$compare = $Schema->compare($old, $new);

		if(!$compare) {
			return false;
		}

		foreach($compare as $table => $types) {
			if(!$types){
				return false;
			}
			foreach($types as $type => $fields) {
				if(!$fields){
					return false;
				}
				foreach($fields as $fieldName => $column) {
					switch ($type) {
						case 'add':
							if(!$this->addColumn(array('field'=>$fieldName,'table'=>$table, 'column'=>$column))){
								return false;
							}
							break;
						case 'change':
							if(!$this->changeColumn(array('field'=>$fieldName,'table'=>$table, 'column'=>$column))){
								return false;
							}
							break;
						case 'drop':
							if(!$this->dropColumn(array('field'=>$fieldName,'table'=>$table))){
								return false;
							}
							break;
					}
				}
			}
		}
		clearCache(null, 'models');
		return true;

	}
/**
 * Deletes all the records in a table and resets the count of the auto-incrementing
 * primary key, where applicable.
 *
 * @param mixed $table A string or model class representing the table to be truncated
 * @return boolean SQL TRUNCATE TABLE statement, false if not applicable.
 * @access public
 */
	function truncate($table) {
		
		// TODO 現状、CSVのDELETE文はWHERE句がないと実行されない
		return $this->execute('DELETE From ' . $this->fullTableName($table) . ' WHERE 1=1');
		
	}
/**
 * Begin a transaction
 * TODO 未実装
 * 
 * @param Model $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 */
	function begin(&$model) {
		
		return null;
		
	}
/**
 * Commit a transaction
 * TODO 未実装
 * @param Model $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 * @access pablic
 */
	function commit(&$model) {
		
		return null;
		
	}
/**
 * Rollback a transaction
 * TODO 未実装
 * 
 * @param Model $model
 * @return boolean True on success, false on fail
 * (i.e. if the database/model does not support transactions,
 * or a transaction has not started).
 *@access public
 */
	function rollback(&$model) {
		
		return null;
		
	}
/**
 * Generates an array representing a query or part of a query from a single model or two associated models
 *
 * @param Model $model
 * @param Model $linkModel
 * @param string $type
 * @param string $association
 * @param array $assocData
 * @param array $queryData
 * @param boolean $external
 * @param array $resultSet
 * @return mixed
 * @access public
 */
	function generateAssociationQuery(&$model, &$linkModel, $type, $association = null, $assocData = array(), &$queryData, $external = false, &$resultSet) {
		
		$queryData = $this->__scrubQueryData($queryData);
		$assocData = $this->__scrubQueryData($assocData);

		if (empty($queryData['fields'])) {
			$queryData['fields'] = $this->fields($model, $model->alias);
		} elseif (!empty($model->hasMany) && $model->recursive > -1) {
			$assocFields = $this->fields($model, $model->alias, array("{$model->alias}.{$model->primaryKey}"));
			$passedFields = $this->fields($model, $model->alias, $queryData['fields']);

			if (count($passedFields) === 1) {
				$match = strpos($passedFields[0], $assocFields[0]);
				$match1 = strpos($passedFields[0], 'COUNT(');
				if ($match === false && $match1 === false) {
					$queryData['fields'] = array_merge($passedFields, $assocFields);
				} else {
					$queryData['fields'] = $passedFields;
				}
			} else {
				$queryData['fields'] = array_merge($passedFields, $assocFields);
			}
			unset($assocFields, $passedFields);
		}

		if ($linkModel == null) {
			return $this->buildStatement(
				array(
					'fields' => array_unique($queryData['fields']),
					'table' => $this->fullTableName($model),
					'alias' => $model->alias,
					'limit' => $queryData['limit'],
					'offset' => $queryData['offset'],
					'joins' => $queryData['joins'],
					'conditions' => $queryData['conditions'],
					'order' => $queryData['order'],
					'group' => $queryData['group']
				),
				$model
			);
		}
		if ($external && !empty($assocData['finderQuery'])) {
			return $assocData['finderQuery'];
		}

		$alias = $association;
		$self = ($model->name == $linkModel->name);
		$fields = array();

		if ((!$external && in_array($type, array('hasOne', 'belongsTo')) && $this->__bypass === false) || $external) {
			$fields = $this->fields($linkModel, $alias, $assocData['fields']);
		}
		if (empty($assocData['offset']) && !empty($assocData['page'])) {
			$assocData['offset'] = ($assocData['page'] - 1) * $assocData['limit'];
		}
		$assocData['limit'] = $this->limit($assocData['limit'], $assocData['offset']);

		switch ($type) {
			case 'hasOne':
			case 'belongsTo':
				$conditions = $this->__mergeConditions(
					$assocData['conditions'],
					$this->getConstraint($type, $model, $linkModel, $alias, array_merge($assocData, compact('external', 'self')))
				);

				if (!$self && $external) {
					foreach ($conditions as $key => $condition) {
						if (is_numeric($key) && strpos($condition, $model->alias . '.') !== false) {
							unset($conditions[$key]);
						}
					}
				}

				if ($external) {
					$query = array_merge($assocData, array(
						'conditions' => $conditions,
						'table' => $this->fullTableName($linkModel),
						'fields' => $fields,
						'alias' => $alias,
						'group' => null
					));
					$query = array_merge(array('order' => $assocData['order'], 'limit' => $assocData['limit']), $query);
				} else {
					$join = array(
						'table' => $this->fullTableName($linkModel),
						'alias' => $alias,
						'type' => isset($assocData['type']) ? $assocData['type'] : 'LEFT',
						'conditions' => trim($this->conditions($conditions, true, false, $model))
					);
					$queryData['fields'] = array_merge($queryData['fields'], $fields);

					if (!empty($assocData['order'])) {
						$queryData['order'][] = $assocData['order'];
					}
					if (!in_array($join, $queryData['joins'])) {
						$queryData['joins'][] = $join;
					}
					return true;
				}
			break;
			case 'hasMany':
				$assocData['fields'] = $this->fields($linkModel, $alias, $assocData['fields']);
				if (!empty($assocData['foreignKey'])) {
					$assocData['fields'] = array_merge($assocData['fields'], $this->fields($linkModel, $alias, array("{$alias}.{$assocData['foreignKey']}")));
				}
				$query = array(
					'conditions' => $this->__mergeConditions($this->getConstraint('hasMany', $model, $linkModel, $alias, $assocData), $assocData['conditions']),
					'fields' => array_unique($assocData['fields']),
					'table' => $this->fullTableName($linkModel),
					'alias' => $alias,
					'order' => $assocData['order'],
					'limit' => $assocData['limit'],
					'group' => null
				);
			break;
			case 'hasAndBelongsToMany':
				$joinFields = array();
				$joinAssoc = null;

				if (isset($assocData['with']) && !empty($assocData['with'])) {
					$joinKeys = array($assocData['foreignKey'], $assocData['associationForeignKey']);
					list($with, $joinFields) = $model->joinModel($assocData['with'], $joinKeys);

					$joinTbl = $this->fullTableName($model->{$with});
					$joinAlias = $joinTbl;

					if (is_array($joinFields) && !empty($joinFields)) {
						$joinFields = $this->fields($model->{$with}, $model->{$with}->alias, $joinFields);
						$joinAssoc = $joinAlias = $model->{$with}->alias;
					} else {
						$joinFields = array();
					}
				} else {
					$joinTbl = $this->fullTableName($assocData['joinTable']);
					$joinAlias = $joinTbl;
				}
				// CUSTOMIZE modify 2011/04/23 ryuring
				// CSVでHABTMに対応する為、ここでは、リンクテーブルのテーブルのみ取得するように変更
				// >>>
				/*$query = array(
					'conditions' => $assocData['conditions'],
					'limit' => $assocData['limit'],
					'table' => $this->fullTableName($linkModel),
					'alias' => $alias,
					'fields' => array_merge($this->fields($linkModel, $alias, $assocData['fields']), $joinFields),
					'order' => $assocData['order'],
					'group' => null,
					'joins' => array(array(
						'table' => $joinTbl,
						'alias' => $joinAssoc,
						'conditions' => $this->getConstraint('hasAndBelongsToMany', $model, $linkModel, $joinAlias, $assocData, $alias)
					))
				);*/
				// ---
				$query = array(
					'conditions' => $this->getConstraint('hasMany', $model, $linkModel, $joinAlias, $assocData),
					'limit' => $assocData['limit'],
					'table' => $joinTbl,
					'alias' => $joinAssoc,
					'fields' => $joinFields,
					'order' => $assocData['order'],
					'group' => null
				);
				// <<<
			break;
		}
		if (isset($query)) {
			return $this->buildStatement($query, $model);
		}
		return null;
		
	}
	
}
/**
 * クイックソート
 *
 * TODO GLOBAL グローバルな関数として再配置する必要あり
 *
 * @param array $int_array = ソートする配列
 * @param int $left = 開始位置（0で決め打ち）
 * @param int $right = 終了位置（$int_arrayの要素数：決め打ち）
 * @param string $flag = ソート対象の配列要素
 * @param string $order = ソートの昇順(ASC)・降順(DESC)　デフォルトは昇順
 * @return array ソート後の配列
 */
	function qsort(&$int_array, $left = 0, $right, $flag = "", $order = "ASC") {
		
		if ($left >= $right) {
			return;
		}
		swap ($int_array, $left, intval(($left+$right)/2));
		$last = $left;
		for ($i = $left + 1; $i <= $right; $i++) {
			if($flag) {
				if($order=="DESC") {
					if ($int_array[$i]["".$flag.""] > $int_array[$left]["".$flag.""]) {
						swap($int_array, ++$last, $i);
					}
				} else {
					if ($int_array[$i]["".$flag.""] < $int_array[$left]["".$flag.""]) {
						swap($int_array, ++$last, $i);
					}
				}
			} else {
				if($order=="DESC") {
					if ($int_array[$i] > $int_array[$left]) {
						swap($int_array, ++$last, $i);
					}
				} else {
					if ($int_array[$i] < $int_array[$left]) {
						swap($int_array, ++$last, $i);
					}
				}
			}
		}
		swap($int_array, $left, $last);
		qsort($int_array, $left, $last-1, $flag = $flag, $order = $order);
		qsort($int_array, $last+1, $right, $flag = $flag, $order = $order);
		
	}
/**
 * swap
 * qsort で利用される
 * TODO GLOBAL グローバルな関数として再配置する必要あり
 *
 * @param array
 * @param string
 * @param string
 * @return void
 * @access public
 */
	function swap(&$v, $i, $j) {
		
		$temp = $v[$i];
		$v[$i] = $v[$j];
		$v[$j] = $temp;
		
	}
