<?php
/**
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright (c) baserCMS Users Community <http://basercms.net/community/>
 *
 * @copyright		Copyright (c) baserCMS Users Community
 * @link			http://basercms.net baserCMS Project
 * @package			Mail.Test.Case.Model
 * @since			baserCMS v 3.0.0
 * @license			http://basercms.net/license/index.html
 */

App::uses('MailMessage', 'Mail.Model');

/**
 * Class MailMessageTest
 * 
 * @property MailMessage $MailMessage
 */
class MailMessageTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.SiteConfig',
		'baser.Default.Site',
		'baser.Default.Content',
		'plugin.Mail.Default/MailMessage',
		'plugin.Mail.Default/MailConfig',
		'plugin.Mail.Model/MailMessage/MailContentMailMessage',
		'plugin.Mail.Model/MailMessage/MailFieldMailMessage',
	);

	public function setUp() {
		$this->MailMessage = ClassRegistry::init('Mail.MailMessage');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->MailMessage);
		parent::tearDown();
	}
	
/**
 * モデルのセットアップを行う
 * 
 * MailMessageモデルは利用前にこのメソッドを呼び出しておく必要あり
 * 
 * @param type $mailContentId
 * @return boolean
 */
	public function testSetup() {
		$this->MailMessage->setup(1);
		$this->assertEquals('mail_message_1', $this->MailMessage->createTableName(1), 'テーブルを正しく設定できません');

		// setupUpload
		$saveDir = $this->MailMessage->Behaviors->BcUpload->settings['MailMessage']['saveDir'];
		$expected = "mail" . DS . "limited" . DS . '1' . DS . "messages";
		$this->assertEquals($expected, $saveDir, 'アップロード設定を正しく設定できません');
	}

/**
 * テーブル名を設定する
 */
	public function testSetUseTable() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * アップロード設定を行う
 */
	public function testSetupUpload() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * beforeSave
 *
 * @return boolean
 */
	public function testBeforeSave() {
		// 初期化
		$this->MailMessage->createTable(1);
		// ======================================================
		// createTable の際、CakeSchema::read(); が実行され、
		// ClassRegistry内のモデルが全てAppModelに変換され MailMessage::setup() が失敗する
		// その為、ClassRegistry::flush() を行うが、次は、setup() 内の setupUpload() で、Behavior のロードに失敗する
		// といったわけで、ClassRegistry::addObject で強制的に更新
		// ======================================================
		ClassRegistry::flush();
		ClassRegistry::addObject('MailMessage', $this->MailMessage);
		$this->MailMessage->setup(1);
		$this->MailMessage->data = array('MailMessage' => array(
			'name_1' => "\xE2\x85\xA0\xE2\x85\xA1\xE3\x8D\x8D\xE3\x88\xB9",
			'name_2' => 'hoge',
			'root' => '2',
			'category' => '2'
		));
		$result = $this->MailMessage->save();
		$this->MailMessage->dropTable(1);
		$this->assertEquals('IIIメートル(代)', $result['MailMessage']['name_1'], 'beforeSaveでデータベース用のデータに変換されていません');
	}

