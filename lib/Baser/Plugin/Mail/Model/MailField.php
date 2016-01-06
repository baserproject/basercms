<?php

/**
 * メールフィールドモデル
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2008 - 2015, baserCMS Users Community
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
 * クラス名
 *
 * @var string
 * @access public
 */
	public $name = 'MailField';

/**
 * ビヘイビア
 * 
 * @var array
 * @access public
 */
	public $actsAs = array('BcCache');

/**
 * validate
 *
 * @var array
 * @access public
 */
	public $validate = array(
		'name' => array(
			array('rule' => array('notEmpty'),
				'message' => "項目名を入力してください。"),
			array('rule' => array('maxLength', 255),
				'message' => '項目名は255文字以内で入力してください。')
		),
		'field_name' => array(
			array('rule' => array('halfTextMailField'),
				'message' => 'フィールド名は半角英数字のみで入力してください。',
				'allowEmpty' => false),
			array('rule' => 'duplicateMailField',
				'message' => '入力されたフィールド名は既に登録されています。'),
			array('rule' => array('maxLength', 255),
				'message' => 'フィールド名は255文字以内で入力してください。')
		),
		'type' => array(
			array('rule' => array('notEmpty'),
				'message' => "タイプを入力してください。")
		),
		'head' => array(
			array('rule' => array('maxLength', 255),
				'message' => '項目見出しは255文字以内で入力してください。')
		),
		'attention' => array(
			array('rule' => array('maxLength', 255),
				'message' => '注意書きは255文字以内で入力してください。')
		),
		'before_attachment' => array(
			array('rule' => array('maxLength', 255),
				'message' => '前見出しは255文字以内で入力してください。')
		),
		'after_attachment' => array(
			array('rule' => array('maxLength', 255),
				'message' => '後見出しは255文字以内で入力してください。')
		),
		'options' => array(
			array('rule' => array('maxLength', 255),
				'message' => 'オプションは255文字以内で入力してください。')
		),
		'class' => array(
			array('rule' => array('maxLength', 255),
				'message' => 'クラス名は255文字以内で入力してください。')
		),
		'separator' => array(
			array('rule' => array('maxLength', 20),
				'message' => '区切り文字は20文字以内で入力してください。')
		),
		'default_value' => array(
			array('rule' => array('maxLength', 255),
				'message' => '初期値は255文字以内で入力してください。')
		),
		'description' => array(
			array('rule' => array('maxLength', 255),
				'message' => '説明文は255文字以内で入力してください。')
		),
		'group_field' => array(
			array('rule' => array('maxLength', 255),
				'message' => 'グループフィールドは255文字以内で入力してください。')
		),
		'group_valid' => array(
			array('rule' => array('maxLength', 255),
				'message' => 'グループ入力チェックは255文字以内で入力してください。')
		)
	);

/**
 * コントロールソースを取得する
 *
 * @param string $field
 * @return array source
 * @access public
 */
	public function getControlSource($field = null) {
		
		$source['type'] = array(
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
		);

		$source['valid'] = array(
			'VALID_NOT_EMPTY'	=> '入力必須',
			'VALID_EMAIL'		=> 'Eメールチェック',
			'/^(|[0-9]+)$/'		=> '数値チェック',
			'/^([0-9]+)$/'		=> '数値チェック（入力必須）'
		);

		$source['valid_ex'] = array(
			'VALID_EMAIL_CONFIRM'	=> 'Eメール比較チェック',
			'VALID_GROUP_COMPLATE'	=> 'グループチェック',
			'VALID_NOT_UNCHECKED'	=> 'チェックボックス未入力チェック',
			'VALID_DATETIME'		=> '日付チェック',
			'VALID_MAX_FILE_SIZE'	=> 'ファイルアップロードサイズ制限',
			'VALID_FILE_EXT'		=> 'ファイル拡張子チェック'
		);

		$source['auto_convert'] = array('CONVERT_HANKAKU' => '半角変換');

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
 * @access public
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
 * 半角英数-_\s
 *
 * @param array $check
 * @return boolean
 * @access public
 */
	public function halfTextMailField($check) {
		$subject = $check[key($check)];
		$pattern = "/^[a-zA-Z0-9-_\s]*$/";
		return !!(preg_match($pattern, $subject) === 1);
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

		if ($this->find('count', array('conditions' => array('MailField.mail_content_id' => $data['MailField']['mail_content_id'], 'MailField.field_name' => $data['MailField']['field_name'])))) {
			$data['MailField']['name'] .= '_copy';
			$data['MailField']['field_name'] .= '_copy';
			return $this->copy(null, $data, $options); // 再帰処理
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
			return $result;
		} else {
			return false;
		}
	}

}
