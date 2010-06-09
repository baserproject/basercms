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
 * CSV DBO Driver
 *
 * @package			baser.models.datasources.dbo
 */
class DboCsv extends DboSource {
/**
 * ドライバーの説明文
 *
 * @var		string
 * @access	public
 */
	var $description = "CSV DBO Driver";
/**
 * 開始クォート
 * TODO 空文字でいいかも
 *
 * @var		string
 * @access	public
 */
	var $startQuote = "`";
/**
 * 終了クォート
 * TODO 空文字でいいかも
 *
 * @var		string
 * @access	public
 */
	var $endQuote = "`";
/**
 * エンコーディング
 *
 * @var		string
 * @access	public
 */
	var $endcoding = "UTF-8";
/**
 * コネクション
 *
 * @var 	array
 * @access 	public
 */
	var $connection = array();
/**
 * 接続状態
 *
 * @var		boolean
 * @access 	public
 */
	var $connected = false;
/**
 * CSVファイル名（フルパス）
 *
 * @var		string
 * @access	public
 */
	var $csvName = '';
/**
 * 最後に追加されたID
 *
 * @var		string
 * @access	protected
 */
	var $_lastInsertId = '';
/**
 * 基本SQLコマンドの一覧
 * 未実装
 *
 * @var 	array
 * @access	protected
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
 * @var		string
 * @access	protected
 */
	var $_resultTable = 'csvs';
/**
 * CSVドライバの基本設定
 *
 * @var 	array
 * @access	protected
 */
	var $_baseConfig = array(
			'database' => 'cake'
	);
/**
 * column definition
 *
 * @var 	array
 * @access	public
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
 * @param 	array	接続設定
 * @param 	boolean	自動接続の有無
 * @return 	void
 * @access	private
 */
	function __construct($config = null, $autoConnect = true) {

		parent::__construct($config,false);

	}
/**
 * The "R" in CRUD
 *
 * TODO 改修要
 *
 * @param 	Model 	$model
 * @param 	array 	$queryData
 * @param 	integer アソシエーションの深さ
 * @return 	array	結果セット
 * @access	public
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
		$this->_csvFields = array_keys($model->schema());

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
 * @param	Model	$model
 * @param	array	フィールドリスト
 * @param	array	値リスト
 * @return	boolean	Success
 * @access	public
 */
	function create(&$model, $fields = null, $values = null) {

		// DB接続
		if(!$this->connect($model,true)) {
			return false;
		}

		// 全てのフィールドを取得
		$this->_csvFields = array_keys($model->schema());

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
 * @param	Model	$model
 * @param	array	フィールドリスト
 * @param	array	値リスト
 * @param	mixed	条件
 * @return	boolean
 * @access	public
 */
	function update(&$model, $fields = array(), $values = null, $conditions = null) {

		// DB接続
		if(!$this->connect($model,true)) {
			return false;
		}

		$this->_csvFields = array_keys($model->schema());

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

		if (empty($conditions)) {
			$alias = $joins = false;
		}
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
 * @param	Model	$model
 * @param	mixed	条件
 * @return	boolean	Success
 * @access	public
 */
	function delete(&$model, $conditions = null) {

		// DB接続
		if(!$this->connect($model,true)) {
			return false;
		}

		$this->_csvFields = array_keys($model->schema());


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
 * @param	model	モデル
 * @param	boolean	CSVファイルロック有無
 * @return	boolean 接続できた場合は True 、できなかった場合は False
 * @access	public
 */
	function connect(&$model,$lock=true) {

		$config = $this->config;
		$tableName = $this->fullTableName($model,false);
		$this->connected[$tableName] = false;

		if(!$this->_connect($tableName,$lock,$model->plugin)) {

			// 接続が見つからない場合はエラー
			//die (__("DboCsv::connect : Can't find Connection : ".$model->tablePrefix.$model->table));
			$this->cakeError('missingConnection', array(array('className' => $model->alias)));
		}else {
			return true;
		}

	}
/**
 * Reconnects to database server with optional new settings
 * CSVの場合は切断するだけ
 * @param array $config An array defining the new configuration settings
 * @return boolean True on success, false on failure
 */
	function reconnect($config = null) {
		$this->disconnect();
		$this->setConfig($config);
		$this->_sources = null;
	}
/**
 * 接続処理
 *
 * @param	string	$tableName
 * @param	boolean	$lock
 * @param	string	$plugin
 * @param	boolean	$force
 * @return	mixed	ファイルポインタ / false
 * @access	protected
 */
	function _connect($tableName,$lock=true,$plugin=null,$force = false) {

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
		if (isset($config['encoding']) && !empty($config['encoding'])) {
			$this->setEncoding($config['encoding']);
		}
		return $this->connected[$tableName];
	}
/**
 * CSVファイルのファイルリソースを開放する
 *
 * @param	string	テーブル名
 * @return	boolean	開放できたら True を返す
 * @access	public
 */
	function disconnect($tableName = null) {

		// TODO 必要かも
		//@mysql_free_result($this->results);

		if($tableName) {
			$index = $tableName;
		}else {
			$index = 0;
		}
		if($this->csvCloseByLocked($this->connection[$index])) {

			unset($this->connected[$index]);
			unset($this->csvName[$index]);
			return true;

		}else {
			return false;
		}

	}
/**
 * データベースに接続できているかチェックする
 *
 * @param	string	テーブル名
 * @return 	boolean	True if the database is connected, else false
 * @access	public
 */
	function isConnected($tableName = null) {

		if($this->connected) {
			if($tableName) {
				return $this->connected[$tableName];
			}else {
				return $this->connected[0];
			}
		}else {
			return false;
		}

	}
/**
 * CSVファイルを開く
 *
 * @param	string	CSVファイルのパス
 * @return 	stream	CSVファイルへのポインタ
 * @access	public
 */
	function csvConnect($file) {

		// ファイルを開く
		$fp = fopen($file,'r');
		return $fp;

	}
/**
 * ロック状態でCSVファイルを開く
 *
 * 開く前にバックアップを生成する
 *
 * @param	string	CSVファイルのパス
 * @return 	stream	CSVファイルへのポインタ
 * @access	public
 */
	function csvConnectByLocked($file) {

		/* 念の為バックアップ */
		// TODO すぐに上書きされてしまうので意味がないかも
		// 上書きしないように一意の名称でバックアップをとるとゴミが溜まりすぎる
		if(!is_dir(TMP."csv")) {
			mkdir(TMP."csv",0777);
		}
		if(file_exists($file)) {
			copy($file,TMP."csv".DS.basename($file).".bak");
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
 * ロック状態のCSVファイルを解除した上で開放する
 *
 * @param	stream	CSVファイルへのパス
 * @return 	void	開放に成功した場合には true を返す
 * @access	public
 */
	function csvCloseByLocked(&$fp) {
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
 * @param 	string 	SQL statement
 * @return 	mixed 	配列の結果セットまたは、true/false
 * @access 	protected
 */
	function _execute($sql) {

		return $this->csvQuery($sql);

	}
/**
 * CSVデータの操作を行う
 *
 * @param 	string	$sql SQL statement
 * @return 	mixed 	配列の結果セットまたは、true/false
 * @access	public
 */
	function csvQuery($sql) {

		// SQL文を解析して、CSV操作用のクエリデータを生成する
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
			}
		}else {
			$ret = true;
		}
		return $ret;

	}
/**
 * CSVデータを読み込む
 *
 * @param 	array 	$queryData
 * @return 	mixed	array / false result
 * @access 	public
 */
	function readCsv($queryData) {

		$queryData = am($queryData,array('option'=>''));

		/* COUNTフィールドの確認 */
		if(preg_match("/^COUNT\(\*\)\sAS\scount$/s",trim($queryData['fields'][0]))) {
			$queryData['fields'] = null;
			$queryData['option'] = 'count';
		}
		/* MAXフィールドの確認（１フィールドのみ対応） */
		if(preg_match("/^MAX\((.+?)\)\sAS\s(.*?)$/s",trim($queryData['fields'][0]),$matches)) {
			$queryData['fields'] = null;
			$maxField = $matches[1];
			$maxAsField = $matches[2];
			$queryData['option'] = 'max';
		}

		/* CSVファイルを配列として読み込む */
		// TODO ここでは、全データを読み込む仕様となっているので大量のデータを扱う場合、メモリに負荷がかかってしまう。
		// 並び替えを実行した上で、指定件数を取り出すという要件を実現する為、こういう仕様となっている。
		// 何か解決策があれば・・・
		$records = $this->_readCsvFile($queryData['tableName']);
		// 文字コードを変換
		mb_convert_variables($this->endcoding,"SJIS",$records);

		/* ソート処理（１フィールドのみ対応） */
		if(!empty($queryData['order'][0])) {
			list($sortField,$direct) = split(" ",$queryData['order'][0]);
			qsort($records, 0, count($records)-1, $sortField,strtoupper($direct));
		}

		/* ページ指定がある場合は、取得開始件数を計算 */
		if($queryData['page']) {
			$begin = ($queryData['page'] - 1) * $queryData['limit'] + 1;
		}

		/* データのフィルタリング */
		$count=0;
		$matchCount=0;
		$maxId = 0;
		$maxValue = 0;
		if($records) {
			foreach($records as $record) {

				// IDの最大値を取得
				if($record['id']>$maxId) {
					$maxId = $record['id'];
				}

				// 条件に合致しない場合は取得せず次へ
				if(!empty($queryData['conditions']) && !eval($queryData['conditions'])) {
					continue;
				}

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

		$this->_resultTable = $queryData["className"];
		$this->_maxId = $maxId;
		$this->_count = $count;

		// カウントオプションの場合は件数を返す
		if(!empty($queryData['option']) && $queryData['option'] == 'count') {
			return array(0=>array('count'=>$this->_count));
		}

		// MAXオプションの場合は指定フィールドの最大値を返す
		if(!empty($queryData['option']) && $queryData['option'] == 'max') {
			return array(0=>array($maxAsField=>$maxValue));
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
 * @param 	array 	クエリデータ
 * @return 	boolen 	true/false
 * @access 	public
 */
	function createCsv($queryData) {

		// 主キーがない場合のauto処理
		if(empty($queryData['values']['id'])) {
			$this->readCsv($queryData);
			$queryData['values']['id'] = $this->_maxId+1;
			$this->_lastInsertId = $queryData['values']['id'];
		}

		// カラムをテーブル情報どおりに並べる
		foreach($this->_csvFields  as $field) {
			if(isset($queryData['values'][$field])) {
				$tmpData[$field]=$queryData['values'][$field];
			}else {
				$tmpData[$field]=null;
			}
		}

		// CSVファイルを全て読み込む
		rewind($this->connection[$queryData['tableName']]);
		$csv = fread($this->connection[$queryData['tableName']],filesize($this->csvName[$queryData['tableName']]));

		// 最後の行に改行がなかったら改行を追加する
		// もっといい方法があれば・・・
		if(!preg_match("/\n$/s",$csv)) {
			$csv .= "\r\n";
		}

		// 新しいレコードを追加
		mb_convert_variables("SJIS",$this->endcoding,$tmpData);
		$csv .= implode(",",$tmpData)."\r\n";

		// ファイルサイズを0に
		ftruncate($this->connection[$queryData['tableName']],0);

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$queryData['tableName']], $csv);

		return $ret;

	}
/**
 * CSVテーブルを生成する
 *
 * @param array $queryData
 */
	function buildCsv($queryData) {

		$this->_connect($queryData['tableName'],true,null,true);
		$head = $this->_getCsvHead($queryData['fields']);
		return fwrite($this->connection[$queryData['tableName']], $head);

	}
/**
 * CSVファイルを更新する
 *
 * @param 	array 	クエリデータ
 * @return 	boolen 	true/false
 * @access 	public
 */
	function updateCsv($queryData) {

		$records = $this->_readCsvFile($queryData['tableName']);

		// ヘッダーの生成
		$head = $this->_getCsvHead();

		mb_convert_variables("SJIS",$this->endcoding,$queryData['values']);

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
						$record[$key] = $field;
					}
				}else {
					// 既存データをCSV用にコンバートする
					$record = $this->_convertRecord($record);
				}
				$body .= implode(",",$record)."\r\n";
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$queryData['tableName']],0);

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$queryData['tableName']], $head.$body);

		return $ret;

	}
/**
 * CSVファイルよりレコードを削除する
 *
 * @param 	array 	クエリデータ
 * @return 	boolean	true/false
 * @access 	public
 */
	function deleteCsv($queryData) {

		$records = $this->_readCsvFile($queryData['tableName']);

		// ヘッダーの生成
		$head = $this->_getCsvHead();

		// ボディを生成
		$body = '';
		foreach($records as $key => $record) {
			if(!eval($queryData['conditions'])) {
				$record = $this->_convertRecord($record);
				$body .= implode(",",$record)."\r\n";
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$queryData['tableName']],0);

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$queryData['tableName']], $head.$body);

		return $ret;

	}