/**
 * Called after data has been checked for errors
 *
 * @dataProvider validateDataProvider
 */
	public function testValidate($id, $data, $expected, $message) {
		$this->MailMessage->setup($id);
		$this->MailMessage->data = array('MailMessage' => $data);

		$this->MailMessage->validates();
		$this->assertEquals($expected, $this->MailMessage->validationErrors, $message);
	}

	public function validateDataProvider() {
		return array(
			// 正常系
			array(1, array(
				'email_1' => 'a@example.co.jp', 'email_2' => 'a@example.co.jp',
				'tel_1' => '000', 'tel_2' => '0000', 'tel_3' => '0000',
				'category' => 1, 'message' => array('year' => 9999, 'month' => 99, 'day' => 99),
				'name_1' => 'baser', 'name_2' => 'cms',
				'root' => '検索エンジン', 
			),
			array(), 'バリデーションチェックが正しく行われていません'),
			// 異常系
			array(1, array(
				'email_1' => 'email', 'email_2' => 'email_hoge', // Eメール確認チェック
				'tel_1' => 'num1', 'tel_2' => false, 'tel_3' => false, // 不完全データチェック
				'category' => false, 'message' => false, // 拡張バリデートチェック, FixtureでmessageにVALID_DATETIME付与済み
				'name_1' => '', 'name_2' => '', // バリデートグループエラーチェック
			),
			array(
				'name_1' => array('必須項目です。'),
				'name_2' => array('必須項目です。'),
				'email_1' => array('形式が不正です。',true),
				'email_2' => array('形式が不正です。',true),
				'root' => array('必須項目です。'),
				'email_not_same' => array(true),
				'tel_not_complate' => array(true),
				'tel_1' => array(true),
				'tel_2' => array(true),
				'tel_3' => array(true),
				'category' => array('必須項目です。'),
				'name' => array(true, true),
				'email' => array(true, true)
			), 'バリデーションチェックが正しく行われていません'),
		);
	}

/**
 * バリデート処理
 */
	public function testBeforeValidate() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * Called after data has been checked for errors
 */
	public function testAfterValidate() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * 自動変換
 * 確認画面で利用される事も踏まえてバリデートを通す為の
 * 可能な変換処理を行う。
 *
 * @param string $auto_convert 変換タイプ
 * @param string $value 入力値
 * @param string $expected 期待値
 * @param string $message テスト失敗時に表示されるメッセージ
 * @dataProvider autoConvertDataProvider
 */
	public function testAutoConvert($auto_convert, $value, $expected, $message) {
		// 初期化
		$this->MailMessage->mailFields = array(
			array('MailField' => array(
				'field_name' => 'value',
				'auto_convert' => $auto_convert,
				'use_field' => true,
			)
		));
		$data = array('MailMessage' => array(
			'value' => $value
		));

		// 実行
		$result = $this->MailMessage->autoConvert($data);

		$this->assertEquals($expected, $result['MailMessage']['value'], $message);
	}

	public function autoConvertDataProvider() {
		return array(
			array('CONVERT_HANKAKU', '１２３ａｂｃ', '123abc', '半角変換が正しく処理されていません'),
			array('CONVERT_ZENKAKU', '123abc', '１２３ａｂｃ', '全角変換が正しく処理されていません'),
			array(null, '<!-- hoge', '&lt;!-- hoge', 'サニタイズが正しく処理されていません'),
			array(null, '    hoge    ', 'hoge', '空白削除が正しく処理されていません'),
		);
	}

/**
 * 初期値の設定をする
 *
 * @param string $type
 * @dataProvider getDefaultValueDataProvider
 */
	public function testGetDefaultValue($type) {
		// 初期化
		$this->MailMessage->mailFields = array(
			array('MailField' => array(
				'field_name' => 'value',
				'use_field' => true,
				'default_value' => 'default',
				'type' => $type,
			)
		));
		$data = array('MailMessage' => array(
			'key1' => 'hoge1',
			'key2' => 'hoge2',
		));

		// 実行
		$result = $this->MailMessage->getDefaultValue($data);

		if ($type != 'multi_check') {
			$expected = array(
				'MailMessage' => array(
					'value' => 'default',
					'key1' => 'hoge1',
					'key2' => 'hoge2'
			));
			$this->assertEquals($expected, $result);

		} else {
			$this->assertEquals('default', $result['MailMessage']['value'][0]);
		}
	}

	public function getDefaultValueDataProvider() {
		return array(
			array(null),
			array('multi_check'),
		);
	}

