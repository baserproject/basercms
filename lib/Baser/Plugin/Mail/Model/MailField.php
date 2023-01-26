<?php
/**
 * baserCMS :  Based Website Development Project <https://basercms.net>
 * Copyright (c) baserCMS Users Community <https://basercms.net/community/>
 *
 * @copyright       Copyright (c) baserCMS Users Community
 * @link            https://basercms.net baserCMS Project
 * @package         Mail.Model
 * @since           baserCMS v 0.1.0
 * @license         https://basercms.net/license/index.html
 */

/**
 * メールフィールドモデル
 *
 * @package Mail.Model
 *
 */
class MailField extends MailAppModel
{

	/**
	 * ビヘイビア
	 *
	 * @var array
	 */
	public $actsAs = ['BcCache'];

	/**
	 * MailField constructor.
	 *
	 * @param bool $id
	 * @param null $table
	 * @param null $ds
	 */
	public function __construct($id = false, $table = null, $ds = null)
	{
		parent::__construct($id, $table, $ds);
		$this->validate = [
			'id' => [
				[
					'rule' => 'numeric',
					'on' => 'update',
					'message' => __d('baser', 'IDに不正な値が利用されています。')
				]
			],
			'name' => [
				[
					'rule' => ['notBlank'],
					'message' => __d('baser', '項目名を入力してください。')
				],
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '項目名は255文字以内で入力してください。')
				]
			],
			'field_name' => [
				[
					'rule' => ['halfTextMailField'],
					'message' => __d('baser', 'フィールド名は半角英数字のみで入力してください。'),
					'allowEmpty' => false
				],
				[
					'rule' => 'duplicateMailField',
					'message' => __d('baser', '入力されたフィールド名は既に登録されています。')
				],
				[
					'rule' => ['maxLength', 50],
					'message' => __d('baser', 'フィールド名は50文字以内で入力してください。')
				]
			],
			'type' => [
				[
					'rule' => ['notBlank'],
					'message' => __d('baser', 'タイプを入力してください。')
				]
			],
			'head' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '項目見出しは255文字以内で入力してください。')
				]
			],
			'attention' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '注意書きは255文字以内で入力してください。')
				]
			],
			'before_attachment' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '前見出しは255文字以内で入力してください。')
				]
			],
			'after_attachment' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '後見出しは255文字以内で入力してください。')
				]
			],
			'source' => [
				[
					'rule' => ['sourceMailField'],
					'message' => __d('baser', '選択リストを入力してください。')
				]
			],
			'options' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', 'オプションは255文字以内で入力してください。')
				]
			],
			'size' => [
				[
					'rule' => ['naturalNumber'],
					'message' => __d('baser', '表示サイズは半角数字のみで入力してください。'),
					'allowEmpty' => true,
				],
				[
					'rule' => ['maxLength', 9],
					'message' => __d('baser', '表示サイズは9文字以内で入力してください。')
				]
			],
			'rows' => [
				[
					'rule' => ['naturalNumber'],
					'message' => __d('baser', '行数は半角数字のみで入力してください。'),
					'allowEmpty' => true,
				],
				[
					'rule' => ['maxLength', 9],
					'message' => __d('baser', '行数は9文字以内で入力してください。')
				]
			],
			'maxlength' => [
				[
					'rule' => ['naturalNumber'],
					'message' => __d('baser', '最大値は半角数字のみで入力してください。'),
					'allowEmpty' => true,
				],
				[
					'rule' => ['maxLength', 9],
					'message' => __d('baser', '最大値は9文字以内で入力してください。')
				]
			],
			'class' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', 'クラス名は255文字以内で入力してください。')
				]
			],
			'separator' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '区切り文字は255文字以内で入力してください。')
				]
			],
			'default_value' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '初期値は255文字以内で入力してください。')
				]
			],
			'description' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', '説明文は255文字以内で入力してください。')
				]
			],
			'group_field' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', 'グループフィールドは255文字以内で入力してください。')
				]
			],
			'group_valid' => [
				[
					'rule' => ['maxLength', 255],
					'message' => __d('baser', 'グループ入力チェックは255文字以内で入力してください。')
				]
			]
		];
	}

	/**
	 * コントロールソースを取得する
	 *
	 * @param string $field
	 * @return array source
	 */
	public function getControlSource($field = null)
	{
		$source['type'] = [
			'text' => __d('baser', 'テキスト'),
			'textarea' => __d('baser', 'テキストエリア'),
			'radio' => __d('baser', 'ラジオボタン'),
			'select' => __d('baser', 'セレクトボックス'),
			'email' => __d('baser', 'Eメール'),
			'multi_check' => __d('baser', 'マルチチェックボックス'),
			'file' => __d('baser', 'ファイル'),
			'autozip' => __d('baser', '自動補完郵便番号'),
			'pref' => __d('baser', '都道府県リスト'),
			'date_time_wareki' => __d('baser', '和暦日付'),
			'date_time_calender' => __d('baser', 'カレンダー'),
			'tel' => __d('baser', '電話番号'),
			'number' => __d('baser', '数値'),
			'password' => __d('baser', 'パスワード'),
			'hidden' => __d('baser', '隠しフィールド')
		];
		$source['valid'] = [
			'VALID_NOT_EMPTY' => __d('baser', '入力必須'),
			'VALID_EMAIL' => __d('baser', 'Eメールチェック（入力必須）'),
			'/^(|[0-9]+)$/' => __d('baser', '数値チェック'),
			'/^([0-9]+)$/' => __d('baser', '数値チェック（入力必須）'),
			'/^(|[0-9\-]+)$/' => __d('baser', '数値ハイフンチェック'),
			'/^([0-9\-]+)$/' => __d('baser', '数値ハイフンチェック（入力必須）')
		];
		$source['valid_ex'] = [
			'VALID_EMAIL_CONFIRM' => __d('baser', 'Eメール比較チェック'),
			'VALID_GROUP_COMPLATE' => __d('baser', 'グループチェック'),
			'VALID_NOT_UNCHECKED' => __d('baser', 'チェックボックス未入力チェック'),
			'VALID_DATETIME' => __d('baser', '日付チェック'),
			'VALID_MAX_FILE_SIZE' => __d('baser', 'ファイルアップロードサイズ制限'),
			'VALID_FILE_EXT' => __d('baser', 'ファイル拡張子チェック'),
			'VALID_ZENKAKU_KATAKANA' => __d('baser', '全角カタカナチェック'),
			'VALID_ZENKAKU_HIRAGANA' => __d('baser', '全角ひらがなチェック'),
			'VALID_NOT_EMOJI' 		=> __d('baser', '絵文字を含めない'),
			'VALID_REGEX' => __d('baser', '正規表現チェック'),
		];
		$source['auto_convert'] = ['CONVERT_HANKAKU' => __d('baser', '半角変換')];
		if (!$field) {
			return $source;
		}

		return $source[$field];
	}

	/**
	 * 同じ名称のフィールド名がないかチェックする
	 * 同じメールコンテンツが条件
	 *
	 * @param array $check
	 * @return boolean
	 */
	public function duplicateMailField($check)
	{
		$conditions = ['MailField.' . key($check) => $check[key($check)],
			'MailField.mail_content_id' => $this->data['MailField']['mail_content_id']];
		if ($this->exists()) {
			$conditions['NOT'] = ['MailField.id' => $this->id];
		}
		$ret = $this->find('first', ['conditions' => $conditions]);
		if ($ret) {
			return false;
		}

		return true;
	}

	/**
	 * メールフィールドの値として正しい文字列か検証する
	 * 半角英数-_
	 *
	 * @param array $check
	 * @return boolean
	 */
	public function halfTextMailField($check)
	{
		$subject = $check[key($check)];
		$pattern = "/^[a-zA-Z0-9-_]*$/";
		return !!(preg_match($pattern, $subject) === 1);
	}

	/**
	 * 選択リストの入力チェック
	 *
	 * @param integer $check
	 */
	public function sourceMailField($check)
	{
		switch($this->data['MailField']['type']) {
			case 'radio':        // ラジオボタン
			case 'select':        // セレクトボックス
			case 'multi_check':    // マルチチェックボックス
			case 'autozip':        // 自動保管郵便番号
				// 選択リストのチェックを行う
				return (!empty($check[key($check)]));
		}
		// 選択リストが不要のタイプの時はチェックしない
		return true;
	}

	/**
	 * フィールドデータをコピーする
	 *
	 * @param int $id
	 * @param array $data
	 * @return mixed UserGroup Or false
	 */
	public function copy($id, $data = [], $options = [])
	{
		$options = array_merge(
			[
				'sortUpdateOff' => false,
			],
			$options
		);

		extract($options);

		if ($id) {
			$data = $this->find(
				'first',
				[
					'conditions' => [
						'MailField.id' => $id
					],
					'recursive' => -1
				]
			);
		}
		$oldData = $data;

		if ($this->find('count', ['conditions' => ['MailField.mail_content_id' => $data['MailField']['mail_content_id'], 'MailField.field_name' => $data['MailField']['field_name']]])) {
			$data['MailField']['name'] .= '_copy';
			if (strlen($data['MailField']['name']) >= 64) {
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
				$data = $event->result === true? $event->data['data'] : $event->result;
			}
		}

		$data['MailField']['no'] = $this->getMax(
				'no',
				[
					'MailField.mail_content_id' => $data['MailField']['mail_content_id']
				]
			) + 1;
		if (!$sortUpdateOff) {
			$data['MailField']['sort'] = $this->getMax('sort') + 1;
		}
		$data['MailField']['use_field'] = false;

		unset($data['MailField']['id']);
		unset($data['MailField']['modified']);
		unset($data['MailField']['created']);

		$this->create($data);
		$result = $this->save();
		if (!$result) {
			return false;
		}

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
	}

	/**
	 * 選択リストのソースを整形する
	 * 空白と \r を除外し、改行で結合する
	 * | の対応は後方互換として残しておく
	 * @param string $source 選択リストソース
	 * @return string 整形後選択リストソース
	 */
	public function formatSource($source)
	{
		$source = str_replace('|', "\n", $source);
		$values = explode("\n", $source);
		$sourceList = [];
		foreach($values as $value) {
			$sourceList[] = preg_replace("/(^\s+|\r|\n\s+$)/u", '', $value);
		}
		return implode("\n", $sourceList);
	}

}