/**
 * CSVファイルを配列として読み込む
 *
 * @param	string	テーブル名
 * @return 	array	配列の結果セット
 * @access 	protected
 */
	function _readCsvFile($tableName=null) {

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
		rewind($this->connection[$index]);
		$this->_csvFields = fgetcsv($this->connection[$index],10240);

		while(($record = fgetcsv_reg($this->connection[$index], 10240)) !== false) {
			$_record = array();
			// 配列の添え字をフィールド名に変換
			foreach($record as $key => $value) {
				$_record[$this->_csvFields[$key]] = $value;
			}
			$records[] = $_record;
		}

		return $records;

	}
/**
 * CSV用のヘッダを取得する
 * 
 * @param	array
 * @return	string
 * @access	protected
 */
	function _getCsvHead($fields = null) {
		if(!$fields) {
			$fields = $this->_csvFields;
		}
		$head = "";
		foreach($fields as $field) {
			$head .= "\"".$field . "\",";
		}
		return substr($head,0,strlen($head)-1) . "\r\n";
	}
/**
 * CSV用のフィールドデータに変換する
 * 
 * @param string $value
 * @param boolean $dc （ " を "" に変換するか）
 * @return string
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
 * @param Model $model
 * @param String $fieldName
 */
	function addColumn(&$model,$fieldName) {

		// DB接続
		if(!$this->connect($model,true)) {
			return false;
		}

		// 全てのフィールドを取得
		$this->cacheSources = false;
		$schema = $model->schema();
		if($schema) {
			$this->_csvFields = array_keys($schema);
			if(in_array($fieldName,$this->_csvFields)) {
				return false;
			}
		}else {
			$this->_csvFields = array();
		}

		$this->_csvFields[] = $fieldName;
		$head = $this->_getCsvHead();

		// 全てのレコードを取得
		$records = $this->_readCsvFile($model->tablePrefix.$model->useTable);
		$body="";
		if($records) {
			foreach($records as $key => $record) {
				$_record = $this->_convertRecord($record);
				$_record[] = "";
				$body .= implode(",",$_record)."\r\n";
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$model->tablePrefix.$model->useTable],0);

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$model->tablePrefix.$model->useTable], $head.$body);

		$this->disconnect($model->tablePrefix.$model->useTable);
		return $ret;

	}