/**
 * データベース用のデータに変換する
 *
 * @param array $type
 * @param mixed $value データベース用のデータの値
 * @param mixed $expected 期待値
 * @dataProvider convertToDbDataProvider
 */
	public function testConvertToDb($type, $value, $expected) {
		// 初期化
		$this->MailMessage->mailFields = array(
			array('MailField' => array(
				'field_name' => 'value',
				'use_field' => true,
				'type' => $type,
			)
		));
		$dbData = array('MailMessage' => array(
			'value' => $value,
		));

		// 実行
		$result = $this->MailMessage->convertToDb($dbData);

		$this->assertEquals($expected, $result['MailMessage']['value']);
	}

	public function convertToDbDataProvider() {
		return array(
			array(null, 'hoge', 'hoge'),
			array('multi_check', 'hoge', 'hoge'),
			array('multi_check', array('hoge1', 'hoge2', 'hoge3'), 'hoge1|hoge2|hoge3'),
			array(null, "\xE2\x85\xA0\xE2\x85\xA1\xE3\x8D\x8D\xE3\x88\xB9", 'IIIメートル(代)'),
			array('multi_check', array("\xE2\x85\xA0", "\xE2\x85\xA1", "\xE3\x8D\x8D", "\xE3\x88\xB9"), 'I|II|メートル|(代)'),
		);
	}

/**
 * 機種依存文字の変換処理
 * 内部文字コードがUTF-8である必要がある。
 * 多次元配列には対応していない。
 */
	public function testReplaceText() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * メール用に変換する
 *
 * @param int $no_send no_sendの値
 * @param string $type 指定するタイプ
 * @dataProvider convertDatasToMailDataProvider
 */
	public function testConvertDatasToMail($no_send, $type) {
		// 初期化
		$this->MailMessage->mailFields = array(
			array('MailField' => array(
				'field_name' => 'value',
				'use_field' => true,
				'no_send' => $no_send,
				'type' => $type,
			)
		));
		$dbData = array(
			'mailFields' => array(
				'key1' => array('MailField' => array(
					'before_attachment' => '<before>before_attachment',
					'after_attachment' => '<after><br>after_attachment',
					'head' => '<head><br>head',
				)
			)),
			'message' => array(
				'value' => '<br><br />hoge',
			)
		);
		if ($type == 'file') {
			$dbData['message']['value_tmp'] = 'hoge_tmp';
		}


		// 実行
		$result = $this->MailMessage->convertDatasToMail($dbData);

		if (is_null($type)) {
			if (!$no_send) {
				$expectedMailField = array(
					'before_attachment' => 'before_attachment',
					'after_attachment' => "\nafter_attachment",
					'head' => 'head',
				);
				$this->assertEquals($expectedMailField, $result['mailFields']['key1']['MailField'], 'mailFieldsに正しい値を格納できていません');

				$expectedMessage = "\n\nhoge";
				$this->assertEquals($expectedMessage, $result['message']['value']);

			} else {
				$this->assertEmpty($result['message']);
			}

		} else if ($type == 'multi_check') {
			$expectedMessage = "\n\nhoge";
			$this->assertEquals($expectedMessage, $result['message']['value'][0]);

		} else if ($type == 'file') {
			$expectedMessage = 'hoge_tmp';
			$this->assertEquals($expectedMessage, $result['message']['value']);
		}

	}

	public function convertDatasToMailDataProvider() {
		return array(
			array(0, null),
			array(1, null),
			array(0, 'multi_check'),
			array(0, 'file'),
		);
	}

/**
 * テーブル名を生成する
 * int型でなかったら強制終了
 */
	public function testCreateTableName() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * フルテーブル名を生成する
 */
	public function testCreateFullTableName() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * メッセージテーブルを作成する
 */
	public function testCreateTable() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * メッセージテーブルを削除する
 */
	public function testDropTable() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

