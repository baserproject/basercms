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

/**
 * メールフィールドモデル
 *
 * @package Mail.Model
 *
 */
class MailField extends MailAppModel {

/**
 * ビヘイビア
 * 
 * @var array
 */
	public $actsAs = array('BcCache');

/**
 * validate
 *
 * @var array
 */
	public $validate = [
		'id' => [
			['rule' => 'numeric', 'on' => 'update', 'message' => 'IDに不正な値が利用されています。']
		],
		'name' => [
			['rule' => ['notBlank'],
				'message' => "項目名を入力してください。"],
			['rule' => ['maxLength', 255],
				'message' => '項目名は255文字以内で入力してください。']
		],
		'field_name' => [
			['rule' => ['halfTextMailField'],
				'message' => 'フィールド名は半角英数字のみで入力してください。',
				'allowEmpty' => false],
			['rule' => 'duplicateMailField',
				'message' => '入力されたフィールド名は既に登録されています。'],
			['rule' => ['maxLength', 255],
				'message' => 'フィールド名は255文字以内で入力してください。']
		],
		'type' => [
			['rule' => ['notBlank'],
				'message' => "タイプを入力してください。"]
		],
		'head' => [
			['rule' => ['maxLength', 255],
				'message' => '項目見出しは255文字以内で入力してください。']
		],
		'attention' => [
			['rule' => ['maxLength', 255],
				'message' => '注意書きは255文字以内で入力してください。']
		],
		'before_attachment' => [
			['rule' => ['maxLength', 255],
				'message' => '前見出しは255文字以内で入力してください。']
		],
		'after_attachment' => [
			['rule' => ['maxLength', 255],
				'message' => '後見出しは255文字以内で入力してください。']
		],
		'source' => [
			['rule' => ['sourceMailField'],
				'message' => '選択リストを入力してください。']
		],
		'options' => [
			['rule' => ['maxLength', 255],
				'message' => 'オプションは255文字以内で入力してください。']
		],
		'class' => [
			['rule' => ['maxLength', 255],
				'message' => 'クラス名は255文字以内で入力してください。']
		],
		'separator' => [
			['rule' => ['maxLength', 20],
				'message' => '区切り文字は20文字以内で入力してください。']
		],
		'default_value' => [
			['rule' => ['maxLength', 255],
				'message' => '初期値は255文字以内で入力してください。']
		],
		'description' => [
			['rule' => ['maxLength', 255],
				'message' => '説明文は255文字以内で入力してください。']
		],
		'group_field' => [
			['rule' => ['maxLength', 255],
				'message' => 'グループフィールドは255文字以内で入力してください。']
		],
		'group_valid' => [
			['rule' => ['maxLength', 255],
				'message' => 'グループ入力チェックは255文字以内で入力してください。']
		]
	];

/**
 * コントロールソースを取得する
 *
 * @param string $field
 * @return array source
 */
	public function getControlSource($field = null) {
		$source['type'] = [
			'text'				=> 'テキスト',
			'textarea'			=> 'テキストエリア',
			'radio'				=> 'ラジオボタン',
			'select'			=> 'セレクトボックス',
			'email'				=> 'Eメール',
			'multi_check'		=> 'マルチチェックボックス',
			'file'				=> 'ファイル',
			'autozip'			=> '自動補完郵便番号',
			'pref'				=> '都道府県リスト',
			'date_time_wareki'	=> '和暦日付',
			'date_time_calender'=> 'カレンダー',
			'hidden'			=> '隠しフィールド'
		];
		$source['valid'] = [
			'VALID_NOT_EMPTY'	=> '入力必須',
			'VALID_EMAIL'		=> 'Eメールチェック（入力必須）',
			'/^(|[0-9]+)$/'		=> '数値チェック',
			'/^([0-9]+)$/'		=> '数値チェック（入力必須）'
		];
		$source['valid_ex'] = [
			'VALID_EMAIL_CONFIRM'	=> 'Eメール比較チェック',
			'VALID_GROUP_COMPLATE'	=> 'グループチェック',
			'VALID_NOT_UNCHECKED'	=> 'チェックボックス未入力チェック',
			'VALID_DATETIME'		=> '日付チェック',
			'VALID_MAX_FILE_SIZE'	=> 'ファイルアップロードサイズ制限',
			'VALID_FILE_EXT'		=> 'ファイル拡張子チェック',
			'VALID_ZENKAKU_KATAKANA' 		=> '全角カタカナチェック'
		];
		$source['auto_convert'] = ['CONVERT_HANKAKU' => '半角変換'];
		if ($field) {
			return $source[$field];
		} else {
			return $source;
		}
	}

/**
 * 同じ名称のフィールド名がないかチェックする
 * 同じメールコンテンツが条件
 *
 * @param array $check
 * @return boolean
 */
	public function duplicateMailField($check) {
		$conditions = array('MailField.' . key($check) => $check[key($check)],
			'MailField.mail_content_id' => $this->data['MailField']['mail_content_id']);
		if ($this->exists()) {
			$conditions['NOT'] = array('MailField.id' => $this->id);
		}
		$ret = $this->find('first', array('conditions' => $conditions));
		if ($ret) {
			return false;
		} else {
			return true;
		}
	}

/**
 * メールフィールドの値として正しい文字列か検証する
 * 半角英数-_
 *
 * @param array $check
 * @return boolean
 */
	public function halfTextMailField($check) {
		$subject = $check[key($check)];
		$pattern = "/^[a-zA-Z0-9-_]*$/";
		return !!(preg_match($pattern, $subject) === 1);
	}

/**
 * 選択リストの入力チェック
 * 
 * @param type $check
 */
	public function sourceMailField($check) {
		switch ($this->data['MailField']['type']) {
			case 'radio':		// ラジオボタン
			case 'select':		// セレクトボックス
			case 'multi_check':	// マルチチェックボックス
			case 'autozip':		// 自動保管郵便番号
				// 選択リストのチェックを行う
				$result = (!empty($check[key($check)]));
				break;
			default:
				// 選択リストが不要のタイプの時はチェックしない
				$result = true;
				break;
		}
		return $result;
	}

/**
 * フィールドデータをコピーする
 *
 * @param int $id
 * @param array $data
 * @return mixed UserGroup Or false
 */
	public function copy($id, $data = array(), $options = array()) {
		$options = array_merge(array(
			'sortUpdateOff' => false,
			), $options);

		extract($options);

		if ($id) {
			$data = $this->find('first', array('conditions' => array('MailField.id' => $id), 'recursive' => -1));
		}
		$oldData = $data;

		if ($this->find('count', array('conditions' => array('MailField.mail_content_id' => $data['MailField']['mail_content_id'], 'MailField.field_name' => $data['MailField']['field_name'])))) {
			$data['MailField']['name'] .= '_copy';
			if(strlen($data['MailField']['name']) >= 64) {
				return false;
			}
			$data['MailField']['field_name'] .= '_copy';
			return $this->copy(null, $data, $options); // 再帰処理
		}

		// EVENT MailField.beforeCopy
		if (!$sortUpdateOff) {
			$event = $this->dispatchEvent('beforeCopy', [
				'data' => $data,
				'id' => $id,
			]);
			if ($event !== false) {
				$data = $event->result === true ? $event->data['data'] : $event->result;
			}
		}

		$data['MailField']['no'] = $this->getMax('no', array('MailField.mail_content_id' => $data['MailField']['mail_content_id'])) + 1;
		if (!$sortUpdateOff) {
			$data['MailField']['sort'] = $this->getMax('sort') + 1;
		}
		$data['MailField']['use_field'] = false;

		unset($data['MailField']['id']);
		unset($data['MailField']['modified']);
		unset($data['MailField']['created']);

		$this->create($data);
		$result = $this->save();
		if ($result) {
			$result['MailField']['id'] = $this->getInsertID();
			$data = $result;

			// EVENT MailField.afterCopy
			if (!$sortUpdateOff) {
				$event = $this->dispatchEvent('afterCopy', [
					'id' => $data['MailField']['id'],
					'data' => $data,
					'oldId' => $id,
					'oldData' => $oldData,
				]);
			}

			return $result;
		} else {
			return false;
		}
	}

/**
 * After Delete 
 */
	public function afterDelete() {
		parent::afterDelete();
		// フロントエンドでは、MailContentのキャッシュを利用する為削除しておく
		$MailContent = ClassRegistry::init('Mail.MailContent');
		$MailContent->delCache();
	}

/**
 * After Save
 * 
 * @param bool $created
 * @param array $options
 */
	public function afterSave($created, $options = array()) {
		parent::afterSave($created, $options);
		// フロントエンドでは、MailContentのキャッシュを利用する為削除しておく
		$MailContent = ClassRegistry::init('Mail.MailContent');
		$MailContent->delCache();
	}

}
