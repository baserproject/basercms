<?php
/* SVN FILE: $Id$ */
/**
 * AppModel 拡張クラス
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
 * @package			baser.models
 * @since			Baser v 0.1.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
uses('sanitize');
/**
 * AppModel 拡張クラス
 *
 * 既存のCakePHPプロジェクトで、設置済のAppModelと共存できるように、AppModelとは別にした。
 *
 * @package			baser.models
 */
class AppModel extends Model {
/**
 * driver
 *
 * @var		string
 * @access	public
 */
	var $driver = '';
/**
 * プラグイン名
 *
 * @var		string
 * @access	public
 */
	var $plugin = '';
	var $useDbConfig = 'baser';
/**
 * コンストラクタ
 *
 * @return	void
 * @access	private
 */
	function __construct($id = false, $table = null, $ds = null) {

		if($this->useDbConfig && ($this->name || !empty($id['name']))) {

			// DBの設定がない場合、存在しないURLをリクエストすると、エラーが繰り返されてしまい
			// Cakeの正常なエラーページが表示されないので、設定がある場合のみ親のコンストラクタを呼び出す。
			$cm =& ConnectionManager::getInstance();
			if(isset($cm->config->baser['driver'])) {
				if($cm->config->baser['driver'] != '') {
					parent::__construct($id, $table, $ds);
				}elseif($cm->config->baser['login']=='dummy' &&
						$cm->config->baser['password']=='dummy' &&
						$cm->config->baser['database'] == 'dummy' &&
						Configure::read('Baser.urlParam')=='') {
					// データベース設定がインストール段階の状態でトップページへのアクセスの場合、
					// 初期化ページにリダイレクトする
					App::import('Controller','App');
					$AppController = new AppController();
					session_start();
					$_SESSION['Message']['flash'] = array('message'=>'インストールに失敗している可能性があります。<br />インストールを最初からやり直すにはBaserCMSを初期化してください。','layout'=>'default');
					$AppController->redirect(baseUrl().'installations/reset');
				}
			}

		}

	}
/**
 * afterFind
 *
 * @param	mixed	$results
 * @return	mixed	$results
 * @access	public
 */
	function afterFind($results) {

		/* データベース文字コードを内部文字コードに変換 */
		// MySQL4.0 以下で動作
		if($this->driver == 'mysql' && mysql_get_server_info() <= 4.0) {
			$results = $this->convertEncodingByArray($results, mb_internal_encoding(), Configure::read('Config.dbCharset'));
		}
		return $results;

	}
/**
 * beforeSave
 *
 * @return	boolean
 * @access	public
 */
	function beforeSave($options) {

		$result = parent::beforeSave($options);

		// 日付フィールドが空の場合、nullを保存する
		foreach ($this->_schema as $key => $field) {
			if (('date' == $field['type'] ||
							'datetime' == $field['type'] ||
							'time' == $field['type']) &&
					isset($this->data[$this->name][$key])) {
				if ($this->data[$this->name][$key] == '') {
					$this->data[$this->name][$key] = null;
				}
			}
		}

		/* 内部文字コードをデータベース文字コードに変換 */
		// MySQL4.0 以下で動作
		if($this->driver == 'mysql' && mysql_get_server_info() <= 4.0) {
			$this->data = $this->convertEncodingByArray($this->data, Configure::read('Config.dbCharset'), mb_internal_encoding());
		}
		return $result;

	}
/**
 * Saves model data to the database. By default, validation occurs before save.
 *
 * @param	array	$data Data to save.
 * @param	boolean	$validate If set, validation will be done before the save
 * @param	array	$fieldList List of fields to allow to be written
 * @return	mixed	On success Model::$data if its not empty or true, false on failure
 * @access 	public
 */
	function save($data = null, $validate = true, $fieldList = array()) {

		if(!$data)
			$data = $this->data;

		// created,modifiedが更新されないバグ？対応
		if (!$this->__exists) {
			if(isset($data[$this->alias])) {
				$data[$this->alias]['created']=null;
			}else {
				$data['created']=null;
			}
		}
		if(isset($data[$this->alias])) {
			$data[$this->alias]['modified']=null;
		}else {
			$data['modified']=null;
		}

		return parent::save($data, $validate, $fieldList);

	}
/**
 * 配列の文字コードを変換する
 *
 * TODO GLOBAL グローバルな関数として再配置する必要あり
 *
 * @param	array	変換前のデータ
 * @param	string	変換元の文字コード
 * @param	string 	変換後の文字コード
 * @return	array	変換後のデータ
 * @access 	public
 */
	function convertEncodingByArray($data, $outenc ,$inenc) {
		foreach($data as $key=>$value) {
			if(is_array($value)) {
				$data[$key] = $this->convertEncodingByArray($value, $outenc, $inenc);
			} else {
				if (mb_detect_encoding($value) <> $outenc) {
					$data[$key] = mb_convert_encoding($value, $outenc, $inenc);
				}
			}
		}
		return $data;
	}
/**
 * データベースログを記録する
 *
 * @param 	string	$message
 * @return	boolean
 * @access	public
 */
	function saveDbLog($message) {

		// ログを記録する
		App::import('Model', 'Dblog');
		$Dblog = new Dblog();
		$logdata['Dblog']['name'] = $message;
		return $Dblog->save($logdata);

	}
/**
 * フォームの初期値を設定する
 *
 * 継承先でオーバーライドする事
 *
 * @return 	array
 * @access	public
 */
	function getDefaultValue() {
		return array();
	}
/**
 * コントロールソースを取得する
 *
 * 継承先でオーバーライドする事
 *
 * @return 	array
 * @access	public
 */
	function getControlSources() {
		return array();
	}
/**
 * 子カテゴリのIDリストを取得する
 *
 * treeビヘイビア要
 *
 * @param	mixed	$id
 * @return 	array
 * @access	public
 */
	function getChildIdsList($id) {

		$ids = array();
		if($this->childcount($id)) {
			$children = $this->children($id);
			foreach($children as $child) {
				$ids[] = (int)$child[$this->name]['id'];
			}
		}
		return $ids;
	}
/**
 * 機種依存文字の変換処理
 *
 * 内部文字コードがUTF-8である必要がある。
 * 多次元配列には対応していない。
 *
 * @param	string	変換対象文字列
 * @return	string	変換後文字列
 * @access	public
 * TODO AppExModeに移行すべきかも
 */
	function replaceText($str) {

		$ret = $str;
		$arr = array(
				"\xE2\x85\xA0" => "I",
				"\xE2\x85\xA1" => "II",
				"\xE2\x85\xA2" => "III",
				"\xE2\x85\xA3" => "IV",
				"\xE2\x85\xA4" => "V",
				"\xE2\x85\xA5" => "VI",
				"\xE2\x85\xA6" => "VII",
				"\xE2\x85\xA7" => "VIII",
				"\xE2\x85\xA8" => "IX",
				"\xE2\x85\xA9" => "X",
				"\xE2\x85\xB0" => "i",
				"\xE2\x85\xB1" => "ii",
				"\xE2\x85\xB2" => "iii",
				"\xE2\x85\xB3" => "iv",
				"\xE2\x85\xB4" => "v",
				"\xE2\x85\xB5" => "vi",
				"\xE2\x85\xB6" => "vii",
				"\xE2\x85\xB7" => "viii",
				"\xE2\x85\xB8" => "ix",
				"\xE2\x85\xB9" => "x",
				"\xE2\x91\xA0" => "(1)",
				"\xE2\x91\xA1" => "(2)",
				"\xE2\x91\xA2" => "(3)",
				"\xE2\x91\xA3" => "(4)",
				"\xE2\x91\xA4" => "(5)",
				"\xE2\x91\xA5" => "(6)",
				"\xE2\x91\xA6" => "(7)",
				"\xE2\x91\xA7" => "(8)",
				"\xE2\x91\xA8" => "(9)",
				"\xE2\x91\xA9" => "(10)",
				"\xE2\x91\xAA" => "(11)",
				"\xE2\x91\xAB" => "(12)",
				"\xE2\x91\xAC" => "(13)",
				"\xE2\x91\xAD" => "(14)",
				"\xE2\x91\xAE" => "(15)",
				"\xE2\x91\xAF" => "(16)",
				"\xE2\x91\xB0" => "(17)",
				"\xE2\x91\xB1" => "(18)",
				"\xE2\x91\xB2" => "(19)",
				"\xE2\x91\xB3" => "(20)",
				"\xE3\x8A\xA4" => "(上)",
				"\xE3\x8A\xA5" => "(中)",
				"\xE3\x8A\xA6" => "(下)",
				"\xE3\x8A\xA7" => "(左)",
				"\xE3\x8A\xA8" => "(右)",
				"\xE3\x8D\x89" => "ミリ",
				"\xE3\x8D\x8D" => "メートル",
				"\xE3\x8C\x94" => "キロ",
				"\xE3\x8C\x98" => "グラム",
				"\xE3\x8C\xA7" => "トン",
				"\xE3\x8C\xA6" => "ドル",
				"\xE3\x8D\x91" => "リットル",
				"\xE3\x8C\xAB" => "パーセント",
				"\xE3\x8C\xA2" => "センチ",
				"\xE3\x8E\x9D" => "cm",
				"\xE3\x8E\x8F" => "kg",
				"\xE3\x8E\xA1" => "m2",
				"\xE3\x8F\x8D" => "K.K.",
				"\xE2\x84\xA1" => "TEL",
				"\xE2\x84\x96" => "No.",
				"\xE3\x8D\xBB" => "平成",
				"\xE3\x8D\xBC" => "昭和",
				"\xE3\x8D\xBD" => "大正",
				"\xE3\x8D\xBE" => "明治",
				"\xE3\x88\xB1" => "(株)",
				"\xE3\x88\xB2" => "(有)",
				"\xE3\x88\xB9" => "(代)",
		);

		return str_replace( array_keys( $arr), array_values( $arr), $str);

	}
/**
 * MysqlDumpファイルでデータベースを初期化
 *
 * 既に存在するテーブルは上書きしない
 *
 * @param	array	データベース設定名
 * @param	string	保存先パス
 * @return 	boolean
 * @access	protected
 */
	function initDatabase($dbConfigName,$fileName) {

		$dbConfigs = new DATABASE_CONFIG();
		$dbConfig = $dbConfigs->{$dbConfigName};

		if ($dbConfig['driver'] == 'sqlite3' || $dbConfig['driver'] == 'sqlite3_ex') {
			$dbType = 'sqlite';
		} elseif ($dbConfig['driver'] == 'postgres_ex') {
			$dbType = 'postgres';
		} elseif ($dbConfig['driver'] == 'mysql_ex') {
			$dbType = 'mysql';
		} else {
			$dbType = $dbConfig['driver'];
		}

		if($dbType != 'csv') {

			if($dbConfigName == 'plugin') {  // プラグイン
				if(file_exists(APP.'plugins'.DS.$fileName.DS.'config'.DS.'sql'.DS.$fileName.'_'.$dbType.'.sql')) {
					$filePath = APP.'plugins'.DS.$fileName.DS.'config'.DS.'sql'.DS.$fileName.'_'.$dbType.'.sql';
				}elseif(file_exists(BASER_PLUGINS.$fileName.DS.'config'.DS.'sql'.DS.$fileName.'_'.$dbType.'.sql')) {
					$filePath = BASER_PLUGINS.$fileName.DS.'config'.DS.'sql'.DS.$fileName.'_'.$dbType.'.sql';
				}
			}else {  // etc
				if(file_exists(CONFIGS.'sql'.DS.$fileName.'_'.$dbType.'.sql')) {
					$filePath = CONFIGS.'sql'.DS.$fileName.'_'.$dbType.'.sql';
				}elseif(file_exists(BASER_CONFIGS.'sql'.DS.$fileName.'_'.$dbType.'.sql')) {
					$filePath = BASER_CONFIGS.'sql'.DS.$fileName.'_'.$dbType.'.sql';
				}
			}

			if(!empty($filePath)) {
				return $this->restoreDb($dbConfig,$filePath);
			}else {
				return true;
			}

		} elseif ($dbType == 'csv') {  // CSV

			if($dbConfigName == 'plugin') {  // プラグイン
				if(file_exists(APP.'plugins'.DS.$fileName.DS.'config'.DS.'csv'.DS.$fileName.DS)) {
					$filePath = APP.'plugins'.DS.$fileName.DS.'config'.DS.'csv'.DS.$fileName.DS;
				}elseif(file_exists(BASER_PLUGINS.$fileName.DS.'config'.DS.'csv'.DS.$fileName.DS)) {
					$filePath = BASER_PLUGINS.$fileName.DS.'config'.DS.'csv'.DS.$fileName.DS;
				}
			}else {  // etc
				if(file_exists(CONFIGS.'csv'.DS.$fileName.DS)) {
					$filePath = CONFIGS.'csv'.DS.$fileName.DS;
				}elseif(file_exists(BASER_CONFIGS.'csv'.DS.$fileName.DS)) {
					$filePath = BASER_CONFIGS.'csv'.DS.$fileName.DS;
				}
			}

			if(!empty($filePath)) {
				return $this->restoreDb($dbConfig,$filePath);
			}else {
				return true;
			}

		}

	}
/**
 * データベースを復元する
 * 既にあるテーブルは上書きしない
 * @param array $config
 * @param string $source
 */
	function restoreDb($config, $source) {

		App::import('Vendor','DbRestore',array('file'=>'dbrestore.php'));
		$dbType = preg_replace('/_ex$/i','',$config['driver']);
		switch ($dbType) {
			case 'mysql':
				$connection = @mysql_connect($config['host'],$config['login'],$config['password']);
				$sql = "SET NAMES ".Configure::read('internalEncodingByMySql');
				mysql_query($sql);
				$dbRestore = new DbRestore('mysql');
				$dbRestore->connect($config['database'], $config['host'], $config['login'], $config['password'],$config['port']);
				return $dbRestore->doRestore($source);
				break;

			case 'postgres':
				$dbRestore = new DbRestore('postgres');
				$dbRestore->connect($config['database'], $config['host'], $config['login'], $config['password'],$config['port']);
				return $dbRestore->doRestore($source);
				break;

			case 'sqlite':
			case 'sqlite3':
				if($config['driver']=='sqlite3_ex') {
					$driver = 'sqlite3';
				}else {
					$driver = $config['driver'];
				}
				$dbRestore = new DbRestore($driver);
				$dbRestore->connect($config['database']);
				return $dbRestore->doRestore($source);
				break;

			case 'csv':
				$targetDir = APP.'db'.DS.'csv'.DS.'baser'.DS;
				$folder = new Folder($source);
				$files = $folder->read(true,true);
				$ret = true;
				foreach($files[1] as $file) {
					if($file != 'empty' && $ret) {
						if (!file_exists($targetDir.$config['prefix'].$file)) {
							$_ret = copy($source.$file,$targetDir.$config['prefix'].$file);
							if ($_ret) {
								chmod($targetDir.$config['prefix'].$file,0666);
							}else {
								$ret = $_ret;
							}
						}
					}
				}
				return $ret;
				break;
		}

	}
/**
 * 最短の長さチェック
 *
 * @param mixed	$check
 * @param int	$min
 * @return boolean
 * @access public
 */
	function minLength($check, $min) {
		$check=(is_array($check))?current($check):$check;
		$length = mb_strlen($check,Configure::read('App.encoding'));
		return ($length >= $min);
	}
/**
 * 最長の長さチェック
 *
 * @param mixed	$check
 * @param int	$max
 * @param boolean
 * @access public
 */
	function maxLength($check, $max) {
		$check=(is_array($check))?current($check):$check;
		$length = mb_strlen($check,Configure::read('App.encoding'));
		return ($length <= $max);
	}
/**
 * 範囲を指定しての長さチェック
 *
 * @param mixed	$check
 * @param int	$min
 * @param int	$max
 * @param boolean
 * @access public
 */
	function between($check, $min, $max) {
		$check=(is_array($check))?current($check):$check;
		$length = mb_strlen($check,Configure::read('App.encoding'));
		return ($length >= $min && $length <= $max);
	}
/**
 * 指定フィールドのMAX値を取得する
 *
 * 現在数値フィールドのみ対応
 *
 * @param string $field
 * @param array $conditions
 * @return int
 */
	function getMax($field,$conditions=array()) {

		if(strpos($field,'.') === false) {
			$modelName = $this->alias;
		}else {
			list($modelName,$field) = split('\.',$field);
		}

		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$this->recursive = -1;
		if($db->config['driver']=='csv') {
			// CSVDBの場合はMAX関数が利用できない為、プログラムで処理する
			// TODO dboでMAX関数の実装できたらここも変更する
			$this->cacheQueries=false;
			$dbDatas = $this->find('all',array('conditions'=>$conditions,'fields'=>array($modelName.'.'.$field)));
			$this->cacheQueries=true;
			$max = 0;
			if($dbDatas) {
				foreach($dbDatas as $dbData) {
					if($max < $dbData[$modelName][$field]) {
						$max = $dbData[$modelName][$field];
					}
				}
			}
			return $max;
		}else {
			$this->cacheQueries=false;
			// SQLiteの場合、Max関数にmodel名を含むと、戻り値の添字が崩れる（CakePHPのバグ）
			$dbData = $this->find('all',array('conditions'=>$conditions,'fields'=>array('MAX('.$field.')')));
			$this->cacheQueries=true;
			if(isset($dbData[0][0]['MAX('.$field.')'])) {
				return $dbData[0][0]['MAX('.$field.')'];
			}elseif(isset($dbData[0][0]['max'])) {
				return $dbData[0][0]['max'];
			}else {
				return 0;
			}
		}
	}
/**
 * テーブルにフィールドを追加する
 * @param string $addField
 * @param array $column
 * @return boolean
 * @access public
 */
	function addField($addFieldName,$column) {
		$this->_schema=null;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$ret = $db->addColumn($this,$addFieldName,$column);
		$this->deleteModelCache();
		return $ret;
	}
/**
 * フィールドを変更する
 * @param string $oldFieldName
 * @param string $newFieldName
 * @param array $column
 * @return boolean
 * @access public
 */
	function editField($oldFieldName,$newFieldName,$column=null) {
		$this->_schema=null;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$ret = $db->editColumn($this,$oldFieldName,$newFieldName,$column);
		$this->deleteModelCache();
		return $ret;
	}
/**
 * フィールドを削除する
 * @param string $delFieldName
 * @return boolean
 * @access public
 */
	function deleteField($delFieldName) {
		$this->_schema=null;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$ret = $db->deleteColumn($this,$delFieldName);
		$this->deleteModelCache();
		return $ret;
	}
/**
 * テーブルの存在チェックを行う
 * @param string $tableName
 * @return boolean
 */
	function tableExists ($tableName) {
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$tables = $db->listSources();
		return in_array($tableName, $tables);
	}
/**
 * 英数チェック
 *
 * @param	string	チェック対象文字列
 * @return	boolean
 * @access	public
 */
	function alphaNumeric($check) {

		if(!$check[key($check)]) {
			return true;
		}
		if(preg_match("/^[a-zA-Z0-9]+$/",$check[key($check)])) {
			return true;
		}else {
			return false;
		}

	}
/**
 * データの重複チェックを行う
 * @param array $check
 * @return boolean
 */
	function duplicate($check,$field) {

		$conditions = array($this->name.'.'.key($check)=>$check[key($check)]);
		if($this->exists()) {
			$conditions['NOT'] = array($this->name.'.id'=>$this->id);
		}
		$ret = $this->find($conditions);
		if($ret) {
			return false;
		}else {
			return true;
		}
	}
/**
 * ファイルサイズチェック
 */
	function fileSize($check,$size) {
		$file = $check[key($check)];
		if(!empty($file['name'])) {
			// サイズが空の場合は、HTMLのMAX_FILE_SIZEの制限によりサイズオーバー
			if(!$file['size']) return false;
			if($file['size']>$size) return;
		}
		return true;
	}
/**
 * 半角チェック
 * @param array $check
 * @return boolean
 */
	function halfText($check) {
		$value = $check[key($check)];
		$len = strlen($value);
		$mblen = mb_strlen($value,'UTF-8');
		if($len != $mblen) {
			return false;
		}
		return true;
	}
/**
 * 一つ位置を上げる
 * @param string	$id
 * @param array		$conditions
 * @return boolean
 */
	function sortup($id,$conditions) {
		return $this->changeSort($id,-1,$conditions);
	}
/**
 * 一つ位置を下げる
 * @param string	$id
 * @param array		$conditions
 * @return boolean
 */
	function sortdown($id,$conditions) {
		return $this->changeSort($id,1,$conditions);
	}
/**
 * 並び順を変更する
 * @param string	$id
 * @param int			$offset
 * @param array		$conditions
 * @return boolean
 */
	function changeSort($id,$offset,$conditions) {

		// 一時的にキャッシュをOFFする
		$this->cacheQueries = false;

		$current = $this->find(array($this->alias.'.id'=>$id),array($this->alias.'.id',$this->alias.'.sort'));

		// 変更相手のデータを取得
		if($offset > 0) {	// DOWN
			$order = array($this->alias.'.sort');
			$limit = $offset;
			$conditions[$this->alias.'.sort >'] = $current[$this->alias]['sort'];
		}elseif($offset < 0) {	// UP
			$order = array($this->alias.'.sort DESC');
			$limit = $offset * -1;
			$conditions[$this->alias.'.sort <'] = $current[$this->alias]['sort'];
		}else {
			return true;
		}

		$target = $this->find('all',array('conditions'=>$conditions,
				'fields'=>array($this->alias.'.id',$this->alias.'.sort'),
				'order'=>$order,
				'limit'=>$limit,
				'recursive'=>-1));

		if(!isset($target[0])) {
			return false;
		}else {
			$target = $target[0];
		}

		$currentSort = $current[$this->alias]['sort'];
		$targetSort = $target[$this->alias]['sort'];

		$current[$this->alias]['sort'] = $targetSort;
		$target[$this->alias]['sort'] = $currentSort;

		$this->save($current,false);
		$this->save($target,false);

		return true;

	}
/**
 * Modelキャッシュを削除する
 * @return void
 * @access public
 */
	function deleteModelCache() {
		App::import('Core','Folder');
		$folder = new Folder(CACHE.'models'.DS);
		$caches = $folder->read(true,true);
		foreach($caches[1] as $cache) {
			if(basename($cache) != 'empty') {
				@unlink(CACHE.'models'.DS.$cache);
			}
		}
	}
}
?>