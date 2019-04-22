<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Model
 * @since			baserCMS v 0.1.0
 * @license			http://basercms.net/license/index.html
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
class MailMessage extends MailAppModel {

/**
 * テーブル
 *
 * @var string
 */
	public $useTable = false;

/**
 * メールフォーム情報
 *
 * @var array
 */
	public $mailFields = array();

/**
 * メールコンテンツ情報
 * 
 * @var array
 */
	public $mailContent = array();
	
/**
 * ビヘイビア
 *
 * @var array
 */
	public $actsAs = array(
		'BcUpload' => array(
			'subdirDateFormat' => 'Y/m/'
		)
	);
	
/**
 * モデルのセットアップを行う
 * 
 * MailMessageモデルは利用前にこのメソッドを呼び出しておく必要あり
 * 
 * @param int $mailContentId
 * @return boolean
 */
	public function setup($mailContentId) {
		
		// テーブル名の設定
		$this->setUseTable($mailContentId);
		// 利用するメールフィールド取得
		App::uses('MailField', 'Mail.Model');
		$MailContent = ClassRegistry::init('Mail.MailContent');
		$mailContent = $MailContent->find('first', [
			'conditions' => ['MailContent.id' => $mailContentId, 'MailField.use_field' => true],
			'order' => 'MailField.sort ASC',
			'recursive' => 1,
			'joins' => [[
				'type' => 'inner',
				'table' => 'mail_fields',
				'alias' => 'MailField',
				'conditions' => 'MailContent.id = MailField.mail_content_id'
			]]
		]);
		if(!$mailContent) {
			return false;
		}
		$this->mailContent = ['MailContent' => $mailContent['MailContent']];
		if(!empty($mailContent['MailField'])) {
			foreach($mailContent['MailField'] as $value) {
				$this->mailFields[]	= ['MailField' => $value];
			}
		}

		// アップロード設定
		$this->setupUpload($mailContentId);
		return true;
		
	}

/**
 * テーブル名を設定する
 * 
 * @param $mailContentId
 */
	public function setUseTable($mailContentId) {
		$this->table = $this->useTable = $this->createTableName($mailContentId);
	}
	
/**
 * アップロード設定を行う
 */
	public function setupUpload($name) {

		$settings = $this->Behaviors->BcUpload->settings['MailMessage'];
		$settings['fields'] = array();
		foreach ($this->mailFields as $mailField) {
			$mailField = $mailField['MailField'];
			if($mailField['type'] == 'file') {
				$settings['fields'][$mailField['field_name']] = array(
					'type' => 'all',
					'namefield' => 'id',
					'nameformat' => '%08d'
				);
			}
		}
		if (empty($settings['saveDir']) || !preg_match('/^' . preg_quote("mail" . DS . $name, '/') . '\//', $settings['saveDir'])) {
			$settings['saveDir'] = "mail" . DS . "limited" . DS . $name . DS . "messages";
		}
		$this->Behaviors->load('BcUpload', $settings);
		
	}
	
/**
 * beforeSave
 *
 * @return boolean
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
	}

/**
 * validate（入力チェック）を個別に設定する
 * VALID_NOT_EMPTY	空不可
 * VALID_EMAIL		メール形式チェック
 *
 * @return void
 */
	protected function _setValidate() {
		foreach ($this->mailFields as $mailField) {
			$mailField = $mailField['MailField'];
			if ($mailField['valid'] && !empty($mailField['use_field'])) {
				// 必須項目
				if ($mailField['valid'] == 'VALID_NOT_EMPTY' || $mailField['valid'] == 'VALID_EMAIL') {
					if($mailField['type'] == 'file') {
						if(!isset($this->data['MailMessage'][$mailField['field_name'] . '_tmp'])) {
							$this->validate[$mailField['field_name']] = array('notBlank' => array(
									'rule' => array('notFileEmpty'),
									'message' => __('必須項目です。'),
									'required' => true
							));
						}
					} else {
						$this->validate[$mailField['field_name']] = array('notBlank' => array(
								'rule' => array('notBlank'),
								'message' => __('必須項目です。'),
								'required' => true
						));
					}
				// 半角数字
				} elseif ($mailField['valid'] == '/^(|[0-9]+)$/') {
					$this->validate[$mailField['field_name']] = array(
							'rule' => '/^(|[0-9]+)$/',
							'message' => '半角数字で入力してください。'
				);
				// 半角数字（入力必須）
				} elseif ($mailField['valid'] == '/^([0-9]+)$/') {
					$this->validate[$mailField['field_name']] = array(
							'rule' => '/^([0-9]+)$/',
							'message' => __('半角数字で入力してください。')
					);
				} else {
					$this->validate[$mailField['field_name']] = $mailField['valid'];
				}
				if (!empty($this->data['MailMessage'][$mailField['field_name']]) && $mailField['valid'] == 'VALID_EMAIL') {
					$this->validate[$mailField['field_name']] = array('email' => array(
						'rule' => array('email'),
						'message' => __('形式が無効です。')
					));
				}
			}
			// ### 拡張バリデーション
			if($mailField['valid_ex'] && !empty($mailField['use_field'])) {
				$valids = explode(',', $mailField['valid_ex']);
				foreach($valids as $valid) {
					$options = explode('|', $mailField['options']);
					$options = call_user_func_array('aa', $options);
					switch ($valid) {
						case 'VALID_MAX_FILE_SIZE':
							if (!empty($options['maxFileSize']) && $this->data['MailMessage'][$mailField['field_name']]['error'] !== UPLOAD_ERR_NO_FILE) {
								switch ($this->data['MailMessage'][$mailField['field_name']]['error']) {
									case UPLOAD_ERR_INI_SIZE:
									case UPLOAD_ERR_FORM_SIZE:
										$errorMessage = __('ファイルサイズがオーバーしています。 %s MB以内のファイルをご利用ください。',
											$options['maxFileSize']);
										break;
									default:
										$errorMessage = __('何らかの原因でファイルをアップロードできませんでした。Webサイトの管理者に連絡してください。');
								}
								$this->validate[$mailField['field_name']]['fileCheck'] = [
									'rule' => ['fileCheck', $options['maxFileSize'] * 1000 * 1000],
									'message' => $errorMessage
								];
								// 必須入力としている場合、必須エラーが優先され、ファイルサイズオーバーのエラーメッセージとならないため、バリデーションエラーの優先度を入れ替える
								$this->validate[$mailField['field_name']] = array_reverse($this->validate[$mailField['field_name']]);
							}
							break;
						case 'VALID_FILE_EXT':
							if(!empty($options['fileExt'])) {
								$this->validate[$mailField['field_name']]['fileExt'] = array(
									'rule'	=> array('fileExt', $options['fileExt']),
									'message'	=> __('ファイル形式が無効です。')
								);
							}
							break;
					}
				}
			}
		}
	}
	
/**
 * 拡張バリデートチェック
 *
 * @param array $data
 * @return void
 */
	protected function _validExtends($data) {
		$dists = array();

		// 対象フィールドを取得
		foreach ($this->mailFields as $mailField) {
			$mailField = $mailField['MailField'];
			if (!empty($mailField['use_field'])) {
				$valids = explode(',', $mailField['valid_ex']);
				// マルチチェックボックスのチェックなしチェック
				if (in_array('VALID_NOT_UNCHECKED', $valids)) {
					if (empty($data['MailMessage'][$mailField['field_name']])) {
						$this->invalidate($mailField['field_name'], __('必須項目です。'));
					}
					$dists[$mailField['field_name']][] = @$data['MailMessage'][$mailField['field_name']];
					// datetimeの空チェック
				} elseif (in_array('VALID_DATETIME', $valids)) {
					if (empty($data['MailMessage'][$mailField['field_name']]['year']) ||
						empty($data['MailMessage'][$mailField['field_name']]['month']) ||
						empty($data['MailMessage'][$mailField['field_name']]['day'])) {
						$this->invalidate($mailField['field_name'], __('日付の形式が無効です。'));
					}
				} elseif (in_array('VALID_ZENKAKU_KATAKANA', $valids)) {
					if(!preg_match('/^(|[ァ-ヾ]+)$/u', $data['MailMessage'][$mailField['field_name']])) {
						$this->invalidate($mailField['field_name'], __('全て全角カタカナで入力してください。'));
					}
				}
			}
		}
	}

/**
 * バリデートグループエラーチェック
 *
 * @return void
 */
	protected function _validGroupErrorCheck() {
		$dists = array();

		// 対象フィールドを取得
		foreach ($this->mailFields as $mailField) {
			$mailField = $mailField['MailField'];
			// 対象フィールドがあれば、バリデートグループごとに配列にフィールド名を格納する
			if (!empty($mailField['use_field']) && $mailField['group_valid']) {
				$dists[$mailField['group_valid']][] = $mailField['field_name'];
			}
		}

		// エラーが発生しているかチェック
		foreach ($dists as $key => $dist) {
			foreach ($dist as $data) {
				if (isset($this->validationErrors[$data]) && isset($this->validate[$data])) {
					$this->invalidate($key);
				}
			}
		}
	}

/**
 * 不完全データチェック
 *
 * @param array $data
 * @return void
 */
	protected function _validGroupComplate($data) {
		$dists = array();

		// 対象フィールドを取得
		foreach ($this->mailFields as $mailField) {
			$mailField = $mailField['MailField'];
			// 対象フィールドがあれば、バリデートグループごとに配列に格納する
			$valids = explode(',', $mailField['valid_ex']);
			if (in_array('VALID_GROUP_COMPLATE', $valids) && !empty($mailField['use_field'])) {
				$dists[$mailField['group_valid']][] = [
					'name' => $mailField['field_name'],
					'value' => @$data['MailMessage'][$mailField['field_name']]
				];
			}
		}
		// チェック
		// バリデートグループにおけるデータの埋まり具合をチェックし、全て埋まっていない場合、全て埋まっている場合以外は
		// 不完全データとみなしエラーとする
		foreach ($dists as $key => $dist) {
			$i = 0;
			foreach ($dist as $data) {
				if (!empty($data['value'])) {
					$i++;
				}
			}
			$count = count($dist);
			if ($i > 0 && $i < $count) {
 				$this->invalidate($key . '_not_complate', __('入力データが不完全です。'));
 				for ($j = 0; $j < $count; $j++) {
 					$this->invalidate($dist[$j]['name']);
 				}
			}
		}
	}

/**
 * Eメール確認チェック
 *
 * @param array $data
 * @return void
 */
	protected function _validEmailCofirm($data) {
		$dists = array();

		// 対象フィールドを取得
		foreach ($this->mailFields as $mailField) {
			$mailField = $mailField['MailField'];
			if(empty($mailField['use_field'])) {
				continue;
			}
			$valids = explode(',', $mailField['valid_ex']);
			// 対象フィールドがあれば、バリデートグループごとに配列に格納する
			if (in_array('VALID_EMAIL_CONFIRM', $valids)) {
				$dists[$mailField['group_valid']][] = [
					'name' => $mailField['field_name'],
					'value' => @$data['MailMessage'][$mailField['field_name']],
					'isGroupValidComplate' => in_array('VALID_GROUP_COMPLATE', explode(',', $mailField['valid_ex']))
				];
			}
		}
		// チェック
		// バリデートグループにおけるデータ２つを比較し、違えばエラーとする
		foreach ($dists as $key => $dist) {
			if(count($dist) < 2) {
				continue;
			}
			if(count($dist) == 2){
				if ($dist[0]['value'] !== $dist[1]['value']) {
					$this->invalidate($key . '_not_same', __('入力データが一致していません。'));
					if($dist[0]['isGroupValidComplate']) {
						$this->invalidate($dist[0]['name']);
					}
					if($dist[1]['isGroupValidComplate']) {
						$this->invalidate($dist[1]['name']);
					}
				}
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
 */
	public function autoConvert($data) {
		foreach ($this->mailFields as $mailField) {
			$mailField = $mailField['MailField'];
			if (!$mailField['use_field']) {
				continue;
			}

			$value = null;
			if(isset($data['MailMessage'][$mailField['field_name']]) &&
				$data['MailMessage'][$mailField['field_name']] !== "") {
				$value = $data['MailMessage'][$mailField['field_name']];
			}

			if ($value !== null) {

				// 半角処理
				if ($mailField['auto_convert'] == 'CONVERT_HANKAKU') {
					$value = mb_convert_kana($value, 'a');
				}
				// 全角処理
				if ($mailField['auto_convert'] == 'CONVERT_ZENKAKU') {
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

			$data['MailMessage'][$mailField['field_name']] = $value;
		}

		return $data;
	}

/**
 * 初期値の設定をする
 *
 * @return array $data
 */
	public function getDefaultValue($data) {
		$_data = array();

		// 対象フィールドを取得
		if ($this->mailFields) {
			foreach ($this->mailFields as $mailField) {
				$mailField = $mailField['MailField'];
				// 対象フィールドがあれば、バリデートグループごとに配列に格納する
				if (!is_null($mailField['default_value']) && $mailField['default_value'] !== "") {

					if ($mailField['type'] == 'multi_check') {
						$_data['MailMessage'][$mailField['field_name']][0] = $mailField['default_value'];
					} else {
						$_data['MailMessage'][$mailField['field_name']] = $mailField['default_value'];
					}
				}
			}
		}

		if ($data) {
			if (!isset($data['MailMessage'])) {
				$data = array('MailMessage' => $data);
			}
			foreach ($data['MailMessage'] as $key => $value) {
				if(isset($data['MailMessage'][$key])) {
					$_data['MailMessage'][$key] = h($value);
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
 */
	public function convertToDb($dbData) {
		// マルチチェックのデータを｜区切りに変換
		foreach ($this->mailFields as $mailField) {
			$mailField = $mailField['MailField'];
			if ($mailField['type'] == 'multi_check' && $mailField['use_field']) {
				if (!empty($dbData['MailMessage'][$mailField['field_name']])) {
					if (is_array($dbData['MailMessage'][$mailField['field_name']])) {
						$dbData['MailMessage'][$mailField['field_name']] = implode("|", $dbData['MailMessage'][$mailField['field_name']]);
					} else {
						$dbData['MailMessage'][$mailField['field_name']] = $dbData['MailMessage'][$mailField['field_name']];
					}
				}
			}
		}

		// 機種依存文字を変換
		$dbData['MailMessage'] = $this->replaceText($dbData['MailMessage']);

		return $dbData;
	}

/**
 * 機種依存文字の変換処理
 * 内部文字コードがUTF-8である必要がある。
 * 多次元配列には対応していない。
 *
 * @param string $str 変換対象文字列
 * @return string $str 変換後文字列
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
			"\xE3\x8B\xBF" => "令和",
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
			$mailField = $mailField['MailField'];
			if($mailField['no_send']) {
				unset($dbData['message'][$mailField['field_name']]);
			}
			if (!empty($dbData['message'][$mailField['field_name']])) {
				$dbData['message'][$mailField['field_name']] = str_replace(array("<br />", "<br>"), "\n", $dbData['message'][$mailField['field_name']]);
				//$dbData['message'][$mailField['field_name']] = mb_convert_kana($dbData['message'][$mailField['field_name']], "K", "UTF-8");
			}
			if ($mailField['type'] == 'multi_check') {
				if (!empty($dbData['message'][$mailField['field_name']]) && !is_array($dbData['message'][$mailField['field_name']])) {
					$dbData['message'][$mailField['field_name']] = explode("|", $dbData['message'][$mailField['field_name']]);
				}
			}
			if($mailField['type'] == 'file' && isset($dbData['message'][$mailField['field_name'] . '_tmp'])) {
				$dbData['message'][$mailField['field_name']] = $dbData['message'][$mailField['field_name'] . '_tmp'];
				unset($dbData['message'][$mailField['field_name'] . '_tmp']);
			}
		}

		return $dbData;
	}

/**
 * テーブル名を生成する
 * int型でなかったら強制終了
 * @param $mailContentId
 * @return string
 */
	public function createTableName($mailContentId) {
		$mailContentId = (int) $mailContentId;
		if(is_int($mailContentId)) {
			return 'mail_message_' . $mailContentId;
		} else {
			throw new BcException(__d('baser', 'createTableNameの引数$mailContentIdはint型しか受けつけていません。'));
		}
	}

/**
 * フルテーブル名を生成する
 * 
 * @param $mailContentId
 * @return string
 */
	public function createFullTableName($mailContentId) {
		return $this->tablePrefix . $this->createTableName($mailContentId);
	}
	
/**
 * メッセージテーブルを作成する
 *
 * @param string $contentName コンテンツ名
 * @return boolean
 */
	public function createTable($mailContentId) {
		$db = $this->getDataSource();
		$schema = array(
			'id' => array('type' => 'integer', 'null' => false, 'default' => null, 'length' => 8, 'key' => 'primary'),
			'modified' => array('type' => 'datetime', 'null' => true, 'default' => null),
			'created' => array('type' => 'datetime', 'null' => true, 'default' => null),
			'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1))
		);
		$table = $this->createTableName($mailContentId);
		$ret = true;
		if ($this->tableExists($db->config['prefix'] . $table)) {
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
 * メッセージテーブルを削除する
 *
 * @param string $contentName コンテンツ名
 * @return boolean
 */
	public function dropTable($mailContentId) {
		$db = $this->getDataSource();
		$table = $this->createTableName($mailContentId);
		if (!$this->tableExists($db->config['prefix'] . $table)) {
			return true;
		}
		$ret = $db->dropTable(array('table' => $table));
		$this->deleteModelCache();
		return $ret;
	}

/**
 * メッセージファイルにフィールドを追加する
 *
 * @param string $contentName
 * @param string $field
 * @return array
 */
	public function addMessageField($mailContentId, $field) {
		$table = $this->createTableName($mailContentId);
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
 */
	public function delMessageField($mailContentId, $field) {
		$table = $this->createTableName($mailContentId);
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
 */
	public function renameMessageField($mailContentId, $oldFieldName, $newfieldName) {
		$table = $this->createTableName($mailContentId);
		$ret = parent::renameField(array('old' => $oldFieldName, 'new' => $newfieldName, 'table' => $table));
		return $ret;
	}

/**
 * メッセージ保存用テーブルのフィールドを最適化する
 * 初回の場合、id/created/modifiedを追加する
 * 2回目以降の場合は、最後のカラムに追加する
 * 
 * @param array $dbConfig
 * @param int $mailContentId
 * @return boolean
 */
	public function construction($mailContentId) {
		$mailFieldClass = ClassRegistry::init('Mail.MailField');
		// フィールドリストを取得
		$mailFields = $mailFieldClass->find('all', array('conditions' => array('MailField.mail_content_id' => $mailContentId)));
		if (!$this->tableExists($this->createFullTableName($mailContentId))) {
			/* 初回の場合 */
			$this->createTable($mailContentId);
			$this->construction($mailContentId);
		} else {
			/* 2回目以降の場合 */
			$this->setUseTable($mailContentId);
			$this->_schema = null;
			$this->cacheSources = false;
			ClassRegistry::flush();
			$schema = $this->schema();
			$messageFields = array_keys($schema);
			foreach ($mailFields as $mailField) {
				if (!in_array($mailField['MailField']['field_name'], $messageFields)) {
					$this->addMessageField($mailContentId, $mailField['MailField']['field_name']);
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
				if($mailField['MailField']['type'] == 'file') {
					$inData[$mailField['MailField']['field_name'] . ' (' . $mailField['MailField']['name'] . ')'] = $message[$this->alias][$mailField['MailField']['field_name']];
				} else {
					$inData[$mailField['MailField']['field_name'] . ' (' . $mailField['MailField']['name'] . ')'] = $Maildata->toDisplayString(
						$mailField['MailField']['type'],
						$message[$this->alias][$mailField['MailField']['field_name']],
						$Mailfield->getOptions($mailField['MailField'])
					);
				}
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
		$MailContent = ClassRegistry::init('Mail.MailContent');
		$contents = $MailContent->find('all', ['recursive' => -1]);

		$result = true;
		foreach ($contents as $content) {
			if ($this->createTable($content['MailContent']['id'])) {
				if (!$this->construction($content['MailContent']['id'])) {
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
		$db = ConnectionManager::getDataSource('default');
		$db->cacheMethods = false;
		$result = parent::find($type, $query);
		$db->cacheMethods = true;
		return $result;
	}

}
