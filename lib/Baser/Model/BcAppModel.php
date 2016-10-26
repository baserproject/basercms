<?php

/**
 * Model 拡張クラス
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Baser.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('Sanitize', 'Utility');
App::uses('Folder', 'Utility');
App::uses('Model', 'Model');
App::uses('Dblog', 'Model');
App::uses('AppController', 'Controller');

/**
 * Model 拡張クラス
 *
 * 既存のCakePHPプロジェクトで、設置済のAppModelと共存できるように、AppModelとは別にした。
 *
 * @package			Baser.Model
 */
class BcAppModel extends Model {

/**
 * DB接続設定名
 *
 * @var string
 */
	public $useDbConfig = 'baser';

/**
 * コンストラクタ
 *
 * @return	void
 */
	public function __construct($id = false, $table = null, $ds = null) {
		$db = ConnectionManager::getDataSource('baser');
		if (isset($db->config['datasource'])) {
			if ($db->config['datasource'] != '') {
				parent::__construct($id, $table, $ds);
			} elseif ($db->config['login'] == 'dummy' &&
				$db->config['password'] == 'dummy' &&
				$db->config['database'] == 'dummy' &&
				Configure::read('BcRequest.pureUrl') == '') {
				// データベース設定がインストール段階の状態でトップページへのアクセスの場合、
				// 初期化ページにリダイレクトする
				$AppController = new AppController();
				session_start();
				$_SESSION['Message']['flash'] = array('message' => 'インストールに失敗している可能性があります。<br />インストールを最初からやり直すにはbaserCMSを初期化してください。', 'layout' => 'default');
				$AppController->redirect(BC_BASE_URL . 'installations/reset');
			}
		}
	}

/**
 * beforeSave
 *
 * @return	boolean
 * @access	public
 */
	public function beforeSave($options = array()) {
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
		return $result;
	}

/**
 * Saves model data to the database. By default, validation occurs before save.
 *
 * @param	array	$data Data to save.
 * @param	boolean	$validate If set, validation will be done before the save
 * @param	array	$fieldList List of fields to allow to be written
 * @return	mixed	On success Model::$data if its not empty or true, false on failure
 */
	public function save($data = null, $validate = true, $fieldList = array()) {
		if (!$data) {
			$data = $this->data;
		}

		// created,modifiedが更新されないバグ？対応
		if (!$this->exists()) {
			if (isset($data[$this->alias])) {
				$data[$this->alias]['created'] = null;
			} else {
				$data['created'] = null;
			}
		}
		if (isset($data[$this->alias])) {
			$data[$this->alias]['modified'] = null;
		} else {
			$data['modified'] = null;
		}

		return parent::save($data, $validate, $fieldList);
	}

/**
 * 配列の文字コードを変換する
 *
 * TODO GLOBAL グローバルな関数として再配置する必要あり
 *
 * @param array $data 変換前のデータ
 * @param string $outenc 変換後の文字コード
 * @param string $inenc 変換元の文字コード
 * @return array 変換後のデータ
 */
	public function convertEncodingByArray($data, $outenc, $inenc) {
		foreach ($data as $key => $value) {
			if (is_array($value)) {
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
 */
	public function saveDbLog($message) {
		// ログを記録する
		$Dblog = ClassRegistry::init('Dblog');
		$logdata['Dblog']['name'] = $message;
		$logdata['Dblog']['user_id'] = @$_SESSION['Auth']['User']['id'];
		return $Dblog->save($logdata);
	}

/**
 * コントロールソースを取得する
 *
 * 継承先でオーバーライドする事
 *
 * @return 	array
 */
	public function getControlSource($field) {
		return array();
	}

/**
 * 子カテゴリのIDリストを取得する
 *
 * treeビヘイビア要
 *
 * @param	mixed	$id ページカテゴリーID
 * @return 	array
 */
	public function getChildIdsList($id) {
		$ids = array();
		if ($this->childCount($id)) {
			$children = $this->children($id);
			foreach ($children as $child) {
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
 * TODO AppExModeに移行すべきかも
 */
	public function replaceText($str) {
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

		return str_replace(array_keys($arr), array_values($arr), $str);
	}

/**
 * データベースを初期化
 *
 * 既に存在するテーブルは上書きしない
 *
 * @param	array	データベース設定名
 * @param	string	プラグイン名
 * @return 	boolean
 */
	public function initDb($dbConfigName, $pluginName = '', $options = array()) {
		$options = array_merge(array(
			'loadCsv'		=> true,
			'filterTable'	=> '',
			'filterType'	=> '',
			'dbDataPattern'	=> ''
		), $options);

		// 初期データフォルダを走査
		if (!$pluginName) {
			$path = BASER_CONFIGS . 'Schema';
		} else {
			$path = BcUtil::getSchemaPath($pluginName);
			if (!$path) {
				return true;
			}
		}
		if ($this->loadSchema($dbConfigName, $path, $options['filterTable'], $options['filterType'], array(), $dropField = false)) {
			if ($options['loadCsv']) {
				$theme = $pattern = null;
				if($options['dbDataPattern']) {
					list($theme, $pattern) = explode('.', $options['dbDataPattern']);
				}
				$path = BcUtil::getDefaultDataPath($pluginName, $theme, $pattern);
				if($path) {
					return $this->loadCsv($dbConfigName, $path);
				} else {
					return true;
				}
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
 */
	public function loadSchema($dbConfigName, $path, $filterTable = '', $filterType = '', $excludePath = array(), $dropField = true) {
		// テーブルリストを取得
		$db = ConnectionManager::getDataSource($dbConfigName);
		$db->cacheSources = false;
		$listSources = $db->listSources();
		$prefix = $db->config['prefix'];
		$Folder = new Folder($path);
		$files = $Folder->read(true, true);

		$result = true;

		foreach ($files[1] as $file) {
			if (in_array($file, $excludePath)) {
				continue;
			}
			if (preg_match('/^(.*?)\.php$/', $file, $matches)) {
				$type = 'create';
				$table = $matches[1];
				if (preg_match('/^create_(.*?)\.php$/', $file, $matches)) {
					$type = 'create';
					$table = $matches[1];
					if (in_array($prefix . $table, $listSources)) {
						continue;
					}
				} elseif (preg_match('/^alter_(.*?)\.php$/', $file, $matches)) {
					$type = 'alter';
					$table = $matches[1];
					if (!in_array($prefix . $table, $listSources)) {
						continue;
					}
				} elseif (preg_match('/^drop_(.*?)\.php$/', $file, $matches)) {
					$type = 'drop';
					$table = $matches[1];
					if (!in_array($prefix . $table, $listSources)) {
						continue;
					}
				} else {
					if (in_array($prefix . $table, $listSources)) {
						continue;
					}
				}
				if ($filterTable && $filterTable != $table) {
					continue;
				}
				if ($filterType && $filterType != $type) {
					continue;
				}
				$tmpdir = TMP . 'schemas' . DS;
				copy($path . DS . $file, $tmpdir . $table . '.php');
				if (!$db->loadSchema(array('type' => $type, 'path' => $tmpdir, 'file' => $table . '.php', 'dropField' => $dropField))) {
					$result = false;
				}
				@unlink($tmpdir . $table . '.php');
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
 */
	public function loadCsv($dbConfigName, $path, $options = array()) {
		$options = array_merge(array(
			'filterTable' => ''
		), $options);

		// テーブルリストを取得
		$db = ConnectionManager::getDataSource($dbConfigName);
		$db->cacheSources = false;
		$listSources = $db->listSources();
		$prefix = $db->config['prefix'];
		$Folder = new Folder($path);
		$files = $Folder->read(true, true);
		foreach ($files[1] as $file) {
			if (preg_match('/^(.*?)\.csv$/', $file, $matches)) {
				$table = $matches[1];
				if (in_array($prefix . $table, $listSources)) {
					if ($options['filterTable'] && $options['filterTable'] != $table) {
						continue;
					}

					if (!$db->loadCsv(array('path' => $path . DS . $file, 'encoding' => 'SJIS'))) {
						return false;
					}
				}
			}
		}

		return true;
	}

/**
 * 最短の長さチェック
 * - 対象となる値の長さが、指定した最短値より長い場合、trueを返す
 *
 * @param mixed	$check 対象となる値
 * @param int	$min 値の最短値
 * @return boolean
 */
	public function minLength($check, $min) {
		$check = (is_array($check)) ? current($check) : $check;
		$length = mb_strlen($check, Configure::read('App.encoding'));
		return ($length >= $min);
	}

/**
 * 最長の長さチェック
 * - 対象となる値の長さが、指定した最長値より短い場合、trueを返す
 *
 * @param mixed	$check 対象となる値
 * @param int	$max 値の最長値
 * @param boolean
 */
	public function maxLength($check, $max) {
		$check = (is_array($check)) ? current($check) : $check;
		$length = mb_strlen($check, Configure::read('App.encoding'));
		return ($length <= $max);
	}

/**
 * 最大のバイト数チェック
 * - 対象となる値のサイズが、指定した最大値より短い場合、true を返す
 *
 * @param mixed $check 対象となる値
 * @param int $max バイト数の最大値
 * @return boolean
 */
	public function maxByte($check, $max) {
		$check = (is_array($check)) ? current($check) : $check;
		$byte = strlen($check);
		return ($byte <= $max);
	}

/**
 * 範囲を指定しての長さチェック
 *
 * @param mixed	$check 対象となる値
 * @param int	$min 値の最短値
 * @param int	$max 値の最長値
 * @param boolean
 */
	public function between($check, $min, $max) {
		$check = (is_array($check)) ? current($check) : $check;
		$length = mb_strlen($check, Configure::read('App.encoding'));
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
	public function getMax($field, $conditions = array()) {
		if (strpos($field, '.') === false) {
			$modelName = $this->alias;
		} else {
			list($modelName, $field) = explode('\.', $field);
		}

		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$this->recursive = -1;
		if ($db->config['datasource'] == 'Database/BcCsv') {
			// CSVDBの場合はMAX関数が利用できない為、プログラムで処理する
			// TODO dboでMAX関数の実装できたらここも変更する
			$this->cacheQueries = false;
			$dbDatas = $this->find('all', array('conditions' => $conditions, 'fields' => array($modelName . '.' . $field)));
			$this->cacheQueries = true;
			$max = 0;
			if ($dbDatas) {
				foreach ($dbDatas as $dbData) {
					if ($max < $dbData[$modelName][$field]) {
						$max = $dbData[$modelName][$field];
					}
				}
			}
			return $max;
		} else {
			$this->cacheQueries = false;
			// SQLiteの場合、Max関数にmodel名を含むと、戻り値の添字が崩れる（CakePHPのバグ）
			$dbData = $this->find('all', array('conditions' => $conditions, 'fields' => array('MAX(' . $modelName . '.' . $field . ') AS max')));
			$this->cacheQueries = true;
			if (isset($dbData[0][0]['max'])) {
				return $dbData[0][0]['max'];
			} else {
				return 0;
			}
		}
	}

/**
 * テーブルにフィールドを追加する
 *
 * @param	array	$options [ field / column / table ]
 * @return	boolean
 */
	public function addField($options) {
		extract($options);

		if (!isset($field) || !isset($column)) {
			return false;
		}

		if (!isset($table)) {
			$table = $this->useTable;
		}

		$this->_schema = null;
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$options = array('field' => $field, 'table' => $table, 'column' => $column);
		$ret = $db->addColumn($options);
		$this->deleteModelCache();
		return $ret;
	}

/**
 * フィールド構造を変更する
 *
 * @param	array	$options [ field / column / table ]
 * @return	boolean
 */
	public function editField($options) {
		extract($options);

		if (!isset($field) || !isset($column)) {
			return false;
		}

		if (!isset($table)) {
			$table = $this->useTable;
		}

		$this->_schema = null;
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$options = array('field' => $field, 'table' => $table, 'column' => $column);
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
	public function delField($options) {
		extract($options);

		if (!isset($field)) {
			return false;
		}

		if (!isset($table)) {
			$table = $this->useTable;
		}

		$this->_schema = null;
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$options = array('field' => $field, 'table' => $table);
		$ret = $db->dropColumn($options);
		$this->deleteModelCache();
		return $ret;
	}

/**
 * フィールド名を変更する
 *
 * @param array	$options [ new / old / table ]
 * @param array $column
 * @return boolean
 * @access public
 */
	public function renameField($options) {
		extract($options);

		if (!isset($new) || !isset($old)) {
			return false;
		}

		if (!isset($table)) {
			$table = $this->useTable;
		}

		$this->_schema = null;
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$options = array('new' => $new, 'old' => $old, 'table' => $table);
		$ret = $db->renameColumn($options);
		$this->deleteModelCache();
		return $ret;
	}

/**
 * テーブルの存在チェックを行う
 * @param string $tableName
 * @return boolean
 */
	public function tableExists($tableName) {
		$db = ConnectionManager::getDataSource($this->useDbConfig);
		$db->cacheSources = false;
		$tables = $db->listSources();
		return in_array($tableName, $tables);
	}

/**
 * 英数チェック
 *
 * @param	string	チェック対象文字列
 * @return	boolean
 */
	public function alphaNumeric($check) {
		if (!$check[key($check)]) {
			return true;
		}
		if (preg_match("/^[a-zA-Z0-9]+$/", $check[key($check)])) {
			return true;
		} else {
			return false;
		}
	}

/**
 * 英数チェックプラス
 *
 * ハイフンアンダースコアを許容
 *
 * @param array $check チェック対象文字列
 * @return boolean
 */
	public function alphaNumericPlus($check) {
		if (!$check[key($check)]) {
			return true;
		}
		if (preg_match("/^[a-zA-Z0-9\-_]+$/", $check[key($check)])) {
			return true;
		} else {
			return false;
		}
	}

/**
 * データの重複チェックを行う
 * @param array $check
 * @return boolean false 重複あり / true 重複なし
 */
	public function duplicate($check) {
		$conditions = array($this->alias . '.' . key($check) => $check[key($check)]);
		if ($this->exists()) {
			$conditions['NOT'] = array($this->alias . '.' . $this->primaryKey => $this->id);
		}
		$ret = $this->find('first', array('conditions' => $conditions));
		if ($ret) {
			return false;
		} else {
			return true;
		}
	}

/**
 * ファイルサイズチェック
 * 
 * @param array $check チェック対象データ
 * @param int $size 最大のファイルサイズ
 */
	public function fileSize($check, $size) {
		$file = $check[key($check)];
		if (!empty($file['name'])) {
			// サイズが空の場合は、HTMLのMAX_FILE_SIZEの制限によりサイズオーバー
			// だが、post_max_size を超えた場合は、ここまで処理がこない可能性がある
			if (!$file['size']) {
				return false;
			}
			if ($file['size'] > $size) {
				return;
			}
		}
		return true;
	}

/**
 * ファイルの拡張子チェック
 * 
 * @param array $check チェック対象データ
 * @param string $ext 許可する拡張子
 */
	public function fileExt($check, $ext) {
		$file = $check[key($check)];
		if (!empty($file['name'])) {
			$exts = explode(',', $ext);
			$ext = decodeContent($file['type'], $file['name']);
			if(in_array($ext, $exts)) {
				return true;
			} else {
				return false;
			}
		}
		return true;
	}
/**
 * 半角チェック
 * 
 * @param array $check 確認する値を含む配列。先頭の要素のみチェックされる
 * @return boolean
 */
	public function halfText($check) {
		$value = $check[key($check)];
		$len = strlen($value);
		$mblen = mb_strlen($value, 'UTF-8');
		if ($len != $mblen) {
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
	public function sortup($id, $conditions) {
		return $this->changeSort($id, -1, $conditions);
	}

/**
 * 一つ位置を下げる
 * @param string	$id
 * @param array		$conditions
 * @return boolean
 */
	public function sortdown($id, $conditions) {
		return $this->changeSort($id, 1, $conditions);
	}

/**
 * 並び順を変更する
 * @param string	$id 
 * @param int			$offset
 * @param array		$conditions
 * @return boolean
 */
	public function changeSort($id, $offset, $conditions = array()) {
		if ($conditions) {
			$_conditions = $conditions;
		} else {
			$_conditions = array();
		}

		// 一時的にキャッシュをOFFする
		$this->cacheQueries = false;

		$current = $this->find('first', array(
			'conditions' => array($this->alias . '.id' => $id),
			'fields' => array($this->alias . '.id', $this->alias . '.sort')
		));

		// 変更相手のデータを取得
		if ($offset > 0) { // DOWN
			$order = array($this->alias . '.sort');
			$limit = $offset;
			$conditions[$this->alias . '.sort >'] = $current[$this->alias]['sort'];
		} elseif ($offset < 0) { // UP
			$order = array($this->alias . '.sort DESC');
			$limit = $offset * -1;
			$conditions[$this->alias . '.sort <'] = $current[$this->alias]['sort'];
		} else {
			return true;
		}

		$conditions = array_merge($conditions, $_conditions);
		$target = $this->find('all', array(
			'conditions' => $conditions,
			'fields' => array($this->alias . '.id', $this->alias . '.sort'),
			'order' => $order,
			'limit' => $limit,
			'recursive' => -1
		));

		if (!isset($target[count($target) - 1])) {
			return false;
		}

		$currentSort = $current[$this->alias]['sort'];
		$targetSort = $target[count($target) - 1][$this->alias]['sort'];

		// current から target までのデータをsortで範囲指定して取得
		$conditions = array();
		if ($offset > 0) { // DOWN
			$conditions[$this->alias . '.sort >='] = $currentSort;
			$conditions[$this->alias . '.sort <='] = $targetSort;
		} elseif ($offset < 0) { // UP
			$conditions[$this->alias . '.sort <='] = $currentSort;
			$conditions[$this->alias . '.sort >='] = $targetSort;
		}

		$conditions = array_merge($conditions, $_conditions);
		$datas = $this->find('all', array(
			'conditions' => $conditions,
			'fields' => array($this->alias . '.id', $this->alias . '.sort'),
			'order' => $order,
			'recursive' => -1
		));

		// 全てのデータを更新
		$dataSource = $this->getDataSource();
		$dataSource->begin();
		foreach ($datas as $data) {
			if ($data[$this->alias]['sort'] == $currentSort) {
				$data[$this->alias]['sort'] = $targetSort;
			} else {
				if ($offset > 0) {
					$data[$this->alias]['sort'] --;
				} elseif ($offset < 0) {
					$data[$this->alias]['sort'] ++;
				}
			}
			if (!$this->save($data, false)) {
				$dataSource->rollback();
				return false;
			}
		}
		$dataSource->commit();

		return true;
	}

/**
 * Modelキャッシュを削除する
 * @return void
 * @access public
 */
	public function deleteModelCache() {
		$this->_schema = null;
		$folder = new Folder(CACHE . 'models' . DS);
		$caches = $folder->read(true, true);
		foreach ($caches[1] as $cache) {
			if (basename($cache) != 'empty') {
				@unlink(CACHE . 'models' . DS . $cache);
			}
		}
	}

/**
 * Key Value 形式のテーブルよりデータを取得して
 * １レコードとしてデータを展開する
 * @return array
 */
	public function findExpanded() {
		$dbDatas = $this->find('all', array('fields' => array('name', 'value')));
		$expandedData = array();
		if ($dbDatas) {
			foreach ($dbDatas as $dbData) {
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
	public function saveKeyValue($data) {
		if (isset($data[$this->alias])) {
			$data = $data[$this->alias];
		}

		$result = true;

		if ($this->Behaviors->attached('BcCache')) {
			$this->Behaviors->disable('BcCache');
		}

		foreach ($data as $key => $value) {

			if ($this->find('count', array('conditions' => array('name' => $key))) > 1) {
				$this->deleteAll(array('name' => $key));
			}

			$dbData = $this->find('first', array('conditions' => array('name' => $key)));

			if (!$dbData) {
				$dbData = array();
				$dbData[$this->alias]['name'] = $key;
				$dbData[$this->alias]['value'] = $value;
				$this->create($dbData);
			} else {
				$dbData[$this->alias]['value'] = $value;
				$this->set($dbData);
			}

			// SQliteの場合、トランザクション用の関数をサポートしていない場合があるので、
			// 個別に保存するようにした。
			if (!$this->save(null, false)) {
				$result = false;
			}
		}

		if ($this->Behaviors->attached('BcCache')) {
			$this->Behaviors->enable('BcCache');
			$this->delCache();
		}

		return true;
	}

/**
 * リストチェック
 * 対象となる値がリストに含まれる場合はエラー
 *
 * @param array $check 対象となる値
 * @param array $list リスト
 * @return boolean Succcess
 */
	public function notInList($check, $list) {
		return !in_array($check[key($check)], $list);
	}

/**
 * Deconstructs a complex data type (array or object) into a single field value.
 *
 * @param string $field The name of the field to be deconstructed
 * @param mixed $data An array or object to be deconstructed into a field
 * @return mixed The resulting data that should be assigned to a field
 */
	public function deconstruct($field, $data) {
		if (!is_array($data)) {
			return $data;
		}

		$type = $this->getColumnType($field);

		// >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
		/* if (!in_array($type, array('datetime', 'timestamp', 'date', 'time'))) { */
		// ---
		if (!in_array($type, array('string', 'text', 'datetime', 'timestamp', 'date', 'time'))) {
			// <<<
			return $data;
		}

		$useNewDate = (isset($data['year']) || isset($data['month']) ||
			isset($data['day']) || isset($data['hour']) || isset($data['minute']));

		// >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
		/* $dateFields = array('Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'min', 's' => 'sec'); */
		// ---
		$dateFields = array('W' => 'wareki', 'Y' => 'year', 'm' => 'month', 'd' => 'day', 'H' => 'hour', 'i' => 'min', 's' => 'sec');
		// <<<
		$timeFields = array('H' => 'hour', 'i' => 'min', 's' => 'sec');
		$date = array();

		if (isset($data['meridian']) && empty($data['meridian'])) {
			return null;
		}

		if (isset($data['hour']) &&
			isset($data['meridian']) &&
			!empty($data['hour']) &&
			$data['hour'] != 12 &&
			$data['meridian'] === 'pm'
		) {
			$data['hour'] = $data['hour'] + 12;
		}
		if (isset($data['hour']) && isset($data['meridian']) && $data['hour'] == 12 && $data['meridian'] === 'am') {
			$data['hour'] = '00';
		}
		if ($type === 'time') {
			foreach ($timeFields as $key => $val) {
				if (!isset($data[$val]) || $data[$val] === '0' || $data[$val] === '00') {
					$data[$val] = '00';
				} elseif ($data[$val] !== '') {
					$data[$val] = sprintf('%02d', $data[$val]);
				}
				if (!empty($data[$val])) {
					$date[$key] = $data[$val];
				} else {
					return null;
				}
			}
		}

		// >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
		/* if ($type === 'datetime' || $type === 'timestamp' || $type === 'date') { */
		// ---
		if ($type == 'text' || $type == 'string' || $type === 'datetime' || $type === 'timestamp' || $type === 'date') {
			// <<<
			foreach ($dateFields as $key => $val) {
				if ($val === 'hour' || $val === 'min' || $val === 'sec') {
					if (!isset($data[$val]) || $data[$val] === '0' || $data[$val] === '00') {
						$data[$val] = '00';
					} else {
						$data[$val] = sprintf('%02d', $data[$val]);
					}
				}

				// >>> CUSTOMIZE ADD 2013/11/10 ryuring	和暦対応
				if ($val == 'wareki' && !empty($data['wareki'])) {
					$warekis = array('m' => 1867, 't' => 1911, 's' => 1925, 'h' => 1988);
					if (!empty($data['year'])) {
						list($wareki, $year) = explode('-', $data['year']);
						$data['year'] = $year + $warekis[$wareki];
					}
				}
				// <<<
				// >>> CUSTOMIZE ADD 2013/11/10 ryuring	和暦対応
				/* if (!isset($data[$val]) || isset($data[$val]) && (empty($data[$val]) || $data[$val][0] === '-')) {
				  return null; */
				// ---
				if ($val != 'wareki' && !isset($data[$val]) || isset($data[$val]) && (empty($data[$val]) || $data[$val][0] === '-')) {
					if ($type == 'text' || $type == 'string') {
						return $data;
					} else {
						return null;
					}
				}
				if (isset($data[$val]) && !empty($data[$val])) {
					$date[$key] = $data[$val];
				}
			}
		}

		if ($useNewDate && !empty($date)) {
			// >>> CUSTOMIZE MODIFY 2013/11/10 ryuring 和暦対応
			/* $format = $this->getDataSource()->columns[$type]['format']; */
			// ---
			if ($type == 'text' || $type == 'string') {
				$format = 'Y-m-d H:i:s';
			} else {
				$format = $this->getDataSource()->columns[$type]['format'];
			}
			// <<<

			foreach (array('m', 'd', 'H', 'i', 's') as $index) {
				if (isset($date[$index])) {
					$date[$index] = sprintf('%02d', $date[$index]);
				}
			}
			return str_replace(array_keys($date), array_values($date), $format);
		}
		return $data;
	}

/**
 * ２つのフィールド値を確認する
 *
 * @param	array	$check 対象となる値
 * @param	mixed	$fields フィールド名
 * @return	boolean
 */
	public function confirm($check, $fields) {
		$value1 = $value2 = '';
		if (is_array($fields) && count($fields) > 1) {
			if (isset($this->data[$this->alias][$fields[0]]) &&
				isset($this->data[$this->alias][$fields[1]])) {
				$value1 = $this->data[$this->alias][$fields[0]];
				$value2 = $this->data[$this->alias][$fields[1]];
			}
		} elseif ($fields) {
			if (isset($check[key($check)]) && isset($this->data[$this->alias][$fields])) {
				$value1 = $check[key($check)];
				$value2 = $this->data[$this->alias][$fields];
			}

		} else {
			return false;
		}
		if ($value1 != $value2) {
			return false;
		}
		return true;
	}

/**
 * 指定したモデル以外のアソシエーションを除外する
 *
 * @param array $auguments アソシエーションを除外しないモデル
 * @param boolean $reset バインド時に１回の find でリセットするかどうか
 * @return void
 */
	public function expects($arguments, $reset = true) {
		$models = array();

		foreach ($arguments as $index => $argument) {
			if (is_array($argument)) {
				if (count($argument) > 0) {
					$arguments = am($arguments, $argument);
				}
				unset($arguments[$index]);
			}
		}

		foreach ($arguments as $index => $argument) {
			if (!is_string($argument)) {
				unset($arguments[$index]);
			}
		}

		if (count($arguments) == 0) {
			$models[$this->name] = array();
		} else {
			foreach ($arguments as $argument) {
				if (strpos($argument, '.') !== false) {
					$model = substr($argument, 0, strpos($argument, '.'));
					$child = substr($argument, strpos($argument, '.') + 1);

					if ($child == $model) {
						$models[$model] = array();
					} else {
						$models[$model][] = $child;
					}
				} else {
					$models[$this->name][] = $argument;
				}
			}
		}

		$relationTypes = array('belongsTo', 'hasOne', 'hasMany', 'hasAndBelongsToMany');

		foreach ($models as $bindingName => $children) {
			$model = null;

			foreach ($relationTypes as $relationType) {
				$currentRelation = (isset($this->$relationType) ? $this->$relationType : null);
				if (isset($currentRelation) && isset($currentRelation[$bindingName]) &&
					is_array($currentRelation[$bindingName]) && isset($currentRelation[$bindingName]['className'])) {
					$model = $currentRelation[$bindingName]['className'];
					break;
				}
			}

			if (!isset($model)) {
				$model = $bindingName;
			}

			if (isset($model) && $model != $this->name && isset($this->$model)) {
				if (!isset($this->__backInnerAssociation)) {
					$this->__backInnerAssociation = array();
				}
				$this->__backInnerAssociation[] = $model;
				$this->$model->expects(true, $children);
			}
		}

		if (isset($models[$this->name])) {
			foreach ($models as $model => $children) {
				if ($model != $this->name) {
					$models[$this->name][] = $model;
				}
			}

			$models = array_unique($models[$this->name]);
			$unbind = array();

			foreach ($relationTypes as $relation) {
				if (isset($this->$relation)) {
					foreach ($this->$relation as $bindingName => $bindingData) {
						if (!in_array($bindingName, $models)) {
							$unbind[$relation][] = $bindingName;
						}
					}
				}
			}
			if (count($unbind) > 0) {
				$this->unbindModel($unbind, $reset);
			}
		}
	}

/**
 * 複数のEメールチェック（カンマ区切り）
 * 
 * @param array $check 複数のメールアドレス
 * @return boolean 
 */
	public function emails($check) {
		$emails = array();
		if (strpos($check[key($check)], ',') !== false) {
			$emails = explode(',', $check[key($check)]);
		}
		if (!$emails) {
			$emails = array($check[key($check)]);
		}
		$result = true;
		foreach ($emails as $email) {
			if (!Validation::email($email)) {
				$result = false;
			}
		}
		return $result;
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
	public function deleteAll($conditions, $cascade = true, $callbacks = false) {
		$result = parent::deleteAll($conditions, $cascade, $callbacks);
		if ($result) {
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
 * 	Fields are treated as SQL snippets, to insert literal values manually escape your data.
 * @param mixed $conditions Conditions to match, true for all records
 * @return boolean True on success, false on failure
 * @link http://book.cakephp.org/view/75/Saving-Your-Data
 */
	public function updateAll($fields, $conditions = true) {
		$result = parent::updateAll($fields, $conditions);
		if ($result) {
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
 */
	public function cakeError($method, $messages = array()) {
		//======================================================================
		// router.php がロードされる前のタイミング（bootstrap.php）でエラーが発生した場合、
		// AppControllerなどがロードされていない為、Object::cakeError() を実行する事ができない。
		// router.php がロードされる前のタイミングでは、通常のエラー表示を行う
		//======================================================================
		if (!Configure::read('BcRequest.routerLoaded')) {
			trigger_error($method, E_USER_ERROR);
		} else {
			parent::cakeError($method, $messages);
		}
	}

/**
 * Queries the datasource and returns a result set array.
 *
 * Used to perform find operations, where the first argument is type of find operation to perform
 * (all / first / count / neighbors / list / threaded),
 * second parameter options for finding (indexed array, including: 'conditions', 'limit',
 * 'recursive', 'page', 'fields', 'offset', 'order', 'callbacks')
 *
 * Eg:
 * {{{
 * $model->find('all', array(
 *   'conditions' => array('name' => 'Thomas Anderson'),
 *   'fields' => array('name', 'email'),
 *   'order' => 'field3 DESC',
 *   'recursive' => 2,
 *   'group' => 'type',
 *   'callbacks' => false,
 * ));
 * }}}
 *
 * In addition to the standard query keys above, you can provide Datasource, and behavior specific
 * keys. For example, when using a SQL based datasource you can use the joins key to specify additional
 * joins that should be part of the query.
 *
 * {{{
 * $model->find('all', array(
 *   'conditions' => array('name' => 'Thomas Anderson'),
 *   'joins' => array(
 *     array(
 *       'alias' => 'Thought',
 *       'table' => 'thoughts',
 *       'type' => 'LEFT',
 *       'conditions' => '`Thought`.`person_id` = `Person`.`id`'
 *     )
 *   )
 * ));
 * }}}
 *
 * ### Disabling callbacks
 *
 * The `callbacks` key allows you to disable or specify the callbacks that should be run. To
 * disable beforeFind & afterFind callbacks set `'callbacks' => false` in your options. You can
 * also set the callbacks option to 'before' or 'after' to enable only the specified callback.
 *
 * ### Adding new find types
 *
 * Behaviors and find types can also define custom finder keys which are passed into find().
 * See the documentation for custom find types
 * (http://book.cakephp.org/2.0/en/models/retrieving-your-data.html#creating-custom-find-types)
 * for how to implement custom find types.
 *
 * Specifying 'fields' for notation 'list':
 *
 * - If no fields are specified, then 'id' is used for key and 'model->displayField' is used for value.
 * - If a single field is specified, 'id' is used for key and specified field is used for value.
 * - If three fields are specified, they are used (in order) for key, value and group.
 * - Otherwise, first and second fields are used for key and value.
 *
 * Note: find(list) + database views have issues with MySQL 5.0. Try upgrading to MySQL 5.1 if you
 * have issues with database views.
 *
 * Note: find(count) has its own return values.
 *
 * @param string $type Type of find operation (all / first / count / neighbors / list / threaded)
 * @param array $query Option fields (conditions / fields / joins / limit / offset / order / page / group / callbacks)
 * @return array|null Array of records, or Null on failure.
 * @link http://book.cakephp.org/2.0/en/models/retrieving-your-data.html
 */
	public function find($type = 'first', $query = array()) {
		$this->findQueryType = $type;
		$this->id = $this->getID();

		$query = $this->buildQuery($type, $query);
		if (is_null($query)) {
			return null;
		}

		// CUSTOMIZE MODIFY 2012/04/23 ryuring
		// キャッシュビヘイビアが利用状態の場合、モデルデータキャッシュを読み込む
		//
		// 【AppModelではキャッシュを定義しない事】
		// 自動的に生成されるクラス定義のない関連モデルの処理で勝手にキャッシュを利用されないようにする為
		// （HABTMの更新がうまくいかなかったので）
		// >>>
		//$results = $this->getDataSource()->read($this, $query);
		// ---
		$cache = true;
		if (isset($query['cache']) && is_bool($query['cache'])) {
			$cache = $query['cache'];
			unset($query['cache']);
		}
		if (BC_INSTALLED && isset($this->Behaviors) && $this->Behaviors->attached('BcCache') &&
			$this->Behaviors->enabled('BcCache') && Configure::read('debug') == 0) {
			$results = $this->readCache($cache, $type, $query);
		} else {
			$results = $this->getDataSource()->read($this, $query);
		}
		// <<<

		$this->resetAssociations();

		if ($query['callbacks'] === true || $query['callbacks'] === 'after') {
			$results = $this->_filterResults($results);
		}

		$this->findQueryType = null;

		if ($type === 'all') {
			return $results;
		} else {
			if ($this->findMethods[$type] === true) {
				return $this->{'_find' . ucfirst($type)}('after', $query, $results);
			}
		}
	}

/**
 * イベントを発火
 * 
 * @param string $name
 * @param array $params
 * @return mixed
 */
	public function dispatchEvent($name, $params = array(), $options = array()) {
		$options = array_merge(array(
			'modParams' => 0,
			'plugin' => $this->plugin,
			'layer' => 'Model',
			'class' => $this->name
			), $options);

		App::uses('BcEventDispatcher', 'Event');
		return BcEventDispatcher::dispatch($name, $this, $params, $options);
	}

/**
 * データが公開済みかどうかチェックする
 *
 * @param boolean $status 			公開ステータス
 * @param string $publishBegin 公開開始日時
 * @param string $publishEnd		公開終了日時
 * @return	array
 * @access public
 */
	public function isPublish($status, $publishBegin, $publishEnd) {
		if (!$status) {
			return false;
		}

		if ($publishBegin && $publishBegin != '0000-00-00 00:00:00') {
			if ($publishBegin > date('Y-m-d H:i:s')) {
				return false;
			}
		}

		if ($publishEnd && $publishEnd != '0000-00-00 00:00:00') {
			if ($publishEnd < date('Y-m-d H:i:s')) {
				return false;
			}
		}

		return true;
	}

/**
 * 日付の正当性チェック
 * 
 * @param array $check 確認する値
 * @return boolean
 */
	public function checkDate($check) {
		$value = $check[key($check)];
		if(!$value) {
			return true;
		}
		$time = '';
		if(strpos($value, ' ') !== false) {
			list($date, $time) = explode(' ', $value);
		} else {
			$date = $value;
		}
		if (DS != '\\') {
			if ($time) {
				if (!strptime($value, '%Y-%m-%d %H:%M')) {
					return false;
				}
			} else {
				if (!strptime($value, '%Y-%m-%d')) {
					return false;
				}
			}
		}
		list($Y, $m, $d) = explode('-', $date);
		if (checkdate($m, $d, $Y) !== true) {
			return false;
		}
		if($time) {
			if(strpos($value, ':') !== false) {
				list($H, $i) = explode(':', $time);
				if (checktime($H, $i) !== true) {
					return false;
				}
			} else {
				return false;
			}
		}
		if (date('Y-m-d H:i:s', strtotime($value)) == '1970-01-01 09:00:00') {
			return false;
		}
		return true;
	}

/**
 * ツリーより再帰的に削除する
 * 
 * @param int $id
 * @return boolean
 */
	public function removeFromTreeRecursive($id) {
		if(!$this->Behaviors->enabled('Tree')) {
			return false;
		}
		$children = $this->children($id);
		foreach($children as $child) {
			$this->removeFromTree($child[$this->alias]['id'], true);
		}
		return $this->removeFromTree($id, true);
	}

/**
 * ファイルが送信されたかチェックするバリデーション
 * 
 * @param array $check
 * @return boolean
 */
	public function notFileEmpty($check) {
		if (empty($check[key($check)]) || (is_array($check[key($check)]) && $check[key($check)]['size'] === 0)) {
			return false;
		}
		return true;
	}
	
}