/**
 * メッセージファイルのフィールドを追加/名前変更/削除する
 */
	public function testAddRenameDelMessageField() {
		$db = $this->MailMessage->getDataSource();
		switch ($db->config['datasource']) {
			case 'Database/BcPostgres' :
				$this->markTestIncomplete('このテストは、まだ実装されていません。');
				$command = '\d+';
				break;
			case 'Database/BcMysql' :
				$command = 'DESCRIBE';
				break;
			case 'Database/BcSqlite' :
				$this->markTestIncomplete('このテストは、まだ実装されていません。');
				$command = '.schema';
			default :
		}

		// 初期化
		$id = 1;
		$fullTable = $this->MailMessage->createFullTableName($id);
		$fieldName = 'hogeField';
		$toFieldName = 'hogeField_renamed';

		$this->MailMessage->createTable($id);
		$this->MailMessage->construction($id);
		
		// フィールド追加
		$this->MailMessage->addMessageField($id, $fieldName);
		$sql = $command . " $fullTable $fieldName";
		$this->assertNotEmpty($this->MailMessage->query($sql), 'メッセージファイルにフィールドを正しく追加できません');

		// フィールド名変更
		$this->MailMessage->renameMessageField($id, $fieldName, $toFieldName);
		$sql = $command . " $fullTable $toFieldName";
		$this->assertNotEmpty($this->MailMessage->query($sql), 'メッセージファイルのフィールド名を正しく変更できません');

		// フィールド削除
		$this->MailMessage->delMessageField($id, $toFieldName);
		$sql = $command . " $fullTable $toFieldName";
		$this->assertEmpty($this->MailMessage->query($sql), 'メッセージファイルのフィールドを正しく削除できません');

		$this->MailMessage->dropTable($id);

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
	public function testConstruction() {
		$db = $this->MailMessage->getDataSource();

		switch ($db->config['datasource']) {
			case 'Database/BcPostgres' :
				$this->markTestIncomplete('このテストは、まだ実装されていません。');
				break;
			case 'Database/BcMysql' :
				$command = 'EXPLAIN';
				break;
			case 'Database/BcSqlite' :
				$this->markTestIncomplete('このテストは、まだ実装されていません。');
				$command = '.schema';
			default :
		}

		$id = 1;
		$fullTable = $this->MailMessage->createFullTableName(1);

		$this->MailMessage->dropTable($id);

		// 一回目
		$this->MailMessage->construction($id);
		$this->assertTrue($this->MailMessage->tableExists($fullTable), 'メッセージテーブルを正しく作成できません');
		
		$expectColumns = array('id', 'modified', 'created');
		$sql = $command . " $fullTable";
		$resultColumns = array();
		foreach ($this->MailMessage->query($sql) as $key => $value) {
			$resultColumns[] = $value['COLUMNS']['Field'];
		}
		foreach($expectColumns as $column) {
			$this->assertContains($column, $resultColumns, '正しくカラムが追加されていません');
		}

		// 二回目
		$this->MailMessage->construction($id);

		$this->MailField = ClassRegistry::init('Mail.MailField');
		$expectColumns = $this->MailField->find('list', array(
			'fields' => 'field_name',
			'conditions' => array('mail_content_id' => 1),
		));
		array_unshift($expectColumns, 'id', 'modified', 'created');

		$sql = $command . " $fullTable";
		$resultColumns = array();
		foreach ($this->MailMessage->query($sql) as $key => $value) {
			$resultColumns[] = $value['COLUMNS']['Field'];
		}
		$this->assertEquals($expectColumns, $resultColumns, '正しくカラムが追加されていません');

	}

/**
 * 受信メッセージの内容を表示状態に変換する
 * 
 * @param int $id
 * @param array $messages
 * @return array
 */
	public function testConvertMessageToCsv() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$messages = array(
			array('MailMessage' => array(
				'id' => 1, 'name_1' => 'v1', 'name_2' => 'v2',
				'name_kana_1' => 'v3', 'name_kana_2' => 'v4', 'sex' => 'v5',
				'email_1' => 'v6', 'email_2' => 'v7', 'tel_1' => 'v8',
				'tel_2' => 'v9', 'tel_3' => 'v10', 'zip' => 'v11',
				'address_1' => 'v12', 'address_2' => 'v13', 'address_3' => 'v14',
				'category' => 'v15', 'message' => 'v16', 'root' => 'v17',
				'root_etc' => 'v18', 'created' => 'v19', 'modified' => 'v20',
				'modified' => 'v21',
			)),
			array('MailMessage' => array(
				'id' => 2, 'name_1' => 'v1', 'name_2' => 'v2',
				'name_kana_1' => 'v3', 'name_kana_2' => 'v4', 'sex' => 'v5',
				'email_1' => 'v6', 'email_2' => 'v7', 'tel_1' => 'v8',
				'tel_2' => 'v9', 'tel_3' => 'v10', 'zip' => 'v11',
				'address_1' => 'v12', 'address_2' => 'v13', 'address_3' => 'v14',
				'category' => 'v15', 'message' => 'v16', 'root' => 'v17',
				'root_etc' => 'v18', 'created' => 'v19', 'modified' => 'v20',
				'modified' => 'v21',
			))
		);

		$expected = array(
			0 => array(
				'MailMessage' => array(
					'NO' => 1, 'name_1 (姓漢字)' => 'v1', 'name_2 (名漢字)' => 'v2',
					'name_kana_1 (姓カナ)' => 'v3', 'name_kana_2 (名カナ)' => 'v4', 'sex (性別)' => '',
					'email_1 (メールアドレス)' => 'v6', 'email_2 (メールアドレス確認)' => 'v7',
					'tel_1 (電話番号１)' => 'v8', 'tel_2 (電話番号２)' => 'v9', 'tel_3 (電話番号３)' => 'v10',
					'zip (郵便番号)' => 'v11', 'address_1 (都道府県)' => '', 'address_2 (市区町村・番地)' => 'v13',
					'address_3 (建物名)' => 'v14', 'category (お問い合わせ項目)' => '', 'message (お問い合わせ内容)' => 'v16',
					'root (ルート)' => '', 'root_etc (ルートその他)' => 'v18', '作成日' => 'v19', '更新日' => 'v21'
				)
			),
			1 => array(
				'MailMessage' => array(
					'NO' => 2, 'name_1 (姓漢字)' => 'v1', 'name_2 (名漢字)' => 'v2',
					'name_kana_1 (姓カナ)' => 'v3', 'name_kana_2 (名カナ)' => 'v4', 'sex (性別)' => '',
					'email_1 (メールアドレス)' => 'v6', 'email_2 (メールアドレス確認)' => 'v7',
					'tel_1 (電話番号１)' => 'v8', 'tel_2 (電話番号２)' => 'v9', 'tel_3 (電話番号３)' => 'v10',
					'zip (郵便番号)' => 'v11', 'address_1 (都道府県)' => '', 'address_2 (市区町村・番地)' => 'v13',
					'address_3 (建物名)' => 'v14', 'category (お問い合わせ項目)' => '', 'message (お問い合わせ内容)' => 'v16',
					'root (ルート)' => '', 'root_etc (ルートその他)' => 'v18', '作成日' => 'v19', '更新日' => 'v21'
				)
			)
		);
		
		$result = $this->MailMessage->convertMessageToCsv(1, $messages);
		$this->assertEquals($expected, $result, '受信メッセージの内容を表示状態に正しく変換できません');
	}

/**
 * メール受信テーブルを全て再構築
 * 
 * @return boolean
 */
	public function testReconstructionAll() {
		$id = 1;
		$fullTable = $this->MailMessage->createFullTableName($id);
		$this->MailMessage->dropTable($id);
		$this->assertTrue($this->MailMessage->reconstructionAll());
		$this->assertTrue($this->MailMessage->tableExists($fullTable));
	}

	
/**
 * find
 * 
 * @param String $type
 * @param mixed $query
 * @return Array
 */
	public function testFind() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');
	}

}
