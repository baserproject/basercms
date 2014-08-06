<?php

/**
 * メッセージモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2014, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2014, baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
 */
/**
 * Include files
 */
App::uses('MailAppModel', 'Mail.Model');
App::uses('MailField', 'Mail.Model');
App::uses('MailContent', 'Mail.Model');

/**
 * メッセージモデル
 *
 * @package Mail.Model
 *
 */
class Message extends MailAppModel {

/**
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'Message';

/**
 * メールフォーム情報
 *
 * @var array
 * @access public
 */
	public $mailFields = null;

/**
 * Constructor.
 *
 * @return void
 * @access private
 */
	public function __construct($id = false, $table = null, $ds = null, $tablePrefix = null) {
		if ($tablePrefix) {
			$cm = ConnectionManager::getDataSource($this->useDbConfig);
			if (!empty($cm->config['prefix'])) {
				$dbPrefix = $cm->config['prefix'];
			} else {
				$dbPrefix = '';
			}
			$this->tablePrefix = $dbPrefix . $tablePrefix;
		}
		parent::__construct();
	}

/**
 * テーブルプレフィックスを設定する
 * 
 * @param string $tablePrefix
 * @return boolean
 */
	public function setTablePrefix($tablePrefix) {
		if (!$tablePrefix) {
			return false;
		}

		$cm = ConnectionManager::getDataSource($this->useDbConfig);

		if (!empty($cm->config['prefix'])) {
			$dbPrefix = $cm->config['prefix'];
		} else {
			$dbPrefix = '';
		}

		$this->tablePrefix = $dbPrefix . $tablePrefix;

		return true;
	}

/**
 * beforeSave
 *
 * @return boolean
 * @access public
 */
	public function beforeSave($options = array()) {
		$this->data = $this->convertToDb($this->data);
		return true;
	}

/**
 * バリデート処理
 *
 * @param	array	$options
 * @return 	array
 * @access	public
 */
	public function beforeValidate($options = array()) {
		// バリデーション設定
		$this->_setValidate();

		return parent::beforeValidate($options);
	}

/**
 * Called after data has been checked for errors
 *
 * @return void
 */
	public function afterValidate() {
		$data = $this->data;
		// Eメール確認チェック
		$this->_validEmailCofirm($data);
		// 不完全データチェック
		$this->_validGroupComplate($data);
		// 拡張バリデートチェック
		$this->_validExtends($data);
		// バリデートグループエラーチェック
		$this->_validGroupErrorCheck();
		// エラー内容変換
		$this->_validSingeErrorCheck();
	}

/**
 * validate（入力チェック）を個別に設定する
 * VALID_NOT_EMPTY	空不可
 * VALID_EMAIL		メール形式チェック
 *
 * @return void
 * @access protected
 * TODO Cake1.2に対応させる
 */
	protected function _setValidate() {
		foreach ($this->mailFields as $mailField) {

			if ($mailField['MailField']['valid'] && !empty($mailField['MailField']['use_field'])) {

				if ($mailField['MailField']['valid'] == 'VALID_NOT_EMPTY') {
					// 必須項目
					$this->validate[$mailField['MailField']['field_name']] = array('notEmpty' => array(
							'rule' => array('notEmpty'),
							'message' => '必須項目です。',
							'required' => true
					));
				} elseif ($mailField['MailField']['valid'] == 'VALID_EMAIL') {
					// メール形式
					$this->validate[$mailField['MailField']['field_name']] = array('email' => array(
							'rule' => array('email'),
							'message' => '形式が不正です。'
					));
				} else {
					$this->validate[$mailField['MailField']['field_name']] = $mailField['MailField']['valid'];
				}
			}
		}
	}

/**
 * 拡張バリデートチェック
 *
 * @param array $data
 * @return void
 * @access protected
 */
	protected function _validExtends($data) {
		$dists = array();

		// 対象フィールドを取得
		foreach ($this->mailFields as $mailField) {

			if (!empty($mailField['MailField']['use_field'])) {
				// マルチチェックボックスのチェックなしチェック
				if ($mailField['MailField']['valid_ex'] == 'VALID_NOT_UNCHECKED') {
					if (empty($data['Message'][$mailField['MailField']['field_name']])) {
						$this->invalidate($mailField['MailField']['field_name']);
					}
					$dists[$mailField['MailField']['field_name']][] = @$data['Message'][$mailField['MailField']['field_name']];

					// datetimeの空チェック
				} elseif ($mailField['MailField']['valid_ex'] == 'VALID_DATETIME') {
					if (empty($data['Message'][$mailField['MailField']['field_name']]['year']) ||
						empty($data['Message'][$mailField['MailField']['field_name']]['month']) ||
						empty($data['Message'][$mailField['MailField']['field_name']]['day'])) {
						$this->invalidate($mailField['MailField']['field_name']);
					}
				}
			}
		}
	}

/**
 * エラー内容変換
 *
 * @return void
 * @access protected
 */
	protected function _validSingeErrorCheck() {
		foreach ($this->validate as $key => $data) {
			// VALID_NOT_EMPTY以外は形式エラーとする
			if (is_array($data) && key($data) != 'notEmpty') {
				if (isset($this->validationErrors[$key])) {
					$this->invalidate($key . '_format');
				}
			}
		}
	}

/**
 * バリデートグループエラーチェック
 *
 * @return void
 * @access protected
 */
	protected function _validGroupErrorCheck() {
		$dists = array();

		// 対象フィールドを取得
		foreach ($this->mailFields as $mailField) {

			// 対象フィールドがあれば、バリデートグループごとに配列にフィールド名を格納する
			if (!empty($mailField['MailField']['use_field']) && $mailField['MailField']['group_valid']) {
				$dists[$mailField['MailField']['group_valid']][] = $mailField['MailField']['field_name'];
			}
		}

		// エラーが発生しているかチェック
		foreach ($dists as $key => $dist) {
			foreach ($dist as $data) {
				if (isset($this->validationErrors[$data]) && isset($this->validate[$data])) {
					// VALID_NOT_EMPTY以外は形式エラーとする
					if (key($this->validate[$data]) != 'notEmpty') {
						$this->invalidate($key);
						$this->invalidate($key . '_format');
					} else {
						$this->invalidate($key);
					}
				}
			}
		}
	}

/**
 * 不完全データチェック
 *
 * @param array $data
 * @return void
 * @access protected
 */
	protected function _validGroupComplate($data) {
		$dists = array();

		// 対象フィールドを取得
		foreach ($this->mailFields as $mailField) {

			// 対象フィールドがあれば、バリデートグループごとに配列に格納する
			if ($mailField['MailField']['valid_ex'] == 'VALID_GROUP_COMPLATE') {
				$dists[$mailField['MailField']['group_valid']][] = $data['Message'][$mailField['MailField']['field_name']];
			}
		}
		// チェック
		// バリデートグループにおけるデータの埋まり具合をチェックし、全て埋まっていない場合、全て埋まっている場合以外は
		// 不完全データとみなしエラーとする
		foreach ($dists as $key => $dist) {
			$i = 0;
			foreach ($dist as $data) {
				if ($data) {
					$i++;
				}
			}
			$count = count($dist);
			if ($i > 0 && $i < $count) {
				$this->invalidate($key . '_not_complate');
				for ($j = 1; $j <= $count; $j++) {
					$this->invalidate($key . '_' . $j);
				}
			}
		}
	}

/**
 * Eメール確認チェック
 *
 * @param array $data
 * @return void
 * @access protected
 */
	protected function _validEmailCofirm($data) {
		$dists = array();

		// 対象フィールドを取得
		foreach ($this->mailFields as $mailField) {

			// 対象フィールドがあれば、バリデートグループごとに配列に格納する
			if ($mailField['MailField']['valid_ex'] == 'VALID_EMAIL_CONFIRM') {
				if (isset($data['Message'][$mailField['MailField']['field_name']])) {
					$dists[$mailField['MailField']['group_valid']][] = $data['Message'][$mailField['MailField']['field_name']];
				}
			}
		}
		// チェック
		// バリデートグループにおけるデータ２つを比較し、違えばエラーとする
		foreach ($dists as $key => $dist) {
			list($a, $b) = $dist;
			if ($a != $b) {
				$this->invalidate($key . '_not_same');
				$this->invalidate($key . '_1');
				$this->invalidate($key . '_2');
			}
		}
	}

/**
 * 自動変換
 * 確認画面で利用される事も踏まえてバリデートを通す為の
 * 可能な変換処理を行う。
 *
 * @param array $data
 * @return array $data
 * @access public
 */
	public function autoConvert($data) {
		foreach ($this->mailFields as $mailField) {

			if (!$mailField['MailField']['use_field']) {
				continue;
			}

			$value = $data['Message'][$mailField['MailField']['field_name']];

			if (!empty($value)) {

				// 半角処理
				if ($mailField['MailField']['auto_convert'] == 'CONVERT_HANKAKU') {
					$value = mb_convert_kana($value, 'a');
				}
				// 全角処理
				if ($mailField['MailField']['auto_convert'] == 'CONVERT_ZENKAKU') {
					$value = mb_convert_kana($value, 'AK');
				}
				// サニタイズ
				if (!is_array($value)) {
					$value = str_replace('<!--', '&lt;!--', $value);
				}
				// TRIM
				if (!is_array($value)) {
					$value = trim($value);
				}
			}

			$data['Message'][$mailField['MailField']['field_name']] = $value;
		}

		return $data;
	}

/**
 * 初期値の設定をする
 *
 * @return array $data
 * @access public
 */
	public function getDefaultValue($data) {
		$_data = array();

		// 対象フィールドを取得
		if ($this->mailFields) {
			foreach ($this->mailFields as $mailField) {

				// 対象フィールドがあれば、バリデートグループごとに配列に格納する
				if (!is_null($mailField['MailField']['default_value']) && $mailField['MailField']['default_value'] !== "") {

					if ($mailField['MailField']['type'] == 'multi_check') {
						$_data['Message'][$mailField['MailField']['field_name']][0] = $mailField['MailField']['default_value'];
					} else {
						$_data['Message'][$mailField['MailField']['field_name']] = $mailField['MailField']['default_value'];
					}
				}
			}
		}

		if ($data) {
			if (!isset($data['Message'])) {
				$data = array('Message' => $data);
			}
			foreach ($data['Message'] as $key => $value) {
				if(isset($data['Message'][$key])) {
					$_data['Message'][$key] = h($value);
				}
			}
		}
		return $_data;
	}

/**
 * データベース用のデータに変換する
 *
 * @param array $dbDatas
 * @return array $dbDatas
 * @access public
 */
	public function convertToDb($dbData) {
		// マルチチェックのデータを｜区切りに変換
		foreach ($this->mailFields as $mailField) {
			if ($mailField['MailField']['type'] == 'multi_check' && $mailField['MailField']['use_field']) {
				if (!empty($dbData['Message'][$mailField['MailField']['field_name']])) {
					if (is_array($dbData['Message'][$mailField['MailField']['field_name']])) {
						$dbData['Message'][$mailField['MailField']['field_name']] = implode("|", $dbData['Message'][$mailField['MailField']['field_name']]);
					} else {
						$dbData['Message'][$mailField['MailField']['field_name']] = $dbData['Message'][$mailField['MailField']['field_name']];
					}
				}
			}
		}

		// 機種依存文字を変換
		$dbData['Message'] = $this->replaceText($dbData['Message']);

		return $dbData;
	}

/**
 * 機種依存文字の変換処理
 * 内部文字コードがUTF-8である必要がある。
 * 多次元配列には対応していない。
 *
 * @param string $str 変換対象文字列
 * @return string $str 変換後文字列
 * @access public
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
 * メール用に変換する
 *
 * @param array $dbDatas
 * @return array $dbDatas
 * @access public
 * TODO ヘルパー化すべきかも
 */
	public function convertDatasToMail($dbData) {
		foreach ($dbData['mailFields'] as $key => $value) {
			$dbData['mailFields'][$key]['MailField']['before_attachment'] = strip_tags($value['MailField']['before_attachment']);
			$dbData['mailFields'][$key]['MailField']['after_attachment'] = strip_tags($value['MailField']['after_attachment'], "<br>");
			$dbData['mailFields'][$key]['MailField']['head'] = strip_tags($value['MailField']['head'], "<br>");
			$dbData['mailFields'][$key]['MailField']['after_attachment'] = str_replace(array("<br />", "<br>"), "\n", $dbData['mailFields'][$key]['MailField']['after_attachment']);
			$dbData['mailFields'][$key]['MailField']['head'] = str_replace(array("<br />", "<br>"), "", $dbData['mailFields'][$key]['MailField']['head']);
		}
		foreach ($this->mailFields as $mailField) {
			if (!empty($dbData['message'][$mailField['MailField']['field_name']])) {
				$dbData['message'][$mailField['MailField']['field_name']] = str_replace(array("<br />", "<br>"), "\n", $dbData['message'][$mailField['MailField']['field_name']]);
				//$dbData['message'][$mailField['MailField']['field_name']] = mb_convert_kana($dbData['message'][$mailField['MailField']['field_name']], "K", "UTF-8");
			}
			if ($mailField['MailField']['type'] == 'multi_check') {
				if (!empty($dbData['message'][$mailField['MailField']['field_name']]) && !is_array($dbData['message'][$mailField['MailField']['field_name']])) {
					$dbData['message'][$mailField['MailField']['field_name']] = explode("|", $dbData['message'][$mailField['MailField']['field_name']]);
				}
			}
		}

		return $dbData;
	}

/**
 * メッセージテーブルを作成する
 *
 * @param string $contentName コンテンツ名
 * @return boolean
 * @access public
 */
	public function createTable($contentName) {
		$db = $this->getDataSource();
		$this->tablePrefix = $this->getTablePrefixByContentName($contentName);
		$fullTable = $this->tablePrefix . 'messages';
		$table = str_replace($db->config['prefix'], '', $fullTable);
		$schema = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
			'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
			'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
		$ret = true;
		if ($this->tableExists($fullTable)) {
			$ret = $db->dropTable(array('table' => $table));
		}
		if (!$ret) {
			return false;
		}
		$ret = $db->createTable(array('schema' => $schema, 'table' => $table));
		$this->deleteModelCache();
		return $ret;
	}

/**
 * メッセージテーブルの名前を変更する
 *
 * @param string $source 元コンテンツ名
 * @param string $target 変更後コンテンツ名
 * @return boolean
 * @access public
 */
	public function renameTable($source, $target) {
		$db = $this->getDataSource();

		$sourceName = $this->getTablePrefixByContentName($source) . 'messages';
		$targetName = $this->getTablePrefixByContentName($target) . 'messages';
		$sourceTable = str_replace($db->config['prefix'], '', $sourceName);
		$targetTable = str_replace($db->config['prefix'], '', $targetName);

		$ret = true;
		if ($target == 'messages') {
			$ret = $db->dropTable(array('table' => $targetTable));
		}
		if (!$ret) {
			return false;
		}
		$ret = $db->renameTable(array('old' => $sourceTable, 'new' => $targetTable));

		if ($ret && $source == 'messages') {
			$ret = $this->createTable($source);
		}

		$this->deleteModelCache();
		return $ret;
	}

/**
 * メッセージテーブルを削除する
 *
 * @param string $contentName コンテンツ名
 * @return boolean
 * @access private
 */
	public function dropTable($contentName) {
		$db = $this->getDataSource();
		$this->tablePrefix = $this->getTablePrefixByContentName($contentName);
		$fullTable = $this->tablePrefix . 'messages';
		$table = str_replace($db->config['prefix'], '', $fullTable);

		if (!$this->tableExists($fullTable)) {
			return true;
		}

		$ret = $db->dropTable(array('table' => $table));

		if ($ret && $contentName == 'messages') {
			$ret = $this->createTable($contentName);
		}

		$this->deleteModelCache();
		return $ret;
	}

/**
 * メッセージファイルにフィールドを追加する
 *
 * @param string $contentName
 * @param string $field
 * @return array
 * @access public
 */
	public function addMessageField($contentName, $field) {
		$fullTable = $this->getTablePrefixByContentName($contentName) . $this->useTable;
		$db = $this->getDataSource();
		$table = str_replace($db->config['prefix'], '', $fullTable);
		$options = array('field' => $field, 'column' => array('type' => 'text'), 'table' => $table);
		$ret = parent::addField($options);
		return $ret;
	}

/**
 * メッセージファイルのフィールドを削除する
 *
 * @param string $contentName
 * @param string $field
 * @return array
 * @access public
 */
	public function delMessageField($contentName, $field) {
		$fullTable = $this->getTablePrefixByContentName($contentName) . $this->useTable;
		$db = $this->getDataSource();
		$table = str_replace($db->config['prefix'], '', $fullTable);
		$ret = parent::delField(array('field' => $field, 'table' => $table));
		return $ret;
	}

/**
 * メッセージファイルのフィールドを編集する
 *
 * @param string $fieldName
 * @param string $oldFieldName
 * @param string $newfieldName
 * @return array
 * @access private
 */
	public function renameMessageField($contentName, $oldFieldName, $newfieldName) {
		$fullTable = $this->getTablePrefixByContentName($contentName) . $this->useTable;
		$db = $this->getDataSource();
		$table = str_replace($db->config['prefix'], '', $fullTable);
		$ret = parent::renameField(array('old' => $oldFieldName, 'new' => $newfieldName, 'table' => $table));
		return $ret;
	}

/**
 * コンテンツ名つきのテーブルプレフィックスを取得する
 * 
 * @param string $contentName
 * @return string
 * @access public
 */
	public function getTablePrefixByContentName($contentName) {
		$db = $this->getDataSource();
		$prefix = '';
		if ($contentName != 'messages') {
			$prefix = $db->config['prefix'] . $contentName . "_";
		} else {
			$prefix = $db->config['prefix'];
		}
		return $prefix;
	}

/**
 * メッセージ保存用テーブルのフィールドを最適化する
 * 初回の場合、id/created/modifiedを追加する
 * 2回目以降の場合は、最後のカラムに追加する
 * 
 * @param array $dbConfig
 * @param int $mailContentId
 * @return boolean
 * @access public
 */
	public function construction($mailContentId) {
		$mailFieldClass = new MailField();
		$mailContentClass = new MailContent();

		// フィールドリストを取得
		$mailFields = $mailFieldClass->find('all', array('conditions' => array('MailField.mail_content_id' => $mailContentId)));
		// コンテンツ名を取得
		$contentName = $mailContentClass->field('name', array('MailContent.id' => $mailContentId));

		if (!$this->tableExists($this->getTablePrefixByContentName($contentName) . 'messages')) {

			/* 初回の場合 */
			$this->createTable($contentName);
		} else {

			/* 2回目以降の場合 */
			$this->tablePrefix = $this->getTablePrefixByContentName($contentName);
			$this->_schema = null;
			$this->cacheSources = false;
			$schema = $this->schema();
			$messageFields = array_keys($schema);
			foreach ($mailFields as $mailField) {
				if (!in_array($mailField['MailField']['field_name'], $messageFields)) {
					$this->addMessageField($contentName, $mailField['MailField']['field_name']);
				}
			}
		}

		return true;
	}

/**
 * 受信メッセージの内容を表示状態に変換する
 * 
 * @param int $id
 * @param array $messages
 * @return array
 * @access public
 */
	public function convertMessageToCsv($id, $messages) {
		App::uses('MailField', 'Mail.Model');
		$mailFieldClass = new MailField();

		// フィールドの一覧を取得する
		$mailFields = $mailFieldClass->find('all', array('conditions' => array('MailField.mail_content_id' => $id), 'order' => 'sort'));

		// フィールド名とデータの変換に必要なヘルパーを読み込む
		App::uses('MaildataHelper', 'Mail.View/Helper');
		App::uses('MailfieldHelper', 'Mail.View/Helper');
		$Maildata = new MaildataHelper(new View());
		$Mailfield = new MailfieldHelper(new View());

		foreach ($messages as $key => $message) {
			$inData = array();
			$inData['NO'] = $message[$this->alias]['id'];
			foreach ($mailFields as $mailField) {
				$inData[$mailField['MailField']['field_name'] . ' (' . $mailField['MailField']['name'] . ')'] = $Maildata->control(
					$mailField['MailField']['type'],
					$message[$this->alias][$mailField['MailField']['field_name']],
					$Mailfield->getOptions($mailField['MailField'])
				);
			}
			$inData['作成日'] = $message[$this->alias]['created'];
			$inData['更新日'] = $message[$this->alias]['modified'];
			$messages[$key][$this->alias] = $inData;
		}

		return $messages;
	}
	
/**
 * メール受信テーブルを全て再構築
 * 
 * @return boolean
 */
	public function reconstructionAll() {
		
		// メール受信テーブルの作成
		$PluginContent = ClassRegistry::init('PluginContent');
		$pluginContents = $PluginContent->find('all', array('conditions' => array('PluginContent.plugin' => 'mail')));

		$result = true;
		foreach ($pluginContents as $pluginContent) {
			if ($this->createTable($pluginContent['PluginContent']['name'])) {
				if (!$this->construction($pluginContent['PluginContent']['content_id'])) {
					$result = false;
				}
			} else {
				$result = false;
			}
		}
		return $result;
		
	}

	
/**
 * find
 * 
 * @param String $type
 * @param mixed $query
 * @return Array
 */
	public function find($type = 'first', $query = array()) {
		// テーブルを共用しているため、環境によってはデータ取得に失敗する。
		// その原因のキャッシュメソッドをfalseに設定。
		$db = ConnectionManager::getDataSource('plugin');
		$db->cacheMethods = false;
		$result = parent::find($type, $query);
		$db->cacheMethods = true;
		return $result;
	}

}