/**
 * フィールドを編集する
 *
 * @param Model $model
 * @param String $fieldName
 */
	function editColumn($model,$oldFieldName,$fieldName) {

		if(!is_object($model)) {
			$model = ClassRegistry::init($model);
		}

		// DB接続
		if(!$this->connect($model,true)) {
			return false;
		}

		// 全てのフィールドを取得
		$this->cacheSources = false;
		$schema = $model->schema();

		if($schema) {
			$this->_csvFields = array_keys($schema);
			if(!in_array($oldFieldName,$this->_csvFields)) {
				return false;
			}
		}else {
			$this->_csvFields = array();
		}

		// キーを取得
		while ($field = current($this->_csvFields)) {
			if ($field == $oldFieldName) {
				$key = key($this->_csvFields);
			}
			next($this->_csvFields);
		}

		// ヘッダーの生成
		$this->_csvFields[$key] = $fieldName;
		$head = $this->_getCsvHead();

		// 全てのレコードを取得
		$records = $this->_readCsvFile($model->tablePrefix.$model->useTable);
		$body="";
		if($records) {
			foreach($records as $key => $record) {
				$_record = $this->_convertRecord($record);
				$body .= implode(",",$_record)."\r\n";
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$model->tablePrefix.$model->useTable],0);

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$model->tablePrefix.$model->useTable], $head.$body);
		$this->disconnect($model->tablePrefix.$model->useTable);
		return $ret;

	}
