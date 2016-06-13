<?php

/**
 * test for MessageModel
 *
 * baserCMS : Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2015, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 * @package         Mail.Test.Case.Model
 * @copyright       Copyright 2008 - 2015, baserCMS Users Community
 * @link            http://basercms.net baserCMS Project
 * @since           baserCMS v 3.0.0-beta
 * @license         http://basercms.net/license/index.html
 */
App::uses('Message', 'Mail.Model');

class MessageTest extends BaserTestCase {

	public $fixtures = array(
		'baser.Default.SiteConfig',
		'baser.Default.pluginContent',
		'plugin.mail.Default/Message',
		'plugin.mail.Default/MailConfig',
		'plugin.mail.Model/Message/MailContentMessage',
		'plugin.mail.Model/Message/MailFieldMessage',
	);

	public function setUp() {
		$this->Message = ClassRegistry::init('Mail.Message');
		parent::setUp();
	}

	public function tearDown() {
		unset($this->Message);
		parent::tearDown();
	}
	
/**
 * モデルのセットアップを行う
 * 
 * Messageモデルは利用前にこのメソッドを呼び出しておく必要あり
 * 
 * @param type $mailContentId
 * @return boolean
 */
	public function testSetup() {
		$this->Message->setup(1);
		$this->assertEquals('mysite_test_contact_', $this->Message->tablePrefix, 'テーブルプレフィックスを正しく設定できません');

		// setupUpload
		$this->Message->setup(99);
		// protectedな値にアクセスするため配列にキャストする
		$result = (array) $this->Message->Behaviors;
		$saveDir = $result["\0*\0_loaded"]['BcUpload']->settings['Message']['saveDir'];
		$expected = "mail" . DS . "limited" . DS . 'uploader' . DS . "messages";
		$this->assertEquals($expected, $saveDir, 'アップロード設定を正しく設定できません');
	}

/**
 * テーブルプレフィックスを設定する
 */
	public function testSetTablePrefix() {
		$this->assertTrue($this->Message->setTablePrefix('message'));
		$this->assertEquals('mysite_test_', $this->Message->tablePrefix);
		
		$this->assertTrue($this->Message->setTablePrefix('contact'));
		$this->assertEquals('mysite_test_contact_', $this->Message->tablePrefix);
		
		$this->assertFalse($this->Message->setTablePrefix(''));
		$this->assertEquals('mysite_test_contact_', $this->Message->tablePrefix);
	}
	
/**
 * beforeSave
 *
 * @return boolean
 * @access public
 */
	public function testBeforeSave() {
		// 初期化
		$this->Message->data = array('Message' => array(
			'value' => "\xE2\x85\xA0\xE2\x85\xA1\xE3\x8D\x8D\xE3\x88\xB9",
		));
		$result = $this->Message->save();
		$this->assertEquals('IIIメートル(代)', $result['Message']['value'], 'beforeSaveでデータベース用のデータに変換されていません');
	}

/**
 * Called after data has been checked for errors
 *
 * @dataProvider validateDataProvider
 */
	public function testValidate($id, $data, $expected, $message) {
		$this->Message->setup($id);
		$this->Message->data = array('Message' => $data);

		$this->Message->validates();
		$this->assertEquals($expected, $this->Message->validationErrors, $message);
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
				'message' => array('日付の形式が不正です。'),
				'name' => array(true, true),
				'email' => array(true, true)
			), 'バリデーションチェックが正しく行われていません'),
			// ファイル正常系
			array(99, array(
				'file_1' => array(
					'name' => 'test.jpg',
					'size' => 9,
					'type' => 'image/jpg'
					)
			), array(), 'ファイルのバリデーションチェックが正しく行われていません'),
			// ファイルサイズ異常系
			array(99, array(
				'file_1' => array(
					'name' => 'test.png',
					'size' => 9999999,
					'type' => 'image/png'
				)
			),
			array(
				'file_1' => array('ファイルサイズがオーバーしています。1MB以内のファイルをご利用ください。')
			), 'ファイルのバリデーションチェックが正しく行われていません'),
			// ファイル形式異常系
			array(99, array(
				'file_1' => array(
					'name' => 'test.png',
					'size' => 9,
					'type' => 'image/png'
				)
			),
			array(
				'file_1' => array('ファイル形式が不正です。')
			), 'ファイルのバリデーションチェックが正しく行われていません'),
		);
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
		$this->Message->mailFields = array(
			array('MailField' => array(
				'field_name' => 'value',
				'auto_convert' => $auto_convert,
				'use_field' => true,
			)
		));
		$data = array('Message' => array(
			'value' => $value
		));

		// 実行
		$result = $this->Message->autoConvert($data);

		$this->assertEquals($expected, $result['Message']['value'], $message);
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
		$this->Message->mailFields = array(
			array('MailField' => array(
				'field_name' => 'value',
				'use_field' => true,
				'default_value' => 'default',
				'type' => $type,
			)
		));
		$data = array('Message' => array(
			'key1' => 'hoge1',
			'key2' => 'hoge2',
		));

		// 実行
		$result = $this->Message->getDefaultValue($data);

		if ($type != 'multi_check') {
			$expected = array(
				'Message' => array(
					'value' => 'default',
					'key1' => 'hoge1',
					'key2' => 'hoge2'
			));
			$this->assertEquals($expected, $result);

		} else {
			$this->assertEquals('default', $result['Message']['value'][0]);
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
		$this->Message->mailFields = array(
			array('MailField' => array(
				'field_name' => 'value',
				'use_field' => true,
				'type' => $type,
			)
		));
		$dbData = array('Message' => array(
			'value' => $value,
		));

		// 実行
		$result = $this->Message->convertToDb($dbData);

		$this->assertEquals($expected, $result['Message']['value']);
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
 * メール用に変換する
 *
 * @param int $no_send no_sendの値
 * @param string $type 指定するタイプ
 * @dataProvider convertDatasToMailDataProvider
 */
	public function testConvertDatasToMail($no_send, $type) {
		// 初期化
		$this->Message->mailFields = array(
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
		$result = $this->Message->convertDatasToMail($dbData);

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
 * メッセージテーブルを作成/名前変更/削除する
 *
 * @param string $contentName コンテンツ名
 * @dataProvider createRenameDropTableDataProvider
 */
	public function testCreateRenameDropTable($contentName) {
		// 初期化
		$fullTable = $this->Message->getTablePrefixByContentName($contentName) . 'messages';
		$toContentName = $contentName . '_renamed';
		$toFullTable = $this->Message->getTablePrefixByContentName($toContentName) . 'messages';

		// テーブル作成
		$this->Message->createTable($contentName);
		$this->assertTrue($this->Message->tableExists($fullTable), 'メッセージテーブルを正しく作成できません');

		// テーブル名変更
		$this->Message->renameTable($contentName, $toContentName);
		$this->assertTrue($this->Message->tableExists($toFullTable), 'メッセージテーブルの名前を正しく変更できません');
		if ($contentName == 'messages') {
			$this->assertTrue($this->Message->tableExists($fullTable), '指定されたコンテンツ名がmessageの時、テーブルが再生成されていません');
		}

		// テーブル削除
		$this->Message->dropTable($toContentName);
		$this->assertFalse($this->Message->tableExists($toFullTable), 'メッセージテーブルを削除できません');
		if ($contentName == 'messages') {
			$this->assertTrue($this->Message->tableExists($fullTable), '指定されたコンテンツ名がmessageの時、テーブルが再生成されていません');
		}
	}

	public function createRenameDropTableDataProvider() {
		return array(
			array('hoge'),
			array('messages'),
		);
	}

/**
 * メッセージファイルのフィールドを追加/名前変更/削除する
 */
	public function testAddRenameDelMessageField() {
		
		$db = $this->Message->getDataSource();
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
		$contentName = 'hoge';
		$fullTable = $this->Message->getTablePrefixByContentName($contentName) . 'messages';
		$fieldName = 'hogeField';
		$toFieldName = 'hogeField_renamed';

		$this->Message->createTable($contentName);

		// フィールド追加
		$this->Message->addMessageField($contentName, $fieldName);
		$sql = $command . " $fullTable $fieldName";
		$this->assertNotEmpty($this->Message->query($sql), 'メッセージファイルにフィールドを正しく追加できません');

		// フィールド名変更
		$this->Message->renameMessageField($contentName, $fieldName, $toFieldName);
		$sql = $command . " $fullTable $toFieldName";
		$this->assertNotEmpty($this->Message->query($sql), 'メッセージファイルのフィールド名を正しく変更できません');

		// フィールド削除
		$this->Message->delMessageField($contentName, $toFieldName);
		$sql = $command . " $fullTable $toFieldName";
		$this->assertEmpty($this->Message->query($sql), 'メッセージファイルのフィールドを正しく削除できません');

		$this->Message->dropTable($contentName);

	}

/**
 * コンテンツ名つきのテーブルプレフィックスを取得する
 */
	public function testGetTablePrefixByContentName() {
		$result = $this->Message->getTablePrefixByContentName('hoge');
		$this->assertEquals('mysite_test_hoge_', $result);

		$result = $this->Message->getTablePrefixByContentName('messages');
		$this->assertEquals('mysite_test_', $result);
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
	public function testConstruction() {

		$db = $this->Message->getDataSource();

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

		$contentName = 'contact';
		$fullTable = $this->Message->getTablePrefixByContentName($contentName) . 'messages';

		$this->Message->dropTable($contentName);

		// 一回目
		$this->Message->construction(1);
		$this->assertTrue($this->Message->tableExists($fullTable), 'メッセージテーブルを正しく作成できません');
		
		$expectColumns = array('id', 'modified', 'created');
		$sql = $command . " $fullTable";
		$resultColumns = array();
		foreach ($this->Message->query($sql) as $key => $value) {
			$resultColumns[] = $value['COLUMNS']['Field'];
		}
		$this->assertEquals($expectColumns, $resultColumns, '正しくカラムが追加されていません');

		// 二回目
		$this->Message->construction(1);

		$this->MailField = ClassRegistry::init('Mail.MailField');
		$expectColumns = $this->MailField->find('list', array(
			'fields' => 'field_name',
			'conditions' => array('mail_content_id' => 1),
		));
		array_unshift($expectColumns, 'id', 'modified', 'created');

		$sql = $command . " $fullTable";
		$resultColumns = array();
		foreach ($this->Message->query($sql) as $key => $value) {
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
 * @access public
 */
	public function testConvertMessageToCsv() {
		$this->markTestIncomplete('このテストは、まだ実装されていません。');

		$messages = array(
			array('Message' => array(
				'id' => 1, 'name_1' => 'v1', 'name_2' => 'v2',
				'name_kana_1' => 'v3', 'name_kana_2' => 'v4', 'sex' => 'v5',
				'email_1' => 'v6', 'email_2' => 'v7', 'tel_1' => 'v8',
				'tel_2' => 'v9', 'tel_3' => 'v10', 'zip' => 'v11',
				'address_1' => 'v12', 'address_2' => 'v13', 'address_3' => 'v14',
				'category' => 'v15', 'message' => 'v16', 'root' => 'v17',
				'root_etc' => 'v18', 'created' => 'v19', 'modified' => 'v20',
				'modified' => 'v21',
			)),
			array('Message' => array(
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
				'Message' => array(
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
				'Message' => array(
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
		
		$result = $this->Message->convertMessageToCsv(1, $messages);
		$this->assertEquals($expected, $result, '受信メッセージの内容を表示状態に正しく変換できません');
	}

/**
 * メール受信テーブルを全て再構築
 * 
 * @return boolean
 */
	public function testReconstructionAll() {
		$contentName = 'contact';
		$fullTable = $this->Message->getTablePrefixByContentName($contentName) . 'messages';
		$this->Message->dropTable($contentName);

		$this->assertTrue($this->Message->reconstructionAll());
		$this->assertTrue($this->Message->tableExists($fullTable));
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
