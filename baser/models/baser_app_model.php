<?php
/* SVN FILE: $Id$ */
/**
 * Model 拡張クラス
 *
 * PHP versions 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2013, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2013, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			baser.models
 * @since			baserCMS v 0.1.0
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
 * Model 拡張クラス
 *
 * 既存のCakePHPプロジェクトで、設置済のAppModelと共存できるように、AppModelとは別にした。
 *
 * @package			baser.models
 */
class BaserAppModel extends Model {
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
/**
 * DB接続設定名
 *
 * @var string
 * @access public
 */
	var $useDbConfig = 'baser';
/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	var $actsAs = array('BcPluginHook');
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
						Configure::read('BcRequest.pureUrl')=='') {
					// データベース設定がインストール段階の状態でトップページへのアクセスの場合、
					// 初期化ページにリダイレクトする
					App::import('Controller','App');
					$AppController = new AppController();
					session_start();
					$_SESSION['Message']['flash'] = array('message'=>'インストールに失敗している可能性があります。<br />インストールを最初からやり直すにはbaserCMSを初期化してください。','layout'=>'default');
					$AppController->redirect(BC_BASE_URL.'installations/reset');
				}
			}

		}
		
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
		if (!$this->exists()) {
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
		$logdata['Dblog']['user_id'] = @$_SESSION['Auth']['User']['id'];
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
	function getControlSource($field) {
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
 * データベースを初期化
 *
 * 既に存在するテーブルは上書きしない
 *
 * @param	array	データベース設定名
 * @param	string	プラグイン名
 * @return 	boolean
 * @access	public
 */
	function initDb($dbConfigName, $pluginName = '', $loadCsv = true, $filterTable = '', $filterType = '') {

		// 初期データフォルダを走査
		if(!$pluginName) {
			$path = BASER_CONFIGS.'sql';
		} else {
			$schemaPaths = array(
				APP.'plugins'.DS.$pluginName.DS.'config'.DS.'sql',
				BASER_PLUGINS.$pluginName.DS.'config'.DS.'sql'
			);
			$path = '';
			foreach($schemaPaths as $schemaPath) {
				if(is_dir($schemaPath)) {
					$path = $schemaPath;
					break;
				}
			}
			if(!$path) {
				return true;
			}
		}

		if($this->loadSchema($dbConfigName, $path, $filterTable, $filterType, array(), $dropField = false)){
			$dataPaths = array(
				APP.'plugins'.DS.$pluginName.DS.'config'.DS.'data'.DS.'default',
				APP.'plugins'.DS.$pluginName.DS.'config'.DS.'sql',
				BASER_PLUGINS.$pluginName.DS.'config'.DS.'data'.DS.'default'
			);
			$path = '';
			foreach($dataPaths as $dataPath) {
				if(is_dir($dataPath)) {
					$path = $dataPath;
					break;
				}
			}
			if(!$path) {
				return true;
			}
			if($loadCsv) {
				return $this->loadCsv($dbConfigName, $path);
			} else {
				return true;
			}
		} else {
			return false;
		}

	}
/**
 * スキーマファイルを利用してデータベース構造を変更する
 *
 * @param	array	データベース設定名
 * @param	string	スキーマファイルのパス
 * @param	string	テーブル指定
 * @param	string	更新タイプ指定
 * @return 	boolean
 * @access	public
 */
	function loadSchema($dbConfigName, $path, $filterTable='', $filterType='', $excludePath = array(), $dropField = true) {

		// テーブルリストを取得
		$db =& ConnectionManager::getDataSource($dbConfigName);
		$db->cacheSources = false;
		$listSources = $db->listSources();
		$prefix = $db->config['prefix'];
		$Folder = new Folder($path);
		$files = $Folder->read(true, true);

		$result = true;
		
		foreach($files[1] as $file) {
			if(in_array($file, $excludePath)) {
				continue;
			}
			if(preg_match('/^(.*?)\.php$/', $file, $matches)) {
				$type = 'create';
				$table = $matches[1];
				if(preg_match('/^create_(.*?)\.php$/', $file, $matches)) {
					$type = 'create';
					$table = $matches[1];
					if(in_array($prefix . $table, $listSources)) {
						continue;
					}
				} elseif (preg_match('/^alter_(.*?)\.php$/', $file, $matches)) {
					$type = 'alter';
					$table = $matches[1];
					if(!in_array($prefix . $table, $listSources)) {
						continue;
					}
				} elseif (preg_match('/^drop_(.*?)\.php$/', $file, $matches)) {
					$type = 'drop';
					$table = $matches[1];
					if(!in_array($prefix . $table, $listSources)) {
						continue;
					}
				} else {
					if(in_array($prefix . $table, $listSources)) {
						continue;
					}
				}
				if($filterTable && $filterTable != $table) {
					continue;
				}
				if($filterType && $filterType != $type) {
					continue;
				}
				$tmpdir = TMP.'schemas'.DS;
				copy($path.DS.$file,$tmpdir.$table.'.php');
				if(!$db->loadSchema(array('type'=>$type, 'path' => $tmpdir, 'file'=> $table.'.php', 'dropField' => $dropField))) {
					$result = false;
				}
				@unlink($tmpdir.$table.'.php');

			}
			
		}
		
		clearAllCache();
		return $result;

	}
/**
 * CSVを読み込む
 *
 * @param	array	データベース設定名
 * @param	string	CSVパス
 * @param	string	テーブル指定
 * @return 	boolean
 * @access	public
 */
	function loadCsv($dbConfigName, $path, $filterTable='') {

		// テーブルリストを取得
		$db =& ConnectionManager::getDataSource($dbConfigName);
		$db->cacheSources = false;
		$listSources = $db->listSources();
		$prefix = $db->config['prefix'];
		$Folder = new Folder($path);
		$files = $Folder->read(true, true);

		foreach($files[1] as $file) {
			if (preg_match('/^(.*?)\.csv$/', $file, $matches)) {
				$table = $matches[1];
				if(in_array($prefix . $table, $listSources)) {
					if($filterTable && $filterTable != $table) {
						continue;
					}
					if(!$db->loadCsv(array('path'=>$path.DS.$file, 'encoding'=>'SJIS'))){
						return false;
					}
				}
			}
		}

		return true;

	}
/**
 * データベースを復元する
 * 既にあるテーブルは上書きしない
 * @param array $config
 * @param string $source
 */
	function restoreDb($config, $source) {

		App::import('Vendor','DbRestore',array('file'=>'dbrestore.php'));
		$dbType = preg_replace('/^bc_/', '', $config['driver']);
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
				if($config['driver']=='bc_sqlite3') {
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
			list($modelName,$field) = explode('.',$field);
		}

		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$this->recursive = -1;
		if($db->config['driver']=='bc_csv') {
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
 *
 * @param	array	$options [ field / column / table ]
 * @return	boolean
 * @access	public
 */
	function addField($options) {

		extract($options);

		if(!isset($field) || !isset($column)) {
			return false;
		}

		if(!isset($table)) {
			$table = $this->useTable;
		}

		$this->_schema=null;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$options = array('field'=>$field, 'table'=>$table, 'column'=>$column);
		$ret = $db->addColumn($options);
		$this->deleteModelCache();
		return $ret;

	}
/**
 * フィールド構造を変更する
 *
 * @param	array	$options [ field / column / table ]
 * @return	boolean
 * @access	public
 */
	function editField($options) {

		extract($options);

		if(!isset($field) || !isset($column)) {
			return false;
		}

		if(!isset($table)) {
			$table = $this->useTable;
		}

		$this->_schema = null;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$options = array('field'=>$field,'table'=>$table, 'column'=>$column);
		$ret = $db->changeColumn($options);
		$this->deleteModelCache();
		return $ret;

	}
/**
 * フィールドを削除する
 *
 * @param	array	$options [ field / table ]
 * @return	boolean
 * @access	public
 */
	function delField($options) {

		extract($options);

		if(!isset($field)) {
			return false;
		}

		if(!isset($table)) {
			$table = $this->useTable;
		}

		$this->_schema=null;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$options = array('field'=>$field,'table'=>$table);
		$ret = $db->dropColumn($options);
		$this->deleteModelCache();
		return $ret;

	}
/**
 * フィールド名を変更する
 *
* @param	array	$options [ new / old / table ]
 * @param array $column
 * @return boolean
 * @access public
 */
	function renameField($options) {

		extract($options);

		if(!isset($new) || !isset($old)) {
			return false;
		}

		if(!isset($table)) {
			$table = $this->useTable;
		}

		$this->_schema=null;
		$db =& ConnectionManager::getDataSource($this->useDbConfig);
		$options = array('new'=>$new, 'old'=>$old, 'table'=>$table);
		$ret = $db->renameColumn($options);
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
		$db->cacheSources = false;
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
 * 英数チェックプラス
 *
 * ハイフンアンダースコアを許容
 *
 * @param	string	チェック対象文字列
 * @return	boolean
 * @access	public
 */
	function alphaNumericPlus($check) {

		if(!$check[key($check)]) {
			return true;
		}
		if(preg_match("/^[a-zA-Z0-9\-_]+$/",$check[key($check)])) {
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

		$conditions = array($this->alias.'.'.key($check)=>$check[key($check)]);
		if($this->exists()) {
			$conditions['NOT'] = array($this->alias.'.id'=>$this->id);
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
	function changeSort($id,$offset,$conditions=array()) {

		if($conditions) {
			$_conditions = $conditions;
		} else {
			$_conditions = array();
		}

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

		$conditions = am($conditions,$_conditions);
		$target = $this->find('all',array('conditions'=>$conditions,
				'fields'=>array($this->alias.'.id',$this->alias.'.sort'),
				'order'=>$order,
				'limit'=>$limit,
				'recursive'=>-1));

		if(!isset($target[count($target)-1])) {
			return false;
		}

		$currentSort = $current[$this->alias]['sort'];
		$targetSort = $target[count($target)-1][$this->alias]['sort'];

		// current から target までのデータをsortで範囲指定して取得
		$conditions = array();
		if($offset > 0) {	// DOWN
			$conditions[$this->alias.'.sort >='] = $currentSort;
			$conditions[$this->alias.'.sort <='] = $targetSort;
		}elseif($offset < 0) {	// UP
			$conditions[$this->alias.'.sort <='] = $currentSort;
			$conditions[$this->alias.'.sort >='] = $targetSort;
		}
		$conditions = am($conditions,$_conditions);
		$datas = $this->find('all',array('conditions'=>$conditions,
				'fields'=>array($this->alias.'.id',$this->alias.'.sort'),
				'order'=>$order,
				'recursive'=>-1));

		// 全てのデータを更新
		foreach ($datas as $data) {
			if($data[$this->alias]['sort'] == $currentSort) {
				$data[$this->alias]['sort'] = $targetSort;
			} else {
				if($offset > 0) {
					$data[$this->alias]['sort']--;
				}elseif($offset < 0) {
					$data[$this->alias]['sort']++;
				}
			}
			if(!$this->save($data,false)){
				return false;
			}
		}

		return true;

	}
/**
 * Modelキャッシュを削除する
 * @return void
 * @access public
 */
	function deleteModelCache() {
		$this->_schema = null;
		App::import('Core','Folder');
		$folder = new Folder(CACHE.'models'.DS);
		$caches = $folder->read(true,true);
		foreach($caches[1] as $cache) {
			if(basename($cache) != 'empty') {
				@unlink(CACHE.'models'.DS.$cache);
			}
		}
	}
/**
 * Key Value 形式のテーブルよりデータを取得して
 * １レコードとしてデータを展開する
 * @return array
 */
	function findExpanded() {

		$dbDatas = $this->find('all',array('fields'=>array('name','value')));
		$expandedData = array();
		if($dbDatas) {
			foreach($dbDatas as $dbData) {
				$expandedData[$dbData[$this->alias]['name']] = $dbData[$this->alias]['value'];
			}
		}
		return $expandedData;

	}
/**
 * Key Value 形式のテーブルにデータを保存する
 * @param	array	$data
 * @return	boolean
 */
	function saveKeyValue($data) {

		if(isset($data[$this->alias])) {
			$data = $data[$this->alias];
		}

		$result = true;
		if($this->Behaviors->attached('BcCache')) {
			$this->Behaviors->disable('BcCache');
		}
		foreach($data as $key => $value) {
			
			if($this->find('count', array('conditions' => array('name' => $key))) > 1) {
				$this->deleteAll(array('name' => $key));
			}
			
			$dbData = $this->find('first', array('conditions'=>array('name'=>$key)));

			if(!$dbData) {
				$dbData = array();
				$dbData[$this->alias]['name'] = $key;
				$dbData[$this->alias]['value'] = $value;
				$this->create($dbData);
			}else {
				$dbData[$this->alias]['value'] = $value;
				$this->set($dbData);
			}

			// SQliteの場合、トランザクション用の関数をサポートしていない場合があるので、
			// 個別に保存するようにした。
			if(!$this->save(null,false)) {
				$result = false;
			}

		}
		
		if($this->Behaviors->attached('BcCache')) {
			$this->Behaviors->enable('BcCache');
			$this->delCache();
		}
		
		return true;

	}
/**
 * リストチェック
 * リストに含む場合はエラー
 *
 * @param string $check Value to check
 * @param array $list List to check against
 * @return boolean Succcess
 * @access public
 */
	function notInList($check, $list) {
		return !in_array($check[key($check)], $list);
	}
/**
 * Deconstructs a complex data type (array or object) into a single field value.
 *
 * @param string $field The name of the field to be deconstructed
 * @param mixed $data An array or object to be deconstructed into a field
 * @return mixed The resulting data that should be assigned to a field
 * @access public
 */
	function deconstruct($field, $data) {
		if (!is_array($data)) {
			return $data;
		}

		$copy = $data;
		$type = $this->getColumnType($field);

		// >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	和暦対応
		// メールフォームで生成するフィールドは全てテキストの為（暫定）
		//if (in_array($type, array('datetime', 'timestamp', 'date', 'time'))) {
		// ---
		if (in_array($type, array('string', 'text', 'datetime', 'timestamp', 'date', 'time'))) {
		// <<<
			$useNewDate = (isset($data['year']) || isset($data['month']) ||
				isset($data['day']) || isset($data['hour']) || isset($data['minute']));

			// >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	和暦対応
			//$dateFields = array('Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'min', 's' => 'sec');
			// ---
			$dateFields = array('W'=>'wareki', 'Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'min', 's' => 'sec');
			// <<<

			$timeFields = array('H' => 'hour', 'i' => 'min', 's' => 'sec');

			// >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	和暦対応
			// メールフォームで生成するフィールドは全てテキストの為（暫定）
			//$db =& ConnectionManager::getDataSource($this->useDbConfig);
			//$format = $db->columns[$type]['format'];
			// ---
			if($type != 'text' && $type != 'string') {
				$db =& ConnectionManager::getDataSource($this->useDbConfig);
				$format = $db->columns[$type]['format'];
			} else {
				$format = 'Y-m-d H:i:s';
			}
			// <<<
			$date = array();

			if (isset($data['hour']) && isset($data['meridian']) && $data['hour'] != 12 && 'pm' == $data['meridian']) {
				$data['hour'] = $data['hour'] + 12;
			}
			if (isset($data['hour']) && isset($data['meridian']) && $data['hour'] == 12 && 'am' == $data['meridian']) {
				$data['hour'] = '00';
			}
			if ($type == 'time') {
				foreach ($timeFields as $key => $val) {
					if (!isset($data[$val]) || $data[$val] === '0' || $data[$val] === '00') {
						$data[$val] = '00';
					} elseif ($data[$val] === '') {
						$data[$val] = '';
					} else {
						$data[$val] = sprintf('%02d', $data[$val]);
					}
					if (!empty($data[$val])) {
						$date[$key] = $data[$val];
					} else {
						return null;
					}
				}
			}

			// >>> CUSTOMIZE MODIFY 2011/01/11 ryuring	和暦対応
			// メールフォームで生成するフィールドは全てテキストの為（暫定）
			//if ($type == 'datetime' || $type == 'timestamp' || $type == 'date') {
			// ---
			if ($type == 'text' || $type == 'string' || $type == 'datetime' || $type == 'timestamp' || $type == 'date') {
			// <<<
				foreach ($dateFields as $key => $val) {
					if ($val == 'hour' || $val == 'min' || $val == 'sec') {
						if (!isset($data[$val]) || $data[$val] === '0' || $data[$val] === '00') {
							$data[$val] = '00';
						} else {
							$data[$val] = sprintf('%02d', $data[$val]);
						}
					}
					// >>> CUSTOMIZE ADD 2011/01/11 ryuring	和暦対応
					if($val == 'wareki' && !empty($data['wareki'])) {
						$warekis = array('m'=>1867, 't'=>1911, 's'=>1925, 'h'=>1988);
						if(!empty($data['year'])) {
							list($wareki, $year) = explode('-', $data['year']);
							$data['year'] = $year + $warekis[$wareki];
						}
					}
					// <<<
					
					// >>> CUSTOMIZE MODIFY 2011/02/17 ryuring 和暦対応
					// if (!isset($data[$val]) || isset($data[$val]) && (empty($data[$val]) || $data[$val][0] === '-')) {
					//	return null;
					// ---
					if ($val != 'wareki' && (!isset($data[$val]) || isset($data[$val]) && (empty($data[$val]) || $data[$val][0] === '-'))) {
						if($type != 'text' && $type != 'string') {
							return null;
						} else {
							return $data;
						}
					// <<<
					}
					if (isset($data[$val]) && !empty($data[$val])) {
						$date[$key] = $data[$val];
					}
				}
			}
			$date = str_replace(array_keys($date), array_values($date), $format);
			if ($useNewDate && !empty($date)) {
				return $date;
			}
		}
		return $data;
	}
/**
 * ２つのフィールド値を確認する
 *
 * @param	array	$check
 * @param	mixed	$fields
 * @return	boolean
 * @access	public
 */
	function confirm($check, $fields) {

		$value1 = $value2 = '';
		if(is_array($fields) && count($fields) > 1) {
			if(isset($this->data[$this->alias][$fields[0]]) && 
					isset($this->data[$this->alias][$fields[1]])) {
				$value1 = $this->data[$this->alias][$fields[0]];
				$value2 = $this->data[$this->alias][$fields[1]];
			}
		} elseif($fields) {
			if(isset($check[key($check)]) && isset($this->data[$this->alias][$fields])) {
				$value1 = $check[key($check)];
				$value2 = $this->data[$this->alias][$fields];
			}
		} else {
			return false;
		}

		if($value1 != $value2) {
			return false;
		}
		return true;

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

		return parent::afterFind($results);

	}
/**
 * Unbinds all relations from a model
 *
 * @param string unbinds all related models.
 * @return void
 * @access public
 */
    function expects($arguments, $reset = true) {
		
        $models = array();

        foreach($arguments as $index => $argument)
        {
            if (is_array($argument))
            {
                if (count($argument) > 0)
                {
                    $arguments = am($arguments, $argument);
                }

                unset($arguments[$index]);
            }
        }

        foreach($arguments as $index => $argument)
        {
            if (!is_string($argument))
            {
                unset($arguments[$index]);
            }
        }

        if (count($arguments) == 0)
        {
            $models[$this->name] = array();
        }
        else
        {
            foreach($arguments as $argument)
            {
                if (strpos($argument, '.') !== false)
                {
                    $model = substr($argument, 0, strpos($argument, '.'));
                    $child = substr($argument, strpos($argument, '.') + 1);

                    if ($child == $model)
                    {
                        $models[$model] = array();
                    }
                    else
                    {
                        $models[$model][] = $child;
                    }
                }
                else
                {
                    $models[$this->name][] = $argument;
                }
            }
        }

        $relationTypes = array ('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');

        foreach($models as $bindingName => $children)
        {
            $model = null;

            foreach($relationTypes as $relationType)
            {
                $currentRelation = (isset($this->$relationType) ? $this->$relationType : null);

                if (isset($currentRelation) && isset($currentRelation[$bindingName]) && is_array($currentRelation[$bindingName]) && isset($currentRelation[$bindingName]['className']))
                {
                    $model = $currentRelation[$bindingName]['className'];
                    break;
                }
            }

            if (!isset($model))
            {
                $model = $bindingName;
            }

            if (isset($model) && $model != $this->name && isset($this->$model))
            {
                if (!isset($this->__backInnerAssociation))
                {
                    $this->__backInnerAssociation = array();
                }

                $this->__backInnerAssociation[] = $model;

                $this->$model->expects(true, $children);
            }
        }

        if (isset($models[$this->name]))
        {
            foreach($models as $model => $children)
            {
                if ($model != $this->name)
                {
                    $models[$this->name][] = $model;
                }
            }

            $models = array_unique($models[$this->name]);
            $unbind = array();

            foreach($relationTypes as $relation)
            {
                if (isset($this->$relation))
                {
                    foreach($this->$relation as $bindingName => $bindingData)
                    {
                        if (!in_array($bindingName, $models))
                        {
                            $unbind[$relation][] = $bindingName;
                        }
                    }
                }
            }
            if (count($unbind) > 0)
            {
                $this->unbindModel($unbind, $reset);
            }
        }
		
    }
/**
 * 複数のEメールチェック（カンマ区切り）
 * 
 * @param array $check
 * @return boolean 
 */
	function emails($check) {
		
		$Validation =& Validation::getInstance();
		$emails = array();
		if(strpos($check[key($check)], ',') !== false) {
			$emails = explode(',', $check[key($check)]);
		}
		if(!$emails) {
			$emails = array($check[key($check)]);
		}
		$result = true;
		foreach($emails as $email) {
			if(!$Validation->email($email)) {
				$result = false;
			}
		}
		
		return $result;
		
	}
/**
 * find
 * 
 * キャッシュビヘイビアが利用状態の場合、モデルデータキャッシュを読み込む
 * 
 * 【AppModelではキャッシュを定義しない事】
 * 自動的に生成されるクラス定義のない関連モデルの処理で勝手にキャッシュを利用されないようにする為
 * （HABTMの更新がうまくいかなかったので）
 * 
 * 【PHP5限定】
 * PHP4では次のような処理ができない為
 * Model ⇒ Behavior ⇒ call_user_func_array ⇒ Model ⇒ Behavior
 * ※ ビヘイビア内で自モデルのメソッドを呼び出しそのメソッド内でさらにビヘイビアを使う
 *
 * @param mixed...
 * @return type 
 * @access public
 */
	function find($conditions = null, $fields = array(), $order = null, $recursive = null) {
		
		if (!is_string($conditions) || (is_string($conditions) && !array_key_exists($conditions, $this->_findMethods))) {
			$type = 'first';
			$query = array_merge(compact('conditions', 'fields', 'order', 'recursive'), array('limit' => 1));
		} else {
			list($type, $query) = array($conditions, $fields);
		}

		$this->findQueryType = $type;
		$this->id = $this->getID();

		$query = array_merge(
			array(
				'conditions' => null, 'fields' => null, 'joins' => array(), 'limit' => null,
				'offset' => null, 'order' => null, 'page' => null, 'group' => null, 'callbacks' => true
			),
			(array)$query
		);

		if ($type != 'all') {
			if ($this->_findMethods[$type] === true) {
				$query = $this->{'_find' . ucfirst($type)}('before', $query);
			}
		}

		if (!is_numeric($query['page']) || intval($query['page']) < 1) {
			$query['page'] = 1;
		}
		if ($query['page'] > 1 && !empty($query['limit'])) {
			$query['offset'] = ($query['page'] - 1) * $query['limit'];
		}
		if ($query['order'] === null && $this->order !== null) {
			$query['order'] = $this->order;
		}
		$query['order'] = array($query['order']);

		if ($query['callbacks'] === true || $query['callbacks'] === 'before') {
			$return = $this->Behaviors->trigger($this, 'beforeFind', array($query), array(
				'break' => true, 'breakOn' => false, 'modParams' => true
			));
			$query = (is_array($return)) ? $return : $query;

			if ($return === false) {
				return null;
			}

			$return = $this->beforeFind($query);
			$query = (is_array($return)) ? $return : $query;

			if ($return === false) {
				return null;
			}
		}

		// CUSTOMIZE MODIFY 2012/04/23 ryuring
		// データキャッシュ
		// >>>
		/*if (!$db =& ConnectionManager::getDataSource($this->useDbConfig)) {
			return false;
		}
		$results = $db->read($this, $query);*/
		// ---
		$cache = true;
		if(isset($query['cache']) && is_bool($query['cache'])) {
			$cache = $query['cache'];
			unset($query['cache']);
		}
		if (PHP5 && BC_INSTALLED && isset($this->Behaviors) && $this->Behaviors->attached('BcCache') && 
				$this->Behaviors->enabled('BcCache') && Configure::read('debug') == 0 ) {
			$results = $this->readCache($cache, $type, $query);
		} else {
			if (!$db =& ConnectionManager::getDataSource($this->useDbConfig)) {
				return false;
			}
			$results = $db->read($this, $query);
		}
		// <<<
		
		$this->resetAssociations();
		$this->findQueryType = null;

		if ($query['callbacks'] === true || $query['callbacks'] === 'after') {
			$results = $this->__filterResults($results);
		}

		if ($type === 'all') {
			return $results;
		} else {
			if ($this->_findMethods[$type] === true) {
				return $this->{'_find' . ucfirst($type)}('after', $query, $results);
			}
		}
	}
/**
 * Deletes multiple model records based on a set of conditions.
 *
 * @param mixed $conditions Conditions to match
 * @param boolean $cascade Set to true to delete records that depend on this record
 * @param boolean $callbacks Run callbacks (not being used)
 * @return boolean True on success, false on failure
 * @access public
 * @link http://book.cakephp.org/view/692/deleteAll
 */
	function deleteAll($conditions, $cascade = true, $callbacks = false) {
		
		$result = parent::deleteAll($conditions, $cascade, $callbacks);
		if($result) {
			if ($this->Behaviors->attached('BcCache') && $this->Behaviors->enabled('BcCache')) {
				$this->delCache($this);
			}
		}
		return $result;
		
	}
/**
 * Updates multiple model records based on a set of conditions.
 *
 * @param array $fields Set of fields and values, indexed by fields.
 *    Fields are treated as SQL snippets, to insert literal values manually escape your data.
 * @param mixed $conditions Conditions to match, true for all records
 * @return boolean True on success, false on failure
 * @access public
 * @link http://book.cakephp.org/view/75/Saving-Your-Data
 */
	function updateAll($fields, $conditions = true) {
		
		$result = parent::updateAll($fields, $conditions);
		if($result) {
			if ($this->Behaviors->attached('BcCache') && $this->Behaviors->enabled('BcCache')) {
				$this->delCache($this);
			}
		}
		return $result;
		
	}
/**
 * Used to report user friendly errors.
 * If there is a file app/error.php or app/app_error.php this file will be loaded
 * error.php is the AppError class it should extend ErrorHandler class.
 *
 * @param string $method Method to be called in the error class (AppError or ErrorHandler classes)
 * @param array $messages Message that is to be displayed by the error class
 * @return error message
 * @access public
 */
	function cakeError($method, $messages = array()) {
		//======================================================================
		// router.php がロードされる前のタイミング（bootstrap.php）でエラーが発生した場合、
		// AppControllerなどがロードされていない為、Object::cakeError() を実行する事ができない。
		// router.php がロードされる前のタイミングでは、通常のエラー表示を行う
		//======================================================================
		if(!Configure::read('BcRequest.routerLoaded')) {
			trigger_error($method, E_USER_ERROR);
		} else {
			parent::cakeError($method, $messages);
		}
		
	}
/**
 * プラグインフックのイベントを発火させる
 * 
 * @param string $hook
 * @return mixed 
 */
	function dispatchPluginHook($hook) {
		
		$args = func_get_args();
		$args[0] =& $this;
		return call_user_func_array( array( $this->Behaviors->BcPluginHook, $hook ), $args );
		
	}
/**
 * プラグインフックのハンドラを実行する
 * 
 * @param string $hook
 * @param mixed $return
 * @return mixed 
 */
	function executePluginHook($hook, $return) {
		
		$args = func_get_args();
		array_unshift($args, $this);
		return call_user_func_array(array($this->Behaviors->BcPluginHook, 'executeHook'), $args);
		
	}
}