/**
 * フィールドを削除する
 *
 * @param Model $model
 * @param String $fieldName
 */
	function deleteColumn(&$model,$fieldName) {

		// DB接続
		$this->cacheSources = false;
		if(!$this->connect($model,true)) {
			return false;
		}

		// 全てのフィールドを取得
		$schema = $model->schema();

		if($schema) {
			$this->_csvFields = array_keys($schema);
			if(!in_array($fieldName,$this->_csvFields)) {
				return false;
			}
		}else {
			$this->_csvFields = array();
		}

		// キーを取得
		while ($field = current($this->_csvFields)) {
			if ($field == $fieldName) {
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
		$records = $this->_readCsvFile($model->tablePrefix.$model->useTable);
		$body="";
		if($records) {
			foreach($records as $key => $record) {
				$_record = $this->_convertRecord($record);
				unset($_record[$fieldName]);
				$body .= implode(",",$_record)."\r\n";
			}
		}

		// ファイルサイズを0に
		ftruncate($this->connection[$model->tablePrefix.$model->useTable],0);

		//ファイルに書きこみ
		$ret = fwrite($this->connection[$model->tablePrefix.$model->useTable], $head.$body);
		$this->disconnect($model->tablePrefix.$model->useTable);
		return $ret;

	}
/**
 * SQLデータのCSV処理用の解析を行う
 *
 * @param 	string	SQL statement
 * @return 	array	configs
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

		$createPattern = "/INSERT INTO[\s]*([^\s]+)[\s]*\(([^\)]+)\)[\s]*VALUES[\s]*\((.+)\)[\s]*$/si";
		$readPattern = "/SELECT(.+)FROM(.+)WHERE(.+?)(ORDER\sBY.+|LIMIT.+|)$/si";
		$updatePattern = "/UPDATE[\s]+(.+)[\s]+SET[\s]+(.+)[\s]+WHERE[\s]+(.+)/si";
		$deletePattern = "/DELETE.+FROM[\s]+(.+)[\s]+WHERE[\s]+(.+)/si"; // deleteAllの場合は、DELETEとFROMの間にクラス名が入る
		$buildPattern = "/CREATE\sTABLE\s([^\s]+)\s*\((.+)\);/si";

		// CREATE
		if(preg_match($createPattern,$sql,$matches)) {
			$parseData['crud'] = 'create';
			$parseData['tableName'] = $this->_parseSqlTableName($matches[1]);
			$parseData['className'] = $this->_parseSqlModelName($parseData['tableName']);
			$parseData = array_merge($parseData,$this->_parseSqlValuesFromCreate($matches[2],$matches[3]));

			// READ
		}elseif(preg_match($readPattern,$sql,$matches)) {
			$parseData['crud'] = 'read';
			$parseData['fields'] = $this->_parseSqlFields($matches[1]);
			$parseData['tableName'] = $this->_parseSqlTableName($matches[2]);
			$parseData['className'] = $this->_parseSqlModelName($parseData['tableName']);
			$parseData['conditions'] = $this->_parseSqlCondition($matches[3],$parseData['fields']);

			if(isset($matches[4])) {
				$etc = $matches[4];
				if(preg_match("/ORDER\sBY(.+?)(LIMIT.+|)$/s",$etc,$matches2)) {
					$parseData['order'] = $this->_parseSqlOrder($matches2[1]);
				}
				if(preg_match("/LIMIT(.+)$/s",$etc,$matches3)) {
					$parseData = array_merge($parseData,$this->_parseSqlLimit($matches3[1]));
				}
			}

			// UPDATE
		}elseif(preg_match($updatePattern,$sql,$matches)) {

			$parseData['crud'] = 'update';
			$parseData['tableName'] = $this->_parseSqlTableName($matches[1]);
			$parseData['className'] = $this->_parseSqlModelName($parseData['tableName']);
			$parseData = array_merge($parseData,$this->_parseSqlValuesFromUpdate($matches[2]));
			$parseData['conditions'] = $this->_parseSqlCondition($matches[3],$parseData['fields']);

			// DELETE
		}elseif(preg_match($deletePattern,$sql,$matches)) {

			$parseData['crud'] = 'delete';
			$parseData['tableName'] = $this->_parseSqlTableName($matches[1]);
			$parseData['className'] = $this->_parseSqlModelName($parseData['tableName']);
			$parseData['conditions'] = $this->_parseSqlCondition($matches[2],$parseData['fields']);

			// BUILD (CREATE TABLE)
		}elseif(preg_match($buildPattern,$sql,$matches)) {
			$parseData['crud'] = 'build';
			$parseData['tableName'] = $this->_parseSqlTableName($matches[1]);
			$parseData['fields'] = $this->_parseSqlFieldsFromBuild($matches[2]);
		}

		return $parseData;

	}
/**
 * SQL文のフィールド名を配列に変換する
 *
 * @param 	string 	SQL statement
 * @return 	array 	フィールド名リスト
 * @access 	protected
 */
	function _parseSqlFields($fields) {
		$aryFields = split(",",$fields);
		foreach($aryFields as $key => $field) {
			if(strpos($field,".")!==false) {
				if(preg_match('/MAX\((.*?)\)\sAS\s`(.*?)`/s',$field,$matches)) {
					list($modelName,$fieldName) = explode(".",$matches[1]);
					$fieldName = 'MAX('.$fieldName.') AS '.$matches[2];
				}else {
					list($modelName,$fieldName) = explode(".",$field);
				}
			}else {
				$fieldName = $field;
			}
			$aryFields[$key] = trim(str_replace("`","",$fieldName));
		}
		return $aryFields;
	}
/**
 * CREATE TABLE 文のフィールド名を配列に変換する
 *
 * @param 	string 	SQL statement
 * @return 	array 	フィールド名リスト
 * @access 	protected
 */
	function _parseSqlFieldsFromBuild($sql) {

		$arySql = split(",",$sql);
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
 * @param 	string 	SQL statement
 * @return 	array 	フィールドリスト
 * @access 	protected
 */
	function _parseSqlValuesFromCreate($fields,$values) {

		$fields = str_replace("`","",$fields);
		$values = str_replace('\,','{CM}',$values);
		$values = str_replace("\"",'""',$values);

		$arrFields = explode(",",$fields);
		$arrValues = explode(",",$values);

		for($i=0;$i<count($arrFields);$i++) {
			$datas[$arrFields[$i]] = $this->_convertField($arrValues[$i],false);
		}

		$parseData['values'] = $datas;
		$parseData['fields'] = $arrFields;
		return $parseData;

	}
/**
 * SQL文のフィールド名と値を配列に変換する（UPDATE文用）
 *
 * @param 	string 	SQL statement
 * @return 	array 	フィールドリスト
 * @access 	protected
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
 * @param 	string	SQL
 * @return	string 	モデル名
 * @access 	protected
 */
	function _parseSqlTableName($tables) {

		$tables = str_replace("`","",$tables);

		if(strpos($tables,"AS") !== false) {
			list($tableName,$modelName) = split("AS",$tables);
		}else {
			$tableName = trim($tables);
		}
		return trim($tableName);

	}
/**
 * モデル名を解析する
 * TODO 現時点では単一のみ実装
 *
 * @param 	string	SQL
 * @return	string 	モデル名
 * @access 	protected
 */
	function _parseSqlModelName($tableName) {

		return Inflector::classify(str_replace($this->config['prefix'],'',$tableName));

	}
/**
 * 検索条件文字列を解析
 *
 * @param	string	SQL Conditions
 * @param	array	フィールドリスト
 * @return 	string	eval用の検索条件
 * @access 	protected
 */
	function _parseSqlCondition($conditions,$fields) {

		if(is_array($conditions)) {
			foreach($conditions as $key => $condition) {
				if(!isset($tmpConditions)) {
					$tmpConditions = $key."=='".$condition."'";
				}else {
					$tmpConditions .= " && ".$key."==".$condition;
				}
			}
			$conditions = $tmpConditions;
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
		$conditions = preg_replace("/YEAR\((.*?)\)/si","date('Y',strtotime($1))",$conditions);
		$conditions = preg_replace("/MONTH\((.*?)\)/si","date('m',strtotime($1))",$conditions);
		$conditions = preg_replace("/DAY\((.*?)\)/si","date('d',strtotime($1))",$conditions);
		$conditions = preg_replace('/([^<>])=+/s','$1==',$conditions);
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
			$conditions = preg_replace("/[`a-z0-9_]+?\sIN\s\(.*?\)/s",$in_conditions,$conditions);
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
 * @param	string	SQL
 * @return 	array	offset/limit/page 格納した配列
 * @access 	protected
 */
	function _parseSqlLimit($limit) {

		$_config = split(",",$limit);
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
 * @param	string	SQL Order
 * @return 	array	並び替え条件リスト
 * @access 	protected
 */
	function _parseSqlOrder($strOrder) {

		$strOrder = preg_replace("/`[^`]+?`\./s","",$strOrder);
		$strOrder = str_replace("`","",$strOrder);
		$aryOrders = split(",",$strOrder);
		foreach($aryOrders as $key =>$order) {
			$_aryOrders[]=trim($order);
		}
		return $_aryOrders;

	}
/**
 * テーブルの全てのリストを取得する
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
				$tables[] = str_replace('.csv','',$csv);
			}
			parent::listSources($tables);
			return $tables;
		}

	}
/**
 * フィールド情報を取得する
 *
 * @param	Model	モデル
 * @return 	array 	フィールド情報のリスト
 * @access 	public
 */
	function describe(&$model) {

		$cache = parent::describe($model);
		if ($cache != null) {
			return $cache;
		}
		$fields = false;

		// 接続されていない場合は、一時的に接続してヘッダーを取得
		// （モデルの初期化時など）
		if(empty($this->connected[$model->tablePrefix.$model->table])) {
			$this->connect($model,false);

			if(empty($this->connection[$model->tablePrefix.$model->table])) {
				die (__("DboCsv::describe : Can't find Connection"));
			}

			$cols = fgetcsv($this->connection[$model->tablePrefix.$model->table],1024);
			$this->disconnect($model->tablePrefix.$model->table);
		}else {
			// TODO 処理を見直す
			// ファイルリソースがあるにも関わらずデータの取得ができない場合がある。（インストール時に再現）
			// 取り急ぎの対応として一旦接続を切って再接続している。
			// ファイルのロック処理？かもしれない。接続を一旦解除しているので他の部分に影響している可能性もある。
			$cols = fgetcsv($this->connection[$model->tablePrefix.$model->table],1024);
			if(!$cols) {
				$this->disconnect($model->tablePrefix.$model->table);
				$this->connect($model,false);
				$cols = fgetcsv($this->connection[$model->tablePrefix.$model->table],1024);
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
 * @param	string 	$data String to be prepared for use in an SQL statement
 * @param	string 	$column The column into which this data will be inserted
 * @param	boolean $safe Whether or not numeric data should be handled automagically if no column data is provided
 * @return	string 	Quoted and escaped data
 * @access 	public
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
 * @param 	string 	エスケープ対象データ
 * @return 	string 	エスケープ処理を行ったデータ
 * @access 	public
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
 * @access 	public
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
 * @access 	public
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
 * @access 	public
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
 * @param	mixed	$source
 * @return 	mixed	最後に追加されたデータのID
 * @access 	public
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
 * @access 	public
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
 * @param 	Model	$model
 * @param 	Model	$linkModel
 * @param 	string	$type Association type
 * @param 	array	$association
 * @param 	mixed	$assocData
 * @param 	array	$queryData
 * @param 	boolean	$external
 * @param 	array	$resultSet
 * @param 	integer $recursive Number of levels of association
 * @param 	array 	$stack
 * @return	void
 * @access 	public
 */
	function queryAssociation(&$model, &$linkModel, $type, $association, $assocData, &$queryData, $external = false, &$resultSet, $recursive, $stack) {

		// DB接続
		if(!$this->connect($linkModel,false)) {
			return false;
		}
		// 全てのフィールドを取得
		$this->_csvFields = array_keys($linkModel->schema());

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
					$query = str_replace('{$__cakeID__$}', '(' .join(', ', $ins) .')', $query);
					$query = str_replace('= (', 'IN (', $query);
					$query = str_replace('=  (', 'IN (', $query);
					$query = str_replace('  WHERE 1 = 1', '', $query);
				}

				$foreignKey = $model->hasAndBelongsToMany[$association]['foreignKey'];
				$joinKeys = array($foreignKey, $model->hasAndBelongsToMany[$association]['associationForeignKey']);
				list($with, $habtmFields) = $model->joinModel($model->hasAndBelongsToMany[$association]['with'], $joinKeys);
				$habtmFieldsCount = count($habtmFields);
				$q = $this->insertQueryData($query, null, $association, $assocData, $model, $linkModel, $stack);

				if ($q != false) {
					$fetch = $this->fetchAll($q, $model->cacheQueries, $model->alias);
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
 * 全ての結果セットを返す
 *
 * @param	string	$sql SQL statement
 * @param	boolean $cache Enables returning/storing cached query results
 * @return	array	Array of resultset rows, or false if no rows matched
 * @access 	public
 */
	function fetchAll($sql, $cache = true, $modelName = null) {
		if ($cache && isset($this->_queryCache[$sql])) {
			if (preg_match('/^\s*select/i', $sql)) {
				return $this->_queryCache[$sql];
			}
		}

		if ($this->execute($sql)) {
			$out = array();

			reset($this->_result);
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
 * @param	string	$sql
 * @return	array	The fetched row as an array
 * @access 	public
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
 * @param	array	$results
 * @return	void
 * @access 	public
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
			$this->map[$index++] = array($this->_resultTable, ($_row['key']));
		}

	}
/**
 * CakePHP固有のモデル配列を生成する
 *
 * 処理ごとに配列ポインタを進める
 *
 * @return	array	$resultRow
 * @access 	public
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
 * エンコーディングを設定する
 *
 * @param	string	$enc Database encoding
 * @return	boolean
 * @access 	public
 */
	function setEncoding($enc) {
		$this->encoding = $enc;
		return true;
	}
/**
 * エンコーディングを返す
 * TODO 現在SJIS固定
 *
 * @return	string	csv encoding
 * @access 	public
 */
	function getEncoding() {
		return "SJIS";
	}
/**
 * Inserts multiple values into a table
 * TODO 未検証
 *
 * @param	string	$table
 * @param	string	$fields
 * @param 	array	$values
 * @return	void
 * @access 	public
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
 * TODO 未検証
 *
 * @param	string	$model Name of model to inspect
 * @return	array	Fields in table. Keys are column and unique
 * @access 	public
 */
	function index($model) {
		$index = array();
		$table = $this->fullTableName($model, false);
		if($table) {
			$indexes = $this->query('SHOW INDEX FROM ' . $table);
			$keys = Set::extract($indexes, '{n}.STATISTICS');
			foreach ($keys as $i => $key) {
				if(!isset($index[$key['Key_name']])) {
					$index[$key['Key_name']]['column'] = $key['Column_name'];
					$index[$key['Key_name']]['unique'] = ife($key['Non_unique'] == 0, 1, 0);
				} else {
					if(!is_array($index[$key['Key_name']]['column'])) {
						$col[] = $index[$key['Key_name']]['column'];
					}
					$col[] = $key['Column_name'];
					$index[$key['Key_name']]['column'] = $col;
				}
			}
		}
		return $index;
	}
/**
 * Generate a MySQL Alter Table syntax for the given Schema comparison
 * TODO 未サポート
 *
 * @param	mixed	$compare
 * @param	string	$table
 * @return	string
 * @access 	public
 */
	function alterSchema($compare, $table = null) {
		return false;
	}
/**
 * Generate a MySQL "drop table" statement for the given Schema object
 * TODO 未サポート
 *
 * @param	object	$schema An instance of a subclass of CakeSchema
 * @param 	string	$table Optional.  If specified only the table name given will be generated.
 *                      Otherwise, all tables defined in the schema are generated.
 * @return 	string
 * @access 	public
 */
	function dropSchema($schema, $table = null) {
		return false;
	}
}
/**
* クイックソート
*
* TODO GLOBAL グローバルな関数として再配置する必要あり
*
* @param	array	$int_array = ソートする配列
* @param	int		$left = 開始位置（0で決め打ち）
* @param	int		$right = 終了位置（$int_arrayの要素数：決め打ち）
* @param	string	$flag = ソート対象の配列要素
* @param	string	$order = ソートの昇順(ASC)・降順(DESC)　デフォルトは昇順
* @return	array	ソート後の配列
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
 *
 * qsort で利用される
 * TODO GLOBAL グローバルな関数として再配置する必要あり
 *
 * @param	array
 * @param	string
 * @param	string
 * @return	void
 */
function swap(&$v, $i, $j) {
	$temp = $v[$i];
	$v[$i] = $v[$j];
	$v[$j] = $temp;
}
/**
 * ファイルポインタから行を取得し、CSVフィールドを処理する
 *
 * TODO GLOBAL グローバルな関数として再配置する必要あり
 *
 * @param	stream	handle
 * @param	int		length
 * @param	string	delimiter
 * @param 	string	enclosure
 * @return	mixed	ファイルの終端に達した場合を含み、エラー時にFALSEを返します。
 */
function fgetcsv_reg (&$handle, $length = null, $d = ',', $e = '"') {
	$d = preg_quote($d);
	$e = preg_quote($e);
	$_line = "";
	$eof = false;
	while (($eof != true)and(!feof($handle))) {
		$_line .= (empty($length) ? fgets($handle) : fgets($handle, $length));
		$itemcnt = preg_match_all('/'.$e.'/', $_line, $dummy);
		if ($itemcnt % 2 == 0) $eof = true;
	}
	$_csv_line = preg_replace('/(?:\r\n|[\r\n])?$/', $d, trim($_line));
	$_csv_pattern = '/('.$e.'[^'.$e.']*(?:'.$e.$e.'[^'.$e.']*)*'.$e.'|[^'.$d.']*)'.$d.'/';
	preg_match_all($_csv_pattern, $_csv_line, $_csv_matches);
	$_csv_data = $_csv_matches[1];
	for($_csv_i=0;$_csv_i<count($_csv_data);$_csv_i++) {
		$_csv_data[$_csv_i]=preg_replace('/^'.$e.'(.*)'.$e.'$/s','$1',$_csv_data[$_csv_i]);
		$_csv_data[$_csv_i]=str_replace($e.$e, $e, $_csv_data[$_csv_i]);
	}
	return empty($_line) ? false : $_csv_data;
}
?>